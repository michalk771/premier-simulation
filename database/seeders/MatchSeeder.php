<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Matches;
use App\Models\League;
use App\Models\Team;

class MatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matches = [
            // Match 1: Team 1 vs Team 2
            ['home_team_id' => 1, 'away_team_id' => 2, 'home_score' => 1, 'away_score' => 2, 'week_number' => 1],
            // Match 2: Team 3 vs Team 4
            ['home_team_id' => 3, 'away_team_id' => 4, 'home_score' => 2, 'away_score' => 1, 'week_number' => 1],
        ];

        foreach ($matches as $matchData) {
            $match = Matches::create($matchData);
            $this->updateLeagueTable($match);
        }
    }

    private function updateLeagueTable($match)
    {
        $homeTeamTable = League::where('team_id', $match->home_team_id)->first();
        $awayTeamTable = League::where('team_id', $match->away_team_id)->first();

        // Update games played
        $homeTeamTable->played++;
        $awayTeamTable->played++;

        // Update goals for and against
        $homeTeamTable->goals_for += $match->home_score;
        $homeTeamTable->goals_against += $match->away_score;

        $awayTeamTable->goals_for += $match->away_score;
        $awayTeamTable->goals_against += $match->home_score;

        // Update goal difference
        $homeTeamTable->goal_difference = $homeTeamTable->goals_for - $homeTeamTable->goals_against;
        $awayTeamTable->goal_difference = $awayTeamTable->goals_for - $awayTeamTable->goals_against;

        // Update points and win/draw/loss statistics
        if ($match->home_score > $match->away_score) {
            $homeTeamTable->won++;
            $homeTeamTable->points += 3;
            $awayTeamTable->lost++;
        } elseif ($match->home_score < $match->away_score) {
            $awayTeamTable->won++;
            $awayTeamTable->points += 3;
            $homeTeamTable->lost++;
        } else {
            $homeTeamTable->drawn++;
            $awayTeamTable->drawn++;
            $homeTeamTable->points++;
            $awayTeamTable->points++;
        }

        // Save updates
        $homeTeamTable->save();
        $awayTeamTable->save();
    }
}

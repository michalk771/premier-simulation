<?php
namespace App\Services;

use App\Models\Matches;
use App\Repositories\Match\MatchRepositoryInterface;
use App\Repositories\Team\TeamRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MatchService
{
    protected MatchRepositoryInterface $matchRepository;
    protected LeagueService $leagueService;
    protected TeamRepositoryInterface $teamRepository;

    public function __construct(
        MatchRepositoryInterface $matchRepository,
        LeagueService $leagueService,
        TeamRepositoryInterface $teamRepository
    ) {
        $this->matchRepository = $matchRepository;
        $this->leagueService = $leagueService;
        $this->teamRepository = $teamRepository;
    }

    public function simulateWeek(int $week): array
    {
        $teams = $this->getTeamsForSimulation();
        $previousMatches = $this->matchRepository->getMatchesByWeek($week);
        $goalsScored = $this->calculateGoalsScored($previousMatches);

        $matches = $this->createMatches($teams);
        $results = [];

        foreach ($matches as $match) {
            $results[] = $this->simulateMatch($match, $goalsScored, $week);
        }

        $leagueTable = $this->leagueService->getLeagueTable(); // Updated

        return [
            'weekMatches' => $results,
            'league' => $leagueTable,
        ];
    }

    protected function getTeamsForSimulation(): \Illuminate\Support\Collection
    {
        $teams = $this->teamRepository->getAllBestTeams();

        if (count($teams) !== 4) {
            throw new \Exception('There must be exactly 4 teams to simulate the week.');
        }

        return $teams;
    }

    protected function calculateGoalsScored(Collection $previousMatches): array
    {
        $goalsScored = [];
        foreach ($previousMatches as $match) {
            $goalsScored[$match->home_team_id] = ($goalsScored[$match->home_team_id] ?? 0) + $match->home_score;
            $goalsScored[$match->away_team_id] = ($goalsScored[$match->away_team_id] ?? 0) + $match->away_score;
        }

        return $goalsScored;
    }

    protected function createMatches(Collection $teams): array
    {
        $firstMatch = [$teams[0], $teams[1]];
        shuffle($firstMatch);

        $secondMatch = [$teams[2], $teams[3]];
        shuffle($secondMatch);

        return [
            ['home_team' => $firstMatch[0], 'away_team' => $firstMatch[1]],
            ['home_team' => $secondMatch[0], 'away_team' => $secondMatch[1]],
        ];
    }

    protected function simulateMatch(array $matchData, array $goalsScored, int $week): Matches
    {
        $homeTeam = $matchData['home_team'];
        $awayTeam = $matchData['away_team'];

        $homeScore = $goalsScored[$homeTeam->id] ?? 0;
        $awayScore = $goalsScored[$awayTeam->id] ?? 0;

        $this->applyStrengthModifiers($homeTeam, $awayTeam, $homeScore, $awayScore);

        $match = $this->matchRepository->createMatch([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'week_number' => $week
        ]);

        $this->updateLeagueTable($homeTeam, $awayTeam, $homeScore, $awayScore);

        return $match;
    }

    protected function applyStrengthModifiers($homeTeam, $awayTeam, int &$homeScore, int &$awayScore): void
    {
        if ($homeTeam->strength > $awayTeam->strength) {
            $homeScore += rand(0, 6);
        } elseif ($homeTeam->strength < $awayTeam->strength) {
            $awayScore += rand(0, 2);
        }
    }

    protected function updateLeagueTable($homeTeam, $awayTeam, int $homeScore, int $awayScore): void
    {
        $homeLeague = $this->leagueService->getTeam($homeTeam->id); // Updated
        $awayLeague = $this->leagueService->getTeam($awayTeam->id); // Updated

        $this->updateTeamStats($homeLeague, $homeScore, $awayScore);
        $this->updateTeamStats($awayLeague, $awayScore, $homeScore);

        $homeLeague->save();
        $awayLeague->save();
    }

    protected function updateTeamStats($team, int $goalsFor, int $goalsAgainst): void
    {
        $team->played++;
        $team->goals_for += $goalsFor;
        $team->goals_against += $goalsAgainst;
        $team->goal_difference = $team->goals_for - $team->goals_against;

        if ($goalsFor > $goalsAgainst) {
            $team->won++;
            $team->points += 3;
        } elseif ($goalsFor < $goalsAgainst) {
            $team->lost++;
        } else {
            $team->drawn++;
            $team->points++;
        }
    }

    public function getMatchesForWeek(int $week, int $limit = 2): \Illuminate\Support\Collection
    {
        return $this->matchRepository->getMatchesByWeek($week, $limit);
    }

    public function getLatestWeeks(): array
    {
        $latestMatches = $this->matchRepository->getLatestMatches(2);
        $leagueTable = $this->leagueService->getLeagueTable(); // Updated
        $finalTable = $this->leagueService->getLeagueTable(); // Updated
        $winPercentages = $this->leagueService->calculateWinPercentages($finalTable); // Updated
        $latestWeekNumber = $this->matchRepository->getLatestWeekNumber();

        return [
            'weekMatches' => $latestMatches,
            'leagueTable' => $leagueTable,
            'finalTable' => $finalTable,
            'winPercentages' => $winPercentages,
            'weekNumber' => $latestWeekNumber,
        ];
    }
}

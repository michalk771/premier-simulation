<?php

namespace App\Repositories\League;

use App\Models\League;
use Illuminate\Support\Collection;

class LeagueRepository implements LeagueRepositoryInterface
{
    private League $league;

    public function __construct(League $league)
    {
        $this->league = $league;
    }

    public function getLeagueTable(): Collection
    {
        return $this->league::with('team')
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->get();
    }

    public function updateTeamStats(int $teamId, array $data): int
    {
        return $this->league::where('team_id', $teamId)
            ->update($data);
    }

    public function getBestTeams(int $limit = 2): Collection
    {
        return $this->league::with('team')
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->take($limit)
            ->get();
    }

    public function calculateWinPercentages(Collection $finalTable): array
    {
        $totalPoints = $finalTable->sum('points');
        $percentages = [];

        foreach ($finalTable as $teamEntry) {
            $percentages[$teamEntry->team_id] = round(($teamEntry->points / $totalPoints) * 100, 2);
        }

        return $percentages;
    }

    public function getTeam(int $teamId)
    {
        return $this->league::where('team_id', $teamId)
            ->first();
    }
}

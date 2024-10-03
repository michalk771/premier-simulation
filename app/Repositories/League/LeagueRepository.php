<?php

namespace App\Repositories\League;

use App\Models\League;
use Illuminate\Support\Collection;

class LeagueRepository implements LeagueRepositoryInterface
{
    public function getLeagueTable(): Collection
    {
        return League::with('team')
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->get();
    }

    public function updateTeamStats(int $teamId, array $data): int
    {
        return League::where('team_id', $teamId)
            ->update($data);
    }

    public function getBestTeams(int $limit = 2): Collection
    {
        return League::with('team')
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
        return League::where('team_id', $teamId)
            ->first();
    }
}

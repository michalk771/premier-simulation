<?php
namespace App\Repositories\Team;

namespace App\Repositories\Team;

use App\Models\Team;
use Illuminate\Support\Collection;

class TeamRepository implements TeamRepositoryInterface
{
    public function getAllTeams(): Collection
    {
        return Team::with('leagueTable')->get();
    }

    public function getAllBestTeams(): Collection
    {
        return Team::with('leagueTable')
            ->get()
            ->sortByDesc(function ($team) {
                return $team->leagueTable->goal_difference;
            })
            ->values();
    }

    public function getTeamById(int $id): ?Team
    {
        return Team::find($id);
    }

    public function createTeam(array $data): Team
    {
        return Team::create($data);
    }
}

<?php
namespace App\Repositories\Team;

namespace App\Repositories\Team;

use App\Models\Team;
use Illuminate\Support\Collection;

class TeamRepository implements TeamRepositoryInterface
{
    private Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function getAllTeams(): Collection
    {
        return $this->team::with('leagueTable')->get();
    }

    public function getAllBestTeams(): Collection
    {
        return $this->team::with('leagueTable')
            ->get()
            ->sortByDesc(function ($team) {
                return $team->leagueTable ? $team->leagueTable->goal_difference : 0;
            })
            ->values();
    }

    public function getTeamById(int $id): ?Team
    {
        return $this->team::find($id);
    }

    public function createTeam(array $data): Team
    {
        return $this->team::create($data);
    }
}

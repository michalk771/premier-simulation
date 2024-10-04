<?php
namespace App\Services;

use App\Repositories\Team\TeamRepositoryInterface;
use Illuminate\Support\Collection;
use App\Models\Team;

class TeamService
{
    protected TeamRepositoryInterface $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function getAllTeams(): Collection
    {
        return $this->teamRepository->getAllTeams();
    }

    public function createTeam(array $data): Team
    {
        return $this->teamRepository->createTeam($data);
    }

    public function getTeamById(int $id): ?Team
    {
        return $this->teamRepository->getTeamById($id);
    }
}

<?php
namespace App\Services;

use App\Repositories\Team\TeamRepositoryInterface;

class TeamService
{
    protected $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function getAllTeams()
    {
        return $this->teamRepository->getAllTeams();
    }

    public function createTeam($data)
    {
        return $this->teamRepository->createTeam($data);
    }

    public function getTeamById($id)
    {
        return $this->teamRepository->getTeamById($id);
    }

    public function updateTeam($id, $data)
    {
        return $this->teamRepository->updateTeam($id, $data);
    }
}

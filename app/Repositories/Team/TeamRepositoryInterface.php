<?php
namespace App\Repositories\Team;

use Illuminate\Support\Collection;
use App\Models\Team;

interface TeamRepositoryInterface
{
    public function getAllTeams(): Collection;

    public function getAllBestTeams(): Collection;

    public function getTeamById(int $id): ?Team;

    public function createTeam(array $data): Team;
}

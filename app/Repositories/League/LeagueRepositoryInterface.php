<?php
namespace App\Repositories\League;

use Illuminate\Support\Collection;

interface LeagueRepositoryInterface
{
    public function getLeagueTable(): Collection;

    public function updateTeamStats(int $teamId, array $data): int;

    public function getBestTeams(int $limit = 2): Collection;

    public function calculateWinPercentages(Collection $finalTable): array;

    public function getTeam(int $teamId);
}

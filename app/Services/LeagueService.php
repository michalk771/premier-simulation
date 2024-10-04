<?php
namespace App\Services;

use App\Models\League;
use App\Repositories\League\LeagueRepositoryInterface;
use \Illuminate\Support\Collection;

class LeagueService
{
    protected LeagueRepositoryInterface $leagueTableRepository;

    public function __construct(LeagueRepositoryInterface $leagueTableRepository)
    {
        $this->leagueTableRepository = $leagueTableRepository;
    }

    public function getLeagueTable(): Collection
    {
        return $this->leagueTableRepository->getLeagueTable();
    }

    public function getTeam(?int $teamId): League
    {
        return $this->leagueTableRepository->getTeam($teamId);
    }

    public function calculateWinPercentages(Collection $finalTable): array
    {
        return $this->leagueTableRepository->calculateWinPercentages($finalTable);
    }
}

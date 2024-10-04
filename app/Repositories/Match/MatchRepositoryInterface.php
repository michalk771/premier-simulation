<?php
namespace App\Repositories\Match;

use Illuminate\Support\Collection;
use App\Models\Matches;

interface MatchRepositoryInterface
{
    public function createMatch(array $data): Matches;

    public function getMatchesByWeek(int $weekNumber, ?int $limit = null): Collection;

    public function getLatestMatches(?int $limit = null): Collection;

    public function getLatestWeeks(int $limit = 4): Collection;

    public function getLatestWeekNumber(): ?int;
}

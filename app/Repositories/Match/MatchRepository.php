<?php
namespace App\Repositories\Match;

use App\Models\Matches;
use Illuminate\Support\Collection;

class MatchRepository implements MatchRepositoryInterface
{
    private Matches $matches;

    public function __construct(Matches $matches)
    {
        $this->matches = $matches;
    }

    public function createMatch(array $data): Matches
    {
        return $this->matches::create($data);
    }

    public function getMatchesByWeek(int $weekNumber, ?int $limit = null): Collection
    {
        $query = $this->matches::with('homeTeam', 'awayTeam')
            ->where('week_number', $weekNumber);

        if ($limit) {
            $query->take($limit);
        }

        return $query->get();
    }

    public function getLatestMatches(?int $limit = null): Collection
    {
        $query = $this->matches::with('homeTeam', 'awayTeam')
            ->orderBy('id', 'desc');

        if ($limit) {
            $query->take($limit);
        }

        return $query->get();
    }

    public function getLatestWeeks(int $limit = 4): Collection
    {
        return $this->matches::with('homeTeam', 'awayTeam')
            ->orderBy('week_number', 'desc')
            ->take($limit)
            ->get()
            ->groupBy('week_number');
    }

    public function getLatestWeekNumber(): ?int
    {
        return $this->matches::orderBy('week_number', 'desc')
            ->value('week_number');
    }
}

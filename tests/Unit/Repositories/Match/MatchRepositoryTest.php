<?php
namespace Tests\Unit\Repositories\Match;

use Tests\TestCase;
use App\Models\Matches;
use App\Repositories\Match\MatchRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Team;

class MatchRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected MatchRepository $matchRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matchRepository = new MatchRepository(new Matches());
    }

    /** @test */
    public function it_creates_a_match_successfully()
    {
        $teamA = Team::factory()->create(['id' => 1]);
        $teamB = Team::factory()->create(['id' => 2]);

        $matchData = [
            'home_team_id' => $teamA->id,
            'away_team_id' => $teamB->id,
            'home_score' => 3,
            'away_score' => 1,
            'week_number' => 1,
        ];

        $match = $this->matchRepository->createMatch($matchData);

        $this->assertInstanceOf(Matches::class, $match);
        $this->assertDatabaseHas('matches', $matchData);
    }

    /** @test */
    public function it_retrieves_matches_by_week()
    {
        Matches::factory()->create(['week_number' => 1]);
        Matches::factory()->create(['week_number' => 1]);
        Matches::factory()->create(['week_number' => 2]);

        $matches = $this->matchRepository->getMatchesByWeek(1);

        $this->assertCount(2, $matches);
    }

    /** @test */
    public function it_retrieves_latest_matches()
    {
        Matches::factory()->count(5)->create();

        $latestMatches = $this->matchRepository->getLatestMatches(3);

        $this->assertCount(3, $latestMatches);
    }

    /** @test */
    public function it_retrieves_latest_weeks()
    {
        Matches::factory()->create(['week_number' => 1]);
        Matches::factory()->create(['week_number' => 2]);

        $latestWeeks = $this->matchRepository->getLatestWeeks(2);

        $this->assertArrayHasKey(1, $latestWeeks);
        $this->assertArrayHasKey(2, $latestWeeks);
    }

    /** @test */
    public function it_retrieves_latest_week_number()
    {
        Matches::factory()->create(['week_number' => 1]);
        Matches::factory()->create(['week_number' => 2]);
        Matches::factory()->create(['week_number' => 3]);

        $latestWeekNumber = $this->matchRepository->getLatestWeekNumber();

        $this->assertEquals(3, $latestWeekNumber);
    }
}

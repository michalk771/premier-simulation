<?php
namespace Tests\Unit\Repositories\League;

use Tests\TestCase;
use App\Models\League;
use App\Repositories\League\LeagueRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Team;

class LeagueRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected LeagueRepository $leagueRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leagueRepository = new LeagueRepository(new League());
    }

    /** @test */
    public function it_retrieves_the_league_table()
    {
        $teamA = Team::factory()->create(['id' => 1]);
        $teamB = Team::factory()->create(['id' => 2]);

        League::factory()->create(['team_id' => 1, 'points' => 10, 'goal_difference' => 5]);
        League::factory()->create(['team_id' => 2, 'points' => 15, 'goal_difference' => 10]);

        $leagueTable = $this->leagueRepository->getLeagueTable();

        $this->assertCount(2, $leagueTable);
        $this->assertEquals(2, $leagueTable[0]->team_id);
    }

    /** @test */
    public function it_updates_team_stats()
    {
        $team = Team::factory()->create(['id' => 1]);

        $leagueEntry = League::factory()->create(['team_id' => 1, 'points' => 10]);

        $data = ['points' => 15, 'goal_difference' => 8];

        $this->leagueRepository->updateTeamStats(1, $data);

        $this->assertDatabaseHas('leagues', [
            'team_id' => 1,
            'points' => 15,
            'goal_difference' => 8,
        ]);
    }

    /** @test */
    public function it_retrieves_best_teams()
    {
        $teamA = Team::factory()->create(['id' => 1]);
        $teamB = Team::factory()->create(['id' => 2]);
        $teamC = Team::factory()->create(['id' => 3]);

        League::factory()->create(['team_id' => 1, 'points' => 10]);
        League::factory()->create(['team_id' => 2, 'points' => 20]);
        League::factory()->create(['team_id' => 3, 'points' => 15]);

        $bestTeams = $this->leagueRepository->getBestTeams(2);

        $this->assertCount(2, $bestTeams);
        $this->assertEquals(2, $bestTeams[0]->team_id);
    }

    /** @test */
    public function it_calculates_win_percentages()
    {
        $finalTable = collect([
            (object) ['team_id' => 1, 'points' => 10],
            (object) ['team_id' => 2, 'points' => 20],
        ]);

        $percentages = $this->leagueRepository->calculateWinPercentages($finalTable);

        $this->assertArrayHasKey(1, $percentages);
        $this->assertEquals(33.33, $percentages[1]);
        $this->assertArrayHasKey(2, $percentages);
        $this->assertEquals(66.67, $percentages[2]);
    }

    /** @test */
    public function it_retrieves_a_team()
    {
        $team = Team::factory()->create(['id' => 1]);

        League::factory()->create(['team_id' => 1]);

        $team = $this->leagueRepository->getTeam(1);

        $this->assertInstanceOf(League::class, $team);
        $this->assertEquals(1, $team->team_id);
    }

    /** @test */
    public function it_returns_null_if_team_not_found()
    {
        $team = $this->leagueRepository->getTeam(999);

        $this->assertNull($team);
    }
}

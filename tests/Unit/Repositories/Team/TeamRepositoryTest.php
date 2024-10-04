<?php

namespace Tests\Unit\Repositories\Team;

use Tests\TestCase;
use App\Models\Team;
use App\Models\League;
use App\Repositories\Team\TeamRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class TeamRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TeamRepository $teamRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = new TeamRepository(new Team());
    }

    /** @test */
    public function it_retrieves_all_teams()
    {
        Team::factory()->create(['id' => 1]);
        Team::factory()->create(['id' => 2]);

        $teams = $this->teamRepository->getAllTeams();

        $this->assertCount(2, $teams);
        $this->assertEquals(1, $teams[0]->id);
    }

    /** @test */
    public function it_retrieves_best_teams_sorted_by_goal_difference()
    {
        $teamA = Team::factory()->create(['id' => 1]);
        $leagueA = League::factory()->create(['team_id' => $teamA->id, 'goal_difference' => 10]);

        $teamB = Team::factory()->create(['id' => 2]);
        $leagueB = League::factory()->create(['team_id' => $teamB->id, 'goal_difference' => 5]);

        $teamC = Team::factory()->create(['id' => 3]);
        $leagueC = League::factory()->create(['team_id' => $teamC->id, 'goal_difference' => 15]);

        $bestTeams = $this->teamRepository->getAllBestTeams();

        $this->assertCount(3, $bestTeams);
        $this->assertEquals(3, $bestTeams[0]->id);
    }

    /** @test */
    public function it_retrieves_a_team_by_id()
    {
        $team = Team::factory()->create(['id' => 1]);

        $retrievedTeam = $this->teamRepository->getTeamById(1);

        $this->assertInstanceOf(Team::class, $retrievedTeam);
        $this->assertEquals(1, $retrievedTeam->id);
    }

    /** @test */
    public function it_returns_null_if_team_not_found()
    {
        $team = $this->teamRepository->getTeamById(999);

        $this->assertNull($team);
    }

    /** @test */
    public function it_creates_a_team()
    {
        $data = [
            'name' => 'Test Team',
            'strength' => 80
        ];

        $team = $this->teamRepository->createTeam($data);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertDatabaseHas('teams', $data);
    }
}

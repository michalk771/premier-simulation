<?php

namespace Tests\Unit\Services;

use App\Models\Team;
use App\Repositories\Team\TeamRepositoryInterface;
use App\Services\TeamService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class TeamServiceTest extends TestCase
{
    /** @var TeamRepositoryInterface&MockObject */
    protected TeamRepositoryInterface|MockObject $teamRepository;

    /** @var TeamService */
    protected TeamService $teamService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = $this->createMock(TeamRepositoryInterface::class);
        $this->teamService = new TeamService($this->teamRepository);
    }

    /** @test */
    public function it_can_get_all_teams()
    {
        $mockTeams = new Collection([
            new Team(['id' => 1, 'name' => 'Team A']),
            new Team(['id' => 2, 'name' => 'Team B']),
        ]);

        $this->teamRepository->method('getAllTeams')->willReturn($mockTeams);

        $result = $this->teamService->getAllTeams();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Team A', $result[0]->name);
    }

    /** @test */
    public function it_can_create_a_team()
    {
        $teamData = ['name' => 'Team C', 'strength' => 80];
        $mockTeam = new Team($teamData);

        $this->teamRepository->method('createTeam')->willReturn($mockTeam);

        $result = $this->teamService->createTeam($teamData);

        $this->assertInstanceOf(Team::class, $result);
        $this->assertEquals('Team C', $result->name);
    }

    /** @test */
    public function it_can_get_team_by_id()
    {
        $mockTeam = new Team(['id' => 1, 'name' => 'Team A']);
        $this->teamRepository->method('getTeamById')->with(1)->willReturn($mockTeam);

        $result = $this->teamService->getTeamById(1);

        $this->assertInstanceOf(Team::class, $result);
        $this->assertEquals('Team A', $result->name);
    }

    /** @test */
    public function it_returns_null_if_team_not_found()
    {
        $this->teamRepository->method('getTeamById')->with(999)->willReturn(null);

        $result = $this->teamService->getTeamById(999);

        $this->assertNull($result);
    }
}

<?php

namespace Tests\Unit\Services;

use App\Models\League;
use App\Repositories\League\LeagueRepositoryInterface;
use App\Services\LeagueService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class LeagueServiceTest extends TestCase
{
    /** @var LeagueRepositoryInterface&MockObject */
    protected LeagueRepositoryInterface|MockObject $leagueTableRepository;

    /** @var LeagueService */
    protected LeagueService $leagueService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leagueTableRepository = $this->createMock(LeagueRepositoryInterface::class);
        $this->leagueService = new LeagueService($this->leagueTableRepository);
    }

    /** @test */
    public function it_can_get_league_table()
    {
        $mockLeagueTable = new Collection([
            new League(['team_id' => 1, 'points' => 10, 'goal_difference' => 5]),
            new League(['team_id' => 2, 'points' => 15, 'goal_difference' => 10]),
        ]);

        $this->leagueTableRepository->method('getLeagueTable')->willReturn($mockLeagueTable);

        $result = $this->leagueService->getLeagueTable();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]->team_id);
    }

    /** @test */
    public function it_can_get_a_team()
    {
        $teamId = 1;
        $mockTeam = new League(['team_id' => $teamId]);

        $this->leagueTableRepository->method('getTeam')->with($teamId)->willReturn($mockTeam);

        $result = $this->leagueService->getTeam($teamId);

        $this->assertInstanceOf(League::class, $result);
        $this->assertEquals($teamId, $result->team_id);
    }

    /** @test */
    public function it_can_calculate_win_percentages()
    {
        $finalTable = collect([
            (object) ['team_id' => 1, 'points' => 10],
            (object) ['team_id' => 2, 'points' => 20],
        ]);

        $expectedPercentages = [
            1 => 33.33,
            2 => 66.67,
        ];

        $this->leagueTableRepository->method('calculateWinPercentages')->willReturn($expectedPercentages);

        $result = $this->leagueService->calculateWinPercentages($finalTable);

        $this->assertArrayHasKey(1, $result);
        $this->assertEquals($expectedPercentages[1], $result[1]);
        $this->assertArrayHasKey(2, $result);
        $this->assertEquals($expectedPercentages[2], $result[2]);
    }
}

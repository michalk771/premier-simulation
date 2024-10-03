<?php

namespace Tests\Unit\Services;

use App\Models\League;
use App\Models\Matches;
use App\Models\Team;
use App\Repositories\Match\MatchRepositoryInterface;
use App\Repositories\Team\TeamRepositoryInterface;
use App\Services\LeagueService;
use App\Services\MatchService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class MatchServiceTest extends TestCase
{
    /** @var MatchRepositoryInterface&MockObject */
    protected MatchRepositoryInterface|MockObject $matchRepository;

    /** @var LeagueService&MockObject */
    protected LeagueService|MockObject $leagueService;

    /** @var TeamRepositoryInterface&MockObject */
    protected TeamRepositoryInterface|MockObject $teamRepository;

    protected MatchService $matchService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matchRepository = $this->createMock(MatchRepositoryInterface::class);
        $this->leagueService = $this->createMock(LeagueService::class);
        $this->teamRepository = $this->createMock(TeamRepositoryInterface::class);

        $this->matchService = new MatchService(
            $this->matchRepository,
            $this->leagueService,
            $this->teamRepository
        );
    }

    public function testSimulateWeek()
    {
        $week = 1;
        $teams = [
            new Team(['id' => 1, 'strength' => 10, 'played' => 0, 'goals_for' => 0, 'goals_against' => 0]),
            new Team(['id' => 2, 'strength' => 5, 'played' => 0, 'goals_for' => 0, 'goals_against' => 0]),
            new Team(['id' => 3, 'strength' => 8, 'played' => 0, 'goals_for' => 0, 'goals_against' => 0]),
            new Team(['id' => 4, 'strength' => 6, 'played' => 0, 'goals_for' => 0, 'goals_against' => 0]),
        ];

        $previousMatches = new \Illuminate\Database\Eloquent\Collection([
            new Matches(['home_team_id' => 1, 'home_score' => 3, 'away_team_id' => 2, 'away_score' => 1]),
            new Matches(['home_team_id' => 3, 'home_score' => 2, 'away_team_id' => 1, 'away_score' => 2]),
        ]);

        $this->teamRepository->method('getAllBestTeams')->willReturn(new \Illuminate\Database\Eloquent\Collection($teams));
        $this->matchRepository->method('getMatchesByWeek')->with($week)->willReturn($previousMatches);
        $this->leagueService->method('getLeagueTable')->willReturn(new \Illuminate\Database\Eloquent\Collection($previousMatches));

        $this->leagueService->method('getTeam')->willReturn(new League(['team_id' => 1]));

        $this->matchRepository->method('createMatch')->willReturn(new Matches());

        $result = $this->matchService->simulateWeek($week);

        $this->assertArrayHasKey('weekMatches', $result);
        $this->assertArrayHasKey('league', $result);
    }

    public function testGetTeamsForSimulationReturnsFourTeams()
    {
        $teams = [
            new Team(['id' => 1, 'strength' => 10]),
            new Team(['id' => 2, 'strength' => 5]),
            new Team(['id' => 3, 'strength' => 8]),
            new Team(['id' => 4, 'strength' => 6]),
        ];

        $this->teamRepository->method('getAllBestTeams')->willReturn(new Collection($teams));

        $result = $this->invokeMethod($this->matchService, 'getTeamsForSimulation');
        $this->assertCount(4, $result);
    }

    public function testGetTeamsForSimulationThrowsException()
    {
        $teams = [
            new Team(['id' => 1, 'strength' => 10]),
            new Team(['id' => 2, 'strength' => 5]),
            new Team(['id' => 3, 'strength' => 8]),
        ];

        $this->teamRepository->method('getAllBestTeams')->willReturn(new Collection($teams));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('There must be exactly 4 teams to simulate the week.');

        $this->invokeMethod($this->matchService, 'getTeamsForSimulation');
    }

    public function testCalculateGoalsScored()
    {
        $matches = new \Illuminate\Database\Eloquent\Collection([
            new Matches(['home_team_id' => 1, 'home_score' => 3, 'away_team_id' => 2, 'away_score' => 1]),
            new Matches(['home_team_id' => 3, 'home_score' => 2, 'away_team_id' => 1, 'away_score' => 2]),
        ]);

        $goalsScored = $this->invokeMethod($this->matchService, 'calculateGoalsScored', [$matches]);

        $this->assertArrayHasKey(1, $goalsScored);
        $this->assertArrayHasKey(2, $goalsScored);
        $this->assertArrayHasKey(3, $goalsScored);
        $this->assertEquals(5, $goalsScored[1]);
        $this->assertEquals(1, $goalsScored[2]);
        $this->assertEquals(2, $goalsScored[3]);
    }

    public function testCreateMatches()
    {
        $teams = [
            new Team(['id' => 1]),
            new Team(['id' => 2]),
            new Team(['id' => 3]),
            new Team(['id' => 4]),
        ];
        $matches = $this->invokeMethod($this->matchService, 'createMatches', [new \Illuminate\Database\Eloquent\Collection($teams)]);

        $this->assertCount(2, $matches);
        $this->assertArrayHasKey('home_team', $matches[0]);
        $this->assertArrayHasKey('away_team', $matches[0]);
        $this->assertArrayHasKey('home_team', $matches[1]);
        $this->assertArrayHasKey('away_team', $matches[1]);
    }

    public function testSimulateMatch()
    {
        $homeTeam = new Team(['id' => 1, 'strength' => 10]);
        $awayTeam = new Team(['id' => 2, 'strength' => 5]);
        $matchData = ['home_team' => $homeTeam, 'away_team' => $awayTeam];
        $goalsScored = [1 => 3, 2 => 1];
        $week = 1;

        $this->matchRepository->method('createMatch')->willReturn(new Matches());
        $this->leagueService->method('getTeam')->willReturn(new League(['team_id' => 1]));

        $match = $this->invokeMethod($this->matchService, 'simulateMatch', [$matchData, $goalsScored, $week]);

        $this->assertInstanceOf(Matches::class, $match);
    }

    public function testUpdateTeamStats()
    {
        $team = new Team();
        $team->played = 0;
        $team->won = 0;
        $team->lost = 0;
        $team->drawn = 0;
        $team->goals_for = 0;
        $team->goals_against = 0;
        $team->goal_difference = 0;
        $team->points = 0;

        $this->invokeMethod($this->matchService, 'updateTeamStats', [$team, 2, 1]);

        $this->assertEquals(1, $team->played);
        $this->assertEquals(1, $team->won);
        $this->assertEquals(3, $team->points);
        $this->assertEquals(2, $team->goals_for);
        $this->assertEquals(1, $team->goals_against);
        $this->assertEquals(1, $team->goal_difference);
    }

    public function testGetMatchesForWeek()
    {
        $week = 1;
        $this->matchRepository->method('getMatchesByWeek')->with($week, 2)->willReturn(new Collection());

        $result = $this->matchService->getMatchesForWeek($week);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function testGetLatestWeeks()
    {
        $latestMatches = collect([
            new Matches(['home_team_id' => 1, 'home_score' => 3, 'away_team_id' => 2, 'away_score' => 1]),
            new Matches(['home_team_id' => 3, 'home_score' => 2, 'away_team_id' => 1, 'away_score' => 2]),
        ]);

        $this->matchRepository->method('getLatestMatches')->with(2)->willReturn($latestMatches);
        $this->leagueService->method('getLeagueTable')->willReturn(new Collection());
        $this->leagueService->method('calculateWinPercentages')->with([])->willReturn([]);
        $this->matchRepository->method('getLatestWeekNumber')->willReturn(1);

        $result = $this->matchService->getLatestWeeks();

        $this->assertArrayHasKey('weekMatches', $result);
        $this->assertArrayHasKey('leagueTable', $result);
        $this->assertArrayHasKey('finalTable', $result);
        $this->assertArrayHasKey('winPercentages', $result);
        $this->assertArrayHasKey('weekNumber', $result);
    }

    protected function invokeMethod($object, $methodName, array $args = [])
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}

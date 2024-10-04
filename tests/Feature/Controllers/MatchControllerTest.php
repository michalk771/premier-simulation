<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Team;
use App\Models\Matches;
use App\Models\League;

class MatchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_simulates_week_and_returns_successful_response()
    {
        $teams = Team::factory()->count(10)->create();
        $matches = Matches::factory()->count(2)->create();

        foreach ($teams as $team) {
            League::factory()->create([
                'team_id' => $team->id,
            ]);
        }

        $response = $this->postJson('/simulate-week', ['week' => 1]);
        $response->assertStatus(200);
    }

    /** @test */
    public function it_fetches_latest_weeks_matches_and_league_table()
    {
        $teams = Team::factory()->count(10)->create();
        $matches = Matches::factory()->count(4)->create();

        foreach ($teams as $team) {
            League::factory()->create([
                'team_id' => $team->id,
            ]);
        }

        $response = $this->getJson('/latest-weeks');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'weekMatches',
            'leagueTable',
            'finalTable',
            'winPercentages',
            'weekNumber'
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Team;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\League>
 */
class LeagueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'team_id' => Team::factory(),
            'points' => $this->faker->numberBetween(0, 100),
            'goal_difference' => $this->faker->numberBetween(-50, 50),
            'won' => $this->faker->numberBetween(0, 10),
            'drawn' => $this->faker->numberBetween(0, 10),
            'lost' => $this->faker->numberBetween(0, 10),
            'played' => $this->faker->numberBetween(0, 38),
            'goals_for' => $this->faker->numberBetween(0, 50),
            'goals_against' => $this->faker->numberBetween(0, 50),
        ];
    }
}

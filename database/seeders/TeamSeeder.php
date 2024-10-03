<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            ['name' => 'Liverpool', 'strength' => 90],
            ['name' => 'Manchester City', 'strength' => 92],
            ['name' => 'Arsenal', 'strength' => 88],
            ['name' => 'Chelsea', 'strength' => 85],
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }
    }
}

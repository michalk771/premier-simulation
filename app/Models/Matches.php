<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Team;

class Matches extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = ['home_team_id', 'away_team_id', 'home_score', 'away_score', 'week_number'];

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
}

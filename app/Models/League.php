<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Team;

class League extends Model
{
    use HasFactory;

    protected $table = 'league';
    protected $fillable = ['team_id', 'played', 'won', 'drawn', 'lost', 'goals_for', 'goals_against', 'goal_difference', 'points'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}

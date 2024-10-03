<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LeagueService;

class LeagueController extends Controller
{
    protected $leagueService;

    public function __construct(LeagueService $leagueService)
    {
        $this->leagueService = $leagueService;
    }
}

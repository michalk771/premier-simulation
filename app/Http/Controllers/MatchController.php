<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MatchService;

class MatchController extends Controller
{
    protected $matchService;

    public function __construct(MatchService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function simulateWeek(Request $request)
    {
        $week = $request->input('week');
        $result = $this->matchService->simulateWeek($week);
        return response()->json(200);
    }

    public function getLatestWeeks()
    {
        return response()->json($this->matchService->getLatestWeeks());
    }
}

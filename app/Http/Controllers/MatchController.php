<?php

namespace App\Http\Controllers;

use App\Services\MatchService;
use App\Http\Requests\SimulateWeekRequest;

class MatchController extends Controller
{
    protected MatchService $matchService;

    public function __construct(MatchService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function simulateWeek(SimulateWeekRequest $request)
    {
        $week = $request->input('week');
        $this->matchService->simulateWeek($week);

        return response()->json(200);
    }

    public function getLatestWeeks()
    {
        return response()->json($this->matchService->getLatestWeeks());
    }
}

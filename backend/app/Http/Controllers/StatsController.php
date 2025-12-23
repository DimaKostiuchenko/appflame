<?php

namespace App\Http\Controllers;

use App\Actions\GetTodayStatisticsAction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function today(GetTodayStatisticsAction $action): JsonResponse
    {
        $today = Carbon::now('UTC');

        $stats = $action->handle($today);

        return response()->json([
            'date' => $today->format('Y-m-d'),
            'counts' => $stats['counts'],
            'total' => $stats['total'],
        ]);
    }
}

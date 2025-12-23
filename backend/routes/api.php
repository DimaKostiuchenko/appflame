<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->group(function () {

    Route::get('/test-auth', function () {
        return response()->json([
            'message' => 'API token authentication successful',
        ]);
    });

    Route::post('/events', [EventController::class, 'store']);
    Route::get('/stats/today', [StatsController::class, 'today']);
})->middleware('throttle:60,1');

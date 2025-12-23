<?php

namespace App\Http\Controllers;

use App\Actions\StoreEventAction;
use App\Http\Requests\StoreEventRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    use ApiResponse;

    public function store(StoreEventRequest $request, StoreEventAction $action): JsonResponse
    {
        $event = $action->handle(
            $request->validated(),
            $request->header('X-Idempotency-Key'),
            $request->ip()
        );

        $message = $event->wasRecentlyCreated ? 'Event created' : 'Event already processed';
        $code = $event->wasRecentlyCreated ? 201 : 200;

        return $this->response($message, ['event_id' => $event->id], $code);
    }
}

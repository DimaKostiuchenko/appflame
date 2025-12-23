<?php

namespace App\Actions;

use App\Models\Event;

class StoreEventAction
{
    public function handle(array $data, string $idempotencyKey, string $ip): Event
    {
        return Event::firstOrCreate(
            ['idempotency_key' => $idempotencyKey],
            [
                'type' => $data['type'],
                'ts' => $data['ts'],
                'session_id' => $data['session_id'],
            ]
        );
    }
}

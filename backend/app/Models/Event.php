<?php

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'type',
        'ts',
        'session_id',
        'idempotency_key',
    ];

    protected function casts(): array
    {
        return [
            'type' => EventType::class,
            'ts' => 'datetime',
        ];
    }

    public static function allowedTypes(): array
    {
        return EventType::values();
    }

}

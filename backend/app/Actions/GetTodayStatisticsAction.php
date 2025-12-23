<?php

namespace App\Actions;

use App\Enums\EventType;
use App\Models\Event;
use DateTimeInterface;

class GetTodayStatisticsAction
{
    public function handle(DateTimeInterface $date): array
    {
        $start = $date->copy()->startOfDay();
        $end = $start->copy()->addDay();

        $existingCounts = Event::whereBetween('ts', [$start, $end])
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        $counts = collect(EventType::values())
            ->mapWithKeys(fn ($type) => [$type => $existingCounts->get($type, 0)]);

        return [
            'counts' => $counts->toArray(),
            'total' => $counts->sum(),
        ];
    }
}

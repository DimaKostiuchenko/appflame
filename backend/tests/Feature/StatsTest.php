<?php

namespace Tests\Feature;

use App\Enums\EventType;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class StatsTest extends TestCase
{
    use RefreshDatabase;

    private string $apiToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Set a test API token
        $this->apiToken = 'test-api-token-'.uniqid();
        Config::set('app.api_token', $this->apiToken);
    }

    public function test_returns_today_stats_with_correct_structure(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->getJson('/api/stats/today');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'date',
                'counts' => [
                    EventType::PAGE_VIEW->value,
                    EventType::CTA_CLICK->value,
                    EventType::FORM_SUBMIT->value,
                ],
                'total',
            ]);

        $responseData = $response->json();
        $this->assertEquals(now('UTC')->startOfDay()->format('Y-m-d'), $responseData['date']);
        $this->assertIsInt($responseData['total']);
        $this->assertIsArray($responseData['counts']);
    }

    public function test_returns_correct_counts_for_each_event_type(): void
    {
        // Create events for today
        Event::create([
            'type' => EventType::PAGE_VIEW,
            'ts' => now('UTC')->startOfDay()->addHours(10),
            'session_id' => 'session-1',
            'idempotency_key' => 'key-1',
        ]);

        Event::create([
            'type' => EventType::PAGE_VIEW,
            'ts' => now('UTC')->startOfDay()->addHours(11),
            'session_id' => 'session-2',
            'idempotency_key' => 'key-2',
        ]);

        Event::create([
            'type' => EventType::CTA_CLICK,
            'ts' => now('UTC')->startOfDay()->addHours(12),
            'session_id' => 'session-3',
            'idempotency_key' => 'key-3',
        ]);

        Event::create([
            'type' => EventType::FORM_SUBMIT,
            'ts' => now('UTC')->startOfDay()->addHours(13),
            'session_id' => 'session-4',
            'idempotency_key' => 'key-4',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->getJson('/api/stats/today');

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertEquals(2, $responseData['counts'][EventType::PAGE_VIEW->value]);
        $this->assertEquals(1, $responseData['counts'][EventType::CTA_CLICK->value]);
        $this->assertEquals(1, $responseData['counts'][EventType::FORM_SUBMIT->value]);
        $this->assertEquals(4, $responseData['total']);
    }

    public function test_returns_zero_counts_for_event_types_with_no_events(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->getJson('/api/stats/today');

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertEquals(0, $responseData['counts'][EventType::PAGE_VIEW->value]);
        $this->assertEquals(0, $responseData['counts'][EventType::CTA_CLICK->value]);
        $this->assertEquals(0, $responseData['counts'][EventType::FORM_SUBMIT->value]);
        $this->assertEquals(0, $responseData['total']);
    }

    public function test_only_includes_events_from_today(): void
    {
        // Create events for today
        Event::create([
            'type' => EventType::PAGE_VIEW,
            'ts' => now('UTC')->startOfDay()->addHours(10),
            'session_id' => 'session-today-1',
            'idempotency_key' => 'key-today-1',
        ]);

        Event::create([
            'type' => EventType::CTA_CLICK,
            'ts' => now('UTC')->startOfDay()->addHours(23)->addMinutes(59),
            'session_id' => 'session-today-2',
            'idempotency_key' => 'key-today-2',
        ]);

        // Create event for yesterday (should not be included)
        Event::create([
            'type' => EventType::PAGE_VIEW,
            'ts' => now('UTC')->startOfDay()->subSecond(),
            'session_id' => 'session-yesterday',
            'idempotency_key' => 'key-yesterday',
        ]);

        // Create event for tomorrow (should not be included)
        // Use a time clearly in tomorrow (not exactly at midnight which would be included)
        Event::create([
            'type' => EventType::FORM_SUBMIT,
            'ts' => now('UTC')->startOfDay()->addDay()->addHour(),
            'session_id' => 'session-tomorrow',
            'idempotency_key' => 'key-tomorrow',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->getJson('/api/stats/today');

        $response->assertStatus(200);
        $responseData = $response->json();

        // Should only count today's events
        $this->assertEquals(1, $responseData['counts'][EventType::PAGE_VIEW->value]);
        $this->assertEquals(1, $responseData['counts'][EventType::CTA_CLICK->value]);
        $this->assertEquals(0, $responseData['counts'][EventType::FORM_SUBMIT->value]);
        $this->assertEquals(2, $responseData['total']);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/stats/today');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated. API token is required.',
            ]);
    }

    public function test_invalid_api_token_returns_unauthorized(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/stats/today');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated. Invalid API token.',
            ]);
    }

    public function test_calculates_total_correctly_with_multiple_events(): void
    {
        // Create 5 events of different types
        for ($i = 1; $i <= 5; $i++) {
            Event::create([
                'type' => EventType::PAGE_VIEW,
                'ts' => now('UTC')->startOfDay()->addHours($i),
                'session_id' => "session-{$i}",
                'idempotency_key' => "key-{$i}",
            ]);
        }

        // Create 3 events of another type
        for ($i = 6; $i <= 8; $i++) {
            Event::create([
                'type' => EventType::CTA_CLICK,
                'ts' => now('UTC')->startOfDay()->addHours($i),
                'session_id' => "session-{$i}",
                'idempotency_key' => "key-{$i}",
            ]);
        }

        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->getJson('/api/stats/today');

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertEquals(5, $responseData['counts'][EventType::PAGE_VIEW->value]);
        $this->assertEquals(3, $responseData['counts'][EventType::CTA_CLICK->value]);
        $this->assertEquals(0, $responseData['counts'][EventType::FORM_SUBMIT->value]);
        $this->assertEquals(8, $responseData['total']);
    }

    public function test_returns_date_in_correct_format(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->getJson('/api/stats/today');

        $response->assertStatus(200);
        $responseData = $response->json();

        // Should be in Y-m-d format
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $responseData['date']);
        $this->assertEquals(now('UTC')->startOfDay()->format('Y-m-d'), $responseData['date']);
    }
}

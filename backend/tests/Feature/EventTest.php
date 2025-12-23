<?php

namespace Tests\Feature;

use App\Enums\EventType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class EventTest extends TestCase
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

    public function test_user_can_create_event_with_valid_data(): void
    {
        $eventData = [
            'type' => EventType::PAGE_VIEW->value,
            'ts' => '2024-01-15T10:30:00.000Z',
            'session_id' => 'session-123',
        ];

        $idempotencyKey = 'idempotency-key-'.uniqid();

        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson('/api/events', $eventData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'event_id',
            ])
            ->assertJson([
                'message' => 'Event created',
            ]);

        $this->assertDatabaseHas('events', [
            'type' => EventType::PAGE_VIEW->value,
            'session_id' => 'session-123',
            'idempotency_key' => $idempotencyKey,
        ]);

        $responseData = $response->json();
        $this->assertDatabaseHas('events', [
            'id' => $responseData['event_id'],
        ]);
    }

    public function test_duplicate_idempotency_key_returns_existing_event(): void
    {
        $eventData = [
            'type' => EventType::CTA_CLICK->value,
            'ts' => '2024-01-15T10:30:00.000Z',
            'session_id' => 'session-456',
        ];

        $idempotencyKey = 'idempotency-key-'.uniqid();

        // First request - should create event
        $firstResponse = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson('/api/events', $eventData);

        $firstResponse->assertStatus(201);
        $eventId = $firstResponse->json()['event_id'];

        // Second request with same idempotency key - should return existing event
        $secondResponse = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson('/api/events', $eventData);

        $secondResponse->assertStatus(200)
            ->assertJson([
                'message' => 'Event already processed',
                'event_id' => $eventId,
            ]);

        // Verify only one event exists in database
        $this->assertDatabaseCount('events', 1);
    }

    public function test_missing_idempotency_key_returns_error(): void
    {
        $eventData = [
            'type' => EventType::FORM_SUBMIT->value,
            'ts' => '2024-01-15T10:30:00.000Z',
            'session_id' => 'session-789',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->postJson('/api/events', $eventData);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'X-Idempotency-Key header is required',
            ]);
    }

    public function test_event_creation_validates_required_fields(): void
    {
        // Test missing type
        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->withHeader('X-Idempotency-Key', 'test-key')
            ->postJson('/api/events', [
                'ts' => '2024-01-15T10:30:00.000Z',
                'session_id' => 'session-123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);

        // Test invalid type
        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->withHeader('X-Idempotency-Key', 'test-key-2')
            ->postJson('/api/events', [
                'type' => 'invalid_type',
                'ts' => '2024-01-15T10:30:00.000Z',
                'session_id' => 'session-123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);

        // Test missing ts
        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->withHeader('X-Idempotency-Key', 'test-key-3')
            ->postJson('/api/events', [
                'type' => EventType::PAGE_VIEW->value,
                'session_id' => 'session-123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ts']);

        // Test invalid ts format
        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->withHeader('X-Idempotency-Key', 'test-key-4')
            ->postJson('/api/events', [
                'type' => EventType::PAGE_VIEW->value,
                'ts' => '2024-01-15 10:30:00',
                'session_id' => 'session-123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ts']);

        // Test missing session_id
        $response = $this->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->withHeader('X-Idempotency-Key', 'test-key-5')
            ->postJson('/api/events', [
                'type' => EventType::PAGE_VIEW->value,
                'ts' => '2024-01-15T10:30:00.000Z',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['session_id']);
    }

    public function test_event_creation_requires_authentication(): void
    {
        $eventData = [
            'type' => EventType::PAGE_VIEW->value,
            'ts' => '2024-01-15T10:30:00.000Z',
            'session_id' => 'session-123',
        ];

        $response = $this->withHeader('X-Idempotency-Key', 'test-key')
            ->postJson('/api/events', $eventData);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated. API token is required.',
            ]);
    }

    public function test_invalid_api_token_returns_unauthorized(): void
    {
        $eventData = [
            'type' => EventType::PAGE_VIEW->value,
            'ts' => '2024-01-15T10:30:00.000Z',
            'session_id' => 'session-123',
        ];

        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->withHeader('X-Idempotency-Key', 'test-key')
            ->postJson('/api/events', $eventData);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated. Invalid API token.',
            ]);
    }
}

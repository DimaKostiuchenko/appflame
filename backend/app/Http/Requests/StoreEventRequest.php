<?php

namespace App\Http\Requests;

use App\Enums\EventType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->header('X-Idempotency-Key')) {
            throw new HttpResponseException(
                response()->json(['error' => 'X-Idempotency-Key header is required'], 400)
            );
        }
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(EventType::class)],
            'ts' => ['required', 'date_format:Y-m-d\TH:i:s.v\Z'],
            'session_id' => ['required', 'string', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'ts.date_format' => 'Timestamp must be in ISO 8601 format (e.g., 2024-01-15T10:30:00.000Z)',
        ];
    }
}

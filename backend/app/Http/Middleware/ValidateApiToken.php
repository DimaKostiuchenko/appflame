<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return $this->response('Unauthenticated. API token is required.', [], 401);
        }

        $validToken = config('app.api_token');

        if (! $validToken || $token !== $validToken) {
            Log::warning('API authentication failed: invalid token', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            return $this->response('Unauthenticated. Invalid API token.', [], 401);
        }

        return $next($request);
    }
}

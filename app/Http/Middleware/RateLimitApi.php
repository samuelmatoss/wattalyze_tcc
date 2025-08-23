<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RateLimitApi
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        // Determina a chave de limitação com base no tipo de requisição
        if ($request->is('api/energy-data*') && $request->has('device_id')) {
            $key = 'device:' . $request->device_id;
        } elseif ($request->user()) {
            $key = 'user:' . $request->user()->id;
        } else {
            $key = 'ip:' . $request->ip();
        }

        $executed = RateLimiter::attempt(
            $key,
            $maxAttempts,
            function() {},
            $decayMinutes * 60
        );

        if (!$executed) {
            $retryAfter = RateLimiter::availableIn($key);
            
            return response()->json([
                'error' => 'Too many requests',
                'retry_after' => $retryAfter,
                'rate_limit' => $maxAttempts,
                'rate_period' => $decayMinutes * 60 . ' seconds',
            ], Response::HTTP_TOO_MANY_REQUESTS, [
                'Retry-After' => $retryAfter,
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
                'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->getTimestamp(),
            ]);
        }

        $response = $next($request);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $maxAttempts),
        ]);
    }
}
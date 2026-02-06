<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiToken = config('services.api.token');
        $requestToken = $request->header('X-API-KEY');

        // Check if token is configured in environment
        if (empty($apiToken)) {
            Log::critical('API_ACCESS_TOKEN is not configured in the environment.');
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

        // Use hash_equals for constant-time comparison to prevent timing attacks
        if (!$requestToken || !hash_equals($apiToken, $requestToken)) {
            Log::warning('Unauthorized API access attempt.', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'provided_token_excerpt' => substr((string) $requestToken, 0, 5) . '...' // Log only partial token for debugging
            ]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class CheckApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-IAE-KEY');
        $validApiKey = config('services.api_key');

        if ($apiKey !== $validApiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized - Invalid API Key',
                'errors' => null,
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
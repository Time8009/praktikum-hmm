<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = env('API_KEY');
        $providedKey = $request->header('x-api-key') ?? $request->header('X-API-KEY');

        if (!$providedKey || $providedKey !== $expectedKey) {
            return response()->json([
                'success' => false,
                'message' => 'Nicht autorisiert. Ungültiger oder fehlender API-Schlüssel.'
            ], 401);
        }

        return $next($request);
    }
}
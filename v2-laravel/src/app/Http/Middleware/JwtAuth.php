<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JwtAuth
{
    public function handle(Request $request, Closure $next, ?string $requiredRole = null)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Kein Token bereitgestellt'], 401);
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return response()->json(['error' => 'Ungültiges Token-Format'], 401);
        }

        [$base64Header, $base64Payload, $providedSignature] = $parts;

        $secret = env('JWT_SECRET', 'fallback_secret_key_123456');
        $expectedSignature = rtrim(strtr(base64_encode(hash_hmac('sha256', "$base64Header.$base64Payload", $secret, true)), '+/', '-_'), '=');

        if (!hash_equals($expectedSignature, $providedSignature)) {
            return response()->json(['error' => 'Token-Signatur ungültig'], 401);
        }

        $payload = json_decode(base64_decode($base64Payload), true);

        if (!is_array($payload) || ($payload['exp'] ?? 0) < time()) {
            return response()->json(['error' => 'Token abgelaufen oder ungültig'], 401);
        }

        if ($requiredRole !== null && ($payload['role'] ?? '') !== $requiredRole) {
            return response()->json(['error' => 'Zugriff verweigert: Rolle ' . $requiredRole . ' erforderlich'], 403);
        }

        $request->attributes->set('auth_user', $payload);

        return $next($request);
    }
}
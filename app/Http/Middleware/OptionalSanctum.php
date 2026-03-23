<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Optionally authenticate the request via Sanctum Bearer token.
 * If Authorization: Bearer <token> is present and valid, sets the user; otherwise continues as guest.
 * Used for routes that accept either Bearer token or other auth (e.g. viewer_token).
 */
class OptionalSanctum
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->tokenable) {
                Auth::setUser($accessToken->tokenable);
            }
        }

        return $next($request);
    }
}

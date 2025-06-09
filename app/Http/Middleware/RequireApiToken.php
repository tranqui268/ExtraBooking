<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class RequireApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'API token required'], 401);
        }
        
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken || !$accessToken->tokenable) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
        
        auth()->setUser($accessToken->tokenable);
        return $next($request);
    }
}

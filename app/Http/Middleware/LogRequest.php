<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       Log::debug('API Request', [
            'url' => $request->url(),
            'method' => $request->method(),
            'input' => $request->all(),
            'headers' => $request->headers->all(),
        ]);
        $response = $next($request);
        Log::debug('API Response', [
            'status' => $response->getStatusCode(),
            'content' => substr($response->getContent(), 0, 500),
        ]);
        return $response;
    }
}

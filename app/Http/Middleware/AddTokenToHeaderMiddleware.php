<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AddTokenToHeaderMiddleware
{

    public function handle(Request $request, Closure $next):Response
    {
        
        $token = $request->cookie('auth_token');     

        if ($token && !$request->hasHeader('Authorization')) {
            $request->headers->set('Authorization','Bearer ' . $token);
        }

        return $next($request);
    }
}

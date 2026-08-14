<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{   
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'data' => null
            ], 401);
        }

        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required',
                'data' => null
            ], 403);
        }

        return $next($request);
    }
}
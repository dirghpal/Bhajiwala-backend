<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Exceptions\ApiStatusException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            throw new ApiStatusException(
                'Unauthenticated',
                401
            );
        }

        if (Auth::user()->role !== 'admin') {
            throw new ApiStatusException(
                'Admin access required',
                403
            );
        }

        return $next($request);
    }
}
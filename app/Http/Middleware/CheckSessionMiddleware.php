<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSessionMiddleware
{
    public function handle($request, Closure $next)
    {
        // Check if the session is missing
        if (!session()->has('user_id')) {
            // Redirect to login page if the session is missing
            return redirect()->route('login');
        }

        return $next($request);
    }
}

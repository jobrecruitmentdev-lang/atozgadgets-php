<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Check if user role is Admin (1) or Staff (2)
            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2) {
                return $next($request);
            }
        }

        return abort(403, 'Unauthorized access.');
    }
}

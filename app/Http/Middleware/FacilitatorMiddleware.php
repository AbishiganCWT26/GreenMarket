<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilitatorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'facilitator') {
            return redirect()->route('unauthorized')->with('error', 'Facilitator can only access this page');
        }

        return $next($request);
    }
}

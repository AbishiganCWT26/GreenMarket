<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadFarmerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'lead_farmer') {
            abort(403, 'Unauthorized access. Lead Farmer only.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryRiderMiddleware
{
	public function handle(Request $request, Closure $next)
	{
		if (!Auth::check()) {
			return redirect()->route('login');
		}

		if (Auth::user()->role !== 'delivery_rider') {
			return redirect()->route('unauthorized')->with('error', 'Delivery riders can only access this page');
		}

		return $next($request);
	}
}

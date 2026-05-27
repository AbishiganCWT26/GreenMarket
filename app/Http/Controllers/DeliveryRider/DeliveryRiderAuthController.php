<?php

namespace App\Http\Controllers\DeliveryRider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryRiderAuthController extends Controller
{
    /**
     * Show login form if specific to rider, otherwise redirect to main login.
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'delivery_rider') {
            return redirect()->route('delivery-rider.dashboard');
        }
        return redirect()->route('login');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}

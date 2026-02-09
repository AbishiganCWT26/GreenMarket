<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Order;

class LeadFarmerComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check() && Auth::user()->role === 'lead_farmer' && Auth::user()->leadFarmer) {
            $leadFarmerId = Auth::user()->leadFarmer->id;
            $userId = Auth::id();

            // Fetch recent notifications
            $recentNotifications = Notification::where('user_id', $userId)
                ->orWhere(function($query) use ($leadFarmerId) {
                    $query->where('related_id', $leadFarmerId)
                        ->where('recipient_type', 'lead_farmer');
                })
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // Fetch shared counts
            $sharedCounts = [
                'lowStockProducts' => Product::where('lead_farmer_id', $leadFarmerId)
                    ->where('quantity', '<', 10)
                    ->count(),
                'pendingOrders' => Order::where('lead_farmer_id', $leadFarmerId)
                    ->where('order_status', 'pending')
                    ->count(),
                'unreadNotifications' => Notification::where(function($query) use ($leadFarmerId, $userId) {
                        $query->where('user_id', $userId)
                            ->orWhere(function($q) use ($leadFarmerId) {
                                $q->where('related_id', $leadFarmerId)
                                    ->where('recipient_type', 'lead_farmer');
                            });
                    })
                    ->where('is_read', false)
                    ->count(),
            ];

            $view->with('recentNotifications', $recentNotifications);
            $view->with('sharedCounts', $sharedCounts);
        }
    }
}

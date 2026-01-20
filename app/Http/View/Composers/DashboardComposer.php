<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardComposer
{
    public function compose(View $view)
    {
        // USER COUNTS
        $totalUsers = DB::table('users')->count();
        $admins = DB::table('users')->where('role', 'admin')->count();
        $leadFarmers = DB::table('users')->where('role', 'lead_farmer')->count();
        $farmers = DB::table('users')->where('role', 'farmer')->count();
        $buyers = DB::table('users')->where('role', 'buyer')->count();
        $facilitators = DB::table('users')->where('role', 'facilitator')->count();

        // PRODUCT COUNT
        $products = DB::table('products')->count();

        // SALES
        $sales = DB::table('orders')
            ->where('order_status', 'paid')
            ->sum('total_amount');

        // GROUPS RANKING
        $groups = DB::table('lead_farmers')
            ->leftJoin('orders', 'lead_farmers.id', '=', 'orders.lead_farmer_id')
            ->select(
                'lead_farmers.id',
                'lead_farmers.group_name',
                DB::raw('COALESCE(SUM(orders.total_amount), 0) AS total_sales'),
                DB::raw('(SELECT COUNT(*) FROM farmers
                    WHERE farmers.lead_farmer_id = lead_farmers.id
                    AND farmers.is_active = TRUE) AS active_farmers'),
                DB::raw('ROUND((RANDOM() * 35) + 60) AS success_rate'),
                DB::raw('ROW_NUMBER() OVER (ORDER BY COALESCE(SUM(orders.total_amount),0) DESC NULLS LAST) AS rank')
            )
            ->groupBy('lead_farmers.id', 'lead_farmers.group_name')
            ->orderBy('total_sales', 'DESC')
            ->limit(10)
            ->get();

        // RECENT COMPLAINTS
        $complaints = DB::table('complaints')
            ->leftJoin('users as complainant', 'complaints.complainant_user_id', '=', 'complainant.id')
            ->leftJoin('users as against', 'complaints.against_user_id', '=', 'against.id')
            ->select(
                'complaints.*',
                'complainant.username as complainant_name',
                'against.username as against_name'
            )
            ->orderBy('complaints.created_at', 'desc')
            ->limit(20)
            ->get();

        $view->with([
            'totalUsers' => $totalUsers,
            'admins' => $admins,
            'leadFarmers' => $leadFarmers,
            'farmers' => $farmers,
            'buyers' => $buyers,
            'facilitators' => $facilitators,
            'products' => $products,
            'sales' => $sales,
            'groups' => $groups,
            'complaints' => $complaints
        ]);
    }
}

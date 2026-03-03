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
            ->whereIn('order_status', ['paid', 'completed'])
            ->sum('total_amount');

        // GROUPS RANKING
        $groups = DB::table('lead_farmers')
            ->leftJoin('orders', function($join) {
                $join->on('lead_farmers.id', '=', 'orders.lead_farmer_id')
                     ->whereIn('orders.order_status', ['paid', 'completed']);
            })
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
            ->paginate(5, ['*'], 'groups_page');

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
            ->paginate(5, ['*'], 'complaints_page');

        // FACILITATORS LIST FOR DROPDOWN
        $facilitatorsList = DB::table('facilitators')
            ->join('users', 'facilitators.user_id', '=', 'users.id')
            ->where('users.is_active', true)
            ->select('facilitators.user_id', 'facilitators.name', 'facilitators.assigned_division')
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
            'complaints' => $complaints,
            'facilitatorsList' => $facilitatorsList
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class LeadfarmerControlleradmin extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10); // Default 10 items per page

            $groups = Cache::remember('lead_farmer_groups_stats', 3600, function () {
                // First, get basic group info
                $query = DB::table('lead_farmers as lf')
                    ->select(
                        'lf.id',
                        'lf.group_name',
                        'lf.group_number',
                        // Active farmers count
                        DB::raw('COUNT(DISTINCT CASE WHEN f.is_active = true THEN f.id END) as active_farmers'),
                        // Total products count
                        DB::raw('COUNT(DISTINCT CASE WHEN p.is_available = true THEN p.id END) as total_products'),
                        // Sales count (distinct orders)
                        DB::raw('COUNT(DISTINCT o.id) as sales_count'),
                        // Total sales - FIXED: Sum of total_amount from orders table
                        DB::raw('(
                            SELECT COALESCE(SUM(orders_sum.total_amount), 0)
                            FROM orders AS orders_sum
                            WHERE orders_sum.lead_farmer_id = lf.id
                        ) as total_sales'),
                        // Success rate calculation
                        DB::raw('
                            CASE
                                WHEN COUNT(DISTINCT o.id) > 0
                                THEN (
                                    COUNT(DISTINCT CASE WHEN o.order_status IN (\'paid\', \'completed\') THEN o.id END) * 100.0 /
                                    COUNT(DISTINCT o.id)
                                )
                                ELSE 0
                            END as success_rate
                        ')
                    )
                    ->leftJoin('farmers as f', 'lf.id', '=', 'f.lead_farmer_id')
                    ->leftJoin('products as p', 'lf.id', '=', 'p.lead_farmer_id')
                    ->leftJoin('orders as o', 'lf.id', '=', 'o.lead_farmer_id')
                    ->groupBy('lf.id', 'lf.group_name', 'lf.group_number')
                    ->orderBy('success_rate', 'desc')
                    ->orderBy('total_sales', 'desc')
                    ->get();

                $rank = 1;
                foreach ($query as $group) {
                    $group->rank = $rank++;
                    $group->success_rate_formatted = number_format($group->success_rate, 1) . '%';
                    $group->total_sales_formatted = 'LKR ' . number_format($group->total_sales, 2);

                    $colorClass = '';
                    if ($group->success_rate >= 80) {
                        $colorClass = 'success-high';
                    } elseif ($group->success_rate >= 60) {
                        $colorClass = 'success-medium';
                    } elseif ($group->success_rate >= 40) {
                        $colorClass = 'success-low';
                    } else {
                        $colorClass = 'success-poor';
                    }
                    $group->color_class = $colorClass;
                }

                return $query;
            });

            // Get current page from request
            $currentPage = $request->get('page', 1);

            // Create paginator
            $paginatedGroups = new LengthAwarePaginator(
                $groups->forPage($currentPage, $perPage),
                $groups->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );

            return view('admin.lead-farmer-groups.index', compact('paginatedGroups'));
        } catch (\Exception $e) {
            \Log::error('Lead farmer groups error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load lead farmer groups data.');
        }
    }
}

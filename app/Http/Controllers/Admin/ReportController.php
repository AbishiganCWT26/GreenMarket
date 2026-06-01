<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function generate()
    {
        return view('admin.reports.generate');
    }

    private function getReportData($type, $filters = [])
    {
        $fromDate = $filters['from_date'] ?? Carbon::now()->subMonth();
        $toDate = $filters['to_date'] ?? Carbon::now();

        switch ($type) {
            case 'order-history':
                $data = DB::table('orders')
                    ->select(
                        'orders.id as order_id',
                        'orders.order_number',
                        'buyers.name as buyer_name',
                        'farmers.name as farmer_name',
                        'orders.order_status',
                        'orders.created_at',
                        'orders.total_amount',
                        'payments.payment_method'
                    )
                    ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
                    ->leftJoin('farmers', 'orders.farmer_id', '=', 'farmers.id')
                    ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->orderBy('orders.created_at', 'desc')
                    ->get();
                break;















            case 'inventory-stock':
                $data = DB::table('products')
                    ->select(
                        'products.product_name',
                        'farmers.name as farmer_name',
                        'products.quantity',
                        'products.unit_of_measure',
                        'products.quality_grade',
                        'products.selling_price',
                        'products.expected_availability_date',
                        'products.product_status'
                    )
                    ->leftJoin('farmers', 'products.farmer_id', '=', 'farmers.id')
                    ->where('products.is_available', true)
                    ->whereBetween('products.created_at', [$fromDate, $toDate])
                    ->orderBy('products.product_name')
                    ->get();
                break;

            case 'category-performance':
                // Subquery to get total quantity listed per category
                $inventorySub = DB::table('products')
                    ->select('category_id', DB::raw("SUM(quantity) as total_qty"))
                    ->groupBy('category_id');

                // Subquery to get sales data per category in the date range
                $salesSub = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->select('products.category_id', 
                        DB::raw("SUM(order_items.quantity_ordered) as total_sold"),
                        DB::raw("SUM(order_items.item_total) as revenue")
                    )
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy('products.category_id');

                $data = DB::table('product_categories')
                    ->select(
                        'product_categories.category_name',
                        DB::raw("COALESCE(inventory.total_qty, 0) as total_product_quantity"),
                        DB::raw("COALESCE(sales.total_sold, 0) as total_sold"),
                        DB::raw("COALESCE(sales.revenue, 0) as revenue")
                    )
                    ->leftJoinSub($inventorySub, 'inventory', 'product_categories.id', '=', 'inventory.category_id')
                    ->leftJoinSub($salesSub, 'sales', 'product_categories.id', '=', 'sales.category_id')
                    ->orderBy('revenue', 'desc')
                    ->get();
                break;

            case 'stock-movement':
                $data = DB::table('products')
                    ->select(
                        'products.product_name',
                        DB::raw("products.quantity as ending_quantity"),
                        DB::raw("COALESCE(SUM(order_items.quantity_ordered), 0) as quantity_sold"),
                        DB::raw("COALESCE(SUM(order_items.quantity_ordered), 0) + products.quantity as starting_quantity"),
                        DB::raw("MAX(orders.created_at) as movement_date")
                    )
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->leftJoin('orders', function($join) use ($fromDate, $toDate) {
                        $join->on('order_items.order_id', '=', 'orders.id')
                             ->whereBetween('orders.created_at', [$fromDate, $toDate]);
                    })
                    ->groupBy('products.id', 'products.product_name', 'products.quantity')
                    ->having(DB::raw("COALESCE(SUM(order_items.quantity_ordered), 0)"), '>', 0)
                    ->orderBy('movement_date', 'desc')
                    ->get();
                break;

            case 'group-performance':
                $data = DB::table('lead_farmers')
                    ->select(
                        'lead_farmers.name as lead_farmer_name',
                        'lead_farmers.group_name',
                        DB::raw("COUNT(DISTINCT farmers.id) as total_farmers_managed"),
                        DB::raw("COUNT(DISTINCT CASE WHEN products.id IS NOT NULL THEN farmers.id END) as active_farmers"),
                        DB::raw("COALESCE(SUM(order_items.quantity_ordered), 0) as total_quantity_sold"),
                        DB::raw("COALESCE(SUM(order_items.item_total), 0) as total_revenue")
                    )
                    ->leftJoin('farmers', 'lead_farmers.id', '=', 'farmers.lead_farmer_id')
                    ->leftJoin('products', 'farmers.id', '=', 'products.farmer_id')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy('lead_farmers.id', 'lead_farmers.name', 'lead_farmers.group_name')
                    ->orderBy('total_revenue', 'desc')
                    ->get();
                break;

            case 'farmer-registration':
                $data = DB::table('farmers')
                    ->select(
                        'farmers.name',
                        'farmers.created_at as registration_date',
                        DB::raw("MAX(users.last_login) as last_login"),
                        DB::raw("COUNT(products.id) as product_listings"),
                        DB::raw("CASE WHEN farmers.is_active = true THEN 'Active' ELSE 'Not Active' END as is_active")
                    )
                    ->leftJoin('products', 'farmers.id', '=', 'products.farmer_id')
                    ->leftJoin('users', 'farmers.user_id', '=', 'users.id')
                    ->whereBetween('farmers.created_at', [$fromDate, $toDate])
                    ->groupBy('farmers.id', 'farmers.name', 'farmers.created_at', 'farmers.is_active')
                    ->orderBy('farmers.created_at', 'desc')
                    ->get();
                break;

            case 'system-adoption':
                $data = DB::table('users')
                    ->select(
                        'role',
                        DB::raw("COUNT(*) as total_users"),
                        DB::raw("SUM(CASE WHEN last_login >= NOW() - INTERVAL '30 days' THEN 1 ELSE 0 END) as active_users"),
                        DB::raw("SUM(CASE WHEN created_at >= NOW() - INTERVAL '7 days' THEN 1 ELSE 0 END) as new_registrations_week"),
                        DB::raw("SUM(CASE WHEN is_active = true THEN 1 ELSE 0 END) as active_accounts")
                    )
                    ->whereBetween('created_at', [$fromDate, $toDate])
                    ->groupBy('role')
                    ->orderBy('role')
                    ->get();
                break;

            case 'user-access':
                $data = DB::table('users')
                    ->select(
                        'username',
                        'role',
                        'last_login',
                        DB::raw("CASE WHEN is_active = true THEN 'Active' ELSE 'Not Active' END as is_active"),
                        DB::raw("0 as login_count")
                    )
                    ->whereBetween('created_at', [$fromDate, $toDate])
                    ->orderBy('last_login', 'desc')
                    ->get();
                break;



            case 'dispute-feedback':
                $data = DB::table('complaints')
                    ->select(
                        'complaints.id as complaint_id',
                        'users.username as complainant',
                        'complaints.complaint_type',
                        'complaints.status',
                        'complaints.created_at',
                        DB::raw("DATE_PART('day', complaints.updated_at - complaints.created_at) as resolution_time"),
                        'product_feedback.rating'
                    )
                    ->leftJoin('users', 'complaints.complainant_user_id', '=', 'users.id')
                    ->leftJoin('product_feedback', 'complaints.related_order_id', '=', 'product_feedback.order_id')
                    ->whereBetween('complaints.created_at', [$fromDate, $toDate])
                    ->orderBy('complaints.created_at', 'desc')
                    ->get();
                break;

            case 'regional-performance':
                $data = DB::table('farmers')
                    ->select(
                        'farmers.district as region',
                        DB::raw("COUNT(DISTINCT farmers.id) as total_farmers"),
                        DB::raw("COUNT(DISTINCT CASE WHEN orders.id IS NOT NULL THEN farmers.id END) as active_farmers"),
                        DB::raw("COUNT(DISTINCT products.id) as total_products"),
                        DB::raw("COUNT(DISTINCT buyers.id) as active_buyers"),
                        DB::raw("COUNT(DISTINCT orders.id) as total_orders"),
                        DB::raw("COALESCE(SUM(order_items.item_total), 0) as total_sales"),
                        DB::raw("CASE WHEN COUNT(DISTINCT orders.id) > 0 
                                 THEN COALESCE(SUM(order_items.item_total), 0) / COUNT(DISTINCT orders.id) 
                                 ELSE 0 END as avg_order_value")
                    )
                    ->leftJoin('products', 'farmers.id', '=', 'products.farmer_id')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->leftJoin('orders', function($join) use ($fromDate, $toDate) {
                        $join->on('order_items.order_id', '=', 'orders.id')
                             ->whereBetween('orders.created_at', [$fromDate, $toDate]);
                    })
                    ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
                    ->groupBy('farmers.district')
                    ->orderBy('total_sales', 'desc')
                    ->get();
                break;

            case 'order-fulfillment':
                $data = DB::table('orders')
                    ->select(
                        'orders.id as order_id',
                        'orders.created_at as order_date',
                        'payments.payment_date',
                        'orders.paid_date as pickup_date',
                        'orders.completed_date',
                        DB::raw("DATE_PART('day', orders.completed_date - orders.created_at) as total_duration")
                    )
                    ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->where('orders.order_status', 'completed')
                    ->orderBy('orders.created_at', 'desc')
                    ->get();
                break;

            case 'product-taxonomy':
                $data = DB::table('product_categories')
                    ->select(
                        'product_categories.category_name',
                        DB::raw("CASE WHEN product_categories.is_active = true THEN 'Active' ELSE 'Not Active' END as category_active"),
                        'product_subcategories.subcategory_name',
                        DB::raw("CASE WHEN product_subcategories.is_active = true THEN 'Active' ELSE 'Not Active' END as subcategory_active"),
                        DB::raw("COUNT(DISTINCT products.id) as listings_count"),
                        DB::raw("COALESCE(SUM(orders.total_amount), 0) as total_sales")
                    )
                    ->leftJoin('product_subcategories', 'product_categories.id', '=', 'product_subcategories.category_id')
                    ->leftJoin('products', 'product_subcategories.id', '=', 'products.subcategory_id')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
                    ->groupBy('product_categories.id', 'product_categories.category_name', 'product_categories.is_active',
                             'product_subcategories.id', 'product_subcategories.subcategory_name', 'product_subcategories.is_active')
                    ->orderBy('product_categories.category_name')
                    ->orderBy('product_subcategories.subcategory_name')
                    ->get();
                break;





            default:
                $data = [];
        }

        return $data;
    }

    public function viewReport($reportType)
    {
        $filters = request()->all();
        $data = $this->getReportData($reportType, $filters);

        $reportTitles = [
            'order-history' => 'Order History Report',
            'inventory-stock' => 'Current Inventory / Stock Report',
            'category-performance' => 'Product Category Performance Report',
            'stock-movement' => 'Stock Movement Report',
            'group-performance' => 'Group Farmer Performance Report',
            'farmer-registration' => 'Farmer Registration Status Report',
            'system-adoption' => 'System Adoption & User Count Report',
            'user-access' => 'User Access & Role Management Report',
            'dispute-feedback' => 'Dispute & Feedback Log Report',
            'regional-performance' => 'Regional Performance & Sales Density Report',
            'order-fulfillment' => 'Order Fulfillment Timeline Report',
            'product-taxonomy' => 'Product Taxonomy Report',

        ];

        return view('admin.reports.view', [
            'data' => $data,
            'reportType' => $reportType,
            'reportTitle' => $reportTitles[$reportType] ?? 'Report',
            'filters' => $filters
        ]);
    }

    public function generatePDF($reportType)
    {
        $filters = request()->all();
        $data = $this->getReportData($reportType, $filters);

        $reportTitles = [
            'order-history' => 'Order History Report',
            'inventory-stock' => 'Current Inventory / Stock Report',
            'category-performance' => 'Product Category Performance Report',
            'stock-movement' => 'Stock Movement Report',
            'group-performance' => 'Group Farmer Performance Report',
            'farmer-registration' => 'Farmer Registration Status Report',
            'system-adoption' => 'System Adoption & User Count Report',
            'user-access' => 'User Access & Role Management Report',
            'dispute-feedback' => 'Dispute & Feedback Log Report',
            'regional-performance' => 'Regional Performance & Sales Density Report',
            'order-fulfillment' => 'Order Fulfillment Timeline Report',
            'product-taxonomy' => 'Product Taxonomy Report',

        ];

        $pdf = PDF::loadView('admin.reports.templates.' . $reportType, [
            'data' => $data,
            'reportTitle' => $reportTitles[$reportType] ?? 'Report',
            'filters' => $filters,
            'generatedAt' => Carbon::now()->format('Y-m-d H:i:s')
        ])->setPaper('a4', 'landscape');

        $title = str_replace(['/', '\\'], '-', $reportTitles[$reportType]);

        return $pdf->download($title . '_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function customReport(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'format' => 'required|in:view,pdf',
            'status_filter' => 'nullable|string',
            'user_type' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'report_title' => 'nullable|string'
        ]);

        if ($validated['format'] === 'pdf') {
            return $this->generatePDF($validated['report_type']);
        }

        return $this->viewReport($validated['report_type']);
    }
}
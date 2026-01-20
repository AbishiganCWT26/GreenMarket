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

            case 'pending-pickup':
                $data = DB::table('orders')
                    ->select(
                        'orders.id as order_id',
                        'buyers.name as buyer_name',
                        'farmers.name as farmer_name',
                        DB::raw("STRING_AGG(products.product_name, ', ') as product_names"),
                        'orders.total_amount',
                        'orders.paid_date',
                        DB::raw("DATE_PART('day', NOW() - orders.paid_date) as days_since_paid"),
                        'farmers.residential_address as pickup_location'
                    )
                    ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
                    ->leftJoin('farmers', 'orders.farmer_id', '=', 'farmers.id')
                    ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                    ->where('orders.order_status', 'confirmed')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy('orders.id', 'buyers.name', 'farmers.name', 'orders.total_amount', 'orders.paid_date', 'farmers.residential_address')
                    ->orderBy('orders.paid_date')
                    ->get();
                break;

            case 'sales-volume':
                $data = DB::table('orders')
                    ->select(
                        DB::raw("DATE(orders.created_at) as period"),
                        DB::raw("COUNT(orders.id) as total_orders"),
                        DB::raw("SUM(order_items.quantity_ordered) as total_quantity"),
                        DB::raw("SUM(orders.total_amount) as total_sales"),
                        DB::raw("AVG(orders.total_amount) as avg_order_value")
                    )
                    ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.order_status', 'completed')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy(DB::raw("DATE(orders.created_at)"))
                    ->orderBy('period')
                    ->get();
                break;

            case 'sales-payment':
                $data = DB::table('orders')
                    ->select(
                        DB::raw("COUNT(orders.id) as total_orders"),
                        DB::raw("SUM(orders.total_amount) as total_sales_value"),
                        DB::raw("SUM(payments.amount) as total_amount_received"),
                        DB::raw("AVG(orders.total_amount) as avg_order_value")
                    )
                    ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
                    ->where('orders.order_status', 'completed')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->first();
                break;

            case 'system-financial':
                $data = DB::table('orders')
                    ->select(
                        DB::raw("COUNT(orders.id) as total_orders"),
                        DB::raw("SUM(orders.total_amount) as total_revenue"),
                        DB::raw("COUNT(DISTINCT buyers.id) as active_buyers"),
                        DB::raw("COUNT(DISTINCT farmers.id) as active_farmers"),
                        DB::raw("AVG(orders.total_amount) as avg_order_value")
                    )
                    ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
                    ->leftJoin('farmers', 'orders.farmer_id', '=', 'farmers.id')
                    ->where('orders.order_status', 'completed')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->first();
                break;

            case 'daily-cash':
                $data = DB::table('orders')
                    ->select(
                        DB::raw("DATE(orders.paid_date) as date"),
                        DB::raw("COUNT(CASE WHEN payments.payment_method = 'COD' THEN orders.id END) as total_cod_orders"),
                        DB::raw("SUM(CASE WHEN payments.payment_method = 'COD' THEN payments.amount ELSE 0 END) as collected_amount"),
                        DB::raw("SUM(CASE WHEN orders.order_status = 'confirmed' AND payments.payment_method = 'COD' THEN orders.total_amount ELSE 0 END) as outstanding_amount")
                    )
                    ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy(DB::raw("DATE(orders.paid_date)"))
                    ->orderBy('date')
                    ->get();
                break;

            case 'cash-collection-delay':
                $data = DB::table('orders')
                    ->select(
                        'orders.id as order_id',
                        'buyers.name as buyer_name',
                        'farmers.name as farmer_name',
                        'orders.total_amount as cod_amount',
                        'orders.created_at',
                        DB::raw("DATE_PART('day', NOW() - orders.created_at) as days_delayed"),
                        DB::raw("CASE
                            WHEN orders.order_status = 'confirmed' AND payments.id IS NULL THEN 'No Payment Recorded'
                            WHEN DATE_PART('day', payments.payment_date - orders.created_at) > 7 THEN 'Delayed Payment'
                            ELSE 'On Time'
                        END as delay_status")
                    )
                    ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
                    ->leftJoin('farmers', 'orders.farmer_id', '=', 'farmers.id')
                    ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
                    ->where('orders.order_status', 'confirmed')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->orderBy('days_delayed', 'desc')
                    ->get();
                break;

            case 'cod-exception':
                $data = DB::table('orders')
                    ->select(
                        'orders.id as order_id',
                        'buyers.name as buyer_name',
                        'farmers.name as farmer_name',
                        'orders.total_amount as order_amount',
                        'payments.amount as recorded_cash',
                        DB::raw("orders.total_amount - payments.amount as difference"),
                        'payments.payment_date as collection_date'
                    )
                    ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
                    ->leftJoin('farmers', 'orders.farmer_id', '=', 'farmers.id')
                    ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
                    ->where('payments.payment_method', 'COD')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->having('difference', '!=', 0)
                    ->orderBy('difference', 'desc')
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
                $data = DB::table('product_categories')
                    ->select(
                        'product_categories.category_name',
                        DB::raw("COUNT(DISTINCT products.id) as total_products"),
                        DB::raw("COALESCE(SUM(order_items.quantity_ordered), 0) as total_sold"),
                        DB::raw("COALESCE(SUM(orders.total_amount), 0) as revenue")
                    )
                    ->leftJoin('products', 'product_categories.id', '=', 'products.category_id')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy('product_categories.id', 'product_categories.category_name')
                    ->orderBy('revenue', 'desc')
                    ->get();
                break;

            case 'stock-movement':
                $data = DB::table('products')
                    ->select(
                        'products.product_name',
                        DB::raw("products.quantity as ending_quantity"),
                        DB::raw("COALESCE(SUM(order_items.quantity_ordered), 0) as sales"),
                        DB::raw("MAX(orders.created_at) as movement_date")
                    )
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy('products.id', 'products.product_name', 'products.quantity')
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
                        DB::raw("COALESCE(SUM(orders.total_amount), 0) as total_revenue")
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
                        DB::raw("CASE
                            WHEN farmers.nic_no IS NOT NULL AND farmers.primary_mobile IS NOT NULL AND farmers.residential_address IS NOT NULL THEN 'Complete'
                            ELSE 'Incomplete'
                        END as profile_status"),
                        DB::raw("COUNT(products.id) as product_listings"),
                        'farmers.is_active'
                    )
                    ->leftJoin('products', 'farmers.id', '=', 'products.farmer_id')
                    ->whereBetween('farmers.created_at', [$fromDate, $toDate])
                    ->groupBy('farmers.id', 'farmers.name', 'farmers.created_at', 'farmers.nic_no', 'farmers.primary_mobile', 'farmers.residential_address', 'farmers.is_active')
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
                        'is_active',
                        DB::raw("(SELECT COUNT(*) FROM sessions WHERE sessions.user_id = users.id) as login_count")
                    )
                    ->whereBetween('created_at', [$fromDate, $toDate])
                    ->orderBy('last_login', 'desc')
                    ->get();
                break;

            case 'data-quality':
                $data = [
                    'users' => DB::table('users')
                        ->select(
                            DB::raw("COUNT(*) as total"),
                            DB::raw("SUM(CASE WHEN email IS NULL THEN 1 ELSE 0 END) as missing_email"),
                            DB::raw("SUM(CASE WHEN last_login IS NULL THEN 1 ELSE 0 END) as never_logged_in")
                        )->first(),
                    'farmers' => DB::table('farmers')
                        ->select(
                            DB::raw("COUNT(*) as total"),
                            DB::raw("SUM(CASE WHEN nic_no IS NULL THEN 1 ELSE 0 END) as missing_nic"),
                            DB::raw("SUM(CASE WHEN address_map_link IS NULL THEN 1 ELSE 0 END) as missing_map_links"),
                            DB::raw("SUM(CASE WHEN payment_details IS NULL THEN 1 ELSE 0 END) as missing_payment_details")
                        )->first(),
                    'products' => DB::table('products')
                        ->select(
                            DB::raw("COUNT(*) as total"),
                            DB::raw("SUM(CASE WHEN product_photo IS NULL THEN 1 ELSE 0 END) as missing_photos"),
                            DB::raw("SUM(CASE WHEN pickup_map_link IS NULL THEN 1 ELSE 0 END) as missing_pickup_maps")
                        )->first()
                ];
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

            case 'regional-cod':
                $data = DB::table('farmers')
                    ->select(
                        'farmers.district',
                        DB::raw("COUNT(DISTINCT farmers.id) as total_farmers"),
                        DB::raw("COUNT(DISTINCT products.id) as total_products"),
                        DB::raw("COALESCE(SUM(orders.total_amount), 0) as total_sales"),
                        DB::raw("AVG(orders.total_amount) as avg_order_value")
                    )
                    ->leftJoin('products', 'farmers.id', '=', 'products.farmer_id')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy('farmers.district')
                    ->orderBy('total_sales', 'desc')
                    ->get();
                break;

            case 'quality-grade':
                $data = DB::table('products')
                    ->select(
                        'products.quality_grade',
                        DB::raw("COUNT(products.id) as total_products"),
                        DB::raw("COALESCE(SUM(order_items.quantity_ordered), 0) as total_sold"),
                        DB::raw("AVG(products.selling_price) as avg_price"),
                        DB::raw("ROUND(COALESCE(SUM(order_items.quantity_ordered), 0) * 100.0 / COUNT(products.id), 2) as sell_through_rate")
                    )
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->whereBetween('products.created_at', [$fromDate, $toDate])
                    ->groupBy('products.quality_grade')
                    ->orderBy('total_sold', 'desc')
                    ->get();
                break;

            case 'order-fulfillment':
                $data = DB::table('orders')
                    ->select(
                        'orders.id as order_id',
                        'orders.order_date',
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

            case 'financial-audit':
                $data = DB::table('payments')
                    ->select(
                        'payments.id as transaction_id',
                        'orders.order_number',
                        'payments.amount',
                        'payments.payment_method',
                        'payments.payment_status',
                        'payments.payment_date',
                        'users.username as verified_by'
                    )
                    ->leftJoin('orders', 'payments.order_id', '=', 'orders.id')
                    ->leftJoin('users', 'payments.id', '=', 'users.id')
                    ->whereBetween('payments.payment_date', [$fromDate, $toDate])
                    ->orderBy('payments.payment_date', 'desc')
                    ->get();
                break;

            case 'inventory-cash-reconciliation':
                $data = DB::table('products')
                    ->select(
                        'products.product_name',
                        DB::raw("COALESCE(SUM(order_items.quantity_ordered), 0) as quantity_sold"),
                        DB::raw("COALESCE(SUM(order_items.quantity_ordered * order_items.unit_price_snapshot), 0) as cash_expected"),
                        DB::raw("COALESCE(SUM(payments.amount), 0) as cash_received"),
                        DB::raw("COALESCE(SUM(order_items.quantity_ordered * order_items.unit_price_snapshot), 0) - COALESCE(SUM(payments.amount), 0) as variance")
                    )
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
                    ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy('products.id', 'products.product_name')
                    ->having(DB::raw("COALESCE(SUM(order_items.quantity_ordered * order_items.unit_price_snapshot), 0) - COALESCE(SUM(payments.amount), 0)"), '!=', 0)
                    ->orderBy('variance', 'desc')
                    ->get();
                break;

            case 'farmer-payment-delay':
                $data = DB::table('farmers')
                    ->select(
                        'farmers.name',
                        DB::raw("COALESCE(SUM(orders.total_amount), 0) as total_sales"),
                        DB::raw("AVG(DATE_PART('day', payments.payment_date - orders.created_at)) as avg_payment_delay"),
                        DB::raw("MAX(payments.payment_date) as last_payment_date"),
                        DB::raw("SUM(CASE WHEN orders.order_status = 'confirmed' AND payments.id IS NULL THEN orders.total_amount ELSE 0 END) as outstanding_amount")
                    )
                    ->leftJoin('orders', 'farmers.id', '=', 'orders.farmer_id')
                    ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy('farmers.id', 'farmers.name')
                    ->having(DB::raw("AVG(DATE_PART('day', payments.payment_date - orders.created_at))"), '>', 7)
                    ->orHaving(DB::raw("SUM(CASE WHEN orders.order_status = 'confirmed' AND payments.id IS NULL THEN orders.total_amount ELSE 0 END)"), '>', 0)
                    ->orderBy('avg_payment_delay', 'desc')
                    ->get();
                break;

            case 'geographic-sales':
                $data = DB::table('farmers')
                    ->select(
                        'farmers.district as region',
                        DB::raw("COUNT(DISTINCT farmers.id) as active_farmers"),
                        DB::raw("COUNT(DISTINCT buyers.id) as active_buyers"),
                        DB::raw("COALESCE(SUM(orders.total_amount), 0) as total_sales"),
                        DB::raw("COUNT(DISTINCT orders.id) as number_of_orders")
                    )
                    ->leftJoin('orders', 'farmers.id', '=', 'orders.farmer_id')
                    ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy('farmers.district')
                    ->orderBy('total_sales', 'desc')
                    ->get();
                break;

            case 'buyer-payment-behavior':
                $data = DB::table('buyers')
                    ->select(
                        'buyers.name',
                        DB::raw("COUNT(orders.id) as total_orders"),
                        DB::raw("COUNT(CASE WHEN payments.payment_method = 'COD' THEN orders.id END) as cod_orders"),
                        DB::raw("ROUND(COUNT(CASE WHEN orders.order_status = 'completed' AND payments.payment_method = 'COD' THEN orders.id END) * 100.0 / NULLIF(COUNT(CASE WHEN payments.payment_method = 'COD' THEN orders.id END), 0), 2) as cod_completion_rate"),
                        DB::raw("AVG(DATE_PART('day', payments.payment_date - orders.created_at)) as avg_payment_time")
                    )
                    ->leftJoin('orders', 'buyers.id', '=', 'orders.buyer_id')
                    ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->groupBy('buyers.id', 'buyers.name')
                    ->orderBy('total_orders', 'desc')
                    ->get();
                break;

            case 'product-taxonomy':
                $data = DB::table('product_categories')
                    ->select(
                        'product_categories.category_name',
                        'product_categories.is_active as category_active',
                        'product_subcategories.subcategory_name',
                        'product_subcategories.is_active as subcategory_active',
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

            case 'cod-payment':
                $data = DB::table('orders')
                    ->select(
                        'orders.id as order_id',
                        'buyers.name as buyer_name',
                        'farmers.name as farmer_name',
                        'orders.total_amount as order_amount',
                        'payments.amount as recorded_payment',
                        DB::raw("orders.total_amount - payments.amount as variance"),
                        'payments.payment_date'
                    )
                    ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
                    ->leftJoin('farmers', 'orders.farmer_id', '=', 'farmers.id')
                    ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
                    ->where('payments.payment_method', 'COD')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->orderBy('variance', 'desc')
                    ->get();
                break;

            case 'cod-revenue':
                $data = DB::table('orders')
                    ->select(
                        DB::raw("COUNT(orders.id) as pending_orders"),
                        DB::raw("SUM(orders.total_amount) as expected_cash"),
                        DB::raw("AVG(orders.total_amount) as avg_order_value")
                    )
                    ->where('orders.order_status', 'confirmed')
                    ->whereBetween('orders.created_at', [$fromDate, $toDate])
                    ->first();
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
            'pending-pickup' => 'Pending Pickup/Delivery Report',
            'sales-volume' => 'Sales Volume & Value Report',
            'sales-payment' => 'Sales & Payment Reconciliation Report',
            'system-financial' => 'System Financial Summary',
            'daily-cash' => 'Daily Cash Position Report',
            'cash-collection-delay' => 'Cash Collection Delay Report',
            'cod-exception' => 'COD Exception Report',
            'inventory-stock' => 'Current Inventory / Stock Report',
            'category-performance' => 'Product Category Performance Report',
            'stock-movement' => 'Stock Movement Report',
            'group-performance' => 'Group Farmer Performance Report',
            'farmer-registration' => 'Farmer Registration Status Report',
            'system-adoption' => 'System Adoption & User Count Report',
            'user-access' => 'User Access & Role Management Report',
            'data-quality' => 'Data Quality Report',
            'dispute-feedback' => 'Dispute & Feedback Log Report',
            'regional-cod' => 'Regional Performance Report',
            'quality-grade' => 'Quality Grade Performance Report',
            'order-fulfillment' => 'Order Fulfillment Timeline Report',
            'financial-audit' => 'Financial Audit & Transaction Report',
            'inventory-cash-reconciliation' => 'Inventory vs Cash Reconciliation Report',
            'farmer-payment-delay' => 'Farmer Payment Delay Risk Report',
            'geographic-sales' => 'Geographic Sales Density Report',
            'buyer-payment-behavior' => 'Buyer Payment Behavior Report',
            'product-taxonomy' => 'Product Taxonomy Report',
            'cod-payment' => 'COD Payment Reconciliation Report',
            'cod-revenue' => 'COD Revenue Forecast Report',
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
            'pending-pickup' => 'Pending Pickup/Delivery Report',
            'sales-volume' => 'Sales Volume & Value Report',
            'sales-payment' => 'Sales & Payment Reconciliation Report',
            'system-financial' => 'System Financial Summary',
            'daily-cash' => 'Daily Cash Position Report',
            'cash-collection-delay' => 'Cash Collection Delay Report',
            'cod-exception' => 'COD Exception Report',
            'inventory-stock' => 'Current Inventory / Stock Report',
            'category-performance' => 'Product Category Performance Report',
            'stock-movement' => 'Stock Movement Report',
            'group-performance' => 'Group Farmer Performance Report',
            'farmer-registration' => 'Farmer Registration Status Report',
            'system-adoption' => 'System Adoption & User Count Report',
            'user-access' => 'User Access & Role Management Report',
            'data-quality' => 'Data Quality Report',
            'dispute-feedback' => 'Dispute & Feedback Log Report',
            'regional-cod' => 'Regional Performance Report',
            'quality-grade' => 'Quality Grade Performance Report',
            'order-fulfillment' => 'Order Fulfillment Timeline Report',
            'financial-audit' => 'Financial Audit & Transaction Report',
            'inventory-cash-reconciliation' => 'Inventory vs Cash Reconciliation Report',
            'farmer-payment-delay' => 'Farmer Payment Delay Risk Report',
            'geographic-sales' => 'Geographic Sales Density Report',
            'buyer-payment-behavior' => 'Buyer Payment Behavior Report',
            'product-taxonomy' => 'Product Taxonomy Report',
            'cod-payment' => 'COD Payment Reconciliation Report',
            'cod-revenue' => 'COD Revenue Forecast Report',
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

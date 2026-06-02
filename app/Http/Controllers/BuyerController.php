<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Buyer;
use App\Mail\BuyerRegistrationMail;
use App\Mail\OrderNotificationMail;
use Illuminate\Support\Facades\Log;
use App\Services\InventoryService;
use App\Models\Product;
use App\Models\Order;

class BuyerController extends Controller
{
    private function getBuyer()
    {
        $user = Auth::user();
        $buyer = DB::table('buyers')->where('user_id', $user->id)->first();
        if (!$buyer) {
            $buyerId = DB::table('buyers')->insertGetId([
                'user_id' => $user->id,
                'name' => $user->username,
                'primary_mobile' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $buyer = (object)['id' => $buyerId];
        }
        return $buyer;
    }

    private function getProductImagePath($productImage)
    {
        if (!$productImage) {
            return asset('assets/images/product-placeholder.png');
        }
        $possiblePaths = [
            public_path('uploads/product_images/' . $productImage),
            public_path('assets/images/products/' . $productImage),
            public_path('storage/products/' . $productImage),
        ];
        foreach ($possiblePaths as $imagePath) {
            if (File::exists($imagePath)) {
                return asset(str_replace(public_path(), '', $imagePath));
            }
        }
        return asset('assets/images/product-placeholder.png');
    }

    private function getCommonData()
    {
        return [
            'categories' => DB::table('product_categories')
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get(),
            'allSubcategories' => DB::table('product_subcategories')
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get(['id', 'subcategory_name', 'category_id']),
            'districts' => DB::table('farmers')
                ->select('district')
                ->distinct()
                ->orderBy('district')
                ->get(),
            'grades' => DB::table('system_standards')
                ->where('standard_type', 'quality_grade')
                ->where('is_active', true)
                ->get(),
        ];
    }

    private function buildProductQuery()
    {
        return DB::table('products')
            ->select(
                'products.*',
                'farmers.name as farmer_name',
                'farmers.preferred_payment',
                'farmers.district',
                'farmers.grama_niladhari_division',
                'product_categories.category_name',
                'product_subcategories.subcategory_name',
                'lead_farmers.name as lead_farmer_name'
            )
            ->leftJoin('farmers', 'products.farmer_id', '=', 'farmers.id')
            ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->leftJoin('product_subcategories', 'products.subcategory_id', '=', 'product_subcategories.id')
            ->leftJoin('lead_farmers', 'products.lead_farmer_id', '=', 'lead_farmers.id')
            ->where('products.is_available', true)
            ->where('products.quantity', '>', 0);
    }

    private function applyFilters($query, $request)
    {
        // Search with case-insensitive search across multiple fields
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // For PostgreSQL, use ILIKE; for MySQL, use LIKE with LOWER()
                if (DB::connection()->getDriverName() === 'pgsql') {
                    $q->where('products.product_name', 'ILIKE', "%{$search}%")
                    ->orWhere('products.product_description', 'ILIKE', "%{$search}%")
                    ->orWhere('farmers.name', 'ILIKE', "%{$search}%")
                    ->orWhere('product_categories.category_name', 'ILIKE', "%{$search}%")
                    ->orWhere('product_subcategories.subcategory_name', 'ILIKE', "%{$search}%")
                    ->orWhere('lead_farmers.name', 'ILIKE', "%{$search}%");
                } else {
                    // For MySQL/MariaDB - case-insensitive search
                    $q->whereRaw('LOWER(products.product_name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(products.product_description) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(farmers.name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(product_categories.category_name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(product_subcategories.subcategory_name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(lead_farmers.name) LIKE ?', ['%' . strtolower($search) . '%']);
                }
            });
        }
        
        // Category filter
        if ($request->has('category') && $request->category != 'all' && !empty($request->category)) {
            $query->where('product_categories.category_name', 'LIKE', "%{$request->category}%");
        }
        
        // Subcategory filter
        if ($request->has('subcategory') && !empty($request->subcategory)) {
            $query->where('product_subcategories.subcategory_name', 'LIKE', "%{$request->subcategory}%");
        }
        
        // District filter
        if ($request->has('district') && !empty($request->district)) {
            $query->where('farmers.district', $request->district);
        }
        
        // Grade filter
        if ($request->has('grade') && !empty($request->grade)) {
            $query->where('products.quality_grade', $request->grade);
        }
        
        // Price filters
        if ($request->has('min_price') && is_numeric($request->min_price) && $request->min_price > 0) {
            $query->where('products.selling_price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && is_numeric($request->max_price) && $request->max_price > 0) {
            $query->where('products.selling_price', '<=', $request->max_price);
        }
        
        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('products.selling_price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('products.selling_price', 'desc');
                    break;
                case 'name':
                    $query->orderBy('products.product_name', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('products.created_at', 'desc');
            }
        } else {
            $query->orderBy('products.created_at', 'desc');
        }
        return $query;
    }

    private function getFilteredSubcategories($categoryName)
    {
        if (!$categoryName || $categoryName == 'all') {
            return [];
        }
        $category = DB::table('product_categories')
            ->where('category_name', 'LIKE', "%{$categoryName}%")
            ->first();
        if (!$category) {
            return [];
        }
        return DB::table('product_subcategories')
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
    }

    private function sendSMS($phone, $message)
    {
        try {
            $user = env('SMS_USER', 'number');
            $password = env('SMS_PASSWORD', '0000');
            $text = urlencode($message);
            $to = $phone;
            $baseurl = env('SMS_API_URL', 'https://textit.biz/sendmsg');
            $url = "$baseurl/?id=$user&pw=$password&to=$to&text=$text";
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 30,
            ]);
            $ret = curl_exec($ch);
            curl_close($ch);
            $res = explode(":", $ret);
            return trim($res[0]) == "OK";
        } catch (\Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }

    public function dashboard(Request $request)
    {
        $buyer = $this->getBuyer();
        $ordersCount = DB::table('orders')->where('buyer_id', $buyer->id)->count();
        $wishlistCount = DB::table('wishlists')->where('buyer_id', $buyer->id)->count();
        $cartCount = DB::table('shopping_cart')
            ->where('buyer_id', $buyer->id)
            ->count();
        session(['cart_count' => $cartCount]);
        $commonData = $this->getCommonData();
        $query = $this->buildProductQuery();
        $query = $this->applyFilters($query, $request);
        $subcategories = $this->getFilteredSubcategories($request->category);

        // Determine the limit based on screen width (from cookie or request input)
        $screenWidth = (int) (request()->cookie('screen_width') ?? request()->input('screen_width', 1200));

        $limit = match(true) {
            // ultralarge Ultrawide / XXXXL Screens: 2560px - 5000px or above
            $screenWidth >= 2560 => 20,
            
            // large Ultrawide / XXXL Screens: 1501px - 2559px
            $screenWidth >= 1501 && $screenWidth <= 2559 => 15,
            
            // Ultrawide / XXL Screens: 1400px - 1500px
            $screenWidth >= 1400 && $screenWidth <= 1500 => 15,
            
            // Extra Large Screens, Large Screens and Normal Tablets: 768px - 1399px
            $screenWidth >= 768 && $screenWidth <= 1399 => 12,
            
            // Small Screens: 576px - 767px
            $screenWidth >= 576 && $screenWidth <= 767 => 9,
            
            // Extra Small to ultra Small: 575px and below
            $screenWidth <= 575 => 6,
            
            // Default fallback
            default => 12
        };

        $recommended = $query->limit($limit)->get();
        
        return view('buyer.dashboard', array_merge([
            'orders_count' => $ordersCount,
            'wishlist_count' => $wishlistCount,
            'recommended' => $recommended,
            'subcategories' => $subcategories,
        ], $commonData));
    }

    public function browseProducts(Request $request)
    {
        $commonData = $this->getCommonData();
        $query = $this->buildProductQuery();
        $query = $this->applyFilters($query, $request);
        $products = $query->paginate(12)->withQueryString();
        $subcategories = $this->getFilteredSubcategories($request->category);
        if ($request->ajax()) {
            return response()->json([
                'products_html' => view('buyer.partials.products_grid', compact('products'))->render(),
                'count' => $products->total()
            ]);
        }
        return view('buyer.browse_products', compact('products', 'subcategories') + $commonData);
    }

    public function getSubcategories(Request $request)
    {
        $request->validate(['category_id' => 'required|integer']);
        $subcategories = DB::table('product_subcategories')
            ->where('category_id', $request->category_id)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get(['id', 'subcategory_name']);
        return response()->json($subcategories);
    }

    public function productDetail($id)
    {
        $product = DB::table('products')
            ->select(
                'products.*',
                'farmers.name as farmer_name',
                'farmers.primary_mobile as farmer_mobile',
                'farmers.district',
                'farmers.grama_niladhari_division',
                'farmers.address_map_link',
                'farmers.preferred_payment',
                'product_categories.category_name',
                'product_subcategories.subcategory_name',
                'lead_farmers.name as lead_farmer_name'
            )
            ->leftJoin('farmers', 'products.farmer_id', '=', 'farmers.id')
            ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->leftJoin('product_subcategories', 'products.subcategory_id', '=', 'product_subcategories.id')
            ->leftJoin('lead_farmers', 'products.lead_farmer_id', '=', 'lead_farmers.id')
            ->where('products.id', $id)
            ->where('products.is_available', true)
            ->first();
        if (!$product) {
            return redirect()->route('buyer.browseProducts')->with('error', 'Product not found.');
        }
        $productImage = $this->getProductImagePath($product->product_photo);
        $relatedProducts = DB::table('products')
            ->select(
                'products.id',
                'products.product_name',
                'products.product_photo',
                'products.selling_price',
                'products.quantity',
                'products.unit_of_measure',
                'products.quality_grade'
            )
            ->where('farmer_id', $product->farmer_id)
            ->where('id', '!=', $id)
            ->where('is_available', true)
            ->where('quantity', '>', 0)
            ->limit(4)
            ->get()
            ->map(function ($relatedProduct) {
                $relatedProduct->product_image = $this->getProductImagePath($relatedProduct->product_photo);
                return $relatedProduct;
            });
        $buyer = $this->getBuyer();
        $isInWishlist = DB::table('wishlists')
            ->where('buyer_id', $buyer->id)
            ->where('product_id', $id)
            ->exists();
        return view('buyer.product_detail', [
            'product' => $product,
            'productImage' => $productImage,
            'relatedProducts' => $relatedProducts,
            'isInWishlist' => $isInWishlist
        ]);
    }

    public function cart()
    {
        $buyer = $this->getBuyer();
        $cartItems = DB::table('shopping_cart')
            ->select(
                'shopping_cart.id as cart_id',
                'shopping_cart.product_id',
                'shopping_cart.quantity',
                'shopping_cart.selling_price_snapshot',
                'products.product_name',
                'products.product_photo',
                'products.selling_price as current_price',
                'products.quantity as available_stock'
            )
            ->join('products', 'shopping_cart.product_id', '=', 'products.id')
            ->where('shopping_cart.buyer_id', $buyer->id)
            ->get();
        $processedItems = [];
        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $imagePath = 'uploads/product_images/' . $item->product_photo;
            $fullPath = public_path($imagePath);
            $productImage = file_exists($fullPath) ? asset($imagePath) : asset('assets/images/product-placeholder.png');
            $itemTotal = $item->quantity * $item->selling_price_snapshot;
            $cartTotal += $itemTotal;
            $processedItems[] = (object) [
                'cart_id' => $item->cart_id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_image' => $productImage,
                'quantity' => $item->quantity,
                'selling_price_snapshot' => $item->selling_price_snapshot,
                'current_price' => $item->current_price,
                'available_stock' => $item->available_stock,
                'item_total' => $itemTotal
            ];
        }
        $cartCount = DB::table('shopping_cart')
            ->where('buyer_id', $buyer->id)
            ->count();
        return view('buyer.cart', [
            'cartItems' => $processedItems,
            'cartTotal' => $cartTotal,
            'cartCount' => $cartCount,
        ]);
    }

    public function wishlist()
    {
        $buyer = $this->getBuyer();
        $wishlistItems = DB::table('wishlists')
            ->select(
                'wishlists.id as wishlist_id',
                'wishlists.created_at as wishlist_created_at',
                'wishlists.updated_at as wishlist_updated_at',
                'products.id',
                'products.product_name',
                'products.product_description',
                'products.product_photo',
                'products.selling_price',
                'products.quantity',
                'products.unit_of_measure',
                'products.quality_grade',
                'products.is_available',
                'products.farmer_id',
                'farmers.name as farmer_name',
                'farmers.district',
                'farmers.grama_niladhari_division',
                'farmers.address_map_link',
                'product_categories.category_name',
                'product_subcategories.subcategory_name',
                'lead_farmers.name as lead_farmer_name'
            )
            ->join('products', 'wishlists.product_id', '=', 'products.id')
            ->leftJoin('farmers', 'products.farmer_id', '=', 'farmers.id')
            ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->leftJoin('product_subcategories', 'products.subcategory_id', '=', 'product_subcategories.id')
            ->leftJoin('lead_farmers', 'products.lead_farmer_id', '=', 'lead_farmers.id')
            ->where('wishlists.buyer_id', $buyer->id)
            ->get()
            ->map(function ($item) {
                $item->product_image = $this->getProductImagePath($item->product_photo);
                return $item;
            });
        return view('buyer.wishlist', ['wishlistItems' => $wishlistItems]);
    }

    public function history()
    {
        $buyer = $this->getBuyer();
        $orders = DB::table('orders')
            ->select(
                'orders.*',
                'farmers.name as farmer_name',
                'lead_farmers.name as lead_farmer_name',
                'lead_farmers.primary_mobile as lead_farmer_contact'
            )
            ->join('farmers', 'orders.farmer_id', '=', 'farmers.id')
            ->join('lead_farmers', 'orders.lead_farmer_id', '=', 'lead_farmers.id')
            ->where('orders.buyer_id', $buyer->id)
            ->orderBy('orders.created_at', 'desc')
            ->get();
        return view('buyer.history', ['orders' => $orders]);
    }

    public function getInvoiceData($orderId)
    {
        $buyer = $this->getBuyer();

        $order = DB::table('orders')
            ->select(
                'orders.*',
                'payments.payment_reference',
                'payments.transaction_id',
                'payments.payment_date',
                'payments.payment_status',
                'invoices.invoice_number',
                'invoices.invoice_path',
                'farmers.name as farmer_name',
                'farmers.primary_mobile as farmer_contact',
                'farmers.residential_address as farmer_address',
                'farmers.district as farmer_district',
                'farmers.grama_niladhari_division',
                'lead_farmers.name as lead_farmer_name',
                'lead_farmers.primary_mobile as lead_farmer_contact',
                'lead_farmers.grama_niladhari_division as lead_farmer_GNdivision',
                'products.pickup_address',
                'products.pickup_map_link'
            )
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->leftJoin('invoices', 'orders.id', '=', 'invoices.order_id')
            ->leftJoin('farmers', 'orders.farmer_id', '=', 'farmers.id')
            ->leftJoin('lead_farmers', 'orders.lead_farmer_id', '=', 'lead_farmers.id')
            // Join with order_items and products to get pickup information
            ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.id', $orderId)
            ->where('orders.buyer_id', $buyer->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        $orderItems = DB::table('order_items')
            ->select(
                'order_items.*',
                'products.product_name',
                'products.unit_of_measure',
                'products.pickup_address',
                'products.pickup_map_link'
            )
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', $orderId)
            ->get();

        // Collect unique pickup addresses from products
        $pickupAddresses = [];
        $pickupMapLinks = [];

        foreach ($orderItems as $item) {
            if ($item->pickup_address && !in_array($item->pickup_address, $pickupAddresses)) {
                $pickupAddresses[] = $item->pickup_address;
            }
            if ($item->pickup_map_link && !in_array($item->pickup_map_link, $pickupMapLinks)) {
                $pickupMapLinks[] = $item->pickup_map_link;
            }
        }

        // Use the order's pickup address if available, otherwise use from products
        $productsPickupAddress = $order->pickup_address;
        if (!$productsPickupAddress && !empty($pickupAddresses)) {
            $productsPickupAddress = implode(' | ', $pickupAddresses);
        }

        $productsPickupMapLink = $order->pickup_map_link;
        if (!$productsPickupMapLink && !empty($pickupMapLinks)) {
            $productsPickupMapLink = implode(' | ', $pickupMapLinks);
        }

        $formattedItems = [];
        $subtotal = 0;

        foreach ($orderItems as $item) {
            $itemTotal = $item->item_total;
            $subtotal += $itemTotal;

            $formattedItems[] = [
                'product_name' => $item->product_name_snapshot ?: $item->product_name,
                'quantity' => number_format($item->quantity_ordered, 2),
                'unit_price' => number_format($item->unit_price_snapshot, 2),
                'total' => number_format($itemTotal, 2),
                'unit_of_measure' => $item->unit_of_measure,
                'pickup_address' => $item->pickup_address,
                'pickup_map_link' => $item->pickup_map_link
            ];
        }

        $grandTotal = $subtotal;
        $buyerAddress = $buyer->residential_address ?: 'Address not provided';

        $paymentStatus = $order->payment_status ?? 'pending';
        if ($paymentStatus === 'completed') {
            $paymentStatus = 'Paid';
        } elseif ($paymentStatus === 'pending') {
            $paymentStatus = 'Pending';
        } else {
            $paymentStatus = ucfirst($paymentStatus);
        }

        $orderStatus = ucfirst(str_replace('_', ' ', $order->order_status));
        $orderType = $order->order_type ?? 'Pickup';

        $paidDate = $order->payment_date ? date('M d, Y', strtotime($order->payment_date)) : null;
        $paymentMethod = $order->payment_status === 'completed' ? 'Credit Card' : 'Cash on Pickup';
        $transactionId = $order->transaction_id;

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'order_type' => $orderType,
            'order_date' => date('M d, Y', strtotime($order->created_at)),
            'order_status' => $orderStatus,
            'payment_status' => $paymentStatus,
            'invoice_number' => $order->invoice_number ?: 'INV-' . $order->order_number,
            'buyer_name' => $buyer->name,
            'buyer_contact' => $buyer->primary_mobile,
            'buyer_address' => $buyerAddress,
            'farmer_name' => $order->farmer_name,
            'farmer_contact' => $order->farmer_contact,
            'farmer_address' => $order->farmer_address . ', ' . $order->farmer_district . ' - ' . $order->grama_niladhari_division,
            'lead_farmer_name' => $order->lead_farmer_name,
            'lead_farmer_contact' => $order->lead_farmer_contact,
            'lead_farmer_GNdivision'=> $order->lead_farmer_GNdivision,
            'products_pickup_address' => $productsPickupAddress ?: 'Pickup address not specified',
            'products_pickup_map_link' => $productsPickupMapLink,
            'items' => $formattedItems,
            'subtotal' => number_format($subtotal, 2),
            'total_amount' => number_format($order->total_amount, 2),
            'paid_date' => $paidDate,
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId
        ]);
    }

    public function submitFeedback(Request $request, $orderId)
    {
        $buyer = $this->getBuyer();
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);
        $order = DB::table('orders')
            ->where('id', $orderId)
            ->where('buyer_id', $buyer->id)
            ->where('order_status', 'completed')
            ->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or not eligible for feedback.'
            ], 404);
        }
        $existingFeedback = DB::table('product_feedback')
            ->where('buyer_id', $buyer->id)
            ->where('order_id', $orderId)
            ->first();
        if ($existingFeedback) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback already submitted for this order.'
            ], 400);
        }
        DB::table('product_feedback')->insert([
            'buyer_id' => $buyer->id,
            'order_id' => $orderId,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!'
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $buyer = DB::table('buyers')->where('user_id', $user->id)->first();
        return view('buyer.profile.profile', [
            'user' => $user,
            'buyer' => $buyer,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $buyer = DB::table('buyers')->where('user_id', $user->id)->first();
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nic_no' => 'nullable|string|max:20|unique:buyers,nic_no,' . ($buyer ? $buyer->id : 'NULL') . ',id',
            'primary_mobile' => 'required|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'residential_address' => 'nullable|string',
            'google_map_link' => 'nullable|url',
        ]);
        User::where('id', $user->id)->update([
            'email' => $validated['email'],
            'updated_at' => now(),
        ]);
        if ($buyer) {
            DB::table('buyers')
                ->where('id', $buyer->id)
                ->update([
                    'name' => $validated['name'],
                    'nic_no' => $validated['nic_no'],
                    'primary_mobile' => $validated['primary_mobile'],
                    'whatsapp_number' => $validated['whatsapp_number'],
                    'residential_address' => $validated['residential_address'],
                    'google_map_link' => $validated['google_map_link'],
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('buyers')->insert([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'nic_no' => $validated['nic_no'],
                'primary_mobile' => $validated['primary_mobile'],
                'whatsapp_number' => $validated['whatsapp_number'],
                'residential_address' => $validated['residential_address'],
                'google_map_link' => $validated['google_map_link'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $user->refresh();
        return redirect()->route('buyer.profile.profile')->with('success', 'Profile updated successfully.');
    }

    public function addToCart(Request $request, $productId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to cart.'
            ], 401);
        }
        $buyer = $this->getBuyer();
        $validated = $request->validate(['quantity' => 'required|numeric|min:0.01']);
        $product = DB::table('products')->find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }
        if (!$product->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not available.'
            ], 400);
        }
        if ($product->quantity <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Product is out of stock.'
            ], 400);
        }
        $quantity = floatval($validated['quantity']);
        if ($quantity > $product->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Requested quantity exceeds available stock. Only ' . number_format($product->quantity, 2) . ' ' . $product->unit_of_measure . ' available.'
            ], 400);
        }
        $existingCartItem = DB::table('shopping_cart')
            ->where('buyer_id', $buyer->id)
            ->where('product_id', $productId)
            ->first();
        if ($existingCartItem) {
            $newQuantity = $existingCartItem->quantity + $quantity;
            if ($newQuantity > $product->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total quantity in cart would exceed available stock.'
                ], 400);
            }
            DB::table('shopping_cart')
                ->where('id', $existingCartItem->id)
                ->update([
                    'quantity' => $newQuantity,
                    'updated_at' => now(),
                ]);
            $message = 'Cart quantity updated successfully!';
        } else {
            DB::table('shopping_cart')->insert([
                'buyer_id' => $buyer->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'selling_price_snapshot' => $product->selling_price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $message = 'Product added to cart successfully!';
        }
        $cartCount = DB::table('shopping_cart')
            ->where('buyer_id', $buyer->id)
            ->count();
        session(['cart_count' => $cartCount]);
        return response()->json([
            'success' => true,
            'message' => $message,
            'cart_count' => $cartCount
        ]);
    }

    public function removeFromCart(Request $request, $cartItemId)
    {
        $buyer = $this->getBuyer();
        $deleted = DB::table('shopping_cart')
            ->where('id', $cartItemId)
            ->where('buyer_id', $buyer->id)
            ->delete();
        if ($deleted) {
            $cartCount = DB::table('shopping_cart')
                ->where('buyer_id', $buyer->id)
                ->count();
            session(['cart_count' => $cartCount]);
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart.',
                'cart_count' => $cartCount
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.'
            ], 404);
        }
    }

    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'nic_no' => 'required|string|max:20|unique:buyers,nic_no',
            'primary_mobile' => 'required|string|max:15',
            'business_name' => 'nullable|string|max:100',
            'business_type' => 'nullable|string|in:individual,restaurant,hotel,retailer,wholesaler',
            'residential_address' => 'required|string',
            'google_map_link' => 'required|url',
            'district' => 'required|string',
            'whatsapp_number' => 'nullable|string|max:15',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed. Please check the form.'
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'buyer',
                'is_active' => true
            ]);
            $buyer = Buyer::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'nic_no' => $request->nic_no,
                'primary_mobile' => $request->primary_mobile,
                'whatsapp_number' => $request->whatsapp_number,
                'residential_address' => $request->residential_address,
                'google_map_link' => $request->google_map_link,
                'district' => $request->district,
                'business_name' => $request->business_name,
                'business_type' => $request->business_type ?? 'individual',
                'is_verified' => false
            ]);
            $emailData = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'login_url' => route('login')
            ];
            try {
                Mail::to($request->email)->send(new BuyerRegistrationMail($emailData));
                $emailSent = true;
            } catch (\Exception $e) {
                \Log::error('Email sending failed: ' . $e->getMessage());
                $emailSent = false;
            }
                $smsMessage = "Welcome to GreenMarket!
                                Shop for fresh produce directly from farmers today.

                                Your login details are:
                                User: {$request->username}
                                Pass: {$request->password}";
            try {
                $smsSent = $this->sendSMS($request->primary_mobile, $smsMessage);
            } catch (\Exception $e) {
                \Log::error('SMS sending failed: ' . $e->getMessage());
                $smsSent = false;
            }
            DB::commit();
            $message = 'Registration successful!';
            if ($emailSent && $smsSent) {
                $message .= ' Check your email and SMS for login details.';
            } elseif ($emailSent) {
                $message .= ' Check your email for login details.';
            } elseif ($smsSent) {
                $message .= ' Check your SMS for login details.';
            } else {
                $message .= ' However, we were unable to send login details via email/SMS. Please contact support.';
            }
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('login')
                ], 201);
            }
            return redirect()->route('login')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            return back()->with('error', 'Registration failed. Please try again.')->withInput();
        }
    }

    public function showPhotoForm()
    {
        return view('buyer.profile.photo');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        $user = Auth::user();
        $currentUser = User::find($user->id);
        $oldPhoto = $currentUser->profile_photo;
        if ($oldPhoto && $oldPhoto != 'default-avatar.png' && $oldPhoto != 'default-buyer.png') {
            $oldPhotoPath = public_path('uploads/profile_pictures/' . $oldPhoto);
            if (File::exists($oldPhotoPath)) {
                File::delete($oldPhotoPath);
            }
        }
        $file = $request->file('profile_photo');
        $extension = $file->getClientOriginalExtension();
        $filename = 'buyer_' . $user->id . '_' . time() . '.' . $extension;
        $uploadPath = public_path('uploads/profile_pictures');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }
        $file->move($uploadPath, $filename);
        $currentUser->update([
            'profile_photo' => $filename,
            'updated_at' => now(),
        ]);
        $user->refresh();
        return redirect()->route('buyer.profile.photo')
            ->with('success', 'Profile photo updated successfully.');
    }

    public function deletePhoto()
    {
        $user = Auth::user();
        $currentUser = User::find($user->id);
        $photoToDelete = $currentUser->profile_photo;
        if ($photoToDelete && $photoToDelete != 'default-avatar.png' && $photoToDelete != 'default-buyer.png') {
            $photoPath = public_path('uploads/profile_pictures/' . $photoToDelete);
            if (File::exists($photoPath)) {
                File::delete($photoPath);
            }
        }
        $currentUser->update([
            'profile_photo' => 'default-buyer.png',
            'updated_at' => now(),
        ]);
        $user->refresh();
        return redirect()->route('buyer.profile.photo')
            ->with('success', 'Profile photo removed successfully. Default photo restored.');
    }

    public function updateBusiness(Request $request)
    {
        $user = Auth::user();
        $buyer = DB::table('buyers')->where('user_id', $user->id)->first();
        $validated = $request->validate([
            'business_name' => 'nullable|string|max:100',
            'business_type' => 'nullable|string|in:individual,restaurant,hotel,retailer,wholesaler',
            'business_address' => 'nullable|string',
        ]);
        if ($buyer) {
            DB::table('buyers')
                ->where('id', $buyer->id)
                ->update([
                    'business_name' => $validated['business_name'],
                    'business_type' => $validated['business_type'],
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('buyers')->insert([
                'user_id' => $user->id,
                'name' => $user->username,
                'business_name' => $validated['business_name'],
                'business_type' => $validated['business_type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return redirect()->route('buyer.profile.profile')
            ->with('success', 'Business details updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->new_password),
            'updated_at' => now(),
        ]);
        return redirect()->route('buyer.profile.profile')
            ->with('success', 'Password changed successfully.');
    }

    public function generateInvoice($orderId)
    {
        $buyer = $this->getBuyer();
        $order = DB::table('orders')
            ->select(
                'orders.*',
                'farmers.name as farmer_name',
                'farmers.primary_mobile as farmer_mobile',
                'farmers.residential_address as farmer_address',
                'farmers.google_map_link',
                'products.product_name',
                'products.selling_price'
            )
            ->join('farmers', 'orders.farmer_id', '=', 'farmers.id')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->where('orders.id', $orderId)
            ->where('orders.buyer_id', $buyer->id)
            ->first();
        if (!$order) {
            return back()->with('error', 'Order not found.');
        }
        return view('buyer.invoice', [
            'order' => $order,
            'buyer' => $buyer,
        ]);
    }

    public function shoppingCart()
    {
        return $this->cart();
    }

    public function orderHistory()
    {
        return $this->history();
    }

    public function editProfile()
    {
        return $this->profile();
    }

    public function notifications()
    {
        $user = Auth::user();
        $notifications = DB::table('notifications')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $unreadCount = $notifications->where('is_read', false)->count();
        return view('buyer.notifications', [
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function addressBook()
    {
        $buyer = $this->getBuyer();
        $addresses = DB::table('buyer_addresses')
            ->where('buyer_id', $buyer->id)
            ->orderBy('is_default', 'desc')
            ->get();
        return view('buyer.addresses', [
            'addresses' => $addresses,
            'buyer' => $buyer,
        ]);
    }

    public function viewProduct($id)
    {
        return $this->productDetail($id);
    }

    public function product($id)
    {
        return $this->productDetail($id);
    }

    public function addToWishlist(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to wishlist.'
            ], 401);
        }
        $buyer = $this->getBuyer();
        $validated = $request->validate(['product_id' => 'required|integer|exists:products,id']);
        $product = DB::table('products')
            ->where('id', $validated['product_id'])
            ->where('is_available', true)
            ->first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not available.'
            ], 400);
        }
        $existingWishlistItem = DB::table('wishlists')
            ->where('buyer_id', $buyer->id)
            ->where('product_id', $validated['product_id'])
            ->first();
        if ($existingWishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist.'
            ], 400);
        }
        try {
            DB::table('wishlists')->insert([
                'buyer_id' => $buyer->id,
                'product_id' => $validated['product_id'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Added to wishlist successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add to wishlist. Please try again.'
            ], 500);
        }
    }

    public function removeFromWishlist(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to manage wishlist.'
            ], 401);
        }
        $buyer = $this->getBuyer();
        $validated = $request->validate(['product_id' => 'required|integer|exists:products,id']);
        $deleted = DB::table('wishlists')
            ->where('buyer_id', $buyer->id)
            ->where('product_id', $validated['product_id'])
            ->delete();
        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Removed from wishlist successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in wishlist.'
            ], 404);
        }
    }

    public function removeFromWishlistById($wishlistId)
    {
        $buyer = $this->getBuyer();
        $deleted = DB::table('wishlists')
            ->where('id', $wishlistId)
            ->where('buyer_id', $buyer->id)
            ->delete();
        if ($deleted) {
            return back()->with('success', 'Removed from wishlist successfully!');
        } else {
            return back()->with('error', 'Wishlist item not found.');
        }
    }

    public function updateCartQuantity(Request $request, $cartItemId)
    {
        $buyer = $this->getBuyer();
        $validated = $request->validate(['quantity' => 'required|numeric|min:0.01']);
        $cartItem = DB::table('shopping_cart')
            ->where('id', $cartItemId)
            ->where('buyer_id', $buyer->id)
            ->first();
        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.'
            ], 404);
        }
        $product = DB::table('products')->find($cartItem->product_id);
        if (!$product || !$product->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'Product is no longer available.'
            ], 400);
        }
        if ($validated['quantity'] > $product->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Requested quantity exceeds available stock. Only ' . number_format($product->quantity, 2) . ' available.'
            ], 400);
        }
        $newQuantity = floatval($validated['quantity']);
        if ($newQuantity <= 0) {
            DB::table('shopping_cart')->where('id', $cartItemId)->delete();
            $itemTotal = 0;
        } else {
            DB::table('shopping_cart')
                ->where('id', $cartItemId)
                ->update([
                    'quantity' => $newQuantity,
                    'updated_at' => now(),
                ]);
            $itemTotal = $newQuantity * $cartItem->selling_price_snapshot;
        }
        $cartCount = DB::table('shopping_cart')
            ->where('buyer_id', $buyer->id)
            ->count();
        session(['cart_count' => $cartCount]);
        $cartTotal = DB::table('shopping_cart')
            ->where('buyer_id', $buyer->id)
            ->select(DB::raw('COALESCE(SUM(quantity * selling_price_snapshot), 0) as total'))
            ->first();
        return response()->json([
            'success' => true,
            'message' => 'Cart quantity updated successfully!',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal->total,
            'item_total' => $itemTotal
        ]);
    }

    public function checkout(Request $request)
    {
        $buyer = $this->getBuyer();
        $cartItems = DB::table('shopping_cart')
            ->select(
                'shopping_cart.id as cart_id',
                'shopping_cart.product_id',
                'shopping_cart.quantity',
                'shopping_cart.selling_price_snapshot',
                'products.product_name',
                'products.product_photo',
                'products.selling_price as current_price',
                'products.quantity as available_stock',
                'products.farmer_id',
                'products.lead_farmer_id',
                'farmers.name as farmer_name',
                'farmers.primary_mobile as farmer_mobile',
                'farmers.residential_address as farmer_address',
                'farmers.address_map_link as pickup_map',
                'lead_farmers.name as lead_farmer_name',
                'lead_farmers.payment_details',
                'lead_farmers.preferred_payment as lead_farmer_payment_method'
            )
            ->join('products', 'shopping_cart.product_id', '=', 'products.id')
            ->join('farmers', 'products.farmer_id', '=', 'farmers.id')
            ->join('lead_farmers', 'products.lead_farmer_id', '=', 'lead_farmers.id')
            ->where('shopping_cart.buyer_id', $buyer->id)
            ->where('products.is_available', true)
            ->where('products.quantity', '>=', DB::raw('shopping_cart.quantity'))
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart')->with('error', 'Your cart is empty or some items are no longer available.');
        }

        $processedItems = [];
        $orderTotal = 0;
        $groupedByLeadFarmer = [];

        foreach ($cartItems as $item) {
            $imagePath = 'uploads/product_images/' . $item->product_photo;
            $fullPath = public_path($imagePath);
            $productImage = file_exists($fullPath) ? asset($imagePath) : asset('assets/images/product-placeholder.png');

            $itemTotal = $item->quantity * $item->selling_price_snapshot;
            $orderTotal += $itemTotal;

            $leadFarmerId = $item->lead_farmer_id;

            if (!isset($groupedByLeadFarmer[$leadFarmerId])) {
                $groupedByLeadFarmer[$leadFarmerId] = [
                    'lead_farmer_name' => $item->lead_farmer_name,
                    'payment_details' => $item->payment_details,
                    'payment_method' => $item->lead_farmer_payment_method,
                    'items' => [],
                    'subtotal' => 0
                ];
            }

            $processedItem = (object)[
                'cart_id' => $item->cart_id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_image' => $productImage,
                'quantity' => $item->quantity,
                'selling_price_snapshot' => $item->selling_price_snapshot,
                'available_stock' => $item->available_stock,
                'item_total' => $itemTotal,
                'farmer_name' => $item->farmer_name,
                'farmer_mobile' => $item->farmer_mobile,
                'farmer_address' => $item->farmer_address,
                'pickup_map' => $item->pickup_map,
                'lead_farmer_id' => $leadFarmerId
            ];

            $processedItems[] = $processedItem;
            $groupedByLeadFarmer[$leadFarmerId]['items'][] = $processedItem;
            $groupedByLeadFarmer[$leadFarmerId]['subtotal'] += $itemTotal;
        }

        $grandTotal = $orderTotal;
        $defaultAddress = $buyer->residential_address;
        $cartCount = DB::table('shopping_cart')
            ->where('buyer_id', $buyer->id)
            ->count();

        return view('buyer.checkout', [
            'cartItems' => $processedItems,
            'groupedByLeadFarmer' => $groupedByLeadFarmer,
            'orderTotal' => $orderTotal,
            'grandTotal' => $grandTotal,
            'buyer' => $buyer,
            'defaultAddress' => $defaultAddress,
            'cartCount' => $cartCount
        ]);
    }

    public function processPayment(Request $request)
    {
        $buyer = $this->getBuyer();
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'card_number' => 'required|string',
            'card_holder' => 'required|string',
            'expiry_month' => 'required|numeric|min:1|max:12',
            'expiry_year' => 'required|numeric|min:' . date('Y') . '|max:' . (date('Y') + 10),
            'cvv' => 'required|string|min:3|max:4',
            'billing_address' => 'nullable|string',
            'save_card' => 'nullable|boolean'
        ]);

        // Get cart items with explicit field names
        $cartItems = DB::table('shopping_cart')
            ->select(
                'shopping_cart.id as cart_id',
                'shopping_cart.product_id',
                'shopping_cart.quantity as cart_quantity', // Explicitly name it
                'shopping_cart.selling_price_snapshot as cart_price', // Explicitly name it
                'products.*',
                'farmers.id as farmer_id',
                'lead_farmers.id as lead_farmer_id',
                'lead_farmers.payment_details'
            )
            ->join('products', 'shopping_cart.product_id', '=', 'products.id')
            ->join('farmers', 'products.farmer_id', '=', 'farmers.id')
            ->join('lead_farmers', 'products.lead_farmer_id', '=', 'lead_farmers.id')
            ->where('shopping_cart.buyer_id', $buyer->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
            $orderTotal = 0;

            // Calculate total using cart fields
            foreach ($cartItems as $item) {
                $orderTotal += $item->cart_quantity * $item->cart_price;
            }

            $grandTotal = $orderTotal;
            $firstCartItem = $cartItems->first();

            $orderId = DB::table('orders')->insertGetId([
                'order_number' => $orderNumber,
                'buyer_id' => $buyer->id,
                'farmer_id' => $firstCartItem->farmer_id,
                'lead_farmer_id' => $firstCartItem->lead_farmer_id,
                'order_status' => 'Processing order',
                'total_amount' => $grandTotal,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Insert order items using cart fields
            foreach ($cartItems as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item->product_id,
                    'product_name_snapshot' => $item->product_name,
                    'quantity_ordered' => $item->cart_quantity, // Use cart_quantity
                    'unit_price_snapshot' => $item->cart_price, // Use cart_price
                    'item_total' => $item->cart_quantity * $item->cart_price, // Use cart fields
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update product quantity using InventoryService
                $product = Product::find($item->product_id);
                if ($product) {
                    app(InventoryService::class)->updateStock(
                        $product,
                        -$item->cart_quantity,
                        'order_placed',
                        'Order #' . $orderNumber . ' placed via Credit Card',
                        $orderId
                    );
                }
            }

            $paymentRef = 'PAY-' . date('YmdHis') . '-' . strtoupper(uniqid());
            $paymentStatus = 'completed';
            $paymentId = DB::table('payments')->insertGetId([
                'order_id' => $orderId,
                'payment_reference' => $paymentRef,
                'amount' => $grandTotal,
                'payment_method' => 'credit_card',
                'payment_status' => $paymentStatus,
                'payment_date' => now(),
                'transaction_id' => 'TXN-' . uniqid(),
                'receipt_url' => '#',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'order_status' => 'paid',
                    'paid_date' => now(),
                    'updated_at' => now()
                ]);

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
            $invoicePath = 'invoices/' . $invoiceNumber . '.pdf';
            DB::table('invoices')->insertGetId([
                'invoice_number' => $invoiceNumber,
                'order_id' => $orderId,
                'invoice_path' => $invoicePath,
                'generated_at' => now()
            ]);

            // ... rest of the notification code remains the same ...

            DB::table('shopping_cart')
                ->where('buyer_id', $buyer->id)
                ->delete();
            session(['cart_count' => 0]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payment successful! Order placed.',
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'invoice_number' => $invoiceNumber
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment failed. Please try again. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkoutSuccess($orderId)
    {
        $buyer = $this->getBuyer();
        $order = DB::table('orders')
            ->select(
                'orders.*',
                'payments.payment_reference',
                'payments.transaction_id',
                'payments.payment_date',
                'invoices.invoice_number',
                'invoices.invoice_path'
            )
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->leftJoin('invoices', 'orders.id', '=', 'invoices.order_id')
            ->where('orders.id', $orderId)
            ->where('orders.buyer_id', $buyer->id)
            ->first();
        if (!$order) {
            return redirect()->route('buyer.dashboard')->with('error', 'Order not found.');
        }
        $orderItems = DB::table('order_items')
            ->select(
                'order_items.*',
                'products.product_photo'
            )
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', $orderId)
            ->get();
        $firstItem = DB::table('order_items')
            ->select('products.farmer_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', $orderId)
            ->first();
        $pickupDetails = null;
        if ($firstItem) {
            $pickupDetails = DB::table('farmers')
                ->where('id', $firstItem->farmer_id)
                ->select('name', 'primary_mobile', 'residential_address', 'address_map_link')
                ->first();
        }
        return view('buyer.checkout_success', [
            'order' => $order,
            'orderItems' => $orderItems,
            'pickupDetails' => $pickupDetails,
            'buyer' => $buyer
        ]);
    }

    public function checkoutFailed()
    {
        return view('buyer.checkout_failed');
    }

    public function placeOrder(Request $request)
{
    $buyer = $this->getBuyer();

    $validated = $request->validate([
        'payment_method' => 'required|string|in:cod,bank_transfer',
        'order_type' => 'required|string|in:Pickup'
    ]);

    // Get cart items with explicit reference to shopping_cart fields
    $cartItems = DB::table('shopping_cart')
        ->select(
            'shopping_cart.id as cart_id',
            'shopping_cart.product_id',
            'shopping_cart.quantity as cart_quantity',
            'shopping_cart.selling_price_snapshot as cart_price',
            'products.*',
            'farmers.id as farmer_id',
            'lead_farmers.id as lead_farmer_id'
        )
        ->join('products', 'shopping_cart.product_id', '=', 'products.id')
        ->join('farmers', 'products.farmer_id', '=', 'farmers.id')
        ->join('lead_farmers', 'products.lead_farmer_id', '=', 'lead_farmers.id')
        ->where('shopping_cart.buyer_id', $buyer->id)
        ->get();

    if ($cartItems->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Your cart is empty.'
        ], 400);
    }

    DB::beginTransaction();

    try {
        $orderNumberPrefix = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        // Group items by lead_farmer_id to create separate orders
        $groupedByLeadFarmer = [];
        foreach ($cartItems as $item) {
            $leadFarmerId = $item->lead_farmer_id;
            if (!isset($groupedByLeadFarmer[$leadFarmerId])) {
                $groupedByLeadFarmer[$leadFarmerId] = [
                    'farmer_id' => $item->farmer_id,
                    'items' => [],
                    'subtotal' => 0
                ];
            }
            $groupedByLeadFarmer[$leadFarmerId]['items'][] = $item;
            $groupedByLeadFarmer[$leadFarmerId]['subtotal'] += $item->cart_quantity * $item->cart_price;
        }

        $orderIds = [];
        $temporaryOrderIds = [];

        foreach ($groupedByLeadFarmer as $leadFarmerId => $group) {
            $orderNumber = $orderNumberPrefix . '-' . $leadFarmerId;

                // For Pickup (COD), create actual order immediately
                $orderId = DB::table('orders')->insertGetId([
                    'order_number' => $orderNumber,
                    'buyer_id' => $buyer->id,
                    'farmer_id' => $group['farmer_id'],
                    'lead_farmer_id' => $leadFarmerId,
                    'order_status' => 'Processing order',
                    'order_type' => 'Pickup',
                    'total_amount' => $group['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $orderIds[] = $orderId;

                foreach ($group['items'] as $item) {
                    DB::table('order_items')->insert([
                        'order_id' => $orderId,
                        'product_id' => $item->product_id,
                        'product_name_snapshot' => $item->product_name,
                        'quantity_ordered' => $item->cart_quantity,
                        'unit_price_snapshot' => $item->cart_price,
                        'item_total' => $item->cart_quantity * $item->cart_price,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Update inventory for immediate orders
                    $product = Product::find($item->product_id);
                    if ($product) {
                        app(InventoryService::class)->updateStock(
                            $product,
                            -$item->cart_quantity,
                            'order_placed',
                            'Order #' . $orderNumber . ' placed via Pickup',
                            $orderId
                        );
                    }
                }

                // Create invoice for Pickup order
                $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
                DB::table('invoices')->insertGetId([
                    'invoice_number' => $invoiceNumber,
                    'order_id' => $orderId,
                    'invoice_path' => 'invoices/' . $invoiceNumber . '.pdf',
                    'generated_at' => now(),
                    'updated_at' => now()
                ]);

                // Notifications
                $this->sendOrderNotifications($orderId, $buyer);
        }

        // Clear shopping cart
        DB::table('shopping_cart')
            ->where('buyer_id', $buyer->id)
            ->delete();

        session(['cart_count' => 0]);

        DB::commit();


        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully! Contact the seller for the pickup.',
            'redirect_url' => route('buyer.history')
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Order placement error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to place order. Error: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Send SMS and Email notifications to farmer and lead farmer
     */
    private function sendOrderNotifications($orderId, $buyer)
    {
        try {
            // Get order details
            $order = DB::table('orders')
                ->select(
                    'orders.*',
                    'farmers.id as farmer_id',
                    'farmers.name as farmer_name',
                    'farmers.primary_mobile as farmer_mobile',
                    'farmers.email as farmer_email',
                    'lead_farmers.id as lead_farmer_id',
                    'lead_farmers.name as lead_farmer_name',
                    'lead_farmers.primary_mobile as lead_farmer_mobile',
                    'lead_farmers.user_id as lead_farmer_user_id'
                )
                ->leftJoin('farmers', 'orders.farmer_id', '=', 'farmers.id')
                ->leftJoin('lead_farmers', 'orders.lead_farmer_id', '=', 'lead_farmers.id')
                ->where('orders.id', $orderId)
                ->first();

            if (!$order) {
                Log::error("Order not found for notifications: $orderId");
                return;
            }

            // Get order items for product details
            $orderItems = DB::table('order_items')
                ->select(
                    'order_items.product_name_snapshot',
                    'order_items.quantity_ordered'
                )
                ->where('order_items.order_id', $orderId)
                ->get();

            // Prepare product information
            $productDetails = [];
            foreach ($orderItems as $item) {
                $productDetails[] = $item->quantity_ordered . ' of ' . $item->product_name_snapshot;
            }
            $productList = implode(', ', $productDetails);

            // 1. Send SMS to Farmer
            if ($order->farmer_mobile) {
                $farmerMessage = "Your product, $productList, has been ordered by $buyer->name. Prepare for pickup.";
                $this->sendSMS($order->farmer_mobile, $farmerMessage);
                Log::info("SMS sent to farmer: {$order->farmer_mobile}");
            }

            // 2. Send SMS to Lead Farmer
            if ($order->lead_farmer_mobile) {
                $leadFarmerMessage = "New order #{$order->order_number} received from $buyer->name. Products: $productList. Total: Rs. {$order->total_amount}. Contact farmer for pickup and collect payment on pickup.";
                $this->sendSMS($order->lead_farmer_mobile, $leadFarmerMessage);
                Log::info("SMS sent to lead farmer: {$order->lead_farmer_mobile}");
            }

            // 4. Send Email to Lead Farmer (get email from users table)
            if ($order->lead_farmer_user_id) {
                $leadFarmerUser = DB::table('users')
                    ->where('id', $order->lead_farmer_user_id)
                    ->first();

                if ($leadFarmerUser && $leadFarmerUser->email) {
                    $leadFarmerEmailData = [
                        'type' => 'lead_farmer',
                        'order_number' => $order->order_number,
                        'buyer_name' => $buyer->name,
                        'product_list' => $productList,
                        'total_amount' => $order->total_amount,
                        'order_date' => $order->created_at,
                        'lead_farmer_name' => $order->lead_farmer_name,
                        'farmer_name' => $order->farmer_name,
                        'message' => "You have received a new order. Please contact the farmer for product pickup and collect payment from the buyer on pickup."
                    ];

                    Mail::to($leadFarmerUser->email)->send(new OrderNotificationMail($leadFarmerEmailData));
                    Log::info("Email sent to lead farmer: {$leadFarmerUser->email}");
                }
            }

            Log::info("All notifications sent for order: {$order->order_number}");

        } catch (\Exception $e) {
            Log::error('Error sending order notifications: ' . $e->getMessage());
            // Don't throw exception, just log the error so order placement continues
        }
    }

    public function cancelOrder(Request $request, $orderId)
    {
        $buyer = $this->getBuyer();
        $order = DB::table('orders')
            ->where('id', $orderId)
            ->where('buyer_id', $buyer->id)
            ->whereIn('order_status', ['Processing order', 'confirmed'])
            ->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or cannot be cancelled.'
            ], 404);
        }
        DB::beginTransaction();
        try {
            $orderItems = DB::table('order_items')
                ->where('order_id', $orderId)
                ->get();
            foreach ($orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    app(InventoryService::class)->updateStock(
                        $product,
                        $item->quantity_ordered,
                        'order_cancelled',
                        'Order #' . $order->order_number . ' cancelled by buyer',
                        $orderId
                    );
                }
            }
            DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'order_status' => 'cancelled',
                    'updated_at' => now()
                ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order. Please try again.'
            ], 500);
        }
    }

    public function createProductRequestForm()
    {
        $units = DB::table('system_standards')
            ->where('standard_type', 'unit_of_measure')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get(['standard_value', 'description']);
        return view('buyer.product_request.create', [
            'units' => $units
        ]);
    }

    public function storeProductRequest(Request $request)
    {
        $buyer = $this->getBuyer();
        $units = DB::table('system_standards')
            ->where('standard_type', 'unit_of_measure')
            ->where('is_active', true)
            ->pluck('standard_value')
            ->toArray();
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'needed_quantity' => 'required|numeric|min:0.01',
            'unit_of_measure' => 'required|string|in:' . implode(',', $units),
            'needed_date' => 'required|date|after_or_equal:today',
            'unit_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000'
        ]);
        DB::beginTransaction();
        try {
            $productImage = null;
            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $filename = 'request_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $uploadPath = public_path('uploads/buyer_product_requests');
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }
                $file->move($uploadPath, $filename);
                $productImage = $filename;
            }
            $requestId = DB::table('buyer_product_requests')->insertGetId([
                'buyer_id' => $buyer->id,
                'product_name' => $validated['product_name'],
                'product_image' => $productImage,
                'needed_quantity' => $validated['needed_quantity'],
                'unit_of_measure' => $validated['unit_of_measure'],
                'needed_date' => $validated['needed_date'],
                'unit_price' => $validated['unit_price'],
                'description' => $validated['description'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Product request submitted successfully!',
                'request_id' => $requestId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product request error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit request. Please try again.'
            ], 500);
        }
    }

    public function myProductRequests()
    {
        $buyer = $this->getBuyer();
        $today = now()->toDateString();
        DB::table('buyer_product_requests')
            ->where('buyer_id', $buyer->id)
            ->where('status', 'active')
            ->whereDate('needed_date', '<', $today)
            ->update([
                'status' => 'expired',
                'updated_at' => now()
            ]);
        $requests = DB::table('buyer_product_requests')
            ->where('buyer_id', $buyer->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('buyer.product_request.my_requests', [
            'requests' => $requests
        ]);
    }

    public function checkExpiredRequests()
    {
        $buyer = $this->getBuyer();
        $today = now()->toDateString();
        $updated = DB::table('buyer_product_requests')
            ->where('buyer_id', $buyer->id)
            ->where('status', 'active')
            ->whereDate('needed_date', '<', $today)
            ->update([
                'status' => 'expired',
                'updated_at' => now()
            ]);
        return response()->json([
            'success' => true,
            'updated' => $updated,
            'message' => $updated > 0 ? "Updated {$updated} expired requests" : "No requests to update"
        ]);
    }

    public function updateRequestStatus(Request $request, $id)
    {
        $buyer = $this->getBuyer();
        $validated = $request->validate([
            'status' => 'required|string|in:active,fulfilled,expired,cancelled'
        ]);
        $requestRecord = DB::table('buyer_product_requests')
            ->where('id', $id)
            ->where('buyer_id', $buyer->id)
            ->first();
        if (!$requestRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found.'
            ], 404);
        }
        DB::table('buyer_product_requests')
            ->where('id', $id)
            ->update([
                'status' => $validated['status'],
                'updated_at' => now()
            ]);
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!'
        ]);
    }

    public function deleteRequest($id)
    {
        $buyer = $this->getBuyer();
        $requestRecord = DB::table('buyer_product_requests')
            ->where('id', $id)
            ->where('buyer_id', $buyer->id)
            ->first();
        if (!$requestRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found.'
            ], 404);
        }
        if ($requestRecord->product_image) {
            $imagePath = public_path('uploads/buyer_product_requests/' . $requestRecord->product_image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        DB::table('buyer_product_requests')
            ->where('id', $id)
            ->delete();
        return response()->json([
            'success' => true,
            'message' => 'Request deleted successfully!'
        ]);
    }

    public function createComplaint(Request $request)
    {
        $buyer = $this->getBuyer();
        $orders = DB::table('orders')
            ->select('orders.id', 'orders.order_number', 'orders.total_amount', 'farmers.name as farmer_name')
            ->leftJoin('farmers', 'orders.farmer_id', '=', 'farmers.id')
            ->where('orders.buyer_id', $buyer->id)
            ->whereIn('orders.order_status', ['paid', 'completed'])
            ->orderBy('orders.created_at', 'desc')
            ->get();
        return view('buyer.complaints.create', [
            'orders' => $orders
        ]);
    }

    public function storeComplaint(Request $request)
    {
        $buyer = $this->getBuyer();
        $user = Auth::user();
        $validated = $request->validate([
            'complaint_type' => 'required|string|in:product_quality,wrong_location,farmer_contact,availability_issue,payment_issue,invoice_error,category_misclassification,farmer_no_show,product_photo_mismatch,request_ignored,filter_issue,vague_instructions,payment_technical,other',
            'related_order_id' => 'nullable|integer|exists:orders,id',
            'description' => 'required|string|min:20|max:2000'
        ]);
        DB::beginTransaction();
        try {
            $againstUserId = null;
            if ($validated['related_order_id']) {
                $order = DB::table('orders')
                    ->where('id', $validated['related_order_id'])
                    ->where('buyer_id', $buyer->id)
                    ->first();
                if (!$order) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order not found or does not belong to you.'
                    ], 403);
                }
                $leadFarmerId = DB::table('orders')
                    ->where('id', $validated['related_order_id'])
                    ->value('lead_farmer_id');
                if ($leadFarmerId) {
                    $leadFarmer = DB::table('lead_farmers')
                        ->where('id', $leadFarmerId)
                        ->first();
                    $againstUserId = $leadFarmer ? $leadFarmer->user_id : null;
                }
            }
            $complaintId = DB::table('complaints')->insertGetId([
                'complainant_user_id' => $user->id,
                'complainant_role' => 'buyer',
                'against_user_id' => $againstUserId,
                'related_order_id' => $validated['related_order_id'] ?? null,
                'complaint_type' => $validated['complaint_type'],
                'description' => $validated['description'],
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::table('notifications')->insert([
                'user_id' => null,
                'recipient_type' => 'system_wide',
                'title' => 'New Complaint Filed',
                'message' => 'Buyer ' . $buyer->name . ' has filed a new complaint (#' . $complaintId . ')',
                'notification_type' => 'admin_alert',
                'related_id' => $complaintId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Complaint submitted successfully!',
                'complaint_id' => $complaintId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Complaint submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit complaint. Please try again.'
            ], 500);
        }
    }

    public function listComplaints(Request $request)
    {
        $buyer = $this->getBuyer();
        $user = Auth::user();
        $totalComplaints = DB::table('complaints')
            ->where('complainant_user_id', $user->id)
            ->where('complainant_role', 'buyer')
            ->count();
        $openComplaints = DB::table('complaints')
            ->where('complainant_user_id', $user->id)
            ->where('complainant_role', 'buyer')
            ->where('status', 'new')
            ->count();
        $inProgressComplaints = DB::table('complaints')
            ->where('complainant_user_id', $user->id)
            ->where('complainant_role', 'buyer')
            ->where('status', 'in_progress')
            ->count();
        $resolvedComplaints = DB::table('complaints')
            ->where('complainant_user_id', $user->id)
            ->where('complainant_role', 'buyer')
            ->whereIn('status', ['resolved', 'rejected'])
            ->count();
        $complaints = DB::table('complaints')
            ->select('complaints.*', 'orders.order_number')
            ->leftJoin('orders', 'complaints.related_order_id', '=', 'orders.id')
            ->where('complaints.complainant_user_id', $user->id)
            ->where('complaints.complainant_role', 'buyer')
            ->orderBy('complaints.created_at', 'desc')
            ->paginate(10);
        $complaints->transform(function ($complaint) {
            $complaint->created_at = \Carbon\Carbon::parse($complaint->created_at);
            $complaint->updated_at = \Carbon\Carbon::parse($complaint->updated_at);
            return $complaint;
        });
        session(['sharedCounts' => ['openComplaints' => $openComplaints]]);
        return view('buyer.complaints.list', [
            'complaints' => $complaints,
            'totalComplaints' => $totalComplaints,
            'openComplaints' => $openComplaints,
            'inProgressComplaints' => $inProgressComplaints,
            'resolvedComplaints' => $resolvedComplaints
        ]);
    }

    public function viewComplaint($id)
    {
        $user = Auth::user();
        $complaint = DB::table('complaints')
            ->select('complaints.*', 'orders.order_number')
            ->leftJoin('orders', 'complaints.related_order_id', '=', 'orders.id')
            ->where('complaints.id', $id)
            ->where('complaints.complainant_user_id', $user->id)
            ->where('complaints.complainant_role', 'buyer')
            ->first();
        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found.'
            ], 404);
        }
        $complaint->created_at_formatted = \Carbon\Carbon::parse($complaint->created_at)->format('M d, Y h:i A');
        $complaint->updated_at_formatted = \Carbon\Carbon::parse($complaint->updated_at)->format('M d, Y h:i A');
        if ($complaint->resolved_by_facilitator_id) {
            $facilitator = DB::table('facilitators')
                ->where('id', $complaint->resolved_by_facilitator_id)
                ->first();
            $complaint->resolved_by = $facilitator ? $facilitator->name : 'Admin';
        }
        return response()->json([
            'success' => true,
            'complaint' => $complaint
        ]);
    }

    public function deleteComplaint($id)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $complaint = DB::table('complaints')
                ->where('id', $id)
                ->where('complainant_user_id', $user->id)
                ->where('complainant_role', 'buyer')
                ->first();
            if (!$complaint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Complaint not found.'
                ], 404);
            }
            if ($complaint->status !== 'new') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only new complaints can be deleted.'
                ], 403);
            }
            DB::table('complaints')
                ->where('id', $id)
                ->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Complaint deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Complaint deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete complaint. Please try again.'
            ], 500);
        }
    }

    public function updateComplaint(Request $request, $id)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'complaint_type' => 'required|string|in:product_quality,wrong_location,farmer_contact,availability_issue,payment_issue,invoice_error,category_misclassification,farmer_no_show,product_photo_mismatch,request_ignored,filter_issue,vague_instructions,payment_technical,other',
            'description' => 'required|string|min:20|max:2000'
        ]);
        DB::beginTransaction();
        try {
            $complaint = DB::table('complaints')
                ->where('id', $id)
                ->where('complainant_user_id', $user->id)
                ->where('complainant_role', 'buyer')
                ->first();
            if (!$complaint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Complaint not found.'
                ], 404);
            }
            if ($complaint->status !== 'new') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only new complaints can be edited.'
                ], 403);
            }
            DB::table('complaints')
                ->where('id', $id)
                ->update([
                    'complaint_type' => $validated['complaint_type'],
                    'description' => $validated['description'],
                    'updated_at' => now()
                ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Complaint updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Complaint update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update complaint. Please try again.'
            ], 500);
        }
    }
}


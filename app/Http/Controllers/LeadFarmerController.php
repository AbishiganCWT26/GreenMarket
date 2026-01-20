<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Farmer;
use App\Models\Product;
use App\Models\LeadFarmer;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Models\SystemStandard;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadFarmerController extends Controller
{
    /**
     * Lead Farmer Dashboard
     */
    public function dashboard()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;
        $userId = Auth::id();

        // Dashboard statistics
        $totalFarmers = Farmer::where('lead_farmer_id', $leadFarmerId)->count();
        $activeProducts = Product::where('lead_farmer_id', $leadFarmerId)
            ->where('is_available', true)
            ->count();

        $totalOrders = Order::where('lead_farmer_id', $leadFarmerId)->count();
        $pendingOrders = Order::where('lead_farmer_id', $leadFarmerId)
            ->where('order_status', 'pending')
            ->count();

        $recentOrders = Order::with(['buyer', 'farmer'])
            ->where('lead_farmer_id', $leadFarmerId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentNotifications = Notification::where('user_id', Auth::id())
            ->orWhere(function($query) use ($leadFarmerId) {
                $query->where('related_id', $leadFarmerId)
                    ->where('recipient_type', 'lead_farmer');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $sharedCounts = [
            'lowStockProducts' => Product::where('lead_farmer_id', $leadFarmerId)
                ->where('quantity', '<', 10)
                ->count(),
            'pendingOrders' => $pendingOrders,
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

        return view('lead_farmer.dashboard', compact(
            'totalFarmers',
            'activeProducts',
            'totalOrders',
            'pendingOrders',
            'recentOrders',
            'recentNotifications',
            'sharedCounts'
        ));
    }

    /**
     * Register Farmer Page
     */
    public function registerFarmer()
    {
        $districts = [
            'Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo',
            'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara',
            'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar',
            'Matale', 'Matara', 'Monaragala', 'Mullaitivu', 'Nuwara Eliya',
            'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'
        ];

        return view('lead_farmer.register_farmer', compact('districts'));
    }

    /**
     * Store Farmer Registration
     */
    public function storeFarmer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'nic_no' => 'required|string|max:20|unique:farmers,nic_no',
            'primary_mobile' => 'required|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'residential_address' => 'required|string',
            'address_map_link' => 'nullable|url',
            'district' => 'required|string',
            'grama_niladhari_division' => 'required|string|max:100',
            'preferred_payment' => 'required|in:bank,ezcash,mcash,all',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Create User account for farmer
            $user = User::create([
                'username' => $request->nic_no,
                'password' => Hash::make($request->nic_no), // Default password is NIC
                'email' => $request->email,
                'role' => 'farmer',
                'is_active' => true,
            ]);

            // Handle profile photo upload
            $profilePhoto = 'default-avatar.png';
            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');
                $filename = 'farmer_' . time() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('public/uploads/profile_pictures', $filename);
                $profilePhoto = $filename;

                // Update user profile photo
                $user->profile_photo = $profilePhoto;
                $user->save();
            }

            // Get lead farmer ID
            $leadFarmerId = Auth::user()->leadFarmer->id;

            // Create Farmer record
            $farmer = Farmer::create([
                'user_id' => $user->id,
                'lead_farmer_id' => $leadFarmerId,
                'name' => $request->name,
                'nic_no' => $request->nic_no,
                'primary_mobile' => $request->primary_mobile,
                'whatsapp_number' => $request->whatsapp_number,
                'email' => $request->email,
                'residential_address' => $request->residential_address,
                'address_map_link' => $request->address_map_link,
                'district' => $request->district,
                'grama_niladhari_division' => $request->grama_niladhari_division,
                'preferred_payment' => $request->preferred_payment,
                'payment_details' => $this->formatPaymentDetails($request),
                // Payment detail columns
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'ezcash_mobile' => $request->ezcash_mobile,
                'mcash_mobile' => $request->mcash_mobile,
            ]);

            DB::commit();

            return redirect()->route('lf.manageFarmers')
                ->with('success', 'Farmer registered successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error registering farmer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Manage Farmers List
     */
    public function manageFarmers()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $query = Farmer::with(['user'])
            ->where('lead_farmer_id', $leadFarmerId);

        // Apply filters
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('nic_no', 'like', "%$search%")
                ->orWhere('primary_mobile', 'like', "%$search%");
            });
        }

        if (request('district')) {
            $query->where('district', request('district'));
        }

        if (request('status') == 'active') {
            $query->where('is_active', true);
        } elseif (request('status') == 'inactive') {
            $query->where('is_active', false);
        }

        $farmers = $query->orderBy('created_at', 'desc')->get();

        $districts = [
            'Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo',
            'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara',
            'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar',
            'Matale', 'Matara', 'Monaragala', 'Mullaitivu', 'Nuwara Eliya',
            'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'
        ];

        return view('lead_farmer.manage_farmers', compact('farmers', 'districts'));
    }

    /**
     * Add New Product Page
     */
    public function addProduct()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        // Get farmers belonging to this lead farmer
        $farmers = Farmer::where('lead_farmer_id', $leadFarmerId)
            ->orderBy('name')
            ->get();

        // Get product categories and subcategories
        $categories = ProductCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $subcategories = ProductSubcategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // Get units of measure
        $units = SystemStandard::where('standard_type', 'unit_of_measure')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->pluck('standard_value');

        // Get quality grades
        $grades = SystemStandard::where('standard_type', 'quality_grade')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->pluck('standard_value');

        return view('lead_farmer.add_product', compact(
            'farmers',
            'categories',
            'subcategories',
            'units',
            'grades'
        ));
    }

    /**
     * Store New Product
     */
    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:200',
            'product_description' => 'nullable|string',
            'product_photo' => 'nullable|image|max:2048',
            'farmer_id' => 'required|exists:farmers,id',
            'type_variant' => 'nullable|string|max:50',
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'quantity' => 'required|numeric|min:0',
            'unit_of_measure' => 'required|string|max:20',
            'quality_grade' => 'nullable|string|max:50',
            'expected_availability_date' => 'required|date',
            'selling_price' => 'required|numeric|min:0',
            'pickup_address' => 'nullable|string',
            'pickup_map_link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get lead farmer ID
            $leadFarmer = Auth::user()->leadFarmer;
            $leadFarmerId = $leadFarmer->id;

            // Handle product photo upload
            $productPhoto = null;
            if ($request->hasFile('product_photo')) {
                $photo = $request->file('product_photo');
                $filename = 'product_' . time() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('public/product_photos', $filename);
                $productPhoto = $filename;
            }

            // Create product
            $product = Product::create([
                'farmer_id' => $request->farmer_id,
                'lead_farmer_id' => $leadFarmerId,
                'product_name' => $request->product_name,
                'product_description' => $request->product_description,
                'product_photo' => $productPhoto,
                'type_variant' => $request->type_variant,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'quantity' => $request->quantity,
                'unit_of_measure' => $request->unit_of_measure,
                'quality_grade' => $request->quality_grade,
                'expected_availability_date' => $request->expected_availability_date,
                'selling_price' => $request->selling_price,
                'pickup_address' => $request->pickup_address ?: Farmer::find($request->farmer_id)->residential_address,
                'pickup_map_link' => $request->pickup_map_link ?: Farmer::find($request->farmer_id)->address_map_link,
                'is_available' => true,
            ]);

            return redirect()->route('lf.manageProducts')
                ->with('success', 'Product added successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error adding product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Manage Products Page
     */
    public function manageProducts(Request $request)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $query = Product::with(['farmer', 'category', 'subcategory'])
            ->where('lead_farmer_id', $leadFarmerId);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%$search%")
                  ->orWhere('product_description', 'like', "%$search%");
            });
        }

        if ($request->filled('farmer_id')) {
            $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status == 'available') {
                $query->where('is_available', true);
            } elseif ($request->status == 'sold_out') {
                $query->where('is_available', false)
                      ->orWhere('quantity', '<=', 0);
            }
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        // Get farmers for filter dropdown
        $farmers = Farmer::where('lead_farmer_id', $leadFarmerId)
            ->orderBy('name')
            ->get();

        // Get categories for filter dropdown
        $categories = ProductCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('lead_farmer.manage_products', compact('products', 'farmers', 'categories'));
    }

    /**
     * Edit Product Page
     */
    public function editProduct($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $product = Product::with(['farmer'])
            ->where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        $farmers = Farmer::where('lead_farmer_id', $leadFarmerId)
            ->orderBy('name')
            ->get();

        $categories = ProductCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $subcategories = ProductSubcategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $units = SystemStandard::where('standard_type', 'unit_of_measure')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->pluck('standard_value');

        $grades = SystemStandard::where('standard_type', 'quality_grade')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->pluck('standard_value');

        return view('lead_farmer.edit_product', compact(
            'product',
            'farmers',
            'categories',
            'subcategories',
            'units',
            'grades'
        ));
    }

    /**
     * Update Product
     */
    public function updateProduct(Request $request, $id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $product = Product::where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:200',
            'product_description' => 'nullable|string',
            'product_photo' => 'nullable|image|max:2048',
            'farmer_id' => 'required|exists:farmers,id',
            'type_variant' => 'nullable|string|max:50',
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'quantity' => 'required|numeric|min:0',
            'unit_of_measure' => 'required|string|max:20',
            'quality_grade' => 'nullable|string|max:50',
            'expected_availability_date' => 'required|date',
            'selling_price' => 'required|numeric|min:0',
            'pickup_address' => 'nullable|string',
            'pickup_map_link' => 'nullable|url',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle product photo update
            if ($request->hasFile('product_photo')) {
                // Delete old photo if exists
                if ($product->product_photo && $product->product_photo != 'default-product.jpg') {
                    Storage::delete('public/product_photos/' . $product->product_photo);
                }

                $photo = $request->file('product_photo');
                $filename = 'product_' . time() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('public/product_photos', $filename);
                $product->product_photo = $filename;
            }

            // Update product
            $product->update([
                'product_name' => $request->product_name,
                'product_description' => $request->product_description,
                'farmer_id' => $request->farmer_id,
                'type_variant' => $request->type_variant,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'quantity' => $request->quantity,
                'unit_of_measure' => $request->unit_of_measure,
                'quality_grade' => $request->quality_grade,
                'expected_availability_date' => $request->expected_availability_date,
                'selling_price' => $request->selling_price,
                'pickup_address' => $request->pickup_address,
                'pickup_map_link' => $request->pickup_map_link,
                'is_available' => $request->has('is_available'),
            ]);

            return redirect()->route('lf.manageProducts')
                ->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete Product
     */
    public function deleteProduct($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $product = Product::where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        try {
            // Delete product photo if exists
            if ($product->product_photo && $product->product_photo != 'default-product.jpg') {
                Storage::delete('public/product_photos/' . $product->product_photo);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit Profile Page
     */
    public function editProfile()
    {
        $user = Auth::user();
        $leadFarmer = $user->leadFarmer;

        return view('lead_farmer.profile', compact('user', 'leadFarmer'));
    }

    /**
     * Update Profile Details
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $leadFarmer = $user->leadFarmer;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'nic_no' => 'required|string|max:20|unique:lead_farmers,nic_no,' . $leadFarmer->id,
            'primary_mobile' => 'required|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100|unique:users,email,' . $user->id,
            'residential_address' => 'required|string',
            'grama_niladhari_division' => 'required|string|max:100',
            'group_name' => 'required|string|max:100',
            'group_number' => 'required|string|max:50|unique:lead_farmers,group_number,' . $leadFarmer->id,
            'preferred_payment' => 'required|in:bank,ezcash,mcash,all',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update user email if changed
            if ($request->email != $user->email) {
                $user->email = $request->email;
                $user->save();
            }

            // Update lead farmer details
            $leadFarmer->update([
                'name' => $request->name,
                'nic_no' => $request->nic_no,
                'primary_mobile' => $request->primary_mobile,
                'whatsapp_number' => $request->whatsapp_number,
                'residential_address' => $request->residential_address,
                'grama_niladhari_division' => $request->grama_niladhari_division,
                'group_name' => $request->group_name,
                'group_number' => $request->group_number,
                'preferred_payment' => $request->preferred_payment,
                'payment_details' => $request->payment_details,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error updating profile: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update Profile Photo
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        try {
            // Delete old photo if exists and not default
            if ($user->profile_photo && $user->profile_photo != 'default-avatar.png') {
                Storage::delete('public/profile_photos/' . $user->profile_photo);
            }

            // Upload new photo
            $photo = $request->file('profile_photo');
            $filename = 'lead_farmer_' . $user->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/profile_photos', $filename);

            // Update user record
            $user->profile_photo = $filename;
            $user->save();

            return redirect()->back()
                ->with('success', 'Profile photo updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating profile photo: ' . $e->getMessage());
        }
    }

    /**
     * Get Subcategories by Category (AJAX)
     */
    public function getSubcategories($categoryId)
    {
        $subcategories = ProductSubcategory::where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return response()->json($subcategories);
    }

    /**
     * View Orders
     */
    public function viewOrders()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $orders = Order::with(['buyer', 'farmer', 'orderItems.product'])
            ->where('lead_farmer_id', $leadFarmerId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('lead_farmer.orders', compact('orders'));
    }

    /**
     * View Order Details
     */
    public function viewOrder($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $order = Order::with(['buyer', 'farmer', 'orderItems.product', 'payments'])
            ->where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        return view('lead_farmer.order_details', compact('order'));
    }

    /**
     * Update Order Status
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $order = Order::where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:confirmed,ready_for_pickup,completed,cancelled',
        ]);

        try {
            $oldStatus = $order->order_status;
            $order->order_status = $request->status;

            // Update dates based on status
            if ($request->status == 'completed' && !$order->completed_date) {
                $order->completed_date = now();
            }

            $order->save();

            // Create notification for buyer
            if ($request->status != $oldStatus) {
                Notification::create([
                    'user_id' => $order->buyer->user_id,
                    'recipient_type' => 'user',
                    'title' => 'Order Status Updated',
                    'message' => "Your order #{$order->order_number} status has been updated to: " . ucfirst(str_replace('_', ' ', $request->status)),
                    'notification_type' => 'system',
                    'related_id' => $order->id,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Order status updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating order status: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to format payment details
     */
    private function formatPaymentDetails(Request $request)
    {
        $details = [];

        if ($request->preferred_payment == 'bank' || $request->preferred_payment == 'all') {
            $details['bank'] = [
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
            ];
        }

        if ($request->preferred_payment == 'ezcash' || $request->preferred_payment == 'all') {
            $details['ezcash'] = [
                'mobile' => $request->ezcash_mobile,
            ];
        }

        if ($request->preferred_payment == 'mcash' || $request->preferred_payment == 'all') {
            $details['mcash'] = [
                'mobile' => $request->mcash_mobile,
            ];
        }

        return json_encode($details);
    }

    /**
     * Sales Reports Page
     */
    public function salesReports()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        // Get sales data for reports
        $salesData = Order::where('lead_farmer_id', $leadFarmerId)
            ->where('order_status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->get();

        // Monthly summary
        $monthlySummary = Order::where('lead_farmer_id', $leadFarmerId)
            ->where('order_status', 'paid')
            ->select(
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'), DB::raw('EXTRACT(YEAR FROM created_at)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('lead_farmer.reports.sales', compact('salesData', 'monthlySummary'));
    }

    /**
     * Inventory Reports Page
     */
    public function inventoryReports()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $products = Product::with(['category', 'subcategory', 'farmer'])
            ->where('lead_farmer_id', $leadFarmerId)
            ->orderBy('quantity', 'asc')
            ->get();

        $lowStockProducts = Product::where('lead_farmer_id', $leadFarmerId)
            ->where('quantity', '<', 10)
            ->count();

        $totalStockValue = Product::where('lead_farmer_id', $leadFarmerId)
            ->select(DB::raw('SUM(quantity * selling_price) as total_value'))
            ->first();

        return view('lead_farmer.reports.inventory', compact('products', 'lowStockProducts', 'totalStockValue'));
    }

    /**
     * Farmer Performance Reports
     */
    public function farmerPerformanceReports()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $farmers = Farmer::withCount(['products' => function($query) {
                $query->where('is_available', true);
            }])
            ->withSum(['products' => function($query) {
                $query->where('is_available', true);
            }], 'quantity')
            ->where('lead_farmer_id', $leadFarmerId)
            ->get();

        // Get sales data per farmer
        $farmerSales = Order::where('lead_farmer_id', $leadFarmerId)
            ->where('order_status', 'paid')
            ->select(
                'farmer_id',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->groupBy('farmer_id')
            ->get()
            ->keyBy('farmer_id');

        return view('lead_farmer.reports.farmer_performance', compact('farmers', 'farmerSales'));
    }

    /**
     * Notifications Page
     */
    public function notifications()
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->orWhere(function($query) use ($user) {
                $query->where('related_id', $user->leadFarmer->id)
                    ->where('recipient_type', 'lead_farmer');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('lead_farmer.notifications', compact('notifications'));
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark single notification as read
     */
    public function markNotificationRead($id)
    {
        $user = Auth::user();

        $notification = Notification::where('id', $id)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere(function($q) use ($user) {
                        $q->where('related_id', $user->leadFarmer->id)
                            ->where('recipient_type', 'lead_farmer');
                    });
            })
            ->firstOrFail();

        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }


    /**
     * Get Farmer Details (AJAX)
     */
    public function getFarmerDetails($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $farmer = Farmer::withCount(['products' => function($query) {
                $query->where('is_available', true);
            }])
            ->with('user') // Eager load the user relationship
            ->where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        // Determine profile photo URL
        $profilePhotoUrl = null;
        if ($farmer->user && $farmer->user->profile_photo) {
            $photoPath = 'uploads/profile_pictures/' . $farmer->user->profile_photo;

            // Check if file exists before generating URL
            if (file_exists(public_path($photoPath))) {
                $profilePhotoUrl = asset($photoPath);
            } else {
                $profilePhotoUrl = asset('uploads/profile_pictures/default-avatar.png');
            }
        } else {
            $profilePhotoUrl = asset('uploads/profile_pictures/default-avatar.png');
        }

        return response()->json([
            'success' => true,
            'farmer' => [
                'id' => $farmer->id,
                'name' => $farmer->name,
                'nic_no' => $farmer->nic_no,
                'primary_mobile' => $farmer->primary_mobile,
                'whatsapp_number' => $farmer->whatsapp_number,
                'email' => $farmer->email,
                'residential_address' => $farmer->residential_address,
                'address_map_link' => $farmer->address_map_link,
                'district' => $farmer->district,
                'grama_niladhari_division' => $farmer->grama_niladhari_division,
                'preferred_payment' => $farmer->preferred_payment,
                'payment_details' => $farmer->payment_details,
                'is_active' => $farmer->is_active,
                'profile_photo_url' => $profilePhotoUrl,
                'products_count' => $farmer->products()->count(),
                'active_products_count' => $farmer->products()->where('is_available', true)->count(),
                'updated_at_formatted' => $farmer->updated_at->format('Y-m-d H:i'),
            ]
        ]);
    }

    /**
     * Edit Farmer Page
     */
    public function editFarmer($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $farmer = Farmer::where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        $districts = [
            'Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo',
            'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara',
            'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar',
            'Matale', 'Matara', 'Monaragala', 'Mullaitivu', 'Nuwara Eliya',
            'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'
        ];

        // Parse payment details
        $paymentDetails = [];
        try {
            $paymentDetails = json_decode($farmer->payment_details, true) ?? [];
        } catch (\Exception $e) {
            $paymentDetails = [];
        }

        return view('lead_farmer.edit_farmer', compact('farmer', 'districts', 'paymentDetails'));
    }

    /**
     * Update Farmer
     */
    public function updateFarmer(Request $request, $id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $farmer = Farmer::where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'nic_no' => 'required|string|max:20|unique:farmers,nic_no,' . $farmer->id,
            'primary_mobile' => 'required|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'residential_address' => 'required|string',
            'address_map_link' => 'nullable|url',
            'district' => 'required|string',
            'grama_niladhari_division' => 'required|string|max:100',
            'preferred_payment' => 'required|in:bank,ezcash,mcash,all',
            'profile_photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update user email if changed
            $user = $farmer->user;
            if ($request->email != $user->email) {
                $user->email = $request->email;
                $user->save();
            }

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($user->profile_photo && $user->profile_photo != 'default-avatar.png') {
                    Storage::delete('public/uploads/profile_pictures/' . $user->profile_photo);
                }

                $photo = $request->file('profile_photo');
                $filename = 'farmer_' . time() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('public/uploads/profile_pictures', $filename);

                $user->profile_photo = $filename;
                $user->save();
            }

            // Update farmer record
            $farmer->update([
                'name' => $request->name,
                'nic_no' => $request->nic_no,
                'primary_mobile' => $request->primary_mobile,
                'whatsapp_number' => $request->whatsapp_number,
                'email' => $request->email,
                'residential_address' => $request->residential_address,
                'address_map_link' => $request->address_map_link,
                'district' => $request->district,
                'grama_niladhari_division' => $request->grama_niladhari_division,
                'preferred_payment' => $request->preferred_payment,
                'payment_details' => $this->formatPaymentDetails($request),
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'ezcash_mobile' => $request->ezcash_mobile,
                'mcash_mobile' => $request->mcash_mobile,
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();

            return redirect()->route('lf.manageFarmers')
                ->with('success', 'Farmer updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error updating farmer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete Farmer
     */
    public function deleteFarmer($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $farmer = Farmer::where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Delete user account
            if ($farmer->user) {
                // Delete profile photo if exists
                if ($farmer->user->profile_photo && $farmer->user->profile_photo != 'default-avatar.png') {
                    Storage::delete('public/uploads/profile_pictures/' . $farmer->user->profile_photo);
                }

                $farmer->user->delete();
            }

            // Delete farmer record (cascade will delete products)
            $farmer->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Farmer deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting farmer: ' . $e->getMessage()
            ], 500);
        }
    }

}



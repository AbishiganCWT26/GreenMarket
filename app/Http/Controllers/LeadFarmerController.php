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
use App\Models\ProductExample;
use App\Models\OtpVerification;
use App\Models\Payment;
use App\Mail\FarmerRegistrationMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadFarmerController extends Controller
{
    public function dashboard()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;
        $userId = Auth::id();

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

    public function storeFarmer(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'nic_no' => 'required|string|max:20|unique:farmers,nic_no',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'primary_mobile' => 'required|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'residential_address' => 'required|string',
            'address_map_link' => 'required|url',
            'district' => 'required|string',
            'grama_niladhari_division' => 'required|string|max:100',
            'preferred_payment' => 'required|in:bank,ezcash,mcash,all',
            'profile_photo' => 'nullable|image|max:5120',
        ];

        if (in_array($request->preferred_payment, ['bank', 'all'])) {
            $rules['bank_name'] = 'required|string|max:100';
            $rules['bank_branch'] = 'required|string|max:100';
            $rules['account_holder_name'] = 'required|string|max:100';
            $rules['account_number'] = 'required|string|max:50';
        }
        if (in_array($request->preferred_payment, ['ezcash', 'all'])) {
            $rules['ezcash_mobile'] = 'required|string|max:15';
        }
        if (in_array($request->preferred_payment, ['mcash', 'all'])) {
            $rules['mcash_mobile'] = 'required|string|max:15';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'role' => 'farmer',
                'is_active' => true,
            ]);

            $profilePhoto = 'default-avatar.png';
            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');
                $filename = 'farmer_' . $user->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('uploads/profile_pictures'), $filename);
                $profilePhoto = $filename;

                $user->profile_photo = $profilePhoto;
                $user->save();
            }

            $leadFarmerId = Auth::user()->leadFarmer->id;

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
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'ezcash_mobile' => $request->ezcash_mobile,
                'mcash_mobile' => $request->mcash_mobile,
            ]);

            DB::commit();

            try {
                $messageBody = "Welcome to " . config('app.name', 'GreenMarket') . "! \n";
                $messageBody .= "Your login details are:\n";
                $messageBody .= "Username: " . $request->username . "\n";
                $messageBody .= "Password: " . $request->password . "\n";
                
                $this->sendSMS($request->primary_mobile, $messageBody);

                if ($request->email) {
                    try {
                        Mail::to($request->email)->send(new FarmerRegistrationMail(
                            $request->name,
                            $request->username,
                            $request->password,
                            $request->email
                        ));
                    } catch (\Exception $e) {
                    }
                }

            } catch (\Exception $e) {
                return response()->json([
                    'success' => true,
                    'farmer' => $farmer,
                    'username' => $user->username,
                    'message' => 'Farmer registered successfully, but notification failed: ' . $e->getMessage()
                ]);
            }

            return response()->json([
                'success' => true,
                'farmer' => $farmer,
                'username' => $user->username,
                'message' => 'Farmer registered successfully! Notifications sent.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error registering farmer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function manageFarmers()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $query = Farmer::with(['user'])
            ->where('lead_farmer_id', $leadFarmerId);

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

    public function addProduct()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $farmers = Farmer::where('lead_farmer_id', $leadFarmerId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = ProductCategory::where('is_active', true)
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

        return view('lead_farmer.add_product', compact(
            'farmers',
            'categories',
            'units',
            'grades'
        ));
    }

    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:200',
            'product_description' => 'nullable|string',
            'product_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'farmer_id' => 'required|exists:farmers,id',
            'type_variant' => 'nullable|string|max:50',
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'product_examples_id' => 'required|exists:product_examples,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_of_measure' => 'required|string|max:20',
            'quality_grade' => 'nullable|string|max:50',
            'expected_availability_date' => 'required|date',
            'selling_price' => 'required|numeric|min:0.01',
            'pickup_address' => 'nullable|string',
            'pickup_map_link' => 'nullable|url',
            'is_available' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $leadFarmer = Auth::user()->leadFarmer;
            $leadFarmerId = $leadFarmer->id;

            $productPhoto = null;
            if ($request->hasFile('product_photo')) {
                $photo = $request->file('product_photo');
                $filename = 'product_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                
                $uploadPath = public_path('uploads/product_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $photo->move($uploadPath, $filename);
                $productPhoto = $filename;
            }

            $farmer = Farmer::find($request->farmer_id);
            
            $product = Product::create([
                'farmer_id' => $request->farmer_id,
                'lead_farmer_id' => $leadFarmerId,
                'product_name' => $request->product_name,
                'product_description' => $request->product_description,
                'product_photo' => $productPhoto,
                'type_variant' => $request->type_variant,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'product_examples_id' => $request->product_examples_id,
                'quantity' => $request->quantity,
                'unit_of_measure' => $request->unit_of_measure,
                'quality_grade' => $request->quality_grade,
                'expected_availability_date' => $request->expected_availability_date,
                'selling_price' => $request->selling_price,
                'pickup_address' => $request->pickup_address ?: $farmer->residential_address,
                'pickup_map_link' => $request->pickup_map_link ?: $farmer->address_map_link,
                'is_available' => $request->has('is_available') ? true : false,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product added successfully!',
                'product_id' => $product->id,
                'product_name' => $product->product_name
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Product creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error adding product: ' . $e->getMessage(),
                'error_details' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
    
    public function manageProducts(Request $request)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $query = Product::with(['farmer', 'category', 'subcategory'])
            ->where('lead_farmer_id', $leadFarmerId);

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
                $query->where('is_available', true)
                    ->where('quantity', '>', 0);
            } elseif ($request->status == 'sold_out') {
                $query->where(function($q) {
                    $q->where('is_available', false)
                    ->orWhere('quantity', '<=', 0);
                });
            }
        }

        $viewType = $request->input('view_type', 'card');
        $itemsPerPage = ($viewType === 'card') ? 15 : 10;

        $products = $query->orderBy('created_at', 'desc')->paginate($itemsPerPage);
        $products->appends($request->all());

        $farmers = Farmer::where('lead_farmer_id', $leadFarmerId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = ProductCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('lead_farmer.manage_products', compact('products', 'farmers', 'categories', 'viewType'));
    }

    public function getProductDetails($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $product = Product::with(['farmer', 'category', 'subcategory', 'productExample'])
            ->where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }

        $html = view('lead_farmer.partials.product_details', compact('product'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }
    
    public function editProduct($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $product = Product::with(['farmer', 'category', 'subcategory', 'productExample'])
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

        $productExamples = ProductExample::where('is_active', true)
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

        $currentFarmerMobile = $product->farmer->primary_mobile ?? '';

        $isLocked = false;
        if ($product->expected_availability_date) {
            $isLocked = Carbon::parse($product->expected_availability_date)->isPast();
        }

        return view('lead_farmer.edit_product', compact(
            'product',
            'farmers',
            'categories',
            'subcategories',
            'productExamples',
            'units',
            'grades',
            'currentFarmerMobile',
            'isLocked'
        ));
    }

    public function sendProductUpdateOtp(Request $request, $id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $product = Product::with(['farmer'])
            ->where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        $farmerMobile = $product->farmer->primary_mobile;

        if (!$farmerMobile) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer mobile number not found'
            ], 400);
        }

        try {
            $otp = rand(100000, 999999);
            
            \Log::info("Product Update OTP for Product {$id}: " . $otp);

            OtpVerification::create([
                'user_id' => Auth::id(),
                'otp' => $otp,
                'action' => 'update_product_' . $id,
                'expires_at' => Carbon::now()->addMinutes(10),
                'used' => false
            ]);

            $message = "Your GreenMarket OTP for updating product details is: $otp. This code is valid for 10 minutes. Please do not share this with anyone.";
            
            $smsSent = $this->sendSMS($farmerMobile, $message);

            if (!$smsSent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP via SMS'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to farmer\'s mobile number'
            ]);

        } catch (\Exception $e) {
            \Log::error('OTP Sending Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function verifyProductUpdateOtp(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP format'
            ], 400);
        }

        try {
            $otpRecord = OtpVerification::where('user_id', Auth::id())
                ->where('otp', $request->otp)
                ->where('action', 'update_product_' . $id)
                ->where('used', false)
                ->where('expires_at', '>', Carbon::now())
                ->latest()
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 400);
            }

            $otpRecord->update([
                'used' => true,
                'used_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error verifying OTP: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function updateProduct(Request $request, $id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $product = Product::with(['farmer'])
            ->where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:200',
            'product_description' => 'nullable|string',
            'product_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'farmer_id' => 'required|exists:farmers,id',
            'type_variant' => 'nullable|string|max:50',
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'product_examples_id' => 'required|exists:product_examples,id',
            'quantity' => 'required|numeric|min:0',
            'unit_of_measure' => 'required|string|max:20',
            'quality_grade' => 'nullable|string|max:50',
            'expected_availability_date' => 'required|date',
            'selling_price' => 'required|numeric|min:0',
            'pickup_address' => 'nullable|string',
            'pickup_map_link' => 'nullable|url',
            'is_available' => 'required|boolean',
            'otp_verified' => 'nullable|in:0,1',
            'otp_code' => 'nullable|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $isLocked = false;
        if ($product->expected_availability_date) {
            $isLocked = Carbon::parse($product->expected_availability_date)->isPast();
        }

        if ($isLocked) {
            $hasAvailabilityDateChanged = $request->expected_availability_date != 
                Carbon::parse($product->expected_availability_date)->format('Y-m-d');
            
            if (!$hasAvailabilityDateChanged) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product editing is locked. Only availability date can be changed.'
                ], 400);
            }
        }

        $sensitiveFields = [
            'farmer_id' => $product->farmer_id,
            'category_id' => $product->category_id,
            'subcategory_id' => $product->subcategory_id,
            'product_examples_id' => $product->product_examples_id,
            'selling_price' => $product->selling_price,
            'expected_availability_date' => $product->expected_availability_date ? 
                Carbon::parse($product->expected_availability_date)->format('Y-m-d') : null
        ];

        $hasSensitiveChanges = false;
        foreach ($sensitiveFields as $field => $originalValue) {
            $newValue = $request->$field;
            if ($field === 'expected_availability_date' && $originalValue) {
                $originalValue = Carbon::parse($originalValue)->format('Y-m-d');
            }
            if ($newValue != $originalValue) {
                $hasSensitiveChanges = true;
                break;
            }
        }

        if ($hasSensitiveChanges) {
            if ($request->otp_verified != '1' || empty($request->otp_code)) {
                return response()->json([
                    'success' => false,
                    'requires_otp' => true,
                    'message' => 'OTP verification required for sensitive field changes'
                ], 403);
            }

            $otpRecord = OtpVerification::where('user_id', Auth::id())
                ->where('otp', $request->otp_code)
                ->where('action', 'update_product_' . $id)
                ->where('used', true)
                ->where('used_at', '>', Carbon::now()->subMinutes(10))
                ->latest()
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            $productPhoto = $product->product_photo;

            if ($request->hasFile('product_photo')) {
                $photo = $request->file('product_photo');
                $filename = 'product_' . $id . '_' . time() . '.' . $photo->getClientOriginalExtension();
                
                $uploadPath = public_path('uploads/product_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                if ($productPhoto && $productPhoto != 'product-placeholder.png') {
                    $oldPhotoPath = public_path('uploads/product_images/' . $productPhoto);
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                
                $photo->move($uploadPath, $filename);
                $productPhoto = $filename;
            }

            $product->update([
                'product_name' => $request->product_name,
                'product_description' => $request->product_description,
                'product_photo' => $productPhoto,
                'farmer_id' => $request->farmer_id,
                'type_variant' => $request->type_variant,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'product_examples_id' => $request->product_examples_id,
                'quantity' => $request->quantity,
                'unit_of_measure' => $request->unit_of_measure,
                'quality_grade' => $request->quality_grade,
                'expected_availability_date' => $request->expected_availability_date,
                'selling_price' => $request->selling_price,
                'pickup_address' => $request->pickup_address,
                'pickup_map_link' => $request->pickup_map_link,
                'is_available' => $request->is_available,
                'updated_at' => Carbon::now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully!',
                'product_name' => $product->product_name
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Product update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProduct($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $product = Product::where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        try {
            if ($product->product_photo && $product->product_photo != 'product-placeholder.png') {
                $photoPath = public_path('uploads/product_images/' . $product->product_photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
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

    public function editProfile()
    {
        $user = Auth::user();
        $leadFarmer = $user->leadFarmer;

        return view('lead_farmer.profile', compact('user', 'leadFarmer'));
    }

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
            'account_holder_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
            'bank_branch' => 'required|string|max:100',
            'payment_details' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            if ($request->email != $user->email) {
                $user->email = $request->email;
                $user->save();
            }

            $leadFarmer->update([
                'name' => $request->name,
                'nic_no' => $request->nic_no,
                'primary_mobile' => $request->primary_mobile,
                'whatsapp_number' => $request->whatsapp_number,
                'residential_address' => $request->residential_address,
                'grama_niladhari_division' => $request->grama_niladhari_division,
                'preferred_payment' => 'bank',
                'payment_details' => $request->payment_details,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
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

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('Password must contain at least one uppercase letter.');
                    }
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('Password must contain at least one number.');
                    }
                    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
                        $fail('Password must contain at least one special character.');
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating password: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showPhotoForm()
    {
        $user = Auth::user();
        return view('lead_farmer.profile_photo', compact('user'));
    }

    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $photo = $request->file('profile_photo');
            $filename = 'lead_farmer_' . $user->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            
            $uploadPath = public_path('uploads/profile_pictures');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if ($user->profile_photo && $user->profile_photo != 'default-avatar.png') {
                $oldPhotoPath = public_path('uploads/profile_pictures/' . $user->profile_photo);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }
            
            $photo->move($uploadPath, $filename);
            
            $user->profile_photo = $filename;
            $user->save();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Profile photo updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error updating profile photo: ' . $e->getMessage());
        }
    }

    public function getSubcategories($categoryId)
    {
        $subcategories = ProductSubcategory::where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return response()->json($subcategories);
    }

    public function getProductExamples($subcategoryId)
    {
        $products = ProductExample::where('subcategory_id', $subcategoryId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return response()->json($products);
    }

    public function viewOrder($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $order = Order::with(['buyer', 'farmer', 'orderItems.product', 'payments'])
            ->where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        return view('lead_farmer.order_details', compact('order'));
    }

    private function formatPaymentDetails(Request $request)
    {
        if ($request->preferred_payment == 'bank') {
            return "Bank Transfer";
        } elseif ($request->preferred_payment == 'ezcash' || $request->preferred_payment == 'mcash') {
            return "Mobile Wallet Transfer";
        } elseif ($request->preferred_payment == 'all') {
            return "All Methods";
        }
        
        return "Bank Transfer";
    }

    public function salesReports()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

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

    public function markAllNotificationsRead()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

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

    public function getFarmerDetails($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $farmer = Farmer::withCount(['products' => function($query) {
                $query->where('is_available', true);
            }])
            ->with('user')
            ->where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        $profilePhotoUrl = null;
        if ($farmer->user && $farmer->user->profile_photo) {
            $photoPath = 'uploads/profile_pictures/' . $farmer->user->profile_photo;

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
                'username' => $farmer->user->username ?? null,
                'primary_mobile' => $farmer->primary_mobile,
                'whatsapp_number' => $farmer->whatsapp_number,
                'email' => $farmer->email,
                'residential_address' => $farmer->residential_address,
                'address_map_link' => $farmer->address_map_link,
                'district' => $farmer->district,
                'grama_niladhari_division' => $farmer->grama_niladhari_division,
                'preferred_payment' => $farmer->preferred_payment,
                'payment_details' => $farmer->payment_details,
                'bank_name' => $farmer->bank_name,
                'bank_branch' => $farmer->bank_branch,
                'account_holder_name' => $farmer->account_holder_name,
                'account_number' => $farmer->account_number,
                'ezcash_mobile' => $farmer->ezcash_mobile,
                'mcash_mobile' => $farmer->mcash_mobile,
                'is_active' => $farmer->is_active,
                'profile_photo_url' => $profilePhotoUrl,
                'products_count' => $farmer->products()->count(),
                'active_products_count' => $farmer->products()->where('is_available', true)->count(),
                'updated_at_formatted' => $farmer->updated_at->format('Y-m-d H:i'),
            ]
        ]);
    }

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

        $paymentDetails = [];
        try {
            $paymentDetails = json_decode($farmer->payment_details, true) ?? [];
        } catch (\Exception $e) {
            $paymentDetails = [];
        }

        return view('lead_farmer.edit_farmer', compact('farmer', 'districts', 'paymentDetails'));
    }

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
            'profile_photo' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $hasSensitiveChanges = $this->hasSensitiveChanges($request, $farmer);

        if ($hasSensitiveChanges) {
            if (!$request->has('otp')) {
                return response()->json([
                    'success' => false,
                    'requires_otp' => true,
                    'message' => 'Sensitive information change detected. OTP verification required.'
                ]);
            }

            $otpRecord = OtpVerification::where('user_id', Auth::id())
                ->where('otp', $request->otp)
                ->where('action', 'update_farmer_' . $id)
                ->where('used', false)
                ->where('expires_at', '>', Carbon::now())
                ->latest()
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP.'
                ], 400);
            }

            $otpRecord->update([
                'used' => true,
                'used_at' => Carbon::now()
            ]);
        }

        DB::beginTransaction();
        try {
            $user = $farmer->user;
            if ($request->email != $user->email) {
                $user->email = $request->email;
                $user->save();
            }

            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo && $user->profile_photo != 'default-avatar.png') {
                    Storage::delete('public/uploads/profile_pictures/' . $user->profile_photo);
                }

                $photo = $request->file('profile_photo');
                $filename = 'farmer_' . time() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('public/uploads/profile_pictures', $filename);

                $user->profile_photo = $filename;
                $user->save();
            }

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

            return response()->json([
                'success' => true,
                'message' => 'Farmer updated successfully!',
                'farmer' => $farmer
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating farmer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendUpdateOtp($id, Request $request)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;
        $farmer = Farmer::where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        if (!$this->hasSensitiveChanges($request, $farmer)) {
            return response()->json([
                'success' => false,
                'message' => 'No sensitive changes detected.'
            ], 400);
        }

        try {
            $otp = rand(100000, 999999);
            
            \Log::info("Farmer Update OTP for User " . Auth::id() . ": " . $otp);

            OtpVerification::create([
                'user_id' => Auth::id(),
                'otp' => $otp,
                'action' => 'update_farmer_' . $id,
                'expires_at' => Carbon::now()->addMinutes(10),
                'used' => false
            ]);

            $mobileNumber = $farmer->primary_mobile;
            $message = "Your GreenMarket OTP for updating your Mobile Number/Payment Info is: $otp. This code is valid for 10 minutes. Please do not share this with anyone.";
            
            $this->sendSMS($mobileNumber, $message);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your mobile number.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    private function hasSensitiveChanges(Request $request, $farmer)
    {
        if ($request->primary_mobile !== $farmer->primary_mobile) return true;
        if ($request->preferred_payment !== $farmer->preferred_payment) return true;
        if ($request->bank_name !== $farmer->bank_name) return true;
        if ($request->bank_branch !== $farmer->bank_branch) return true;
        if ($request->account_holder_name !== $farmer->account_holder_name) return true;
        if ($request->account_number !== $farmer->account_number) return true;
        if ($request->ezcash_mobile !== $farmer->ezcash_mobile) return true;
        if ($request->mcash_mobile !== $farmer->mcash_mobile) return true;
        return false;
    }

    public function deleteFarmer($id)
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $farmer = Farmer::where('id', $id)
            ->where('lead_farmer_id', $leadFarmerId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $farmer->is_active = false;
            $farmer->save();

            $farmer->products()->update([
                'product_status' => 'removed by lead farmer',
                'is_available' => false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Farmer deactivated successfully! All products have been marked as removed.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deactivating farmer: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendSMS($to, $message)
    {
        try {
            $user = env('SMS_USER');
            $password = env('SMS_PASSWORD');
            $baseurl = env('SMS_API_URL', 'https://textit.biz/sendmsg');

            $to = preg_replace('/[^0-9]/', '', $to);
            $text = urlencode($message);
            
            $baseurl = rtrim($baseurl, '/') . '/';
            $url = $baseurl . "?id=" . $user . "&pw=" . $password . "&to=" . $to . "&text=" . $text;
            
            $ret = $this->get_web_page($url);
            $res = explode(":", $ret);
            
            if (trim($res[0]) == "OK") {
                \Log::info("SMS Sent successfully to $to. Response: $ret");
                return true;
            } else {
                \Log::error("SMS Sending Failed to $to. Response: $ret");
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('SMS Error: ' . $e->getMessage());
            return false;
        }
    }

    private function get_web_page($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
    }

    public function viewOrders()
    {
        $leadFarmerId = Auth::user()->leadFarmer->id;

        $orders = Order::with(['buyer', 'farmer', 'orderItems', 'payments'])
            ->where('lead_farmer_id', $leadFarmerId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('lead_farmer.view_orders', compact('orders'));
    }

    public function markPaymentReceived(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cash,bank,mobile_wallet',
            'transaction_number' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $leadFarmerId = Auth::user()->leadFarmer->id;

        DB::beginTransaction();
        try {
            $order = Order::where('id', $request->order_id)
                ->where('lead_farmer_id', $leadFarmerId)
                ->firstOrFail();

            if ($order->order_status == 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot mark payment for cancelled order'
                ], 400);
            }

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_reference' => 'PAY-' . time() . '-' . $order->id,
                'amount' => $order->total_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'completed',
                'payment_date' => now(),
                'transaction_id' => $request->transaction_number,
                'receipt_url' => null
            ]);

            $order->order_status = 'paid';
            $order->paid_date = now();
            $order->save();

            foreach ($order->orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity = max(0, $product->quantity - $item->quantity_ordered);
                    if ($product->quantity <= 0) {
                        $product->is_available = false;
                    }
                    $product->save();
                }
            }

            Notification::create([
                'user_id' => $order->buyer->user_id,
                'recipient_type' => 'buyer',
                'title' => 'Payment Confirmed',
                'message' => "Your payment for order #{$order->order_number} has been confirmed by the lead farmer.",
                'notification_type' => 'payment_confirmation',
                'related_id' => $order->id,
                'is_read' => false
            ]);

            if ($order->farmer && $order->farmer->user) {
                Notification::create([
                    'user_id' => $order->farmer->user_id,
                    'recipient_type' => 'farmer',
                    'title' => 'Payment Received',
                    'message' => "Payment received for your products in order #{$order->order_number}. Please prepare the products for pickup.",
                    'notification_type' => 'payment_received',
                    'related_id' => $order->id,
                    'is_read' => false
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment marked as received successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error marking payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateOrderStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:confirmed,ready_for_pickup,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $leadFarmerId = Auth::user()->leadFarmer->id;

        try {
            $order = Order::where('id', $request->order_id)
                ->where('lead_farmer_id', $leadFarmerId)
                ->firstOrFail();

            $oldStatus = $order->order_status;
            $order->order_status = $request->status;

            if ($request->status == 'completed' && !$order->completed_date) {
                $order->completed_date = now();
            } elseif ($request->status == 'ready_for_pickup') {
                Notification::create([
                    'user_id' => $order->buyer->user_id,
                    'recipient_type' => 'buyer',
                    'title' => 'Ready for Pickup',
                    'message' => "Your order #{$order->order_number} is ready for pickup. Please contact the lead farmer to arrange pickup.",
                    'notification_type' => 'ready_for_pickup',
                    'related_id' => $order->id,
                    'is_read' => false
                ]);
            }

            $order->save();

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

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating order status: ' . $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Farmer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\LeadFarmer;
use App\Models\BuyerProductRequest;
use App\Models\InventoryLog;
use App\Models\ProductCategory;
use App\Services\InventoryService;

class FarmerController extends Controller
{
    /**
     * Share counts with all views
     */
    /**
     * Share counts with all views automatically
     */
    public function __construct()
    {
        // View Composer handles sharing counts and notifications
    }

    // Profile Settings Page
    public function profileSettings()
    {
        $user = Auth::user();

        return view('farmer.profile.settings', compact('user'));
    }

    // Update password (changed from private to public)
    public function update(Request $request)
    {
        // Debug: Check what's being received
        \Log::info('Update request received:', [
            'action' => $request->input('action'),
            'all_data' => $request->all()
        ]);

        // Temporarily remove the action check to see if the password validation works
        // if ($request->input('action') !== 'change_password') {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Invalid action specified.'
        //     ], 400);
        // }

        $validator = Validator::make($request->all(), [
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find(Auth::id());

        $user->password = Hash::make($request->new_password);
        $user->save();

        $farmer = Farmer::where('user_id', $user->id)->first();
        if ($farmer && $farmer->primary_mobile) {
            $message = "You update you password.\nYour login details are:\nusername :- {$user->username}\nPassword :- {$request->new_password}";
            $this->sendSMS($farmer->primary_mobile, $message);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    }

    // You can remove this method if not used
    public function updateSettings(Request $request)
    {
        if ($request->input('action') === 'change_password') {
            return $this->updatePassword($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid action specified.'
        ], 400);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find(Auth::id());

        $user->password = Hash::make($request->new_password);
        $user->save();

        $farmer = Farmer::where('user_id', $user->id)->first();
        if ($farmer && $farmer->primary_mobile) {
            $message = "You update you password.\nYour login details are:\nusername :- {$user->username}\nPassword :- {$request->new_password}";
            $this->sendSMS($farmer->primary_mobile, $message);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    }

    private function sendSMS($phone, $message)
    {
        try {
            $smsUser = env('SMS_USER', 'number');
            $smsPassword = env('SMS_PASSWORD', '0000');
            $text = urlencode($message);
            $to = $phone;
            $baseurl = env('SMS_API_URL', 'https://textit.biz/sendmsg');
            $url = "$baseurl/?id=$smsUser&pw=$smsPassword&to=$to&text=$text";
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

    public function dashboard()
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        $pendingPickups = Order::where('farmer_id', $farmer->id)
            ->where('order_status', 'ready_for_pickup')
            ->count();

        $recentOrders = Order::where('farmer_id', $farmer->id)
            ->with(['buyer', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $lowStockProducts = [];
        $allProducts = Product::where('farmer_id', $farmer->id)
            ->where('product_status', '!=', 'removed')
            ->where('quantity', '>', 0)
            ->get();

        foreach ($allProducts as $product) {
            $totalOrdered = OrderItem::where('product_id', $product->id)
                ->whereHas('order', function($q) {
                    $q->whereNotIn('order_status', ['cancelled', 'refunded']);
                })
                ->sum('quantity_ordered');

            $availableQuantity = $product->quantity - $totalOrdered;

            if ($availableQuantity < 10 && $availableQuantity > 0) {
                $lowStockProducts[] = [
                    'product' => $product,
                    'total_ordered' => $totalOrdered,
                    'available_quantity' => $availableQuantity
                ];
            }
        }

        $leadFarmerName = null;
        $leadFarmerGroup = null;
        if ($farmer->lead_farmer_id) {
            $leadFarmer = LeadFarmer::find($farmer->lead_farmer_id);
            if ($leadFarmer) {
                $leadFarmerName = $leadFarmer->name;
                $leadFarmerGroup = $leadFarmer->group_name;
            }
        }

        return view('farmer.dashboard', compact(
            'pendingPickups',
            'recentOrders',
            'lowStockProducts',
            'leadFarmerName',
            'leadFarmerGroup'
        ));
    }

    /**
     * Display payment settings page
     */
    public function paymentSettings()
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        // If farmer doesn't exist, create a basic record
        if (!$farmer) {
            $farmer = new Farmer();
            $farmer->user_id = $user->id;
            $farmer->name = $user->username;
            $farmer->save();
        }

        return view('farmer.profile.Payment', compact('farmer', 'user'));
    }

    /**
     * Update payment settings
     */
    public function updatePaymentSettings(Request $request)
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found.'
            ], 404);
        }

        // Validate current password first
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => ['current_password' => ['The provided password does not match your current password.']]
            ], 422);
        }

        // Get the preferred payment method
        $preferredPayment = $request->preferred_payment;

        // Initialize update data array
        $updateData = [
            'preferred_payment' => $preferredPayment,
            'payment_details' => $request->payment_details,
        ];

        // Validate based on selected payment method
        $validationRules = [];

        switch ($preferredPayment) {
            case 'bank':
                $validationRules = [
                    'account_holder_name' => 'required|string|max:100',
                    'account_number' => 'required|string|max:50',
                    'bank_name' => 'required|string|max:100',
                    'bank_branch' => 'required|string|max:100',
                ];

                // Add bank details to update data
                $updateData['account_holder_name'] = $request->account_holder_name;
                $updateData['account_number'] = $request->account_number;
                $updateData['bank_name'] = $request->bank_name;
                $updateData['bank_branch'] = $request->bank_branch;

                // Clear mobile payment fields
                $updateData['ezcash_mobile'] = null;
                $updateData['mcash_mobile'] = null;
                break;

            case 'ezcash':
                $validationRules = [
                    'ezcash_mobile' => 'required|string|max:15|regex:/^07[0-9]{8}$/',
                ];

                // Add eZ Cash details to update data
                $updateData['ezcash_mobile'] = $request->ezcash_mobile;

                // Clear other fields
                $updateData['account_holder_name'] = null;
                $updateData['account_number'] = null;
                $updateData['bank_name'] = null;
                $updateData['bank_branch'] = null;
                $updateData['mcash_mobile'] = null;
                break;

            case 'mcash':
                $validationRules = [
                    'mcash_mobile' => 'required|string|max:15|regex:/^07[0-9]{8}$/',
                ];

                // Add mCash details to update data
                $updateData['mcash_mobile'] = $request->mcash_mobile;

                // Clear other fields
                $updateData['account_holder_name'] = null;
                $updateData['account_number'] = null;
                $updateData['bank_name'] = null;
                $updateData['bank_branch'] = null;
                $updateData['ezcash_mobile'] = null;
                break;

            case 'all':
                $validationRules = [
                    'account_holder_name' => 'required|string|max:100',
                    'account_number' => 'required|string|max:50',
                    'bank_name' => 'required|string|max:100',
                    'bank_branch' => 'required|string|max:100',
                    'ezcash_mobile' => 'required|string|max:15|regex:/^07[0-9]{8}$/',
                    'mcash_mobile' => 'required|string|max:15|regex:/^07[0-9]{8}$/',
                ];

                // Add all details to update data
                // Note: Using the same field names for all methods
                $updateData['account_holder_name'] = $request->account_holder_name;
                $updateData['account_number'] = $request->account_number;
                $updateData['bank_name'] = $request->bank_name;
                $updateData['bank_branch'] = $request->bank_branch;
                $updateData['ezcash_mobile'] = $request->ezcash_mobile;
                $updateData['mcash_mobile'] = $request->mcash_mobile;
                break;
        }

        // Validate the request
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update farmer's payment settings
            $farmer->update($updateData);

            // Log the update
            \Log::info('Payment settings updated for farmer:', [
                'farmer_id' => $farmer->id,
                'preferred_payment' => $preferredPayment,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment settings updated successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating payment settings:', [
                'error' => $e->getMessage(),
                'farmer_id' => $farmer->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment settings. Please try again.'
            ], 500);
        }
    }    public function getOrderDetails($id)
    {
        try {
            $order = Order::with([
                'buyer',
                'orderItems.product',
                'payment',
                'orderItems' => function($query) {
                    $query->with(['product' => function($q) {
                        $q->select(
                            'id',
                            'product_name',
                            'product_description',
                            'product_photo',
                            'quantity',
                            'unit_of_measure',
                            'quality_grade',
                            'pickup_address'
                        );
                    }]);
                }
            ])->findOrFail($id);

            $farmer = Farmer::where('user_id', Auth::id())->first();
            if ($order->farmer_id !== $farmer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            foreach ($order->orderItems as $orderItem) {
                if ($orderItem->product) {
                    $orderItem->formatted_quantity = number_format($orderItem->quantity_ordered, 2) . ' ' . $orderItem->product->unit_of_measure;

                    if ($orderItem->product->product_photo && file_exists(public_path('uploads/product_images/' . $orderItem->product->product_photo))) {
                        $orderItem->product_image = asset('uploads/product_images/' . $orderItem->product->product_photo);
                    } else {
                        $orderItem->product_image = asset('assets/images/product-placeholder.png');
                    }
                } else {
                    $orderItem->formatted_quantity = number_format($orderItem->quantity_ordered, 2) . ' units';
                    $orderItem->product_image = asset('assets/images/product-placeholder.png');
                }
            }

            $order->items_total = $order->orderItems->sum('item_total');
            $order->delivery_fee = $order->total_amount - $order->items_total;

            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }
    }

    public function markOrderReady($id)
    {

        try {
            $order = Order::findOrFail($id);

            $farmer = Farmer::where('user_id', Auth::id())->first();
            if ($order->farmer_id !== $farmer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            if ($order->order_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is not in paid status.'
                ]);
            }

            $order->update(['order_status' => 'ready_for_pickup']);

            Notification::create([
                'user_id' => $order->buyer->user_id,
                'recipient_type' => 'user',
                'title' => 'Order Ready for Pickup',
                'message' => 'Your order #' . $order->order_number . ' is ready for pickup from the farmer.',
                'notification_type' => 'ready_for_pickup',
                'related_id' => $order->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order marked as ready for pickup. Buyer has been notified.'
            ]);
        } catch (\Exception $e) {
            \Log::error('markOrderReady failed: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status.'
            ], 500);
        }
    }

    public function myProducts(Request $request)
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        $currentDate = Carbon::now()->format('Y-m-d');

        $query = Product::where('farmer_id', $farmer->id)
            ->with(['category', 'subcategory'])
            ->orderBy('created_at', 'desc');

        $filter = $request->get('filter', 'all');

        if ($filter !== 'all') {
            $query->where('product_status', '!=', 'removed');

            if ($filter === 'available') {
                $productIds = [];
                $products = Product::where('farmer_id', $farmer->id)
                    ->where('product_status', '!=', 'removed')
                    ->get();

                foreach ($products as $product) {
                    $totalOrdered = OrderItem::where('product_id', $product->id)
                        ->whereHas('order', function($q) {
                            $q->whereNotIn('order_status', ['cancelled', 'refunded']);
                        })
                        ->sum('quantity_ordered');

                    $availableQuantity = $product->quantity - $totalOrdered;

                    if ($availableQuantity > 0 &&
                        $product->expected_availability_date <= $currentDate) {
                        $productIds[] = $product->id;
                    }
                }

                $query->whereIn('id', $productIds);

            } elseif ($filter === 'sold') {
                $productIds = [];
                $products = Product::where('farmer_id', $farmer->id)
                    ->where('product_status', '!=', 'removed')
                    ->get();

                foreach ($products as $product) {
                    $totalOrdered = OrderItem::where('product_id', $product->id)
                        ->whereHas('order', function($q) {
                            $q->whereNotIn('order_status', ['cancelled', 'refunded']);
                        })
                        ->sum('quantity_ordered');

                    $availableQuantity = $product->quantity - $totalOrdered;

                    if (($availableQuantity <= 0 || $product->quantity <= 0) &&
                        $product->expected_availability_date <= $currentDate) {
                        $productIds[] = $product->id;
                    }
                }

                $query->whereIn('id', $productIds);

            } elseif ($filter === 'waiting') {
                $query->where('expected_availability_date', '>', $currentDate);
            }
        }

        $products = $query->paginate(12);

        $allProducts = Product::where('farmer_id', $farmer->id)->get();

        $productCount = $allProducts->count();

        $availableCount = 0;
        $soldCount = 0;
        $waitingCount = 0;

        foreach ($allProducts as $product) {
            if ($product->product_status === 'removed') {
                continue;
            }

            $totalOrdered = OrderItem::where('product_id', $product->id)
                ->whereHas('order', function($q) {
                    $q->whereNotIn('order_status', ['cancelled', 'refunded']);
                })
                ->sum('quantity_ordered');

            $availableQuantity = $product->quantity - $totalOrdered;

            if ($product->expected_availability_date > $currentDate) {
                $waitingCount++;
            }
            elseif ($availableQuantity > 0 && $product->expected_availability_date <= $currentDate) {
                $availableCount++;
            }
            elseif ($product->expected_availability_date <= $currentDate) {
                $soldCount++;
            }
        }

        $removedCount = Product::where('farmer_id', $farmer->id)
            ->where('product_status', 'removed')
            ->count();

        $leadFarmer = null;
        if ($farmer->lead_farmer_id) {
            $leadFarmer = LeadFarmer::find($farmer->lead_farmer_id);
        }

        return view('farmer.products.my-products', compact(
            'products',
            'productCount',
            'availableCount',
            'soldCount',
            'waitingCount',
            'removedCount',
            'leadFarmer',
            'filter'
        ));
    }

    public function removedProducts()
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        $removedProducts = Product::where('farmer_id', $farmer->id)
            ->where('product_status', '!=', 'have it')
            ->where('product_status', '!=', 'Have it')
            ->where(function($query) {
                $query->where('product_status', 'like', '%removed%')
                      ->orWhere('product_status', 'removed by lead farmer')
                      ->orWhere('product_status', 'removed by facilitator')
                      ->orWhere('product_status', 'removed by admin');
            })
            ->with(['category', 'subcategory'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $removedCount = $removedProducts->total();

        return view('farmer.products.removed', compact('removedProducts', 'removedCount'));
    }

    public function viewProduct($id)
    {
        try {
            $product = Product::with(['category', 'subcategory', 'farmer'])->findOrFail($id);

            $farmer = Farmer::where('user_id', Auth::id())->first();
            if ($product->farmer_id !== $farmer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            $leadFarmer = null;
            if ($farmer->lead_farmer_id) {
                $leadFarmer = LeadFarmer::find($farmer->lead_farmer_id);
            }

            return response()->json([
                'success' => true,
                'product' => $product,
                'lead_farmer' => $leadFarmer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }
    }

    public function activeOrders()
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        $query = Order::where('farmer_id', $farmer->id)
            ->whereIn('order_status', ['paid', 'ready_for_pickup']);

        $pendingOrders = $query->count();

        $orders = $query->with(['buyer', 'orderItems.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($orders as $order) {
            $order->items_total = $order->orderItems->sum('item_total');
            $order->delivery_fee = $order->total_amount - $order->orderItems->sum('item_total');
        }

        return view('farmer.orders.active', compact('orders', 'pendingOrders'));
    }

    public function orderHistory()
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        $orders = Order::where('farmer_id', $farmer->id)
            ->with(['buyer', 'orderItems.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($orders as $order) {
            if ($order->orderItems->isNotEmpty()) {
                $firstOrderItem = $order->orderItems->first();
                if ($firstOrderItem->product) {
                    $product = $firstOrderItem->product;
                    $order->product_name = $product->product_name;

                    $order->formatted_quantity = number_format($firstOrderItem->quantity_ordered, 2) . ' ' . $product->unit_of_measure;

                    if ($product->product_photo && file_exists(public_path('uploads/product_images/' . $product->product_photo))) {
                        $order->product_image = asset('uploads/product_images/' . $product->product_photo);
                    } else {
                        $order->product_image = asset('assets/images/product-placeholder.png');
                    }
                } else {
                    $order->product_name = $firstOrderItem->product_name_snapshot;
                    $order->formatted_quantity = number_format($firstOrderItem->quantity_ordered, 2) . ' units';
                    $order->product_image = asset('assets/images/product-placeholder.png');
                }
            } else {
                $order->product_name = 'N/A';
                $order->formatted_quantity = 'N/A';
                $order->product_image = asset('assets/images/product-placeholder.png');
            }
        }

        $totalRevenue = Order::where('farmer_id', $farmer->id)
            ->whereHas('payment', function($q) {
                $q->where('payment_status', 'completed');
            })
            ->sum('total_amount');

        $completedOrders = Order::where('farmer_id', $farmer->id)
            ->whereIn('order_status', ['delivered', 'completed', 'ready_for_pickup'])
            ->count();

        $cancelledOrders = Order::where('farmer_id', $farmer->id)
            ->where('order_status', 'cancelled')
            ->count();

        return view('farmer.orders.history', compact('orders', 'totalRevenue', 'completedOrders', 'cancelledOrders'));
    }

    public function createComplaint()
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        $leadFarmer = null;
        $leadFarmerUser = null;
        if ($farmer->lead_farmer_id) {
            $leadFarmer = LeadFarmer::find($farmer->lead_farmer_id);
            if ($leadFarmer && $leadFarmer->user_id) {
                $leadFarmerUser = User::find($leadFarmer->user_id);
                if ($leadFarmerUser) {
                    $leadFarmerUser->name = $leadFarmer->name;
                }
            }
        }

        $orders = Order::where('farmer_id', $farmer->id)
            ->with('buyer')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('farmer.complaints.create', compact('leadFarmer', 'leadFarmerUser', 'orders'));
    }

    private function getUserName($user)
    {
        switch($user->role) {
            case 'buyer':
                $buyer = \App\Models\Buyer::where('user_id', $user->id)->first();
                return $buyer ? $buyer->name : $user->username;
            case 'lead_farmer':
                $leadFarmer = \App\Models\LeadFarmer::where('user_id', $user->id)->first();
                return $leadFarmer ? $leadFarmer->name : $user->username;
            case 'facilitator':
                $facilitator = \App\Models\Facilitator::where('user_id', $user->id)->first();
                return $facilitator ? $facilitator->name : $user->username;
            default:
                return $user->username;
        }
    }

    public function storeComplaint(Request $request)
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        if (!$farmer) {
            return response()->json([
                'success' => false,
                'message' => 'Farmer profile not found.'
            ], 404);
        }

        $request->validate([
            'complaint_type' => 'required|in:payment_delay,payment_missing,wrong_data_entry,other',
            'against_user_id' => 'nullable|exists:users,id',
            'related_order_id' => 'nullable|exists:orders,id',
            'description' => 'required|string|min:10|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $complaint = Complaint::create([
                'complainant_user_id' => $user->id,
                'complainant_role' => 'farmer',
                'against_user_id' => $request->against_user_id,
                'related_order_id' => $request->related_order_id,
                'complaint_type' => $request->complaint_type,
                'description' => $request->description,
                'status' => 'new',
            ]);

            // Notify active admins
            $admins = User::where('role', 'admin')->where('is_active', true)->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'recipient_type' => 'user',
                    'title' => 'New Farmer Complaint',
                    'message' => 'Farmer ' . $farmer->name . ' (#' . $user->id . ') has filed a new complaint about ' . str_replace('_', ' ', $request->complaint_type) . '. Please review it.',
                    'notification_type' => 'admin_alert',
                    'related_id' => $complaint->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Complaint filed successfully. Our team will review it soon.',
                'complaint_id' => $complaint->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to file complaint. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function listComplaints()
    {
        $user = Auth::user();

        $complaints = Complaint::where('complainant_user_id', $user->id)
            ->with(['againstUserInfo', 'relatedOrder.buyer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $openComplaints = Complaint::where('complainant_user_id', $user->id)
            ->where('status', 'new')
            ->count();

        $inProgressComplaints = Complaint::where('complainant_user_id', $user->id)
            ->where('status', 'in_progress')
            ->count();

        $resolvedComplaints = Complaint::where('complainant_user_id', $user->id)
            ->where('status', 'resolved')
            ->count();

        $totalComplaints = $complaints->total();

        return view('farmer.complaints.list', compact(
            'complaints', 
            'openComplaints', 
            'inProgressComplaints', 
            'resolvedComplaints', 
            'totalComplaints'
        ));
    }

    public function viewComplaint($id)
    {
        try {
            $user = Auth::user();
            $complaint = Complaint::with(['againstUserInfo', 'relatedOrder.buyer', 'resolvedByFacilitator'])->findOrFail($id);

            if ($complaint->complainant_user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            $complaint->created_at_formatted = $complaint->created_at->format('M d, Y h:i A');
            $complaint->updated_at_formatted = $complaint->updated_at->format('M d, Y h:i A');

            if ($complaint->againstUserInfo) {
                $complaint->against_user_name = $this->getUserName($complaint->againstUserInfo);
                $complaint->against_user_role = $complaint->againstUserInfo->role;
            }

            return response()->json([
                'success' => true,
                'complaint' => $complaint
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found.'
            ], 404);
        }
    }

    public function deleteComplaint($id)
    {
        try {
            $user = Auth::user();
            $complaint = Complaint::findOrFail($id);

            if ($complaint->complainant_user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            DB::beginTransaction();

            $notification = Notification::where('related_id', $complaint->id)
                ->where('notification_type', 'admin_alert')
                ->first();

            if ($notification && $notification->is_read) {
                $notification->update([
                    'message' => 'Complaint #' . $complaint->id . ' filed by farmer has been deleted by the farmer.',
                    'updated_at' => now()
                ]);
            } else {
                Notification::where('related_id', $complaint->id)->delete();
            }

            $complaint->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Complaint deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete complaint.'
            ], 500);
        }
    }
    public function profile()
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        return view('farmer.profile.profile', compact('farmer', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:100',
            'primary_mobile' => 'required|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'residential_address' => 'required|string',
            'district' => 'required|string',
            'divisional_secretariat' => 'required|string',
            'grama_niladhari_division' => 'required|string|max:100',
            'gn_division_code' => 'nullable|string|max:20',
            'address_map_link' => 'nullable|url',
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
        ]);

        $farmer->update([
            'name' => $request->name,
            'primary_mobile' => $request->primary_mobile,
            'whatsapp_number' => $request->whatsapp_number,
            'email' => $request->email,
            'residential_address' => $request->residential_address,
            'district' => $request->district,
            'divisional_secretariat' => $request->divisional_secretariat,
            'grama_niladhari_division' => $request->grama_niladhari_division,
            'gn_division_code' => $request->gn_division_code,
            'address_map_link' => $request->address_map_link,
        ]);

        return redirect()->route('farmer.profile.profile')
            ->with('success', 'Profile updated successfully!');
    }

    public function profilePhoto()
    {
        $user = Auth::user();

        return view('farmer.profile.photo', compact('user'));
    }

    public function updateProfilePhoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Delete old photo if exists and not the farmer-icon
        if ($user->profile_photo && $user->profile_photo !== 'farmer-icon.svg') {
            $oldFilePath = public_path('uploads/profile_pictures/' . $user->profile_photo);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }

        $photo = $request->file('profile_photo');
        $photoName = 'farmer_' . $user->id . '_' . time() . '.' . $photo->getClientOriginalExtension();

        // Define upload path
        $uploadPath = public_path('uploads/profile_pictures');

        // Create directory if it doesn't exist
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Move uploaded file
        $photo->move($uploadPath, $photoName);

        // Update user record with new photo name
        $user->update([
            'profile_photo' => $photoName,
        ]);

        return redirect()->route('farmer.profile.photo')
            ->with('success', 'Profile photo updated successfully!');
    }

    public function deleteProfilePhoto()
    {
        $user = Auth::user();

        // Check if user has a custom profile photo
        if ($user->profile_photo && $user->profile_photo !== 'farmer-icon.svg') {
            // Delete the photo from storage if it exists
            $filePath = public_path('uploads/profile_pictures/' . $user->profile_photo);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        // Update database to use farmer-icon.svg
        $user->update([
            'profile_photo' => 'farmer-icon.svg',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile photo removed successfully! Default farmer icon restored.'
        ]);
    }

    public function notifications()
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->orWhere('recipient_type', 'system_wide')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('farmer.notifications', compact('notifications'));
    }

    public function markNotificationRead(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'id' => 'required|exists:notifications,id',
        ]);

        $notification = Notification::where('id', $request->id)
            ->where('user_id', $user->id)
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function viewProductRequests()
    {
        $user = Auth::user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        $requests = DB::table('buyer_product_requests')
            ->where('status', 'active')
            ->orderBy('needed_date', 'asc')
            ->get();

        return view('farmer.product_requests.index', [
            'requests' => $requests
        ]);
    }

    public function getRequestDetails($id)
    {
        $request = DB::table('buyer_product_requests')
            ->where('id', $id)
            ->where('status', 'active')
            ->first();

        if (!$request) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found or no longer active'
            ], 404);
        }

        $buyer = DB::table('buyers')
            ->where('id', $request->buyer_id)
            ->first();

        $imageUrl = $request->product_image
            ? asset('uploads/buyer_product_requests/' . $request->product_image)
            : null;

        return response()->json([
            'success' => true,
            'request' => (object)[
                'id' => $request->id,
                'product_name' => $request->product_name,
                'product_image' => $request->product_image,
                'image_url' => $imageUrl,
                'needed_quantity' => $request->needed_quantity,
                'unit_of_measure' => $request->unit_of_measure,
                'needed_date' => $request->needed_date,
                'unit_price' => $request->unit_price,
                'description' => $request->description,
                'created_at' => $request->created_at,
                'status' => $request->status
            ],
            'buyer' => $buyer
        ]);
    }

    public function myInventory()
    {
        $farmerId = Auth::user()->farmer->id;
        
        $products = Product::where('farmer_id', $farmerId)
            ->with(['category', 'subcategory'])
            ->get();
            
        $logs = InventoryLog::whereHas('product', function($q) use ($farmerId) {
                $q->where('farmer_id', $farmerId);
            })
            ->with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $categories = ProductCategory::where('is_active', true)->get();

        // Calculate statistics
        $totalProducts = $products->count();
        $totalInventoryValue = $products->sum(function($p) {
            return $p->quantity * $p->selling_price;
        });
        
        $lowStockCount = $products->filter(function($p) {
            return $p->inventory_status == 'Low Stock' || $p->inventory_status == 'Critical';
        })->count();
        
        $outOfStockCount = $products->filter(function($p) {
            return $p->inventory_status == 'Out of Stock';
        })->count();

        return view('farmer.inventory.Inventory-Dashboard', compact(
            'products',
            'logs',
            'categories',
            'totalProducts',
            'totalInventoryValue',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    public function inventoryList(Request $request)
    {
        $farmerId = Auth::user()->farmer->id;
        $query = Product::with(['category', 'subcategory'])
            ->where('farmer_id', $farmerId);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(15);
        
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $products->setCollection(
                $products->getCollection()->filter(function ($product) use ($statusFilter) {
                    return $product->inventory_status === $statusFilter;
                })
            );
        }

        return response()->json([
            'success' => true,
            'html' => view('farmer.inventory.partials.product_list', compact('products'))->render()
        ]);
    }

    public function inventoryProductsPdf(Request $request)
    {
        $farmerId = Auth::user()->farmer->id;
        $query = Product::with(['category', 'subcategory'])
            ->where('farmer_id', $farmerId);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();
        
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $products = $products->filter(function ($product) use ($statusFilter) {
                return $product->inventory_status === $statusFilter;
            });
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('farmer.inventory.Inventory-Products_pdf', compact('products'));
        return $pdf->download('My-Product-Inventory-' . date('Y-m-d') . '.pdf');
    }

    public function inventoryMovementLogsPdf(Request $request)
    {
        $farmerId = Auth::user()->farmer->id;
        
        $query = InventoryLog::whereHas('product', function($q) use ($farmerId) {
                $q->where('farmer_id', $farmerId);
            })
            ->with(['product', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('farmer.inventory.Inventory-Movement-Logs_pdf', compact('logs'));
        return $pdf->download('My-Inventory-Movements-' . date('Y-m-d') . '.pdf');
    }
}

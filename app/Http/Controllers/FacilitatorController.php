<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Facilitator;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Models\ProductExample;
use App\Models\SystemStandard;
use App\Models\Complaint;
use App\Models\Notification;
use Carbon\Carbon;

class FacilitatorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $facilitator = $user->facilitator;

        $totalCategories = ProductCategory::where('is_active', true)->count();
        $totalUsers = User::where('is_active', true)->count();
        $pendingComplaints = Complaint::where('status', 'new')->count();

        $systemStandards = [
            'units' => SystemStandard::where('standard_type', 'unit_of_measure')->where('is_active', true)->count(),
            'grades' => SystemStandard::where('standard_type', 'quality_grade')->where('is_active', true)->count()
        ];

        $recentActivities = Notification::whereIn('notification_type', ['system', 'admin_alert'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $totalNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        $sharedCounts = [
            'pendingComplaints' => $pendingComplaints,
            'totalNotifications' => $totalNotifications
        ];

        return view('facilitator.dashboard', compact(
            'facilitator',
            'totalCategories',
            'totalUsers',
            'pendingComplaints',
            'systemStandards',
            'recentActivities',
            'sharedCounts'
        ));
    }

    public function taxonomyManagement()
    {
        $categories = ProductCategory::with(['subcategories' => function($query) {
            $query->orderBy('display_order');
        }, 'subcategories.productExamples' => function($query) {
            $query->orderBy('display_order');
        }])
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $units = SystemStandard::where('standard_type', 'unit_of_measure')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $grades = SystemStandard::where('standard_type', 'quality_grade')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('facilitator.taxonomy', compact('categories', 'units', 'grades'));
    }

    // Update the storeCategory method
    public function storeCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required|string|max:100|unique:product_categories,category_name',
                'description' => 'nullable|string'
            ]);

            // Get the next display order
            $maxOrder = ProductCategory::max('display_order') ?? 0;

            $category = ProductCategory::create([
                'category_name' => $validated['category_name'],
                'description' => $validated['description'] ?? null,
                'display_order' => $maxOrder + 1,
                'is_active' => true,
                'created_at' => now(),
                'created_by_user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating category: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update the updateCategory method
    public function updateCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:product_categories,id',
                'name' => 'required|string|max:100|unique:product_categories,category_name,' . $request->id,
                'description' => 'nullable|string'
            ]);

            $category = ProductCategory::find($request->id);
            $category->update([
                'category_name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating category: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update the storeSubcategory method
    public function storeSubcategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'categoryId' => 'required|exists:product_categories,id'
            ]);

            // Check for duplicate subcategory name in the same category
            $existing = ProductSubcategory::where('category_id', $validated['categoryId'])
                ->where('subcategory_name', $validated['name'])
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subcategory with this name already exists in this category!'
                ], 400);
            }

            // Get the next display order for this category
            $maxOrder = ProductSubcategory::where('category_id', $validated['categoryId'])->max('display_order') ?? 0;

            $subcategory = ProductSubcategory::create([
                'category_id' => $validated['categoryId'],
                'subcategory_name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'display_order' => $maxOrder + 1,
                'is_active' => true,
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subcategory created successfully!',
                'subcategory' => $subcategory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating subcategory: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update the updateSubcategory method
    public function updateSubcategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:product_subcategories,id',
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:product_categories,id'
            ]);

            // Check for duplicate subcategory name in the same category (excluding current)
            $existing = ProductSubcategory::where('category_id', $validated['category_id'])
                ->where('subcategory_name', $validated['name'])
                ->where('id', '!=', $validated['id'])
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subcategory with this name already exists in this category!'
                ], 400);
            }

            $subcategory = ProductSubcategory::find($request->id);
            $subcategory->update([
                'category_id' => $validated['category_id'],
                'subcategory_name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subcategory updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating subcategory: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update the storeProductExample method
    public function storeProductExample(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:200',
                'description' => 'nullable|string',
                'subcategoryId' => 'required|exists:product_subcategories,id'
            ]);

            // Check for duplicate product name in the same subcategory
            $existing = ProductExample::where('subcategory_id', $validated['subcategoryId'])
                ->where('product_name', $validated['name'])
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product with this name already exists in this subcategory!'
                ], 400);
            }

            // Get the next display order for this subcategory
            $maxOrder = ProductExample::where('subcategory_id', $validated['subcategoryId'])->max('display_order') ?? 0;

            $product = ProductExample::create([
                'subcategory_id' => $validated['subcategoryId'],
                'product_name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'display_order' => $maxOrder + 1,
                'is_active' => true,
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product example created successfully!',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update the updateProductExample method
    public function updateProductExample(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:product_examples,id',
                'name' => 'required|string|max:200',
                'description' => 'nullable|string',
                'subcategory_id' => 'required|exists:product_subcategories,id'
            ]);

            // Check for duplicate product name in the same subcategory (excluding current)
            $existing = ProductExample::where('subcategory_id', $validated['subcategory_id'])
                ->where('product_name', $validated['name'])
                ->where('id', '!=', $validated['id'])
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product with this name already exists in this subcategory!'
                ], 400);
            }

            $product = ProductExample::find($request->id);
            $product->update([
                'subcategory_id' => $validated['subcategory_id'],
                'product_name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product example updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProduct($id)
    {
        $product = ProductExample::findOrFail($id);
        $product->delete();

        // Reorder products in the same subcategory
        $this->reorderProducts($product->subcategory_id);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully!'
        ]);
    }

    // Helper methods for reordering
    private function reorderCategories()
    {
        $categories = ProductCategory::orderBy('display_order')->get();
        $order = 1;
        foreach ($categories as $category) {
            $category->display_order = $order++;
            $category->save();
        }
    }

    private function reorderSubcategories($categoryId)
    {
        $subcategories = ProductSubcategory::where('category_id', $categoryId)
            ->orderBy('display_order')
            ->get();

        $order = 1;
        foreach ($subcategories as $subcategory) {
            $subcategory->display_order = $order++;
            $subcategory->save();
        }
    }

    private function reorderProducts($subcategoryId)
    {
        $products = ProductExample::where('subcategory_id', $subcategoryId)
            ->orderBy('display_order')
            ->get();

        $order = 1;
        foreach ($products as $product) {
            $product->display_order = $order++;
            $product->save();
        }
    }

    // System Standards CRUD Operations
    public function storeUnitOfMeasure(Request $request)
    {
        try {
            $validated = $request->validate([
                'standard_value' => 'required|string|max:100|unique:system_standards,standard_value',
                'description' => 'nullable|string',
                'standard_type' => 'required|string|in:unit_of_measure,quality_grade'
            ]);

            // Get the maximum display order for this standard type
            $maxOrder = SystemStandard::where('standard_type', $validated['standard_type'])
                ->max('display_order') ?? 0;

            // Create unit with automatic display order
            $unit = SystemStandard::create([
                'standard_type' => $validated['standard_type'],
                'standard_value' => $validated['standard_value'],
                'description' => $validated['description'] ?? null,
                'display_order' => $maxOrder + 1, // Automatic order assignment
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unit of measure created successfully!',
                'unit' => $unit
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating unit of measure: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating unit of measure: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateUnitOfMeasure(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'standard_value' => 'required|string|max:100|unique:system_standards,standard_value,' . $id,
                'description' => 'nullable|string',
                'display_order' => 'nullable|integer|min:1'
            ]);

            $unit = SystemStandard::findOrFail($id);

            // If display order is provided, reorder other items
            if (isset($validated['display_order']) && $validated['display_order'] != $unit->display_order) {
                $this->reorderStandards($unit->standard_type, $unit->id, $validated['display_order']);
            }

            $unit->update([
                'standard_value' => $validated['standard_value'],
                'description' => $validated['description'] ?? null,
                'display_order' => $validated['display_order'] ?? $unit->display_order
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unit of measure updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating unit of measure: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating unit of measure: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder standards when display order changes
     */
    private function reorderStandards($standardType, $currentId, $newOrder)
    {
        DB::beginTransaction();
        try {
            // Get all standards of this type except the current one, ordered by display_order
            $standards = SystemStandard::where('standard_type', $standardType)
                ->where('id', '!=', $currentId)
                ->orderBy('display_order')
                ->get();

            $order = 1;
            foreach ($standards as $standard) {
                if ($order == $newOrder) {
                    $order++; // Skip this position for the current item
                }
                $standard->display_order = $order;
                $standard->save();
                $order++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deactivateUnitOfMeasure($id)
    {
        try {
            $unit = SystemStandard::findOrFail($id);
            $unit->update(['is_active' => false]);

            // Reorder remaining active items
            $this->reorderActiveStandards($unit->standard_type);

            return response()->json([
                'success' => true,
                'message' => 'Unit deactivated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deactivating unit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deactivating unit: ' . $e->getMessage()
            ], 500);
        }
    }

    public function activateUnitOfMeasure($id)
    {
        try {
            $unit = SystemStandard::findOrFail($id);
            $unit->update(['is_active' => true]);

            // Get the maximum display order for active items
            $maxOrder = SystemStandard::where('standard_type', $unit->standard_type)
                ->where('is_active', true)
                ->where('id', '!=', $id)
                ->max('display_order') ?? 0;

            // Assign next available order
            $unit->display_order = $maxOrder + 1;
            $unit->save();

            return response()->json([
                'success' => true,
                'message' => 'Unit activated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error activating unit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error activating unit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder active standards after deactivation
     */
    private function reorderActiveStandards($standardType)
    {
        $activeStandards = SystemStandard::where('standard_type', $standardType)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $order = 1;
        foreach ($activeStandards as $standard) {
            $standard->display_order = $order;
            $standard->save();
            $order++;
        }
    }

    public function storeQualityGrade(Request $request)
    {
        try {
            $validated = $request->validate([
                'standard_value' => 'required|string|max:100',
                'description' => 'nullable|string'
            ]);

            // Check if quality grade already exists
            $existing = SystemStandard::where('standard_type', 'quality_grade')
                ->where('standard_value', $validated['standard_value'])
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quality grade with this name already exists!'
                ], 400);
            }

            // Get the maximum display order for quality grades
            $maxOrder = SystemStandard::where('standard_type', 'quality_grade')
                ->where('is_active', true)
                ->max('display_order') ?? 0;

            $grade = SystemStandard::create([
                'standard_type' => 'quality_grade',
                'standard_value' => $validated['standard_value'],
                'description' => $validated['description'] ?? null,
                'display_order' => $maxOrder + 1, // Automatic order assignment
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quality grade created successfully!',
                'grade' => $grade
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating quality grade: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating quality grade: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateQualityGrade(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'standard_value' => 'required|string|max:100|unique:system_standards,standard_value,' . $id . ',id,standard_type,quality_grade',
                'description' => 'nullable|string'
            ]);

            $grade = SystemStandard::findOrFail($id);

            // Check if it's a quality grade
            if ($grade->standard_type !== 'quality_grade') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid grade type!'
                ], 400);
            }

            $grade->update([
                'standard_value' => $validated['standard_value'],
                'description' => $validated['description'] ?? null,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quality grade updated successfully!',
                'grade' => $grade
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating quality grade: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating quality grade: ' . $e->getMessage()
            ], 500);
        }
    }

    public function activateQualityGrade($id)
    {
        try {
            $grade = SystemStandard::findOrFail($id);

            // Check if it's a quality grade
            if ($grade->standard_type !== 'quality_grade') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid grade type!'
                ], 400);
            }

            // Get the maximum display order for active quality grades
            $maxOrder = SystemStandard::where('standard_type', 'quality_grade')
                ->where('is_active', true)
                ->max('display_order') ?? 0;

            $grade->update([
                'is_active' => true,
                'display_order' => $maxOrder + 1,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quality grade activated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error activating quality grade: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error activating quality grade: ' . $e->getMessage()
            ], 500);
        }
    }

    public function unitOfMeasures()
    {
        $units = SystemStandard::where('standard_type', 'unit_of_measure')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('facilitator.unit_of_measures', compact('units'));
    }

    public function qualityGrades()
    {
        $grades = SystemStandard::where('standard_type', 'quality_grade')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('facilitator.quality_grades', compact('grades'));
    }

    public function userManagement(Request $request)
    {
        $query = User::with(['farmer', 'leadFarmer', 'buyer', 'facilitator'])
            ->where('role', '!=', 'admin')
            ->where('is_active', true);

        // Apply filters if provided
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get counts with the same filters
        $countQuery = User::where('role', '!=', 'admin')->where('is_active', true);

        if ($request->has('role') && $request->role) {
            $countQuery->where('role', $request->role);
        }

        $filteredUsers = $countQuery->get();

        $userTypes = [
            'farmers' => $filteredUsers->where('role', 'farmer')->count(),
            'lead_farmers' => $filteredUsers->where('role', 'lead_farmer')->count(),
            'buyers' => $filteredUsers->where('role', 'buyer')->count(),
            'facilitators' => $filteredUsers->where('role', 'facilitator')->count()
        ];

        return view('facilitator.users', compact('users', 'userTypes'));
    }

    public function userProfile($id)
    {
        try {
            $user = User::with(['farmer', 'leadFarmer', 'buyer', 'facilitator'])->findOrFail($id);

            $profileData = [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email ?? 'No email',
                'profile_photo' => asset('uploads/profile_pictures/' . ($user->profile_photo ?? 'default-avatar.png')),
                'role' => $user->role,
                'role_display' => ucwords(str_replace('_', ' ', $user->role)),
                'is_active' => $user->is_active,
                'status' => $user->is_active ? 'Active' : 'Inactive',
                'joined_date' => \Carbon\Carbon::parse($user->created_at)->format('d M Y'),
                'joined_relative' => \Carbon\Carbon::parse($user->created_at)->diffForHumans(),
                'last_login' => $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d M Y H:i') : 'Never'
            ];

            $contact = '';
            $location = '';
            $additional_info = [];

            if ($user->farmer) {
                $contact = $user->farmer->primary_mobile ?? '';
                $location = $user->farmer->district ?? '';
                $additional_info = [
                    'NIC' => $user->farmer->nic_no ?? 'N/A',
                    'Address' => $user->farmer->residential_address ?? 'N/A',
                    'Grama Niladhari Division' => $user->farmer->grama_niladhari_division ?? 'N/A',
                    'WhatsApp' => $user->farmer->whatsapp_number ?? 'N/A'
                ];
            } elseif ($user->leadFarmer) {
                $contact = $user->leadFarmer->primary_mobile ?? '';
                $location = $user->leadFarmer->grama_niladhari_division ?? '';
                $additional_info = [
                    'NIC' => $user->leadFarmer->nic_no ?? 'N/A',
                    'Group Name' => $user->leadFarmer->group_name ?? 'N/A',
                    'Group Number' => $user->leadFarmer->group_number ?? 'N/A',
                    'Address' => $user->leadFarmer->residential_address ?? 'N/A'
                ];
            } elseif ($user->buyer) {
                $contact = $user->buyer->primary_mobile ?? '';
                $additional_info = [
                    'NIC' => $user->buyer->nic_no ?? 'N/A',
                    'Business Name' => $user->buyer->business_name ?? 'N/A',
                    'Business Type' => $user->buyer->business_type ?? 'N/A',
                    'Address' => $user->buyer->residential_address ?? 'N/A'
                ];
            } elseif ($user->facilitator) {
                $contact = $user->facilitator->primary_mobile ?? '';
                $location = $user->facilitator->assigned_division ?? '';
                $additional_info = [
                    'NIC' => $user->facilitator->nic_no ?? 'N/A',
                    'Email' => $user->facilitator->email ?? 'N/A',
                    'Assigned Division' => $user->facilitator->assigned_division ?? 'N/A'
                ];
            }

            $profileData['contact'] = $contact;
            $profileData['location'] = $location;
            $profileData['additional_info'] = json_encode($additional_info, JSON_PRETTY_PRINT);

            return response()->json([
                'success' => true,
                'user' => $profileData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function sendEditOTP($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot edit your own profile through this method'
                ]);
            }

            $otp = rand(100000, 999999);

            session([
                'edit_otp' => $otp,
                'edit_user_id' => $id,
                'otp_expires_at' => now()->addMinutes(5)
            ]);

            $contact = '';
            if ($user->farmer) {
                $contact = $user->farmer->primary_mobile;
            } elseif ($user->leadFarmer) {
                $contact = $user->leadFarmer->primary_mobile;
            } elseif ($user->buyer) {
                $contact = $user->buyer->primary_mobile;
            } elseif ($user->facilitator) {
                $contact = $user->facilitator->primary_mobile;
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp' => $otp,
                'contact' => $contact ? substr($contact, -4) : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending OTP'
            ], 500);
        }
    }

    public function verifyEditOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
            'user_id' => 'required|exists:users,id'
        ]);

        $storedOTP = session('edit_otp');
        $storedUserId = session('edit_user_id');
        $expiresAt = session('otp_expires_at');

        if (!$storedOTP || !$expiresAt || now()->gt($expiresAt)) {
            session()->forget(['edit_otp', 'edit_user_id', 'otp_expires_at']);
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired'
            ]);
        }

        if ($request->otp != $storedOTP || $request->user_id != $storedUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ]);
        }

        session(['otp_verified_for_edit' => true, 'otp_verified_user_id' => $request->user_id]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
    }

    public function getUserForEdit($id)
    {
        if (!session('otp_verified_for_edit') || session('otp_verified_user_id') != $id) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification required'
            ], 403);
        }

        try {
            $user = User::with(['farmer', 'leadFarmer', 'buyer', 'facilitator'])->findOrFail($id);

            $userData = [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => $user->is_active,
                'profile_photo' => $user->profile_photo
            ];

            if ($user->farmer) {
                $userData['farmer'] = $user->farmer->toArray();
            }
            if ($user->leadFarmer) {
                $userData['lead_farmer'] = $user->leadFarmer->toArray();
            }
            if ($user->buyer) {
                $userData['buyer'] = $user->buyer->toArray();
            }
            if ($user->facilitator) {
                $userData['facilitator'] = $user->facilitator->toArray();
            }

            return response()->json([
                'success' => true,
                'user' => $userData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function updateUser(Request $request, $id)
    {
        if (!session('otp_verified_for_edit') || session('otp_verified_user_id') != $id) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification required'
            ], 403);
        }

        try {
            $user = User::findOrFail($id);

            DB::beginTransaction();

            $user->update([
                'username' => $request->username,
                'email' => $request->email,
                'is_active' => $request->is_active
            ]);

            $roleData = [];
            switch ($user->role) {
                case 'farmer':
                    if ($user->farmer) {
                        $roleData = $request->only([
                            'name', 'nic_no', 'primary_mobile', 'whatsapp_number',
                            'residential_address', 'district', 'grama_niladhari_division'
                        ]);
                        $user->farmer->update($roleData);
                    }
                    break;
                case 'lead_farmer':
                    if ($user->leadFarmer) {
                        $roleData = $request->only([
                            'name', 'nic_no', 'primary_mobile', 'whatsapp_number',
                            'residential_address', 'grama_niladhari_division',
                            'group_name', 'group_number'
                        ]);
                        $user->leadFarmer->update($roleData);
                    }
                    break;
                case 'buyer':
                    if ($user->buyer) {
                        $roleData = $request->only([
                            'name', 'nic_no', 'primary_mobile', 'whatsapp_number',
                            'residential_address', 'business_name', 'business_type'
                        ]);
                        $user->buyer->update($roleData);
                    }
                    break;
                case 'facilitator':
                    if ($user->facilitator) {
                        $roleData = $request->only([
                            'name', 'nic_no', 'primary_mobile', 'whatsapp_number',
                            'email', 'assigned_division'
                        ]);
                        $user->facilitator->update($roleData);
                    }
                    break;
            }

            session()->forget(['otp_verified_for_edit', 'otp_verified_user_id', 'edit_otp', 'edit_user_id', 'otp_expires_at']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportUsers(Request $request)
    {
        try {
            $query = User::with(['farmer', 'leadFarmer', 'buyer', 'facilitator'])
                ->where('role', '!=', 'admin');

            if ($request->reportType == 'active') {
                $query->where('is_active', true);
            }

            $users = $query->orderBy('created_at', 'desc')->get();

            $pdf = \PDF::loadView('facilitator.exports.users_pdf', [
                'users' => $users,
                'includeContact' => $request->includeContact,
                'includeLocation' => $request->includeLocation,
                'reportType' => $request->reportType,
                'generatedAt' => now()
            ]);

            return $pdf->download('Users_Report_' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }



    public function complaints()
    {
        $complaints = Complaint::with(['complainantUser', 'againstUser', 'resolvedByFacilitator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $complaintStats = [
            'new' => $complaints->where('status', 'new')->count(),
            'in_progress' => $complaints->where('status', 'in_progress')->count(),
            'resolved' => $complaints->where('status', 'resolved')->count(),
            'rejected' => $complaints->where('status', 'rejected')->count()
        ];

        return view('facilitator.complaints', compact('complaints', 'complaintStats'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        $facilitator = $user->facilitator;

        return view('facilitator.profile', compact('facilitator'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $facilitator = $user->facilitator;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'primary_mobile' => 'required|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'assigned_division' => 'required|string|max:100'
        ]);

        $facilitator->update($validated);

        return redirect()->route('facilitator.profile')->with('success', 'Profile updated successfully!');
    }

    public function profilePhoto()
    {
        $user = Auth::user();
        return view('facilitator.profile_photo', compact('user'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_photo')) {
            $image = $request->file('profile_photo');
            $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/profile_pictures'), $imageName);

            $user->profile_photo = $imageName;
            $user->save();
        }

        return redirect()->route('facilitator.profile.photo')->with('success', 'Profile photo updated successfully!');
    }

    public function accountSettings()
    {
        $user = Auth::user();
        return view('facilitator.account_settings', compact('user'));
    }

    public function updateAccountSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required'
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('facilitator.account.settings')->with('success', 'Password updated successfully!');
    }

    public function notifications()
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('facilitator.notifications', compact('notifications', 'unreadCount'));
    }

    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}

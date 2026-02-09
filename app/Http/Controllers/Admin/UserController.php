<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserUpdateNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Get total counts for all users (not restricted by pagination)
        $totalUsers = DB::table('users')->count();
        $activeUsers = DB::table('users')->where('is_active', true)->count();
        $inactiveUsers = DB::table('users')->where('is_active', false)->count();
        $adminUsers = DB::table('users')->whereIn('role', ['admin', 'subadmin'])->count();

        $query = DB::table('users')
            ->leftJoin('farmers', 'users.id', '=', 'farmers.user_id')
            ->leftJoin('lead_farmers', 'users.id', '=', 'lead_farmers.user_id')
            ->leftJoin('buyers', 'users.id', '=', 'buyers.user_id')
            ->leftJoin('facilitators', 'users.id', '=', 'facilitators.user_id')
            ->leftJoin('admins', 'users.id', '=', 'admins.user_id')
            ->select(
                'users.*',
                'farmers.name as farmer_name',
                'farmers.nic_no as farmer_nic',
                'farmers.primary_mobile as farmer_mobile',
                'lead_farmers.name as lead_farmer_name',
                'lead_farmers.nic_no as lead_farmer_nic',
                'lead_farmers.primary_mobile as lead_farmer_mobile',
                'buyers.name as buyer_name',
                'buyers.nic_no as buyer_nic',
                'buyers.primary_mobile as buyer_mobile',
                'facilitators.name as facilitator_name',
                'facilitators.nic_no as facilitator_nic',
                'facilitators.primary_mobile as facilitator_mobile',
                'admins.full_name as admin_name',
                'admins.nic_no as admin_nic',
                'admins.phone_number as admin_phone'
            )
            ->orderBy('users.created_at', 'desc');

        if ($request->filled('q')) {
            $search = '%' . $request->q . '%';
            
            $query->where(function($q) use ($search) {
                $q->where('users.username', 'ILIKE', $search)
                    ->orWhere('users.email', 'ILIKE', $search)
                    ->orWhere('farmers.name', 'ILIKE', $search)
                    ->orWhere('lead_farmers.name', 'ILIKE', $search)
                    ->orWhere('buyers.name', 'ILIKE', $search)
                    ->orWhere('facilitators.name', 'ILIKE', $search)
                    ->orWhere('admins.full_name', 'ILIKE', $search)
                    ->orWhere('farmers.nic_no', 'ILIKE', $search)
                    ->orWhere('lead_farmers.nic_no', 'ILIKE', $search)
                    ->orWhere('buyers.nic_no', 'ILIKE', $search)
                    ->orWhere('facilitators.nic_no', 'ILIKE', $search)
                    ->orWhere('admins.nic_no', 'ILIKE', $search)
                    ->orWhere('farmers.primary_mobile', 'ILIKE', $search)
                    ->orWhere('lead_farmers.primary_mobile', 'ILIKE', $search)
                    ->orWhere('buyers.primary_mobile', 'ILIKE', $search)
                    ->orWhere('facilitators.primary_mobile', 'ILIKE', $search)
                    ->orWhere('admins.phone_number', 'ILIKE', $search);
            });
        }

        $viewType = $request->get('view', 'card');
        $perPage = $viewType === 'table' ? 15 : 6 ;
        
        $usersPaginator = $query->paginate($perPage);

        if ($request->ajax()) {
            try {
                $usersWithDetails = $usersPaginator->getCollection()->map(function($user) {
                    return $this->getFullUserDetails($user);
                });

                $usersPaginator->setCollection($usersWithDetails);

                // FIXED: Changed the view name to match what actually exists
                $viewName = $viewType == 'table' ? 'admin.users.partials.user_table' : 'admin.users.partials.user_cards';

                return response()->json([
                    'success' => true,
                    'html' => view($viewName, [
                        'users' => $usersPaginator->getCollection()
                    ])->render(),
                    'pagination' => $usersPaginator->links('vendor.pagination.simple-unique')->render(),
                    'total' => $usersPaginator->total(),
                    'stats' => [
                        'active' => $activeUsers,
                        'inactive' => $inactiveUsers,
                        'admins' => $adminUsers,
                        'total' => $totalUsers
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('Error loading users via AJAX: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to load users: ' . $e->getMessage()
                ], 500);
            }
        }

        $usersWithDetails = $usersPaginator->getCollection()->map(function($user) {
            return $this->getFullUserDetails($user);
        });

        $usersPaginator->setCollection($usersWithDetails);

        return view('admin.users.index', [
            'users' => $usersWithDetails,
            'paginator' => $usersPaginator,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'adminUsers' => $adminUsers
        ]);
    }

    private function getFullUserDetails($user)
    {
        switch($user->role) {
            case 'farmer':
                $user->display_name = $user->farmer_name ?? $user->username;
                $user->contact_number = $user->farmer_mobile ?? 'N/A';
                $user->nic_number = $user->farmer_nic ?? '';
                break;

            case 'lead_farmer':
                $user->display_name = $user->lead_farmer_name ?? $user->username;
                $user->contact_number = $user->lead_farmer_mobile ?? 'N/A';
                $user->nic_number = $user->lead_farmer_nic ?? '';
                break;

            case 'buyer':
                $user->display_name = $user->buyer_name ?? $user->username;
                $user->contact_number = $user->buyer_mobile ?? 'N/A';
                $user->nic_number = $user->buyer_nic ?? '';
                break;

            case 'facilitator':
                $user->display_name = $user->facilitator_name ?? $user->username;
                $user->contact_number = $user->facilitator_mobile ?? 'N/A';
                $user->nic_number = $user->facilitator_nic ?? '';
                break;

            case 'admin':
            case 'subadmin':
                $user->display_name = $user->admin_name ?? $user->username;
                $user->contact_number = $user->admin_phone ?? 'N/A';
                $user->nic_number = $user->admin_nic ?? '';
                break;
        }

        return $user;
    }

    public function show($id)
    {
        $user = DB::table('users')->find($id);

        if (!$user) {
            abort(404);
        }

        $details = $this->getUserDetails($user);

        return view('admin.users.show', compact('user', 'details'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_type' => 'required|in:farmer,lead_farmer,buyer,facilitator,admin,subadmin',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:100'
        ]);

        // Additional validation for farmer types
        if (in_array($validated['user_type'], ['farmer', 'lead_farmer'])) {
            $request->validate([
                'nic_no' => 'required|string|max:12'
            ]);
            
            // Check if NIC already exists in farmers or lead_farmers table
            $nicExists = false;
            $nicNumber = $request->nic_no;
            
            if ($validated['user_type'] == 'farmer') {
                $nicExists = DB::table('farmers')->where('nic_no', $nicNumber)->exists();
            } else {
                $nicExists = DB::table('lead_farmers')->where('nic_no', $nicNumber)->exists();
            }
            
            if ($nicExists) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to create user because NIC No. "' . $nicNumber . '" already exists'
                ], 422);
            }
        }

        // Additional validation for admin types
        if (in_array($validated['user_type'], ['admin', 'subadmin'])) {
            $request->validate([
                'phone_number' => 'required|string|max:20'
            ]);
            
            // Check if NIC already exists in admins table
            if ($request->has('nic_no') && $request->nic_no) {
                $nicExists = DB::table('admins')->where('nic_no', $request->nic_no)->exists();
                if ($nicExists) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Failed to create user because NIC No. "' . $request->nic_no . '" already exists'
                    ], 422);
                }
            }
        }

        DB::beginTransaction();

        try {
            $plainPassword = $validated['password'];
            
            $userId = DB::table('users')->insertGetId([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($plainPassword),
                'role' => $validated['user_type'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($validated['user_type'] == 'farmer' || $validated['user_type'] == 'lead_farmer') {
                $commonData = [
                    'user_id' => $userId,
                    'name' => $request->name,
                    'nic_no' => $request->nic_no,
                    'primary_mobile' => $request->primary_mobile,
                    'whatsapp_number' => $request->whatsapp_number,
                    'residential_address' => $request->residential_address,
                    'grama_niladhari_division' => $request->grama_niladhari_division,
                    'preferred_payment' => $request->preferred_payment ?? 'bank',
                    'district' => $request->district ?? 'Colombo',
                    'account_number' => $request->account_number,
                    'account_holder_name' => $request->account_holder_name,
                    'bank_name' => $request->bank_name,
                    'bank_branch' => $request->bank_branch,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if ($validated['user_type'] == 'lead_farmer') {
                    // Lead farmer table doesn't have email, is_active, ezcash_mobile, mcash_mobile
                    $leadFarmerId = DB::table('lead_farmers')->insertGetId(array_merge($commonData, [
                        'group_name' => $request->group_name,
                        'group_number' => $request->group_number
                    ]));

                    // A lead farmer is also a farmer, and the farmers table DOES have these columns
                    DB::table('farmers')->insert(array_merge($commonData, [
                        'lead_farmer_id' => $leadFarmerId,
                        'email' => $validated['email'] ?? null,
                        'is_active' => true,
                        'ezcash_mobile' => $request->ezcash_mobile,
                        'mcash_mobile' => $request->mcash_mobile
                    ]));
                } else {
                    $farmerData = array_merge($commonData, [
                        'email' => $validated['email'] ?? null,
                        'is_active' => true,
                        'ezcash_mobile' => $request->ezcash_mobile,
                        'mcash_mobile' => $request->mcash_mobile
                    ]);

                    $defaultLeadFarmer = DB::table('lead_farmers')->first();
                    if ($defaultLeadFarmer) {
                        DB::table('farmers')->insert(array_merge($farmerData, [
                            'lead_farmer_id' => $defaultLeadFarmer->id
                        ]));
                    } else {
                        $defaultLeadFarmerId = DB::table('lead_farmers')->insertGetId([
                            'user_id' => $userId,
                            'name' => 'Default Lead Farmer',
                            'nic_no' => '000000000V',
                            'primary_mobile' => '0770000000',
                            'residential_address' => 'Default Address',
                            'grama_niladhari_division' => 'Default Division',
                            'group_name' => 'Default Group',
                            'group_number' => 'GRP-000001',
                            'preferred_payment' => 'bank',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        DB::table('farmers')->insert(array_merge($farmerData, [
                            'lead_farmer_id' => $defaultLeadFarmerId
                        ]));
                    }
                }
            } elseif ($validated['user_type'] == 'buyer') {
                DB::table('buyers')->insert([
                    'user_id' => $userId,
                    'name' => $request->name,
                    'primary_mobile' => $request->primary_mobile,
                    'business_name' => $request->business_name,
                    'business_type' => $request->business_type ?? 'individual',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } elseif ($validated['user_type'] == 'facilitator') {
                DB::table('facilitators')->insert([
                    'user_id' => $userId,
                    'name' => $request->name,
                    'nic_no' => $request->nic_no,
                    'email' => $validated['email'] ?? null,
                    'primary_mobile' => $request->primary_mobile,
                    'whatsapp_number' => $request->whatsapp_number,
                    'assigned_division' => $request->assigned_division,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } elseif ($validated['user_type'] == 'admin' || $validated['user_type'] == 'subadmin') {
                // Insert into admins table
                DB::table('admins')->insert([
                    'user_id' => $userId,
                    'full_name' => $request->name,
                    'nic_no' => $request->nic_no ?? 'NOT_SET',
                    'role' => $validated['user_type'],
                    'phone_number' => $request->phone_number,
                    'zone_assigned_area' => 'Sri Lanka',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            $this->sendUserCreationNotification($userId, $validated['user_type'], $plainPassword);
        
            return response()->json(['success' => true, 'message' => 'User created successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Parse the exception message to provide a user-friendly error
            $errorMessage = $e->getMessage();
            
            // Check for duplicate NIC error
            if (strpos($errorMessage, 'farmers_nic_no_key') !== false && strpos($errorMessage, 'already exists') !== false) {
                // Extract NIC number from error message
                preg_match('/Key \(nic_no\)=\(([^)]+)\) already exists/', $errorMessage, $matches);
                $nicNumber = $matches[1] ?? '';
                
                if ($nicNumber) {
                    $errorMessage = 'Failed to create user because NIC No. "' . $nicNumber . '" already exists';
                } else {
                    $errorMessage = 'Failed to create user because NIC number already exists';
                }
            }
            // Check for duplicate username error
            elseif (strpos($errorMessage, 'users_username_key') !== false && strpos($errorMessage, 'already exists') !== false) {
                preg_match('/Key \(username\)=\(([^)]+)\) already exists/', $errorMessage, $matches);
                $username = $matches[1] ?? '';
                
                if ($username) {
                    $errorMessage = 'Failed to create user because username "' . $username . '" already exists';
                } else {
                    $errorMessage = 'Failed to create user because username already exists';
                }
            }
            // Check for duplicate email error
            elseif (strpos($errorMessage, 'users_email_key') !== false && strpos($errorMessage, 'already exists') !== false) {
                preg_match('/Key \(email\)=\(([^)]+)\) already exists/', $errorMessage, $matches);
                $email = $matches[1] ?? '';
                
                if ($email) {
                    $errorMessage = 'Failed to create user because email "' . $email . '" already exists';
                } else {
                    $errorMessage = 'Failed to create user because email already exists';
                }
            }
            // Check for duplicate NIC in admins table
            elseif (strpos($errorMessage, 'admins_nic_no_key') !== false && strpos($errorMessage, 'already exists') !== false) {
                preg_match('/Key \(nic_no\)=\(([^)]+)\) already exists/', $errorMessage, $matches);
                $nicNumber = $matches[1] ?? '';
                
                if ($nicNumber) {
                    $errorMessage = 'Failed to create user because NIC No. "' . $nicNumber . '" already exists';
                } else {
                    $errorMessage = 'Failed to create user because NIC number already exists';
                }
            }
            // General database error handling to avoid showing raw SQL
            elseif (strpos($errorMessage, 'SQLSTATE') !== false) {
                \Log::error('Database Error during user creation: ' . $errorMessage);
                $errorMessage = 'A database error occurred while creating the user. Please try again later.';
            }
            
            return response()->json(['success' => false, 'message' => $errorMessage], 500);
        }
    }

    public function edit($id)
    {
        $user = DB::table('users')->find($id);

        if (!$user) {
            abort(404);
        }

        $details = $this->getUserDetails($user);

        return view('admin.users.edit', compact('user', 'details'));
    }

    public function update(Request $request, $id)
    {
        $user = DB::table('users')->find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $currentUser = Auth::user();

        if ($user->id == $currentUser->id) {
            $request->merge(['role' => $user->role]);
            $request->merge(['is_active' => $user->is_active]);
        }

        if (in_array($user->role, ['facilitator', 'buyer', 'admin', 'subadmin']) && $user->id != $currentUser->id) {
            $request->merge(['role' => $user->role]);
        }

        if ($user->role == 'farmer' && $request->role != 'farmer' && $request->role != 'lead_farmer') {
            $request->merge(['role' => $user->role]);
        }

        if ($user->role == 'lead_farmer' && $request->role != 'farmer' && $request->role != 'lead_farmer') {
            $request->merge(['role' => $user->role]);
        }

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'nullable|email|max:100|unique:users,email,' . $id,
            'role' => 'required|in:admin,subadmin,facilitator,lead_farmer,farmer,buyer',
            'is_active' => 'required|boolean'
        ]);

        $roleChanged = $validated['role'] != $user->role;

        if ($roleChanged && in_array($user->role, ['farmer', 'lead_farmer']) && in_array($validated['role'], ['farmer', 'lead_farmer'])) {
            return $this->handleFarmerRoleChange($user, $validated, $request, $id);
        }

        DB::beginTransaction();

        try {
            $oldData = (array) $user;

            DB::table('users')->where('id', $id)->update([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'is_active' => $validated['is_active'],
                'updated_at' => now()
            ]);

            if ($validated['role'] == 'farmer' || $validated['role'] == 'lead_farmer') {
                $this->updateFarmerDetails($user, $validated['role'], $request, $id);
            } elseif ($validated['role'] == 'buyer') {
                $this->updateBuyerDetails($user, $request, $id);
            } elseif ($validated['role'] == 'facilitator') {
                $this->updateFacilitatorDetails($user, $request, $id);
            } elseif ($validated['role'] == 'admin' || $validated['role'] == 'subadmin') {
                $this->updateAdminDetails($user, $request, $id);
            }

            DB::commit();

            $newData = (array) DB::table('users')->find($id);
            $this->sendUpdateNotification($id, $oldData, $newData);

            return response()->json(['success' => true, 'message' => 'User updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Parse the exception message to provide a user-friendly error
            $errorMessage = $e->getMessage();
            
            // Check for duplicate NIC error
            if (strpos($errorMessage, 'farmers_nic_no_key') !== false && strpos($errorMessage, 'already exists') !== false) {
                preg_match('/Key \(nic_no\)=\(([^)]+)\) already exists/', $errorMessage, $matches);
                $nicNumber = $matches[1] ?? '';
                
                if ($nicNumber) {
                    $errorMessage = 'Failed to update user because NIC No. "' . $nicNumber . '" already exists';
                } else {
                    $errorMessage = 'Failed to update user because NIC number already exists';
                }
            }
            // Check for duplicate NIC in admins table
            elseif (strpos($errorMessage, 'admins_nic_no_key') !== false && strpos($errorMessage, 'already exists') !== false) {
                preg_match('/Key \(nic_no\)=\(([^)]+)\) already exists/', $errorMessage, $matches);
                $nicNumber = $matches[1] ?? '';
                
                if ($nicNumber) {
                    $errorMessage = 'Failed to update user because NIC No. "' . $nicNumber . '" already exists';
                } else {
                    $errorMessage = 'Failed to update user because NIC number already exists';
                }
            }
            // General database error handling to avoid showing raw SQL
            elseif (strpos($errorMessage, 'SQLSTATE') !== false) {
                \Log::error('Database Error during user update: ' . $errorMessage);
                $errorMessage = 'A database error occurred while updating the user. Please try again later.';
            }
            
            return response()->json(['success' => false, 'message' => $errorMessage], 500);
        }
    }

    private function handleFarmerRoleChange($user, $validated, $request, $userId)
    {
        DB::beginTransaction();

        try {
            if ($user->role == 'farmer' && $validated['role'] == 'lead_farmer') {
                $farmer = DB::table('farmers')->where('user_id', $userId)->first();

                if ($farmer) {
                    if ($farmer->preferred_payment !== 'bank') {
                        return response()->json(['success' => false, 'message' => 'Lead farmer is only allowed preferred payment method as the bank transfer'], 400);
                    }

                    $leadFarmerId = DB::table('lead_farmers')->insertGetId([
                        'user_id' => $userId,
                        'name' => $farmer->name,
                        'nic_no' => $farmer->nic_no,
                        'primary_mobile' => $farmer->primary_mobile,
                        'whatsapp_number' => $farmer->whatsapp_number,
                        'residential_address' => $farmer->residential_address,
                        'grama_niladhari_division' => $farmer->grama_niladhari_division,
                        'district' => $farmer->district ?? 'Colombo',
                        'group_name' => $request->group_name ?? ($farmer->name . "'s Group"),
                        'group_number' => $request->group_number ?? ('GRP-' . strtoupper(Str::random(6))),
                        'preferred_payment' => $farmer->preferred_payment,
                        'account_number' => $farmer->account_number,
                        'account_holder_name' => $farmer->account_holder_name,
                        'bank_name' => $farmer->bank_name,
                        'bank_branch' => $farmer->bank_branch,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    DB::table('farmers')
                        ->where('user_id', $userId)
                        ->update(['lead_farmer_id' => $leadFarmerId]);
                }
            } elseif ($user->role == 'lead_farmer' && $validated['role'] == 'farmer') {
                $leadFarmer = DB::table('lead_farmers')->where('user_id', $userId)->first();

                if ($leadFarmer) {
                    $otherLeadFarmer = DB::table('lead_farmers')
                        ->where('id', '!=', $leadFarmer->id)
                        ->first();

                    if ($otherLeadFarmer) {
                        DB::table('farmers')
                            ->where('user_id', $userId)
                            ->update(['lead_farmer_id' => $otherLeadFarmer->id]);

                        DB::table('lead_farmers')->where('id', $leadFarmer->id)->delete();
                    }
                }
            }

            DB::table('users')->where('id', $userId)->update([
                'role' => $validated['role'],
                'updated_at' => now()
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'User role updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'lead_farmers_nic_no_key') !== false && strpos($errorMessage, 'already exists') !== false) {
                preg_match('/Key \(nic_no\)=\(([^)]+)\) already exists/', $errorMessage, $matches);
                $nicNumber = $matches[1] ?? '';
                
                if ($nicNumber) {
                    $errorMessage = 'Failed to change role because NIC No. "' . $nicNumber . '" already exists as a Lead Farmer';
                }
            }
            // General database error handling to avoid showing raw SQL
            elseif (strpos($errorMessage, 'SQLSTATE') !== false) {
                \Log::error('Database Error during role change: ' . $errorMessage);
                $errorMessage = 'A database error occurred while changing the user role. Please try again later.';
            }
            
            return response()->json(['success' => false, 'message' => $errorMessage], 500);
        }
    }

    private function updateFarmerDetails($user, $role, $request, $userId)
    {
        $table = $role == 'farmer' ? 'farmers' : 'lead_farmers';

        $details = DB::table($table)->where('user_id', $userId)->first();

        $updateData = [
            'updated_at' => now()
        ];

        $paymentFields = ['preferred_payment', 'account_number', 'account_holder_name',
                        'bank_name', 'bank_branch', 'ezcash_mobile', 'mcash_mobile'];

        $profileFields = ['nic_no', 'primary_mobile', 'whatsapp_number', 'residential_address', 
                         'grama_niladhari_division', 'district', 'group_name', 'group_number'];

        foreach ($profileFields as $field) {
            // Filter fields based on table schema
            if ($table == 'lead_farmers' && in_array($field, ['email', 'is_active'])) continue;

            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }

        foreach ($paymentFields as $field) {
            // Filter fields based on table schema
            if ($table == 'lead_farmers' && in_array($field, ['ezcash_mobile', 'mcash_mobile'])) continue;

            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }

        if ($details) {
            DB::table($table)->where('user_id', $userId)->update($updateData);
        } else {
            $userRecord = DB::table('users')->find($userId);

            $createData = array_merge($updateData, [
                'user_id' => $userId,
                'name' => $userRecord->username,
                'nic_no' => $request->nic_no ?? '',
                'primary_mobile' => $request->primary_mobile ?? '',
                'residential_address' => $request->residential_address ?? '',
                'grama_niladhari_division' => $request->grama_niladhari_division ?? '',
                'district' => 'Colombo',
                'is_active' => true,
                'created_at' => now()
            ]);

            if ($role == 'lead_farmer') {
                $createData['group_name'] = $request->group_name ?? ($userRecord->username . "'s Group");
                $createData['group_number'] = $request->group_number ?? ('GRP-' . strtoupper(Str::random(6)));
            }

            DB::table($table)->insert($createData);
        }
    }

    private function updateBuyerDetails($user, $request, $userId)
    {
        $buyer = DB::table('buyers')->where('user_id', $userId)->first();

        $updateData = [
            'business_name' => $request->business_name ?? ($buyer->business_name ?? ''),
            'business_type' => $request->business_type ?? ($buyer->business_type ?? 'individual'),
            'updated_at' => now()
        ];

        if ($buyer) {
            DB::table('buyers')->where('user_id', $userId)->update($updateData);
        } else {
            $userRecord = DB::table('users')->find($userId);

            DB::table('buyers')->insert(array_merge($updateData, [
                'user_id' => $userId,
                'name' => $userRecord->username,
                'primary_mobile' => $request->primary_mobile ?? '',
                'is_verified' => false,
                'created_at' => now()
            ]));
        }
    }

    private function updateFacilitatorDetails($user, $request, $userId)
    {
        $facilitator = DB::table('facilitators')->where('user_id', $userId)->first();

        if ($facilitator) {
            $updateData = [
                'updated_at' => now()
            ];

            $fields = ['name', 'nic_no', 'primary_mobile', 'whatsapp_number', 'email', 'assigned_division'];
            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            DB::table('facilitators')
                ->where('user_id', $userId)
                ->update($updateData);
        }
    }

    private function updateAdminDetails($user, $request, $userId)
    {
        $admin = DB::table('admins')->where('user_id', $userId)->first();

        $updateData = [
            'full_name' => $request->name ?? ($admin->full_name ?? ''),
            'nic_no' => $request->nic_no ?? ($admin->nic_no ?? 'NOT_SET'),
            'phone_number' => $request->phone_number ?? ($admin->phone_number ?? ''),
            'updated_at' => now()
        ];

        if ($admin) {
            DB::table('admins')->where('user_id', $userId)->update($updateData);
        } else {
            $userRecord = DB::table('users')->find($userId);

            DB::table('admins')->insert(array_merge($updateData, [
                'user_id' => $userId,
                'role' => $userRecord->role,
                'zone_assigned_area' => 'Sri Lanka',
                'created_at' => now()
            ]));
        }
    }

    public function deactivate($id)
    {
        $user = DB::table('users')->find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        if ($user->id == Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You cannot deactivate your own account'], 400);
        }

        DB::table('users')->where('id', $id)->update([
            'is_active' => false,
            'updated_at' => now()
        ]);

        $this->sendDeactivationNotification($id);

        return response()->json(['success' => true, 'message' => 'User deactivated successfully']);
    }

    public function activate($id)
    {
        $user = DB::table('users')->find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        DB::table('users')->where('id', $id)->update([
            'is_active' => true,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'User activated successfully']);
    }

    public function suspend($id)
    {
        $user = DB::table('users')->find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        if ($user->id == Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You cannot suspend your own account'], 400);
        }

        DB::table('users')->where('id', $id)->update([
            'is_active' => false,
            'updated_at' => now()
        ]);

        $this->sendSuspensionNotification($id);

        return response()->json(['success' => true, 'message' => 'User suspended successfully']);
    }

    public function promote($id)
    {
        $user = DB::table('users')->find($id);

        if (!$user || $user->role != 'farmer') {
            return response()->json(['success' => false, 'message' => 'Only farmers can be promoted'], 400);
        }

        DB::beginTransaction();

        try {
            $farmer = DB::table('farmers')->where('user_id', $id)->first();

            if (!$farmer) {
                throw new \Exception('Farmer details not found');
            }

            if ($farmer->preferred_payment !== 'bank') {
                return response()->json(['success' => false, 'message' => 'Lead farmer is only allowed preferred payment method as the bank transfer'], 400);
            }

            // Check if NIC already exists as lead farmer
            $nicExists = DB::table('lead_farmers')->where('nic_no', $farmer->nic_no)->exists();
            if ($nicExists) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot promote because NIC No. "' . $farmer->nic_no . '" already exists as a Lead Farmer'
                ], 400);
            }

            $leadFarmerId = DB::table('lead_farmers')->insertGetId([
                'user_id' => $id,
                'name' => $farmer->name,
                'nic_no' => $farmer->nic_no,
                'primary_mobile' => $farmer->primary_mobile,
                'whatsapp_number' => $farmer->whatsapp_number,
                'residential_address' => $farmer->residential_address,
                'district' => $farmer->district ?? 'Colombo',
                'grama_niladhari_division' => $farmer->grama_niladhari_division,
                'group_name' => $farmer->name . "'s Group",
                'group_number' => 'GRP-' . strtoupper(Str::random(6)),
                'preferred_payment' => $farmer->preferred_payment,
                'account_number' => $farmer->account_number,
                'account_holder_name' => $farmer->account_holder_name,
                'bank_name' => $farmer->bank_name,
                'bank_branch' => $farmer->bank_branch,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('users')->where('id', $id)->update([
                'role' => 'lead_farmer',
                'updated_at' => now()
            ]);

            DB::table('farmers')->where('user_id', $id)->update([
                'lead_farmer_id' => $leadFarmerId,
                'updated_at' => now()
            ]);

            DB::commit();

            $this->sendPromotionNotification($id);

            return response()->json(['success' => true, 'message' => 'Farmer promoted to Lead Farmer']);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'lead_farmers_nic_no_key') !== false && strpos($errorMessage, 'already exists') !== false) {
                preg_match('/Key \(nic_no\)=\(([^)]+)\) already exists/', $errorMessage, $matches);
                $nicNumber = $matches[1] ?? '';
                
                if ($nicNumber) {
                    $errorMessage = 'Cannot promote because NIC No. "' . $nicNumber . '" already exists as a Lead Farmer';
                }
            }
            
            return response()->json(['success' => false, 'message' => $errorMessage], 500);
        }
    }

    public function makeSubadmin($id)
    {
        $user = DB::table('users')->find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        if ($user->role == 'admin') {
            DB::table('users')->where('id', $id)->update([
                'role' => 'subadmin',
                'updated_at' => now()
            ]);

            // Update admin role in admins table
            DB::table('admins')->where('user_id', $id)->update([
                'role' => 'subadmin',
                'updated_at' => now()
            ]);

            $this->sendRoleChangeNotification($id, 'subadmin');

            return response()->json(['success' => true, 'message' => 'User made Sub Administrator']);
        }

        return response()->json(['success' => false, 'message' => 'User is not an administrator'], 400);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|in:edit_payment'
        ]);

        $user = DB::table('users')->find($request->user_id);

        if (!in_array($user->role, ['farmer', 'lead_farmer'])) {
            return response()->json(['success' => false, 'message' => 'OTP only required for farmers'], 400);
        }

        $table = $user->role == 'farmer' ? 'farmers' : 'lead_farmers';
        $details = DB::table($table)->where('user_id', $user->id)->first();

        if (!$details || !$details->primary_mobile) {
            return response()->json(['success' => false, 'message' => 'User mobile number not found'], 400);
        }

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        DB::table('otp_verifications')->insert([
            'user_id' => $user->id,
            'otp' => $otp,
            'action' => $request->action,
            'expires_at' => $expiresAt,
            'created_at' => now()
        ]);

        $smsSent = $this->sendSmsOtp($details->primary_mobile, $otp);

        if ($smsSent) {
            return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to send SMS. Please check SMS configuration.'], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|digits:6',
            'action' => 'required|in:edit_payment'
        ]);

        $otpRecord = DB::table('otp_verifications')
            ->where('user_id', $request->user_id)
            ->where('otp', $request->otp)
            ->where('action', $request->action)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->first();

        if (!$otpRecord) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP'], 400);
        }

        DB::table('otp_verifications')
            ->where('id', $otpRecord->id)
            ->update(['used' => true, 'used_at' => now()]);

        return response()->json(['success' => true, 'message' => 'OTP verified successfully']);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = DB::table('users')->find($request->user_id);

        if (!in_array($user->role, ['farmer', 'lead_farmer'])) {
            return response()->json(['success' => false, 'message' => 'OTP only required for farmers'], 400);
        }

        $table = $user->role == 'farmer' ? 'farmers' : 'lead_farmers';
        $details = DB::table($table)->where('user_id', $user->id)->first();

        if (!$details || !$details->primary_mobile) {
            return response()->json(['success' => false, 'message' => 'User mobile number not found'], 400);
        }

        DB::table('otp_verifications')
            ->where('user_id', $user->id)
            ->where('used', false)
            ->delete();

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        DB::table('otp_verifications')->insert([
            'user_id' => $user->id,
            'otp' => $otp,
            'action' => 'edit_payment',
            'expires_at' => $expiresAt,
            'created_at' => now()
        ]);

        $smsSent = $this->sendSmsOtp($details->primary_mobile, $otp);

        if ($smsSent) {
            return response()->json(['success' => true, 'message' => 'OTP resent successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to send SMS. Please check SMS configuration.'], 500);
        }
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'message' => 'required|string'
        ]);

        $user = DB::table('users')->find($request->user_id);

        DB::table('notifications')->insert([
            'user_id' => $user->id,
            'recipient_type' => 'user',
            'recipient_address' => $user->email,
            'title' => ucfirst($request->type) . ' Notification',
            'message' => $request->message,
            'notification_type' => 'system',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Notification sent']);
    }

    private function sendSmsOtp($mobile, $otp)
    {
        try {
            $user = env('SMS_USER');
            $password = env('SMS_PASSWORD');
            $baseurl = env('SMS_API_URL');

            if (!$user || !$password || !$baseurl) {
                \Log::info("SMS OTP for {$mobile}: {$otp} (SMS not configured)");
                return true;
            }

            // Format mobile number: remove non-digits, remove leading '0', prepend '94'
            $mobile = preg_replace('/[^0-9]/', '', $mobile);
            if (strpos($mobile, '0') === 0) {
                $mobile = '94' . substr($mobile, 1);
            } elseif (strpos($mobile, '94') !== 0) {
                $mobile = '94' . $mobile;
            }

            $text = urlencode("Your OTP for payment details update is: $otp. Valid for 5 minutes.");

            $baseurl = rtrim($baseurl, '/');
            $url = "{$baseurl}/?id={$user}&pw={$password}&to={$mobile}&text={$text}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response !== false) {
                $result = explode(":", $response);
                if (trim($result[0]) == "OK") {
                    \Log::info("SMS OTP sent successfully to {$mobile}. Response: {$response}");
                    return true;
                } else {
                    \Log::error("SMS OTP failed for {$mobile}. Response: {$response}");
                }
            } else {
                \Log::error("Curl error sending SMS OTP to {$mobile}. HTTP Code: {$httpCode}");
            }

            return false;

        } catch (\Exception $e) {
            \Log::error("SMS sending failed: " . $e->getMessage());
            return false;
        }
    }

    private function sendSms($mobile, $message)
    {
        try {
            $user = env('SMS_USER');
            $password = env('SMS_PASSWORD');
            $baseurl = env('SMS_API_URL');

            if (!$user || !$password || !$baseurl) {
                \Log::info("SMS for {$mobile}: {$message} (SMS not configured)");
                return true;
            }

            // Format mobile number: remove non-digits, remove leading '0', prepend '94'
            $mobile = preg_replace('/[^0-9]/', '', $mobile);
            if (strpos($mobile, '0') === 0) {
                $mobile = '94' . substr($mobile, 1);
            } elseif (strpos($mobile, '94') !== 0) {
                $mobile = '94' . $mobile;
            }

            $text = urlencode($message);

            $baseurl = rtrim($baseurl, '/');
            $url = "{$baseurl}/?id={$user}&pw={$password}&to={$mobile}&text={$text}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response !== false) {
                $result = explode(":", $response);
                if (trim($result[0]) == "OK") {
                    \Log::info("SMS sent successfully to {$mobile}. Response: {$response}");
                    return true;
                } else {
                    \Log::error("SMS failed for {$mobile}. Response: {$response}");
                }
            } else {
                \Log::error("Curl error sending SMS to {$mobile}. HTTP Code: {$httpCode}");
            }

            return false;

        } catch (\Exception $e) {
            \Log::error("SMS sending failed: " . $e->getMessage());
            return false;
        }
    }

    private function getUserDetails($user)
    {
        $details = null;

        switch ($user->role) {
            case 'farmer':
                $details = DB::table('farmers')->where('user_id', $user->id)->first();
                break;
            case 'lead_farmer':
                $details = DB::table('lead_farmers')->where('user_id', $user->id)->first();
                break;
            case 'buyer':
                $details = DB::table('buyers')->where('user_id', $user->id)->first();
                break;
            case 'facilitator':
                $details = DB::table('facilitators')->where('user_id', $user->id)->first();
                break;
            case 'admin':
            case 'subadmin':
                $details = DB::table('admins')->where('user_id', $user->id)->first();
                if (!$details) {
                    $details = (object) [
                        'full_name' => $user->username,
                        'email' => $user->email
                    ];
                }
                break;
        }

        return $details;
    }

    private function sendUserCreationNotification($userId, $role, $plainPassword = null)
    {
        $user = DB::table('users')->find($userId);

        if ($user->email) {
            try {
                Mail::to($user->email)->send(new UserUpdateNotification(
                    'Account Created',
                    "Your account has been created successfully. Role: " . ucfirst($role),
                    $user
                ));
            } catch (\Exception $e) {
                \Log::error("Email sending failed: " . $e->getMessage());
            }
        }

        $mobile = $this->getUserMobile($userId, $role);

        if ($mobile) {
            // Use plain password if provided, otherwise skip password in SMS
            $passwordText = $plainPassword ? "Password: {$plainPassword}" : "";
            
            $message = "Welcome to GreenMarket! Your account has been created.\nUsername: {$user->username}\nPassword: {$passwordText}";
            
            $this->sendSms($mobile, $message);
        }
    }

    private function sendUpdateNotification($userId, $oldData, $newData)
    {
        $user = DB::table('users')->find($userId);

        $changes = [];
        foreach ($newData as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                $changes[] = "$key changed from '{$oldData[$key]}' to '$value'";
            }
        }

        if (!empty($changes) && $user->email) {
            try {
                Mail::to($user->email)->send(new UserUpdateNotification(
                    'Account Updated',
                    "Your account has been updated. Changes:\n" . implode("\n", $changes),
                    $user
                ));
            } catch (\Exception $e) {
                \Log::error("Email sending failed: " . $e->getMessage());
            }
        }

        $mobile = $this->getUserMobile($userId, $user->role);

        if ($mobile && !empty($changes)) {
            $this->sendSms($mobile, "Your account details have been updated.");
        }
    }

    private function sendDeactivationNotification($userId)
    {
        $user = DB::table('users')->find($userId);

        if ($user->email) {
            try {
                Mail::to($user->email)->send(new UserUpdateNotification(
                    'Account Deactivated',
                    "Your account has been deactivated by the administrator.",
                    $user
                ));
            } catch (\Exception $e) {
                \Log::error("Email sending failed: " . $e->getMessage());
            }
        }

        $mobile = $this->getUserMobile($userId, $user->role);

        if ($mobile) {
            $this->sendSms($mobile, "Your account has been deactivated.");
        }
    }

    private function sendSuspensionNotification($userId)
    {
        $user = DB::table('users')->find($userId);

        if ($user->email) {
            try {
                Mail::to($user->email)->send(new UserUpdateNotification(
                    'Account Suspended',
                    "Your account has been temporarily suspended.",
                    $user
                ));
            } catch (\Exception $e) {
                \Log::error("Email sending failed: " . $e->getMessage());
            }
        }

        $mobile = $this->getUserMobile($userId, $user->role);

        if ($mobile) {
            $this->sendSms($mobile, "Your account has been suspended.");
        }
    }

    private function sendPromotionNotification($userId)
    {
        $user = DB::table('users')->find($userId);

        if ($user->email) {
            try {
                Mail::to($user->email)->send(new UserUpdateNotification(
                    'Promotion to Lead Farmer',
                    "Congratulations! You have been promoted to Lead Farmer role.",
                    $user
                ));
            } catch (\Exception $e) {
                \Log::error("Email sending failed: " . $e->getMessage());
            }
        }

        $mobile = $this->getUserMobile($userId, 'lead_farmer');

        if ($mobile) {
            $this->sendSms($mobile, "Congratulations! You have been promoted to Lead Farmer.");
        }
    }

    private function sendRoleChangeNotification($userId, $newRole)
    {
        $user = DB::table('users')->find($userId);

        if ($user->email) {
            try {
                Mail::to($user->email)->send(new UserUpdateNotification(
                    'Role Updated',
                    "Your role has been changed to: " . ucfirst(str_replace('_', ' ', $newRole)),
                    $user
                ));
            } catch (\Exception $e) {
                \Log::error("Email sending failed: " . $e->getMessage());
            }
        }

        $mobile = $this->getUserMobile($userId, $newRole);

        if ($mobile) {
            $this->sendSms($mobile, "Your role has been updated to " . ucfirst(str_replace('_', ' ', $newRole)));
        }
    }

    private function getUserMobile($userId, $role)
    {
        if ($role == 'farmer' || $role == 'lead_farmer') {
            $table = $role == 'farmer' ? 'farmers' : 'lead_farmers';
            $details = DB::table($table)->where('user_id', $userId)->first();
            return $details->primary_mobile ?? null;
        } elseif ($role == 'buyer') {
            $buyer = DB::table('buyers')->where('user_id', $userId)->first();
            return $buyer->primary_mobile ?? null;
        } elseif ($role == 'facilitator') {
            $facilitator = DB::table('facilitators')->where('user_id', $userId)->first();
            return $facilitator->primary_mobile ?? null;
        } elseif ($role == 'admin' || $role == 'subadmin') {
            $admin = DB::table('admins')->where('user_id', $userId)->first();
            return $admin->phone_number ?? null;
        }

        return null;
    }

    public function destroy($id)
    {
        $user = DB::table('users')->find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        if (in_array($user->role, ['admin', 'subadmin'])) {
            return response()->json(['success' => false, 'message' => 'Cannot delete admin or subadmin accounts']);
        }

        if ($user->role == 'farmer') {
            return $this->deleteFarmer($user);
        } elseif ($user->role == 'lead_farmer') {
            return $this->deleteLeadFarmer($user);
        } elseif ($user->role == 'facilitator') {
            return $this->deleteFacilitator($user);
        } elseif ($user->role == 'buyer') {
            return $this->deleteBuyer($user);
        }

        return response()->json(['success' => false, 'message' => 'Unknown user role'], 400);
    }

    private function deleteFarmerUser($userId)
    {
        $user = DB::table('users')->find($userId);
        if (!$user) return;

        $farmer = DB::table('farmers')->where('user_id', $userId)->first();
        if (!$farmer) return;

        $this->sendSms($farmer->primary_mobile, "Your farmer account has been deleted from GreenMarket system.");

        $products = DB::table('products')->where('farmer_id', $farmer->id)->get();
        foreach ($products as $product) {
            DB::table('wishlists')->where('product_id', $product->id)->delete();
            DB::table('shopping_cart')->where('product_id', $product->id)->delete();
            DB::table('products')->where('id', $product->id)->delete();
        }

        DB::table('notifications')->where('user_id', $userId)->delete();
        DB::table('complaints')
            ->where('complainant_user_id', $userId)
            ->orWhere('against_user_id', $userId)
            ->delete();

        DB::table('farmers')->where('id', $farmer->id)->delete();
        DB::table('users')->where('id', $userId)->delete();
    }

    private function deleteFarmer($user)
    {
        $farmer = DB::table('farmers')->where('user_id', $user->id)->first();

        if (!$farmer) {
            return response()->json(['success' => false, 'message' => 'Farmer details not found'], 400);
        }

        DB::beginTransaction();

        try {
            $this->sendSms($farmer->primary_mobile, "Your farmer account has been deleted from GreenMarket system.");

            $products = DB::table('products')->where('farmer_id', $farmer->id)->get();
            foreach ($products as $product) {
                DB::table('wishlists')->where('product_id', $product->id)->delete();
                DB::table('shopping_cart')->where('product_id', $product->id)->delete();
                DB::table('products')->where('id', $product->id)->delete();
            }

            DB::table('notifications')->where('user_id', $user->id)->delete();
            DB::table('complaints')
                ->where('complainant_user_id', $user->id)
                ->orWhere('against_user_id', $user->id)
                ->delete();

            DB::table('farmers')->where('id', $farmer->id)->delete();
            DB::table('users')->where('id', $user->id)->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Farmer deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete farmer: ' . $e->getMessage()], 500);
        }
    }

    private function deleteLeadFarmer($user)
    {
        $leadFarmer = DB::table('lead_farmers')->where('user_id', $user->id)->first();

        if (!$leadFarmer) {
            return ['success' => false, 'message' => 'Lead farmer details not found'];
        }

        $farmersUnderLeadFarmer = DB::table('farmers')
            ->where('lead_farmer_id', $leadFarmer->id)
            ->count();

        if ($farmersUnderLeadFarmer > 0) {
            return [
                'success' => false,
                'requires_action' => true,
                'message' => 'This lead farmer has ' . $farmersUnderLeadFarmer . ' farmers under them. Please choose an action.',
                'lead_farmer_id' => $leadFarmer->id,
                'farmers_count' => $farmersUnderLeadFarmer
            ];
        }

        DB::beginTransaction();

        try {
            $this->sendSms($leadFarmer->primary_mobile, "Your lead farmer account has been deleted from GreenMarket system.");

            $products = DB::table('products')->where('lead_farmer_id', $leadFarmer->id)->get();
            foreach ($products as $product) {
                DB::table('wishlists')->where('product_id', $product->id)->delete();
                DB::table('shopping_cart')->where('product_id', $product->id)->delete();
            }
            DB::table('products')->where('lead_farmer_id', $leadFarmer->id)->delete();

            $orders = DB::table('orders')->where('lead_farmer_id', $leadFarmer->id)->get();
            foreach ($orders as $order) {
                DB::table('order_items')->where('order_id', $order->id)->delete();
                DB::table('invoices')->where('order_id', $order->id)->delete();
                DB::table('orders')->where('id', $order->id)->delete();
            }

            DB::table('notifications')->where('user_id', $user->id)->delete();
            DB::table('complaints')
                ->where('complainant_user_id', $user->id)
                ->orWhere('against_user_id', $user->id)
                ->delete();

            DB::table('lead_farmers')->where('id', $leadFarmer->id)->delete();
            DB::table('users')->where('id', $user->id)->delete();

            DB::commit();
            return ['success' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Failed to delete lead farmer: ' . $e->getMessage()];
        }
    }

    public function processLeadFarmerDeletion(Request $request, $id)
    {
        $user = DB::table('users')->find($id);

        if (!$user || $user->role != 'lead_farmer') {
            return response()->json(['success' => false, 'message' => 'Invalid lead farmer'], 400);
        }

        $leadFarmer = DB::table('lead_farmers')->where('user_id', $user->id)->first();
        if (!$leadFarmer) {
            return response()->json(['success' => false, 'message' => 'Lead farmer details not found'], 400);
        }

        $action = $request->input('action');
        $newLeadFarmerId = $request->input('new_lead_farmer_id');

        if ($action === 'transfer' && !$newLeadFarmerId) {
            return response()->json(['success' => false, 'message' => 'Please select a lead farmer to transfer to'], 400);
        }

        DB::beginTransaction();

        try {
            $this->sendSms($leadFarmer->primary_mobile, "Your lead farmer account has been deleted from GreenMarket system.");

            if ($action === 'delete_all') {
                $farmers = DB::table('farmers')->where('lead_farmer_id', $leadFarmer->id)->get();
                foreach ($farmers as $farmer) {
                    $this->deleteFarmerUser($farmer->user_id);
                }

                $products = DB::table('products')->where('lead_farmer_id', $leadFarmer->id)->get();
                foreach ($products as $product) {
                    DB::table('wishlists')->where('product_id', $product->id)->delete();
                    DB::table('shopping_cart')->where('product_id', $product->id)->delete();
                }
                DB::table('products')->where('lead_farmer_id', $leadFarmer->id)->delete();

                $orders = DB::table('orders')->where('lead_farmer_id', $leadFarmer->id)->get();
                foreach ($orders as $order) {
                    DB::table('order_items')->where('order_id', $order->id)->delete();
                    DB::table('invoices')->where('order_id', $order->id)->delete();
                    DB::table('orders')->where('id', $order->id)->delete();
                }

            } elseif ($action === 'transfer' && $newLeadFarmerId) {
                $newLeadFarmer = DB::table('lead_farmers')->find($newLeadFarmerId);
                if (!$newLeadFarmer) {
                    throw new \Exception('Selected lead farmer not found');
                }

                DB::table('farmers')
                    ->where('lead_farmer_id', $leadFarmer->id)
                    ->update(['lead_farmer_id' => $newLeadFarmerId]);
                    
                DB::table('products')
                    ->where('lead_farmer_id', $leadFarmer->id)
                    ->update(['lead_farmer_id' => $newLeadFarmerId]);
                    
                DB::table('orders')
                    ->where('lead_farmer_id', $leadFarmer->id)
                    ->update(['lead_farmer_id' => $newLeadFarmerId]);
            }

            DB::table('notifications')->where('user_id', $user->id)->delete();
            DB::table('complaints')
                ->where('complainant_user_id', $user->id)
                ->orWhere('against_user_id', $user->id)
                ->delete();

            DB::table('lead_farmers')->where('id', $leadFarmer->id)->delete();
            DB::table('users')->where('id', $user->id)->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Lead farmer deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lead farmer process deletion error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete lead farmer: ' . $e->getMessage()], 500);
        }
    }

    private function deleteFacilitator($user)
    {
        $facilitator = DB::table('facilitators')->where('user_id', $user->id)->first();

        if (!$facilitator) {
            return response()->json(['success' => false, 'message' => 'Facilitator details not found'], 400);
        }

        DB::beginTransaction();

        try {
            DB::table('notifications')->where('user_id', $user->id)->delete();
            DB::table('complaints')
                ->where('complainant_user_id', $user->id)
                ->orWhere('resolved_by_facilitator_id', $facilitator->id)
                ->delete();

            DB::table('facilitators')->where('id', $facilitator->id)->delete();
            DB::table('users')->where('id', $user->id)->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Facilitator deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete facilitator: ' . $e->getMessage()], 500);
        }
    }

    private function deleteBuyer($user)
    {
        $buyer = DB::table('buyers')->where('user_id', $user->id)->first();

        if (!$buyer) {
            return response()->json(['success' => false, 'message' => 'Buyer details not found'], 400);
        }

        $hasPayments = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('orders.buyer_id', $buyer->id)
            ->exists();

        if ($hasPayments) {
            return response()->json(['success' => false, 'message' => 'Cannot delete buyer with payment history. Reports will be affected.']);
        }

        DB::beginTransaction();

        try {
            $this->sendSms($buyer->primary_mobile, "Your buyer account has been deleted from GreenMarket system.");

            DB::table('shopping_cart')->where('buyer_id', $buyer->id)->delete();
            DB::table('wishlists')->where('buyer_id', $buyer->id)->delete();
            DB::table('buyer_product_requests')->where('buyer_id', $buyer->id)->delete();
            DB::table('notifications')->where('user_id', $user->id)->delete();
            DB::table('complaints')
                ->where('complainant_user_id', $user->id)
                ->orWhere('against_user_id', $user->id)
                ->delete();

            $orders = DB::table('orders')->where('buyer_id', $buyer->id)->get();
            foreach ($orders as $order) {
                DB::table('order_items')->where('order_id', $order->id)->delete();
                DB::table('invoices')->where('order_id', $order->id)->delete();
                DB::table('orders')->where('id', $order->id)->delete();
            }

            DB::table('buyers')->where('id', $buyer->id)->delete();
            DB::table('users')->where('id', $user->id)->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Buyer deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete buyer: ' . $e->getMessage()], 500);
        }
    }

    public function getLeadFarmersForTransfer()
    {
        $leadFarmers = DB::table('lead_farmers')
            ->select('id', 'name', 'group_name')
            ->get();

        return response()->json([
            'success' => true,
            'leadFarmers' => $leadFarmers
        ]);
    }
}
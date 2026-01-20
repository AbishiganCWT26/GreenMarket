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
        $query = DB::table('users')
            ->leftJoin('farmers', 'users.id', '=', 'farmers.user_id')
            ->leftJoin('lead_farmers', 'users.id', '=', 'lead_farmers.user_id')
            ->leftJoin('buyers', 'users.id', '=', 'buyers.user_id')
            ->leftJoin('facilitators', 'users.id', '=', 'facilitators.user_id')
            ->select(
                'users.*',
                'farmers.name as farmer_name',
                'farmers.nic_no as farmer_nic',
                'lead_farmers.name as lead_farmer_name',
                'lead_farmers.nic_no as lead_farmer_nic',
                'buyers.name as buyer_name',
                'buyers.nic_no as buyer_nic',
                'facilitators.name as facilitator_name',
                'facilitators.nic_no as facilitator_nic'
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
                  ->orWhere('farmers.nic_no', 'ILIKE', $search)
                  ->orWhere('lead_farmers.nic_no', 'ILIKE', $search)
                  ->orWhere('buyers.nic_no', 'ILIKE', $search)
                  ->orWhere('facilitators.nic_no', 'ILIKE', $search);
            });
        }

        $usersPaginator = $query->paginate(12);

        if ($request->ajax()) {
            $usersWithDetails = $usersPaginator->map(function($user) {
                return $this->getFullUserDetails($user);
            });

            return response()->json([
                'html' => view('admin.users.partials.user_cards', [
                    'users' => $usersWithDetails
                ])->render(),
                'pagination' => view('vendor.pagination.simple-unique', ['paginator' => $usersPaginator])->render(),
                'total' => $usersPaginator->total()
            ]);
        }

        $usersWithDetails = $usersPaginator->map(function($user) {
            return $this->getFullUserDetails($user);
        });

        return view('admin.users.index', [
            'users' => $usersWithDetails,
            'paginator' => $usersPaginator,
            'totalUsers' => $usersPaginator->total()
        ]);
    }

    private function getFullUserDetails($user)
    {
        switch($user->role) {
            case 'farmer':
                $user->display_name = $user->farmer_name ?? $user->username;
                $user->contact_number = DB::table('farmers')->where('user_id', $user->id)->value('primary_mobile') ?? 'N/A';
                $user->nic_number = $user->farmer_nic ?? '';
                break;

            case 'lead_farmer':
                $user->display_name = $user->lead_farmer_name ?? $user->username;
                $user->contact_number = DB::table('lead_farmers')->where('user_id', $user->id)->value('primary_mobile') ?? 'N/A';
                $user->nic_number = $user->lead_farmer_nic ?? '';
                break;

            case 'buyer':
                $user->display_name = $user->buyer_name ?? $user->username;
                $user->contact_number = DB::table('buyers')->where('user_id', $user->id)->value('primary_mobile') ?? 'N/A';
                $user->nic_number = $user->buyer_nic ?? '';
                break;

            case 'facilitator':
                $user->display_name = $user->facilitator_name ?? $user->username;
                $user->contact_number = DB::table('facilitators')->where('user_id', $user->id)->value('primary_mobile') ?? 'N/A';
                $user->nic_number = $user->facilitator_nic ?? '';
                break;

            case 'admin':
            case 'subadmin':
                $user->display_name = $user->username;
                $user->contact_number = 'N/A';
                $user->nic_number = '';
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

        DB::beginTransaction();

        try {
            $userId = DB::table('users')->insertGetId([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['user_type'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($validated['user_type'] == 'farmer' || $validated['user_type'] == 'lead_farmer') {
                $farmerData = [
                    'user_id' => $userId,
                    'name' => $request->name,
                    'nic_no' => $request->nic_no,
                    'primary_mobile' => $request->primary_mobile,
                    'whatsapp_number' => $request->whatsapp_number,
                    'email' => $validated['email'],
                    'residential_address' => $request->residential_address,
                    'grama_niladhari_division' => $request->grama_niladhari_division,
                    'preferred_payment' => $request->preferred_payment ?? 'bank',
                    'district' => 'Colombo',
                    'is_active' => true,
                    'account_number' => $request->account_number,
                    'account_holder_name' => $request->account_holder_name,
                    'bank_name' => $request->bank_name,
                    'bank_branch' => $request->bank_branch,
                    'ezcash_mobile' => $request->ezcash_mobile,
                    'mcash_mobile' => $request->mcash_mobile,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if ($validated['user_type'] == 'lead_farmer') {
                    $leadFarmerId = DB::table('lead_farmers')->insertGetId(array_merge($farmerData, [
                        'group_name' => $request->group_name,
                        'group_number' => $request->group_number
                    ]));

                    DB::table('farmers')->insert(array_merge($farmerData, [
                        'lead_farmer_id' => $leadFarmerId
                    ]));
                } else {
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
                    'primary_mobile' => $request->primary_mobile,
                    'whatsapp_number' => $request->whatsapp_number,
                    'email' => $validated['email'],
                    'assigned_division' => $request->assigned_division,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            $this->sendUserCreationNotification($userId, $validated['user_type']);

            return response()->json(['success' => true, 'message' => 'User created successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create user: ' . $e->getMessage()], 500);
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
            }

            DB::commit();

            $newData = (array) DB::table('users')->find($id);
            $this->sendUpdateNotification($id, $oldData, $newData);

            return response()->json(['success' => true, 'message' => 'User updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update user: ' . $e->getMessage()], 500);
        }
    }

    private function handleFarmerRoleChange($user, $validated, $request, $userId)
    {
        DB::beginTransaction();

        try {
            if ($user->role == 'farmer' && $validated['role'] == 'lead_farmer') {
                $farmer = DB::table('farmers')->where('user_id', $userId)->first();

                if ($farmer) {
                    $leadFarmerId = DB::table('lead_farmers')->insertGetId([
                        'user_id' => $userId,
                        'name' => $farmer->name,
                        'nic_no' => $farmer->nic_no,
                        'primary_mobile' => $farmer->primary_mobile,
                        'whatsapp_number' => $farmer->whatsapp_number,
                        'email' => $farmer->email,
                        'residential_address' => $farmer->residential_address,
                        'grama_niladhari_division' => $farmer->grama_niladhari_division,
                        'group_name' => $request->group_name ?? ($farmer->name . "'s Group"),
                        'group_number' => $request->group_number ?? ('GRP-' . strtoupper(Str::random(6))),
                        'preferred_payment' => $farmer->preferred_payment,
                        'account_number' => $farmer->account_number,
                        'account_holder_name' => $farmer->account_holder_name,
                        'bank_name' => $farmer->bank_name,
                        'bank_branch' => $farmer->bank_branch,
                        'ezcash_mobile' => $farmer->ezcash_mobile,
                        'mcash_mobile' => $farmer->mcash_mobile,
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
            return response()->json(['success' => false, 'message' => 'Failed to change role: ' . $e->getMessage()], 500);
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

        foreach ($paymentFields as $field) {
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
                'email' => $userRecord->email,
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
            DB::table('facilitators')
                ->where('user_id', $userId)
                ->update([
                    'updated_at' => now()
                ]);
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

            $leadFarmerId = DB::table('lead_farmers')->insertGetId([
                'user_id' => $id,
                'name' => $farmer->name,
                'nic_no' => $farmer->nic_no,
                'primary_mobile' => $farmer->primary_mobile,
                'whatsapp_number' => $farmer->whatsapp_number,
                'email' => $farmer->email,
                'residential_address' => $farmer->residential_address,
                'grama_niladhari_division' => $farmer->grama_niladhari_division,
                'group_name' => $farmer->name . "'s Group",
                'group_number' => 'GRP-' . strtoupper(Str::random(6)),
                'preferred_payment' => $farmer->preferred_payment,
                'account_number' => $farmer->account_number,
                'account_holder_name' => $farmer->account_holder_name,
                'bank_name' => $farmer->bank_name,
                'bank_branch' => $farmer->bank_branch,
                'ezcash_mobile' => $farmer->ezcash_mobile,
                'mcash_mobile' => $farmer->mcash_mobile,
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
            return response()->json(['success' => false, 'message' => 'Failed to promote farmer: ' . $e->getMessage()], 500);
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
                \Log::info("SMS OTP for {$mobile}: {$otp}");
                return true;
            }

            $text = urlencode("Your OTP for payment details update is: $otp. Valid for 5 minutes.");

            $url = "$baseurl/?id=$user&pw=$password&to=$mobile&text=$text";

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
                    return true;
                }
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
                \Log::info("SMS for {$mobile}: {$message}");
                return true;
            }

            $text = urlencode($message);

            $url = "$baseurl/?id=$user&pw=$password&to=$mobile&text=$text";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            curl_close($ch);

            return $response !== false;

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
                $details = (object) [
                    'name' => $user->username,
                    'email' => $user->email
                ];
                break;
        }

        return $details;
    }

    private function sendUserCreationNotification($userId, $role)
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
            $this->sendSms($mobile, "Welcome to GreenMarket! Your account has been created. Username: {$user->username}");
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
        }

        return null;
    }
}

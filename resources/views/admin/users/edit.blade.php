@extends('admin.layouts.admin_master')

@section('title', 'Edit User')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Admin/edit-user-management.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="{{ asset('js/gn-data.js') }}"></script>
@endsection

@section('content')
<div class="edit-user-container">
	<div class="page-header">
		<div class="header-content">
			<div class="back-btn-wrapper">
				<a href="{{ route('admin.users.index') }}" class="btn-back">
					<i class="fas fa-arrow-left"></i>
				</a>
			</div>
			<div class="header-main">
				<h1><i class="fas fa-user-edit"></i> Edit User</h1>
				<p>Update user information and account settings</p>
			</div>
			<div class="user-status-badge">
				@if($user->is_active)
				<span class="status-active">
					<i class="fas fa-circle"></i> Active
				</span>
				@else
				<span class="status-inactive">
					<i class="fas fa-circle"></i> Inactive
				</span>
				@endif
			</div>
		</div>
		<div class="user-quick-info">
			<div class="user-details">
				<h3>{{ $user->username }}</h3>
				<div class="user-meta">
					<span class="role-badge role-{{ $user->role }}">
						<i class="fas fa-{{ $user->role == 'admin' ? 'crown' : ($user->role == 'farmer' ? 'tractor' : ($user->role == 'buyer' ? 'shopping-cart' : 'user')) }}"></i>
						{{ ucfirst(str_replace('_', ' ', $user->role)) }}
					</span>
					<span class="user-id">ID: HGH{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
				</div>
			</div>
		</div>
	</div>

	<div class="edit-form-wrapper">
		<form method="POST" action="{{ route('admin.users.update', $user->id) }}" id="editUserForm" class="edit-form">
			@csrf
			@method('PUT')

			<div class="form-sections">
				<div class="form-section">
					<div class="section-header">
						<div class="section-icon">
							<i class="fas fa-user-circle"></i>
						</div>
						<h3>Basic Information</h3>
					</div>
					<div class="form-fields">
						<div class="form-row">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-user"></i> Username
								</label>
								<input type="text" name="username" class="form-input" value="{{ $user->username }}" required>
							</div>
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-envelope"></i> Email
								</label>
								<input type="email" name="email" class="form-input" value="{{ $user->email }}">
							</div>
						</div>

						<div class="form-row">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-user-tag"></i> Role
								</label>
								<input type="text" class="form-input" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" readonly>
								<input type="hidden" name="role" value="{{ $user->role }}">
								<small class="form-note">User role is not editable</small>
							</div>
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-toggle-on"></i> Account Status
								</label>
								<select name="is_active" class="form-select" {{ $user->id == Auth::id() ? 'disabled' : '' }}>
									<option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
									<option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
								</select>
								@if($user->id == Auth::id())
								<small class="form-note">You cannot deactivate your own account</small>
								@endif
							</div>
						</div>
					</div>
				</div>

				@php
					$farmerData = DB::table('farmers')->where('user_id', $user->id)->first();
					$leadFarmerData = DB::table('lead_farmers')->where('user_id', $user->id)->first();
					$buyerData = DB::table('buyers')->where('user_id', $user->id)->first();
					$facilitatorData = DB::table('facilitators')->where('user_id', $user->id)->first();
					$facilitatorAssignments = $facilitatorData ? DB::table('facilitator_assignments')->where('facilitator_id', $facilitatorData->id)->get() : collect();
					
					$userDetails = ($user->role == 'farmer' ? $farmerData : ($user->role == 'lead_farmer' ? $leadFarmerData : null)) ?? (object)[];
					
					if (!isset($userDetails->preferred_payment)) $userDetails->preferred_payment = 'bank';
					if (!isset($userDetails->nic_no)) $userDetails->nic_no = '';
					if (!isset($userDetails->primary_mobile)) $userDetails->primary_mobile = '';
					if (!isset($userDetails->whatsapp_number)) $userDetails->whatsapp_number = '';
					if (!isset($userDetails->district)) $userDetails->district = '';
					if (!isset($userDetails->residential_address)) $userDetails->residential_address = '';
					if (!isset($userDetails->grama_niladhari_division)) $userDetails->grama_niladhari_division = '';
					if (!isset($userDetails->account_number)) $userDetails->account_number = '';
					if (!isset($userDetails->account_holder_name)) $userDetails->account_holder_name = '';
					if (!isset($userDetails->bank_name)) $userDetails->bank_name = '';
					if (!isset($userDetails->bank_branch)) $userDetails->bank_branch = '';
					if (!isset($userDetails->ezcash_mobile)) $userDetails->ezcash_mobile = '';
					if (!isset($userDetails->mcash_mobile)) $userDetails->mcash_mobile = '';
					if (!isset($userDetails->group_name)) $userDetails->group_name = '';
					if (!isset($userDetails->group_number)) $userDetails->group_number = '';
				@endphp

				@if(in_array($user->role, ['farmer', 'lead_farmer']))
				<div id="farmer-sections">
					<div class="form-section profile-section">
						<div class="section-header">
							<div class="section-icon">
								<i class="fas fa-address-card"></i>
							</div>
							<div class="section-title">
								<h3>Profile Information</h3>
								<p class="section-subtitle">Personal and contact details</p>
							</div>
						</div>
						<div class="form-fields">
							<div class="form-row">
								<div class="form-group" style="flex: 1 1 100%;">
									<label class="form-label">
										<i class="fas fa-user"></i> Full Name
									</label>
									<input type="text" name="name" class="form-input" value="{{ $userDetails->name ?? '' }}" {{ in_array($user->role, ['farmer', 'lead_farmer']) ? 'required' : '' }}>
								</div>

								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-id-card"></i> NIC Number
									</label>
									<div class="nic-input-wrapper">
										<input type="text" name="nic_no" id="farmer_nic" class="form-input" value="{{ $userDetails->nic_no ?? '' }}" {{ in_array($user->role, ['farmer', 'lead_farmer']) ? 'required' : '' }}>
										<div id="farmer_nic_status" class="nic-status mt-1" style="font-size: 11px; min-height: 16px;"></div>
									</div>
									<small class="form-note mt-1" style="display: block; color: var(--text-muted);">
										<i class="fas fa-info-circle"></i> Updating NIC requires OTP verification
									</small>
								</div>
							</div>

							<div class="form-row">
								
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-phone"></i> Primary Mobile
									</label>
									<input type="text" name="primary_mobile" class="form-input" value="{{ $userDetails->primary_mobile ?? '' }}" {{ in_array($user->role, ['farmer', 'lead_farmer']) ? 'required' : '' }}>
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fab fa-whatsapp"></i> WhatsApp Number
									</label>
									<input type="text" name="whatsapp_number" class="form-input" value="{{ $userDetails->whatsapp_number ?? '' }}">
								</div>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-map-marker-alt"></i> District
									</label>
									<select name="district" id="farmer_district" class="form-select">
										<option value="" disabled>Select District</option>
										<option value="Ampara" {{ ($userDetails->district ?? '') == 'Ampara' ? 'selected' : '' }}>Ampara</option>
										<option value="Anuradhapura" {{ ($userDetails->district ?? '') == 'Anuradhapura' ? 'selected' : '' }}>Anuradhapura</option>
										<option value="Badulla" {{ ($userDetails->district ?? '') == 'Badulla' ? 'selected' : '' }}>Badulla</option>
										<option value="Batticaloa" {{ ($userDetails->district ?? '') == 'Batticaloa' ? 'selected' : '' }}>Batticaloa</option>
										<option value="Colombo" {{ ($userDetails->district ?? '') == 'Colombo' ? 'selected' : '' }}>Colombo</option>
										<option value="Galle" {{ ($userDetails->district ?? '') == 'Galle' ? 'selected' : '' }}>Galle</option>
										<option value="Gampaha" {{ ($userDetails->district ?? '') == 'Gampaha' ? 'selected' : '' }}>Gampaha</option>
										<option value="Hambantota" {{ ($userDetails->district ?? '') == 'Hambantota' ? 'selected' : '' }}>Hambantota</option>
										<option value="Jaffna" {{ ($userDetails->district ?? '') == 'Jaffna' ? 'selected' : '' }}>Jaffna</option>
										<option value="Kalutara" {{ ($userDetails->district ?? '') == 'Kalutara' ? 'selected' : '' }}>Kalutara</option>
										<option value="Kandy" {{ ($userDetails->district ?? '') == 'Kandy' ? 'selected' : '' }}>Kandy</option>
										<option value="Kegalle" {{ ($userDetails->district ?? '') == 'Kegalle' ? 'selected' : '' }}>Kegalle</option>
										<option value="Kilinochchi" {{ ($userDetails->district ?? '') == 'Kilinochchi' ? 'selected' : '' }}>Kilinochchi</option>
										<option value="Kurunegala" {{ ($userDetails->district ?? '') == 'Kurunegala' ? 'selected' : '' }}>Kurunegala</option>
										<option value="Mannar" {{ ($userDetails->district ?? '') == 'Mannar' ? 'selected' : '' }}>Mannar</option>
										<option value="Matale" {{ ($userDetails->district ?? '') == 'Matale' ? 'selected' : '' }}>Matale</option>
										<option value="Matara" {{ ($userDetails->district ?? '') == 'Matara' ? 'selected' : '' }}>Matara</option>
										<option value="Moneragala" {{ ($userDetails->district ?? '') == 'Moneragala' ? 'selected' : '' }}>Moneragala</option>
										<option value="Mullaitivu" {{ ($userDetails->district ?? '') == 'Mullaitivu' ? 'selected' : '' }}>Mullaitivu</option>
										<option value="Nuwara Eliya" {{ ($userDetails->district ?? '') == 'Nuwara Eliya' ? 'selected' : '' }}>Nuwara Eliya</option>
										<option value="Polonnaruwa" {{ ($userDetails->district ?? '') == 'Polonnaruwa' ? 'selected' : '' }}>Polonnaruwa</option>
										<option value="Puttalam" {{ ($userDetails->district ?? '') == 'Puttalam' ? 'selected' : '' }}>Puttalam</option>
										<option value="Ratnapura" {{ ($userDetails->district ?? '') == 'Ratnapura' ? 'selected' : '' }}>Ratnapura</option>
										<option value="Trincomalee" {{ ($userDetails->district ?? '') == 'Trincomalee' ? 'selected' : '' }}>Trincomalee</option>
										<option value="Vavuniya" {{ ($userDetails->district ?? '') == 'Vavuniya' ? 'selected' : '' }}>Vavuniya</option>
									</select>
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-building"></i> Divisional Secretariat
									</label>
									<select name="divisional_secretariat" id="farmer_ds" class="form-select">
										<option value="{{ $userDetails->divisional_secretariat ?? '' }}" selected>{{ $userDetails->divisional_secretariat ?? 'Select DS First' }}</option>
									</select>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-map"></i> Grama Niladhari Division
									</label>
									<select name="grama_niladhari_division" id="farmer_gn" class="form-select">
										<option value="{{ $userDetails->grama_niladhari_division ?? '' }}" data-code="{{ $userDetails->gn_division_code ?? '' }}" selected>{{ $userDetails->grama_niladhari_division ?? 'Select GN First' }}</option>
									</select>
								</div>
								<div class="form-group" style="padding-left: 0;">
									<label class="form-label">GN Code</label>
									<input type="text" name="gn_division_code" id="farmer_gn_code" class="form-input" value="{{ $userDetails->gn_division_code ?? '' }}" readonly>
								</div>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-home"></i> Residential Address
								</label>
								<textarea name="residential_address" class="form-input" rows="2" {{ in_array($user->role, ['farmer', 'lead_farmer']) ? 'required' : '' }}>{{ $userDetails->residential_address ?? '' }}</textarea>
							</div>
						</div>
					</div>
					<div class="form-section payment-section">
						<div class="section-header">
							<div class="section-icon">
								<i class="fas fa-money-bill-wave"></i>
							</div>
							<div class="section-title">
								<h3>Payment Details</h3>
								<p class="section-subtitle">Changes to payment details require OTP verification</p>
							</div>
							<div class="section-badge">
								<span class="secure-badge">
									<i class="fas fa-shield-alt"></i> Secure
								</span>
							</div>
						</div>
						<div class="form-fields">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-credit-card"></i> Preferred Payment Method
								</label>
								<select name="preferred_payment" class="form-select" {{ $user->role == 'lead_farmer' ? 'disabled' : '' }}>
									<option value="bank" {{ ($userDetails->preferred_payment ?? 'bank') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
									@if($user->role != 'lead_farmer')
									<option value="ezcash" {{ ($userDetails->preferred_payment ?? '') == 'ezcash' ? 'selected' : '' }}>Ez Cash</option>
									<option value="mcash" {{ ($userDetails->preferred_payment ?? '') == 'mcash' ? 'selected' : '' }}>mCash</option>
									<option value="all" {{ ($userDetails->preferred_payment ?? '') == 'all' ? 'selected' : '' }}>All Methods</option>
									@endif
								</select>
								@if($user->role == 'lead_farmer')
								<input type="hidden" name="preferred_payment" value="bank">
								<small class="form-note">Lead farmers are only allowed bank transfer payment method</small>
								@endif
							</div>

							<div id="bank-payment-fields" style="{{ in_array($userDetails->preferred_payment, ['bank', 'all']) ? '' : 'display: none;' }}">
								<div class="form-row">
									<div class="form-group">
										<label class="form-label">
											<i class="fas fa-university"></i> Bank Account Number
										</label>
										<input type="text" name="account_number" class="form-input" value="{{ $userDetails->account_number ?? '' }}">
									</div>
									<div class="form-group">
										<label class="form-label">
											<i class="fas fa-user-tie"></i> Account Holder Name
										</label>
										<input type="text" name="account_holder_name" class="form-input" value="{{ $userDetails->account_holder_name ?? '' }}">
									</div>
								</div>

								<div class="form-row">
									<div class="form-group">
										<label class="form-label">
											<i class="fas fa-landmark"></i> Bank Name
										</label>
										<input type="text" name="bank_name" class="form-input" value="{{ $userDetails->bank_name ?? '' }}">
									</div>
									<div class="form-group">
										<label class="form-label">
											<i class="fas fa-map-marker-alt"></i> Bank Branch
										</label>
										<input type="text" name="bank_branch" class="form-input" value="{{ $userDetails->bank_branch ?? '' }}">
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group" id="ezcash-payment-fields" style="{{ in_array($userDetails->preferred_payment, ['ezcash', 'all']) ? '' : 'display: none;' }}">
									<label class="form-label">
										<i class="fas fa-mobile-alt"></i> Ez Cash Number
									</label>
									<input type="text" name="ezcash_mobile" id="farmer_ezcash" class="form-input" value="{{ $userDetails->ezcash_mobile ?? '' }}">
									<div id="ezcash_error" style="color: #EF4444; font-size: 11px; display: none;">Must start with 074, 076, or 077</div>
								</div>
								<div class="form-group" id="mcash-payment-fields" style="{{ in_array($userDetails->preferred_payment, ['mcash', 'all']) ? '' : 'display: none;' }}">
									<label class="form-label">
										<i class="fas fa-phone-alt"></i> mCash Number
									</label>
									<input type="text" name="mcash_mobile" id="farmer_mcash" class="form-input" value="{{ $userDetails->mcash_mobile ?? '' }}">
									<div id="mcash_error" style="color: #EF4444; font-size: 11px; display: none;">Must start with 070 or 071</div>
								</div>
							</div>

							<div id="lead-farmer-only-fields" style="{{ $user->role == 'lead_farmer' ? '' : 'display: none;' }}">
								<div class="form-row">
									<div class="form-group">
										<label class="form-label">
											<i class="fas fa-users"></i> Group Name
										</label>
										<input type="text" name="group_name" class="form-input" value="{{ $userDetails->group_name ?? '' }}">
									</div>
									<div class="form-group">
										<label class="form-label">
											<i class="fas fa-hashtag"></i> Group Number
										</label>
										<input type="text" name="group_number" class="form-input" value="{{ $userDetails->group_number ?? '' }}">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				@endif

				@if($user->role == 'buyer')
				<div id="buyer-sections">
					<div class="form-section business-section">
						<div class="section-header">
							<div class="section-icon">
								<i class="fas fa-briefcase"></i>
							</div>
							<h3>Business Information</h3>
						</div>
						<div class="form-fields">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-store"></i> Business Name
								</label>
								<input type="text" name="business_name" class="form-input" value="{{ $buyerData->business_name ?? '' }}">
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-building"></i> Business Type
									</label>
									<select name="business_type" class="form-select">
										<option value="individual" {{ ($buyerData->business_type ?? '') == 'individual' ? 'selected' : '' }}>Individual</option>
										<option value="restaurant" {{ ($buyerData->business_type ?? '') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
										<option value="hotel" {{ ($buyerData->business_type ?? '') == 'hotel' ? 'selected' : '' }}>Hotel</option>
										<option value="retailer" {{ ($buyerData->business_type ?? '') == 'retailer' ? 'selected' : '' }}>Retailer</option>
										<option value="wholesaler" {{ ($buyerData->business_type ?? '') == 'wholesaler' ? 'selected' : '' }}>Wholesaler</option>
									</select>
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-phone"></i> Contact Number
									</label>
									<input type="text" class="form-input" value="{{ $buyerData->primary_mobile ?? '' }}" readonly>
									<small class="form-note">Contact number cannot be changed here</small>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-id-card"></i> NIC Number
									</label>
									<div class="nic-input-wrapper">
										<input type="text" name="buyer_nic_no" id="buyer_nic" class="form-input" value="{{ $buyerData->nic_no ?? '' }}">
										<div id="buyer_nic_status" class="nic-status mt-1" style="font-size: 11px; min-height: 16px;"></div>
									</div>
									<small class="form-note mt-1" style="display: block; color: var(--text-muted);">
										<i class="fas fa-info-circle"></i> Updating NIC requires OTP verification
									</small>
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fab fa-whatsapp"></i> WhatsApp Number
									</label>
									<input type="text" name="whatsapp_number" class="form-input" value="{{ $buyerData->whatsapp_number ?? '' }}">
								</div>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-map-marker-alt"></i> District
									</label>
									<select name="district" class="form-select">
										<option value="" disabled {{ empty($buyerData->district) ? 'selected' : '' }}>Select District</option>
										@php
											$districts = ['Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara', 'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar', 'Matale', 'Matara', 'Moneragala', 'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'];
										@endphp
										@foreach($districts as $d)
											<option value="{{ $d }}" {{ ($buyerData->district ?? '') == $d ? 'selected' : '' }}>{{ $d }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-home"></i> Residential Address
								</label>
								<textarea name="residential_address" class="form-input" rows="2" {{ $user->role == 'buyer' ? 'required' : '' }}>{{ $buyerData->residential_address ?? '' }}</textarea>
							</div>
						</div>
					</div>
				</div>
				@endif

				@if($user->role == 'facilitator')
				<div id="facilitator-sections">
					<div class="form-section facilitator-section">
						<div class="section-header">
							<div class="section-icon">
								<i class="fas fa-user-tie"></i>
							</div>
							<h3>Facilitator Details</h3>
						</div>
						<div class="form-fields">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-id-card"></i> NIC Number
								</label>
								<div class="nic-input-wrapper">
									<input type="text" name="facilitator_nic_no" id="facilitator_nic" class="form-input" value="{{ $facilitatorData->nic_no ?? '' }}">
									<div id="facilitator_nic_status" class="nic-status mt-1" style="font-size: 11px; min-height: 16px;"></div>
								</div>
								<small class="form-note mt-1" style="display: block; color: var(--text-muted);">
									<i class="fas fa-info-circle"></i> Updating NIC requires OTP verification
								</small>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-phone"></i> Primary Mobile
									</label>
									<input type="text" name="facilitator_primary_mobile" id="facilitator_mobile" class="form-input" value="{{ $facilitatorData->primary_mobile ?? '' }}">
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fab fa-whatsapp"></i> WhatsApp Number
									</label>
									<input type="text" name="facilitator_whatsapp" id="facilitator_whatsapp" class="form-input" value="{{ $facilitatorData->whatsapp_number ?? '' }}">
								</div>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-map-marker-alt"></i> District <span class="required">*</span>
								</label>
								<select id="facilitator_district" name="district" class="form-select" {{ $user->role == 'facilitator' ? 'required' : '' }}>
									<option value="" disabled {{ !isset($facilitatorAssignments) && empty($facilitatorData->assigned_division) ? 'selected' : '' }}>Select District</option>
									@php
										$districts = ['Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara', 'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar', 'Matale', 'Matara', 'Moneragala', 'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'];
										$currentDist = (isset($facilitatorAssignments) && $facilitatorAssignments->count() > 0) ? $facilitatorAssignments->first()->district : ($facilitatorData->assigned_division ?? '');
									@endphp
									@foreach($districts as $d)
										<option value="{{ $d }}" {{ $currentDist == $d ? 'selected' : '' }}>{{ $d }}</option>
									@endforeach
								</select>
							</div>

							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-th-list"></i> Assigned Divisions (DS & GN)
								</label>
								<div id="facilitator-assignments">
									@if($facilitatorAssignments->count() > 0)
										@foreach($facilitatorAssignments as $index => $assignment)
										<div class="assignment-item" style="border: 1px solid var(--border-color); padding: 15px; margin-bottom: 15px; border-radius: 8px; position: relative; background: var(--bg-light);">
											@if($index > 0)
											<button type="button" class="remove-assignment" style="position: absolute; right: 10px; top: 10px; border: none; background: none; color: #ef4444; font-size: 1.2rem;">&times;</button>
											@endif
											<div class="form-row">
												<div class="form-group">
													<label class="form-label">DS Division</label>
													<select class="form-select assign-ds" {{ $user->role == 'facilitator' ? 'required' : '' }}>
														<option value="{{ $assignment->divisional_secretariat }}" selected>{{ $assignment->divisional_secretariat }}</option>
													</select>
												</div>
												<div class="form-group">
													<label class="form-label">GN Division</label>
													<select class="form-select assign-gn" {{ $user->role == 'facilitator' ? 'required' : '' }}>
														<option value="{{ $assignment->gn_division }}" data-code="{{ $assignment->gn_division_code }}" selected>{{ $assignment->gn_division }}</option>
													</select>
												</div>
											</div>
											<div class="form-group" style="margin-top: 10px;">
												<label class="form-label">GN Code</label>
												<input type="text" class="form-input assign-code" value="{{ $assignment->gn_division_code }}" readonly>
											</div>
										</div>
										@endforeach
									@else
										<div class="assignment-item" style="border: 1px solid var(--border-color); padding: 15px; margin-bottom: 15px; border-radius: 8px; background: var(--bg-light);">
											<div class="form-row">
												<div class="form-group">
													<label class="form-label">DS Division</label>
													<select class="form-select assign-ds" disabled>
														<option value="" disabled selected>Select District First</option>
													</select>
												</div>
												<div class="form-group">
													<label class="form-label">GN Division</label>
													<select class="form-select assign-gn" disabled>
														<option value="" disabled selected>Select DS First</option>
													</select>
												</div>
											</div>
											<div class="form-group" style="margin-top: 10px;">
												<label class="form-label">GN Code</label>
												<input type="text" class="form-input assign-code" readonly>
											</div>
										</div>
									@endif
								</div>
								<button type="button" id="add-assignment" class="btn-secondary" style="font-size: 0.85rem; padding: 8px 15px;">
									<i class="fas fa-plus"></i> Add Another Division
								</button>
							</div>
						</div>
					</div>
				</div>
				@endif
			</div>

			<div class="form-actions">
				<button type="button" class="btn-secondary btn-cancel">
					<i class="fas fa-times"></i> Cancel
				</button>
				<button type="submit" class="btn-primary btn-save">
					<i class="fas fa-save"></i> Save Changes
				</button>
			</div>
		</form>
	</div>
</div>

<div class="otp-modal-overlay" id="otpModal">
	<div class="otp-modal-dialog">
		<div class="otp-modal-content">
			<div class="otp-modal-header">
				<div class="otp-modal-icon">
					<i class="fas fa-shield-alt"></i>
				</div>
				<div class="otp-modal-title">
					<h3>OTP Verification Required</h3>
					<p>Enter the OTP sent to user's mobile number</p>
				</div>
				<button class="otp-close-btn" id="closeOtpModal">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<div class="otp-modal-body">
				<div class="otp-inputs">
					<input type="text" maxlength="1" class="otp-input" data-index="1">
					<input type="text" maxlength="1" class="otp-input" data-index="2">
					<input type="text" maxlength="1" class="otp-input" data-index="3">
					<input type="text" maxlength="1" class="otp-input" data-index="4">
					<input type="text" maxlength="1" class="otp-input" data-index="5">
					<input type="text" maxlength="1" class="otp-input" data-index="6">
				</div>

				<div class="otp-timer">
					<i class="fas fa-clock"></i>
					<span>OTP expires in: <strong id="otpTimer">05:00</strong></span>
				</div>

				<div class="otp-actions">
					<button class="btn-otp-secondary" id="resendOtpBtn">
						<i class="fas fa-redo"></i> Resend OTP
					</button>
					<button class="btn-otp-primary" id="verifyOtpBtn">
						<i class="fas fa-check"></i> Verify & Save
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function formatNIC(nic) {
	if (!nic) return '';
	nic = nic.trim().toUpperCase();
	if (nic.length === 10 && /^[0-9]{9}[VX]$/.test(nic)) {
		return nic;
	}
	if (nic.length === 12 && /^[0-9]{12}$/.test(nic)) {
		return nic;
	}
	return nic;
}

function validateNIC(nic) {
	if (!nic) return false;
	nic = nic.trim().toUpperCase();
	const oldNicPattern = /^[0-9]{9}[VX]$/;
	const newNicPattern = /^[0-9]{12}$/;
	if (oldNicPattern.test(nic)) {
		const year = parseInt(nic.substr(0, 2));
		const days = parseInt(nic.substr(2, 3));
		if (days > 500) {
			return days <= 866;
		}
		return days > 0 && days <= 366;
	}
	if (newNicPattern.test(nic)) {
		const year = parseInt(nic.substr(0, 4));
		const days = parseInt(nic.substr(4, 3));
		if (days > 500) {
			return days <= 866;
		}
		return year >= 1900 && year <= 2100 && days > 0 && days <= 366;
	}
	return false;
}

$(document).ready(function() {
	// Safety check for gnData
	if (typeof gnData === 'undefined') {
		console.warn('gnData is not defined. Location dropdowns may not work properly.');
		window.gnData = {};
	}

	let otpTimer = null;
	let timeLeft = 300;
	let otpVerified = false;
	let formDataToSubmit = null;

	$('select[name="preferred_payment"]').change(function() {
		const selectedMethod = $(this).val();
		
		$('#bank-payment-fields').hide();
		$('#ezcash-payment-fields').hide();
		$('#mcash-payment-fields').hide();
		
		if (selectedMethod === 'bank') {
			$('#bank-payment-fields').show();
		} else if (selectedMethod === 'ezcash') {
			$('#ezcash-payment-fields').show();
		} else if (selectedMethod === 'mcash') {
			$('#mcash-payment-fields').show();
		} else if (selectedMethod === 'all') {
			$('#bank-payment-fields').show();
			$('#ezcash-payment-fields').show();
			$('#mcash-payment-fields').show();
		}
	});

	// NIC Validation
	const setupNICListener = (selector, statusSelector) => {
		$(selector).on('input', function() {
			const originalVal = $(this).val();
			const formattedVal = formatNIC(originalVal);
			if (originalVal !== formattedVal) {
				$(this).val(formattedVal);
			}
			
			const nic = formattedVal.trim().toUpperCase();
			const statusDiv = $(statusSelector);
			
			if (!nic) {
				statusDiv.html('');
			} else if (validateNIC(nic)) {
				statusDiv.html('<span style="color: #10B981"><i class="fas fa-check-circle"></i> Valid NIC format</span>');
			} else {
				statusDiv.html('<span style="color: #EF4444"><i class="fas fa-times-circle"></i> Invalid NIC format</span>');
			}
		});
		// Initial check
		if ($(selector).val()) $(selector).trigger('input');
	};

	setupNICListener('#farmer_nic', '#farmer_nic_status');
	setupNICListener('#facilitator_nic', '#facilitator_nic_status');
	setupNICListener('#buyer_nic', '#buyer_nic_status');

	// EzCash / mCash validation
	$('#farmer_ezcash').on('input', function() {
		const val = $(this).val();
		if (val && !/^(074|076|077)/.test(val)) {
			$('#ezcash_error').show();
		} else {
			$('#ezcash_error').hide();
		}
	});

	$('#farmer_mcash').on('input', function() {
		const val = $(this).val();
		if (val && !/^(070|071)/.test(val)) {
			$('#mcash_error').show();
		} else {
			$('#mcash_error').hide();
		}
	});

	// Moved form submission handler higher to ensure it's attached
	$('#editUserForm').submit(function(e) {
		e.preventDefault();

		try {
			const userRole = '{{ $user->role }}';
			const userId = {{ $user->id }};
			const isPaymentChanged = checkPaymentChanges();

			// New validation checks
			if (userRole === 'farmer' || userRole === 'lead_farmer') {
				const nic = $('#farmer_nic').val();
				if (nic && !validateNIC(nic)) {
					showError('Please enter a valid NIC number.');
					return;
				}
				const ezcash = $('#farmer_ezcash').val();
				const mcash = $('#farmer_mcash').val();
				if (ezcash && !/^(074|076|077)/.test(ezcash)) {
					showError('EzCash number must start with 074, 076, or 077');
					return;
				}
				if (mcash && !/^(070|071)/.test(mcash)) {
					showError('mCash number must start with 070 or 071');
					return;
				}
			} else if (userRole === 'facilitator') {
				const nic = $('#facilitator_nic').val();
				if (nic && !validateNIC(nic)) {
					showError('Please enter a valid NIC number.');
					return;
				}
			} else if (userRole === 'buyer') {
				const nic = $('#buyer_nic').val();
				if (nic && !validateNIC(nic)) {
					showError('Please enter a valid NIC number.');
					return;
				}
			}

			formDataToSubmit = new FormData(this);

			if (userRole === 'facilitator') {
				const district = $('#facilitator_district').val();
				let index = 0;
				$('.assignment-item').each(function() {
					const ds = $(this).find('.assign-ds').val();
					const gn = $(this).find('.assign-gn').val();
					const code = $(this).find('.assign-code').val();
					if (district && ds && gn) {
						formDataToSubmit.append(`assignments[${index}][district]`, district);
						formDataToSubmit.append(`assignments[${index}][divisional_secretariat]`, ds);
						formDataToSubmit.append(`assignments[${index}][gn_division]`, gn);
						formDataToSubmit.append(`assignments[${index}][gn_division_code]`, code);
						index++;
					}
				});
			}

			const isNicChanged = checkNicChanges();

			if (isNicChanged || isPaymentChanged) {
				if (otpVerified) {
					submitForm();
					return;
				}

				let alertTitle = 'OTP Verification Required';
				let alertHtml = '';
				let actionToTrigger = 'edit_payment';

				if (isNicChanged && isPaymentChanged) {
					alertHtml = `Changes to NIC and payment details require OTP verification.<br><br>
								<small class="text-muted">An OTP will be sent to the user's registered mobile number</small>`;
					actionToTrigger = 'verify_nic'; // For simplicity, we can use one sensitive action if both changed
				} else if (isNicChanged) {
					alertHtml = `Changes to NIC number require OTP verification.<br><br>
								<small class="text-muted">An OTP will be sent to the user's registered mobile number</small>`;
					actionToTrigger = 'verify_nic';
				} else {
					alertHtml = `Changes to payment details require OTP verification.<br><br>
								<small class="text-muted">An OTP will be sent to the user's registered mobile number</small>`;
					actionToTrigger = 'edit_payment';
				}

				Swal.fire({
					title: alertTitle,
					html: alertHtml,
					icon: 'info',
					showCancelButton: true,
					confirmButtonColor: '#10B981',
					cancelButtonColor: '#6b7280',
					confirmButtonText: 'Send OTP',
					cancelButtonText: 'Cancel',
					background: 'var(--card-bg)',
					color: 'var(--text-color)'
				}).then((result) => {
					if (result.isConfirmed) {
						sendOtpAndShowModal(actionToTrigger);
					}
				});
			} else {
				submitForm();
			}
		} catch (err) {
			console.error('Submit error:', err);
			showError('An unexpected error occurred: ' + err.message);
		}
	});

	const setupAssignment = (container) => {
		const dsSelect = container.find('.assign-ds');
		const gnSelect = container.find('.assign-gn');
		const codeInput = container.find('.assign-code');
		const districtSelect = $('#facilitator_district');
		
		let initialGN = gnSelect.val();

		const updateDS = () => {
			const dist = districtSelect.val();
			const currentDS = dsSelect.val();
			
			dsSelect.empty().append('<option value="" disabled selected>Select DS</option>').prop('disabled', !dist);
			gnSelect.empty().append('<option value="" disabled selected>Select DS First</option>').prop('disabled', true);
			
			if (dist && gnData[dist]) {
				Object.keys(gnData[dist]).forEach(ds => {
					dsSelect.append(`<option value="${ds}" ${ds === currentDS ? 'selected' : ''}>${ds}</option>`);
				});
				if (currentDS && dsSelect.val() === currentDS) {
					updateGN();
				}
			}
		};

		const updateGN = () => {
			const dist = districtSelect.val();
			const ds = dsSelect.val();
			const currentGN = gnSelect.val() || initialGN;
			
			gnSelect.empty().append('<option value="" disabled selected>Select GN</option>').prop('disabled', !ds);
			
			if (dist && ds && gnData[dist] && gnData[dist][ds]) {
				gnData[dist][ds].forEach(gn => {
					gnSelect.append(`<option value="${gn.name}" data-code="${gn.code}" ${gn.name === currentGN ? 'selected' : ''}>${gn.name}</option>`);
				});
				if (currentGN) {
					const selectedOption = gnSelect.find(':selected');
					const code = selectedOption.data('code');
					if (code) codeInput.val(code);
				}
			}
			initialGN = null;
		};

		if (districtSelect.val() && dsSelect.find('option').length <= 1) {
			updateDS();
		}

		dsSelect.on('change', function() {
			updateGN();
			codeInput.val('');
		});

		gnSelect.on('change', function() {
			const code = $(this).find(':selected').data('code');
			codeInput.val(code || '');
		});
	};

	const setupFarmerGNHierarchy = () => {
		const districtSelect = $('#farmer_district');
		const dsSelect = $('#farmer_ds');
		const gnSelect = $('#farmer_gn');
		const codeInput = $('#farmer_gn_code');
		
		let initialDS = dsSelect.val();
		let initialGN = gnSelect.val();

		const updateDS = () => {
			const dist = districtSelect.val();
			const currentDS = dsSelect.val();
			
			dsSelect.empty().append('<option value="" disabled selected>Select DS</option>').prop('disabled', !dist);
			
			if (dist && gnData[dist]) {
				Object.keys(gnData[dist]).forEach(ds => {
					dsSelect.append(`<option value="${ds}" ${ds === (currentDS || initialDS) ? 'selected' : ''}>${ds}</option>`);
				});
				if (currentDS || initialDS) {
					updateGN();
				}
			}
			initialDS = null;
		};

		const updateGN = () => {
			const dist = districtSelect.val();
			const ds = dsSelect.val();
			const currentGN = gnSelect.val() || initialGN;
			
			gnSelect.empty().append('<option value="" disabled selected>Select GN</option>').prop('disabled', !ds);
			
			if (dist && ds && gnData[dist] && gnData[dist][ds]) {
				gnData[dist][ds].forEach(gn => {
					gnSelect.append(`<option value="${gn.name}" data-code="${gn.code}" ${gn.name === currentGN ? 'selected' : ''}>${gn.name}</option>`);
				});
				if (currentGN) {
					const selectedOption = gnSelect.find(':selected');
					const code = selectedOption.data('code');
					if (code) codeInput.val(code);
				}
			}
			initialGN = null;
		};

		districtSelect.on('change', function() {
			updateDS();
			codeInput.val('');
		});
		
		dsSelect.on('change', function() {
			updateGN();
			codeInput.val('');
		});

		gnSelect.on('change', function() {
			const code = $(this).find(':selected').data('code');
			codeInput.val(code || '');
		});

		if (districtSelect.val()) updateDS();
	};

	if ($('#farmer-sections').is(':visible')) {
		setupFarmerGNHierarchy();
	}

	$('#facilitator_district').on('change', function() {
		$('.assignment-item').each(function() {
			const dsSelect = $(this).find('.assign-ds');
			const gnSelect = $(this).find('.assign-gn');
			const codeInput = $(this).find('.assign-code');
			const dist = $('#facilitator_district').val();
			
			dsSelect.empty().append('<option value="" disabled selected>Select DS</option>').prop('disabled', !dist);
			gnSelect.empty().append('<option value="" disabled selected>Select DS First</option>').prop('disabled', true);
			codeInput.val('');
			
			if (dist && gnData[dist]) {
				Object.keys(gnData[dist]).forEach(ds => {
					dsSelect.append(`<option value="${ds}">${ds}</option>`);
				});
			}
		});
	});

	$('.assignment-item').each(function() {
		setupAssignment($(this));
	});

	$('#add-assignment').on('click', function() {
		const newItem = $(`
			<div class="assignment-item" style="border: 1px solid var(--border-color); padding: 15px; margin-bottom: 15px; border-radius: 8px; position: relative; background: var(--bg-light);">
				<button type="button" class="remove-assignment" style="position: absolute; right: 10px; top: 10px; border: none; background: none; color: #ef4444; font-size: 1.2rem;">&times;</button>
				<div class="form-row">
					<div class="form-group">
						<label class="form-label">DS Division</label>
						<select class="form-select assign-ds" required disabled>
							<option value="" disabled selected>Select District First</option>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">GN Division</label>
						<select class="form-select assign-gn" required disabled>
							<option value="" disabled selected>Select DS First</option>
						</select>
					</div>
				</div>
				<div class="form-group" style="margin-top: 10px;">
					<label class="form-label">GN Code</label>
					<input type="text" class="form-input assign-code" readonly>
				</div>
			</div>
		`);
		$('#facilitator-assignments').append(newItem);
		setupAssignment(newItem);
		newItem.find('.remove-assignment').on('click', function() {
			newItem.remove();
		});
	});

	$(document).on('click', '.remove-assignment', function() {
		$(this).closest('.assignment-item').remove();
	});

	function showAlert(icon, title, text) {
		Swal.fire({
			icon: icon,
			title: title,
			text: text,
			confirmButtonColor: '#10B981',
			confirmButtonText: 'OK',
			timer: 3000,
			timerProgressBar: true,
			showClass: {
				popup: 'animate__animated animate__fadeInDown'
			},
			hideClass: {
				popup: 'animate__animated animate__fadeOutUp'
			}
		});
	}

	function showSuccess(message, title = 'Success') {
		Swal.fire({
			icon: 'success',
			title: title,
			text: message,
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
			timer: 3000,
			timerProgressBar: true,
			background: 'var(--card-bg)',
			color: 'var(--text-color)'
		});
	}

	function showError(message, title = 'Error') {
		Swal.fire({
			icon: 'error',
			title: title,
			text: message,
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
			timer: 4000,
			timerProgressBar: true,
			background: 'var(--card-bg)',
			color: 'var(--text-color)'
		});
	}

	function startOtpTimer() {
		clearInterval(otpTimer);
		timeLeft = 300;

		otpTimer = setInterval(function() {
			timeLeft--;
			let minutes = Math.floor(timeLeft / 60);
			let seconds = timeLeft % 60;

			$('#otpTimer').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);

			if (timeLeft <= 0) {
				clearInterval(otpTimer);
				showError('OTP has expired. Please request a new OTP');
			}
		}, 1000);
	}

	$('.btn-cancel').click(function() {
		Swal.fire({
			title: 'Discard Changes?',
			text: 'All unsaved changes will be lost',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#ef4444',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Discard',
			cancelButtonText: 'Continue Editing',
			background: 'var(--card-bg)',
			color: 'var(--text-color)'
		}).then((result) => {
			if (result.isConfirmed) {
				window.location.href = '{{ route("admin.users.index") }}';
			}
		});
	});



	function checkPaymentChanges() {
		try {
			const originalData = {
				preferred_payment: {!! json_encode($userDetails->preferred_payment ?? "bank") !!},
				account_number: {!! json_encode($userDetails->account_number ?? "") !!},
				account_holder_name: {!! json_encode($userDetails->account_holder_name ?? "") !!},
				bank_name: {!! json_encode($userDetails->bank_name ?? "") !!},
				bank_branch: {!! json_encode($userDetails->bank_branch ?? "") !!},
				ezcash_mobile: {!! json_encode($userDetails->ezcash_mobile ?? "") !!},
				mcash_mobile: {!! json_encode($userDetails->mcash_mobile ?? "") !!}
			};

			const currentData = {
				preferred_payment: $('[name="preferred_payment"]').val(),
				account_number: $('input[name="account_number"]').val() || "",
				account_holder_name: $('input[name="account_holder_name"]').val() || "",
				bank_name: $('input[name="bank_name"]').val() || "",
				bank_branch: $('input[name="bank_branch"]').val() || "",
				ezcash_mobile: $('input[name="ezcash_mobile"]').val() || "",
				mcash_mobile: $('input[name="mcash_mobile"]').val() || ""
			};

			for (const key in originalData) {
				const originalVal = (originalData[key] || "").toString().trim();
				const currentVal = (currentData[key] || "").toString().trim();
				
				if (originalVal !== currentVal) {
					return true;
				}
			}
		} catch (e) {
			console.error('Error checking payment changes:', e);
		}

		return false;
	}

	function checkNicChanges() {
		try {
			const userRole = '{{ $user->role }}';
			let originalNic = '';
			let currentNic = '';

			if (userRole === 'farmer' || userRole === 'lead_farmer') {
				originalNic = {!! json_encode($userDetails->nic_no ?? "") !!};
				currentNic = $('#farmer_nic').val();
			} else if (userRole === 'facilitator') {
				originalNic = {!! json_encode($facilitatorData->nic_no ?? "") !!};
				currentNic = $('#facilitator_nic').val();
			} else if (userRole === 'buyer') {
				originalNic = {!! json_encode($buyerData->nic_no ?? "") !!};
				currentNic = $('#buyer_nic').val();
			}

			return originalNic.toString().trim() !== currentNic.toString().trim();
		} catch (e) {
			console.error('Error checking NIC changes:', e);
			return false;
		}
	}

	let currentOtpAction = 'edit_payment';

	function sendOtpAndShowModal(action = 'edit_payment') {
		currentOtpAction = action;
		const userId = {{ $user->id }};

		Swal.fire({
			title: 'Sending OTP...',
			text: 'Please wait',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: '{{ route("admin.users.sendOtp") }}',
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				user_id: userId,
				action: currentOtpAction
			},
			success: function(response) {
				Swal.close();
				$('#otpModal').css('display', 'flex').hide().fadeIn();
				$('.otp-input').val('');
				startOtpTimer();
				showSuccess('OTP sent successfully to user');
			},
			error: function(xhr) {
				Swal.close();
				showError(xhr.responseJSON?.message || 'Failed to send OTP. Please try again');
			}
		});
	}

	function submitForm() {
		Swal.fire({
			title: 'Saving Changes...',
			text: 'Please wait while we update the user',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: $('#editUserForm').attr('action'),
			method: 'POST',
			data: formDataToSubmit,
			processData: false,
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			success: function(response) {
				Swal.fire({
					icon: 'success',
					title: 'Updated Successfully!',
					html: `User details have been updated.<br><br>
						  <small class="text-muted">The user has been notified of these changes</small>`,
					confirmButtonColor: '#10B981',
					showConfirmButton: true,
					allowOutsideClick: false,
					background: 'var(--card-bg)',
					color: 'var(--text-color)'
				}).then((result) => {
					window.location.href = '{{ route("admin.users.index") }}';
				});
			},
			error: function(xhr) {
				Swal.fire({
					icon: 'error',
					title: 'Update Failed',
					text: xhr.responseJSON?.message || 'Failed to update user details',
					confirmButtonColor: '#10B981',
					background: 'var(--card-bg)',
					color: 'var(--text-color)'
				});
			}
		});
	}

	$('.otp-input').on('input', function() {
		const index = parseInt($(this).data('index'));
		const value = $(this).val();

		if (value.length === 1 && index < 6) {
			$(`.otp-input[data-index="${index + 1}"]`).focus();
		}
	});

	$('.otp-input').on('keydown', function(e) {
		if (e.key === 'Backspace' && $(this).val() === '') {
			const index = parseInt($(this).data('index'));
			if (index > 1) {
				$(`.otp-input[data-index="${index - 1}"]`).focus();
			}
		}
	});

	$('#verifyOtpBtn').click(function() {
		const otp = $('.otp-input').map(function() {
			return $(this).val();
		}).get().join('');

		if (otp.length !== 6) {
			showError('Please enter the complete 6-digit OTP');
			return;
		}

		const userId = {{ $user->id }};

		Swal.fire({
			title: 'Verifying OTP...',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: '{{ route("admin.users.verifyOtp") }}',
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				user_id: userId,
				otp: otp,
				action: currentOtpAction
			},
			success: function(response) {
				Swal.close();
				clearInterval(otpTimer);
				otpVerified = true;
				$('#otpModal').fadeOut();
				showSuccess('OTP verified successfully');

				setTimeout(() => {
					submitForm();
				}, 1500);
			},
			error: function(xhr) {
				Swal.close();
				showError(xhr.responseJSON?.message || 'Invalid OTP. Please try again');
			}
		});
	});

	$('#resendOtpBtn').click(function() {
		const userId = {{ $user->id }};

		Swal.fire({
			title: 'Resending OTP...',
			text: 'Please wait',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: '{{ route("admin.users.resendOtp") }}',
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				user_id: userId,
				action: currentOtpAction
			},
			success: function(response) {
				Swal.close();
				showSuccess('New OTP has been sent to the user');
				startOtpTimer();
			},
			error: function(xhr) {
				Swal.close();
				showError('Failed to resend OTP');
			}
		});
	});

	$('#closeOtpModal').click(function() {
		$('#otpModal').fadeOut();
		clearInterval(otpTimer);
	});

	$(window).click(function(e) {
		if ($(e.target).hasClass('otp-modal-overlay')) {
			$('#otpModal').fadeOut();
			clearInterval(otpTimer);
		}
	});

	$('.form-input, .form-select').on('focus', function() {
		$(this).closest('.form-group').addClass('focused');
	});

	$('.form-input, .form-select').on('blur', function() {
		$(this).closest('.form-group').removeClass('focused');
	});

	$('.form-select').change(function() {
		$(this).addClass('changed');
		setTimeout(() => {
			$(this).removeClass('changed');
		}, 1000);
	});

	$('.form-input').on('input', function() {
		$(this).addClass('typing');
	});

	$('.form-input').on('blur', function() {
		$(this).removeClass('typing');
		if ($(this).val() !== $(this).data('original')) {
			$(this).addClass('modified');
		} else {
			$(this).removeClass('modified');
		}
	});

	$('.form-input').each(function() {
		$(this).data('original', $(this).val());
	});
});
</script>
@endsection
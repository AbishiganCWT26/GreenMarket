@extends('admin.layouts.admin_master')

@section('title', 'User Management')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/Admin/index-user-management.css') }}">
@endsection

@section('content')
<div class="container">
	<div class="header">
		<div class="header-top">
			<div class="title-section">
				<h1><i class="fas fa-users-cog"></i> User Management</h1>
			</div>
			<div class="controls">
				<form action="" class="search-bar">
					<input type="search" id="search-input" name="search" pattern=".*\S.*" required placeholder="Search users...">
					<button class="search-btn" type="submit">
						<i class="fas fa-search"></i>
					</button>
				</form>
				<button class="btn btn-primary" id="add-user-btn">
					<i class="fas fa-user-plus"></i> Add User
				</button>
			</div>
		</div>
		
		<div class="stats-cards">
			<div class="stat-card total">
				<i class="fas fa-users"></i>
				<div class="stat-info">
					<div class="number">{{ $totalUsers }}</div>
					<div class="label">Total Users</div>
				</div>
			</div>
			<div class="stat-card active">
				<i class="fas fa-user-check"></i>
				<div class="stat-info">
					<div class="number">{{ $activeUsers }}</div>
					<div class="label">Active</div>
				</div>
			</div>
			<div class="stat-card inactive">
				<i class="fas fa-user-slash"></i>
				<div class="stat-info">
					<div class="number">{{ $inactiveUsers }}</div>
					<div class="label">Inactive</div>
				</div>
			</div>
			<div class="stat-card admins">
				<i class="fas fa-user-shield"></i>
				<div class="stat-info">
					<div class="number">{{ $adminUsers }}</div>
					<div class="label">Admins</div>
				</div>
			</div>
		</div>
	</div>

	<div class="content-area">
		<div id="users-content">
			@include('admin.users.partials.user_cards', ['users' => $users])
		</div>
		
		<div id="loading" class="loading" style="display: none;">
			<i class="fas fa-spinner fa-spin"></i>
			<p>Loading users...</p>
		</div>
		
		<div class="pagination-container" id="pagination-container">
			{!! $paginator->links('vendor.pagination.simple-unique1') !!}
		</div>
	</div>
</div>

<div id="leadFarmerDeletionModal" class="modal-overlay" style="display:none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3><i class="fas fa-exclamation-triangle"></i> Lead Farmer Deletion</h3>
				<button class="modal-close" id="closeLeadFarmerModal">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<div class="modal-body">
				<div id="leadFarmerModalContent"></div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script src="{{ asset('js/gn-data.js') }}"></script>
<script>
	$(document).ready(function() {
		let currentView = 'card';
		let currentPage = 1;
		let searchTerm = '';
		let loading = false;
		let leadFarmers = @json($leadFarmers);

		function updateStatsCards(stats) {
			if (stats) {
				$('.stat-card.total .number').text(stats.total || 0);
				$('.stat-card.active .number').text(stats.active || 0);
				$('.stat-card.inactive .number').text(stats.inactive || 0);
				$('.stat-card.admins .number').text(stats.admins || 0);
			}
		}

		updateStatsCards({
			total: {{ $totalUsers }},
			active: {{ $activeUsers }},
			inactive: {{ $inactiveUsers }},
			admins: {{ $adminUsers }}
		});

		function showLoading(show) {
			loading = show;
			if (show) {
				$('#loading').show();
				$('#users-content').hide();
			} else {
				$('#loading').hide();
				$('#users-content').show();
			}
		}

		function loadUsers(page = 1, search = '') {
			if (loading) return;
			
			showLoading(true);
			currentPage = page;
			searchTerm = search;

			$.ajax({
				url: "{{ route('admin.users.index') }}",
				method: 'GET',
				data: {
					view: currentView,
					q: search,
					page: page
				},
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				},
				success: function(response) {
					if (response.success && response.html) {
						$('#users-content').html(response.html);
						if (response.pagination) {
							$('#pagination-container').html(response.pagination);
						}
						if (response.stats) {
							updateStatsCards(response.stats);
						}
						if (response.leadFarmers) {
							leadFarmers = response.leadFarmers;
						}
						Swal.fire({
							icon: 'success',
							title: 'Success!',
							text: 'Users loaded successfully',
							timer: 1500,
							showConfirmButton: false
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'Invalid response format',
							confirmButtonColor: '#10B981'
						});
					}
					showLoading(false);
				},
				error: function(xhr) {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: 'Failed to load users',
						confirmButtonColor: '#10B981'
					});
					showLoading(false);
				}
			});
		}

		function showAddUserModal() {
			Swal.fire({
				title: 'Add New User',
				html: `
					<div class="user-form">
						<div class="form-group">
							<label>User Type <span class="required">*</span></label>
							<select id="user-type" class="form-select" required>
								<option value="">Select Type</option>
								<option value="farmer">Farmer</option>
								<option value="lead_farmer">Lead Farmer</option>
								<option value="buyer">Buyer</option>
								<option value="facilitator">Facilitator</option>
								<option value="admin">Administrator</option>
								<option value="subadmin">Sub Administrator</option>
							</select>
						</div>
						<div id="role-specific-fields" style="display:none;"></div>
					</div>
				`,
				showCancelButton: true,
				confirmButtonText: 'Create User',
				cancelButtonText: 'Cancel',
				confirmButtonColor: '#10B981',
				cancelButtonColor: '#6b7280',
				width: '500px',
				didOpen: () => {
					$('#user-type').on('change', function() {
						const userType = $(this).val();
						let html = '';
						
						switch(userType) {
							case 'farmer':
								html = `
									<div class="form-section">
										<h4>Farmer Details</h4>
										<div class="form-group">
											<label>Lead Farmer <span class="required">*</span></label>
											<select id="lead_farmer_id" class="form-select" required>
												<option value="" disabled selected>Select a lead farmer</option>
												${leadFarmers.map(lf => `<option value="${lf.id}">${lf.name} - ${lf.district}</option>`).join('')}
											</select>
										</div>
										<div class="form-group">
											<label>Full Name <span class="required">*</span></label>
											<input type="text" id="name" class="form-input" placeholder="Enter full name" required>
										</div>
										<div class="form-group">
											<label>Username <span class="required">*</span></label>
											<input type="text" id="username" class="form-input" placeholder="Enter username" required>
										</div>
										<div class="form-group">
											<label>Email</label>
											<input type="email" id="email" class="form-input" placeholder="Enter email">
										</div>
										<div class="form-group">
											<label>Password <span class="required">*</span></label>
											<div class="password-container">
												<input type="password" id="password" class="form-input" placeholder="Enter password" required>
												<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
											</div>
											<small class="password-hint">Minimum 8 characters with uppercase, number & special character</small>
										</div>
										<div class="form-group">
											<label>Confirm Password <span class="required">*</span></label>
											<div class="password-container">
												<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
												<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
											</div>
										</div>
										<div class="form-group">
											<label>NIC Number <span class="required">*</span></label>
											<input type="text" id="farmer_nic" class="form-input" placeholder="Enter NIC" required>
										</div>
										<div class="form-group">
											<label>Primary Mobile <span class="required">*</span></label>
											<input type="tel" id="farmer_mobile" class="form-input" placeholder="Enter mobile number" required>
										</div>
										<div class="form-group">
											<label>WhatsApp Number</label>
											<input type="tel" id="farmer_whatsapp" class="form-input" placeholder="Enter WhatsApp">
										</div>
										<div class="form-group">
											<label>Residential Address <span class="required">*</span></label>
											<textarea id="farmer_address" class="form-input" placeholder="Enter address" rows="2" required></textarea>
										</div>
										<div class="form-group">
											<label>District <span class="required">*</span></label>
											<select id="farmer_district" class="form-select" required>
												<option value="" disabled selected>Select District</option>
												${Object.keys(gnData).map(d => `<option value="${d}">${d}</option>`).join('')}
											</select>
										</div>
										<div class="form-group">
											<label>Divisional Secretariat <span class="required">*</span></label>
											<select id="farmer_ds" class="form-select" required disabled>
												<option value="" disabled selected>Select District First</option>
											</select>
										</div>
										<div class="form-group">
											<label>Grama Niladhari Division <span class="required">*</span></label>
											<select id="farmer_gnd" class="form-select" required disabled>
												<option value="" disabled selected>Select DS First</option>
											</select>
										</div>
										<div class="form-group">
											<label>GN Division Code</label>
											<input type="text" id="farmer_gn_code" class="form-input" placeholder="GN Code" readonly>
										</div>
										<div class="form-group">
											<label>Preferred Payment <span class="required">*</span></label>
											<select id="farmer_payment" class="form-select" required>
												<option value="bank">Bank Transfer</option>
												<option value="ezcash">EzCash</option>
												<option value="mcash">mCash</option>
												<option value="all">All Methods</option>
											</select>
										</div>
										<div id="farmer-bank-fields">
											<div class="form-group">
												<label>Account Number <span id="account-required" class="required" style="display:none;">*</span></label>
												<input type="text" id="farmer_account" class="form-input" placeholder="Enter account number">
											</div>
											<div class="form-group">
												<label>Account Holder Name <span id="account-name-required" class="required" style="display:none;">*</span></label>
												<input type="text" id="farmer_account_name" class="form-input" placeholder="Enter holder name">
											</div>
											<div class="form-group">
												<label>Bank Name <span id="bank-required" class="required" style="display:none;">*</span></label>
												<input type="text" id="farmer_bank" class="form-input" placeholder="Enter bank name">
											</div>
											<div class="form-group">
												<label>Bank Branch <span id="branch-required" class="required" style="display:none;">*</span></label>
												<input type="text" id="farmer_branch" class="form-input" placeholder="Enter branch">
											</div>
										</div>
										<div id="farmer-ezcash-fields" style="display:none;">
											<div class="form-group">
												<label>EzCash Mobile Number <span id="ezcash-required" class="required" style="display:none;">*</span></label>
												<input type="tel" id="farmer_ezcash" class="form-input" placeholder="Enter EzCash mobile">
											</div>
										</div>
										<div id="farmer-mcash-fields" style="display:none;">
											<div class="form-group">
												<label>mCash Mobile Number <span id="mcash-required" class="required" style="display:none;">*</span></label>
												<input type="tel" id="farmer_mcash" class="form-input" placeholder="Enter mCash mobile">
											</div>
										</div>
									</div>
								`;
								break;
							case 'lead_farmer':
								html = `
									<div class="form-section">
										<h4>Lead Farmer Details</h4>
										<div class="form-group">
											<label>Full Name <span class="required">*</span></label>
											<input type="text" id="name" class="form-input" placeholder="Enter full name" required>
										</div>
										<div class="form-group">
											<label>Username <span class="required">*</span></label>
											<input type="text" id="username" class="form-input" placeholder="Enter username" required>
										</div>
										<div class="form-group">
											<label>Email</label>
											<input type="email" id="email" class="form-input" placeholder="Enter email">
										</div>
										<div class="form-group">
											<label>Password <span class="required">*</span></label>
											<div class="password-container">
												<input type="password" id="password" class="form-input" placeholder="Enter password" required>
												<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
											</div>
											<small class="password-hint">Minimum 8 characters with uppercase, number & special character</small>
										</div>
										<div class="form-group">
											<label>Confirm Password <span class="required">*</span></label>
											<div class="password-container">
												<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
												<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
											</div>
										</div>
										<div class="form-group">
											<label>NIC Number <span class="required">*</span></label>
											<input type="text" id="lead_nic" class="form-input" placeholder="Enter NIC" required>
										</div>
										<div class="form-group">
											<label>Primary Mobile <span class="required">*</span></label>
											<input type="tel" id="lead_mobile" class="form-input" placeholder="Enter mobile number" required>
										</div>
										<div class="form-group">
											<label>WhatsApp Number</label>
											<input type="tel" id="lead_whatsapp" class="form-input" placeholder="Enter WhatsApp">
										</div>
										<div class="form-group">
											<label>Residential Address <span class="required">*</span></label>
											<textarea id="lead_address" class="form-input" placeholder="Enter address" rows="2" required></textarea>
										</div>
										<div class="form-group">
											<label>District <span class="required">*</span></label>
											<select id="lead_district" class="form-select" required>
												<option value="" disabled selected>Select District</option>
												${Object.keys(gnData).map(d => `<option value="${d}">${d}</option>`).join('')}
											</select>
										</div>
										<div class="form-group">
											<label>Divisional Secretariat <span class="required">*</span></label>
											<select id="lead_ds" class="form-select" required disabled>
												<option value="" disabled selected>Select District First</option>
											</select>
										</div>
										<div class="form-group">
											<label>Grama Niladhari Division <span class="required">*</span></label>
											<select id="lead_gnd" class="form-select" required disabled>
												<option value="" disabled selected>Select DS First</option>
											</select>
										</div>
										<div class="form-group">
											<label>GN Division Code</label>
											<input type="text" id="lead_gn_code" class="form-input" placeholder="GN Code" readonly>
										</div>
										<div class="form-group">
											<label>Group Name <span class="required">*</span></label>
											<input type="text" id="lead_group_name" class="form-input" placeholder="Enter group name" required>
										</div>
										<div class="form-group">
											<label>Group Number <span class="required">*</span></label>
											<input type="text" id="lead_group_number" class="form-input" placeholder="Enter group number" required>
										</div>
										<div class="form-group">
											<label>Account Number <span class="required">*</span></label>
											<input type="text" id="lead_account" class="form-input" placeholder="Enter account number" required>
										</div>
										<div class="form-group">
											<label>Account Holder Name <span class="required">*</span></label>
											<input type="text" id="lead_account_name" class="form-input" placeholder="Enter holder name" required>
										</div>
										<div class="form-group">
											<label>Bank Name <span class="required">*</span></label>
											<input type="text" id="lead_bank" class="form-input" placeholder="Enter bank name" required>
										</div>
										<div class="form-group">
											<label>Bank Branch <span class="required">*</span></label>
											<input type="text" id="lead_branch" class="form-input" placeholder="Enter branch" required>
										</div>
									</div>
								`;
								break;
							case 'buyer':
								html = `
									<div class="form-section">
										<h4>Buyer Details</h4>
										<div class="form-group">
											<label>Full Name <span class="required">*</span></label>
											<input type="text" id="name" class="form-input" placeholder="Enter full name" required>
										</div>
										<div class="form-group">
											<label>Username <span class="required">*</span></label>
											<input type="text" id="username" class="form-input" placeholder="Enter username" required>
										</div>
										<div class="form-group">
											<label>Email</label>
											<input type="email" id="email" class="form-input" placeholder="Enter email">
										</div>
										<div class="form-group">
											<label>Password <span class="required">*</span></label>
											<div class="password-container">
												<input type="password" id="password" class="form-input" placeholder="Enter password" required>
												<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
											</div>
											<small class="password-hint">Minimum 8 characters with uppercase, number & special character</small>
										</div>
										<div class="form-group">
											<label>Confirm Password <span class="required">*</span></label>
											<div class="password-container">
												<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
												<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
											</div>
										</div>
										<div class="form-group">
											<label>NIC Number</label>
											<input type="text" id="buyer_nic" class="form-input" placeholder="Enter NIC">
										</div>
										<div class="form-group">
											<label>Primary Mobile <span class="required">*</span></label>
											<input type="tel" id="buyer_mobile" class="form-input" placeholder="Enter mobile number" required>
										</div>
										<div class="form-group">
											<label>WhatsApp Number</label>
											<input type="tel" id="buyer_whatsapp" class="form-input" placeholder="Enter WhatsApp">
										</div>
										<div class="form-group">
											<label>Residential Address</label>
											<textarea id="buyer_address" class="form-input" placeholder="Enter address" rows="2"></textarea>
										</div>
										<div class="form-group">
											<label>District <span class="required">*</span></label>
											<select id="buyer_district" class="form-select" required>
												<option value="" disabled selected>Select District</option>
												${typeof gnData !== 'undefined' ? Object.keys(gnData).map(d => `<option value="${d}">${d}</option>`).join('') : ''}
											</select>
										</div>
										<div class="form-group">
											<label>Business Name</label>
											<input type="text" id="buyer_business" class="form-input" placeholder="Enter business name">
										</div>
										<div class="form-group">
											<label>Business Type</label>
											<select id="buyer_type" class="form-select">
												<option value="individual">Individual</option>
												<option value="restaurant">Restaurant</option>
												<option value="hotel">Hotel</option>
												<option value="retailer">Retailer</option>
												<option value="wholesaler">Wholesaler</option>
											</select>
										</div>
									</div>
								`;
								break;
							case 'facilitator':
								html = `
									<div class="form-section">
										<h4>Facilitator Details</h4>
										<div class="form-group">
											<label>Full Name <span class="required">*</span></label>
											<input type="text" id="name" class="form-input" placeholder="Enter full name" required>
										</div>
										<div class="form-group">
											<label>Username <span class="required">*</span></label>
											<input type="text" id="username" class="form-input" placeholder="Enter username" required>
										</div>
										<div class="form-group">
											<label>Email</label>
											<input type="email" id="email" class="form-input" placeholder="Enter email">
										</div>
										<div class="form-group">
											<label>Password <span class="required">*</span></label>
											<div class="password-container">
												<input type="password" id="password" class="form-input" placeholder="Enter password" required>
												<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
											</div>
											<small class="password-hint">Minimum 8 characters with uppercase, number & special character</small>
										</div>
										<div class="form-group">
											<label>Confirm Password <span class="required">*</span></label>
											<div class="password-container">
												<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
												<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
											</div>
										</div>
										<div class="form-group">
											<label>NIC Number <span class="required">*</span></label>
											<input type="text" id="facilitator_nic" class="form-input" placeholder="Enter NIC" required>
										</div>
										<div class="form-group">
											<label>Primary Mobile <span class="required">*</span></label>
											<input type="tel" id="facilitator_mobile" class="form-input" placeholder="Enter mobile number" required>
										</div>
										<div class="form-group">
											<label>WhatsApp Number</label>
											<input type="tel" id="facilitator_whatsapp" class="form-input" placeholder="Enter WhatsApp">
										</div>
										<div class="form-group">
											<label>District <span class="required">*</span></label>
											<select id="facilitator_district" class="form-select" required>
												<option value="" disabled selected>Select District</option>
												${Object.keys(gnData).map(d => `<option value="${d}">${d}</option>`).join('')}
											</select>
										</div>
										<div class="form-group">
											<label>Divisional Secretariat & GN Divisions <span class="required">*</span></label>
											<div id="facilitator-assignments">
												<div class="assignment-item" style="border: 1px solid #e5e7eb; padding: 10px; margin-bottom: 10px; border-radius: 6px;">
													<div class="form-group">
														<select class="form-select assign-ds" required disabled>
															<option value="" disabled selected>Select District First</option>
														</select>
													</div>
													<div class="form-group">
														<select class="form-select assign-gn" required disabled>
															<option value="" disabled selected>Select DS First</option>
														</select>
													</div>
													<div class="form-group">
														<input type="text" class="form-input assign-code" placeholder="GN Code" readonly>
													</div>
												</div>
											</div>
											<button type="button" id="add-assignment" class="btn btn-sm btn-outline-primary" style="font-size: 12px; margin-top: 5px;">+ Add Another Division</button>
										</div>
									</div>
								`;
								break;
							case 'admin':
							case 'subadmin':
								html = `
									<div class="form-section">
										<h4>${userType === 'admin' ? 'Administrator' : 'Sub Administrator'} Details</h4>
										<div class="form-group">
											<label>Full Name <span class="required">*</span></label>
											<input type="text" id="name" class="form-input" placeholder="Enter full name" required>
										</div>
										<div class="form-group">
											<label>Username <span class="required">*</span></label>
											<input type="text" id="username" class="form-input" placeholder="Enter username" required>
										</div>
										<div class="form-group">
											<label>Email</label>
											<input type="email" id="email" class="form-input" placeholder="Enter email">
										</div>
										<div class="form-group">
											<label>Password <span class="required">*</span></label>
											<div class="password-container">
												<input type="password" id="password" class="form-input" placeholder="Enter password" required>
												<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
											</div>
											<small class="password-hint">Minimum 8 characters with uppercase, number & special character</small>
										</div>
										<div class="form-group">
											<label>Confirm Password <span class="required">*</span></label>
											<div class="password-container">
												<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
												<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
											</div>
										</div>
										<div class="form-group">
											<label>NIC Number</label>
											<input type="text" id="admin_nic" class="form-input" placeholder="Enter NIC">
										</div>
										<div class="form-group">
											<label>Phone Number <span class="required">*</span></label>
											<input type="tel" id="admin_phone" class="form-input" placeholder="Enter phone number" required>
										</div>
									</div>
								`;
								break;
						}
						
						$('#role-specific-fields').html(html).show();
						
						if (userType === 'farmer') {
							$('#farmer_payment').on('change', function() {
								const payment = $(this).val();
								
								$('#account-required, #account-name-required, #bank-required, #branch-required, #ezcash-required, #mcash-required').hide();
								
								$('#farmer-bank-fields, #farmer-ezcash-fields, #farmer-mcash-fields').hide();
								
								if (payment === 'bank' || payment === 'all') {
									$('#farmer-bank-fields').show();
									$('#account-required, #account-name-required, #bank-required, #branch-required').show();
								}
								if (payment === 'ezcash' || payment === 'all') {
									$('#farmer-ezcash-fields').show();
									$('#ezcash-required').show();
								}
								if (payment === 'mcash' || payment === 'all') {
									$('#farmer-mcash-fields').show();
									$('#mcash-required').show();
								}
								if (payment === 'all') {
									$('#farmer-bank-fields, #farmer-ezcash-fields, #farmer-mcash-fields').show();
									$('#account-required, #account-name-required, #bank-required, #branch-required, #ezcash-required, #mcash-required').show();
								}
							});
						}

						// GN Hierarchy Logic
						const setupGNHierarchy = (prefix) => {
							const districtSelect = $(`#${prefix}_district`);
							const dsSelect = $(`#${prefix}_ds`);
							const gndSelect = $(`#${prefix}_gnd`);
							const codeInput = $(`#${prefix}_gn_code`);

							districtSelect.on('change', function() {
								const dist = $(this).val();
								dsSelect.empty().append('<option value="" disabled selected>Select DS</option>').prop('disabled', false);
								gndSelect.empty().append('<option value="" disabled selected>Select DS First</option>').prop('disabled', true);
								codeInput.val('');
								
								if (gnData[dist]) {
									Object.keys(gnData[dist]).forEach(ds => {
										dsSelect.append(`<option value="${ds}">${ds}</option>`);
									});
								}
							});

							dsSelect.on('change', function() {
								const dist = districtSelect.val();
								const ds = $(this).val();
								gndSelect.empty().append('<option value="" disabled selected>Select GN Division</option>').prop('disabled', false);
								codeInput.val('');

								if (gnData[dist] && gnData[dist][ds]) {
									gnData[dist][ds].forEach(gn => {
										gndSelect.append(`<option value="${gn.name}" data-code="${gn.code}">${gn.name}</option>`);
									});
								}
							});

							gndSelect.on('change', function() {
								const code = $(this).find(':selected').data('code');
								codeInput.val(code || '');
							});
						};

						if (userType === 'farmer') {
							setupGNHierarchy('farmer');
						} else if (userType === 'lead_farmer') {
							setupGNHierarchy('lead');
						} else if (userType === 'facilitator') {
							const setupAssignment = (container) => {
								const dsSelect = container.find('.assign-ds');
								const gnSelect = container.find('.assign-gn');
								const codeInput = container.find('.assign-code');
								const districtSelect = $('#facilitator_district');

								const updateDS = () => {
									const dist = districtSelect.val();
									dsSelect.empty().append('<option value="" disabled selected>Select DS</option>').prop('disabled', !dist);
									gnSelect.empty().append('<option value="" disabled selected>Select DS First</option>').prop('disabled', true);
									codeInput.val('');
									if (dist && gnData[dist]) {
										Object.keys(gnData[dist]).forEach(ds => {
											dsSelect.append(`<option value="${ds}">${ds}</option>`);
										});
									}
								};

								if (districtSelect.val()) {
									updateDS();
								}

								dsSelect.on('change', function() {
									const dist = districtSelect.val();
									const ds = $(this).val();
									gnSelect.empty().append('<option value="" disabled selected>Select GN</option>').prop('disabled', false);
									codeInput.val('');
									if (gnData[dist] && gnData[dist][ds]) {
										gnData[dist][ds].forEach(gn => {
											gnSelect.append(`<option value="${gn.name}" data-code="${gn.code}">${gn.name}</option>`);
										});
									}
								});

								gnSelect.on('change', function() {
									const code = $(this).find(':selected').data('code');
									codeInput.val(code || '');
								});
							};

							$('#facilitator_district').on('change', function() {
								$('.assignment-item').each(function() {
									const dsSelect = $(this).find('.assign-ds');
									const gnSelect = $(this).find('.assign-gn');
									const codeInput = $(this).find('.assign-code');
									const dist = $('#facilitator_district').val();
									
									dsSelect.empty().append('<option value="" disabled selected>Select DS</option>').prop('disabled', false);
									gnSelect.empty().append('<option value="" disabled selected>Select DS First</option>').prop('disabled', true);
									codeInput.val('');
									
									if (gnData[dist]) {
										Object.keys(gnData[dist]).forEach(ds => {
											dsSelect.append(`<option value="${ds}">${ds}</option>`);
										});
									}
								});
							});

							setupAssignment($('.assignment-item'));

							$('#add-assignment').on('click', function() {
								const newItem = $(`
									<div class="assignment-item" style="border: 1px solid #e5e7eb; padding: 10px; margin-bottom: 10px; border-radius: 6px; position: relative;">
										<button type="button" class="remove-assignment" style="position: absolute; right: 5px; top: 5px; border: none; background: none; color: #ef4444;">&times;</button>
										<div class="form-group">
											<select class="form-select assign-ds" required disabled>
												<option value="" disabled selected>Select District First</option>
											</select>
										</div>
										<div class="form-group">
											<select class="form-select assign-gn" required disabled>
												<option value="" disabled selected>Select DS First</option>
											</select>
										</div>
										<div class="form-group">
											<input type="text" class="form-input assign-code" placeholder="GN Code" readonly>
										</div>
									</div>
								`);
								$('#facilitator-assignments').append(newItem);
								setupAssignment(newItem);
								newItem.find('.remove-assignment').on('click', function() {
									newItem.remove();
								});
							});
						}

						// WhatsApp Auto-fill Logic
						const roleMap = {
							'farmer': { mobile: '#farmer_mobile', whatsapp: '#farmer_whatsapp' },
							'lead_farmer': { mobile: '#lead_mobile', whatsapp: '#lead_whatsapp' },
							'buyer': { mobile: '#buyer_mobile', whatsapp: '#buyer_whatsapp' },
							'facilitator': { mobile: '#facilitator_mobile', whatsapp: '#facilitator_whatsapp' }
						};

						if (roleMap[userType]) {
							const { mobile, whatsapp } = roleMap[userType];
							$(mobile).on('input', function() {
								const mobileVal = $(this).val();
								const whatsappField = $(whatsapp);
								if (!whatsappField.val() || whatsappField.val() === $(this).data('prev-mobile')) {
									whatsappField.val(mobileVal);
								}
								$(this).data('prev-mobile', mobileVal);
							});
							
							$(whatsapp).on('input', function() {
								// If user manually edits WhatsApp, we stop auto-sync if they clear it
								if (!$(this).val()) {
									$(mobile).data('prev-mobile', '');
								}
							});
						}
					});
				},
				preConfirm: function() {
					const userType = $('#user-type').val();
					const name = $('#name').val();
					const username = $('#username').val();
					const email = $('#email').val();
					const password = $('#password').val();
					const passwordConfirmation = $('#password_confirmation').val();

					if (userType === 'farmer') {
						const leadFarmerId = $('#lead_farmer_id').val();
						if (!leadFarmerId) {
							Swal.showValidationMessage('Please select a Lead Farmer');
							return false;
						}

						const gnd = $('#farmer_gnd').val();
						if (!gnd) {
							Swal.showValidationMessage('Grama Niladhari Division field data is missing');
							return false;
						}
					}

					if (userType === 'lead_farmer') {
						const gnd = $('#lead_gnd').val();
						if (!gnd) {
							Swal.showValidationMessage('Grama Niladhari Division field data is missing');
							return false;
						}
					}

					if (!userType || !name || !username || !password) {
						Swal.showValidationMessage('Please fill all required fields');
						return false;
					}

					if (password !== passwordConfirmation) {
						Swal.showValidationMessage('Passwords do not match');
						return false;
					}

					if (password.length < 8) {
						Swal.showValidationMessage('Password must be at least 8 characters');
						return false;
					}

					if (!/(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/.test(password)) {
						Swal.showValidationMessage('Password must include uppercase, number and special character');
						return false;
					}

					const formData = {
						user_type: userType,
						name: name,
						username: username,
						email: email,
						password: password,
						password_confirmation: passwordConfirmation
					};

					if (userType === 'farmer') {
						formData.lead_farmer_id = $('#lead_farmer_id').val();
						formData.nic_no = $('#farmer_nic').val() || '';
						formData.primary_mobile = $('#farmer_mobile').val() || '';
						formData.whatsapp_number = $('#farmer_whatsapp').val() || formData.primary_mobile;
						formData.residential_address = $('#farmer_address').val() || '';
						formData.district = $('#farmer_district').val() || '';
						formData.divisional_secretariat = $('#farmer_ds').val() || '';
						formData.grama_niladhari_division = $('#farmer_gnd').val() || '';
						formData.gn_division_code = $('#farmer_gn_code').val() || '';
						formData.preferred_payment = $('#farmer_payment').val() || 'bank';
						
						const paymentMethod = $('#farmer_payment').val();
						
						if (paymentMethod === 'bank' || paymentMethod === 'all') {
							const account = $('#farmer_account').val() || '';
							const accountName = $('#farmer_account_name').val() || '';
							const bankName = $('#farmer_bank').val() || '';
							const bankBranch = $('#farmer_branch').val() || '';
							
							if (!account) {
								Swal.showValidationMessage('Account Number is required for Bank Transfer');
								return false;
							}
							if (!accountName) {
								Swal.showValidationMessage('Account Holder Name is required for Bank Transfer');
								return false;
							}
							if (!bankName) {
								Swal.showValidationMessage('Bank Name is required for Bank Transfer');
								return false;
							}
							if (!bankBranch) {
								Swal.showValidationMessage('Bank Branch is required for Bank Transfer');
								return false;
							}
							
							formData.account_number = account;
							formData.account_holder_name = accountName;
							formData.bank_name = bankName;
							formData.bank_branch = bankBranch;
						}
						
						if (paymentMethod === 'ezcash' || paymentMethod === 'all') {
							const ezcashMobile = $('#farmer_ezcash').val() || '';
							if (!ezcashMobile) {
								Swal.showValidationMessage('EzCash Mobile Number is required for EzCash payment');
								return false;
							}
							formData.ezcash_mobile = ezcashMobile;
						}
						
						if (paymentMethod === 'mcash' || paymentMethod === 'all') {
							const mcashMobile = $('#farmer_mcash').val() || '';
							if (!mcashMobile) {
								Swal.showValidationMessage('mCash Mobile Number is required for mCash payment');
								return false;
							}
							formData.mcash_mobile = mcashMobile;
						}
						
						if ($('#farmer_ezcash').val()) formData.ezcash_mobile = $('#farmer_ezcash').val();
						if ($('#farmer_mcash').val()) formData.mcash_mobile = $('#farmer_mcash').val();                   
					} else if (userType === 'lead_farmer') {
						formData.nic_no = $('#lead_nic').val() || '';
						formData.primary_mobile = $('#lead_mobile').val() || '';
						formData.whatsapp_number = $('#lead_whatsapp').val() || formData.primary_mobile;
						formData.residential_address = $('#lead_address').val() || '';
						formData.district = $('#lead_district').val() || '';
						formData.divisional_secretariat = $('#lead_ds').val() || '';
						formData.grama_niladhari_division = $('#lead_gnd').val() || '';
						formData.gn_division_code = $('#lead_gn_code').val() || '';
						formData.group_name = $('#lead_group_name').val() || '';
						formData.group_number = $('#lead_group_number').val() || '';
						formData.account_number = $('#lead_account').val() || '';
						formData.account_holder_name = $('#lead_account_name').val() || '';
						formData.bank_name = $('#lead_bank').val() || '';
						formData.bank_branch = $('#lead_branch').val() || '';
						formData.preferred_payment = 'bank';
					} else if (userType === 'facilitator') {
						formData.nic_no = $('#facilitator_nic').val() || '';
						formData.primary_mobile = $('#facilitator_mobile').val() || '';
						formData.whatsapp_number = $('#facilitator_whatsapp').val() || formData.primary_mobile;
						
						const district = $('#facilitator_district').val();
						const assignments = [];
						$('.assignment-item').each(function() {
							const ds = $(this).find('.assign-ds').val();
							const gn = $(this).find('.assign-gn').val();
							const code = $(this).find('.assign-code').val();
							if (district && ds && gn) {
								assignments.push({
									district: district,
									divisional_secretariat: ds,
									gn_division: gn,
									gn_division_code: code
								});
							}
						});
						formData.assignments = assignments;
						formData.district = district;
						formData.divisional_secretariat = assignments.length > 0 ? assignments[0].divisional_secretariat : '';
						formData.assigned_division = assignments.length > 0 ? assignments[0].gn_division : '';
						formData.gn_division_code = assignments.length > 0 ? assignments[0].gn_division_code : '';
					} else if (userType === 'buyer') {
						formData.nic_no = $('#buyer_nic').val() || '';
						formData.primary_mobile = $('#buyer_mobile').val() || '';
						formData.whatsapp_number = $('#buyer_whatsapp').val() || formData.primary_mobile;
						formData.residential_address = $('#buyer_address').val() || '';
						formData.district = $('#buyer_district').val() || '';
						formData.business_name = $('#buyer_business').val() || '';
						formData.business_type = $('#buyer_type').val() || 'individual';
					} else if (userType === 'facilitator') {
						formData.nic_no = $('#facilitator_nic').val() || '';
						formData.primary_mobile = $('#facilitator_mobile').val() || '';
						formData.whatsapp_number = $('#facilitator_whatsapp').val() || formData.primary_mobile;
						formData.assigned_division = $('#facilitator_division').val() || '';
					} else if (userType === 'admin' || userType === 'subadmin') {
						formData.nic_no = $('#admin_nic').val() || '';
						formData.phone_number = $('#admin_phone').val() || '';
					}

					return formData;
				}
			}).then((result) => {
				if (result.isConfirmed) {
					const formData = result.value;
					
					$.ajax({
						url: "{{ route('admin.users.store') }}",
						method: 'POST',
						data: formData,
						headers: {
							'X-CSRF-TOKEN': '{{ csrf_token() }}'
						},
						success: function(response) {
							if (response.success) {
								Swal.fire({
									icon: 'success',
									title: 'Success!',
									text: response.message,
									confirmButtonColor: '#10B981'
								}).then(() => {
									loadUsers();
								});
							} else {
								Swal.fire({
									icon: 'error',
									title: 'Error',
									text: response.message,
									confirmButtonColor: '#10B981'
								});
							}
						},
						error: function(xhr) {
							let error = 'Failed to create user';
							
							if (xhr.status === 422 && xhr.responseJSON?.errors) {
								const errors = xhr.responseJSON.errors;
								error = Object.values(errors).flat()[0];
							} else if (xhr.responseJSON?.message) {
								error = xhr.responseJSON.message;
							}

							Swal.fire({
								icon: 'error',
								title: 'Submission Failed',
								html: `
									<div style="text-align: center;">
										<p style="margin: 0; color: #b91c1c; font-size: 14px; font-weight: 500;">
											${error}
										</p>
										<p style="margin: 8px 0 0 0; color: #6b7280; font-size: 12px;">
											Please check the details and try again.
										</p>
									</div>
								`,
								confirmButtonText: 'OK',
								confirmButtonColor: '#10B981',
							});
						}
					});
				}
			});
		}

		window.togglePasswordVisibility = function(fieldId) {
			const field = $('#' + fieldId);
			const toggleIcon = field.next('.password-toggle');
			
			if (field.attr('type') === 'password') {
				field.attr('type', 'text');
				toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
			} else {
				field.attr('type', 'password');
				toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
			}
		}

		$('#search-input').on('input', function() {
			const search = $(this).val();
			clearTimeout($(this).data('timeout'));
			$(this).data('timeout', setTimeout(() => {
				loadUsers(1, search);
			}, 500));
		});

		$('.search-bar').on('submit', function(e) {
			e.preventDefault();
			const search = $('#search-input').val();
			loadUsers(1, search);
		});

		$('#add-user-btn').click(showAddUserModal);

		$(document).on('click', '.pagination a', function(e) {
			e.preventDefault();
			const page = $(this).attr('href').split('page=')[1];
			loadUsers(page, searchTerm);
		});

		$(document).on('click', '.action-btn', function(e) {
			e.preventDefault();
			const action = $(this).data('action');
			const userId = $(this).data('user-id');
			const userName = $(this).data('user-name') || 'User';

			switch(action) {
				case 'view':
					window.location.href = `/admin/users/${userId}`;
					break;
				case 'edit':
					window.location.href = `/admin/users/${userId}/edit`;
					break;
				case 'suspend':
					Swal.fire({
						title: 'Suspend User',
						text: `Are you sure you want to suspend ${userName}?`,
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#10B981',
						cancelButtonColor: '#6b7280',
						confirmButtonText: 'Yes, suspend',
						cancelButtonText: 'Cancel'
					}).then((result) => {
						if (result.isConfirmed) {
							$.ajax({
								url: `/admin/users/${userId}/suspend`,
								method: 'POST',
								headers: {
									'X-CSRF-TOKEN': '{{ csrf_token() }}'
								},
								success: function(response) {
									if (response.success) {
										Swal.fire({
											icon: 'success',
											title: 'Suspended!',
											text: response.message,
											confirmButtonColor: '#10B981'
										}).then(() => {
											loadUsers(currentPage, searchTerm);
										});
									} else {
										Swal.fire({
											icon: 'error',
											title: 'Error',
											text: response.message,
											confirmButtonColor: '#10B981'
										});
									}
								},
								error: function(xhr) {
									Swal.fire({
										icon: 'error',
										title: 'Error',
										text: 'Failed to suspend user',
										confirmButtonColor: '#10B981'
									});
								}
							});
						}
					});
					break;
				case 'activate':
					$.ajax({
						url: `/admin/users/${userId}/activate`,
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': '{{ csrf_token() }}'
						},
						success: function(response) {
							if (response.success) {
								Swal.fire({
									icon: 'success',
									title: 'Activated!',
									text: response.message,
									confirmButtonColor: '#10B981'
								}).then(() => {
									loadUsers(currentPage, searchTerm);
								});
							}
						}
					});
					break;
				case 'delete':
					handleDeleteUser(userId, userName);
					break;
				case 'promote':
					Swal.fire({
						title: 'Promote to Lead Farmer',
						text: `Promote ${userName} to Lead Farmer role?`,
						icon: 'question',
						showCancelButton: true,
						confirmButtonColor: '#10B981',
						cancelButtonColor: '#6b7280',
						confirmButtonText: 'Yes, promote',
						cancelButtonText: 'Cancel'
					}).then((result) => {
						if (result.isConfirmed) {
							$.ajax({
								url: `/admin/users/${userId}/promote`,
								method: 'POST',
								headers: {
									'X-CSRF-TOKEN': '{{ csrf_token() }}'
								},
								success: function(response) {
									if (response.success) {
										Swal.fire({
											icon: 'success',
											title: 'Promoted!',
											text: response.message,
											confirmButtonColor: '#10B981'
										}).then(() => {
											loadUsers(currentPage, searchTerm);
										});
									} else {
										Swal.fire({
											icon: 'error',
											title: 'Error',
											text: response.message,
											confirmButtonColor: '#10B981'
										});
									}
								},
								error: function(xhr) {
									Swal.fire({
										icon: 'error',
										title: 'Error',
										text: xhr.responseJSON?.message || 'Failed to promote user',
										confirmButtonColor: '#10B981'
									});
								}
							});
						}
					});
					break;
			}
		});

		function handleDeleteUser(userId, userName) {
			Swal.fire({
				title: 'Delete User',
				html: `Are you sure you want to delete <strong>${userName}</strong>?<br><br>
					<small style="color: #6b7280;">This action cannot be undone and all related data will be permanently deleted.</small>`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#ef4444',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Yes, delete',
				cancelButtonText: 'Cancel',
				width: '500px'
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: `/admin/users/${userId}`,
						method: 'DELETE',
						headers: {
							'X-CSRF-TOKEN': '{{ csrf_token() }}'
						},
						success: function(response) {
							if (response.success) {
								Swal.fire({
									icon: 'success',
									title: 'Deleted!',
									text: response.message,
									confirmButtonColor: '#10B981'
								}).then(() => {
									loadUsers(currentPage, searchTerm);
								});
							} else if (response.requires_action) {
								showLeadFarmerDeletionModal(userId, response);
							} else {
								Swal.fire({
									icon: 'error',
									title: 'Error',
									text: response.message,
									confirmButtonColor: '#10B981'
								});
							}
						},
						error: function(xhr) {
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: xhr.responseJSON?.message || 'Failed to delete user',
								confirmButtonColor: '#10B981'
							});
						}
					});
				}
			});
		}

		function showLeadFarmerDeletionModal(userId, response) {
			$.ajax({
				url: `/admin/get-lead-farmers-for-transfer`,
				method: 'GET',
				success: function(leadFarmersResponse) {
					let leadFarmersHtml = '';
					if (leadFarmersResponse.leadFarmers && leadFarmersResponse.leadFarmers.length > 0) {
						leadFarmersHtml = '<option value="">Select Lead Farmer</option>';
						leadFarmersResponse.leadFarmers.forEach(function(leadFarmer) {
							if (leadFarmer.id != response.lead_farmer_id) {
								leadFarmersHtml += `<option value="${leadFarmer.id}">${leadFarmer.name} - ${leadFarmer.group_name}</option>`;
							}
						});
					}
					
					const modalHtml = `
						<p>${response.message}</p>
						<div class="deletion-options">
							<div class="option-card" data-action="delete_all">
								<div class="option-icon">
									<i class="fas fa-trash"></i>
								</div>
								<div class="option-content">
									<h4>Delete All Farmers</h4>
									<p>Permanently delete all ${response.farmers_count || 0} farmers under this lead farmer</p>
								</div>
							</div>
							<div class="option-card" data-action="transfer">
								<div class="option-icon">
									<i class="fas fa-exchange-alt"></i>
								</div>
								<div class="option-content">
									<h4>Transfer Farmers</h4>
									<p>Transfer all farmers to another lead farmer</p>
									<div class="transfer-select" style="display:none; margin-top:10px;">
										<select id="newLeadFarmerSelect" class="form-select">
											${leadFarmersHtml || '<option value="">No other lead farmers available</option>'}
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-actions" style="margin-top:20px; display:none;" id="modalActions">
							<button class="btn-secondary" id="cancelAction">Cancel</button>
							<button class="btn-primary" id="confirmAction" disabled>Confirm</button>
						</div>
					`;
					
					$('#leadFarmerModalContent').html(modalHtml);
					$('#leadFarmerDeletionModal').fadeIn();
					
					let selectedAction = '';
					let newLeadFarmerId = '';
					
					$('.option-card').click(function() {
						$('.option-card').removeClass('selected');
						$(this).addClass('selected');
						selectedAction = $(this).data('action');
						$('#modalActions').show();
						$('#confirmAction').prop('disabled', true);
						
						if (selectedAction === 'transfer') {
							$(this).find('.transfer-select').show();
							$('#newLeadFarmerSelect').on('change', function() {
								newLeadFarmerId = $(this).val();
								$('#confirmAction').prop('disabled', !newLeadFarmerId);
							});
						} else {
							$('#confirmAction').prop('disabled', false);
						}
					});
					
					$('#confirmAction').click(function() {
						if (!selectedAction) return;
						
						const data = {
							action: selectedAction,
							_token: '{{ csrf_token() }}'
						};
						
						if (selectedAction === 'transfer' && newLeadFarmerId) {
							data.new_lead_farmer_id = newLeadFarmerId;
						}
						
						$.ajax({
							url: `/admin/users/${userId}/process-deletion`,
							method: 'POST',
							data: data,
							success: function(response) {
								if (response.success) {
									$('#leadFarmerDeletionModal').fadeOut();
									Swal.fire({
										icon: 'success',
										title: 'Success!',
										text: response.message,
										confirmButtonColor: '#10B981'
									}).then(() => {
										loadUsers(currentPage, searchTerm);
									});
								} else {
									Swal.fire({
										icon: 'error',
										title: 'Error',
										text: response.message,
										confirmButtonColor: '#10B981'
									});
								}
							},
							error: function(xhr) {
								Swal.fire({
									icon: 'error',
									title: 'Error',
									text: xhr.responseJSON?.message || 'Failed to process deletion',
									confirmButtonColor: '#10B981'
								});
							}
						});
					});
					
					$('#cancelAction, #closeLeadFarmerModal').click(function() {
						$('#leadFarmerDeletionModal').fadeOut();
					});
				},
				error: function(xhr) {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: 'Failed to load lead farmers',
						confirmButtonColor: '#10B981'
					});
				}
			});
		}

		$(document).on('click', '.view-photo', function(e) {
			e.preventDefault();
			const photoUrl = $(this).data('photo');
			Swal.fire({
				imageUrl: photoUrl,
				imageAlt: 'Profile Photo',
				showConfirmButton: false,
				showCloseButton: true,
				width: '300px',
				padding: '10px'
			});
		});

		updateActiveStats();
	});
</script>
@endsection
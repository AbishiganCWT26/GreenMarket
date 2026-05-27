@extends('admin.layouts.admin_master')

@section('title', 'User Management')

@section('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/Admin/index-user-management.css') }}">
	<style>
		.password-strength-container {
			height: 5px;
			width: 100%;
			background-color: #e5e7eb;
			border-radius: 3px;
			overflow: hidden;
		}

		.password-strength-bar {
			height: 100%;
			width: 0;
			transition: all 0.3s ease;
		}

		.password-feedback {
			font-size: 11px;
			line-height: 1.4;
		}

		.nic-status {
			font-weight: 500;
		}

		.password-container {
			position: relative;
		}

		.password-toggle {
			position: absolute;
			right: 15px;
			top: 50%;
			transform: translateY(-50%);
			cursor: pointer;
			color: #6b7280;
		}

		.role-filter-container {
			position: relative;
			min-width: 180px;
			z-index: 100;
		}

		.role-filter-trigger {
			display: flex;
			align-items: center;
			gap: 10px;
			background: white;
			border: 1px solid var(--border-color);
			border-radius: 12px;
			padding: 8px 16px;
			cursor: pointer;
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			font-size: 13px;
			color: var(--text-dark);
			box-shadow: var(--shadow-xs);
			user-select: none;
		}

		.role-filter-trigger:hover {
			border-color: var(--primary-green);
			box-shadow: var(--shadow-sm);
			transform: translateY(-1px);
		}

		.role-filter-trigger i.role-filter-icon {
			color: var(--primary-green);
			font-size: 14px;
		}

		.role-filter-trigger span {
			flex: 1;
			font-weight: 500;
		}

		.role-filter-trigger .arrow-icon {
			font-size: 10px;
			color: var(--muted);
			transition: transform 0.3s ease;
		}

		.role-filter-container.active .role-filter-trigger {
			border-color: var(--primary-green);
			box-shadow: 0 0 0 3px var(--focus-shadow);
		}

		.role-filter-container.active .arrow-icon {
			transform: rotate(180deg);
		}

		.role-filter-menu {
			position: absolute;
			top: calc(100% + 8px);
			left: 0;
			right: 0;
			background: white;
			border: 1px solid var(--border-color);
			border-radius: 12px;
			padding: 6px;
			box-shadow: var(--shadow-md);
			z-index: 1000;
			opacity: 0;
			visibility: hidden;
			transform: translateY(-10px);
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			max-height: 350px;
			overflow-y: auto;
		}

		.role-filter-container.active .role-filter-menu {
			opacity: 1;
			visibility: visible;
			transform: translateY(0);
		}

		.role-filter-item {
			padding: 10px 12px;
			border-radius: 8px;
			cursor: pointer;
			transition: all 0.2s ease;
			color: var(--text-dark);
			display: flex;
			align-items: center;
			gap: 10px;
			font-weight: 400;
		}

		.role-filter-item:hover {
			background: var(--body-bg);
			color: var(--primary-green);
		}

		.role-filter-item.active {
			background: rgba(16, 185, 129, 0.1);
			color: var(--primary-green);
			font-weight: 600;
		}

		.role-icon-wrapper {
			width: 22px;
			height: 22px;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-shrink: 0;
			position: relative;
		}

		.role-img-icon {
			width: 100%;
			height: 100%;
			object-fit: contain;
			border-radius: 4px;
			position: absolute;
			z-index: 2;
			background: white;
		}

		.role-img-icon.error {
			display: none;
		}

		.role-fa-fallback {
			font-size: 14px;
			position: absolute;
			z-index: 1;
		}

		.role-filter-item[data-value="farmer"] i {
			color: var(--success-color);
		}

		.role-filter-item[data-value="lead_farmer"] i {
			color: var(--info-color);
		}

		.role-filter-item[data-value="buyer"] i {
			color: var(--accent-amber);
		}

		.role-filter-item[data-value="facilitator"] i {
			color: var(--purple);
		}

		.role-filter-item[data-value="admin"] i {
			color: var(--danger-color);
		}

		.role-filter-item[data-value="delivery_rider"] i {
			color: var(--warning-color);
		}

		.error-text {
			color: #ef4444 !important;
			font-size: 0.85rem !important;
			margin-top: 5px !important;
			font-weight: 500 !important;
			display: block !important;
		}

		.swal2-html-container {
			white-space: normal !important;
		}
	</style>
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
						<input type="search" id="search-input" name="search" pattern=".*\S.*" required
							placeholder="Search users...">
						<button class="search-btn" type="submit">
							<i class="fas fa-search"></i>
						</button>
					</form>
					<div class="role-filter-container" id="role-filter-dropdown">
						<div class="role-filter-trigger">
							<i class="fas fa-filter role-filter-icon"></i>
							<span id="selected-role-name">All Roles</span>
							<i class="fas fa-chevron-down arrow-icon"></i>
						</div>
						<div class="role-filter-menu">
							<div class="role-filter-item active" data-value="">
								<div class="role-icon-wrapper">
									<i class="fas fa-users role-fa-fallback"></i>
								</div>
								All Roles
							</div>
							<div class="role-filter-item" data-value="admin">
								<div class="role-icon-wrapper">
									<img src="{{ asset('assets/images/Profiles/default-avatar.png') }}"
										class="role-img-icon" onerror="this.classList.add('error')">
									<i class="fas fa-user-shield role-fa-fallback"></i>
								</div>
								Administrator
							</div>
							<div class="role-filter-item" data-value="buyer">
								<div class="role-icon-wrapper">
									<img src="{{ asset('uploads/profile_pictures/buyer.svg') }}" class="role-img-icon"
										onerror="this.classList.add('error')">
									<i class="fas fa-shopping-basket role-fa-fallback"></i>
								</div>
								Buyer
							</div>
							<div class="role-filter-item" data-value="delivery_rider">
								<div class="role-icon-wrapper">
									<img src="{{ asset('assets/images/Profiles/Delivery-Rider.png') }}"
										class="role-img-icon" onerror="this.classList.add('error')">
									<i class="fas fa-motorcycle role-fa-fallback"></i>
								</div>
								Delivery Rider
							</div>
							<div class="role-filter-item" data-value="facilitator">
								<div class="role-icon-wrapper">
									<img src="{{ asset('assets/images/Profiles/facilitator.png') }}" class="role-img-icon"
										onerror="this.classList.add('error')">
									<i class="fas fa-hands-helping role-fa-fallback"></i>
								</div>
								Facilitator
							</div>
							<div class="role-filter-item" data-value="farmer">
								<div class="role-icon-wrapper">
									<img src="{{ asset('assets/images/farmer.png') }}" class="role-img-icon"
										onerror="this.classList.add('error')">
									<i class="fas fa-seedling role-fa-fallback"></i>
								</div>
								Farmer
							</div>
							<div class="role-filter-item" data-value="lead_farmer">
								<div class="role-icon-wrapper">
									<img src="{{ asset('assets/images/Profiles/lead-farmer.png') }}" class="role-img-icon"
										onerror="this.classList.add('error')">
									<i class="fas fa-user-graduate role-fa-fallback"></i>
								</div>
								Lead Farmer
							</div>
							
							
						</div>
						<input type="hidden" name="role" id="role-filter" value="">
					</div>
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
	<script src="{{ asset('js/form-validation.js') }}"></script>
	<script>
		$(document).ready(function () {
			let currentView = 'card';
			let currentPage = 1;
			let searchTerm = '';
			let currentRole = '';
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

				const ajaxData = {
					view: currentView,
					q: search,
					page: page
				};
				if (currentRole) ajaxData.role = currentRole;

				$.ajax({
					url: "{{ route('admin.users.index') }}",
					method: 'GET',
					data: ajaxData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					},
					success: function (response) {
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
								@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
								title: 'Success!',
								text: 'Users loaded successfully',
								timer: 1500,
								showConfirmButton: false
							});
						} else {
							Swal.fire({
								@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
								title: 'Error',
								text: 'Invalid response format',
								confirmButtonColor: '#10B981'
							});
						}
						showLoading(false);
					},
					error: function (xhr) {
						Swal.fire({
							@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
							title: 'Error',
							text: 'Failed to load users',
							confirmButtonColor: '#10B981'
						});
						showLoading(false);
					}
				});
			}

			const passwordRequirementsHtml = `
																							<div id="password-validation-rules" class="password-requirements-grid mt-2 p-3 bg-light rounded shadow-sm border" style="display: none;">
																								<h6 class="mb-2 text-dark" style="font-size: 0.8rem; font-weight: 600;"><i class="fas fa-shield-alt" style="margin-right: 8px;"></i>Security Standards</h6>

																								<div class="password-strength mb-3">
																									<div class="d-flex justify-content-between align-items-center mb-1">
																										<small style="font-size: 11px; font-weight: 600; color: #64748b;">Strength: <span id="strengthText">None</span></small>
																									</div>
																									<div class="strength-bar" id="strengthBar" style="height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
																										<div class="strength-fill" style="width: 0%; height: 100%; transition: width 0.3s; background: #cbd5e1;"></div>
																									</div>
																								</div>

																								<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
																									<div id="rule-length" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>8+ characters</div>
																									<div id="rule-number" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>1 Number</div>
																									<div id="rule-capital" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>1 Capital</div>
																									<div id="rule-lowercase" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>1 Lowercase</div>
																									<div id="rule-special" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>1 Special</div>
																									<div id="rule-no-space" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>No spaces</div>
																									<div id="rule-no-repeat" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>No 3x repeats</div>
																									<div id="rule-no-sequence" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>No sequences</div>
																									<div id="rule-not-common" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>Not common</div>
																									<div id="rule-no-links" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>No links</div>
																									<div id="rule-no-personal" class="rule-item" style="font-size: 11px; color: #64748b; display: flex; align-items: center;"><i class="fas fa-circle" style="font-size: 8px; margin-right: 8px;"></i>No personal</div>
																								</div>
																								<style>
																									.rule-item.valid { color: #10B981 !important; font-weight: 600; }
																									.rule-item.invalid { color: #ef4444 !important; }
																									.rule-item.valid i { color: #10B981 !important; margin-right: 8px; }
																									.rule-item.invalid i { color: #ef4444 !important; margin-right: 8px; }
																								</style>
																							</div>
																						`;

			function updatePasswordRules(password, username, email) {
				const result = validateAdvancedPassword(password, username, email);
				const rulesContainer = $('#password-validation-rules');

				if (password) {
					rulesContainer.fadeIn();
				} else {
					rulesContainer.hide();
				}

				Object.keys(result.rules).forEach(rule => {
					const el = $(`#rule-${rule}`);
					if (el.length) {
						const icon = el.find('i');
						if (result.rules[rule]) {
							el.addClass('valid').removeClass('invalid');
							icon.attr('class', 'fas fa-check-circle me-2');
						} else {
							if (password) {
								el.addClass('invalid').removeClass('valid');
								icon.attr('class', 'fas fa-times-circle me-2');
							} else {
								el.removeClass('valid invalid');
								icon.attr('class', 'fas fa-circle me-2');
							}
						}
					}
				});

				window.passwordValid = result.allValid;
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
																												<option value="delivery_rider">Delivery Rider</option>
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
						$('#user-type').on('change', function () {
							const userType = $(this).val();
							let html = '';

							switch (userType) {
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
																																<input type="password" id="password" class="form-input" placeholder="Enter password" required oninput="updatePasswordRules(this.value, $('#username').val(), $('#email').val())">
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
																															</div>
																															${passwordRequirementsHtml}
																														</div>
																														<div class="form-group">
																															<label>Confirm Password <span class="required">*</span></label>
																															<div class="password-container">
																																<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
																															</div>
																															<div id="passwordMatch" class="mt-2"></div>
																														</div>
																														<div class="form-group">
																															<label>NIC Number <span class="required">*</span></label>
																															<input type="text" id="farmer_nic" class="form-input" placeholder="Enter NIC" required>
																															<div id="farmer_nic_status" class="nic-status mt-1" style="font-size: 11px;"></div>
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
																																<input type="tel" id="farmer_ezcash" class="form-input" placeholder="e.g., 074/076/077xxxxxxx" maxlength="10">
																																<div id="ezcash-error" class="error-text" style="display:none;">EzCash number must start with 074, 076 or 077</div>
																															</div>
																														</div>
																														<div id="farmer-mcash-fields" style="display:none;">
																															<div class="form-group">
																																<label>mCash Mobile Number <span id="mcash-required" class="required" style="display:none;">*</span></label>
																																<input type="tel" id="farmer_mcash" class="form-input" placeholder="e.g., 070/071xxxxxxx" maxlength="10">
																																<div id="mcash-error" class="error-text" style="display:none;">mCash number must start with 070 or 071</div>
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
																																<input type="password" id="password" class="form-input" placeholder="Enter password" required oninput="updatePasswordRules(this.value, $('#username').val(), $('#email').val())">
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
																															</div>
																															${passwordRequirementsHtml}
																														</div>
																														<div class="form-group">
																															<label>Confirm Password <span class="required">*</span></label>
																															<div class="password-container">
																																<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
																															</div>
																															<div id="passwordMatch" class="mt-2"></div>
																														</div>
																														<div class="form-group">
																															<label>NIC Number <span class="required">*</span></label>
																															<input type="text" id="lead_nic" class="form-input" placeholder="Enter NIC" required>
																															<div id="lead_nic_status" class="nic-status mt-1" style="font-size: 11px;"></div>
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
																																<input type="password" id="password" class="form-input" placeholder="Enter password" required oninput="updatePasswordRules(this.value, $('#username').val(), $('#email').val())">
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
																															</div>
																															${passwordRequirementsHtml}
																														</div>
																														<div class="form-group">
																															<label>Confirm Password <span class="required">*</span></label>
																															<div class="password-container">
																																<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
																															</div>
																															<div id="passwordMatch" class="mt-2"></div>
																														</div>
																														<div class="form-group">
																															<label>NIC Number</label>
																															<input type="text" id="buyer_nic" class="form-input" placeholder="Enter NIC">
																															<div id="buyer_nic_status" class="nic-status mt-1" style="font-size: 11px;"></div>
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
																															<label>Google Map Link of the Residential Address</label>
																															<input type="url" id="buyer_google_map_link" class="form-input" placeholder="Enter Google Map link">
																															<small style="display:block; margin-top:5px; color:#6b7280; font-size:12px;">Mention product will be delivery to the Residential Address of the google map link</small>
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
																																<input type="password" id="password" class="form-input" placeholder="Enter password" required oninput="updatePasswordRules(this.value, $('#username').val(), $('#email').val())">
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
																															</div>
																															${passwordRequirementsHtml}
																														</div>
																														<div class="form-group">
																															<label>Confirm Password <span class="required">*</span></label>
																															<div class="password-container">
																																<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
																															</div>
																															<div id="passwordMatch" class="mt-2"></div>
																														</div>
																														<div class="form-group">
																															<label>NIC Number <span class="required">*</span></label>
																															<input type="text" id="facilitator_nic" class="form-input" placeholder="Enter NIC" required>
																															<div id="facilitator_nic_status" class="nic-status mt-1" style="font-size: 11px;"></div>
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
									html = `
																													<div class="form-section">
																														<h4>Administrator Details</h4>
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
																																<input type="password" id="password" class="form-input" placeholder="Enter password" required oninput="updatePasswordRules(this.value, $('#username').val(), $('#email').val())">
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
																															</div>
																															${passwordRequirementsHtml}
																														</div>
																														<div class="form-group">
																															<label>Confirm Password <span class="required">*</span></label>
																															<div class="password-container">
																																<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
																															</div>
																															<div id="passwordMatch" class="mt-2"></div>
																														</div>
																														<div class="form-group">
																															<label>NIC Number</label>
																															<input type="text" id="admin_nic" class="form-input" placeholder="Enter NIC">
																															<div id="admin_nic_status" class="nic-status mt-1" style="font-size: 11px;"></div>
																														</div>
																														<div class="form-group">
																															<label>Phone Number <span class="required">*</span></label>
																															<input type="tel" id="admin_phone" class="form-input" placeholder="Enter phone number" required>
																														</div>
																													</div>
																												`;
									break;
								case 'delivery_rider':
									html = `
																													<div class="form-section">
																														<h4>Delivery Rider Details</h4>
																														<div class="form-group">
																															<label>Full Name <span class="required">*</span></label>
																															<input type="text" id="name" class="form-input" placeholder="Enter full name" required>
																														</div>
																														<div class="form-group">
																															<label>Username <span class="required">*</span></label>
																															<input type="text" id="username" class="form-input" placeholder="Enter username" required>
																														</div>
																														<div class="form-group">
																															<label>Email <span class="required">*</span></label>
																															<input type="email" id="email" class="form-input" placeholder="Enter email" required>
																														</div>
																														<div class="form-group">
																															<label>Password <span class="required">*</span></label>
																															<div class="password-container">
																																<input type="password" id="password" class="form-input" placeholder="Enter password" required oninput="updatePasswordRules(this.value, $('#username').val(), $('#email').val())">
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
																															</div>
																															${passwordRequirementsHtml}
																														</div>
																														<div class="form-group">
																															<label>Confirm Password <span class="required">*</span></label>
																															<div class="password-container">
																																<input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
																																<i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
																															</div>
																															<div id="passwordMatch" class="mt-2"></div>
																														</div>
																														<div class="form-group">
																															<label>NIC Number <span class="required">*</span></label>
																															<input type="text" id="rider_nic" class="form-input" placeholder="Enter NIC" required>
																															<div id="rider_nic_status" class="nic-status mt-1" style="font-size: 11px;"></div>
																														</div>
																														<div class="form-group">
																															<label>Licence Number <span class="required">*</span></label>
																															<input type="text" id="licence_number" class="form-input" placeholder="Enter Licence Number" required>
																														</div>
																														<div class="form-group">
																															<label>Primary Mobile <span class="required">*</span></label>
																															<input type="tel" id="rider_mobile" class="form-input" placeholder="Enter mobile number" required>
																														</div>
																														<div class="form-group">
																															<label>WhatsApp Number</label>
																															<input type="tel" id="rider_whatsapp" class="form-input" placeholder="Enter WhatsApp">
																														</div>
																														<div class="form-group">
																															<label>Vehicle Number <span class="required">*</span></label>
																															<input type="text" id="vehicle_number" class="form-input" placeholder="Enter vehicle number" required>
																														</div>
																														<div class="form-group">
																															<label>Vehicle Type <span class="required">*</span></label>
																															<select id="vehicle_type" class="form-select" required>
																																<option value="" disabled selected>Select Vehicle Type</option>
																																<option value="Bicycle">Bicycle</option>
																																<option value="Motorbike">Motorbike</option>
																																<option value="Three-Wheeler">Three-Wheeler</option>
																																<option value="Mini Truck">Mini Truck</option>
																																<option value="Truck">Truck</option>
																															</select>
																														</div>
																														<div class="form-group">
																															<label>Max KG Capacity <span class="required">*</span></label>
																															<input type="number" id="max_kg_capacity" class="form-input" placeholder="Enter max capacity in KG" required min="1">
																														</div>
																														<div class="form-group">
																															<label>Assigned District(s) <span class="required">*</span></label>
																															<select id="assigned_districts" class="form-select" multiple required style="height: 120px; padding: 5px;">
																																${Object.keys(gnData).map(d => `<option value="${d}">${d}</option>`).join('')}
																															</select>
																															<small class="text-muted d-block mt-1">Hold Ctrl (Windows) / Cmd (Mac) to select multiple districts</small>
																														</div>
																														<div class="form-group">
																															<label>Residential Address <span class="required">*</span></label>
																															<textarea id="rider_address" class="form-input" placeholder="Enter Residential Address" rows="2" required></textarea>
																														</div>
																														<div class="form-group">
																															<label>Any Needed Details</label>
																															<textarea id="extra_details" class="form-input" placeholder="availability, notes..." rows="2"></textarea>
																														</div>
																													</div>
																												`;
									break;
							}

							$('#role-specific-fields').html(html).show();

							if (userType === 'farmer') {
								$('#farmer_payment').on('change', function () {
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

								districtSelect.on('change', function () {
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

								dsSelect.on('change', function () {
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

								gndSelect.on('change', function () {
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

									dsSelect.on('change', function () {
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

									gnSelect.on('change', function () {
										const code = $(this).find(':selected').data('code');
										codeInput.val(code || '');
									});
								};

								$('#facilitator_district').on('change', function () {
									$('.assignment-item').each(function () {
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

								$('#add-assignment').on('click', function () {
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
									newItem.find('.remove-assignment').on('click', function () {
										newItem.remove();
									});
								});
							}

							// WhatsApp Auto-fill Logic
							const roleMap = {
								'farmer': { mobile: '#farmer_mobile', whatsapp: '#farmer_whatsapp' },
								'lead_farmer': { mobile: '#lead_mobile', whatsapp: '#lead_whatsapp' },
								'buyer': { mobile: '#buyer_mobile', whatsapp: '#buyer_whatsapp' },
								'facilitator': { mobile: '#facilitator_mobile', whatsapp: '#facilitator_whatsapp' },
								'delivery_rider': { mobile: '#rider_mobile', whatsapp: '#rider_whatsapp' }
							};

							if (roleMap[userType]) {
								const { mobile, whatsapp } = roleMap[userType];
								$(mobile).on('input', function () {
									const mobileVal = $(this).val();
									const whatsappField = $(whatsapp);
									if (!whatsappField.val() || whatsappField.val() === $(this).data('prev-mobile')) {
										whatsappField.val(mobileVal);
									}
									$(this).data('prev-mobile', mobileVal);
								});

								$(whatsapp).on('input', function () {
									// If user manually edits WhatsApp, we stop auto-sync if they clear it
									if (!$(this).val()) {
										$(mobile).data('prev-mobile', '');
									}
								});
							}
							// Real-time validation listeners
							// Real-time validation listeners are handled by global updatePasswordRules for password
							// and specific listeners for match and other fields

							$(document).on('input', '#password_confirmation', function () {
								const pwd = $('#password').val();
								const confirm = $(this).val();
								const matchDiv = $('#passwordMatch');
								if (!confirm) {
									matchDiv.html('');
								} else if (pwd === confirm) {
									matchDiv.html('<small style="color: #10B981; font-weight: 600;"><i class="fas fa-check-circle"></i> Passwords match</small>');
								} else {
									matchDiv.html('<small style="color: #EF4444; font-weight: 600;"><i class="fas fa-times-circle"></i> Passwords do not match</small>');
								}
							});

							$(document).on('input', '#username, #email', function () {
								const pwd = $('#password').val();
								if (pwd) {
									updatePasswordRules(pwd, $('#username').val(), $('#email').val());
								}
							});

							const setupNICListener = (selector, statusSelector) => {
								$(document).on('input', selector, function () {
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
										statusDiv.html('<span style="color: #10B981; font-weight: 600;"><i class="fas fa-check-circle"></i> Valid NIC format</span>');
									} else {
										statusDiv.html('<span style="color: #EF4444; font-weight: 600;"><i class="fas fa-times-circle"></i> Invalid NIC format</span>');
									}
								});
							};

							setupNICListener('#farmer_nic', '#farmer_nic_status');
							setupNICListener('#lead_nic', '#lead_nic_status');
							setupNICListener('#facilitator_nic', '#facilitator_nic_status');
							setupNICListener('#buyer_nic', '#buyer_nic_status');
							setupNICListener('#admin_nic', '#admin_nic_status');
							setupNICListener('#rider_nic', '#rider_nic_status');

							const validateWallet = (fieldId, errorId, prefixes) => {
								$(document).on('input', fieldId, function () {
									const val = $(this).val();
									const error = $(errorId);
									if (val.length >= 3) {
										const prefix = val.substring(0, 3);
										if (!prefixes.includes(prefix)) {
											error.show();
										} else {
											error.hide();
										}
									} else if (val.length > 0) {
										if (!'07'.startsWith(val.substring(0, val.length))) {
											error.show();
										} else {
											error.hide();
										}
									} else {
										error.hide();
									}
								});
							};

							validateWallet('#farmer_ezcash', '#ezcash-error', ['074', '076', '077']);
							validateWallet('#farmer_mcash', '#mcash-error', ['070', '071']);

							$(document).on('input', '#primary_mobile', function () {
								const val = $(this).val();
								if (val && val.length > 10) $(this).val(val.substr(0, 10));
							});
						});
					},
					preConfirm: function () {
						const userType = $('#user-type').val();
						const name = $('#name').val();
						const username = $('#username').val();
						const email = $('#email').val();
						const password = $('#password').val();
						const passwordConfirmation = $('#password_confirmation').val();

						if (!userType || !name || !username || !password) {
							Swal.showValidationMessage('Please fill all required fields');
							return false;
						}

						if (password !== passwordConfirmation) {
							Swal.showValidationMessage('Passwords do not match');
							return false;
						}

						const advancedResult = validateAdvancedPassword(password, username, email);
						if (!advancedResult.allValid) {
							Swal.showValidationMessage('Your password must meet all 11 security standards listed.');
							const rulesContainer = $('#password-validation-rules');
							rulesContainer.fadeIn();
							return false;
						}

						let formData = {
							user_type: userType,
							name: name,
							username: username,
							email: email,
							password: password,
							password_confirmation: passwordConfirmation
						};

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

							const nic = $('#farmer_nic').val();
							if (!nic) {
								Swal.showValidationMessage('NIC Number is required for Farmers');
								return false;
							}
							if (!validateNIC(nic)) {
								Swal.showValidationMessage('Invalid NIC format');
								return false;
							}

							const paymentMethod = $('#farmer_payment').val() || 'bank';
							formData.lead_farmer_id = leadFarmerId;
							formData.nic_no = nic;
							formData.primary_mobile = $('#farmer_mobile').val() || '';
							formData.whatsapp_number = $('#farmer_whatsapp').val() || formData.primary_mobile;
							formData.residential_address = $('#farmer_address').val() || '';
							formData.district = $('#farmer_district').val() || '';
							formData.divisional_secretariat = $('#farmer_ds').val() || '';
							formData.grama_niladhari_division = gnd;
							formData.gn_division_code = $('#farmer_gn_code').val() || '';
							formData.preferred_payment = paymentMethod;

							if (paymentMethod === 'bank' || paymentMethod === 'all') {
								const account = $('#farmer_account').val() || '';
								const accountName = $('#farmer_account_name').val() || '';
								const bankName = $('#farmer_bank').val() || '';
								const bankBranch = $('#farmer_branch').val() || '';

								if (!account || !accountName || !bankName || !bankBranch) {
									Swal.showValidationMessage('All bank details are required for bank transfer');
									return false;
								}
								formData.account_number = account;
								formData.account_holder_name = accountName;
								formData.bank_name = bankName;
								formData.bank_branch = bankBranch;
							}

							if (paymentMethod === 'ezcash' || paymentMethod === 'all') {
								const ezcash = $('#farmer_ezcash').val();
								if (!ezcash) {
									Swal.showValidationMessage('EzCash number is required');
									return false;
								}
								if (!/^(074|076|077)/.test(ezcash)) {
									Swal.showValidationMessage('EzCash number must start with 074, 076, or 077');
									return false;
								}
								if (ezcash.length !== 10) {
									Swal.showValidationMessage('EzCash number must be 10 digits');
									return false;
								}
								formData.ezcash_mobile = ezcash;
							}

							if (paymentMethod === 'mcash' || paymentMethod === 'all') {
								const mcash = $('#farmer_mcash').val();
								if (!mcash) {
									Swal.showValidationMessage('mCash number is required');
									return false;
								}
								if (!/^(070|071)/.test(mcash)) {
									Swal.showValidationMessage('mCash number must start with 070 or 071');
									return false;
								}
								if (mcash.length !== 10) {
									Swal.showValidationMessage('mCash number must be 10 digits');
									return false;
								}
								formData.mcash_mobile = mcash;
							}
						} else if (userType === 'lead_farmer') {
							const gnd = $('#lead_gnd').val();
							if (!gnd) {
								Swal.showValidationMessage('Grama Niladhari Division is required');
								return false;
							}
							const nic = $('#lead_nic').val();
							if (!nic) {
								Swal.showValidationMessage('NIC Number is required for Lead Farmers');
								return false;
							}
							if (!validateNIC(nic)) {
								Swal.showValidationMessage('Invalid NIC format');
								return false;
							}
							formData.nic_no = nic || '';
							formData.primary_mobile = $('#lead_mobile').val() || '';
							formData.whatsapp_number = $('#lead_whatsapp').val() || formData.primary_mobile;
							formData.residential_address = $('#lead_address').val() || '';
							formData.district = $('#lead_district').val() || '';
							formData.divisional_secretariat = $('#lead_ds').val() || '';
							formData.grama_niladhari_division = gnd;
							formData.gn_division_code = $('#lead_gn_code').val() || '';
							formData.group_name = $('#lead_group_name').val() || '';
							formData.group_number = $('#lead_group_number').val() || '';
							formData.account_number = $('#lead_account').val() || '';
							formData.account_holder_name = $('#lead_account_name').val() || '';
							formData.bank_name = $('#lead_bank').val() || '';
							formData.bank_branch = $('#lead_branch').val() || '';
							formData.preferred_payment = 'bank';
						} else if (userType === 'facilitator') {
							const district = $('#facilitator_district').val();
							const nic = $('#facilitator_nic').val();
							if (!nic || !validateNIC(nic)) {
								Swal.showValidationMessage('Valid NIC is required for Facilitators');
								return false;
							}
							const assignments = [];
							$('.assignment-item').each(function () {
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
							if (assignments.length === 0) {
								Swal.showValidationMessage('At least one division assignment is required');
								return false;
							}
							formData.nic_no = nic;
							formData.primary_mobile = $('#facilitator_mobile').val() || '';
							formData.whatsapp_number = $('#facilitator_whatsapp').val() || formData.primary_mobile;
							formData.assignments = assignments;
							formData.district = district;
							formData.divisional_secretariat = assignments[0].divisional_secretariat;
							formData.assigned_division = assignments[0].gn_division;
							formData.gn_division_code = assignments[0].gn_division_code;
						} else if (userType === 'buyer') {
							const nic = $('#buyer_nic').val();
							if (nic && !validateNIC(nic)) {
								Swal.showValidationMessage('Invalid NIC format');
								return false;
							}
							formData.nic_no = nic || '';
							formData.primary_mobile = $('#buyer_mobile').val() || '';
							formData.whatsapp_number = $('#buyer_whatsapp').val() || formData.primary_mobile;
							formData.residential_address = $('#buyer_address').val() || '';
							formData.google_map_link = $('#buyer_google_map_link').val() || '';
							formData.district = $('#buyer_district').val() || '';
							formData.business_name = $('#buyer_business').val() || '';
							formData.business_type = $('#buyer_type').val() || 'individual';
						} else if (userType === 'admin') {
							const nic = $('#admin_nic').val();
							if (nic && !validateNIC(nic)) {
								Swal.showValidationMessage('Invalid NIC format');
								return false;
							}
							formData.nic_no = nic || '';
							formData.phone_number = $('#admin_phone').val() || '';
						} else if (userType === 'delivery_rider') {
							const email = $('#email').val();
							if (!email) {
								Swal.showValidationMessage('Email Address is required');
								return false;
							}
							const nic = $('#rider_nic').val();
							if (!nic || !validateNIC(nic)) {
								Swal.showValidationMessage('Valid NIC is required for Delivery Riders');
								return false;
							}
							const licenceNo = $('#licence_number').val();
							if (!licenceNo) {
								Swal.showValidationMessage('Licence Number is required');
								return false;
							}
							const mobile = $('#rider_mobile').val();
							if (!mobile) {
								Swal.showValidationMessage('Phone Number is required');
								return false;
							}
							if (!/^\d{10}$/.test(mobile)) {
								Swal.showValidationMessage('Primary Mobile number must be exactly 10 digits and contain only numbers');
								return false;
							}
							const whatsapp = $('#rider_whatsapp').val();
							if (whatsapp && !/^\d{10}$/.test(whatsapp)) {
								Swal.showValidationMessage('WhatsApp Number must be exactly 10 digits and contain only numbers');
								return false;
							}
							const vehicleNo = $('#vehicle_number').val();
							if (!vehicleNo) {
								Swal.showValidationMessage('Vehicle Number is required');
								return false;
							}
							const vehicleType = $('#vehicle_type').val();
							if (!vehicleType) {
								Swal.showValidationMessage('Vehicle Type is required');
								return false;
							}
							const maxCapacity = $('#max_kg_capacity').val();
							if (!maxCapacity || maxCapacity <= 0) {
								Swal.showValidationMessage('Valid Max KG Capacity is required');
								return false;
							}
							const districts = $('#assigned_districts').val();
							if (!districts || districts.length === 0) {
								Swal.showValidationMessage('Please assign at least one district');
								return false;
							}
							const address = $('#rider_address').val();
							if (!address) {
								Swal.showValidationMessage('Residential Address is required');
								return false;
							}
							formData.nic_no = nic;
							formData.licence_number = licenceNo;
							formData.primary_mobile = mobile;
							formData.whatsapp_number = whatsapp || mobile;
							formData.vehicle_number = vehicleNo;
							formData.vehicle_type = vehicleType;
							formData.max_kg_capacity = maxCapacity;
							formData.assigned_districts = districts;
							formData.residential_address = address;
							formData.extra_details = $('#extra_details').val() || '';
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
							success: function (response) {
								if (response.success) {
									Swal.fire({
										@if(file_exists(public_path('assets/icons/Gif/success3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
										title: 'Success!',
										text: response.message,
										confirmButtonColor: '#10B981'
									}).then(() => {
										loadUsers();
									});
								} else {
									Swal.fire({
										@if(file_exists(public_path('assets/icons/Gif/error5.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error5.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
										title: 'Error',
										text: response.message,
										confirmButtonColor: '#10B981'
									});
								}
							},
							error: function (xhr) {
								let error = 'Failed to create user';

								if (xhr.status === 422 && xhr.responseJSON?.errors) {
									const errors = xhr.responseJSON.errors;
									error = Object.values(errors).flat()[0];
								} else if (xhr.responseJSON?.message) {
									error = xhr.responseJSON.message;
								}

								Swal.fire({
									@if(file_exists(public_path('assets/icons/Gif/Failed1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Failed1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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

			window.togglePasswordVisibility = function (fieldId) {
				const field = $(`#${fieldId}`);
				// Find the toggle icon relative to the field container to be more precise
				const container = field.closest('.password-container');
				const toggleIcon = container.find('.password-toggle');

				if (field.attr('type') === 'password') {
					field.attr('type', 'text');
					toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
				} else {
					field.attr('type', 'password');
					toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
				}
			};

			window.updatePasswordRules = function (password, username, email) {
				const rulesContainer = $('#password-validation-rules');
				if (!password) {
					rulesContainer.hide();
					return;
				}

				const result = validateAdvancedPassword(password, { username, email });
				rulesContainer.fadeIn();

				// Update strength bar
				const strengthBar = $('#strengthBar .strength-fill');
				const strengthText = $('#strengthText');

				strengthBar.css({
					'width': result.percent + '%',
					'background-color': result.color
				});
				strengthText.text(result.strengthText).css('color', result.color);

				// Update 11 rules grid
				updatePasswordRuleFeedback(result);

				window.passwordValid = result.allValid;
			};

			function isSequential(str) {
				for (let i = 0; i < str.length - 2; i++) {
					const c1 = str.toLowerCase().charCodeAt(i);
					const c2 = str.toLowerCase().charCodeAt(i + 1);
					const c3 = str.toLowerCase().charCodeAt(i + 2);
					if ((c1 + 1 === c2 && c2 + 1 === c3) || (c1 - 1 === c2 && c2 - 1 === c3)) return true;
				}
				return false;
			}

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


			$('#search-input').on('input', function () {
				const search = $(this).val();
				clearTimeout($(this).data('timeout'));
				$(this).data('timeout', setTimeout(() => {
					loadUsers(1, search);
				}, 500));
			});

			$('.search-bar').on('submit', function (e) {
				e.preventDefault();
				const search = $('#search-input').val();
				loadUsers(1, search);
			});

			$('#add-user-btn').click(showAddUserModal);

			$(document).on('click', '.pagination a', function (e) {
				e.preventDefault();
				const page = $(this).attr('href').split('page=')[1];
				loadUsers(page, searchTerm);
			});

			$(document).on('click', '.action-btn', function (e) {
				e.preventDefault();
				const action = $(this).data('action');
				const userId = $(this).data('user-id');
				const userName = $(this).data('user-name') || 'User';

				switch (action) {
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
							@if(file_exists(public_path('assets/icons/Gif/alert3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
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
									success: function (response) {
										if (response.success) {
											Swal.fire({
												@if(file_exists(public_path('assets/icons/Gif/success3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
												title: 'Suspended!',
												text: response.message,
												confirmButtonColor: '#10B981'
											}).then(() => {
												loadUsers(currentPage, searchTerm);
											});
										} else {
											Swal.fire({
												@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
												title: 'Error',
												text: response.message,
												confirmButtonColor: '#10B981'
											});
										}
									},
									error: function (xhr) {
										Swal.fire({
											@if(file_exists(public_path('assets/icons/Gif/Failed1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Failed1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
							success: function (response) {
								if (response.success) {
									Swal.fire({
										@if(file_exists(public_path('assets/icons/Gif/success4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
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
							@if(file_exists(public_path('assets/icons/Gif/question2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
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
									success: function (response) {
										if (response.success) {
											Swal.fire({
												@if(file_exists(public_path('assets/icons/Gif/success3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
												title: 'Promoted!',
												text: response.message,
												confirmButtonColor: '#10B981'
											}).then(() => {
												loadUsers(currentPage, searchTerm);
											});
										} else {
											Swal.fire({
												@if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
												title: 'Error',
												text: response.message,
												confirmButtonColor: '#10B981'
											});
										}
									},
									error: function (xhr) {
										Swal.fire({
											@if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
					@if(file_exists(public_path('assets/icons/Gif/alert3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
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
							success: function (response) {
								if (response.success) {
									Swal.fire({
										@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
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
										@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
										title: 'Error',
										text: response.message,
										confirmButtonColor: '#10B981'
									});
								}
							},
							error: function (xhr) {
								Swal.fire({
									@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
					success: function (leadFarmersResponse) {
						let leadFarmersHtml = '';
						if (leadFarmersResponse.leadFarmers && leadFarmersResponse.leadFarmers.length > 0) {
							leadFarmersHtml = '<option value="">Select Lead Farmer</option>';
							leadFarmersResponse.leadFarmers.forEach(function (leadFarmer) {
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

						$('.option-card').click(function () {
							$('.option-card').removeClass('selected');
							$(this).addClass('selected');
							selectedAction = $(this).data('action');
							$('#modalActions').show();
							$('#confirmAction').prop('disabled', true);

							if (selectedAction === 'transfer') {
								$(this).find('.transfer-select').show();
								$('#newLeadFarmerSelect').on('change', function () {
									newLeadFarmerId = $(this).val();
									$('#confirmAction').prop('disabled', !newLeadFarmerId);
								});
							} else {
								$('#confirmAction').prop('disabled', false);
							}
						});

						$('#confirmAction').click(function () {
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
								success: function (response) {
									if (response.success) {
										$('#leadFarmerDeletionModal').fadeOut();
										Swal.fire({
											@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
											title: 'Success!',
											text: response.message,
											confirmButtonColor: '#10B981'
										}).then(() => {
											loadUsers(currentPage, searchTerm);
										});
									} else {
										Swal.fire({
											@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
											title: 'Error',
											text: response.message,
											confirmButtonColor: '#10B981'
										});
									}
								},
								error: function (xhr) {
									Swal.fire({
										@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
										title: 'Error',
										text: xhr.responseJSON?.message || 'Failed to process deletion',
										confirmButtonColor: '#10B981'
									});
								}
							});
						});

						$('#cancelAction, #closeLeadFarmerModal').click(function () {
							$('#leadFarmerDeletionModal').fadeOut();
						});
					},
					error: function (xhr) {
						Swal.fire({
							@if(file_exists(public_path('assets/icons/Gif/Failed1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Failed1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
							title: 'Error',
							text: 'Failed to load lead farmers',
							confirmButtonColor: '#10B981'
						});
					}
				});
			}

			$(document).on('click', '.view-photo', function (e) {
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

			// Custom Role Dropdown Logic
			const $dropdown = $('#role-filter-dropdown');
			const $trigger = $dropdown.find('.role-filter-trigger');
			const $menu = $dropdown.find('.role-filter-menu');
			const $hiddenInput = $('#role-filter');
			const $selectedText = $('#selected-role-name');

			$trigger.on('click', function (e) {
				e.stopPropagation();
				$dropdown.toggleClass('active');
			});

			$(document).on('click', function (e) {
				if (!$dropdown.is(e.target) && $dropdown.has(e.target).length === 0) {
					$dropdown.removeClass('active');
				}
			});

			$dropdown.find('.role-filter-item').on('click', function () {
				const value = $(this).data('value');
				const text = $(this).text().trim();

				$dropdown.find('.role-filter-item').removeClass('active');
				$(this).addClass('active');

				$selectedText.text(text);
				$hiddenInput.val(value).trigger('change');
				$dropdown.removeClass('active');
			});

			// Role filter change handler
			$('#role-filter').on('change', function () {
				currentRole = $(this).val();
				loadUsers(1, searchTerm);
			});

			// Search form submit handler — use AJAX so filters are preserved
			$(document).on('submit', '.search-bar', function (e) {
				e.preventDefault();
				const search = $('#search-input').val().trim();
				loadUsers(1, search);
			});

			// Pagination click interceptor — preserve filter + search across pages
			$(document).on('click', '#pagination-container a', function (e) {
				e.preventDefault();
				const href = $(this).attr('href');
				if (!href) return;
				const url = new URL(href, window.location.href);
				const page = url.searchParams.get('page') || 1;
				loadUsers(parseInt(page), searchTerm);
			});

			updateActiveStats();
		});
	</script>
@endsection
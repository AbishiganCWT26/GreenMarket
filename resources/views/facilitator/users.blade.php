@extends('facilitator.layouts.facilitator_master')

@section('title', 'User Management')
@section('page-title', 'User Management')


@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/users.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
	<div class="row mb-4">
		<div class="col-12">
			<div class="glass-card p-4 mb-4">
				<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
					<div>
						<h1 class="h3 fw-bold text-primary mb-2"><i class="fa-solid fa-users-gear me-2"></i>User Management</h1>
						<p class="text-muted mb-0">Manage all users across the system with advanced controls</p>
					</div>
					<div class="d-flex flex-wrap gap-2">
						<button class="btn btn-success btn-sm d-flex align-items-center gap-2" onclick="exportUsers()">
							<i class="fa-solid fa-file-pdf"></i>
							<span>Export PDF</span>
						</button>
						<button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2" onclick="refreshUsers()">
							<i class="fa-solid fa-rotate"></i>
							<span>Refresh</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row mb-4">
		<div class="col-12">
			<div class="stats-container">
				<div class="row g-3">
					<div class="col-6 col-md-3">
						<div class="stat-card bg-green-soft">
							<div class="stat-icon">
								<i class="fa-solid fa-tractor text-green"></i>
							</div>
							<div class="stat-content">
								<h3 class="stat-value">{{ $userTypes['farmers'] ?? 0 }}</h3>
								<p class="stat-label">Farmers</p>
							</div>
						</div>
					</div>
					<div class="col-6 col-md-3">
						<div class="stat-card bg-blue-soft">
							<div class="stat-icon">
								<i class="fa-solid fa-user-tie text-blue"></i>
							</div>
							<div class="stat-content">
								<h3 class="stat-value">{{ $userTypes['lead_farmers'] ?? 0 }}</h3>
								<p class="stat-label">Lead Farmers</p>
							</div>
						</div>
					</div>
					<div class="col-6 col-md-3">
						<div class="stat-card bg-amber-soft">
							<div class="stat-icon">
								<i class="fa-solid fa-cart-shopping text-amber"></i>
							</div>
							<div class="stat-content">
								<h3 class="stat-value">{{ $userTypes['buyers'] ?? 0 }}</h3>
								<p class="stat-label">Buyers</p>
							</div>
						</div>
					</div>
					<div class="col-6 col-md-3">
						<div class="stat-card bg-purple-soft">
							<div class="stat-icon">
								<i class="fa-solid fa-hands-helping text-purple"></i>
							</div>
							<div class="stat-content">
								<h3 class="stat-value">{{ $userTypes['facilitators'] ?? 0 }}</h3>
								<p class="stat-label">Facilitators</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row mb-4">
		<div class="col-12">
			<div class="glass-card p-4">
				<div class="card-header border-0 bg-transparent p-0 mb-4">
					<h5 class="mb-0 fw-semibold"><i class="fa-solid fa-filter me-2"></i>Filter Users</h5>
				</div>
				<div class="row g-3">
					<div class="col-md-3 col-sm-6">
						<label class="form-label small fw-medium">Role Type</label>
						<div class="input-group">
							<span class="input-group-text bg-light border-end-0">
								<i class="fa-solid fa-user-tag text-muted"></i>
							</span>
							<select class="form-select border-start-0 ps-0" id="roleFilter">
								<option value="">All Roles</option>
								<option value="farmer">Farmer</option>
								<option value="lead_farmer">Lead Farmer</option>
								<option value="buyer">Buyer</option>
								<option value="facilitator">Facilitator</option>
							</select>
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<label class="form-label small fw-medium">Account Status</label>
						<div class="input-group">
							<span class="input-group-text bg-light border-end-0">
								<i class="fa-solid fa-circle-check text-muted"></i>
							</span>
							<select class="form-select border-start-0 ps-0" id="statusFilter">
								<option value="">All Status</option>
								<option value="active">Active</option>
								<option value="inactive">Inactive</option>
							</select>
						</div>
					</div>
					<div class="col-md-4 col-sm-8">
						<label class="form-label small fw-medium">Search Users</label>
						<div class="input-group">
							<span class="input-group-text bg-light border-end-0">
								<i class="fa-solid fa-search text-muted"></i>
							</span>
							<input type="text" class="form-control border-start-0 ps-0" id="searchFilter" placeholder="Name, Email, NIC, or Mobile">
						</div>
					</div>
					<div class="col-md-2 col-sm-4 d-flex align-items-end">
						<button class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" onclick="applyFilters()">
							<i class="fa-solid fa-filter"></i>
							<span>Filter</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="glass-card">
				<div class="card-header border-0 bg-transparent p-4 pb-0">
					<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
						<div>
							<h5 class="mb-1 fw-semibold"><i class="fa-solid fa-list me-2"></i>Users List</h5>
							<p class="text-muted small mb-0">{{ $users->count() }} users found</p>
						</div>
						<div class="d-flex align-items-center gap-2">
							<div class="badge bg-primary-subtle text-primary px-3 py-2">
								<i class="fa-solid fa-users me-1"></i>
								<span>Total: {{ $users->total() }}</span>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body p-4">
					<div class="table-responsive">
						<table class="table table-hover align-middle">
							<thead class="table-light">
								<tr>
									<th class="ps-4">User Profile</th>
									<th>Role</th>
									<th>Contact</th>
									<th>Location</th>
									<th>Status</th>
									<th>Joined Date</th>
									<th class="text-end pe-4">Actions</th>
								</tr>
							</thead>
							<tbody>
								@forelse($users as $user)
								<tr class="user-row" data-user-id="{{ $user->id }}">
									<td class="ps-4">
										<div class="d-flex align-items-center gap-3">
											<div class="avatar-wrapper">
												<img src="{{ asset('uploads/profile_pictures/' . ($user->profile_photo ?? 'default-avatar.png')) }}"
													class="rounded-circle avatar-img"
													onerror="this.onerror=null; this.src='{{ asset('assets/icons/user-icon.svg') }}';">
											</div>
											<div>
												<h6 class="mb-0 fw-medium">{{ $user->username }}</h6>
												<small class="text-muted d-block">{{ $user->email ?? 'No email' }}</small>
												<small class="text-muted">ID: {{ $user->id }}</small>
											</div>
										</div>
									</td>
									<td>
										@php
											$roleColor = match($user->role) {
												'farmer' => 'success',
												'lead_farmer' => 'primary',
												'buyer' => 'warning',
												'facilitator' => 'info',
												default => 'secondary'
											};
											$roleIcon = match($user->role) {
												'farmer' => 'fa-tractor',
												'lead_farmer' => 'fa-user-tie',
												'buyer' => 'fa-cart-shopping',
												'facilitator' => 'fa-hands-helping',
												default => 'fa-user'
											};
										@endphp
										<span class="badge bg-{{ $roleColor }}-subtle text-{{ $roleColor }} d-inline-flex align-items-center gap-1 py-2 px-3 rounded-pill">
											<i class="fa-solid {{ $roleIcon }} fa-sm"></i>
											{{ ucwords(str_replace('_', ' ', $user->role)) }}
										</span>
									</td>
									<td>
										@php
											$contact = '';
											if(isset($user->farmer) && $user->farmer) $contact = $user->farmer->primary_mobile ?? '';
											elseif(isset($user->leadFarmer) && $user->leadFarmer) $contact = $user->leadFarmer->primary_mobile ?? '';
											elseif(isset($user->buyer) && $user->buyer) $contact = $user->buyer->primary_mobile ?? '';
											elseif(isset($user->facilitator) && $user->facilitator) $contact = $user->facilitator->primary_mobile ?? '';
										@endphp
										@if(!empty($contact))
											<a href="tel:{{ $contact }}" class="text-decoration-none">
												<i class="fa-solid fa-phone fa-sm me-2 text-muted"></i>
												{{ $contact }}
											</a>
										@else
											<span class="text-muted">N/A</span>
										@endif
									</td>
									<td>
										@php
											$location = '';
											if(isset($user->farmer) && $user->farmer) $location = $user->farmer->district ?? '';
											elseif(isset($user->leadFarmer) && $user->leadFarmer) $location = $user->leadFarmer->grama_niladhari_division ?? '';
											elseif(isset($user->facilitator) && $user->facilitator) $location = $user->facilitator->assigned_division ?? '';
										@endphp
										@if(!empty($location))
											<div class="d-flex align-items-center gap-2">
												<i class="fa-solid fa-location-dot fa-sm text-muted"></i>
												<span>{{ $location }}</span>
											</div>
										@else
											<span class="text-muted">N/A</span>
										@endif
									</td>
									<td>
										@if($user->is_active)
										<span class="badge bg-success-subtle text-success d-inline-flex align-items-center gap-1 py-2 px-3 rounded-pill">
											<i class="fa-solid fa-circle-check"></i>
											Active
										</span>
										@else
										<span class="badge bg-danger-subtle text-danger d-inline-flex align-items-center gap-1 py-2 px-3 rounded-pill">
											<i class="fa-solid fa-circle-xmark"></i>
											Inactive
										</span>
										@endif
									</td>
									<td>
										<div class="d-flex flex-column">
											<span class="fw-medium">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</span>
											<small class="text-muted">
												{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}
											</small>
										</div>
									</td>
									<td class="text-end pe-4">
										<div class="btn-group btn-group-sm" role="group">
											<button class="btn btn-outline-primary btn-action" title="View Profile" onclick="viewUserProfile({{ $user->id }})">
												<i class="fa-solid fa-eye"></i>
											</button>
											<button class="btn btn-outline-success btn-action" title="Edit User" onclick="editUserWithOTP({{ $user->id }})">
												<i class="fa-solid fa-edit"></i>
											</button>
											@if($user->id !== Auth::id())
											<button class="btn btn-outline-danger btn-action" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}" onclick="toggleUserStatus({{ $user->id }}, '{{ $user->is_active ? 'deactivate' : 'activate' }}')">
												@if($user->is_active)
												<i class="fa-solid fa-user-slash"></i>
												@else
												<i class="fa-solid fa-user-check"></i>
												@endif
											</button>
											@endif
										</div>
									</td>
								</tr>
								@empty
								<tr>
									<td colspan="7" class="text-center py-5">
										<div class="empty-state">
											<i class="fa-solid fa-users-slash fa-3x text-muted mb-3"></i>
											<h5 class="mb-2">No Users Found</h5>
											<p class="text-muted mb-0">Try adjusting your filters or search criteria</p>
											<button class="btn btn-sm btn-outline-primary mt-3" onclick="clearFilters()">
												<i class="fa-solid fa-times me-2"></i>
												Clear Filters
											</button>
										</div>
									</td>
								</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					@if($users->hasPages())
					<div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
						<div class="text-muted small">
							Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
						</div>
						<nav aria-label="Page navigation">
							<ul class="pagination pagination-sm mb-0">
								<li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
									<a class="page-link" href="{{ $users->previousPageUrl() }}">
										<i class="fa-solid fa-chevron-left"></i>
									</a>
								</li>
								@for ($i = 1; $i <= $users->lastPage(); $i++)
								<li class="page-item {{ $users->currentPage() == $i ? 'active' : '' }}">
									<a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
								</li>
								@endfor
								<li class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
									<a class="page-link" href="{{ $users->nextPageUrl() }}">
										<i class="fa-solid fa-chevron-right"></i>
									</a>
								</li>
							</ul>
						</nav>
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

<div id="userProfileModal"></div>
<div id="editUserModal"></div>
<div id="otpVerificationModal"></div>

<script>
function applyFilters() {
	const role = document.getElementById('roleFilter').value;
	const status = document.getElementById('statusFilter').value;
	const search = document.getElementById('searchFilter').value;

	let params = new URLSearchParams();
	if (role) params.append('role', role);
	if (status) params.append('status', status);
	if (search) params.append('search', search);

	window.location.href = '{{ route("facilitator.users") }}?' + params.toString();
}

function clearFilters() {
	document.getElementById('roleFilter').value = '';
	document.getElementById('statusFilter').value = '';
	document.getElementById('searchFilter').value = '';
	applyFilters();
}

function refreshUsers() {
	const btn = event.target.closest('button');
	btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span class="ms-2">Refreshing...</span>';
	btn.disabled = true;

	setTimeout(() => {
		btn.innerHTML = '<i class="fa-solid fa-rotate"></i><span class="ms-2">Refresh</span>';
		btn.disabled = false;
		window.location.reload();
	}, 800);
}

function exportUsers() {
	Swal.fire({
		title: 'Export Users Report',
		html: `
			<div class="text-center py-3">
				<i class="fa-solid fa-file-pdf fa-3x text-danger mb-3"></i>
				<h5 class="mb-2">Generate PDF Report</h5>
				<p class="text-muted mb-3">Select report options</p>

				<div class="mb-3">
					<label class="form-label text-start d-block mb-2">Report Type</label>
					<select class="form-select" id="reportType">
						<option value="full">Full User List</option>
						<option value="active">Active Users Only</option>
						<option value="summary">Summary Report</option>
					</select>
				</div>

				<div class="mb-3">
					<label class="form-label text-start d-block mb-2">Include Details</label>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" id="includeContact" checked>
						<label class="form-check-label" for="includeContact">
							Contact Information
						</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" id="includeLocation" checked>
						<label class="form-check-label" for="includeLocation">
							Location Details
						</label>
					</div>
				</div>
			</div>
		`,
		showCancelButton: true,
		confirmButtonText: 'Generate PDF',
		confirmButtonColor: '#10B981',
		cancelButtonText: 'Cancel',
		background: '#ffffff',
		color: '#0f1724',
		width: '450px',
		preConfirm: () => {
			return {
				reportType: document.getElementById('reportType').value,
				includeContact: document.getElementById('includeContact').checked,
				includeLocation: document.getElementById('includeLocation').checked
			}
		}
	}).then(result => {
		if (result.isConfirmed) {
			Swal.fire({
				title: 'Generating PDF...',
				html: `
					<div class="text-center">
						<div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
							<span class="visually-hidden">Loading...</span>
						</div>
						<p>Preparing your report...</p>
						<p class="small text-muted">This may take a few seconds</p>
					</div>
				`,
				showConfirmButton: false,
				allowOutsideClick: false,
				background: '#ffffff',
				color: '#0f1724',
				width: '350px'
			});

			const params = new URLSearchParams();
			params.append('reportType', result.value.reportType);
			params.append('includeContact', result.value.includeContact);
			params.append('includeLocation', result.value.includeLocation);
			params.append('_token', '{{ csrf_token() }}');

			fetch('{{ route("facilitator.users.export") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: params.toString()
			})
			.then(response => response.blob())
			.then(blob => {
				Swal.close();
				const url = window.URL.createObjectURL(blob);
				const a = document.createElement('a');
				a.href = url;
				a.download = `Users_Report_${new Date().toISOString().split('T')[0]}.pdf`;
				document.body.appendChild(a);
				a.click();
				window.URL.revokeObjectURL(url);
				document.body.removeChild(a);

				Swal.fire({
					title: 'Success!',
					text: 'PDF report has been generated and downloaded.',
					icon: 'success',
					confirmButtonColor: '#10B981',
					background: '#ffffff',
					color: '#0f1724'
				});
			})
			.catch(error => {
				Swal.close();
				Swal.fire({
					title: 'Error!',
					text: 'Failed to generate PDF report. Please try again.',
					icon: 'error',
					confirmButtonColor: '#dc3545',
					background: '#ffffff',
					color: '#0f1724'
				});
			});
		}
	});
}

function viewUserProfile(userId) {
    Swal.fire({
        title: 'Loading Profile...',
        html: `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Fetching user details...</p>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        background: '#ffffff',
        color: '#0f1724',
        width: '300px'
    });

    fetch(`/facilitator/users/${userId}/profile`)
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            const user = data.user;
            const additionalInfo = JSON.parse(user.additional_info);
            let infoHtml = '';

            for (const [key, value] of Object.entries(additionalInfo)) {
                infoHtml += `
                    <div class="row mb-2">
                        <div class="col-5 fw-medium text-muted">${key}:</div>
                        <div class="col-7">${value}</div>
                    </div>
                `;
            }

            Swal.fire({
                title: `${user.username}'s Profile`,
                html: `
                    <div class="user-profile-view">
                        <div class="text-center mb-4">
                            <img src="${user.profile_photo}"
                                 class="rounded-circle mb-3"
                                 style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #10B981;">
                            <h5 class="mb-1">${user.username}</h5>
                            <p class="text-muted mb-3">${user.email || 'No email'}</p>
                            <div class="d-flex justify-content-center gap-2 mb-3">
                                <span class="badge bg-${user.is_active ? 'success' : 'danger'}-subtle text-${user.is_active ? 'success' : 'danger'}">
                                    <i class="fa-solid fa-circle fa-xs me-1"></i>
                                    ${user.status}
                                </span>
                                <span class="badge bg-primary-subtle text-primary">
                                    ${user.role_display}
                                </span>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="profile-info-card p-3 border rounded">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fa-solid fa-id-card text-primary"></i>
                                        <h6 class="mb-0">User ID</h6>
                                    </div>
                                    <p class="mb-0 fw-medium">${user.id}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="profile-info-card p-3 border rounded">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fa-solid fa-phone text-info"></i>
                                        <h6 class="mb-0">Contact</h6>
                                    </div>
                                    <p class="mb-0 fw-medium">${user.contact || 'N/A'}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="profile-info-card p-3 border rounded">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fa-solid fa-location-dot text-warning"></i>
                                        <h6 class="mb-0">Location</h6>
                                    </div>
                                    <p class="mb-0 fw-medium">${user.location || 'N/A'}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="profile-info-card p-3 border rounded">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fa-solid fa-calendar-check text-purple"></i>
                                        <h6 class="mb-0">Joined</h6>
                                    </div>
                                    <p class="mb-0 fw-medium">${user.joined_date}</p>
                                    <small class="text-muted">${user.joined_relative}</small>
                                </div>
                            </div>
                        </div>

                        ${infoHtml ? `
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="mb-3"><i class="fa-solid fa-info-circle me-2"></i>Additional Information</h6>
                            <div class="bg-light p-3 rounded">
                                ${infoHtml}
                            </div>
                        </div>
                        ` : ''}
                    </div>
                `,
                width: '600px',
                confirmButtonText: 'Close',
                confirmButtonColor: '#10B981',
                background: '#ffffff',
                color: '#0f1724'
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to load user profile',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire({
            title: 'Error!',
            text: 'Failed to load user profile',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    });
}

function editUserWithOTP(userId) {
    Swal.fire({
        title: 'Edit User - OTP Verification',
        html: `
            <div class="text-center py-3">
                <i class="fa-solid fa-shield-alt fa-3x text-primary mb-3"></i>
                <h5 class="mb-2">OTP Verification Required</h5>
                <p class="text-muted mb-4">For security reasons, editing user details requires OTP verification.</p>

                <div class="alert alert-info mb-4">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    An OTP will be sent to the user's registered mobile number.
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Send OTP',
        confirmButtonColor: '#10B981',
        cancelButtonText: 'Cancel',
        background: '#ffffff',
        color: '#0f1724',
        width: '500px'
    }).then(result => {
        if (result.isConfirmed) {
            sendOTPForEdit(userId);
        }
    });
}

function sendOTPForEdit(userId) {
    Swal.fire({
        title: 'Sending OTP...',
        html: `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Sending OTP to user's mobile...</p>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        background: '#ffffff',
        color: '#0f1724',
        width: '350px'
    });

    fetch(`/facilitator/users/${userId}/send-otp`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            showOTPVerificationModal(userId, data.contact);
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to send OTP',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire({
            title: 'Error!',
            text: 'Failed to send OTP',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    });
}

function showOTPVerificationModal(userId, contactLast4) {
    Swal.fire({
        title: 'Verify OTP',
        html: `
            <div class="text-center py-3">
                <i class="fa-solid fa-mobile-alt fa-3x text-primary mb-3"></i>
                <h5 class="mb-2">Enter OTP</h5>
                <p class="text-muted mb-3">Enter the 6-digit OTP sent to mobile ending with ${contactLast4 || 'user'}</p>

                <div class="otp-input-container mb-4">
                    <input type="text"
                           id="otpInput"
                           class="form-control text-center fs-4"
                           maxlength="6"
                           pattern="[0-9]*"
                           inputmode="numeric"
                           style="letter-spacing: 10px; height: 60px;">
                </div>

                <div class="text-muted small mb-3">
                    <i class="fa-solid fa-clock me-1"></i>
                    OTP expires in 5 minutes
                </div>

                <button class="btn btn-link text-decoration-none" onclick="sendOTPForEdit(${userId})">
                    <i class="fa-solid fa-redo me-1"></i>
                    Resend OTP
                </button>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Verify OTP',
        confirmButtonColor: '#10B981',
        cancelButtonText: 'Cancel',
        background: '#ffffff',
        color: '#0f1724',
        width: '450px',
        preConfirm: () => {
            const otp = document.getElementById('otpInput').value;
            if (!otp || otp.length !== 6) {
                Swal.showValidationMessage('Please enter a valid 6-digit OTP');
                return false;
            }
            return { otp: otp };
        }
    }).then(result => {
        if (result.isConfirmed) {
            verifyOTP(userId, result.value.otp);
        }
    });
}

function verifyOTP(userId, otp) {
    Swal.fire({
        title: 'Verifying OTP...',
        html: `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Verifying OTP...</p>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        background: '#ffffff',
        color: '#0f1724',
        width: '300px'
    });

    fetch('/facilitator/users/verify-otp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            otp: otp,
            user_id: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'OTP verified successfully',
                icon: 'success',
                confirmButtonColor: '#10B981'
            }).then(() => {
                loadEditUserForm(userId);
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Invalid OTP',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire({
            title: 'Error!',
            text: 'Failed to verify OTP',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    });
}

function loadEditUserForm(userId) {
    Swal.fire({
        title: 'Loading User Data...',
        html: `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Loading user information...</p>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        background: '#ffffff',
        color: '#0f1724',
        width: '300px'
    });

    fetch(`/facilitator/users/${userId}/edit-data`)
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            showEditUserForm(data.user);
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to load user data',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire({
            title: 'Error!',
            text: 'Failed to load user data',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    });
}

function showEditUserForm(user) {
    let roleSpecificFields = '';
    let roleData = {};

    if (user.farmer) {
        roleData = user.farmer;
        roleSpecificFields = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="name" value="${roleData.name || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIC Number</label>
                    <input type="text" class="form-control" name="nic_no" value="${roleData.nic_no || ''}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Primary Mobile</label>
                    <input type="text" class="form-control" name="primary_mobile" value="${roleData.primary_mobile || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="text" class="form-control" name="whatsapp_number" value="${roleData.whatsapp_number || ''}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">District</label>
                    <input type="text" class="form-control" name="district" value="${roleData.district || ''}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Grama Niladhari Division</label>
                    <input type="text" class="form-control" name="grama_niladhari_division" value="${roleData.grama_niladhari_division || ''}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Residential Address</label>
                <textarea class="form-control" name="residential_address" rows="2">${roleData.residential_address || ''}</textarea>
            </div>
        `;
    } else if (user.lead_farmer) {
        roleData = user.lead_farmer;
        roleSpecificFields = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="name" value="${roleData.name || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIC Number</label>
                    <input type="text" class="form-control" name="nic_no" value="${roleData.nic_no || ''}" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Primary Mobile</label>
                    <input type="text" class="form-control" name="primary_mobile" value="${roleData.primary_mobile || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="text" class="form-control" name="whatsapp_number" value="${roleData.whatsapp_number || ''}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Group Name</label>
                    <input type="text" class="form-control" name="group_name" value="${roleData.group_name || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Group Number</label>
                    <input type="text" class="form-control" name="group_number" value="${roleData.group_number || ''}" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Grama Niladhari Division</label>
                <input type="text" class="form-control" name="grama_niladhari_division" value="${roleData.grama_niladhari_division || ''}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Residential Address</label>
                <textarea class="form-control" name="residential_address" rows="2">${roleData.residential_address || ''}</textarea>
            </div>
        `;
    } else if (user.buyer) {
        roleData = user.buyer;
        roleSpecificFields = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="name" value="${roleData.name || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIC Number</label>
                    <input type="text" class="form-control" name="nic_no" value="${roleData.nic_no || ''}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Primary Mobile</label>
                    <input type="text" class="form-control" name="primary_mobile" value="${roleData.primary_mobile || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="text" class="form-control" name="whatsapp_number" value="${roleData.whatsapp_number || ''}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Business Name</label>
                    <input type="text" class="form-control" name="business_name" value="${roleData.business_name || ''}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Business Type</label>
                    <select class="form-select" name="business_type">
                        <option value="individual" ${roleData.business_type === 'individual' ? 'selected' : ''}>Individual</option>
                        <option value="restaurant" ${roleData.business_type === 'restaurant' ? 'selected' : ''}>Restaurant</option>
                        <option value="hotel" ${roleData.business_type === 'hotel' ? 'selected' : ''}>Hotel</option>
                        <option value="retailer" ${roleData.business_type === 'retailer' ? 'selected' : ''}>Retailer</option>
                        <option value="wholesaler" ${roleData.business_type === 'wholesaler' ? 'selected' : ''}>Wholesaler</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Residential Address</label>
                <textarea class="form-control" name="residential_address" rows="2">${roleData.residential_address || ''}</textarea>
            </div>
        `;
    } else if (user.facilitator) {
        roleData = user.facilitator;
        roleSpecificFields = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="name" value="${roleData.name || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIC Number</label>
                    <input type="text" class="form-control" name="nic_no" value="${roleData.nic_no || ''}" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Primary Mobile</label>
                    <input type="text" class="form-control" name="primary_mobile" value="${roleData.primary_mobile || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="text" class="form-control" name="whatsapp_number" value="${roleData.whatsapp_number || ''}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="${roleData.email || ''}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assigned Division</label>
                    <input type="text" class="form-control" name="assigned_division" value="${roleData.assigned_division || ''}" required>
                </div>
            </div>
        `;
    }

    Swal.fire({
        title: `Edit User: ${user.username}`,
        html: `
            <form id="editUserForm">
                <input type="hidden" name="user_id" value="${user.id}">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" value="${user.username}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="${user.email || ''}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Account Status</label>
                    <select class="form-select" name="is_active">
                        <option value="1" ${user.is_active ? 'selected' : ''}>Active</option>
                        <option value="0" ${!user.is_active ? 'selected' : ''}>Inactive</option>
                    </select>
                </div>

                <hr class="my-4">

                <h6 class="mb-3">${user.role.replace('_', ' ').toUpperCase()} Details</h6>

                ${roleSpecificFields}
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update User',
        confirmButtonColor: '#10B981',
        cancelButtonText: 'Cancel',
        background: '#ffffff',
        color: '#0f1724',
        width: '700px',
        preConfirm: () => {
            const form = document.getElementById('editUserForm');
            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            return data;
        }
    }).then(result => {
        if (result.isConfirmed) {
            updateUser(user.id, result.value);
        }
    });
}

function updateUser(userId, formData) {
    Swal.fire({
        title: 'Updating User...',
        html: `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Updating user information...</p>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        background: '#ffffff',
        color: '#0f1724',
        width: '300px'
    });

    fetch(`/facilitator/users/${userId}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'User updated successfully',
                icon: 'success',
                confirmButtonColor: '#10B981'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to update user',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire({
            title: 'Error!',
            text: 'Failed to update user',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    });
}

function toggleUserStatus(userId, action) {
    const actionText = action === 'deactivate' ? 'Deactivate' : 'Activate';
    const confirmText = action === 'deactivate'
        ? 'This user will no longer be able to access the system.'
        : 'This user will regain access to the system.';

    Swal.fire({
        title: `${actionText} User?`,
        text: confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: `Yes, ${actionText}`,
        confirmButtonColor: action === 'deactivate' ? '#dc3545' : '#10B981',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        background: '#ffffff',
        color: '#0f1724',
        width: '450px'
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                html: `
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>${actionText} user...</p>
                    </div>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                background: '#ffffff',
                color: '#0f1724',
                width: '300px'
            });

            fetch(`/facilitator/users/${userId}/${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ _method: 'PUT' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.close();
                    Swal.fire({
                        title: 'Success!',
                        text: `User ${actionText}d successfully!`,
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                        background: '#ffffff',
                        color: '#0f1724'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Operation failed');
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: error.message || `Failed to ${action} user`,
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    background: '#ffffff',
                    color: '#0f1724'
                });
            });
        }
    });
}

document.getElementById('searchFilter').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('role')) {
        document.getElementById('roleFilter').value = urlParams.get('role');
    }
    if (urlParams.has('status')) {
        document.getElementById('statusFilter').value = urlParams.get('status');
    }
    if (urlParams.has('search')) {
        document.getElementById('searchFilter').value = urlParams.get('search');
    }
});
</script>
@endsection

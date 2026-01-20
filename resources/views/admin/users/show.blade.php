@extends('admin.layouts.admin_master')

@section('title', 'User Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/view-user-management.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="view-user-container">
	<div class="user-header-section">
		<div class="header-content">
			<div class="back-btn-wrapper">
				<a href="{{ route('admin.users.index') }}" class="btn-back">
					<i class="fas fa-arrow-left"></i>
				</a>
			</div>
			<div class="header-main">
				<h1><i class="fas fa-user-circle"></i> User Details</h1>
				<p>View complete information about this user</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('admin.users.edit', $user->id) }}" class="btn-edit-user">
					<i class="fas fa-edit"></i> Edit User
				</a>
			</div>
		</div>

		<div class="user-profile-card">
			<div class="profile-info">
				<div class="profile-main">
					<h2>{{ $user->username }}</h2>
					<div class="profile-meta">
						<span class="role-badge role-{{ $user->role }}">
							<i class="fas fa-{{ $user->role == 'admin' ? 'crown' : ($user->role == 'farmer' ? 'tractor' : ($user->role == 'buyer' ? 'shopping-cart' : 'user')) }}"></i>
							{{ ucfirst(str_replace('_', ' ', $user->role)) }}
						</span>
						<span class="user-id">ID: HGH{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
					</div>
				</div>
				<div class="profile-status">
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
		</div>
	</div>

	<div class="user-details-sections">
		<div class="details-section basic-section">
			<div class="section-header">
				<div class="section-icon">
					<i class="fas fa-user"></i>
				</div>
				<h3>Basic Information</h3>
			</div>
			<div class="section-content">
					<div class="detail-row">
						<div class="detail-label">
							<i class="fas fa-user"></i> Username
						</div>
						<div class="detail-value">{{ $user->username }}</div>
					</div>
					<div class="detail-row">
						<div class="detail-label">
							<i class="fas fa-envelope"></i> Email
						</div>
						<div class="detail-value">{{ $user->email ?: 'Not provided' }}</div>
					</div>
					<div class="detail-row">
						<div class="detail-label">
							<i class="fas fa-user-tag"></i> Role
						</div>
						<div class="detail-value">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
					</div>
					<div class="detail-row">
						<div class="detail-label">
							<i class="fas fa-calendar-alt"></i> Joined Date
						</div>
						<div class="detail-value">{{ date('M d, Y', strtotime($user->created_at)) }}</div>
					</div>
					<div class="detail-row">
						<div class="detail-label">
							<i class="fas fa-clock"></i> Last Login
						</div>
						<div class="detail-value">
							@if($user->last_login)
							{{ date('M d, Y h:i A', strtotime($user->last_login)) }}
							@else
							Never logged in
							@endif
						</div>
					</div>
			</div>
		</div>

		@if($details)
			@if(in_array($user->role, ['farmer', 'lead_farmer']))
			<div class="details-section profile-section">
				<div class="section-header">
					<div class="section-icon">
						<i class="fas fa-tractor"></i>
					</div>
					<h3>Profile Information</h3>
				</div>
				<div class="section-content">
						<div class="detail-row">
							<div class="detail-label">
								<i class="fas fa-id-card"></i> Full Name
							</div>
							<div class="detail-value">{{ $details->name }}</div>
						</div>
						<div class="detail-row">
							<div class="detail-label">
								<i class="fas fa-id-card"></i> NIC Number
							</div>
							<div class="detail-value">{{ $details->nic_no }}</div>
						</div>
						<div class="detail-row">
							<div class="detail-label">
								<i class="fas fa-phone"></i> Mobile Number
							</div>
							<div class="detail-value">{{ $details->primary_mobile }}</div>
						</div>
						@if(isset($details->whatsapp_number) && $details->whatsapp_number)
                        <div class="detail-row">
                            <div class="detail-label">
                                <i class="fab fa-whatsapp"></i> WhatsApp Number
                            </div>
                            <div class="detail-value">{{ $details->whatsapp_number }}</div>
                        </div>
                        @endif
						<div class="detail-row">
							<div class="detail-label">
								<i class="fas fa-home"></i> Address
							</div>
							<div class="detail-value">{{ $details->residential_address }}</div>
						</div>
						<div class="detail-row">
							<div class="detail-label">
								<i class="fas fa-map-marker-alt"></i> Grama Niladhari Division
							</div>
							<div class="detail-value">{{ $details->grama_niladhari_division }}</div>
						</div>
						@if($user->role == 'lead_farmer')
						<div class="detail-row">
							<div class="detail-label">
								<i class="fas fa-users"></i> Group Name
							</div>
							<div class="detail-value">{{ $details->group_name }}</div>
						</div>
						<div class="detail-row">
							<div class="detail-label">
								<i class="fas fa-hashtag"></i> Group Number
							</div>
							<div class="detail-value">{{ $details->group_number }}</div>
						</div>
						@endif
				</div>
			</div>

			<div class="details-section payment-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="section-title">
                        <h3>Payment Details</h3>
                        <p class="section-subtitle">Protected with OTP verification</p>
                    </div>
                    <div class="section-badge">
                        <span class="secure-badge">
                            <i class="fas fa-shield-alt"></i> Secure
                        </span>
                    </div>
                </div>
                <div class="section-content">
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-credit-card"></i> Preferred Payment Method
                        </div>
                        <div class="detail-value">
                            @if(isset($details->preferred_payment) && $details->preferred_payment)
                                {{ ucfirst($details->preferred_payment) }}
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                    @if(isset($details->account_number) && $details->account_number)
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-university"></i> Bank Account
                        </div>
                        <div class="detail-value">
                            {{ $details->account_number }}
                            @if(isset($details->bank_name) && $details->bank_name)
                                ({{ $details->bank_name }})
                            @endif
                        </div>
                    </div>
                    @endif
                    @if(isset($details->account_holder_name) && $details->account_holder_name)
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-user-tie"></i> Account Holder
                        </div>
                        <div class="detail-value">{{ $details->account_holder_name }}</div>
                    </div>
                    @endif

                    {{-- Show ezcash_mobile and mcash_mobile only for farmers (not lead farmers) --}}
                    @if($user->role == 'farmer')
                        @if(isset($details->ezcash_mobile) && $details->ezcash_mobile)
                        <div class="detail-row">
                            <div class="detail-label">
                                <i class="fas fa-mobile-alt"></i> Ez Cash Number
                            </div>
                            <div class="detail-value">{{ $details->ezcash_mobile }}</div>
                        </div>
                        @endif
                        @if(isset($details->mcash_mobile) && $details->mcash_mobile)
                        <div class="detail-row">
                            <div class="detail-label">
                                <i class="fas fa-phone-alt"></i> mCash Number
                            </div>
                            <div class="detail-value">{{ $details->mcash_mobile }}</div>
                        </div>
                        @endif
                    @endif

                    {{-- Show payment_details for lead farmers --}}
                    @if($user->role == 'lead_farmer' && isset($details->payment_details) && $details->payment_details)
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-info-circle"></i> Additional Payment Info
                        </div>
                        <div class="detail-value">{{ $details->payment_details }}</div>
                    </div>
                    @endif
                </div>
            </div>
			@endif

			@if($user->role == 'buyer')
            <div class="details-section business-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3>Business Information</h3>
                </div>
                <div class="section-content">
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-store"></i> Business Name
                        </div>
                        <div class="detail-value">
                            @if(isset($details->business_name) && $details->business_name)
                                {{ $details->business_name }}
                            @else
                                Not provided
                            @endif
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-building"></i> Business Type
                        </div>
                        <div class="detail-value">
                            @if(isset($details->business_type) && $details->business_type)
                                {{ ucfirst($details->business_type) }}
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-phone"></i> Contact Number
                        </div>
                        <div class="detail-value">
                            @if(isset($details->primary_mobile) && $details->primary_mobile)
                                {{ $details->primary_mobile }}
                            @else
                                Not provided
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

			@if($user->role == 'facilitator')
			<div class="details-section facilitator-section">
				<div class="section-header">
					<div class="section-icon">
						<i class="fas fa-user-tie"></i>
					</div>
					<h3>Facilitator Details</h3>
				</div>
				<div class="section-content">
						<div class="detail-row">
							<div class="detail-label">
								<i class="fas fa-id-card"></i> NIC Number
							</div>
							<div class="detail-value">{{ $details->nic_no }}</div>
						</div>
						<div class="detail-row">
							<div class="detail-label">
								<i class="fas fa-phone"></i> Mobile Number
							</div>
							<div class="detail-value">{{ $details->primary_mobile }}</div>
						</div>
						<div class="detail-row">
							<div class="detail-label">
								<i class="fas fa-map-pin"></i> Assigned Division
							</div>
							<div class="detail-value">{{ $details->assigned_division }}</div>
						</div>
				</div>
			</div>
			@endif
		@endif
	</div>

	<div class="user-actions-footer">
		<a href="{{ route('admin.users.index') }}" class="btn-back-list">
			<i class="fas fa-arrow-left"></i> Back to Users
		</a>
		<div class="action-buttons">
			@if($user->is_active)
			<button class="btn-suspend" data-user-id="{{ $user->id }}">
				<i class="fas fa-pause"></i> Suspend
			</button>
			@else
			<button class="btn-activate" data-user-id="{{ $user->id }}">
				<i class="fas fa-play"></i> Activate
			</button>
			@endif
			@if($user->role == 'farmer')
			<button class="btn-promote" data-user-id="{{ $user->id }}">
				<i class="fas fa-star"></i> Promote
			</button>
			@endif
			<button class="btn-send-notification" data-user-id="{{ $user->id }}">
				<i class="fas fa-bell"></i> Send Notification
			</button>
		</div>
	</div>
</div>

<div class="notification-modal-overlay" id="notificationModal">
	<div class="notification-modal-dialog">
		<div class="notification-modal-content">
			<div class="notification-modal-header">
				<div class="notification-modal-icon">
					<i class="fas fa-bell"></i>
				</div>
				<h3>Send Notification</h3>
				<button class="notification-close-btn" id="closeNotificationModal">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<div class="notification-modal-body">
				<div class="form-group">
					<label>Notification Type</label>
					<select id="notificationType" class="form-input">
						<option value="info">Information</option>
						<option value="warning">Warning</option>
						<option value="success">Success</option>
						<option value="error">Error</option>
					</select>
				</div>
				<div class="form-group">
					<label>Message</label>
					<textarea id="notificationMessage" class="form-input" rows="4" placeholder="Enter notification message..."></textarea>
				</div>
			</div>
			<div class="notification-modal-footer">
				<button class="btn-secondary" id="cancelNotification">
					Cancel
				</button>
				<button class="btn-primary" id="sendNotification">
					<i class="fas fa-paper-plane"></i> Send
				</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
	let currentUserId = null;

	function showAlert(icon, title, text) {
		Swal.fire({
			icon: icon,
			title: title,
			text: text,
			confirmButtonColor: '#10B981',
			confirmButtonText: 'OK',
			timer: 3000,
			timerProgressBar: true
		});
	}

	$('.btn-suspend').click(function() {
		const userId = $(this).data('user-id');
		const userName = '{{ $user->username }}';

		Swal.fire({
			title: 'Suspend User?',
			html: `Are you sure you want to suspend <strong>${userName}</strong>?`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#f59e0b',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, Suspend',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: `/admin/users/${userId}/suspend`,
					method: 'POST',
					data: {
						_token: '{{ csrf_token() }}'
					},
					success: function(response) {
						showAlert('success', 'User Suspended', 'The user has been suspended');
						setTimeout(() => {
							location.reload();
						}, 1500);
					},
					error: function(xhr) {
						showAlert('error', 'Failed', xhr.responseJSON?.message || 'Failed to suspend user');
					}
				});
			}
		});
	});

	$('.btn-activate').click(function() {
		const userId = $(this).data('user-id');
		const userName = '{{ $user->username }}';

		Swal.fire({
			title: 'Activate User?',
			html: `Are you sure you want to activate <strong>${userName}</strong>?`,
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, Activate',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: `/admin/users/${userId}/activate`,
					method: 'POST',
					data: {
						_token: '{{ csrf_token() }}'
					},
					success: function(response) {
						showAlert('success', 'User Activated', 'The user has been activated');
						setTimeout(() => {
							location.reload();
						}, 1500);
					},
					error: function(xhr) {
						showAlert('error', 'Failed', xhr.responseJSON?.message || 'Failed to activate user');
					}
				});
			}
		});
	});

	$('.btn-promote').click(function() {
		const userId = $(this).data('user-id');
		const userName = '{{ $user->username }}';

		Swal.fire({
			title: 'Promote to Lead Farmer?',
			html: `Are you sure you want to promote <strong>${userName}</strong> to Lead Farmer?`,
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#8b5cf6',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, Promote',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: `/admin/users/${userId}/promote`,
					method: 'POST',
					data: {
						_token: '{{ csrf_token() }}'
					},
					success: function(response) {
						showAlert('success', 'User Promoted', 'The user has been promoted to Lead Farmer');
						setTimeout(() => {
							location.reload();
						}, 1500);
					},
					error: function(xhr) {
						showAlert('error', 'Failed', xhr.responseJSON?.message || 'Failed to promote user');
					}
				});
			}
		});
	});

	$('.btn-send-notification').click(function() {
		currentUserId = $(this).data('user-id');
		$('#notificationModal').fadeIn();
	});

	$('#sendNotification').click(function() {
		const type = $('#notificationType').val();
		const message = $('#notificationMessage').val();

		if (!message.trim()) {
			showAlert('error', 'Error', 'Please enter a message');
			return;
		}

		$.ajax({
			url: '{{ route("admin.users.sendNotification") }}',
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				user_id: currentUserId,
				type: type,
				message: message
			},
			success: function(response) {
				$('#notificationModal').fadeOut();
				$('#notificationMessage').val('');
				showAlert('success', 'Notification Sent', 'Notification has been sent to the user');
			},
			error: function(xhr) {
				showAlert('error', 'Failed', 'Failed to send notification');
			}
		});
	});

	$('#closeNotificationModal, #cancelNotification').click(function() {
		$('#notificationModal').fadeOut();
		$('#notificationMessage').val('');
	});

	$(window).click(function(e) {
		if ($(e.target).hasClass('notification-modal-overlay')) {
			$('#notificationModal').fadeOut();
			$('#notificationMessage').val('');
		}
	});
});
</script>
@endsection

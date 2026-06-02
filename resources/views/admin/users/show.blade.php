@extends('admin.layouts.admin_master')

@section('title', 'User Details')

@section('styles')
	<link rel="stylesheet" href="{{ asset('css/Admin/view-user-management.css') }}">
	
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
								<i
									class="fas fa-{{ $user->role == 'admin' ? 'crown' : ($user->role == 'farmer' ? 'tractor' : ($user->role == 'buyer' ? 'shopping-cart' :'user')) }}"></i>
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
					<div class="detail-row">
						<div class="detail-label">
							<i class="fas fa-edit"></i> Last Updated
						</div>
						<div class="detail-value">
							@if($details && isset($details->updated_at))
								{{ date('M d, Y h:i A', strtotime($details->updated_at)) }}
							@else
								{{ date('M d, Y h:i A', strtotime($user->updated_at)) }}
							@endif
						</div>
					</div>
					<div class="detail-row">
						<div class="detail-label">
							<i class="fas fa-user-edit"></i> Updated By
						</div>
						<div class="detail-value">
							@if($details && isset($details->updated_by) && $details->updated_by)
								@php $updaterUser = \App\Models\User::find($details->updated_by); @endphp
								{{ $updaterUser ? $updaterUser->username . ' (ID: ' . $updaterUser->id . ')' : 'Unknown User' }}
							@else
								<span style="color: #6b7280; font-style: italic;">Not trackable (Self / Legacy)</span>
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
									<i class="fas fa-map-marker-alt"></i> District
								</div>
								<div class="detail-value">{{ $details->district ?? 'Not provided' }}</div>
							</div>
							<div class="detail-row">
								<div class="detail-label">
									<i class="fas fa-building"></i> Divisional Secretariat
								</div>
								<div class="detail-value">{{ $details->divisional_secretariat ?? 'Not provided' }}</div>
							</div>
							<div class="detail-row">
								<div class="detail-label">
									<i class="fas fa-map"></i> Grama Niladhari Division
								</div>
								<div class="detail-value">{{ $details->grama_niladhari_division ?? 'Not provided' }}</div>
							</div>
							<div class="detail-row">
								<div class="detail-label">
									<i class="fas fa-fingerprint"></i> GN Division Code
								</div>
								<div class="detail-value">{{ $details->gn_division_code ?? 'Not provided' }}</div>
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

					@if($user->role == 'farmer' && isset($details->lead_farmer_name))
						<div class="details-section lead-farmer-section">
							<div class="section-header">
								<div class="section-icon">
									<i class="fas fa-user-tie"></i>
								</div>
								<h3>Lead Farmer Information</h3>
							</div>
							<div class="section-content">
								<div class="detail-row">
									<div class="detail-label">
										<i class="fas fa-user"></i> Lead Farmer Name
									</div>
									<div class="detail-value">{{ $details->lead_farmer_name }}</div>
								</div>
								<div class="detail-row">
									<div class="detail-label">
										<i class="fas fa-map-marker-alt"></i> Lead Farmer District
									</div>
									<div class="detail-value">{{ $details->lead_farmer_district }}</div>
								</div>
								<div class="detail-row">
									<div class="detail-label">
										<i class="fas fa-phone"></i> Lead Farmer Contact
									</div>
									<div class="detail-value">{{ $details->lead_farmer_mobile }}</div>
								</div>
							</div>
						</div>
					@endif

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
							<div class="detail-row">
								<div class="detail-label">
									<i class="fab fa-whatsapp"></i> WhatsApp Number
								</div>
								<div class="detail-value">
									@if(isset($details->whatsapp_number) && $details->whatsapp_number)
										{{ $details->whatsapp_number }}
									@else
										Not provided
									@endif
								</div>
							</div>
							<div class="detail-row">
								<div class="detail-label">
									<i class="fas fa-map-marker-alt"></i> District
								</div>
								<div class="detail-value">
									@if(isset($details->district) && $details->district)
										{{ $details->district }}
									@else
										Not provided
									@endif
								</div>
							</div>
							<div class="detail-row">
								<div class="detail-label">
									<i class="fas fa-home"></i> Residential Address
								</div>
								<div class="detail-value">
									@if(isset($details->residential_address) && $details->residential_address)
										{{ $details->residential_address }}
									@else
										Not provided
									@endif
								</div>
							</div>
							<div class="detail-row">
								<div class="detail-label">
									<i class="fas fa-map-marked-alt"></i> Google Map Link
								</div>
								<div class="detail-value">
									@if(isset($details->google_map_link) && $details->google_map_link)
										<a href="{{ $details->google_map_link }}" target="_blank">View on Map</a>
										<br><small class="text-muted">Mention product will be delivery to the Residential Address of the google map link</small>
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
									<i class="fas fa-map-marker-alt"></i> District
								</div>
								<div class="detail-value">
									{{ (isset($details->assignments) && count($details->assignments) > 0) ? $details->assignments->first()->district : ($details->assigned_division ?? 'Not provided') }}
								</div>
							</div>
							<div class="detail-row" style="display: block;">
								<div class="detail-label" style="margin-bottom: 10px;">
									<i class="fas fa-th-list"></i> Assigned Divisions
								</div>
								<div class="detail-value">
									@if(isset($details->assignments) && count($details->assignments) > 0)
										<div class="assignments-list"
											style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
											@foreach($details->assignments as $assignment)
												<div class="assignment-card"
													style="border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; background: rgba(0,0,0,0.02);">
													<div style="font-weight: 600; font-size: 0.9rem; color: var(--primary-color);">
														{{ $assignment->district }} - {{ $assignment->divisional_secretariat }}</div>
													<div style="font-size: 0.85rem;">{{ $assignment->gn_division }}</div>
													<div style="font-size: 0.75rem; color: var(--text-muted);">Code:
														{{ $assignment->gn_division_code }}</div>
												</div>
											@endforeach
										</div>
									@else
										<div class="no-assignments" style="color: var(--text-muted); font-style: italic;">
											No divisions assigned
										</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				@endif
								</div>
							</div>
							<div class="detail-row">
								<div class="detail-label">
									<i class="fas fa-map-marked-alt"></i> Assigned Districts
								</div>
								<div class="detail-value">
									@if(isset($details->assigned_districts))
										@php
											$assignedDistricts = is_string($details->assigned_districts) ? json_decode($details->assigned_districts, true) : $details->assigned_districts;
										@endphp
										@if(is_array($assignedDistricts) && count($assignedDistricts) > 0)
											{{ implode(', ', $assignedDistricts) }}
										@else
											No districts assigned
										@endif
									@else
										No districts assigned
									@endif
								</div>
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
						<textarea id="notificationMessage" class="form-input" rows="3"
							placeholder="Enter notification message..."></textarea>
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
	
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(document).ready(function () {
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

			$('.btn-suspend').click(function () {
				const userId = $(this).data('user-id');
				const userName = '{{ $user->username }}';

				Swal.fire({
					title: 'Suspend User?',
					html: `Are you sure you want to suspend <strong>${userName}</strong>?`,
					@if(file_exists(public_path('assets/icons/Gif/alert3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
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
							success: function (response) {
								showAlert('success', 'User Suspended', 'The user has been suspended');
								setTimeout(() => {
									location.reload();
								}, 1500);
							},
							error: function (xhr) {
								showAlert('error', 'Failed', xhr.responseJSON?.message || 'Failed to suspend user');
							}
						});
					}
				});
			});

			$('.btn-activate').click(function () {
				const userId = $(this).data('user-id');
				const userName = '{{ $user->username }}';

				Swal.fire({
					title: 'Activate User?',
					html: `Are you sure you want to activate <strong>${userName}</strong>?`,
					@if(file_exists(public_path('assets/icons/Gif/question2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
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
							success: function (response) {
								showAlert('success', 'User Activated', 'The user has been activated');
								setTimeout(() => {
									location.reload();
								}, 1500);
							},
							error: function (xhr) {
								showAlert('error', 'Failed', xhr.responseJSON?.message || 'Failed to activate user');
							}
						});
					}
				});
			});

			$('.btn-promote').click(function () {
				const userId = $(this).data('user-id');
				const userName = '{{ $user->username }}';

				Swal.fire({
					title: 'Promote to Lead Farmer?',
					html: `Are you sure you want to promote <strong>${userName}</strong> to Lead Farmer?`,
					@if(file_exists(public_path('assets/icons/Gif/question1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
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
							success: function (response) {
								showAlert('success', 'User Promoted', 'The user has been promoted to Lead Farmer');
								setTimeout(() => {
									location.reload();
								}, 1500);
							},
							error: function (xhr) {
								showAlert('error', 'Failed', xhr.responseJSON?.message || 'Failed to promote user');
							}
						});
					}
				});
			});

			$('.btn-send-notification').click(function () {
				currentUserId = $(this).data('user-id');
				$('#notificationModal').css('display', 'flex').hide().fadeIn();
			});

			$('#sendNotification').click(function () {
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
					success: function (response) {
						$('#notificationModal').fadeOut();
						$('#notificationMessage').val('');
						showAlert('success', 'Notification Sent', 'Notification has been sent to the user');
					},
					error: function (xhr) {
						showAlert('error', 'Failed', 'Failed to send notification');
					}
				});
			});

			$('#closeNotificationModal, #cancelNotification').click(function () {
				$('#notificationModal').fadeOut();
				$('#notificationMessage').val('');
			});

			$(window).click(function (e) {
				if ($(e.target).hasClass('notification-modal-overlay')) {
					$('#notificationModal').fadeOut();
					$('#notificationMessage').val('');
				}
			});
		});
	</script>
@endsection

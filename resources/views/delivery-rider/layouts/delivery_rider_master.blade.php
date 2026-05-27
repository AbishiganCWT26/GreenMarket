<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5, user-scalable=yes">
	<title>Delivery Rider | @yield('title')</title>
	<link rel="stylesheet" href="{{ asset('css/delivery-rider/delivery-rider-master.css') }}">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	@yield('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
	@include('includes.loader')
	<div class="dashboard-wrapper">
		<aside class="sidebar" id="sidebar">
			<div class="sidebar-header">
				<img src="{{ asset('assets/images/Logo Green Market.png') }}" class="logo" alt="Greenmarket">
				<h3>Delivery Rider</h3>
				<button id="sidebar-close" class="sidebar-toggle">
					<i class="fa-solid fa-times"></i>
				</button>
			</div>
			<nav class="sidebar-nav">
				<ul class="main-menu">
					<li>
						<a href="{{ route('delivery-rider.dashboard') }}" class="menu-link {{ request()->routeIs('delivery-rider.dashboard') ? 'active' : '' }}">
							<i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
						</a>
					</li>
					<li>
						<a href="{{ route('delivery-rider.incoming-shipments') }}" class="menu-link {{ request()->routeIs('delivery-rider.incoming-shipments*') ? 'active' : '' }}">
							<i class="fa-solid fa-truck-ramp-box"></i><span>Incoming</span>
						</a>
					</li>
					<li>
						<a href="{{ route('delivery-rider.active-deliveries') }}" class="menu-link {{ request()->routeIs('delivery-rider.active-deliveries*') ? 'active' : '' }}">
							<i class="fa-solid fa-motorcycle"></i><span>Active</span>
						</a>
					</li>
					<li>
						<a href="{{ route('delivery-rider.completed-deliveries') }}" class="menu-link {{ request()->routeIs('delivery-rider.completed-deliveries*') ? 'active' : '' }}">
							<i class="fa-solid fa-circle-check"></i><span>Completed</span>
						</a>
					</li>
					<li>
						<a href="{{ route('delivery-rider.profile') }}" class="menu-link {{ request()->routeIs('delivery-rider.profile*') ? 'active' : '' }}">
							<i class="fa-solid fa-user"></i><span>Profile</span>
						</a>
					</li>
					<li>
						<a href="{{ route('logout.confirmation') }}" class="menu-link logout-link">
							<i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
						</a>
					</li>
				</ul>
			</nav>
		</aside>

		<main class="main-content">
			<header class="navbar">
				<div class="left-header">
					<button id="mobile-menu-btn" class="mobile-menu-btn">
						<i class="fa-solid fa-bars"></i>
					</button>
					<h1 class="page-title">
						<i class="fa-solid fa-motorcycle"></i>
						@yield('page-title', 'Dashboard')
					</h1>
				</div>
				<div class="header-right-group">
					<div class="notif-wrapper">
						<div class="notif-btn" id="notifBtn">
							<i class="fa-regular fa-bell"></i>
							@if(isset($sharedCounts['totalNotifications']) && $sharedCounts['totalNotifications'] > 0)
								<span class="notif-dot"></span>
							@endif
						</div>
						<div class="notif-dropdown" id="notifDropdown">
							<div class="notif-header">
								<span>Notifications</span>
								<button class="mark-all-read" id="markAllRead">Mark all read</button>
							</div>
							<div class="notif-list">
								@if(isset($recentActivities) && count($recentActivities) > 0)
									@foreach($recentActivities as $notification)
										<div class="notif-item {{ $notification->is_read ? 'read' : '' }}" data-id="{{ $notification->id }}">
											<div class="notif-icon">
												@if($notification->notification_type == 'admin_alert')
													<i class="fa-solid fa-triangle-exclamation"></i>
												@else
													<i class="fa-solid fa-info-circle"></i>
												@endIf
											</div>
											<div class="notif-content">
												<div class="notif-title">{{ $notification->title }}</div>
												<div class="notif-msg">{{ Str::limit($notification->message, 60) }}</div>
												<div class="notif-meta">
													<small>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
													@if(!$notification->is_read)
														<button class="mark-single-read" data-id="{{ $notification->id }}">Mark read</button>
													@else
														<span class="read-badge">Read</span>
													@endif
												</div>
											</div>
										</div>
									@endforeach
								@else
									<div class="notif-empty">No notifications</div>
								@endif
							</div>
							<div class="notif-footer">
								<a href="{{ route('delivery-rider.notifications') }}">View all</a>
							</div>
						</div>
					</div>
					<div class="header-user-meta">
						<span class="role">Rider</span>
						<span class="username">{{ Auth::user()->deliveryRider->name ?? Auth::user()->username ?? 'Rider' }}</span>
					</div>
					<a href="{{ route('delivery-rider.profile') }}" class="profile-photo-link">
						<img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'default-avatar.png')) }}" class="profile-photo" onerror="this.src='{{ asset('assets/icons/rider-icon.svg') }}'">
					</a>
					<a href="{{ route('logout.confirmation') }}" class="logout-icon" title="Logout">
						<i class="fa-solid fa-arrow-right-from-bracket"></i>
					</a>
				</div>
			</header>
			<section class="dashboard-body">
				@yield('content')
			</section>
		</main>
	</div>
	<div class="overlay" id="overlay"></div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
	@yield('scripts')

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const mobileMenuBtn = document.getElementById('mobile-menu-btn');
			const sidebarClose = document.getElementById('sidebar-close');
			const sidebar = document.getElementById('sidebar');
			const overlay = document.getElementById('overlay');
			const notifBtn = document.getElementById('notifBtn');
			const notifDropdown = document.getElementById('notifDropdown');

			if (mobileMenuBtn) {
				mobileMenuBtn.addEventListener('click', function() {
					sidebar.classList.add('open');
					overlay.classList.add('active');
				});
			}
			if (sidebarClose) {
				sidebarClose.addEventListener('click', function() {
					sidebar.classList.remove('open');
					overlay.classList.remove('active');
				});
			}
			if (overlay) {
				overlay.addEventListener('click', function() {
					sidebar.classList.remove('open');
					overlay.classList.remove('active');
				});
			}
			if (notifBtn && notifDropdown) {
				notifBtn.addEventListener('click', function(e) {
					e.stopPropagation();
					notifDropdown.classList.toggle('show');
				});
				document.addEventListener('click', function(e) {
					if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
						notifDropdown.classList.remove('show');
					}
				});
			}

			const markAllReadBtn = document.getElementById('markAllRead');
			if (markAllReadBtn) {
				markAllReadBtn.addEventListener('click', function() {
					fetch('{{ route("delivery-rider.notifications.mark-all-read") }}', {
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
							'Content-Type': 'application/json'
						}
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							document.querySelectorAll('.notif-dot').forEach(d => d.remove());
							document.querySelectorAll('.notif-item').forEach(i => i.classList.add('read'));
							document.querySelectorAll('.mark-single-read').forEach(btn => {
								const span = document.createElement('span');
								span.className = 'read-badge';
								span.textContent = 'Read';
								btn.replaceWith(span);
							});
							notifDropdown.classList.remove('show');
							Swal.fire({
								imageUrl: '{{ asset("assets/icons/Gif/success5.gif") }}',
								imageWidth: 60,
								imageHeight: 60,
								title: 'Success',
								text: 'All notifications marked as read',
								timer: 1500,
								showConfirmButton: false,
								toast: true,
								position: 'top-end',
								customClass: { popup: 'swal-toast-popup' }
							});
						}
					})
					.catch(() => {
						Swal.fire({
							imageUrl: '{{ asset("assets/icons/Gif/error6.gif") }}',
							imageWidth: 60,
							imageHeight: 60,
							title: 'Error',
							text: 'Failed to mark notifications',
							confirmButtonColor: '#10B981'
						});
					});
				});
			}

			$(document).on('click', '.mark-single-read', function(e) {
				e.stopPropagation();
				const btn = $(this);
				const notifId = btn.data('id');
				const notifItem = btn.closest('.notif-item');
				let url = "{{ route('delivery-rider.notifications.mark-read', ':id') }}";
				url = url.replace(':id', notifId);

				fetch(url, {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
						'Content-Type': 'application/json'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						notifItem.addClass('read');
						btn.replaceWith('<span class="read-badge">Read</span>');
						if (document.querySelectorAll('.notif-item:not(.read)').length === 0) {
							document.querySelectorAll('.notif-dot').forEach(d => d.remove());
						}
						Swal.fire({
							imageUrl: '{{ asset("assets/icons/Gif/success5.gif") }}',
							imageWidth: 60,
							imageHeight: 60,
							title: 'Marked read',
							timer: 1000,
							showConfirmButton: false,
							toast: true,
							position: 'top-end',
							customClass: { popup: 'swal-toast-popup' }
						});
					}
				})
				.catch(() => {
					Swal.fire({
						imageUrl: '{{ asset("assets/icons/Gif/error6.gif") }}',
						imageWidth: 60,
						imageHeight: 60,
						title: 'Error',
						text: 'Failed to mark as read',
						confirmButtonColor: '#10B981'
					});
				});
			});

			@if(session('success'))
				Swal.fire({
					imageUrl: '{{ asset("assets/icons/Gif/success5.gif") }}',
					imageWidth: 60,
					imageHeight: 60,
					title: 'Success!',
					text: '{{ session("success") }}',
					timer: 3000,
					showConfirmButton: false,
					toast: true,
					position: 'top-end',
					customClass: { popup: 'swal-toast-popup' }
				});
			@endif

			@if(session('error'))
				Swal.fire({
					imageUrl: '{{ asset("assets/icons/Gif/error6.gif") }}',
					imageWidth: 60,
					imageHeight: 60,
					title: 'Error!',
					text: '{{ session("error") }}',
					timer: 3000,
					showConfirmButton: false,
					toast: true,
					position: 'top-end',
					customClass: { popup: 'swal-toast-popup' }
				});
			@endif

			@if(session('warning'))
				Swal.fire({
					imageUrl: '{{ asset("assets/icons/Gif/alert3.gif") }}',
					imageWidth: 60,
					imageHeight: 60,
					title: 'Warning!',
					text: '{{ session("warning") }}',
					timer: 3000,
					showConfirmButton: false,
					toast: true,
					position: 'top-end',
					customClass: { popup: 'swal-toast-popup' }
				});
			@endif

			@if($errors->any())
				Swal.fire({
					imageUrl: '{{ asset("assets/icons/Gif/Validation Error1.gif") }}',
					imageWidth: 60,
					imageHeight: 60,
					title: 'Validation Error',
					html: '{!! implode("<br>", $errors->all()) !!}',
					confirmButtonColor: '#10B981'
				});
			@endif

			setTimeout(() => {
				document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
					if (typeof bootstrap !== 'undefined') {
						const bsAlert = new bootstrap.Alert(alert);
						bsAlert.close();
					}
				});
			}, 5000);
		});
	</script>
</body>
</html>
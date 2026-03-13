<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>👩‍🌾 Farmer Hub | @yield('title')</title>
	<link rel="stylesheet" href="{{ asset('css/farmer-master.css') }}">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	@yield('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<style>
		.goog-te-gadget-simple {
			background-color: #f0f4f9 !important;
			border: 1px solid #e2e8f0 !important;
			border-radius: 30px !important;
			padding: 2px 8px 2px 6px !important;
			display: flex !important;
			align-items: center !important;
			gap: 2px !important;
			font-family: 'Inter', sans-serif !important;
			font-size: 7pt !important;
			transition: all 0.2s ease !important;
			cursor: pointer !important;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
			line-height: 1 !important;
		}

		.translate-card {
			background-color: #ffffff !important;
			box-shadow: 0 3px 8px rgba(15, 23, 36, 0.05), 0 1px 2px rgba(15, 23, 36, 0.03) !important;
			border-radius: 18px !important;
			padding: 2px 4px !important;
			display: inline-flex !important;
			align-items: center !important;
			gap: 2px !important;
			transition: all 0.25s ease !important;
			border: 1px solid rgba(16, 185, 129, 0.1) !important;
			backdrop-filter: blur(1px) !important;
		}

		.goog-te-gadget-simple:hover {
			background-color: #e9eef3 !important;
			border-color: #10B981 !important;
			box-shadow: 0 4px 8px rgba(16, 185, 129, 0.1) !important;
			transform: translateY(-1px);
		}

		.goog-te-gadget-simple:active {
			transform: scale(0.98) translateY(1px) !important;
			box-shadow: 0 1px 3px rgba(16, 185, 129, 0.15) !important;
		}

		.goog-te-menu-value {
			color: #0f1724 !important;
			font-size: 0.65rem !important;
			font-weight: 500 !important;
			letter-spacing: 0.01em !important;
			display: flex !important;
			align-items: center !important;
			gap: 1px !important;
		}

		.goog-te-gadget-icon {
			display: none !important;
		}

		.goog-te-menu-value img {
			display: none !important;
		}

		.goog-te-menu-value span:first-child {
			color: #0f1724 !important;
			font-weight: 500 !important;
			font-size: 0.6rem !important;
		}

		.goog-te-menu-value span:last-child {
			color: #10B981 !important;
			font-size: 0.7rem !important;
			margin-left: 1px !important;
			font-weight: 600 !important;
			opacity: 0.9;
			transition: transform 0.2s;
		}

		.goog-te-gadget-simple:hover .goog-te-menu-value span:last-child {
			transform: translateY(1px);
			color: #059669 !important;
		}

		.VIpgJd-ZVi9od-ORHb,
		.VIpgJd-ZVi9od-ORHb-OEVmcd,
		.goog-te-banner-frame.skiptranslate {
			display: none !important;
		}

		.goog-tooltip,
		.goog-tooltip:hover,
		.goog-text-highlight {
			display: none !important;
			background-color: transparent !important;
			border: none !important;
			box-shadow: none !important;
		}

		iframe.goog-te-banner-frame,
		.goog-te-banner-frame,
		[class*="VIpgJd-ZVi9od-ORHb"] {
			display: none !important;
		}

		body {
			top: 0 !important;
		}

		@media screen and (min-width: 2560px) and (max-width: 5000px) {
			.goog-te-gadget-simple {
				border-radius: 35px !important;
				padding: 3px 10px 3px 8px !important;
				font-size: 10pt !important;
				gap: 3px !important;
			}
			.translate-card {
				border-radius: 22px !important;
				padding: 3px 5px !important;
				gap: 3px !important;
			}
			.goog-te-menu-value {
				font-size: 0.7rem !important;
			}
			.goog-te-menu-value span:first-child {
				font-size: 0.65rem !important;
			}
			.goog-te-menu-value span:last-child {
				font-size: 0.75rem !important;
			}
		}

		@media screen and (min-width: 1501px) and (max-width: 2559px) {
			.goog-te-gadget-simple {
				border-radius: 32px !important;
				padding: 2px 9px 2px 7px !important;
				font-size: 10pt !important;
			}
			.translate-card {
				border-radius: 20px !important;
				padding: 2px 4px !important;
			}
		}

		@media screen and (min-width: 1400px) and (max-width: 1500px) {
			.goog-te-gadget-simple {
				border-radius: 30px !important;
				padding: 2px 8px 2px 6px !important;
				font-size: 10pt !important;
			}
		}

		@media screen and (min-width: 1200px) and (max-width: 1399px) {
			.goog-te-gadget-simple {
				border-radius: 28px !important;
				padding: 2px 7px 2px 5px !important;
				font-size: 10pt !important;
			}
			.translate-card {
				border-radius: 16px !important;
			}
		}

		@media screen and (min-width: 1001px) and (max-width: 1199px) {
			.goog-te-gadget-simple {
				border-radius: 26px !important;
				padding: 2px 6px 2px 5px !important;
				font-size: 10pt !important;
			}
			.goog-te-menu-value {
				font-size: 0.6rem !important;
			}
			.goog-te-menu-value span:first-child {
				font-size: 0.55rem !important;
			}
			.goog-te-menu-value span:last-child {
				font-size: 0.65rem !important;
			}
		}

		@media screen and (max-width: 1000px) {
			.goog-te-gadget-simple {
				border-radius: 25px !important;
				padding: 2px 6px 2px 4px !important;
				font-size: 10pt !important;
			}
			.translate-card {
				border-radius: 15px !important;
			}
			.goog-te-menu-value {
				font-size: 0.55rem !important;
			}
		}

		@media screen and (min-width: 992px) and (max-width: 999px) {
			.goog-te-gadget-simple {
				border-radius: 24px !important;
				padding: 1px 5px 1px 4px !important;
				gap: 1px !important;
			}
		}

		@media screen and (min-width: 768px) and (max-width: 991px) {
			.goog-te-gadget-simple {
				border-radius: 22px !important;
				padding: 1px 5px 1px 3px !important;
				font-size: 10pt !important;
			}
			.translate-card {
				border-radius: 14px !important;
			}
			.goog-te-menu-value {
				font-size: 0.5rem !important;
			}
			.goog-te-menu-value span:first-child {
				font-size: 0.45rem !important;
			}
			.goog-te-menu-value span:last-child {
				font-size: 0.55rem !important;
			}
		}

		@media screen and (min-width: 576px) and (max-width: 767px) {
			.goog-te-gadget-simple {
				border-radius: 20px !important;
				padding: 1px 4px 1px 3px !important;
				font-size: 10pt !important;
				gap: 1px !important;
			}
			.translate-card {
				border-radius: 12px !important;
				padding: 1px 3px !important;
			}
			.goog-te-menu-value {
				font-size: 0.45rem !important;
			}
		}

		@media screen and (min-width: 481px) and (max-width: 575px) {
			.goog-te-gadget-simple {
				border-radius: 18px !important;
				padding: 1px 3px 1px 2px !important;
				font-size: 10pt !important;
				gap: 0.5px !important;
			}
			.translate-card {
				border-radius: 10px !important;
				padding: 1px 2px !important;
			}
			.goog-te-menu-value span:first-child {
				font-size: 0.4rem !important;
			}
			.goog-te-menu-value span:last-child {
				font-size: 0.45rem !important;
			}
		}

		@media screen and (min-width: 330px) and (max-width: 480px) {
			.goog-te-gadget-simple {
				border-radius: 16px !important;
				padding: 0.5px 2px 0.5px 1px !important;
				font-size: 10pt !important;
				gap: 0.5px !important;
			}
			.translate-card {
				border-radius: 8px !important;
				padding: 0.5px 1px !important;
			}
			.goog-te-menu-value {
				font-size: 0.4rem !important;
			}
			.goog-te-menu-value span:first-child {
				font-size: 0.35rem !important;
			}
			.goog-te-menu-value span:last-child {
				font-size: 0.4rem !important;
			}
		}

		@media screen and (max-width: 329px) {
			.goog-te-gadget-simple {
				border-radius: 14px !important;
				padding: 0.5px 2px 0.5px 1px !important;
				font-size: 6pt !important;
				gap: 0.5px !important;
			}
			.translate-card {
				border-radius: 6px !important;
				padding: 0.5px 1px !important;
			}
			.goog-te-menu-value {
				font-size: 0.5rem !important;
			}
			.goog-te-menu-value span:first-child {
				font-size: 0.5rem !important;
			}
			.goog-te-menu-value span:last-child {
				font-size: 0.5rem !important;
			}
		}
    </style>
</head>
<body>
@include('includes.loader')
<div class="dashboard-wrapper">
	<aside class="sidebar" id="sidebar">
		<div class="sidebar-header">
			<img src="{{ asset('assets/images/Logo Green Market.png') }}" class="logo" alt="Greenmarket">
			<h3>Farmer Panel</h3>
			<button id="sidebar-close" class="sidebar-toggle">
				<i class="fa-solid fa-times"></i>
			</button>
		</div>
		
		<nav class="sidebar-nav">
			<ul class="main-menu">
				<li>
					<a href="{{ route('farmer.dashboard') }}" class="menu-link {{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}">
						<i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
					</a>
				</li>
				<li class="menu-heading">PRODUCTS</li>
				<li>
					<a href="{{ route('farmer.products.my-products') }}" class="menu-link {{ request()->routeIs('farmer.products.my-products') ? 'active' : '' }}">
						<i class="fa-solid fa-seedling"></i><span>My Products</span>
						@if(isset($sharedCounts['productCount']) && $sharedCounts['productCount'] > 0)
							<span class="badge bg-success">{{ $sharedCounts['productCount'] }}</span>
						@endif
					</a>
				</li>
				<li>
					<a href="{{ route('farmer.productRequests') }}" class="menu-link {{ request()->routeIs('farmer.productRequests') ? 'active' : '' }}">
						<i class="fa-solid fa-handshake"></i><span>Buyer Requests</span>
					</a>
				<li>
					<a href="{{ route('farmer.inventory') }}" class="menu-link {{ request()->routeIs('farmer.inventory') ? 'active' : '' }}">
						<i class="fa-solid fa-warehouse"></i><span>My Inventory</span>
					</a>
				</li>
				<li class="menu-heading">ORDERS</li>
				<li>
					<a href="{{ route('farmer.orders.active') }}" class="menu-link {{ request()->routeIs('farmer.orders.active') ? 'active' : '' }}">
						<i class="fa-solid fa-clipboard-list"></i><span>Active Orders</span>
						@if(isset($sharedCounts['pendingOrders']) && $sharedCounts['pendingOrders'] > 0)
							<span class="badge bg-warning">{{ $sharedCounts['pendingOrders'] }}</span>
						@endif
					</a>
				</li>
				<li>
					<a href="{{ route('farmer.orders.history') }}" class="menu-link {{ request()->routeIs('farmer.orders.history') ? 'active' : '' }}">
						<i class="fa-solid fa-history"></i><span>Order History</span>
					</a>
				</li>
				<li class="menu-heading">COMPLAINTS</li>
				<li>
					<a href="{{ route('farmer.complaints.create') }}" class="menu-link {{ request()->routeIs('farmer.complaints.create') ? 'active' : '' }}">
						<i class="fa-solid fa-flag"></i><span>File Complaint</span>
					</a>
				</li>
				<li>
					<a href="{{ route('farmer.complaints.list') }}" class="menu-link {{ request()->routeIs('farmer.complaints.list') ? 'active' : '' }}">
						<i class="fa-solid fa-list-check"></i><span>My Complaints</span>
						@if(isset($sharedCounts['openComplaints']) && $sharedCounts['openComplaints'] > 0)
							<span class="badge bg-danger">{{ $sharedCounts['openComplaints'] }}</span>
						@endif
					</a>
				</li>
				<li class="menu-heading">ACCOUNT</li>
				<li>
					<a href="{{ route('farmer.profile.profile') }}" class="menu-link {{ request()->routeIs('farmer.profile.profile') ? 'active' : '' }}">
						<i class="fa-solid fa-user"></i><span>My Profile</span>
					</a>
				</li>
				<li>
					<a href="{{ route('farmer.profile.photo') }}" class="menu-link {{ request()->routeIs('farmer.profile.photo') ? 'active' : '' }}">
						<i class="fa-solid fa-camera"></i><span>Profile Photo</span>
					</a>
				</li>
				<li>
					<a href="{{ route('farmer.profile.settings') }}" class="menu-link {{ request()->routeIs('farmer.profile.settings') ? 'active' : '' }}">
						<i class="fa-solid fa-gear"></i><span>Account Settings</span>
					</a>
				</li>
				<li>
					<a href="{{ route('farmer.profile.payment') }}" class="menu-link {{ request()->routeIs('farmer.profile.payment') ? 'active' : '' }}">
						<i class="fas fa-money-check-alt"></i><span>Payment Preferences</span>
					</a>
				</li>
				<li>
					<a href="{{ route('farmer.notifications') }}" class="menu-link {{ request()->routeIs('farmer.notifications') ? 'active' : '' }}">
						<i class="fa-solid fa-bell"></i><span>Notifications</span>
						@if(isset($unreadNotifications) && $unreadNotifications > 0)
							<span class="notif-dot"></span>
						@endif
					</a>
				</li>
			</ul>
		</nav>
		<div class="sidebar-footer">
			@if(Auth::user()->farmer && Auth::user()->farmer->lead_farmer_id)
			<div class="lead-farmer-info">
				<small>Linked to Lead Farmer:</small>
				<strong>{{ Auth::user()->farmer->leadFarmer->name ?? 'Not Assigned' }}</strong>
			</div>
			@endif
			<a href="{{ route('logout.confirmation') }}" id="nav-logout-link" class="logout-link">
				<i class="fa-solid fa-arrow-right-from-bracket"></i>
				<span>Logout</span>
			</a>
		</div>
	</aside>
	<main class="main-content">
		<header class="navbar">
			<div class="left-header">
				<button id="mobile-menu-btn" class="mobile-menu-btn">
					<i class="fa-solid fa-bars"></i>
				</button>
				<h1 class="page-title">
					<i class="fa-solid fa-tractor"></i>
					@yield('page-title', 'Farmer Dashboard')
				</h1>
			</div>
			<div class="header-right-group">
				<div class="translate-wrapper" style="display:flex;justify-content:center;margin:10px 0;">
                    <div class="translate-card" style="background-color:#ffffff;box-shadow:0 7px 15px rgba(15,23,36,0.08),0 1px 3px rgba(15,23,36,0.04);border-radius:28px;padding:4px 10px;display:inline-flex;align-items:center;gap:8px;transition:all 0.25s ease;border:1px solid rgba(16,185,129,0.15);backdrop-filter:blur(2px);">
                        <div class="translate-icon" style="background:linear-gradient(145deg,#10B981,#059669);width:28px;height:28px;border-radius:14px;display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;box-shadow:0 6px 12px rgba(5,150,105,0.25);transition:0.2s ease;">
                            <i class="fas fa-language"></i>
                        </div>
                        <div id="google_translate_element"></div>
                    </div>
                </div>
				<div class="notif-wrapper">
					<div class="notif-btn" id="notifBtn">
						<i class="fa-regular fa-bell"></i>
						@if(isset($unreadNotifications) && $unreadNotifications > 0)
							<span class="notif-dot"></span>
						@endif
					</div>
					<div class="notif-dropdown" id="notifDropdown">
						<div class="notif-header">
							<span>Notifications</span>
							<button class="mark-all-read" id="markAllRead">Mark all read</button>
						</div>
						<div class="notif-list">
							@if(!isset($notifications) || count($notifications) == 0)
								<div class="notif-empty">No notifications</div>
							@else
								@foreach($notifications as $n)
								<div class="notif-item {{ $n->is_read ? 'read' : 'unread' }}" data-id="{{ $n->id }}">
									<div class="notif-icon">
										@if($n->notification_type == 'order_payment')
											<i class="fa-solid fa-money-bill-wave text-success"></i>
										@elseif($n->notification_type == 'admin_alert')
											<i class="fa-solid fa-triangle-exclamation text-warning"></i>
										@else
											<i class="fa-solid fa-info-circle text-info"></i>
										@endif
									</div>
									<div class="notif-content">
										<div class="notif-title">{{ $n->title }}</div>
										<div class="notif-msg">{{ Str::limit($n->message, 80) }}</div>
										<small class="notif-time">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</small>
									</div>
								</div>
								@endforeach
							@endif
						</div>
						<div class="notif-footer">
							<a href="{{ route('farmer.notifications') }}">View all notifications</a>
						</div>
					</div>
				</div>
				<div class="header-user-meta">
					<span class="role">Farmer</span>
					<span class="username">
						{{ Auth::user()->farmer->name ?? Auth::user()->username ?? 'Farmer' }}
					</span>
				</div>
				<a href="{{ route('farmer.profile.photo') }}" class="profile-photo-link">
					<img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'default-avatar.png')) }}"
						 class="profile-photo"
						 onerror="this.src='{{ asset('assets/icons/farmer-icon.svg') }}'">
				</a>
				<a href="{{ route('logout.confirmation') }}" class="logout-icon" id="header-logout-link" title="Logout">
					<i class="fa-solid fa-arrow-right-from-bracket"></i>
				</a>
			</div>
		</header>
		<section class="dashboard-body">
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					<i class="fa-solid fa-circle-check me-2"></i>
					{{ session('success') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			@endif
			@if(session('error'))
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<i class="fa-solid fa-circle-exclamation me-2"></i>
					{{ session('error') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			@endif
			@if(session('warning'))
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					<i class="fa-solid fa-triangle-exclamation me-2"></i>
					{{ session('warning') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			@endif
			@yield('content')
		</section>
	</main>
</div>
<div class="overlay" id="overlay"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@yield('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
	const mobileMenuBtn = document.getElementById('mobile-menu-btn');
	const sidebarClose = document.getElementById('sidebar-close');
	const sidebar = document.getElementById('sidebar');
	const overlay = document.getElementById('overlay');
	const notifBtn = document.getElementById('notifBtn');
	const notifDropdown = document.getElementById('notifDropdown');
	const pendingPickupAlert = document.getElementById('pendingPickupAlert');
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
			e.preventDefault();
			notifDropdown.classList.toggle('show');
		});
		document.addEventListener('click', function(e) {
			if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
				notifDropdown.classList.remove('show');
			}
		});
	}
	const logoutButtons = document.querySelectorAll('#nav-logout-link, #header-logout-link');
	logoutButtons.forEach(button => {
		button.addEventListener('click', function(e) {
			e.preventDefault();
			Swal.fire({
				title: 'Ready to leave?',
				text: 'You are about to log out of your account',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#10B981',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Yes, logout',
				cancelButtonText: 'Stay',
				background: '#ffffff',
				backdrop: 'rgba(15, 23, 36, 0.3)'
			}).then((result) => {
				if (result.isConfirmed) {
					const form = document.createElement('form');
					form.method = 'POST';
					form.action = '{{ route("logout") }}';
					form.innerHTML = '@csrf';
					document.body.appendChild(form);
					form.submit();
				}
			});
		});
	});
	const markAllReadBtn = document.getElementById('markAllRead');
	if (markAllReadBtn) {
		markAllReadBtn.addEventListener('click', function() {
			fetch('{{ route("farmer.notifications.mark-all-read") }}', {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}',
					'Content-Type': 'application/json'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					document.querySelectorAll('.notif-item.unread').forEach(item => {
						item.classList.remove('unread');
						item.classList.add('read');
					});
					const notifDot = document.querySelector('.notif-dot');
					if (notifDot) notifDot.remove();
					Swal.fire({
						icon: 'success',
						title: 'Success',
						text: 'All notifications marked as read',
						timer: 1500,
						showConfirmButton: false,
						background: '#ffffff'
					});
				}
			})
			.catch(error => {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Failed to mark notifications as read',
					confirmButtonColor: '#10B981',
					background: '#ffffff'
				});
			});
		});
	}
	if (pendingPickupAlert) {
		pendingPickupAlert.addEventListener('click', function() {
			window.location.href = '{{ route("farmer.orders.active") }}';
		});
	}
	@if(session('success'))
		Swal.fire({
			icon: 'success',
			title: 'Success!',
			text: '{{ session("success") }}',
			timer: 3000,
			showConfirmButton: false,
			background: '#ffffff',
			backdrop: 'rgba(16, 185, 129, 0.1)',
			toast: true,
			position: 'top-end'
		});
	@endif
	@if(session('error'))
		Swal.fire({
			icon: 'error',
			title: 'Error!',
			text: '{{ session("error") }}',
			timer: 3000,
			showConfirmButton: false,
			background: '#ffffff',
			backdrop: 'rgba(239, 68, 68, 0.1)',
			toast: true,
			position: 'top-end'
		});
	@endif
	@if(session('warning'))
		Swal.fire({
			icon: 'warning',
			title: 'Warning!',
			text: '{{ session("warning") }}',
			timer: 3000,
			showConfirmButton: false,
			background: '#ffffff',
			backdrop: 'rgba(245, 158, 11, 0.1)',
			toast: true,
			position: 'top-end'
		});
	@endif
	@if($errors->any())
		Swal.fire({
			icon: 'error',
			title: 'Validation Error',
			html: '{!! implode("<br>", $errors->all()) !!}',
			timer: 4000,
			background: '#ffffff'
		});
	@endif
	setTimeout(() => {
		document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
			const bsAlert = new bootstrap.Alert(alert);
			bsAlert.close();
		});
	}, 5000);
	const profilePhotoInput = document.getElementById('profile_photo');
	if (profilePhotoInput) {
		profilePhotoInput.addEventListener('change', function(e) {
			const file = e.target.files[0];
			if (file) {
				const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
				if (!validTypes.includes(file.type)) {
					Swal.fire({
						icon: 'error',
						title: 'Invalid File Type',
						text: 'Please upload only JPEG, PNG, JPG or GIF images.',
						confirmButtonColor: '#10B981',
						background: '#ffffff'
					});
					this.value = '';
					return;
				}
				if (file.size > 2 * 1024 * 1024) {
					Swal.fire({
						icon: 'error',
						title: 'File Too Large',
						text: 'Image size should be less than 2MB.',
						confirmButtonColor: '#10B981',
						background: '#ffffff'
					});
					this.value = '';
					return;
				}
			}
		});
	}
	const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
	tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl);
	});
});
</script>
<script>
window.welcomeShown = false;
window.showWelcomeMessage = function() {
	if (!window.welcomeShown) {
		Swal.fire({
			icon: 'info',
			title: 'translate ready',
			text: 'choose language from the compact menu',
			timer: 3000,
			showConfirmButton: false,
			toast: true,
			position: 'bottom-end',
			background: '#ffffff',
			iconColor: '#3b82f6',
			customClass: { popup: 'swal-popup-compact' }
		});
		window.welcomeShown = true;
	}
};
window.googleTranslateElementInit = function() {
	new google.translate.TranslateElement({
		pageLanguage: 'en',
		includedLanguages: 'en,si,ta',
		layout: google.translate.TranslateElement.InlineLayout.SIMPLE
	}, 'google_translate_element');
	setTimeout(window.showWelcomeMessage, 600);
};
(function() {
	const script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
	script.async = true;
	document.head.appendChild(script);
	setTimeout(function() {
		const gadget = document.querySelector('.goog-te-gadget-simple');
		if (!gadget) return;
		let previousLangText = gadget.innerText || 'en';
		const observer = new MutationObserver(function(mutations) {
			mutations.forEach(function(mut) {
				if (mut.type === 'characterData' || mut.type === 'childList') {
					const currentText = gadget.innerText || '';
					if (currentText !== previousLangText && currentText.trim() !== '') {
						Swal.fire({
							icon: 'success',
							title: 'language updated',
							text: 'page content will now appear in selected language.',
							timer: 3500,
							showConfirmButton: false,
							background: '#ffffff',
							iconColor: '#10B981',
							toast: true,
							position: 'bottom-end',
							showClass: { popup: 'animate__animated animate__fadeInUp' },
							hideClass: { popup: 'animate__animated animate__fadeOutDown' },
							customClass: { popup: 'swal-popup-compact' }
						});
						previousLangText = currentText;
					}
				}
			});
		});
		if (gadget) {
			observer.observe(gadget, { childList: true, subtree: true, characterData: true });
		}
	}, 800);
	const observerRetry = new MutationObserver(function(mutations, obs) {
		if (document.querySelector('.goog-te-gadget-simple')) {
			obs.disconnect();
			setTimeout(function() {
				const gadget = document.querySelector('.goog-te-gadget-simple');
				if (!gadget) return;
				let previousLangText = gadget.innerText || 'en';
				const observer = new MutationObserver(function(mutations) {
					mutations.forEach(function(mut) {
						if (mut.type === 'characterData' || mut.type === 'childList') {
							const currentText = gadget.innerText || '';
							if (currentText !== previousLangText && currentText.trim() !== '') {
								Swal.fire({
									icon: 'success',
									title: 'language updated',
									text: 'page content will now appear in selected language.',
									timer: 3500,
									showConfirmButton: false,
									background: '#ffffff',
									iconColor: '#10B981',
									toast: true,
									position: 'bottom-end',
									showClass: { popup: 'animate__animated animate__fadeInUp' },
									hideClass: { popup: 'animate__animated animate__fadeOutDown' },
									customClass: { popup: 'swal-popup-compact' }
								});
								previousLangText = currentText;
							}
						}
					});
				});
				observer.observe(gadget, { childList: true, subtree: true, characterData: true });
			}, 150);
		}
	});
	observerRetry.observe(document.body, { childList: true, subtree: true });
	window.addEventListener('error', function(e) {
		if (e.target && (e.target.src || '').includes('translate.google')) {
			e.preventDefault();
			Swal.fire({
				icon: 'error',
				title: 'translation error',
				text: 'google translate failed to load. please refresh.',
				confirmButtonColor: '#059669',
				background: '#ffffff',
				iconColor: '#f59e0b'
			});
		}
	}, true);
})();
(function() {
	if (document.body) {
		document.body.style.marginTop = '0px';
		document.body.style.position = 'static';
	}
	const bodyObserver = new MutationObserver(function() {
		if (document.body.style.marginTop !== '0px') {
			document.body.style.marginTop = '0px';
		}
		if (document.body.style.position !== 'static') {
			document.body.style.position = 'static';
		}
	});
	bodyObserver.observe(document.body, { attributes: true, attributeFilter: ['style'] });
})();
</script>
</body>
</html>
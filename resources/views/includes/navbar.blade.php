<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GreenMarket</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<style>
		:root {
			--primary-green: #10B981;
			--dark-green: #059669;
			--body-bg: #f6f8fa;
			--card-bg: #ffffff;
			--text-color: #0f1724;
			--muted: #6b7280;
			--accent-amber: #f59e0b;
			--blue: #3b82f6;
			--purple: #8b5cf6;
			--yellow: #f59e0b;
			--shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
			--shadow-md: 0 7px 15px rgba(15,23,36,0.08);
			--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		}

		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Inter', sans-serif;
			background-color: var(--body-bg);
			color: var(--text-color);
			line-height: 1.6;
		}

		.site-header {
			background: var(--card-bg);
			padding: 2px 10px;
			position: sticky;
			top: 0;
			z-index: 1000;
			box-shadow: var(--shadow-sm);
			border-bottom: 1px solid rgba(16, 185, 129, 0.1);
		}

		.header-container {
			max-width: 1200px;
			margin: 0 auto;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.logo-section {
			display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
		}

		.logo-link {
			display: flex;
			align-items: center;
			gap: 8px;
			text-decoration: none;
			transition: var(--transition);
		}

		.logo-link:hover {
			transform: translateY(-2px);
		}

		.logo-img {
			height: 60px;
			width: auto;
			object-fit: contain;
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
		}

		.logo-text {
			font-family: 'Poppins', sans-serif;
			font-weight: 700;
			font-size: 1.3rem;
			background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
			-webkit-background-clip: text;
			background-clip: text;
			color: transparent;
		}

		.desktop-nav {
			display: flex;
			align-items: center;
			gap: 5px;
			flex-wrap: wrap;
			justify-content: flex-end;
		}

		.nav-link {
			text-decoration: none;
			color: var(--text-color);
			font-weight: 500;
			padding: 8px 14px;
			border-radius: 8px;
			transition: var(--transition);
			display: flex;
			align-items: center;
			gap: 6px;
			font-size: 0.95rem;
			position: relative;
			overflow: hidden;
		}

		.nav-link::after {
			content: '';
			position: absolute;
			bottom: 0;
			left: 50%;
			width: 0;
			height: 2px;
			background: var(--primary-green);
			transition: all 0.3s ease;
			transform: translateX(-50%);
		}

		.nav-link:hover::after {
			width: 80%;
		}

		.nav-link:hover {
			color: var(--primary-green);
			background: rgba(16, 185, 129, 0.05);
			transform: translateY(-1px);
		}

		.nav-link.active {
			color: var(--primary-green);
			background: rgba(16, 185, 129, 0.08);
		}

		.auth-buttons {
			display: flex;
			align-items: center;
			gap: 8px;
			margin-left: 5px;
		}

		.btn-auth {
			padding: 8px 20px;
			border-radius: 8px;
			font-weight: 600;
			cursor: pointer;
			transition: var(--transition);
			display: flex;
			align-items: center;
			gap: 6px;
			border: none;
			font-size: 0.95rem;
			white-space: nowrap;
		}

		.btn-login {
			background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
			color: white;
			box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
		}

		.btn-login:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
		}

		.btn-register {
			background: linear-gradient(135deg, var(--blue), var(--purple));
			color: white;
			box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
		}

		.btn-register:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
		}

		.btn-logout {
			background: linear-gradient(135deg, #ef4444, #dc2626);
			color: white;
		}

		.btn-logout:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
		}

		.user-section {
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.user-greeting {
			padding: 6px 12px;
			background: rgba(16, 185, 129, 0.08);
			border-radius: 6px;
			font-weight: 500;
			display: flex;
			align-items: center;
			gap: 6px;
			font-size: 0.9rem;
		}

		.dashboard-link {
			background: linear-gradient(135deg, var(--accent-amber), var(--yellow));
			color: white;
			text-decoration: none;
			padding: 8px 16px;
			border-radius: 8px;
			font-weight: 600;
			display: flex;
			align-items: center;
			gap: 6px;
			transition: var(--transition);
			font-size: 0.95rem;
		}

		.dashboard-link:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
		}

		.mobile-menu-btn {
			display: none;
			background: none;
			border: none;
			font-size: 1.3rem;
			color: var(--text-color);
			cursor: pointer;
			padding: 6px;
			border-radius: 6px;
			transition: var(--transition);
		}

		.mobile-menu-btn:hover {
			background: rgba(16, 185, 129, 0.1);
		}

		.mobile-nav {
			position: fixed;
			top: 60px;
			left: 0;
			width: 100%;
			background: var(--card-bg);
			padding: 15px;
			box-shadow: var(--shadow-md);
			display: none;
			flex-direction: column;
			gap: 10px;
			z-index: 999;
			border-top: 1px solid rgba(16, 185, 129, 0.1);
		}

		.mobile-nav.active {
			display: flex;
			animation: slideDown 0.3s ease;
		}

		@keyframes slideDown {
			from {
				opacity: 0;
				transform: translateY(-10px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.mobile-nav .nav-link {
			padding: 12px;
			border-radius: 8px;
			border-left: 3px solid transparent;
		}

		.mobile-nav .nav-link:hover {
			border-left-color: var(--primary-green);
			background: rgba(16, 185, 129, 0.08);
		}

		.mobile-auth-buttons {
			display: flex;
			flex-direction: column;
			gap: 8px;
			margin-top: 10px;
		}

		.mobile-auth-buttons .btn-auth {
			justify-content: center;
			padding: 10px;
		}

		@media (max-width: 1199px) {
			.header-container {
				padding: 0 15px;
			}

			.nav-link {
				padding: 8px 12px;
				font-size: 0.9rem;
			}

			.btn-auth {
				padding: 8px 16px;
				font-size: 0.9rem;
			}
		}

		@media (max-width: 991px) {
			.desktop-nav {
				display: none;
			}

			.mobile-menu-btn {
				display: block;
			}

			.logo-img {
				height: 32px;
			}

			.logo-text {
				font-size: 1.2rem;
			}
		}

		@media (max-width: 767px) {
			.site-header {
				padding: 10px 15px;
			}

			.logo-text {
				font-size: 1.1rem;
			}

			.logo-img {
				height: 30px;
			}

			.mobile-nav {
				top: 56px;
				padding: 12px;
			}
		}

		@media (max-width: 480px) {
			.site-header {
				padding: 8px 12px;
			}

			.logo-section {
				gap: 6px;
			}

			.logo-text {
				font-size: 1rem;
			}

			.logo-img {
				height: 28px;
			}

			.mobile-menu-btn {
				font-size: 1.2rem;
				padding: 5px;
			}

			.mobile-nav {
				top: 52px;
				gap: 8px;
			}

			.mobile-nav .nav-link {
				padding: 10px;
				font-size: 0.9rem;
			}
		}

		@media (min-width: 1000px) {
			.header-container {
				max-width: 1000px;
			}
		}

		@media (min-width: 1200px) {
			.header-container {
				max-width: 1200px;
			}

			.nav-link {
				padding: 10px 16px;
				font-size: 1rem;
			}

			.btn-auth {
				padding: 10px 22px;
				font-size: 1rem;
			}
		}

		.float-animation {
			animation: float 3s ease-in-out infinite;
		}

		@keyframes float {
			0%, 100% {
				transform: translateY(0);
			}
			50% {
				transform: translateY(-3px);
			}
		}

		.pulse-hover:hover {
			animation: pulse 0.5s;
		}

		@keyframes pulse {
			0% {
				transform: scale(1);
			}
			50% {
				transform: scale(1.05);
			}
			100% {
				transform: scale(1);
			}
		}

		.shake-animation:hover {
			animation: shake 0.5s;
		}

		@keyframes shake {
			0%, 100% { transform: translateX(0); }
			25% { transform: translateX(-3px); }
			75% { transform: translateX(3px); }
		}

		.ripple-effect {
			position: relative;
			overflow: hidden;
		}

		.ripple-effect::after {
			content: '';
			position: absolute;
			top: 50%;
			left: 50%;
			width: 5px;
			height: 5px;
			background: rgba(255, 255, 255, 0.5);
			opacity: 0;
			border-radius: 100%;
			transform: scale(1, 1) translate(-50%);
			transform-origin: 50% 50%;
		}

		.ripple-effect:hover::after {
			animation: ripple 1s ease-out;
		}

		@keyframes ripple {
			0% {
				transform: scale(0, 0);
				opacity: 0.5;
			}
			100% {
				transform: scale(20, 20);
				opacity: 0;
			}
		}
	</style>
</head>
<body>
	<header class="site-header">
		<div class="header-container">
			<div class="logo-section">
                <a href="{{ url('/') }}"
                class="logo-link float-animation"
                oncontextmenu="return false;">

                    <img src="{{ asset('assets/images/logo-4.png') }}"
                        alt="GreenMarket Logo"
                        class="logo-img"
                        draggable="false">

                    <span class="logo-text">GreenMarket</span>
                </a>
            </div>

			<button class="mobile-menu-btn" id="mobileMenuBtn">
				<i class="fas fa-bars"></i>
			</button>

			<nav class="desktop-nav">
				<a href="{{ url('/') }}" class="nav-link ripple-effect {{ request()->is('/') ? 'active' : '' }}">
					<i class="fas fa-home"></i> Home
				</a>
				<a href="{{ url('/about-us') }}" class="nav-link ripple-effect {{ request()->is('about-us') || request()->is('about-us/*') ? 'active' : '' }}">
					<i class="fas fa-info-circle"></i> About
				</a>
				<a href="{{ url('/how-it-works') }}" class="nav-link ripple-effect {{ request()->is('how-it-works') ? 'active' : '' }}">
					<i class="fas fa-question-circle"></i> How It Works
				</a>
				<a href="{{ url('/contact-us') }}" class="nav-link ripple-effect {{ request()->is('contact-us') || request()->is('contact-us/*') ? 'active' : '' }}">
					<i class="fas fa-envelope"></i> Contact
				</a>

				<div class="auth-buttons">
					@guest
						<a href="{{ url('/register/buyer') }}" class="btn-auth btn-register pulse-hover">
							<i class="fas fa-user-plus"></i> Register
						</a>
						<a href="{{ url('/login') }}" class="btn-auth btn-login pulse-hover">
							<i class="fas fa-sign-in-alt"></i> Login
						</a>
					@else
						<div class="user-section">
							<div class="user-greeting">
								<i class="fas fa-user-circle"></i>
								<span>{{ Auth::user()->username ?? 'User' }}</span>
							</div>
							<a href="{{ $dashboardUrl ?? '#' }}" class="dashboard-link pulse-hover">
								<i class="fas fa-tachometer-alt"></i>
							</a>
							<a href="{{ url('/logout') }}"
							   onclick="event.preventDefault(); logoutUser();"
							   class="btn-auth btn-logout shake-animation">
								<i class="fas fa-sign-out-alt"></i>
							</a>
							<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
								@csrf
							</form>
						</div>
					@endguest
				</div>
			</nav>
		</div>

		<nav class="mobile-nav" id="mobileNav">
			<a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
				<i class="fas fa-home"></i> Home
			</a>
			<a href="{{ url('/about-us') }}" class="nav-link {{ request()->is('about-us') || request()->is('about-us/*') ? 'active' : '' }}">
				<i class="fas fa-info-circle"></i> About
			</a>
			<a href="{{ url('/how-it-works') }}" class="nav-link {{ request()->is('how-it-works') ? 'active' : '' }}">
				<i class="fas fa-question-circle"></i> How It Works
			</a>
			<a href="{{ url('/contact-us') }}" class="nav-link {{ request()->is('contact-us') || request()->is('contact-us/*') ? 'active' : '' }}">
				<i class="fas fa-envelope"></i> Contact
			</a>

			<div class="mobile-auth-buttons">
				@guest
					<a href="{{ url('/buyer/register') }}" class="btn-auth btn-register">
						<i class="fas fa-user-plus"></i> Register
					</a>
					<a href="{{ url('/login') }}" class="btn-auth btn-login">
						<i class="fas fa-sign-in-alt"></i> Login
					</a>
				@else
					<div class="user-greeting" style="margin: 5px 0; justify-content: center;">
						<i class="fas fa-user-circle"></i>
						<span>Welcome, {{ Auth::user()->username ?? 'User' }}</span>
					</div>
					<a href="{{ $dashboardUrl ?? '#' }}" class="btn-auth" style="background: linear-gradient(135deg, var(--accent-amber), var(--yellow));">
						<i class="fas fa-tachometer-alt"></i> Dashboard
					</a>
					<a href="{{ url('/logout') }}"
					   onclick="event.preventDefault(); logoutUser();"
					   class="btn-auth btn-logout">
						<i class="fas fa-sign-out-alt"></i> Logout
					</a>
				@endguest
			</div>
		</nav>
	</header>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const mobileMenuBtn = document.getElementById('mobileMenuBtn');
			const mobileNav = document.getElementById('mobileNav');
			const navLinks = document.querySelectorAll('.nav-link');

			mobileMenuBtn.addEventListener('click', function() {
				mobileNav.classList.toggle('active');
				const icon = this.querySelector('i');
				icon.classList.toggle('fa-bars');
				icon.classList.toggle('fa-times');
				this.classList.toggle('pulse-hover');
			});

			navLinks.forEach(link => {
				link.addEventListener('click', function() {
					if (window.innerWidth <= 991) {
						mobileNav.classList.remove('active');
						mobileMenuBtn.querySelector('i').classList.remove('fa-times');
						mobileMenuBtn.querySelector('i').classList.add('fa-bars');
					}
				});
			});

			window.addEventListener('resize', function() {
				if (window.innerWidth > 991) {
					mobileNav.classList.remove('active');
					mobileMenuBtn.querySelector('i').classList.remove('fa-times');
					mobileMenuBtn.querySelector('i').classList.add('fa-bars');
				}
			});

			// Set active class based on page-title or URL
			function setActiveNavLink() {
				// Get current URL path
				const currentPath = window.location.pathname;

				// Find and activate the matching link
				navLinks.forEach(link => {
					const href = link.getAttribute('href');
					if (href === currentPath) {
						link.classList.add('active');
					}
				});
			}

			// Set active nav on page load
			setActiveNavLink();

			@if(session('success'))
				Swal.fire({
					icon: 'success',
					title: 'Welcome back!',
					html: `
						<div style="text-align: center; padding: 10px;">
							<h3 style="color: #10B981; margin-bottom: 10px;">Login Successful</h3>
							<p style="color: #6b7280;">Welcome <strong>{{ session('name') }}</strong></p>
							<p style="color: #6b7280; font-size: 0.9rem;">Role: <span style="background: rgba(16, 185, 129, 0.1); padding: 4px 12px; border-radius: 20px; color: #059669;">{{ session('role') }}</span></p>
						</div>
					`,
					timer: 3000,
					showConfirmButton: false,
					background: '#ffffff',
					color: '#0f1724'
				});
			@endif

			@if($errors->any())
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: '{{ $errors->first() }}',
					confirmButtonColor: '#10B981',
					background: '#ffffff',
					color: '#0f1724'
				});
			@endif
		});

		function logoutUser() {
			Swal.fire({
				title: 'Logout?',
				text: 'Are you sure you want to logout?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#10B981',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Yes, Logout',
				cancelButtonText: 'Cancel',
				background: '#ffffff',
				color: '#0f1724'
			}).then((result) => {
				if (result.isConfirmed) {
					document.getElementById('logout-form').submit();
				}
			});
		}

		function showRegisterSuccess() {
			@if(session('register_success'))
				Swal.fire({
					icon: 'success',
					title: 'Registration Successful!',
					text: '{{ session('register_success') }}',
					confirmButtonColor: '#10B981',
					background: '#ffffff',
					color: '#0f1724'
				});
			@endif
		}

		window.onload = function() {
			showRegisterSuccess();
		};
	</script>
</body>
</html>

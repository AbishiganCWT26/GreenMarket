<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GreenMarket</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	
	<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

</head>
<body>
	@include('includes.loader')
	<header class="site-header">
		<div class="header-container" translate="no">
			<div class="logo-wrapper">
				<a href="{{ url('/') }}" class="logo-link" oncontextmenu="return false;">
					<div class="logo-image-wrapper">
						<img src="{{ asset('assets/images/Logo Green Market.png') }}" alt="GreenMarket" class="logo-img" draggable="false">
					</div>
					<span class="logo-text" translate="no">GreenMarket</span>
				</a>
			</div>

			<nav class="nav-menu">
				<a href="{{ url('/') }}" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
					<i class="fas fa-home"></i>
					<span translate="no">Home</span>
				</a>
				<a href="{{ url('/about-us') }}" class="nav-item {{ request()->is('about-us') || request()->is('about-us/*') ? 'active' : '' }}">
					<i class="fas fa-info-circle"></i>
					<span translate="no">About Us</span>
				</a>
				<a href="{{ url('/contact-us') }}" class="nav-item {{ request()->is('contact-us') || request()->is('contact-us/*') ? 'active' : '' }}">
					<i class="fas fa-envelope"></i>
					<span translate="no">Contact Us</span>
				</a>
			</nav>

			<div class="header-actions">
				@guest
					<div class="auth-group" translate="no">
						<a href="{{ url('/register/buyer') }}" class="btn btn-register">
							<i class="fas fa-user-plus"></i>
							<span>Register</span>
						</a>
						<a href="{{ url('/login') }}" class="btn btn-login">
							<i class="fas fa-sign-in-alt"></i>
							<span>Login</span>
						</a>
					</div>
				@else
					@php
						$user = auth()->user();
						$dashboardUrl = '/';
						$ProfileUrl = '/';
						$SettingsUrl = '/';
						switch($user->role) {
							case 'admin':
								$dashboardUrl = '/admin/dashboard';
								$ProfileUrl = '/admin/profile';
								$SettingsUrl = '/admin/profile';
								break;
							case 'facilitator':
								$dashboardUrl = '/facilitator/dashboard';
								$ProfileUrl = '/facilitator/profile';
								$SettingsUrl = '/facilitator/profile';
								break;
							case 'lead_farmer':
								$dashboardUrl = '/lead-farmer/dashboard';
								$ProfileUrl = '/lead-farmer/profile';
								$SettingsUrl = '/lead-farmer/profile';
								break;
							case 'farmer':
								$dashboardUrl = '/farmer/dashboard';
								$ProfileUrl = '/farmer/profile';
								$SettingsUrl = '/farmer/profile/settings';
								break;
							case 'buyer':
								$dashboardUrl = '/buyer/dashboard';
								$ProfileUrl = '/buyer/profile';
								$SettingsUrl = '/buyer/password';
								break;
						}
					@endphp
					<div class="user-dropdown">
						<button class="user-toggle" id="userToggle">
							<div class="user-avatar">
								@php
									$profilePhoto = Auth::user()->profile_photo ?? 'default-avatar.png';
									$photoPath = asset('uploads/profile_pictures/' . $profilePhoto);
								@endphp
								<img src="{{ $photoPath }}" alt="{{ Auth::user()->username ?? 'User' }}">
							</div>
							<span class="user-name">{{ Auth::user()->username ?? 'User' }}</span>
							<i class="fas fa-chevron-down"></i>
						</button>
						<div class="dropdown-menu" id="dropdownMenu" translate="no">
							<a href="{{ url($dashboardUrl) }}" class="dropdown-item">
								<i class="fas fa-tachometer-alt"></i>
								<span>Dashboard</span>
							</a>
							<a href="{{ url($ProfileUrl) }}" class="dropdown-item">
								<i class="fas fa-user-circle"></i>
								<span>Profile</span>
							</a>
							<a href="{{ url($SettingsUrl) }}" class="dropdown-item">
								<i class="fas fa-cog"></i>
								<span>Settings</span>
							</a>
							<div class="dropdown-divider"></div>
							<a href="{{ route('logout.confirmation') }}" class="dropdown-item logout">
								<i class="fas fa-sign-out-alt"></i>
								<span>Logout</span>
							</a>
							<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
								@csrf
							</form>
						</div>
					</div>
				@endguest
				<div class="translate-card">
					<div class="translate-icon">
						@if(file_exists(public_path('assets/icons/translation logo.svg')))
							<img src="{{ asset('assets/icons/translation logo.svg') }}" alt="Translate" class="translation-logo">
						@else
							<i class="fas fa-language"></i>
						@endif
					</div>
					<div id="google_translate_element"></div>
				</div>
				<button class="mobile-menu-btn" id="mobileMenuBtn">
					<i class="fas fa-bars"></i>
				</button>
			</div>
		</div>

		<div class="mobile-nav" id="mobileNav" translate="no">
			<a href="{{ url('/') }}" class="mobile-nav-item {{ request()->is('/') ? 'active' : '' }}">
				<i class="fas fa-home"></i>
				<span>Home</span>
			</a>
			<a href="{{ url('/about-us') }}" class="mobile-nav-item {{ request()->is('about-us') || request()->is('about-us/*') ? 'active' : '' }}">
				<i class="fas fa-info-circle"></i>
				<span>About Us</span>
			</a>
			<a href="{{ url('/contact-us') }}" class="mobile-nav-item {{ request()->is('contact-us') || request()->is('contact-us/*') ? 'active' : '' }}">
				<i class="fas fa-envelope"></i>
				<span>Contact Us</span>
			</a>

			@guest
				<div class="mobile-auth-group" translate="no">
					<a href="{{ url('/register/buyer') }}" class="mobile-btn mobile-btn-register">
						<i class="fas fa-user-plus"></i>
						<span>Register</span>
					</a>
					<a href="{{ url('/login') }}" class="mobile-btn mobile-btn-login">
						<i class="fas fa-sign-in-alt"></i>
						<span>Login</span>
					</a>
				</div>
			@else
				<div class="mobile-user-section" translate="no">
					<div class="mobile-user-info">
						<div class="mobile-user-avatar">
							<img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'default-avatar.png')) }}" alt="{{ Auth::user()->username ?? 'User' }}">
						</div>
						<div class="mobile-user-details">
							<span class="mobile-user-name">{{ Auth::user()->username ?? 'User' }}</span>
							<span class="mobile-user-email">{{ Auth::user()->email ?? '' }}</span>
						</div>
					</div>
					<a href="{{ url($dashboardUrl) }}" class="mobile-nav-item">
						<i class="fas fa-tachometer-alt"></i>
						<span>Dashboard</span>
					</a>
					<a href="{{ url($ProfileUrl) }}" class="mobile-nav-item">
						<i class="fas fa-user-circle"></i>
						<span>Profile</span>
					</a>
					<a href="{{ url($SettingsUrl) }}" class="mobile-nav-item">
						<i class="fas fa-cog"></i>
						<span>Settings</span>
					</a>
					<a href="{{ route('logout.confirmation') }}" class="mobile-nav-item logout">
						<i class="fas fa-sign-out-alt"></i>
						<span>Logout</span>
					</a>
				</div>
			@endguest
		</div>
	</header>

	<script type="text/javascript">
		function googleTranslateElementInit() {
			new google.translate.TranslateElement({
				pageLanguage: 'en',
				includedLanguages: 'en,si,ta',
				layout: google.translate.TranslateElement.InlineLayout.SIMPLE
			}, 'google_translate_element');
		}
		
		window.welcomeShown = false;
		
		function showWelcomeMessage() {
			if (!window.welcomeShown) {
				window.welcomeShown = true;
			}
		}
		
		const originalInit = window.googleTranslateElementInit;
		window.googleTranslateElementInit = function() {
			originalInit();
			setTimeout(showWelcomeMessage, 600);
		};
	</script>
	<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const mobileMenuBtn = document.getElementById('mobileMenuBtn');
			const mobileNav = document.getElementById('mobileNav');
			const userToggle = document.getElementById('userToggle');
			const dropdownMenu = document.getElementById('dropdownMenu');
			const mobileNavItems = document.querySelectorAll('.mobile-nav-item');

			mobileMenuBtn.addEventListener('click', function(e) {
				e.stopPropagation();
				mobileNav.classList.toggle('active');
				const icon = this.querySelector('i');
				icon.classList.toggle('fa-bars');
				icon.classList.toggle('fa-times');
			});

			if (userToggle && dropdownMenu) {
				userToggle.addEventListener('click', function(e) {
					e.stopPropagation();
					dropdownMenu.classList.toggle('active');
					const icon = this.querySelector('.fa-chevron-down');
					if (icon) {
						icon.style.transform = dropdownMenu.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0)';
					}
				});

				document.addEventListener('click', function(e) {
					if (!userToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
						dropdownMenu.classList.remove('active');
						const icon = userToggle.querySelector('.fa-chevron-down');
						if (icon) {
							icon.style.transform = 'rotate(0)';
						}
					}
				});
			}

			mobileNavItems.forEach(item => {
				item.addEventListener('click', function() {
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
					if (mobileMenuBtn.querySelector('i')) {
						mobileMenuBtn.querySelector('i').classList.remove('fa-times');
						mobileMenuBtn.querySelector('i').classList.add('fa-bars');
					}
				}
			});

			setTimeout(function() {
				const gadget = document.querySelector('.goog-te-gadget-simple');
				if (!gadget) return;

				let previousLangText = gadget.innerText || 'en';

				const observer = new MutationObserver(function(mutations) {
					mutations.forEach(function(mut) {
						if (mut.type === 'characterData' || mut.type === 'childList') {
							const currentText = gadget.innerText || '';
							if (currentText !== previousLangText && currentText.trim() !== '') {
								previousLangText = currentText;
							}
						}
					});
				});

				if (gadget) {
					observer.observe(gadget, { childList: true, subtree: true, characterData: true });
				}
			}, 800);

			window.addEventListener('error', function(e) {
				if (e.target && (e.target.src || '').includes('translate.google')) {
					e.preventDefault();
					Swal.fire({
						@if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
						title: 'translation error',
						text: 'google translate failed to load. please refresh.',
						confirmButtonColor: '#059669',
						background: '#ffffff',
						iconColor: '#f59e0b'
					});
				}
			}, true);

			@if(session('success'))
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/welcome1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/welcome1.gif') }}', imageWidth: 100, imageHeight: 60 @else icon: 'success' @endif,
					title: 'Welcome!',
					html: `
						<div style="text-align: center; padding: 10px;">
							<h3 style="color: #10B981; margin-bottom: 8px;">{{ session('title') ?? 'Success' }}</h3>
							<p style="color: #6b7280;">{{ session('success') }}</p>
						</div>
					`,
					timer: 3000,
					showConfirmButton: false,
					background: '#ffffff',
					color: '#0f1724'
				});
			@endif

			@if(session('error'))
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/error5.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error5.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
					title: 'Oops...',
					text: '{{ session('error') }}',
					confirmButtonColor: '#10B981',
					background: '#ffffff',
					color: '#0f1724'
				});
			@endif

			@if($errors->any())
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/error6.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error6.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
					title: 'Validation Error',
					text: '{{ $errors->first() }}',
					confirmButtonColor: '#10B981',
					background: '#ffffff',
					color: '#0f1724'
				});
			@endif
		});

	</script>
</body>
</html>

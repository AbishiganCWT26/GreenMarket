{{-- resources/views/aboutus.blade.php --}}
@extends('public_master')

@section('title', 'About GreenMarket')
@section('page-title', 'about-us')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/aboutus.css') }}">
@endsection

@section('content')
@php
$aboutConfigs = DB::table('system_config')
    ->where('config_group', 'about_us')
    ->where('is_public', true)
    ->pluck('config_value', 'config_key')
    ->toArray();

function getAboutConfig($key, $configs, $default = '') {
    return $configs[$key] ?? $default;
}

$howItWorksConfigs = DB::table('system_config')
    ->where('config_group', 'how_it_works')
    ->where('is_public', true)
    ->pluck('config_value', 'config_key')
    ->toArray();

function getHowItWorksConfig($key, $configs) {
    return $configs[$key] ?? '';
}
@endphp

<div class="about-page">
	<div class="about-container">
		<section class="about-hero">
			<div class="hero-content">
				<h1 class="hero-title">About GreenMarket</h1>
				<p class="hero-tagline">Bridging the gap between local farmers and buyers with technology</p>
				<p class="hero-text">{{ getAboutConfig('about_us_1st_para', $aboutConfigs) }}</p>
				<div class="hero-stats">
					<div class="stat-card">
						<span class="stat-number" data-count="{{ $stats['active_farmers'] ?? 0 }}">{{ $stats['active_farmers'] ?? 0 }}+</span>
						<span class="stat-label">Local Farmers</span>
					</div>
					<div class="stat-card">
						<span class="stat-number" data-count="{{ $stats['total_products'] ?? 0 }}">{{ $stats['total_products'] ?? 0 }}+</span>
						<span class="stat-label">Products Listed</span>
					</div>
					<div class="stat-card">
						<span class="stat-number" data-count="{{ $stats['successful_orders'] ?? 0 }}">{{ $stats['successful_orders'] ?? 0 }}+</span>
						<span class="stat-label">Successful Orders</span>
					</div>
				</div>
			</div>
			<div class="hero-image">
				@php $aboutImage1 = getAboutConfig('about_us_image_1', $aboutConfigs); @endphp
				<img src="{{ asset('assets/images/' . $aboutImage1) }}" alt="Farmers and Buyers Connection">
			</div>
		</section>

		<section class="about-story">
			<div class="story-image">
				@php $aboutImage2 = getAboutConfig('about_us_image_2', $aboutConfigs); @endphp
				<img src="{{ asset('assets/images/' . $aboutImage2) }}" alt="Our Farming Community">
				<div class="story-badge"><i class="fas fa-leaf"></i> Organic & Fresh</div>
			</div>
			<div class="story-content">
				<h2 class="section-title">Our Story</h2>
				<p>{{ getAboutConfig('about_us_Our_Story_para_1', $aboutConfigs) }}</p>
				<p>{{ getAboutConfig('about_us_Our_Story_para_2', $aboutConfigs) }}</p>
				<div class="story-features">
					<div><i class="fas fa-award"></i> Quality Certified</div>
					<div><i class="fas fa-shield-alt"></i> Secure Platform</div>
				</div>
			</div>
		</section>

		<section class="vision-mission">
			<div class="section-header">
				<h2 class="section-title">Our Vision & Mission</h2>
				<p>Driving sustainable agricultural transformation</p>
			</div>
			<div class="vm-grid">
				<div class="vm-card">
					<div class="vm-icon"><img src="{{ asset('assets/icons/vision-icon.png') }}" alt="Vision"></div>
					<h3>Our Vision</h3>
					<p>{{ getAboutConfig('about_us_Vision_para', $aboutConfigs) }}</p>
					<ul>
						<li><i class="fas fa-check-circle"></i> {{ getAboutConfig('about_us_Vision_1st_point', $aboutConfigs) }}</li>
						<li><i class="fas fa-check-circle"></i> {{ getAboutConfig('about_us_Vision_2nd_point', $aboutConfigs) }}</li>
						<li><i class="fas fa-check-circle"></i> {{ getAboutConfig('about_us_Vision_3rd_point', $aboutConfigs) }}</li>
					</ul>
				</div>
				<div class="vm-card">
					<div class="vm-icon"><img src="{{ asset('assets/icons/mission-icon.png') }}" alt="Mission"></div>
					<h3>Our Mission</h3>
					<p>{{ getAboutConfig('about_us_Mission_para', $aboutConfigs) }}</p>
					<ul>
						<li><i class="fas fa-bullseye"></i> {{ getAboutConfig('about_us_Mission_1st_point', $aboutConfigs) }}</li>
						<li><i class="fas fa-bullseye"></i> {{ getAboutConfig('about_us_Mission_2nd_point', $aboutConfigs) }}</li>
						<li><i class="fas fa-bullseye"></i> {{ getAboutConfig('about_us_Mission_3rd_point', $aboutConfigs) }}</li>
					</ul>
				</div>
			</div>
		</section>

		<section class="what-we-offer">
			<div class="section-header">
				<h2 class="section-title">What We Offer</h2>
				<p>Comprehensive solutions for modern agriculture</p>
			</div>
			<div class="offer-grid">
				<div class="offer-card"><i class="fas fa-store"></i><h3>Digital Marketplace</h3><p>A platform for farmers to sell garden produce directly to buyers without intermediaries.</p><span>For Farmers</span></div>
				<div class="offer-card"><i class="fas fa-handshake"></i><h3>Direct Connections</h3><p>Connect farmers directly with buyers for transparent and trust-based relationships.</p><span>For Everyone</span></div>
				<div class="offer-card"><i class="fas fa-shield-alt"></i><h3>Secure Payments</h3><p>Safe and reliable payment gateway ensuring secure transactions for all parties.</p><span>Secure</span></div>
				<div class="offer-card"><i class="fas fa-tools"></i><h3>Management Tools</h3><p>Easy-to-use product management tools for inventory and sales tracking.</p><span>For Sellers</span></div>
				<div class="offer-card"><i class="fas fa-mobile-alt"></i><h3>Mobile Access</h3><p>Access the platform from any device with responsive design and mobile optimization.</p><span>Accessible</span></div>
				<div class="offer-card"><i class="fas fa-chart-line"></i><h3>Analytics & Reports</h3><p>Detailed sales analytics and performance reports for better decision making.</p><span>Insights</span></div>
			</div>
		</section>

		<section class="our-values">
			<div class="section-header">
				<h2 class="section-title">Our Values</h2>
				<p>The principles that guide everything we do</p>
			</div>
			<div class="values-grid">
				<div class="value-card"><i class="fas fa-tractor"></i><h3>Support Local Agriculture</h3><p>We prioritize and empower local farmers, preserving traditional farming practices while integrating modern technology.</p></div>
				<div class="value-card"><i class="fas fa-leaf"></i><h3>Promote Sustainability</h3><p>Encouraging eco-friendly farming practices and reducing food waste through direct farm-to-table connections.</p></div>
				<div class="value-card"><i class="fas fa-lock"></i><h3>Ensure Secure Transactions</h3><p>Implementing robust security measures to protect all transactions and user data on our platform.</p></div>
				<div class="value-card"><i class="fas fa-users"></i><h3>Build Community Trust</h3><p>Fostering trust between farmers and buyers through transparent processes and verified profiles.</p></div>
				<div class="value-card"><i class="fas fa-balance-scale"></i><h3>Fair Pricing</h3><p>Ensuring fair compensation for farmers while providing competitive prices for buyers.</p></div>
				<div class="value-card"><i class="fas fa-heart"></i><h3>Quality Commitment</h3><p>Dedicated to maintaining high quality standards for all products listed on our platform.</p></div>
			</div>
		</section>

		<section class="how-it-works">
			<div class="section-header">
				<h2 class="section-title">How GreenMarket Works</h2>
				<p>Simple. Transparent. Direct from Farm to Table.</p>
			</div>
			<div class="hw-grid">
				<div class="hw-card">
					<div class="hw-header"><i class="fas fa-shopping-cart"></i><h3>For Buyers</h3></div>
					<div class="hw-image">
						@php $buyerImage = getHowItWorksConfig('How_Works_For_Buyers_image', $howItWorksConfigs); @endphp
						<img src="{{ asset('assets/images/' . $buyerImage) }}" alt="Buyer process" onerror="this.style.display='none'">
					</div>
					@php $buyerInstructions = getHowItWorksConfig('How_Works_For_Buyers_para', $howItWorksConfigs); $buyerParagraphs = explode("\n\n", $buyerInstructions); @endphp
					@foreach($buyerParagraphs as $paragraph) @if(trim($paragraph)) <p><i class="fas fa-circle"></i> {{ trim($paragraph) }}</p> @endif @endforeach
				</div>
				<div class="hw-card">
					<div class="hw-header"><i class="fas fa-seedling"></i><h3>For Farmers</h3></div>
					<div class="hw-image">
						@php $farmerImage = getHowItWorksConfig('How_Works_For_Farmer_image', $howItWorksConfigs); @endphp
						<img src="{{ asset('assets/images/' . $farmerImage) }}" alt="Farmer process" onerror="this.style.display='none'">
					</div>
					@php $farmerInstructions = getHowItWorksConfig('How_Works_For_Farmers_para', $howItWorksConfigs); $farmerParagraphs = explode("\n\n", $farmerInstructions); @endphp
					@foreach($farmerParagraphs as $paragraph) @if(trim($paragraph)) <p><i class="fas fa-circle"></i> {{ trim($paragraph) }}</p> @endif @endforeach
				</div>
			</div>
			<div class="hw-stats">
				<div class="hw-stat"><span class="hw-stat-value">{{ $stats['total_categories'] ?? 0 }}+</span><span>Categories</span></div>
				<div class="hw-stat"><span class="hw-stat-value">{{ $stats['total_products'] ?? 0 }}+</span><span>Products</span></div>
				<div class="hw-stat"><span class="hw-stat-value">{{ $stats['active_farmers'] ?? 0 }}+</span><span>Farmers</span></div>
				<div class="hw-stat"><span class="hw-stat-value">{{ $stats['total_buyers'] ?? 0 }}+</span><span>Buyers</span></div>
			</div>
		</section>

		<section class="cta">
			<div class="cta-content">
				<h2>Join Our Growing Community</h2>
				<p>Whether you're a farmer looking to expand your market or a buyer seeking fresh local produce, GreenMarket is your platform for sustainable agricultural connections.</p>
				<div class="cta-buttons">
					<a href="{{ url('/contact-us') }}" class="btn"><i class="fas fa-envelope"></i> Contact Us</a>
					<a href="{{ route('buyer.register') }}" class="btn" id="registerTriggerAbout"><i class="fas fa-user-plus"></i> Register</a>
					<a href="{{ route('login') }}" class="btn"><i class="fas fa-sign-in-alt"></i> Login</a>
				</div>
			</div>
		</section>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	const stats = document.querySelectorAll('.stat-number');
	stats.forEach(stat => {
		const target = parseInt(stat.getAttribute('data-count')) || 0;
		let current = 0;
		const increment = target / 100;
		const timer = setInterval(() => {
			current += increment;
			if (current >= target) {
				current = target;
				clearInterval(timer);
			}
			stat.textContent = Math.floor(current) + '+';
		}, 20);
	});

	const hwStats = document.querySelectorAll('.hw-stat-value');
	hwStats.forEach(stat => {
		const text = stat.textContent;
		const number = parseInt(text);
		if (!isNaN(number) && number > 0) {
			let current = 0;
			const increment = number / 25;
			const timer = setInterval(() => {
				current += increment;
				if (current >= number) {
					current = number;
					clearInterval(timer);
				}
				stat.textContent = Math.floor(current) + '+';
			}, 30);
		}
	});

	const registerTrigger = document.getElementById('registerTriggerAbout');
	function registerClickHandler(e) {
		e.preventDefault();
		Swal.fire({
			title: 'Ready to Join?',
			html: `
				<div style="text-align:center;">
					<i class="fas fa-user-plus" style="font-size:2rem; color:#10B981; margin-bottom:8px;"></i>
					<h3 style="color:#0f1724; margin-bottom:6px;">Choose Your Role</h3>
					<p style="color:#6b7280; margin-bottom:12px;">Select how you want to use GreenMarket</p>
					<div style="display:flex; flex-direction:column; gap:6px;">
						<button onclick="window.location.href='{{ route('buyer.register') }}'" style="background:#10B981; color:white; border:none; padding:10px; border-radius:12px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px;">
							<i class="fas fa-shopping-cart"></i> As Buyer
						</button>
						<button onclick="showFarmerInfo()" style="background:white; color:#0f1724; border:1px solid #10B981; padding:10px; border-radius:12px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px;">
							<i class="fas fa-seedling"></i> As Farmer
						</button>
					</div>
				</div>
			`,
			showConfirmButton: false,
			showCloseButton: true,
			background: '#ffffff',
			width: '320px',
			customClass: { popup: 'compact-popup' }
		});
	}
	if (registerTrigger) registerTrigger.addEventListener('click', registerClickHandler);

	window.showFarmerInfo = function() {
		Swal.fire({
			title: 'Farmer Registration',
			html: `
				<div style="text-align:left;">
					<i class="fas fa-seedling" style="font-size:2rem; color:#10B981; display:block; text-align:center; margin-bottom:8px;"></i>
					<div style="background:#f6f8fa; padding:12px; border-radius:14px;">
						<p style="margin-bottom:4px;"><strong>Step 1:</strong> Contact Lead Farmer</p>
						<p style="margin-bottom:4px;"><strong>Step 2:</strong> Provide product details</p>
						<p><strong>Step 3:</strong> Lead Farmer registers you</p>
					</div>
					<p style="color:#6b7280; font-size:0.75rem; margin-top:8px;">Don't know Lead Farmer? Contact Grama Sevakar.</p>
				</div>
			`,
			confirmButtonText: 'Got It',
			confirmButtonColor: '#10B981',
			background: '#ffffff',
			width: '300px'
		});
	};

	const successMessage = sessionStorage.getItem('successMessage');
	if (successMessage) {
		Swal.fire({
			icon: 'success',
			title: 'Success!',
			text: successMessage,
			timer: 2000,
			showConfirmButton: false,
			background: '#ffffff'
		});
		sessionStorage.removeItem('successMessage');
	}

	const errorMessage = sessionStorage.getItem('errorMessage');
	if (errorMessage) {
		Swal.fire({
			icon: 'error',
			title: 'Oops...',
			text: errorMessage,
			background: '#ffffff'
		});
		sessionStorage.removeItem('errorMessage');
	}
});
</script>
@endsection
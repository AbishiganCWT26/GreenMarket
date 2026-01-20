@extends('public_master')

@section('title', 'GreenMarket - Fresh Local Produce Marketplace')


@section('styles')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="main-container">
	<section class="hero-section">
		<div class="hero-slideshow-container">
			<div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-0.png') }}" alt="welcome banner image 1st" style="width:100%">
				<div class="slide-text">Welcome to GreenMarket</div>
			</div>

            <div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-19.jpg') }}" alt="Fresh Vegetables" style="width:100%">
				<div class="slide-text">Fresh From Farm</div>
			</div>

			<div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-14.jpg') }}" alt="Organic Fruits" style="width:100%">
				<div class="slide-text">100% Organic Produce</div>
			</div>

			<div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-22.jpg') }}" alt="Local Farmers" style="width:100%">
				<div class="slide-text">Direct from Local Farmers</div>
			</div>

			<div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-13.jpg') }}" alt="Farm Fresh" style="width:100%">
				<div class="slide-text">Farm to Table</div>
			</div>

			<div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-26.jpg') }}" alt="Quality Guarantee" style="width:100%">
				<div class="slide-text">Quality Guaranteed</div>
			</div>

            <div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-18.jpg') }}" alt="Quality Guarantee" style="width:100%">
				<div class="slide-text">Support Home Gardeners - Empower local communities</div>
			</div>

            <div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-25.jpg') }}" alt="Quality Guarantee" style="width:100%">
				<div class="slide-text">Easy Online Orders - Shop fresh produce anytime</div>
			</div>

            <div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-11.jpg') }}" alt="Quality Guarantee" style="width:100%">
				<div class="slide-text">Real-Time Notifications - Instant order updates</div>
			</div>

            <div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-16.jpg') }}" alt="Quality Guarantee" style="width:100%">
				<div class="slide-text">Smart Taxonomy Search - Find products easily</div>
			</div>

            <div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-10.jpg') }}" alt="Quality Guarantee" style="width:100%">
				<div class="slide-text">Pickup Ready - Contact farmers directly</div>
			</div>

            <div class="hero-slides fade">
				<img src="{{ asset('assets/images/hero-bg-7.jpg') }}" alt="Quality Guarantee" style="width:100%">
				<div class="slide-text">Sustainable Farming - Grow local, buy local</div>
			</div>
		</div>

		<div class="hero-content-overlay">
			<div class="hero-text">
				<div class="hero-tag">
					<span><i class="fas fa-leaf"></i> Fresh & Organic</span>
				</div>
				<h1 class="hero-title">
					<span class="hero-title-main">Fresh From Farm</span>
					<span class="hero-title-sub">To Your Table</span>
				</h1>
				<p class="hero-description">
					Connect directly with local farmers. Get the freshest produce delivered with transparency and trust.
				</p>
				<div class="hero-buttons">
					@if(auth()->check())
						@php
							$user = auth()->user();
							$dashboardUrl = '/';
							switch($user->role) {
								case 'admin': $dashboardUrl = '/admin/dashboard'; break;
								case 'facilitator': $dashboardUrl = '/facilitator/dashboard'; break;
								case 'lead_farmer': $dashboardUrl = '/lead-farmer/dashboard'; break;
								case 'farmer': $dashboardUrl = '/farmer/dashboard'; break;
								case 'buyer': $dashboardUrl = '/buyer/dashboard'; break;
							}
						@endphp
						<a href="{{ url($dashboardUrl) }}" class="btn btn-primary btn-hero">
							<i class="fas fa-tachometer-alt"></i> Dashboard
						</a>
					@else
						<a href="{{ url('/login') }}" class="btn btn-primary btn-hero">
							<i class="fas fa-sign-in-alt"></i> Sign In
						</a>
					@endif
				</div>
				<div class="hero-features">
					<div class="feature">
						<img src="{{ asset('assets/icons/Fair price-yellow.png') }}" alt="Fair Price Icon" width="24" height="24">
						<span>Fair Price</span>
					</div>
					<div class="feature">
						<i class="fas fa-shield-alt"></i>
						<span>Quality Guarantee</span>
					</div>
					<div class="feature">
						<i class="fas fa-handshake"></i>
						<span>Direct from Farmers</span>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="statistics-section">
		<div class="section-header">
			<h2 class="section-title">Our Impact in Numbers</h2>
			<p class="section-subtitle">Connecting farmers and buyers across Sri Lanka</p>
		</div>

		<div class="statistics-grid">
			<div class="stat-card">
				<div class="stat-icon">
					<i class="fas fa-seedling"></i>
				</div>
				<div class="stat-number" data-count="{{ $stats['total_products'] }}">0</div>
				<div class="stat-label">Fresh Products</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon">
					<img src="{{ asset('assets/icons/farmer-icon.svg') }}" alt="farmer Icon" width="35" height="35" style="filter: brightness(0) invert(1);">
				</div>
				<div class="stat-number" data-count="{{ $stats['registered_farmers'] }}">0</div>
				<div class="stat-label">Local Farmers</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon">
					<i class="fas fa-shopping-bag"></i>
				</div>
				<div class="stat-number" data-count="{{ $stats['successful_orders'] }}">0</div>
				<div class="stat-label">Successful Orders</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon">
				   <img src="{{ asset('assets/icons/buyer.svg') }}" alt="farmer Icon" width="30" height="35" style="filter: brightness(0) invert(1);">
				</div>
				<div class="stat-number" data-count="{{ $stats['happy_buyers'] }}">0</div>
				<div class="stat-label">Happy Buyers</div>
			</div>
		</div>
	</section>

	<section class="features-and-benefits-section">
		<div class="section-header">
			<h3 class="section-title">Our Services</h3>
		</div>

		<div class="services-grid">
			<div class="service-card">
				<div class="service-icon">
					<img src="{{ asset('assets/icons/Cash on Delivery.svg') }}" alt="Cash on Delivery">
				</div>
				<h4 class="service-title">CASH ON DELIVERY</h4>
				<p class="service-description">Pay after collection</p>
			</div>

			<div class="service-card">
				<div class="service-icon">
					<img src="{{ asset('assets/icons/Secured Payments.svg') }}" alt="Secured Payments">
				</div>
				<h4 class="service-title">SECURED PAYMENTS</h4>
				<p class="service-description">100% Safe</p>
			</div>

			<div class="service-card">
				<div class="service-icon">
					<img src="{{ asset('assets/icons/Sales Support.svg') }}" alt="Sales Support">
				</div>
				<h4 class="service-title">SALES SUPPORT</h4>
				<p class="service-description">Call and order</p>
			</div>

			<div class="service-card">
				<div class="service-icon">
					<img src="{{ asset('assets/icons/Fair price.svg') }}" alt="Fair Price">
				</div>
				<h4 class="service-title">FAIR PRICE</h4>
				<p class="service-description">Best prices guaranteed</p>
			</div>

			<div class="service-card">
				<div class="service-icon">
					<img src="{{ asset('assets/icons/100 percentage Organic.svg') }}" alt="100% Organic">
				</div>
				<h4 class="service-title">100% ORGANIC</h4>
				<p class="service-description">Natural & chemical-free</p>
			</div>

			<div class="service-card">
				 <div class="service-icon">
					 <img src="{{ asset('assets/icons/Pickup & Transport.svg') }}" alt="Delivery Truck">
				</div>
				<h4 class="service-title">Buyer Handles Pickup</h4>
				<p class="service-description">Buyer Arranges Transport </p>
			</div>
		</div>
	</section>

	<section class="categories-showcase-section">
		<div class="section-header">
			<h2 class="section-title">Our Product Categories</h2>
			<p class="section-subtitle">Fresh produce from various categories</p>
		</div>

		<div class="categories-grid">
            @foreach($categories as $category)
            <div class="category-card">
                <div class="category-icon">
                    @php
                        // Use the icon_filename from the database, with a fallback
                        $icon = $category->icon_filename ?? 'default.png';
                    @endphp
                    <img src="{{ asset('assets/images/taxonomy-icons/' . $icon) }}" alt="{{ $category->category_name }}">
                </div>
                <div class="category-content">
                    <h3>{{ $category->category_name }}</h3>
                    <p>{{ $category->description ?? 'Fresh organic products' }}</p>
                    <div class="category-count">{{ $category->product_count }} Products</div>
                </div>
            </div>
            @endforeach
        </div>
	</section>

	<section class="coverage-section">
		<div class="section-header">
			<h2 class="section-title">Our Geographic Coverage</h2>
			<p class="section-subtitle">Serving farmers across Sri Lanka</p>
		</div>

		<div class="coverage-container">
			<div class="districts-list">
				<h3>Districts We Serve</h3>
				<div class="districts-grid">
					@foreach($districts as $district)
					<div class="district-item">
						<span class="district-name">{{ $district->district }}</span>
						<span class="district-count">{{ $district->farmer_count }} Farmers</span>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/home-extras.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	let slideIndex = 0;
	showSlides();

	function showSlides() {
		let slides = document.getElementsByClassName("hero-slides");

		for (let i = 0; i < slides.length; i++) {
			slides[i].style.display = "none";
		}

		slideIndex++;
		if (slideIndex > slides.length) {slideIndex = 1}

		if(slides[slideIndex-1]) {
			slides[slideIndex-1].style.display = "block";
		}

		setTimeout(showSlides, 3000);
	}
});
</script>
@endsection

@extends('public_master')

@section('title', 'About GreenMarket - Connecting Farmers & Buyers')
@section('page-title', 'about-us')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
@php
    // Direct database query to fetch about_us configuration
    $aboutConfigs = DB::table('system_config')
        ->where('config_group', 'about_us')
        ->where('is_public', true)
        ->pluck('config_value', 'config_key')
        ->toArray();

    // Helper function to get config value with fallback
    function getAboutConfig($key, $configs) {
        return $configs[$key] ?? $default;
    }
@endphp

<div class="about-us-page">
	<section class="about-hero-section">
		<div class="container">
			<div class="hero-content">
				<div class="hero-text animate-fade-in">
					<h1 class="hero-title">About GreenMarket</h1>
					<p class="hero-subtitle">
						Bridging the gap between local farmers and buyers with technology
					</p>
					<p class="hero-description">
						{{ getAboutConfig('about_us_1st_para', $aboutConfigs) }}
					</p>
					<div class="hero-stats">
						<div class="stat-item">
							<div class="stat-number">500+</div>
							<div class="stat-label">Local Farmers</div>
						</div>
						<div class="stat-item">
							<div class="stat-number">2000+</div>
							<div class="stat-label">Products Listed</div>
						</div>
						<div class="stat-item">
							<div class="stat-number">100%</div>
							<div class="stat-label">Secure Payments</div>
						</div>
					</div>
				</div>
				<div class="hero-image animate-slide-in">
					@php
						$aboutImage1 = getAboutConfig('about_us_image_1', $aboutConfigs);
					@endphp
					<img src="{{ asset('assets/images/' . $aboutImage1) }}" alt="Farmers and Buyers Connection" class="hero-img">
					<div class="image-overlay"></div>
				</div>
			</div>
		</div>
	</section>

	<section class="about-details-section">
		<div class="container">
			<div class="about-content">
				<div class="about-image animate-fade-in-left">
					@php
						$aboutImage2 = getAboutConfig('about_us_image_2', $aboutConfigs);
					@endphp
					<img src="{{ asset('assets/images/' . $aboutImage2) }}" alt="Our Farming Community" class="details-img">
					<div class="floating-badge">
						<i class="fas fa-leaf"></i>
						<span>Organic & Fresh</span>
					</div>
				</div>
				<div class="about-text animate-fade-in-right">
					<h2 class="section-title">Our Story</h2>
					<p class="about-description">
						{{ getAboutConfig('about_us_Our_Story_para_1', $aboutConfigs) }}
					</p>
					<p class="about-description">
						{{ getAboutConfig('about_us_Our_Story_para_2', $aboutConfigs) }}
					</p>
					<div class="achievements">
						<div class="achievement-item">
							<i class="fas fa-award achievement-icon"></i>
							<div class="achievement-content">
								<h4>Quality Certified</h4>
								<p>Verified farmers & produce</p>
							</div>
						</div>
						<div class="achievement-item">
							<i class="fas fa-shield-alt achievement-icon"></i>
							<div class="achievement-content">
								<h4>Secure Platform</h4>
								<p>Safe transactions guaranteed</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="vision-mission-section">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title">Our Vision & Mission</h2>
				<p class="section-subtitle">Driving sustainable agricultural transformation</p>
			</div>
			<div class="vm-grid">
				<div class="vm-card vision-card animate-scale-up">
					<div class="vm-icon-container">
						<img src="{{ asset('assets/icons/vision-icon.png') }}" alt="Vision" class="vm-icon">
					</div>
					<h3 class="vm-title">Our Vision</h3>
					<p class="vm-description">
						{{ getAboutConfig('about_us_Vision_para', $aboutConfigs) }}
					</p>
					<div class="vm-features">
						<div class="vm-feature">
							<i class="fas fa-check-circle feature-icon"></i>
							<span>{{ getAboutConfig('about_us_Vision_1st_point', $aboutConfigs) }}</span>
						</div>
						<div class="vm-feature">
							<i class="fas fa-check-circle feature-icon"></i>
							<span>{{ getAboutConfig('about_us_Vision_2nd_point', $aboutConfigs) }}</span>
						</div>
						<div class="vm-feature">
							<i class="fas fa-check-circle feature-icon"></i>
							<span>{{ getAboutConfig('about_us_Vision_3rd_point', $aboutConfigs) }}</span>
						</div>
					</div>
				</div>
				<div class="vm-card mission-card animate-scale-up" style="animation-delay: 0.2s;">
					<div class="vm-icon-container">
						<img src="{{ asset('assets/icons/mission-icon.png') }}" alt="Mission" class="vm-icon">
					</div>
					<h3 class="vm-title">Our Mission</h3>
					<p class="vm-description">
						{{ getAboutConfig('about_us_Mission_para', $aboutConfigs) }}
					</p>
					<div class="vm-features">
						<div class="vm-feature">
							<i class="fas fa-bullseye feature-icon"></i>
							<span>{{ getAboutConfig('about_us_Mission_1st_point', $aboutConfigs) }}</span>
						</div>
						<div class="vm-feature">
							<i class="fas fa-bullseye feature-icon"></i>
							<span>{{ getAboutConfig('about_us_Mission_2nd_point', $aboutConfigs) }}</span>
						</div>
						<div class="vm-feature">
							<i class="fas fa-bullseye feature-icon"></i>
							<span>{{ getAboutConfig('about_us_Mission_3rd_point', $aboutConfigs) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="what-we-offer-section">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title">What We Offer</h2>
				<p class="section-subtitle">Comprehensive solutions for modern agriculture</p>
			</div>
			<div class="offerings-grid">
				<div class="offering-card animate-slide-up">
					<div class="offering-icon">
						<i class="fas fa-store"></i>
					</div>
					<h3 class="offering-title">Digital Marketplace</h3>
					<p class="offering-description">
						A platform for farmers to sell garden produce directly to buyers without intermediaries.
					</p>
					<div class="offering-badge">For Farmers</div>
				</div>
				<div class="offering-card animate-slide-up" style="animation-delay: 0.1s;">
					<div class="offering-icon">
						<i class="fas fa-handshake"></i>
					</div>
					<h3 class="offering-title">Direct Connections</h3>
					<p class="offering-description">
						Connect farmers directly with buyers for transparent and trust-based relationships.
					</p>
					<div class="offering-badge">For Everyone</div>
				</div>
				<div class="offering-card animate-slide-up" style="animation-delay: 0.2s;">
					<div class="offering-icon">
						<i class="fas fa-shield-alt"></i>
					</div>
					<h3 class="offering-title">Secure Payments</h3>
					<p class="offering-description">
						Safe and reliable payment gateway ensuring secure transactions for all parties.
					</p>
					<div class="offering-badge">Secure</div>
				</div>
				<div class="offering-card animate-slide-up" style="animation-delay: 0.3s;">
					<div class="offering-icon">
						<i class="fas fa-tools"></i>
					</div>
					<h3 class="offering-title">Management Tools</h3>
					<p class="offering-description">
						Easy-to-use product management tools for inventory and sales tracking.
					</p>
					<div class="offering-badge">For Sellers</div>
				</div>
				<div class="offering-card animate-slide-up" style="animation-delay: 0.4s;">
					<div class="offering-icon">
						<i class="fas fa-mobile-alt"></i>
					</div>
					<h3 class="offering-title">Mobile Access</h3>
					<p class="offering-description">
						Access the platform from any device with responsive design and mobile optimization.
					</p>
					<div class="offering-badge">Accessible</div>
				</div>
				<div class="offering-card animate-slide-up" style="animation-delay: 0.5s;">
					<div class="offering-icon">
						<i class="fas fa-chart-line"></i>
					</div>
					<h3 class="offering-title">Analytics & Reports</h3>
					<p class="offering-description">
						Detailed sales analytics and performance reports for better decision making.
					</p>
					<div class="offering-badge">Insights</div>
				</div>
			</div>
		</div>
	</section>

	<section class="our-values-section">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title">Our Values</h2>
				<p class="section-subtitle">The principles that guide everything we do</p>
			</div>
			<div class="values-grid">
				<div class="value-card animate-float">
					<div class="value-icon-container">
						<div class="value-icon">
							<i class="fas fa-tractor"></i>
						</div>
					</div>
					<h3 class="value-title">Support Local Agriculture</h3>
					<p class="value-description">
						We prioritize and empower local farmers, preserving traditional farming practices while integrating modern technology.
					</p>
				</div>
				<div class="value-card animate-float" style="animation-delay: 0.1s;">
					<div class="value-icon-container">
						<div class="value-icon">
							<i class="fas fa-leaf"></i>
						</div>
					</div>
					<h3 class="value-title">Promote Sustainability</h3>
					<p class="value-description">
						Encouraging eco-friendly farming practices and reducing food waste through direct farm-to-table connections.
					</p>
				</div>
				<div class="value-card animate-float" style="animation-delay: 0.2s;">
					<div class="value-icon-container">
						<div class="value-icon">
							<i class="fas fa-lock"></i>
						</div>
					</div>
					<h3 class="value-title">Ensure Secure Transactions</h3>
					<p class="value-description">
						Implementing robust security measures to protect all transactions and user data on our platform.
					</p>
				</div>
				<div class="value-card animate-float" style="animation-delay: 0.3s;">
					<div class="value-icon-container">
						<div class="value-icon">
							<i class="fas fa-users"></i>
						</div>
					</div>
					<h3 class="value-title">Build Community Trust</h3>
					<p class="value-description">
						Fostering trust between farmers and buyers through transparent processes and verified profiles.
					</p>
				</div>
				<div class="value-card animate-float" style="animation-delay: 0.4s;">
					<div class="value-icon-container">
						<div class="value-icon">
							<i class="fas fa-balance-scale"></i>
						</div>
					</div>
					<h3 class="value-title">Fair Pricing</h3>
					<p class="value-description">
						Ensuring fair compensation for farmers while providing competitive prices for buyers.
					</p>
				</div>
				<div class="value-card animate-float" style="animation-delay: 0.5s;">
					<div class="value-icon-container">
						<div class="value-icon">
							<i class="fas fa-heart"></i>
						</div>
					</div>
					<h3 class="value-title">Quality Commitment</h3>
					<p class="value-description">
						Dedicated to maintaining high quality standards for all products listed on our platform.
					</p>
				</div>
			</div>
		</div>
	</section>

	<section class="cta-section">
		<div class="container">
			<div class="cta-content animate-fade-in">
				<h2 class="cta-title">Join Our Growing Community</h2>
				<p class="cta-description">
					Whether you're a farmer looking to expand your market or a buyer seeking fresh local produce, GreenMarket is your platform for sustainable agricultural connections.
				</p>
				<div class="cta-buttons">
					<a href="{{ url('/contact-us') }}" class="btn btn-primary btn-cta">
						<i class="fas fa-envelope"></i> Contact Us
					</a>
					<button class="btn btn-secondary btn-cta" id="learnMoreBtn">
						<i class="fas fa-info-circle"></i> Learn More
					</button>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('scripts')
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
	--shadow-lg: 0 20px 40px rgba(15,23,36,0.12);
	--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.about-us-page {
	background-color: var(--body-bg);
	min-height: 100vh;
}

.container {
	max-width: 1200px;
	margin: 0 auto;
	padding: 0 20px;
}

.about-hero-section {
	padding: 80px 0;
	background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%);
	position: relative;
	overflow: hidden;
}

.hero-content {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 60px;
	align-items: center;
}

.hero-text {
	z-index: 2;
}

.hero-title {
	font-size: 3rem;
	font-weight: 800;
	color: var(--text-color);
	margin-bottom: 20px;
	line-height: 1.2;
}

.hero-subtitle {
	font-size: 1.25rem;
	color: var(--primary-green);
	font-weight: 600;
	margin-bottom: 25px;
}

.hero-description {
	font-size: 1.1rem;
	line-height: 1.6;
	color: var(--muted);
	margin-bottom: 30px;
}

.hero-stats {
	display: flex;
	gap: 30px;
	margin-top: 40px;
}

.stat-item {
	text-align: center;
}

.stat-number {
	font-size: 2rem;
	font-weight: 700;
	color: var(--primary-green);
	line-height: 1;
}

.stat-label {
	font-size: 0.9rem;
	color: var(--muted);
	margin-top: 5px;
}

.hero-image {
	position: relative;
	border-radius: 20px;
	overflow: hidden;
	box-shadow: var(--shadow-lg);
}

.hero-img {
	width: 100%;
	height: 400px;
	object-fit: cover;
	border-radius: 20px;
	transition: var(--transition);
}

.hero-image:hover .hero-img {
	transform: scale(1.05);
}

.image-overlay {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: linear-gradient(45deg, rgba(16, 185, 129, 0.2), transparent);
	border-radius: 20px;
}

.about-details-section {
	padding: 100px 0;
	background-color: var(--card-bg);
}

.about-content {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 60px;
	align-items: center;
}

.about-image {
	position: relative;
}

.details-img {
	width: 100%;
	border-radius: 20px;
	box-shadow: var(--shadow-md);
	transition: var(--transition);
}

.about-image:hover .details-img {
	transform: translateY(-10px);
	box-shadow: var(--shadow-lg);
}

.floating-badge {
	position: absolute;
	top: 20px;
	right: 20px;
	background: var(--primary-green);
	color: white;
	padding: 10px 20px;
	border-radius: 50px;
	display: flex;
	align-items: center;
	gap: 10px;
	font-weight: 600;
	box-shadow: var(--shadow-md);
	animation: float 3s ease-in-out infinite;
}

.section-title {
	font-size: 2.5rem;
	font-weight: 700;
	color: var(--text-color);
	margin-bottom: 20px;
}

.section-subtitle {
	font-size: 1.1rem;
	color: var(--muted);
	margin-bottom: 50px;
	text-align: center;
}

.about-description {
	font-size: 1.05rem;
	line-height: 1.7;
	color: var(--muted);
	margin-bottom: 20px;
}

.achievements {
	margin-top: 30px;
	display: flex;
	flex-direction: column;
	gap: 20px;
}

.achievement-item {
	display: flex;
	align-items: center;
	gap: 15px;
	padding: 15px;
	background: var(--body-bg);
	border-radius: 10px;
	transition: var(--transition);
}

.achievement-item:hover {
	transform: translateX(10px);
	background: rgba(16, 185, 129, 0.1);
}

.achievement-icon {
	font-size: 1.5rem;
	color: var(--primary-green);
}

.achievement-content h4 {
	font-size: 1.1rem;
	color: var(--text-color);
	margin-bottom: 5px;
}

.achievement-content p {
	font-size: 0.9rem;
	color: var(--muted);
}

.vision-mission-section {
	padding: 100px 0;
	background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
}

.section-header {
	text-align: center;
	margin-bottom: 60px;
}

.vm-grid {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 40px;
}

.vm-card {
	background: var(--card-bg);
	padding: 40px;
	border-radius: 20px;
	box-shadow: var(--shadow-md);
	transition: var(--transition);
	position: relative;
	overflow: hidden;
}

.vm-card:hover {
	transform: translateY(-10px);
	box-shadow: var(--shadow-lg);
}

.vm-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 5px;
}

.vision-card::before {
	background: var(--primary-green);
}

.mission-card::before {
	background: var(--accent-amber);
}

.vm-icon-container {
	width: 80px;
	height: 80px;
	background: rgba(16, 185, 129, 0.1);
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	margin-bottom: 25px;
}

.mission-card .vm-icon-container {
	background: rgba(245, 158, 11, 0.1);
}

.vm-icon {
	width: 40px;
	height: 40px;
}

.vm-title {
	font-size: 1.8rem;
	font-weight: 700;
	color: var(--text-color);
	margin-bottom: 20px;
}

.vm-description {
	font-size: 1.05rem;
	line-height: 1.6;
	color: var(--muted);
	margin-bottom: 25px;
}

.vm-features {
	display: flex;
	flex-direction: column;
	gap: 15px;
}

.vm-feature {
	display: flex;
	align-items: center;
	gap: 10px;
}

.feature-icon {
	color: var(--primary-green);
	font-size: 0.9rem;
}

.mission-card .feature-icon {
	color: var(--accent-amber);
}

.vm-feature span {
	font-size: 0.95rem;
	color: var(--text-color);
}

.what-we-offer-section {
	padding: 100px 0;
	background-color: var(--card-bg);
}

.offerings-grid {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 30px;
}

.offering-card {
	background: var(--card-bg);
	padding: 30px;
	border-radius: 15px;
	box-shadow: var(--shadow-sm);
	transition: var(--transition);
	position: relative;
	border: 2px solid transparent;
}

.offering-card:hover {
	transform: translateY(-10px);
	box-shadow: var(--shadow-md);
	border-color: var(--primary-green);
}

.offering-icon {
	width: 60px;
	height: 60px;
	background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
	border-radius: 15px;
	display: flex;
	align-items: center;
	justify-content: center;
	margin-bottom: 25px;
	color: white;
	font-size: 1.5rem;
	transition: var(--transition);
}

.offering-card:hover .offering-icon {
	transform: scale(1.1) rotate(5deg);
}

.offering-title {
	font-size: 1.3rem;
	font-weight: 600;
	color: var(--text-color);
	margin-bottom: 15px;
}

.offering-description {
	font-size: 0.95rem;
	line-height: 1.6;
	color: var(--muted);
	margin-bottom: 20px;
}

.offering-badge {
	display: inline-block;
	padding: 5px 15px;
	background: rgba(16, 185, 129, 0.1);
	color: var(--primary-green);
	border-radius: 20px;
	font-size: 0.85rem;
	font-weight: 600;
}

.our-values-section {
	padding: 100px 0;
	background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
}

.values-grid {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 30px;
}

.value-card {
	background: var(--card-bg);
	padding: 30px;
	border-radius: 15px;
	text-align: center;
	box-shadow: var(--shadow-sm);
	transition: var(--transition);
}

.value-card:hover {
	transform: translateY(-5px);
	box-shadow: var(--shadow-md);
}

.value-icon-container {
	margin-bottom: 25px;
}

.value-icon {
	width: 70px;
	height: 70px;
	background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	margin: 0 auto;
	color: white;
	font-size: 1.8rem;
	transition: var(--transition);
}

.value-card:hover .value-icon {
	transform: scale(1.1) rotate(360deg);
}

.value-title {
	font-size: 1.3rem;
	font-weight: 600;
	color: var(--text-color);
	margin-bottom: 15px;
}

.value-description {
	font-size: 0.95rem;
	line-height: 1.6;
	color: var(--muted);
}

.cta-section {
	padding: 100px 0;
	background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
	color: white;
	text-align: center;
}

.cta-title {
	font-size: 2.5rem;
	font-weight: 700;
	margin-bottom: 20px;
}

.cta-description {
	font-size: 1.1rem;
	line-height: 1.6;
	max-width: 700px;
	margin: 0 auto 40px;
	opacity: 0.9;
}

.cta-buttons {
	display: flex;
	gap: 20px;
	justify-content: center;
}

.btn {
	padding: 15px 30px;
	border-radius: 50px;
	font-weight: 600;
	text-decoration: none;
	transition: var(--transition);
	border: none;
	cursor: pointer;
	display: inline-flex;
	align-items: center;
	gap: 10px;
	font-size: 1rem;
}

.btn-primary {
	background: white;
	color: var(--primary-green);
}

.btn-primary:hover {
	background: rgba(255, 255, 255, 0.9);
	transform: translateY(-3px);
	box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.btn-secondary {
	background: transparent;
	color: white;
	border: 2px solid white;
}

.btn-secondary:hover {
	background: rgba(255, 255, 255, 0.1);
	transform: translateY(-3px);
}

@keyframes float {
	0%, 100% {
		transform: translateY(0);
	}
	50% {
		transform: translateY(-10px);
	}
}

@keyframes fade-in {
	from {
		opacity: 0;
		transform: translateY(20px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

@keyframes slide-in {
	from {
		opacity: 0;
		transform: translateX(50px);
	}
	to {
		opacity: 1;
		transform: translateX(0);
	}
}

@keyframes fade-in-left {
	from {
		opacity: 0;
		transform: translateX(-50px);
	}
	to {
		opacity: 1;
		transform: translateX(0);
	}
}

@keyframes fade-in-right {
	from {
		opacity: 0;
		transform: translateX(50px);
	}
	to {
		opacity: 1;
		transform: translateX(0);
	}
}

@keyframes scale-up {
	from {
		opacity: 0;
		transform: scale(0.9);
	}
	to {
		opacity: 1;
		transform: scale(1);
	}
}

@keyframes slide-up {
	from {
		opacity: 0;
		transform: translateY(50px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

.animate-fade-in {
	animation: fade-in 1s ease-out;
}

.animate-slide-in {
	animation: slide-in 1s ease-out;
}

.animate-fade-in-left {
	animation: fade-in-left 1s ease-out;
}

.animate-fade-in-right {
	animation: fade-in-right 1s ease-out;
}

.animate-scale-up {
	animation: scale-up 1s ease-out;
}

.animate-slide-up {
	animation: slide-up 0.8s ease-out;
}

.animate-float {
	animation: float 3s ease-in-out infinite;
}

@media screen and (max-width: 1199px) {
	.container {
		max-width: 960px;
	}
	.hero-title {
		font-size: 2.5rem;
	}
	.section-title {
		font-size: 2rem;
	}
}

@media screen and (max-width: 991px) {
	.hero-content,
	.about-content,
	.vm-grid {
		grid-template-columns: 1fr;
		gap: 40px;
	}
	.offerings-grid,
	.values-grid {
		grid-template-columns: repeat(2, 1fr);
	}
	.team-grid {
		grid-template-columns: repeat(3, 1fr);
	}
	.hero-title {
		font-size: 2.2rem;
	}
	.hero-stats {
		justify-content: center;
	}
}

@media screen and (max-width: 767px) {
	.hero-title {
		font-size: 2rem;
	}
	.section-title {
		font-size: 1.8rem;
	}
	.offerings-grid,
	.values-grid {
		grid-template-columns: 1fr;
	}
	.team-grid {
		grid-template-columns: repeat(2, 1fr);
	}
	.cta-buttons {
		flex-direction: column;
		align-items: center;
	}
	.btn {
		width: 100%;
		max-width: 300px;
		justify-content: center;
	}
	.hero-stats {
		flex-direction: column;
		gap: 20px;
	}
}

@media screen and (max-width: 480px) {
	.team-grid {
		grid-template-columns: 1fr;
	}
	.hero-title {
		font-size: 1.8rem;
	}
	.cta-title {
		font-size: 2rem;
	}
	.vm-card,
	.offering-card,
	.value-card,
	.team-card {
		padding: 20px;
	}
	.container {
		padding: 0 15px;
	}
}

@media screen and (max-width: 1000px) {
	.hero-content {
		gap: 40px;
	}
	.about-content {
		gap: 40px;
	}
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
	const learnMoreBtn = document.getElementById('learnMoreBtn');

	if (learnMoreBtn) {
		learnMoreBtn.addEventListener('click', function() {
			Swal.fire({
				title: 'About GreenMarket',
				html: `
					<div style="text-align: left;">
						<p><strong>🌱 Our Platform Features:</strong></p>
						<ul style="text-align: left; margin-left: 20px;">
							<li>Direct farmer-to-buyer connections</li>
							<li>Secure payment processing</li>
							<li>Real-time inventory management</li>
							<li>Quality assurance standards</li>
							<li>Mobile-responsive design</li>
							<li>Multi-language support</li>
						</ul>
						<p style="margin-top: 15px;"><strong>📞 Get Started:</strong></p>
						<p>Register today to experience fresh, local produce delivered with transparency and trust.</p>
					</div>
				`,
				icon: 'info',
				confirmButtonText: 'Get Started',
				confirmButtonColor: '#10B981',
				showCancelButton: true,
				cancelButtonText: 'Close',
				cancelButtonColor: '#6b7280',
				width: '600px'
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = '/login';
				}
			});
		});
	}

	const teamCards = document.querySelectorAll('.team-card');
	teamCards.forEach(card => {
		card.addEventListener('click', function() {
			const role = this.querySelector('.team-role').textContent;
			const name = this.querySelector('.team-name').textContent;
			const description = this.querySelector('.team-description').textContent;

			Swal.fire({
				title: name,
				html: `
					<div style="text-align: center;">
						<div style="width: 60px; height: 60px; background: linear-gradient(135deg, #10B981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: white; font-size: 1.5rem;">
							${this.querySelector('.team-avatar').innerHTML}
						</div>
						<p style="color: #10B981; font-weight: 600; margin-bottom: 15px;">${role}</p>
						<p>${description}</p>
					</div>
				`,
				icon: 'info',
				confirmButtonText: 'OK',
				confirmButtonColor: '#10B981'
			});
		});
	});

	const offeringCards = document.querySelectorAll('.offering-card');
	offeringCards.forEach(card => {
		card.addEventListener('click', function() {
			const title = this.querySelector('.offering-title').textContent;
			const description = this.querySelector('.offering-description').textContent;
			const badge = this.querySelector('.offering-badge').textContent;

			Swal.fire({
				title: title,
				html: `
					<div style="text-align: center;">
						<div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10B981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: white; font-size: 1.2rem;">
							${this.querySelector('.offering-icon').innerHTML}
						</div>
						<div style="display: inline-block; padding: 4px 12px; background: rgba(16, 185, 129, 0.1); color: #10B981; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-bottom: 15px;">
							${badge}
						</div>
						<p>${description}</p>
					</div>
				`,
				icon: 'success',
				confirmButtonText: 'Got it',
				confirmButtonColor: '#10B981'
			});
		});
	});

	const animateElements = document.querySelectorAll('.animate-fade-in, .animate-slide-in, .animate-fade-in-left, .animate-fade-in-right, .animate-scale-up, .animate-slide-up, .animate-float');

	const observerOptions = {
		threshold: 0.1,
		rootMargin: '0px 0px -50px 0px'
	};

	const observer = new IntersectionObserver((entries) => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				entry.target.style.animationPlayState = 'running';
				observer.unobserve(entry.target);
			}
		});
	}, observerOptions);

	animateElements.forEach(element => {
		element.style.animationPlayState = 'paused';
		observer.observe(element);
	});

	const stats = document.querySelectorAll('.stat-number');
	stats.forEach(stat => {
		const target = parseInt(stat.textContent);
		let current = 0;
		const increment = target / 100;
		const timer = setInterval(() => {
			current += increment;
			if (current >= target) {
				current = target;
				clearInterval(timer);
			}
			stat.textContent = Math.floor(current) + (stat.textContent.includes('+') ? '+' : '');
		}, 20);
	});
});
</script>
@endsection

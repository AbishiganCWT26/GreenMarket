@extends('public_master')

@section('title', 'How It Works - GreenMarket')

@section('styles')
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
		--transition: all 0.25s ease;
	}

	.how-it-works-page {
		background: var(--body-bg);
		min-height: 100vh;
		width: 100%;
		padding: 15px 0;
	}

	.page-header {
		background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
		color: white;
		padding: 25px 15px;
		text-align: center;
		width: 100%;
		margin-bottom: 20px;
		border-radius: 0 0 20px 20px;
	}

	.page-header h1 {
		font-size: 1.5rem;
		font-weight: 700;
		margin-bottom: 8px;
		letter-spacing: 0.5px;
	}

	.page-header p {
		font-size: 0.8rem;
		max-width: 500px;
		margin: 0 auto;
		opacity: 0.9;
		line-height: 1.3;
	}

	.container {
		width: 100%;
		max-width: 100%;
		padding: 0 10px;
	}

	.role-cards {
		display: grid;
		grid-template-columns: 1fr;
		gap: 15px;
		width: 100%;
		padding: 0 5px;
	}

	.role-card {
		background: var(--card-bg);
		border-radius: 12px;
		padding: 18px;
		box-shadow: var(--shadow-sm);
		transition: var(--transition);
		border: 1px solid #e5e7eb;
		position: relative;
		overflow: hidden;
		height: auto;
	}

	.role-card::after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 4px;
		height: 100%;
		background: linear-gradient(to bottom, var(--primary-green), var(--dark-green));
		transform: scaleY(0);
		transform-origin: top;
		transition: transform 0.3s ease;
	}

	.role-card:hover::after {
		transform: scaleY(1);
	}

	.role-card:hover {
		box-shadow: var(--shadow-md);
		transform: translateY(-3px);
	}

	.role-icon {
		font-size: 1.5rem;
		color: var(--primary-green);
		margin-bottom: 12px;
		display: inline-block;
	}

	.role-title {
		font-size: 1rem;
		font-weight: 600;
		color: var(--text-color);
		margin-bottom: 10px;
		display: flex;
		align-items: center;
		gap: 6px;
	}

	.role-description {
		color: var(--muted);
		line-height: 1.4;
		font-size: 0.8rem;
		margin-bottom: 8px;
	}

	.role-image-container {
		margin: 12px 0;
		border-radius: 8px;
		overflow: hidden;
		box-shadow: var(--shadow-sm);
		position: relative;
		height: 140px;
	}

	.role-image {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
		transition: var(--transition);
	}

	.role-image:hover {
		transform: scale(1.03);
	}

	.image-caption {
		text-align: center;
		padding: 6px;
		background: rgba(16, 185, 129, 0.05);
		color: var(--muted);
		font-size: 0.75rem;
		font-weight: 500;
	}

	.stats-grid {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 12px;
		margin: 25px 0;
		width: 100%;
		padding: 0 5px;
	}

	.stat-card {
		background: var(--card-bg);
		padding: 16px;
		border-radius: 10px;
		text-align: center;
		box-shadow: var(--shadow-sm);
		transition: var(--transition);
		border: 1px solid #e5e7eb;
		position: relative;
		overflow: hidden;
		cursor: pointer;
	}

	.stat-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 3px;
		background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
		transform: scaleX(0);
		transform-origin: left;
		transition: transform 0.3s ease;
	}

	.stat-card:hover::before {
		transform: scaleX(1);
	}

	.stat-card:hover {
		transform: translateY(-2px);
		box-shadow: 0 5px 15px rgba(16, 185, 129, 0.1);
	}

	.stat-number {
		font-size: 1.5rem;
		font-weight: 700;
		color: var(--primary-green);
		line-height: 1;
		margin-bottom: 4px;
	}

	.stat-label {
		color: var(--muted);
		font-size: 0.75rem;
		font-weight: 500;
	}

	.features-section {
		margin-top: 25px;
		width: 100%;
		padding: 0 5px;
	}

	.section-title {
		font-size: 1.1rem;
		font-weight: 600;
		color: var(--text-color);
		margin-bottom: 15px;
		text-align: center;
		position: relative;
		padding-bottom: 6px;
	}

	.section-title::after {
		content: '';
		position: absolute;
		bottom: 0;
		left: 50%;
		transform: translateX(-50%);
		width: 30px;
		height: 2px;
		background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
		border-radius: 1px;
	}

	.features-grid {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 12px;
	}

	.feature-item {
		background: var(--card-bg);
		padding: 16px;
		border-radius: 10px;
		text-align: center;
		box-shadow: var(--shadow-sm);
		transition: var(--transition);
		border: 1px solid #e5e7eb;
		position: relative;
		overflow: hidden;
	}

	.feature-item:hover {
		transform: translateY(-3px);
		box-shadow: 0 6px 12px rgba(16, 185, 129, 0.12);
		border-color: var(--primary-green);
	}

	.feature-icon {
		font-size: 1.3rem;
		color: var(--primary-green);
		margin-bottom: 8px;
		display: inline-block;
		transition: var(--transition);
	}

	.feature-item:hover .feature-icon {
		transform: scale(1.1);
	}

	.feature-title {
		font-weight: 600;
		color: var(--text-color);
		margin-bottom: 6px;
		font-size: 0.85rem;
	}

	.feature-desc {
		color: var(--muted);
		font-size: 0.75rem;
		line-height: 1.3;
	}

	.why-choose-us {
		margin-top: 25px;
		width: 100%;
		padding: 0 5px;
	}

	.benefits-list {
		margin-top: 12px;
	}

	.benefit-item {
		display: flex;
		align-items: center;
		gap: 10px;
		margin-bottom: 12px;
		padding: 14px;
		background: var(--card-bg);
		border-radius: 10px;
		box-shadow: var(--shadow-sm);
		transition: var(--transition);
		border: 1px solid #e5e7eb;
	}

	.benefit-item:hover {
		transform: translateX(4px);
		box-shadow: 0 5px 12px rgba(16, 185, 129, 0.1);
		border-color: var(--primary-green);
	}

	.benefit-icon {
		color: var(--primary-green);
		font-size: 1rem;
		flex-shrink: 0;
		transition: var(--transition);
	}

	.benefit-item:hover .benefit-icon {
		transform: rotate(15deg);
	}

	.benefit-text {
		flex: 1;
	}

	.benefit-title {
		font-weight: 600;
		color: var(--text-color);
		margin-bottom: 3px;
		font-size: 0.85rem;
	}

	.benefit-desc {
		color: var(--muted);
		font-size: 0.75rem;
		line-height: 1.3;
	}

	.help-section {
		background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(5, 150, 105, 0.05));
		border-radius: 12px;
		padding: 20px;
		margin-top: 25px;
		text-align: center;
		border: 1px solid rgba(16, 185, 129, 0.1);
		width: 100%;
	}

	.help-title {
		font-size: 1rem;
		font-weight: 600;
		color: var(--text-color);
		margin-bottom: 12px;
	}

	.help-content {
		color: var(--muted);
		font-size: 0.8rem;
		line-height: 1.4;
		margin-bottom: 16px;
	}

	.help-buttons {
		margin-top: 16px;
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.help-btn {
		padding: 10px 16px;
		border-radius: 8px;
		font-weight: 600;
		text-decoration: none;
		transition: var(--transition);
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 6px;
		border: 1px solid transparent;
		cursor: pointer;
		font-size: 0.85rem;
		width: 100%;
	}

	.btn-primary {
		background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
		color: white;
		box-shadow: 0 3px 8px rgba(16, 185, 129, 0.2);
	}

	.btn-primary:hover {
		transform: translateY(-2px);
		box-shadow: 0 5px 12px rgba(16, 185, 129, 0.3);
	}

	.btn-secondary {
		background: var(--card-bg);
		color: var(--text-color);
		border-color: var(--primary-green);
	}

	.btn-secondary:hover {
		background: rgba(16, 185, 129, 0.05);
		transform: translateY(-2px);
	}

	@keyframes fadeIn {
		from { opacity: 0; transform: translateY(10px); }
		to { opacity: 1; transform: translateY(0); }
	}

	@keyframes float {
		0%, 100% { transform: translateY(0); }
		50% { transform: translateY(-5px); }
	}

	@keyframes shimmer {
		0% { background-position: -200px 0; }
		100% { background-position: calc(200px + 100%) 0; }
	}

	.float-animation {
		animation: float 3s ease-in-out infinite;
	}

	.fade-in {
		animation: fadeIn 0.5s ease-out;
	}

	.shimmer-effect {
		background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
		background-size: 200px 100%;
		animation: shimmer 1.5s infinite;
	}

	@media (min-width: 768px) {
		.page-header {
			padding: 30px 20px;
		}

		.page-header h1 {
			font-size: 1.8rem;
		}

		.container {
			padding: 0 20px;
		}

		.role-cards {
			grid-template-columns: repeat(2, 1fr);
			gap: 20px;
			padding: 0 15px;
		}

		.role-image-container {
			height: 150px;
		}

		.stats-grid {
			grid-template-columns: repeat(4, 1fr);
			gap: 15px;
			padding: 0 15px;
		}

		.features-grid {
			grid-template-columns: repeat(3, 1fr);
			gap: 15px;
		}

		.help-buttons {
			flex-direction: row;
			justify-content: center;
			gap: 12px;
		}

		.help-btn {
			width: auto;
			min-width: 140px;
		}
	}

	@media (min-width: 1024px) {
		.page-header {
			padding: 35px 30px;
		}

		.page-header h1 {
			font-size: 2rem;
		}

		.container {
			padding: 0 30px;
		}

		.role-cards {
			gap: 25px;
			padding: 0 20px;
		}

		.role-image-container {
			height: 160px;
		}

		.features-grid {
			grid-template-columns: repeat(4, 1fr);
			gap: 18px;
		}

		.benefits-list {
			display: grid;
			grid-template-columns: repeat(2, 1fr);
			gap: 15px;
		}
	}

	@media (min-width: 1200px) {
		.container {
			max-width: 1200px;
			margin: 0 auto;
		}
	}

	@media (max-width: 360px) {
		.page-header {
			padding: 20px 10px;
		}

		.page-header h1 {
			font-size: 1.3rem;
		}

		.container {
			padding: 0 8px;
		}

		.role-card {
			padding: 14px;
		}

		.role-image-container {
			height: 120px;
		}

		.features-grid {
			grid-template-columns: 1fr;
		}
	}

	@media (hover: none) {
		.role-card:hover,
		.stat-card:hover,
		.feature-item:hover,
		.benefit-item:hover,
		.help-btn:hover {
			transform: none;
		}
	}

	@media (prefers-reduced-motion: reduce) {
		* {
			animation-duration: 0.01ms !important;
			animation-iteration-count: 1 !important;
			transition-duration: 0.01ms !important;
		}
	}
</style>
@endsection

@section('content')
@php
    // Direct database query to fetch how_it_works configuration
    $howItWorksConfigs = DB::table('system_config')
        ->where('config_group', 'how_it_works')
        ->where('is_public', true)
        ->pluck('config_value', 'config_key')
        ->toArray();

    // Helper function to get config value with fallback
    function getHowItWorksConfig($key, $configs) {
        return $configs[$key] ?? $default;
    }
@endphp

<div class="how-it-works-page">
	<div class="page-header">
		<h1 class="float-animation">How GreenMarket Works</h1>
		<p>Simple. Transparent. Direct from Farm to Table.</p>
	</div>

	<div class="container">
		<div class="role-cards fade-in">
			<!-- For Buyers Card -->
			<div class="role-card shimmer-effect">
				<div class="role-icon">
					<i class="fas fa-shopping-cart"></i>
				</div>
				<h3 class="role-title">
					<i class="fas fa-user-tag"></i> For Buyers
				</h3>

				<div class="role-image-container">
					@php
						$buyerImage = getHowItWorksConfig('How_Works_For_Buyers_image', $howItWorksConfigs);
					@endphp
					<img src="{{ asset('assets/images/' . $buyerImage) }}" alt="How GreenMarket Works for Buyers" class="role-image" onerror="this.style.display='none'">
					<div class="image-caption">Buyer Process Flow</div>
				</div>

				@php
					$buyerInstructions = getHowItWorksConfig('How_Works_For_Buyers_para', $howItWorksConfigs);
					// Split the text by new lines to create separate paragraphs
					$buyerParagraphs = explode("\n\n", $buyerInstructions);
				@endphp

				@foreach($buyerParagraphs as $paragraph)
					@if(trim($paragraph))
						<p class="role-description">
							{{ trim($paragraph) }}
						</p>
					@endif
				@endforeach
			</div>

			<!-- For Farmers Card -->
			<div class="role-card shimmer-effect">
				<div class="role-icon">
					<i class="fas fa-seedling"></i>
				</div>
				<h3 class="role-title">
					<i class="fas fa-tractor"></i> For Farmers
				</h3>

				<div class="role-image-container">
					@php
						$farmerImage = getHowItWorksConfig('How_Works_For_Farmer_image', $howItWorksConfigs);
					@endphp
					<img src="{{ asset('assets/images/' . $farmerImage) }}" alt="How GreenMarket Works for Farmers" class="role-image" onerror="this.style.display='none'">
					<div class="image-caption">Farmer Process Flow</div>
				</div>

				@php
					$farmerInstructions = getHowItWorksConfig('How_Works_For_Farmers_para', $howItWorksConfigs);
					// Split the text by new lines to create separate paragraphs
					$farmerParagraphs = explode("\n\n", $farmerInstructions);
				@endphp

				@foreach($farmerParagraphs as $paragraph)
					@if(trim($paragraph))
						<p class="role-description">
							{{ trim($paragraph) }}
						</p>
					@endif
				@endforeach
			</div>
		</div>

		<div class="stats-grid fade-in">
			<div class="stat-card">
				<div class="stat-number">{{ $stats['total_categories'] ?? 0 }}+</div>
				<div class="stat-label">Product Categories</div>
			</div>
			<div class="stat-card">
				<div class="stat-number">{{ $stats['total_products'] ?? 0 }}+</div>
				<div class="stat-label">Available Products</div>
			</div>
			<div class="stat-card">
				<div class="stat-number">{{ $stats['active_farmers'] ?? 0 }}+</div>
				<div class="stat-label">Active Farmers</div>
			</div>
			<div class="stat-card">
				<div class="stat-number">{{ $stats['total_buyers'] ?? 0 }}+</div>
				<div class="stat-label">Happy Buyers</div>
			</div>
		</div>

		<div class="features-section fade-in">
			<h2 class="section-title">Key Features</h2>
			<div class="features-grid">
				<div class="feature-item">
					<div class="feature-icon">
						<i class="fas fa-user-shield"></i>
					</div>
					<h4 class="feature-title">Secure Registration</h4>
					<p class="feature-desc">Role-based verified accounts</p>
				</div>
				<div class="feature-item">
					<div class="feature-icon">
						<i class="fas fa-bell"></i>
					</div>
					<h4 class="feature-title">Real-time Notifications</h4>
					<p class="feature-desc">SMS and email updates</p>
				</div>
				<div class="feature-item">
					<div class="feature-icon">
						<i class="fas fa-eye"></i>
					</div>
					<h4 class="feature-title">Transparent Process</h4>
					<p class="feature-desc">Clear order tracking</p>
				</div>
				<div class="feature-item">
					<div class="feature-icon">
						<i class="fas fa-comments"></i>
					</div>
					<h4 class="feature-title">Direct Communication</h4>
					<p class="feature-desc">Connect with relevant parties</p>
				</div>
				<div class="feature-item">
					<div class="feature-icon">
						<i class="fas fa-star"></i>
					</div>
					<h4 class="feature-title">Feedback System</h4>
					<p class="feature-desc">Rate and review</p>
				</div>
				<div class="feature-item">
					<div class="feature-icon">
						<i class="fas fa-shield-alt"></i>
					</div>
					<h4 class="feature-title">Secure Payments</h4>
					<p class="feature-desc">Safe transaction processing</p>
				</div>
			</div>
		</div>

		<div class="why-choose-us fade-in">
			<h2 class="section-title">Why Choose GreenMarket?</h2>
			<div class="benefits-list">
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fas fa-check-circle"></i>
					</div>
					<div class="benefit-text">
						<h4 class="benefit-title">Fresh & Local</h4>
						<p class="benefit-desc">Direct from home gardens</p>
					</div>
				</div>
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fas fa-check-circle"></i>
					</div>
					<div class="benefit-text">
						<h4 class="benefit-title">Fair Pricing</h4>
						<p class="benefit-desc">Transparent pricing</p>
					</div>
				</div>
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fas fa-check-circle"></i>
					</div>
					<div class="benefit-text">
						<h4 class="benefit-title">Easy Process</h4>
						<p class="benefit-desc">Simple steps for all</p>
					</div>
				</div>
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fas fa-check-circle"></i>
					</div>
					<div class="benefit-text">
						<h4 class="benefit-title">Community Focus</h4>
						<p class="benefit-desc">Supporting local farmers</p>
					</div>
				</div>
			</div>
		</div>

		<div class="help-section fade-in">
			<h2 class="help-title">Need Help Getting Started?</h2>
			<p class="help-content">
				Our support team is here to help you! Contact us for assistance with registration or using the platform.
			</p>
			<div class="help-buttons">
				<a href="{{ route('contact.form') }}" class="help-btn btn-primary">
					<i class="fas fa-headset"></i> Contact Support
				</a>
				<a href="{{ route('buyer.register') }}" class="help-btn btn-secondary">
					<i class="fas fa-user-plus"></i> Register
				</a>
				<a href="{{ route('login') }}" class="help-btn btn-secondary">
					<i class="fas fa-sign-in-alt"></i> Login
				</a>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const statsNumbers = document.querySelectorAll('.stat-number');
		statsNumbers.forEach(stat => {
			const text = stat.textContent;
			const number = parseInt(text);
			if (!isNaN(number) && number > 0) {
				animateCounter(stat, number);
			}
		});

		function animateCounter(element, finalNumber) {
			let currentNumber = 0;
			const increment = finalNumber / 25;
			const timer = setInterval(() => {
				currentNumber += increment;
				if (currentNumber >= finalNumber) {
					currentNumber = finalNumber;
					clearInterval(timer);
				}
				element.textContent = Math.floor(currentNumber) + '+';
			}, 30);
		}

		const registerBtn = document.querySelector('.help-buttons a[href*="register"]');
		if (registerBtn) {
			registerBtn.addEventListener('click', function(e) {
				e.preventDefault();
				Swal.fire({
					title: 'Ready to Join?',
					html: `
						<div style="text-align: center; padding: 8px;">
							<i class="fas fa-user-plus" style="font-size: 1.8rem; color: #10B981; margin-bottom: 8px;"></i>
							<h3 style="color: #0f1724; margin-bottom: 6px; font-size: 1rem;">Choose Your Role</h3>
							<p style="color: #6b7280; margin-bottom: 10px; font-size: 0.85rem;">Select how you want to use GreenMarket</p>
							<div style="display: flex; flex-direction: column; gap: 6px; margin-top: 10px;">
								<button onclick="window.location.href='{{ route('buyer.register') }}'" style="background: linear-gradient(135deg, #10B981, #059669); color: white; border: none; padding: 9px 14px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px; font-size: 0.85rem;">
									<i class="fas fa-shopping-cart"></i> As Buyer
								</button>
								<button onclick="showFarmerInfo()" style="background: #ffffff; color: #0f1724; border: 1px solid #10B981; padding: 9px 14px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px; font-size: 0.85rem;">
									<i class="fas fa-seedling"></i> As Farmer
								</button>
							</div>
						</div>
					`,
					showConfirmButton: false,
					showCloseButton: true,
					background: '#ffffff',
					color: '#0f1724',
					width: '300px'
				});
			});
		}

		window.showFarmerInfo = function() {
			Swal.fire({
				title: 'Farmer Registration',
				html: `
					<div style="text-align: left; padding: 6px;">
						<i class="fas fa-seedling" style="font-size: 1.6rem; color: #10B981; margin-bottom: 6px; display: block; text-align: center;"></i>
						<h3 style="color: #0f1724; margin-bottom: 6px; text-align: center; font-size: 1rem;">How Farmers Register</h3>
						<div style="background: rgba(16, 185, 129, 0.1); padding: 8px; border-radius: 6px; margin-bottom: 6px;">
							<p style="color: #0f1724; margin-bottom: 3px; font-size: 0.8rem;"><strong>Step 1:</strong> Contact your area's Lead Farmer</p>
							<p style="color: #0f1724; margin-bottom: 3px; font-size: 0.8rem;"><strong>Step 2:</strong> Provide product details</p>
							<p style="color: #0f1724; font-size: 0.8rem;"><strong>Step 3:</strong> Lead Farmer registers you</p>
						</div>
						<p style="color: #6b7280; font-size: 0.7rem; text-align: center;">Don't know your Lead Farmer? Contact Grama Sevakar.</p>
					</div>
				`,
				confirmButtonText: 'Got It',
				confirmButtonColor: '#10B981',
				background: '#ffffff',
				color: '#0f1724',
				width: '280px'
			});
		};

		document.querySelectorAll('.role-image').forEach(img => {
			img.addEventListener('error', function() {
				this.style.display = 'none';
				const container = this.closest('.role-image-container');
				if (container) {
					const caption = container.querySelector('.image-caption');
					if (caption) {
						caption.textContent = 'Image not available';
						caption.style.background = 'rgba(239, 68, 68, 0.05)';
						caption.style.color = '#ef4444';
					}
				}
			});

			img.addEventListener('load', function() {
				this.style.opacity = '0';
				setTimeout(() => {
					this.style.transition = 'opacity 0.3s ease';
					this.style.opacity = '1';
				}, 10);
			});
		});

		if (window.location.hash === '#register') {
			setTimeout(() => {
				if (registerBtn) {
					registerBtn.click();
				}
			}, 300);
		}

		const observerOptions = {
			root: null,
			rootMargin: '0px',
			threshold: 0.05
		};

		const observer = new IntersectionObserver((entries) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					entry.target.classList.add('fade-in');
				}
			});
		}, observerOptions);

		document.querySelectorAll('.role-card, .stat-card, .feature-item, .benefit-item').forEach(el => {
			observer.observe(el);
		});
	});
</script>
@endsection

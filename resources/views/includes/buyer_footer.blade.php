<div class="footer-wrapper" translate="no">
	<footer class="site-footer">
		<div class="footer-container">
			<div class="footer-row">

				<div class="brand-block">
					<img src="{{ asset('assets/images/Logo Green Market.png') }}" alt="GreenMarket">
					<div class="brand-text">
						<span class="brand-name">GreenMarket</span>
						<span class="brand-copy">&copy; {{ date('Y') }}</span>
					</div>
				</div>

				<div class="footer-actions">
					<a href="{{ route('home') }}"><i class="fas fa-home"></i></a>
					<a href="{{ route('buyer.browseProducts') }}"><i class="fas fa-store"></i></a>
					<a href="{{ route('buyer.cart') }}"><i class="fas fa-shopping-cart"></i></a>
					<a href="{{ route('buyer.wishlist') }}"><i class="fas fa-heart"></i></a>
					<a href="{{ route('buyer.history') }}"><i class="fas fa-clock"></i></a>
					<a href="{{ route('buyer.productRequests.my') }}"><i class="fas fa-list"></i></a>
					
					
					<a href="{{ route('logout.confirmation') }}">
						<i class="fas fa-sign-out-alt"></i>
					</a>
				</div>

			</div>
		</div>
	</footer>

	<a href="{{ route('buyer.cart') }}" class="float-cart">
		<i class="fas fa-shopping-bag"></i>
		@if(session('cart_count', 0) > 0)
			<span class="cart-count">{{ session('cart_count') }}</span>
		@endif
	</a>

	<button id="scrollTopBtn" class="float-top">
		<i class="fas fa-arrow-up"></i>
	</button>
</div>

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
		--border: #e5e7eb;
		--shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
		--shadow-md: 0 10px 25px rgba(15,23,36,0.08);
	}

	.footer-wrapper {
		background: transparent;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	}

	.site-footer {
		margin: 16px;
		border-radius: 14px;
		background: rgba(255,255,255,0.7);
		backdrop-filter: blur(12px);
		border: 1px solid var(--border);
		box-shadow: var(--shadow-sm);
		padding: 10px 14px;
		animation: fadeInUp 0.4s ease;
	}

	.footer-container {
		max-width: 1400px;
		margin: 0 auto;
	}

	.footer-row {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 10px;
	}

	.brand-block {
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.brand-block img {
		height: 26px;
		transition: 0.3s;
	}

	.brand-block:hover img {
		transform: rotate(-5deg) scale(1.08);
	}

	.brand-text {
		display: flex;
		flex-direction: column;
		line-height: 1.1;
	}

	.brand-name {
		font-size: 12px;
		font-weight: 600;
		color: var(--text-color);
	}

	.brand-copy {
		font-size: 10px;
		color: var(--muted);
	}

	.footer-actions {
		display: flex;
		align-items: center;
		gap: 6px;
	}

	.footer-actions a {
		width: 32px;
		height: 32px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 8px;
		color: var(--muted);
		background: transparent;
		border: 1px solid transparent;
		transition: all 0.25s ease;
		cursor: pointer;
	}

	.footer-actions a i {
		color: var(--dark-green);
	}

	.footer-actions a:hover i {
		color: white;
	}

	.footer-actions a:hover {
		background: var(--primary-green);
		color: white;
		transform: translateY(-2px) scale(1.05);
		box-shadow: var(--shadow-md);
	}

	.float-cart,
	.float-top {
		position: fixed;
		right: 10px;
		width: 30px;
		height: 30px;
		border-radius: 50%;
		background: transparent;
		border: 1px solid var(--muted);
		display: flex;
		align-items: center;
		justify-content: center;
		box-shadow: var(--shadow-sm);
		transition: all 0.3s ease;
		z-index: 999;
	}

	.float-cart i {
		color: var(--dark-green);
	}

	.float-cart:hover i {
		color: white;
	}

	.float-cart {
		bottom: 60px;
	}

	.float-top {
		bottom: 20px;
		display: none;
		color: var(--dark-green);
	}

	.float-top.visible {
		display: flex;
		animation: fadeInUp 0.3s ease;
	}

	.float-cart:hover,
	.float-top:hover {
		background: var(--primary-green);
		color: #fff;
		transform: translateY(-3px) scale(1.08);
		box-shadow: var(--shadow-md);
	}

	.cart-count {
		position: absolute;
		top: -6px;
		right: -6px;
		background: var(--primary-green);
		color: #fff;
		font-size: 9px;
		padding: 2px 5px;
		border-radius: 20px;
	}

	@keyframes fadeInUp {
		from {
			opacity: 0;
			transform: translateY(12px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	@media (max-width: 992px) {
		.footer-row {
			flex-direction: column;
			gap: 10px;
		}
	}

	@media (max-width: 576px) {
		.footer-actions a {
			width: 28px;
			height: 28px;
		}
		.brand-name { font-size: 11px; }
		.brand-copy { font-size: 9px; }
	}

	@media (max-width: 380px) {
		.footer-actions {
			gap: 4px;
		}
	}
</style>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const scrollTopBtn = document.getElementById('scrollTopBtn');

		function toggleScrollBtn() {
			if (window.scrollY > 250) {
				scrollTopBtn.classList.add('visible');
			} else {
				scrollTopBtn.classList.remove('visible');
			}
		}

		window.addEventListener('scroll', toggleScrollBtn);
		toggleScrollBtn();

		scrollTopBtn.addEventListener('click', function() {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		});


	});
</script>
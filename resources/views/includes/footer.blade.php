@php
    use Illuminate\Support\Facades\DB;

    $footerData = DB::table('system_config')
        ->where('config_group', 'footer')
        ->where('is_public', true)
        ->get()
        ->keyBy('config_key');

    $copyright = $footerData['footer_copyright']->config_value ?? '© ' . date('Y') . ' GreenMarket. All Rights Reserved.';
    $smallPara = $footerData['footer_small_para']->config_value ?? '';
    $contactNo = $footerData['footer_contact_no']->config_value ?? '+94 112 345 678';
    $email = $footerData['footer_email']->config_value ?? 'info@smartagri.lk';
    $address = $footerData['footer_address']->config_value ?? 'Colombo, Sri Lanka';
    $faxNo = $footerData['footer_fax_no']->config_value ?? '';

    $socialLinks = [
        'facebook' => $footerData['footer_facebook']->config_value ?? '#',
        'youtube' => $footerData['footer_youtube']->config_value ?? '#',
        'twitter' => $footerData['footer_twitter']->config_value ?? '#',
        'blogspot' => $footerData['footer_blogspot']->config_value ?? '#'
    ];

    $privacyPolicy = $footerData['footer_privacy_policy']->config_value ?? '#';
    $termsOfService = $footerData['footer_terms_of_service']->config_value ?? '#';
@endphp

<footer class="site-footer">
	<div class="footer-main">
		<div class="container">
			<div class="footer-grid">
				<div class="footer-column brand-column">
					<div class="brand-wrapper">
						<a href="{{ url('/') }}" class="logo-wrapper">
							<img src="{{ asset('assets/images/logo-4.png') }}" alt="GreenMarket" class="logo-img" oncontextmenu="return false;">
							<div class="logo-text">
								<h3>GreenMarket</h3>
								<p>Fresh & Simple</p>
							</div>
						</a>

						@if($smallPara)
						<p class="brand-description">{{ $smallPara }}</p>
						@endif

						<div class="social-wrapper">
							<a href="{{ $socialLinks['facebook'] }}" target="_blank" class="social-icon facebook" data-platform="Facebook">
								<i class="fab fa-facebook-f"></i>
								<span class="tooltip">Facebook</span>
							</a>
							<a href="{{ $socialLinks['youtube'] }}" target="_blank" class="social-icon youtube" data-platform="YouTube">
								<i class="fab fa-youtube"></i>
								<span class="tooltip">YouTube</span>
							</a>
							<a href="{{ $socialLinks['twitter'] }}" target="_blank" class="social-icon twitter" data-platform="Twitter">
								<i class="fab fa-twitter"></i>
								<span class="tooltip">Twitter</span>
							</a>
							<a href="{{ $socialLinks['blogspot'] }}" target="_blank" class="social-icon blog" data-platform="Blog">
								<i class="fas fa-blog"></i>
								<span class="tooltip">Blog</span>
							</a>
						</div>
					</div>
				</div>

				<div class="footer-column">
					<h4 class="section">
						<i class="fas fa-bolt"></i>
						Quick Links
					</h4>
					<ul class="links-list">
						<li>
							<a href="{{ url('/') }}" class="nav-item">
								<i class="fas fa-home"></i>
								<span>Home</span>
								<i class="fas fa-chevron-right arrow"></i>
							</a>
						</li>
						<li>
							<a href="{{ url('/about-us') }}" class="nav-item">
								<i class="fas fa-info-circle"></i>
								<span>About Us</span>
								<i class="fas fa-chevron-right arrow"></i>
							</a>
						</li>
						<li>
							<a href="{{ url('/how-it-works') }}" class="nav-item">
								<i class="fas fa-cogs"></i>
								<span>How It Works</span>
								<i class="fas fa-chevron-right arrow"></i>
							</a>
						</li>
						<li>
							<a href="{{ url('/contact-us') }}" class="nav-item">
								<i class="fas fa-envelope"></i>
								<span>Contact</span>
								<i class="fas fa-chevron-right arrow"></i>
							</a>
						</li>
						<li>
							<a href="{{ url('/register/buyer') }}" class="nav-item">
								<i class="fas fa-user-plus"></i>
								<span>Register</span>
								<i class="fas fa-chevron-right arrow"></i>
							</a>
						</li>
						<li>
							<a href="{{ url('/login') }}" class="nav-item">
								<i class="fas fa-sign-in-alt"></i>
								<span>Login</span>
								<i class="fas fa-chevron-right arrow"></i>
							</a>
						</li>
					</ul>
				</div>

				<div class="footer-column">
					<h4 class="section">
						<i class="fas fa-address-book"></i>
						Contact Info
					</h4>
					<div class="contact-wrapper">
						<div class="contact-item">
							<div class="contact-icon">
								<i class="fas fa-envelope"></i>
							</div>
							<div class="contact-details">
								<span class="label">Email</span>
								<a href="mailto:{{ $email }}" class="value">{{ $email }}</a>
							</div>
						</div>
						<div class="contact-item">
							<div class="contact-icon">
								<i class="fas fa-phone"></i>
							</div>
							<div class="contact-details">
								<span class="label">Phone</span>
								<a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactNo) }}" class="value">{{ $contactNo }}</a>
							</div>
						</div>
						@if($faxNo)
						<div class="contact-item">
							<div class="contact-icon">
								<i class="fas fa-fax"></i>
							</div>
							<div class="contact-details">
								<span class="label">Fax</span>
								<span class="value">{{ $faxNo }}</span>
							</div>
						</div>
						@endif
						<div class="contact-item">
							<div class="contact-icon">
								<i class="fas fa-map-marker-alt"></i>
							</div>
							<div class="contact-details">
								<span class="label">Address</span>
								<span class="value address">{{$address}}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="container">
			<div class="bottom-wrapper">
				<div class="copyright">
					{!! $copyright !!}
				</div>
				<div class="legal-links">
					<a href="{{ asset('uploads/Legal Documents/' . $privacyPolicy) }}" class="legal-item" data-type="privacy">Privacy Policy</a>
					<span class="divider">|</span>
					<a href="{{ asset('uploads/Legal Documents/' . $termsOfService) }}" class="legal-item" data-type="terms">Terms of Service</a>
					<span class="divider">|</span>
					<span class="version">v1.0.0</span>
				</div>
			</div>
		</div>
	</div>

	<button class="back-to-top" id="backToTop">
		<i class="fas fa-arrow-up"></i>
	</button>
</footer>

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
	--border-radius: 10px;
}

.site-footer {
	background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
	border-top: 1px solid #e2e8f0;
	position: relative;
	font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
	overflow: hidden;
}

.site-footer::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 3px;
	background: linear-gradient(90deg, var(--primary-green), var(--blue), var(--purple));
	opacity: 0.8;
}

.footer-main {
	padding: 50px 0 40px;
}

.container {
	max-width: 1200px;
	margin: 0 auto;
	padding: 0 20px;
}

.footer-grid {
	display: grid;
	grid-template-columns: 1.2fr 1fr 1fr;
	gap: 40px;
}

.brand-column {
	padding-right: 30px;
	border-right: 1px solid #e2e8f0;
}

.brand-wrapper {
	display: flex;
	flex-direction: column;
	gap: 20px;
}

.logo-wrapper {
	display: flex;
	align-items: center;
	gap: 15px;
	text-decoration: none;
	padding: 12px;
	border-radius: var(--border-radius);
	background: rgba(16, 185, 129, 0.05);
	border: 1px solid rgba(16, 185, 129, 0.1);
	transition: var(--transition);
}

.logo-wrapper:hover {
	transform: translateY(-3px);
	background: rgba(16, 185, 129, 0.1);
	border-color: var(--primary-green);
	box-shadow: var(--shadow-md);
}

.logo-img {
	width: 100px;
	height: 100px;
	object-fit: contain;
	transition: var(--transition);
	filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.logo-wrapper:hover .logo-img {
	transform: rotate(360deg) scale(1.1);
}

.logo-text h3 {
	font-size: 1.4rem;
	font-weight: 700;
	background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
	margin: 0 0 4px 0;
	letter-spacing: -0.5px;
}

.logo-text p {
	font-size: 0.85rem;
	color: var(--muted);
	margin: 0;
	font-weight: 500;
}

.brand-description {
	font-size: 0.9rem;
	line-height: 1.6;
	color: var(--text-color);
	margin: 0;
	padding: 15px 0;
	border-top: 1px solid #e2e8f0;
	border-bottom: 1px solid #e2e8f0;
}

.social-wrapper {
	display: flex;
	gap: 12px;
}

.social-icon {
	width: 42px;
	height: 42px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 1rem;
	text-decoration: none;
	transition: var(--transition);
	position: relative;
	overflow: hidden;
}

.social-icon::before {
	content: '';
	position: absolute;
	top: 0;
	left: -100%;
	width: 100%;
	height: 100%;
	background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
	transition: 0.5s;
}

.social-icon:hover::before {
	left: 100%;
}

.social-icon.facebook { background: #1877f2; }
.social-icon.youtube { background: #ff0000; }
.social-icon.twitter { background: #1da1f2; }
.social-icon.blog { background: #f57d00; }

.social-icon:hover {
	transform: translateY(-4px) scale(1.1);
	box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

.tooltip {
	position: absolute;
	bottom: 100%;
	left: 50%;
	transform: translateX(-50%) translateY(5px);
	background: var(--text-color);
	color: white;
	padding: 6px 12px;
	border-radius: 6px;
	font-size: 0.75rem;
	white-space: nowrap;
	opacity: 0;
	visibility: hidden;
	transition: var(--transition);
	pointer-events: none;
	z-index: 100;
}

.social-icon:hover .tooltip {
	opacity: 1;
	visibility: visible;
	transform: translateX(-50%) translateY(0);
}

.section {
	font-size: 1.1rem;
	font-weight: 600;
	color: var(--text-color);
	margin: 0 0 25px 0;
	display: flex;
	align-items: center;
	gap: 10px;
	position: relative;
	padding-bottom: 10px;
}

.section::after {
	content: '';
	position: absolute;
	bottom: 0;
	left: 0;
	width: 40px;
	height: 3px;
	background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
	border-radius: 2px;
}

.section i {
	color: var(--primary-green);
	font-size: 0.9rem;
}

.links-list {
	list-style: none;
	padding: 0;
	margin: 0;
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.nav-item {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 12px 16px;
	background: white;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	text-decoration: none;
	color: var(--text-color);
	font-size: 0.9rem;
	transition: var(--transition);
	position: relative;
	overflow: hidden;
}

.nav-item::before {
	content: '';
	position: absolute;
	left: 0;
	top: 0;
	height: 100%;
	width: 4px;
	background: var(--primary-green);
	transform: translateX(-100%);
	transition: transform 0.3s ease;
}

.nav-item:hover::before {
	transform: translateX(0);
}

.nav-item:hover {
	background: linear-gradient(90deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.04) 100%);
	border-color: var(--primary-green);
	transform: translateX(8px);
}

.nav-item i:first-child {
	color: var(--primary-green);
	font-size: 0.9rem;
	width: 20px;
	text-align: center;
	transition: var(--transition);
}

.nav-item:hover i:first-child {
	transform: scale(1.2);
	color: var(--dark-green);
}

.nav-item span {
	flex: 1;
}

.arrow {
	color: var(--muted);
	font-size: 0.7rem;
	transition: var(--transition);
	opacity: 0;
}

.nav-item:hover .arrow {
	opacity: 1;
	transform: translateX(5px);
}

.contact-wrapper {
	display: flex;
	flex-direction: column;
	gap: 15px;
}

.contact-item {
	display: flex;
	align-items: flex-start;
	gap: 15px;
	padding: 15px;
	background: white;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	transition: var(--transition);
}

.contact-item:hover {
	transform: translateY(-3px);
	border-color: var(--primary-green);
	box-shadow: var(--shadow-md);
	background: rgba(16, 185, 129, 0.05);
}

.contact-icon {
	width: 36px;
	height: 36px;
	min-width: 36px;
	background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
	border-radius: 8px;
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 0.9rem;
	transition: var(--transition);
}

.contact-item:hover .contact-icon {
	transform: rotate(15deg) scale(1.1);
}

.contact-details {
	flex: 1;
	min-width: 0;
}

.label {
	display: block;
	font-size: 0.75rem;
	color: var(--muted);
	text-transform: uppercase;
	letter-spacing: 0.5px;
	margin-bottom: 4px;
	font-weight: 600;
}

.value {
	display: block;
	font-size: 0.9rem;
	color: var(--text-color);
	text-decoration: none;
	transition: var(--transition);
	word-break: break-word;
	line-height: 1.4;
}

.value:hover {
	color: var(--primary-green);
	text-decoration: underline;
}

.address {
	white-space: pre-line;
	font-size: 0.85rem;
	line-height: 1.5;
}

.footer-bottom {
	padding: 20px 0;
	background: var(--text-color);
	color: white;
	border-top: 1px solid rgba(255,255,255,0.1);
}

.bottom-wrapper {
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-wrap: wrap;
	gap: 15px;
}

.copyright {
	font-size: 0.85rem;
	color: #cbd5e1;
}

.legal-links {
	display: flex;
	align-items: center;
	gap: 15px;
}

.legal-item {
	color: #cbd5e1;
	text-decoration: none;
	font-size: 0.85rem;
	transition: var(--transition);
	padding: 6px 12px;
	border-radius: 6px;
	position: relative;
	overflow: hidden;
}

.legal-item::before {
	content: '';
	position: absolute;
	top: 0;
	left: -100%;
	width: 100%;
	height: 100%;
	background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
	transition: 0.5s;
}

.legal-item:hover::before {
	left: 100%;
}

.legal-item:hover {
	color: white;
	background: rgba(255, 255, 255, 0.1);
}

.divider {
	color: #6b7280;
	font-size: 0.85rem;
}

.version {
	font-size: 0.8rem;
	font-weight: 600;
	color: var(--primary-green);
	padding: 4px 10px;
	background: rgba(16, 185, 129, 0.15);
	border-radius: 12px;
	border: 1px solid rgba(16, 185, 129, 0.3);
}

.back-to-top {
	position: fixed;
	bottom: 25px;
	right: 25px;
	width: 48px;
	height: 48px;
	background: linear-gradient(135deg, rgba(16, 185, 129, 0.8), rgba(5, 150, 105, 0.8));
	border: none;
	border-radius: 50px;
	color: white;
	cursor: pointer;
	transition: var(--transition);
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.1rem;
	z-index: 1000;
	opacity: 0;
	visibility: hidden;
	transform: translateY(20px) scale(0.9);
	box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.back-to-top.visible {
	opacity: 1;
	visibility: visible;
	transform: translateY(0) scale(1);
}

.back-to-top:hover {
	transform: translateY(-5px) scale(1.05);
	box-shadow: 0 10px 30px rgba(16, 185, 129, 0.6);
}

@media screen and (max-width: 1024px) {
	.footer-grid {
		grid-template-columns: 1fr 1fr;
		gap: 30px;
	}

	.brand-column {
		grid-column: 1 / -1;
		border-right: none;
		border-bottom: 1px solid #e2e8f0;
		padding-right: 0;
		padding-bottom: 30px;
		margin-bottom: 10px;
	}

	.logo-wrapper {
		justify-content: center;
	}

	.social-wrapper {
		justify-content: center;
	}

	.footer-main {
		padding: 40px 0 30px;
	}

	.section {
		font-size: 1rem;
	}

	.nav-item {
		padding: 10px 14px;
		font-size: 0.85rem;
	}

	.contact-item {
		padding: 12px;
	}

	.back-to-top {
		width: 44px;
		height: 44px;
		font-size: 1rem;
	}
}

@media screen and (max-width: 768px) {
	.footer-grid {
		grid-template-columns: 1fr;
		gap: 25px;
	}

	.brand-column {
		grid-column: 1;
		padding-bottom: 25px;
		margin-bottom: 0;
	}

	.logo-text h3 {
		font-size: 1.2rem;
	}

	.brand-description {
		font-size: 0.85rem;
	}

	.social-icon {
		width: 38px;
		height: 38px;
		font-size: 0.9rem;
	}

	.links-list {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 10px;
	}

	.nav-item {
		flex-direction: column;
		text-align: center;
		gap: 8px;
		min-height: 80px;
		justify-content: center;
	}

	.nav-item span {
		font-size: 0.8rem;
	}

	.arrow {
		display: none;
	}

	.contact-wrapper {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 12px;
	}

	.contact-item {
		flex-direction: column;
		text-align: center;
		align-items: center;
		padding: 15px 12px;
		min-height: 100px;
	}

	.contact-details {
		text-align: center;
	}

	.label {
		font-size: 0.7rem;
	}

	.value {
		font-size: 0.85rem;
	}

	.address {
		font-size: 0.8rem;
	}

	.bottom-wrapper {
		flex-direction: column;
		text-align: center;
		gap: 12px;
	}

	.legal-links {
		flex-wrap: wrap;
		justify-content: center;
	}

	.back-to-top {
		bottom: 20px;
		right: 20px;
		width: 40px;
		height: 40px;
		font-size: 0.9rem;
	}
}

@media screen and (max-width: 576px) {
	.container {
		padding: 0 15px;
	}

	.footer-main {
		padding: 30px 0 25px;
	}

	.footer-grid {
		gap: 20px;
	}

	.logo-img {
		width: 80px;
		height: 80px;
	}

	.logo-wrapper {
		padding: 10px;
	}

	.links-list {
		grid-template-columns: 1fr;
	}

	.nav-item {
		flex-direction: row;
		text-align: left;
		min-height: auto;
	}

	.contact-wrapper {
		grid-template-columns: 1fr;
	}

	.contact-item {
		flex-direction: row;
		text-align: left;
		align-items: flex-start;
		min-height: auto;
	}

	.contact-details {
		text-align: left;
	}

	.legal-item {
		padding: 4px 8px;
		font-size: 0.8rem;
	}

	.copyright {
		font-size: 0.8rem;
	}

	.back-to-top {
		bottom: 15px;
		right: 15px;
		width: 36px;
		height: 36px;
		font-size: 0.85rem;
	}
}

@media screen and (max-width: 480px) {
	.logo-text h3 {
		font-size: 1.1rem;
	}

	.brand-description {
		font-size: 0.8rem;
		padding: 12px 0;
	}

	.social-icon {
		width: 34px;
		height: 34px;
	}

	.nav-item {
		padding: 10px 12px;
	}

	.contact-item {
		padding: 12px 10px;
	}

	.footer-bottom {
		padding: 15px 0;
	}
}

@media (prefers-reduced-motion: reduce) {
	.back-to-top,
	.logo-wrapper,
	.nav-item,
	.social-icon,
	.contact-item,
	.legal-item {
		transition: none !important;
	}

	.back-to-top:hover,
	.logo-wrapper:hover,
	.nav-item:hover,
	.social-icon:hover,
	.contact-item:hover {
		transform: none !important;
	}

	.social-icon::before,
	.legal-item::before,
	.nav-item::before {
		display: none !important;
	}
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const backToTop = document.getElementById('backToTop');

	function handleScroll() {
		if (window.pageYOffset > 300) {
			backToTop.classList.add('visible');
		} else {
			backToTop.classList.remove('visible');
		}
	}

	window.addEventListener('scroll', handleScroll);
	handleScroll();

	backToTop.addEventListener('click', function(e) {
		e.preventDefault();
		window.scrollTo({
			top: 0,
			behavior: 'smooth'
		});
	});

	const socialIcons = document.querySelectorAll('.social-icon');
	socialIcons.forEach(icon => {
		icon.addEventListener('click', function(e) {
			const href = this.getAttribute('href');
			const platform = this.dataset.platform || 'Social Media';

			if (href === '#') {
				e.preventDefault();
				if (typeof Swal !== 'undefined') {
					Swal.fire({
						title: `${platform}`,
						text: `Follow us on ${platform} for fresh updates and news!`,
						icon: 'info',
						confirmButtonColor: '#10B981',
						confirmButtonText: 'OK',
						background: '#f8fafc',
						color: '#0f1724',
						showClass: {
							popup: 'animate__animated animate__fadeInUp'
						}
					});
				} else {
					alert(`Follow us on ${platform} for updates!`);
				}
			} else if (!href.startsWith('http')) {
				e.preventDefault();
				if (typeof Swal !== 'undefined') {
					Swal.fire({
						title: 'Invalid Link',
						text: 'This social media link needs to be configured properly.',
						icon: 'error',
						confirmButtonColor: '#ef4444'
					});
				}
			}
		});
	});

	const legalItems = document.querySelectorAll('.legal-item');
	legalItems.forEach(item => {
		item.addEventListener('click', function(e) {
			const href = this.getAttribute('href');
			const linkText = this.textContent;
			const linkType = this.dataset.type;

			if (href === '#') {
				e.preventDefault();
				if (typeof Swal !== 'undefined') {
					Swal.fire({
						title: `${linkText}`,
						text: `Our ${linkText} is currently being updated. Please check back soon!`,
						icon: 'info',
						confirmButtonColor: '#10B981'
					});
				} else {
					alert(`${linkText} coming soon!`);
				}
			} else if (href.includes('.pdf')) {
				e.preventDefault();
				if (typeof Swal !== 'undefined') {
					Swal.fire({
						title: 'Download',
						text: `Would you like to download ${linkText}?`,
						icon: 'question',
						showCancelButton: true,
						confirmButtonColor: '#10B981',
						cancelButtonColor: '#6b7280',
						confirmButtonText: 'Download',
						cancelButtonText: 'Cancel'
					}).then((result) => {
						if (result.isConfirmed) {
							window.open(href, '_blank');
						}
					});
				} else {
					if (confirm(`Download ${linkText}?`)) {
						window.open(href, '_blank');
					}
				}
			}
		});
	});

	function adjustLayout() {
		const width = window.innerWidth;
		const linksList = document.querySelector('.links-list');
		const contactWrapper = document.querySelector('.contact-wrapper');

		if (width <= 576) {
			if (linksList) linksList.style.gridTemplateColumns = '1fr';
			if (contactWrapper) contactWrapper.style.gridTemplateColumns = '1fr';
		} else if (width <= 768) {
			if (linksList) linksList.style.gridTemplateColumns = 'repeat(2, 1fr)';
			if (contactWrapper) contactWrapper.style.gridTemplateColumns = 'repeat(2, 1fr)';
		} else {
			if (linksList) {
				linksList.style.gridTemplateColumns = '';
				linksList.style.display = 'flex';
				linksList.style.flexDirection = 'column';
			}
			if (contactWrapper) {
				contactWrapper.style.gridTemplateColumns = '';
				contactWrapper.style.display = 'flex';
				contactWrapper.style.flexDirection = 'column';
			}
		}
	}

	window.addEventListener('resize', adjustLayout);
	adjustLayout();

	setTimeout(() => {
		if (typeof Swal === 'undefined') {
			console.warn('SweetAlert2 not loaded - using fallback alerts');

			document.querySelectorAll('.social-icon[href="#"], .legal-item[href="#"]').forEach(link => {
				link.addEventListener('click', function(e) {
					e.preventDefault();
					const text = this.classList.contains('social-icon') ?
						'Social media page coming soon!' :
						'Document page coming soon!';
					alert(text);
				});
			});
		}
	}, 100);
});
</script>

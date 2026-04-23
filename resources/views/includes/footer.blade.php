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
				<div class="footer-col brand-col">
					<div class="brand-card">
						<div class="brand-header">
							<a href="{{ url('/') }}" class="brand-link">
								<img src="{{ asset('assets/images/Logo Green Market.png') }}" alt="GreenMarket" class="brand-logo" oncontextmenu="return false;">
								<div class="brand-info" translate="no">
									<h3>GreenMarket</h3>
									<span>Fresh & Simple</span>
								</div>
							</a>
						</div>
						@if($smallPara)
						<p class="brand-desc">{{ $smallPara }}</p>
						@endif
						<div class="social-wrapper">
							<div class="social-row">
								<a href="{{ $socialLinks['facebook'] }}" class="social-btn facebook" data-platform="Facebook">
									<i class="fab fa-facebook-f"></i>
								</a>
								<a href="{{ $socialLinks['youtube'] }}" class="social-btn youtube" data-platform="YouTube">
									<i class="fab fa-youtube"></i>
								</a>
								<a href="{{ $socialLinks['twitter'] }}" class="social-btn twitter" data-platform="Twitter">
									<i class="fab fa-twitter"></i>
								</a>
								<a href="{{ $socialLinks['blogspot'] }}" class="social-btn blog" data-platform="Blog">
									<i class="fas fa-blog"></i>
								</a>
							</div>
						</div>
						<div class="partner-strip">
							<div class="partner-item">
								<a href="https://csiap.lk/" target="_blank">
									<img src="{{ asset('assets/images/CSIAP Logo.png') }}" alt="CSIAP" class="partner-img vertical">
								</a>
							</div>
							<div class="partner-item">
								<a href="https://doa.gov.lk/home-page/" target="_blank">
									<img src="{{ asset('assets/images/Sri Lank logo.png') }}" alt="Sri Lanka" class="partner-img vertical">
								</a>
							</div>
							<div class="partner-item">
								<a href="https://www.worldbank.org/" target="_blank">
									<img src="{{ asset('assets/images/world bank logo.png') }}" alt="World Bank" class="partner-img horizontal">
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="footer-col" translate="no">
					<div class="section-header1">
						<i class="fas fa-link"></i>
						<h4>Quick Links</h4>
					</div>
					<ul class="footer-links">
						<li><a href="{{ url('/') }}"><i class="fas fa-home"></i><span>Home</span></a></li>
						<li><a href="{{ url('/about-us') }}"><i class="fas fa-info-circle"></i><span>About Us</span></a></li>
						<li><a href="{{ url('/contact-us') }}"><i class="fas fa-envelope"></i><span>Contact</span></a></li>
						<li><a href="{{ url('/register/buyer') }}"><i class="fas fa-user-plus"></i><span>Register</span></a></li>
						<li><a href="{{ url('/login') }}"><i class="fas fa-sign-in-alt"></i><span>Login</span></a></li>
					</ul>
				</div>
				<div class="footer-col" translate="no">
					<div class="section-header1">
						<i class="fas fa-address-card"></i>
						<h4>Contact Info</h4>
					</div>
					<div class="contact-cards">
						<div class="contact-item">
							<div class="contact-icon"><i class="fas fa-envelope"></i></div>
							<div class="contact-content">
								<span class="contact-label">Email</span>
								<a href="mailto:{{ $email }}" class="contact-value">{{ $email }}</a>
							</div>
						</div>
						<div class="contact-item">
							<div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
							<div class="contact-content">
								<span class="contact-label">Phone</span>
								<a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactNo) }}" class="contact-value">{{ $contactNo }}</a>
							</div>
						</div>
						@if($faxNo)
						<div class="contact-item">
							<div class="contact-icon"><i class="fas fa-fax"></i></div>
							<div class="contact-content">
								<span class="contact-label">Fax</span>
								<fax:+94{{ preg_replace('/^0/', '', preg_replace('/[^0-9]/', '', $faxNo)) }}" class="contact-value">{{ $faxNo }}</a>
							</div>
						</div>
						@endif
						<div class="contact-item">
							<div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
							<div class="contact-content">
								<span class="contact-label">Address</span>
								<a href="https://maps.app.goo.gl/9GdYWPBXh2PafRxP9" class="contact-value address">{{ $address }}</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-bottom" translate="no">
		<div class="container">
			<div class="bottom-content">
				<div class="copyright">{!! $copyright !!}</div>
				<div class="legal-links">
					<a href="{{ asset('uploads/Legal Documents/' . $privacyPolicy) }}" class="legal-link">Privacy</a>
					<span class="separator">•</span>
					<a href="{{ asset('uploads/Legal Documents/' . $termsOfService) }}" class="legal-link">Terms</a>
					<span class="separator">•</span>
					<span class="version">v1.0.0</span>
				</div>
			</div>
		</div>
	</div>
	<button class="scroll-top" id="backToTop">
		<i class="fas fa-arrow-up"></i>
	</button>
</footer>

<link rel="stylesheet" href="{{ asset('css/footer.css') }}">

<script>
document.addEventListener('DOMContentLoaded', function() {
	const backToTop = document.getElementById('backToTop');
	const scrollHandler = () => {
		backToTop.classList.toggle('visible', window.pageYOffset > 300);
	};
	window.addEventListener('scroll', scrollHandler);
	scrollHandler();
	backToTop.addEventListener('click', (e) => {
		e.preventDefault();
		window.scrollTo({ top: 0, behavior: 'smooth' });
	});
	const socialBtns = document.querySelectorAll('.social-btn');
	socialBtns.forEach(btn => {
		btn.addEventListener('click', function(e) {
			const href = this.getAttribute('href');
			const platform = this.dataset.platform;
			if (href === '#') {
				e.preventDefault();
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/info1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/info1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'info' @endif,
					title: platform,
					text: `Follow us on ${platform} for updates!`,
					confirmButtonColor: '#10B981',
					timer: 2000,
					showConfirmButton: true
				});
			} else if (!href.startsWith('http')) {
				e.preventDefault();
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/error6.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error6.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
					title: 'Invalid Link',
					text: 'This social media link needs configuration.',
					confirmButtonColor: '#ef4444'
				});
			}
		});
	});
	const legalLinks = document.querySelectorAll('.legal-link');
	legalLinks.forEach(link => {
		link.addEventListener('click', function(e) {
			const href = this.getAttribute('href');
			const text = this.textContent;
			if (href === '#') {
				e.preventDefault();
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/info1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/info1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'info' @endif,
					title: text,
					text: `${text} Policy is being updated. Check back soon!`,
					confirmButtonColor: '#10B981'
				});
			} else if (href.includes('.pdf')) {
				e.preventDefault();
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/download confirmation1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/download confirmation1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
					title: 'Download',
					text: `Download ${text} Policy?`,
					showCancelButton: true,
					confirmButtonColor: '#10B981',
					cancelButtonColor: '#6b7280',
					confirmButtonText: 'Download'
				}).then((result) => {
					if (result.isConfirmed) {
						window.open(href, '_blank');
						Swal.fire({
							@if(file_exists(public_path('assets/icons/Gif/success4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
							title: 'Success',
							text: 'Download started!',
							showConfirmButton: false,
							timer: 1500
						});
					}
				});
			}
		});
	});
});
</script>
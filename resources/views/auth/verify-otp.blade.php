<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GreenMarket | OTP Verification</title>
	<link rel="stylesheet" href="{{ asset('css/login.css') }}" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<style>
		.otp-inputs {
			display: flex;
			gap: 10px;
			justify-content: center;
			margin: 20px 0;
		}

		.otp-input {
			width: 45px;
			height: 45px;
			text-align: center;
			font-size: 18px;
			font-weight: bold;
			border: 2px solid #e5e7eb;
			border-radius: 8px;
			background: #f9fafb;
			transition: all 0.3s ease;
		}

		.otp-input:focus {
			outline: none;
			border-color: #10B981;
			box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
			transform: translateY(-2px);
		}

		.timer {
			text-align: center;
			color: #6b7280;
			font-size: 14px;
			margin: 15px 0;
		}

		.timer.expired {
			color: #ef4444;
		}

		.resend-link {
			text-align: center;
			margin-top: 15px;
		}

		.resend-link a {
			color: #3b82f6;
			text-decoration: none;
			font-weight: 600;
			transition: all 0.3s ease;
		}

		.resend-link a:hover {
			color: #10B981;
			text-decoration: underline;
		}

		.resend-link a.disabled {
			color: #9ca3af;
			cursor: not-allowed;
			text-decoration: none;
		}

		.otp-info {
			background: rgba(16, 185, 129, 0.08);
			border: 1px solid rgba(16, 185, 129, 0.2);
			border-radius: 10px;
			padding: 12px;
			margin-bottom: 20px;
			text-align: center;
			font-size: 14px;
		}

		.otp-info i {
			color: #10B981;
			margin-right: 5px;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="login-wrapper">
			<div class="logo-section" onclick="window.location.href='/'" tabindex="0" role="button">
				<div class="logo-container">
					<img src="{{ asset('assets/images/logo-4.png') }}" alt="GreenMarket Logo" class="logo">
					<h1 class="logo-title">GreenMarket</h1>
				</div>
				<p class="logo-tagline">Secure password reset</p>
			</div>

			<div class="login-card">
				<div class="card-header">
					<h2><i class="fas fa-shield-alt"></i> OTP Verification</h2>
					<p>Enter the 6-digit code sent to you</p>
				</div>

				<div class="otp-info">
					<i class="fas fa-info-circle"></i>
					OTP sent to {{ session('reset_username') }}. Valid for 10 minutes.
				</div>

				<form id="otpForm" method="POST" action="{{ route('password.verify.otp.submit') }}" class="login-form">
					@csrf

					<div class="otp-inputs">
						@for($i = 1; $i <= 6; $i++)
						<input type="text"
							   class="otp-input"
							   name="otp{{$i}}"
							   maxlength="1"
							   data-index="{{$i-1}}"
							   oninput="moveToNext(this, event)"
							   onkeydown="moveToPrevious(this, event)"
							   autocomplete="off">
						@endfor
					</div>

					<input type="hidden" name="otp" id="fullOtp">

					<div class="timer" id="timer">
						<i class="fas fa-clock"></i>
						Time remaining: <span id="time">10:00</span>
					</div>

					@if ($errors->any())
						<div style="color: #ef4444; text-align: center; font-size: 14px; margin: 10px 0;">
							{{ $errors->first() }}
						</div>
					@endif

					<button type="submit" class="login-btn" id="verifyBtn">
						<i class="fas fa-check-circle"></i> Verify OTP
					</button>
				</form>

				<div class="resend-link">
					<a href="#" id="resendLink" onclick="resendOTP(event)">Resend OTP</a>
				</div>

				<div class="quick-links">
					<a href="{{ route('login') }}" class="home-link">
						<i class="fas fa-arrow-left"></i> Back to Login
					</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		let timerInterval;
		let totalSeconds = 600;

		function updateTimer() {
			const minutes = Math.floor(totalSeconds / 60);
			const seconds = totalSeconds % 60;

			document.getElementById('time').textContent =
				`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

			if (totalSeconds <= 0) {
				clearInterval(timerInterval);
				document.getElementById('timer').classList.add('expired');
				document.getElementById('verifyBtn').disabled = true;
				Swal.fire({
					icon: 'error',
					title: 'OTP Expired',
					text: 'The OTP has expired. Please request a new one.',
					background: '#ffffff',
					color: '#0f1724',
					confirmButtonColor: '#10B981'
				});
			}

			totalSeconds--;
		}

		function moveToNext(input, event) {
			const value = input.value;

			if (/^\d$/.test(value)) {
				const nextIndex = parseInt(input.dataset.index) + 1;
				const nextInput = document.querySelector(`.otp-input[data-index="${nextIndex}"]`);

				if (nextInput) {
					nextInput.focus();
					nextInput.select();
				} else {
					updateFullOTP();
				}
			} else {
				input.value = '';
			}

			updateFullOTP();
		}

		function moveToPrevious(input, event) {
			if (event.key === 'Backspace' && !input.value) {
				const prevIndex = parseInt(input.dataset.index) - 1;
				const prevInput = document.querySelector(`.otp-input[data-index="${prevIndex}"]`);

				if (prevInput) {
					prevInput.focus();
					prevInput.select();
				}
			}

			setTimeout(() => updateFullOTP(), 10);
		}

		function updateFullOTP() {
			let fullOtp = '';
			const inputs = document.querySelectorAll('.otp-input');

			inputs.forEach(input => {
				fullOtp += input.value;
			});

			document.getElementById('fullOtp').value = fullOtp;

			const verifyBtn = document.getElementById('verifyBtn');
			verifyBtn.disabled = fullOtp.length !== 6;

			if (fullOtp.length === 6) {
				verifyBtn.style.opacity = '1';
			} else {
				verifyBtn.style.opacity = '0.7';
			}
		}

		function resendOTP(e) {
			e.preventDefault();

			const resendLink = document.getElementById('resendLink');
			if (resendLink.classList.contains('disabled')) {
				return;
			}

			Swal.fire({
				title: 'Resend OTP',
				text: 'Send OTP again to your email and mobile?',
				icon: 'question',
				background: '#ffffff',
				color: '#0f1724',
				showCancelButton: true,
				confirmButtonColor: '#10B981',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Yes, resend',
				cancelButtonText: 'Cancel'
			}).then((result) => {
				if (result.isConfirmed) {
					fetch('{{ route("password.forgot") }}', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': '{{ csrf_token() }}'
						},
						body: JSON.stringify({
							username: '{{ session("reset_username") }}',
							send_sms: true
						})
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							resetTimer();
							resendLink.classList.add('disabled');
							resendLink.style.pointerEvents = 'none';

							Swal.fire({
								icon: 'success',
								title: 'OTP Resent!',
								text: 'New OTP has been sent to your registered email and mobile.',
								background: '#ffffff',
								color: '#0f1724',
								confirmButtonColor: '#10B981'
							});

							setTimeout(() => {
								resendLink.classList.remove('disabled');
								resendLink.style.pointerEvents = 'auto';
							}, 30000);
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Failed',
								text: data.message || 'Failed to resend OTP.',
								background: '#ffffff',
								color: '#0f1724',
								confirmButtonColor: '#10B981'
							});
						}
					})
					.catch(error => {
						console.error('Error:', error);
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'An error occurred. Please try again.',
							background: '#ffffff',
							color: '#0f1724',
							confirmButtonColor: '#10B981'
						});
					});
				}
			});
		}

		function resetTimer() {
			clearInterval(timerInterval);
			totalSeconds = 600;
			document.getElementById('timer').classList.remove('expired');
			document.getElementById('verifyBtn').disabled = false;
			updateTimer();
			timerInterval = setInterval(updateTimer, 1000);
		}

		document.addEventListener('DOMContentLoaded', function() {
			document.querySelector('.otp-input[data-index="0"]').focus();
			updateFullOTP();
			timerInterval = setInterval(updateTimer, 1000);

			document.getElementById('otpForm').addEventListener('submit', function(e) {
				const fullOtp = document.getElementById('fullOtp').value;

				if (fullOtp.length !== 6) {
					e.preventDefault();
					Swal.fire({
						icon: 'error',
						title: 'Invalid OTP',
						text: 'Please enter all 6 digits of the OTP.',
						background: '#ffffff',
						color: '#0f1724',
						confirmButtonColor: '#10B981'
					});
					return false;
				}

				if (totalSeconds <= 0) {
					e.preventDefault();
					Swal.fire({
						icon: 'error',
						title: 'OTP Expired',
						text: 'The OTP has expired. Please request a new one.',
						background: '#ffffff',
						color: '#0f1724',
						confirmButtonColor: '#10B981'
					});
					return false;
				}
			});

			document.querySelectorAll('.otp-input').forEach(input => {
				input.addEventListener('paste', function(e) {
					e.preventDefault();
					const pasteData = e.clipboardData.getData('text').trim();

					if (/^\d{6}$/.test(pasteData)) {
						const inputs = document.querySelectorAll('.otp-input');
						inputs.forEach((inputEl, index) => {
							if (index < 6) {
								inputEl.value = pasteData.charAt(index);
							}
						});
						updateFullOTP();
					}
				});
			});
		});
	</script>
</body>
</html>

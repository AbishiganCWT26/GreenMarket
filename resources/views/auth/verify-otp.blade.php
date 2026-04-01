<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GreenMarket | Verify OTP</title>
	<link rel="stylesheet" href="{{ asset('css/login.css') }}" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
	
	<style>
		.otp-boxes {
			display: flex;
			gap: 6px;
			justify-content: center;
			margin: 15px 0 8px;
		}
		.otp-input {
			width: 32px;
			height: 32px;
			text-align: center;
			font-size: 0.9rem;
			font-weight: 600;
			border: 1.5px solid var(--border);
			border-radius: var(--radius-sm);
			background: var(--bg);
			transition: var(--transition);
		}
		.otp-input:focus {
			outline: none;
			border-color: var(--primary);
			box-shadow: 0 0 0 2px rgba(16,185,129,0.1);
			transform: translateY(-1px);
		}
		.otp-timer {
			text-align: center;
			font-size: 0.7rem;
			color: var(--muted);
			margin: 8px 0;
		}
		.otp-timer.expired { color: #ef4444; }
		.otp-resend {
			text-align: center;
			margin: 10px 0;
		}
		.otp-resend a {
			color: var(--blue);
			text-decoration: none;
			font-size: 0.7rem;
			display: inline-flex;
			align-items: center;
			gap: 4px;
		}
		.otp-resend a.disabled {
			color: #9ca3af;
			pointer-events: none;
			cursor: default;
		}
		.otp-info {
			background: rgba(16,185,129,0.05);
			border: 1px solid rgba(16,185,129,0.1);
			border-radius: var(--radius-sm);
			padding: 8px;
			margin-bottom: 12px;
			text-align: center;
			font-size: 0.7rem;
		}
		.otp-info i { color: var(--primary); margin-right: 4px; }
	</style>
</head>
<body>
	<div class="auth-container">
		<div class="auth-card">
			<div class="auth-header" onclick="window.location.href='/'" role="button">
				<img src="{{ asset('assets/images/Logo Green Market.png') }}" alt="GreenMarket" class="auth-logo">
				<div class="auth-brand">
					<h1>GreenMarket</h1>
					<p>secure password reset</p>
				</div>
			</div>

			<div class="auth-body">
				<div class="auth-title">
					<h2><i class="fas fa-shield-alt" style="color: var(--primary);"></i> Verify OTP</h2>
					<p>Enter 6-digit code</p>
				</div>

				<div class="otp-info">
					<i class="fas fa-info-circle"></i>
					OTP sent to {{ session('reset_username') }}
				</div>

				<form id="otpForm" method="POST" action="{{ route('password.verify.otp.submit') }}">
					@csrf

					<div class="otp-boxes">
						@for($i = 1; $i <= 6; $i++)
						<input type="text" class="otp-input" name="otp{{$i}}" maxlength="1" data-index="{{$i-1}}" oninput="moveNext(this, event)" onkeydown="movePrev(this, event)" autocomplete="off">
						@endfor
					</div>

					<input type="hidden" name="otp" id="fullOtp">

					<div class="otp-timer" id="timer">
						<i class="fas fa-clock"></i>
						<span id="time">10:00</span>
					</div>

					<button type="submit" class="auth-btn" id="verifyBtn">
						<i class="fas fa-check-circle"></i> Verify OTP
					</button>
				</form>

				<div class="otp-resend">
					<a href="#" id="resendLink" onclick="resendOTP(event)">Resend OTP</a>
				</div>

				<div class="auth-back">
					<a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Back to Login</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		let timerSeconds = 600;
		let timerInterval;

		function moveNext(input) {
			if (input.value.length === 1) {
				const next = input.parentElement.querySelector(`[data-index="${parseInt(input.dataset.index) + 1}"]`);
				if (next) next.focus();
			}
			updateFullOTP();
		}

		function movePrev(input, e) {
			if (e.key === 'Backspace' && !input.value) {
				const prev = input.parentElement.querySelector(`[data-index="${parseInt(input.dataset.index) - 1}"]`);
				if (prev) prev.focus();
			}
			setTimeout(updateFullOTP, 10);
		}

		function updateFullOTP() {
			let otp = '';
			document.querySelectorAll('.otp-input').forEach(i => otp += i.value);
			document.getElementById('fullOtp').value = otp;
			document.getElementById('verifyBtn').disabled = otp.length !== 6;
		}

		function updateTimer() {
			const mins = Math.floor(timerSeconds / 60);
			const secs = timerSeconds % 60;
			document.getElementById('time').textContent = `${mins.toString().padStart(2,'0')}:${secs.toString().padStart(2,'0')}`;
			if (timerSeconds <= 0) {
				clearInterval(timerInterval);
				document.getElementById('timer').classList.add('expired');
				document.getElementById('verifyBtn').disabled = true;
				Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'OTP Expired', text: 'Please request new OTP' });
			}
			timerSeconds--;
		}

		function resendOTP(e) {
			e.preventDefault();
			const link = document.getElementById('resendLink');
			if (link.classList.contains('disabled')) return;

			Swal.fire({
				title: 'Resend OTP?',
				text: 'Send new code to your email',
				@if(file_exists(public_path('assets/icons/Gif/question1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
				showCancelButton: true,
				confirmButtonColor: '#10B981',
				cancelButtonColor: '#6b7280'
			}).then((r) => {
				if (r.isConfirmed) {
					fetch('{{ route("password.forgot") }}', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': '{{ csrf_token() }}'
						},
						body: JSON.stringify({ username: '{{ session("reset_username") }}' })
					})
					.then(res => res.json())
					.then(data => {
						if (data.success) {
							timerSeconds = 600;
							clearInterval(timerInterval);
							timerInterval = setInterval(updateTimer, 1000);
							link.classList.add('disabled');
							setTimeout(() => link.classList.remove('disabled'), 30000);
							Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif, title: 'OTP Resent', timer: 1500, showConfirm: false });
						}
					});
				}
			});
		}

		document.addEventListener('DOMContentLoaded', function() {
			document.querySelector('.otp-input[data-index="0"]').focus();
			timerInterval = setInterval(updateTimer, 1000);

			document.getElementById('otpForm').addEventListener('submit', function(e) {
				if (document.getElementById('fullOtp').value.length !== 6) {
					e.preventDefault();
					Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Invalid OTP', text: 'Enter all 6 digits' });
				}
				if (timerSeconds <= 0) {
					e.preventDefault();
					Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'OTP Expired', text: 'Request new OTP' });
				}
			});

			document.querySelectorAll('.otp-input').forEach(i => {
				i.addEventListener('paste', function(e) {
					e.preventDefault();
					const paste = e.clipboardData.getData('text').trim();
					if (/^\d{6}$/.test(paste)) {
						document.querySelectorAll('.otp-input').forEach((el, idx) => el.value = paste[idx]);
						updateFullOTP();
					}
				});
			});
		});
	</script>
</body>
</html>

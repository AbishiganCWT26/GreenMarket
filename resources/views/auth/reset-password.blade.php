<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GreenMarket | Reset Password</title>
	<link rel="stylesheet" href="{{ asset('css/login.css') }}" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<style>
		.pass-meter {
			height: 2px;
			background: #e5e7eb;
			border-radius: 1px;
			margin-top: 3px;
			overflow: hidden;
		}
		.meter-bar {
			height: 100%;
			width: 0;
			transition: width 0.2s ease;
		}
		.meter-text {
			font-size: 0.6rem;
			text-align: right;
			margin-top: 2px;
		}
		.pass-rules {
			background: rgba(16,185,129,0.02);
			border: 1px solid rgba(16,185,129,0.1);
			border-radius: var(--radius-sm);
			padding: 6px;
			margin: 8px 0 12px;
			font-size: 0.65rem;
		}
		.pass-rules ul {
			list-style: none;
			padding: 0;
			margin: 0;
		}
		.pass-rules li {
			display: flex;
			align-items: center;
			gap: 4px;
			margin-bottom: 2px;
		}
		.pass-rules i { font-size: 0.55rem; width: 12px; }
		.rule-valid { color: var(--primary); }
		.rule-invalid { color: #9ca3af; }
		.info-box {
			background: rgba(59,130,246,0.05);
			border: 1px solid rgba(59,130,246,0.1);
			border-radius: var(--radius-sm);
			padding: 6px;
			margin-bottom: 10px;
			text-align: center;
			font-size: 0.7rem;
		}
		.info-box i { color: var(--blue); margin-right: 4px; }
	</style>
</head>
<body>
	<div class="auth-container">
		<div class="auth-card">
			<div class="auth-header" onclick="window.location.href='/'" role="button">
				<img src="{{ asset('assets/images/Logo Green Market.png') }}" alt="GreenMarket" class="auth-logo">
				<div class="auth-brand">
					<h1>GreenMarket</h1>
					<p>create new password</p>
				</div>
			</div>

			<div class="auth-body">
				<div class="auth-title">
					<h2><i class="fas fa-lock" style="color: var(--primary);"></i> Reset Password</h2>
					<p>Create a strong password</p>
				</div>

				<div class="info-box">
					<i class="fas fa-user-shield"></i>
					Reset for: <strong>{{ session('reset_username') }}</strong>
				</div>

				<form id="resetForm" method="POST" action="{{ route('password.reset.submit') }}">
					@csrf

					<div class="input-field">
						<label><i class="fas fa-key"></i> New Password</label>
						<div class="password-wrapper">
							<input type="password" id="password" name="password" placeholder="Enter new password" required>
							<button type="button" class="password-toggle" data-target="password">
								<i class="fas fa-eye"></i>
							</button>
						</div>
						<div class="pass-meter">
							<div class="meter-bar" id="strengthBar"></div>
						</div>
						<div class="meter-text" id="strengthText"></div>
					</div>

					<div class="input-field">
						<label><i class="fas fa-key"></i> Confirm Password</label>
						<div class="password-wrapper">
							<input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
							<button type="button" class="password-toggle" data-target="password_confirmation">
								<i class="fas fa-eye"></i>
							</button>
						</div>
					</div>

					<div class="pass-rules">
						<ul>
							<li id="rule-length"><i class="fas fa-circle rule-invalid"></i> <span>8+ characters</span></li>
							<li id="rule-upper"><i class="fas fa-circle rule-invalid"></i> <span>Uppercase letter</span></li>
							<li id="rule-lower"><i class="fas fa-circle rule-invalid"></i> <span>Lowercase letter</span></li>
							<li id="rule-number"><i class="fas fa-circle rule-invalid"></i> <span>Number</span></li>
							<li id="rule-special"><i class="fas fa-circle rule-invalid"></i> <span>Special character</span></li>
						</ul>
					</div>

					<button type="submit" class="auth-btn" id="resetBtn">
						<i class="fas fa-sync-alt"></i> Reset Password
					</button>
				</form>

				<div class="auth-back">
					<a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Back to Login</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		function checkStrength(pass) {
			let score = 0;
			const rules = {
				length: pass.length >= 8,
				upper: /[A-Z]/.test(pass),
				lower: /[a-z]/.test(pass),
				number: /[0-9]/.test(pass),
				special: /[^A-Za-z0-9]/.test(pass)
			};

			Object.values(rules).forEach(v => v && (score += 20));

			document.getElementById('rule-length').querySelector('i').className = rules.length ? 'fas fa-check-circle rule-valid' : 'fas fa-circle rule-invalid';
			document.getElementById('rule-upper').querySelector('i').className = rules.upper ? 'fas fa-check-circle rule-valid' : 'fas fa-circle rule-invalid';
			document.getElementById('rule-lower').querySelector('i').className = rules.lower ? 'fas fa-check-circle rule-valid' : 'fas fa-circle rule-invalid';
			document.getElementById('rule-number').querySelector('i').className = rules.number ? 'fas fa-check-circle rule-valid' : 'fas fa-circle rule-invalid';
			document.getElementById('rule-special').querySelector('i').className = rules.special ? 'fas fa-check-circle rule-valid' : 'fas fa-circle rule-invalid';

			let color = '#ef4444', text = 'Very Weak';
			if (score >= 20) { color = '#f59e0b'; text = 'Weak'; }
			if (score >= 40) { color = '#f59e0b'; text = 'Fair'; }
			if (score >= 60) { color = '#3b82f6'; text = 'Good'; }
			if (score >= 80) { color = '#10B981'; text = 'Strong'; }
			if (score >= 100) { color = '#059669'; text = 'Very Strong'; }

			document.getElementById('strengthBar').style.width = score + '%';
			document.getElementById('strengthBar').style.backgroundColor = color;
			document.getElementById('strengthText').textContent = text;
			document.getElementById('strengthText').style.color = color;

			return { score, rules };
		}

		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.password-toggle').forEach(btn => {
				btn.addEventListener('click', function() {
					const target = document.getElementById(this.dataset.target);
					const type = target.type === 'password' ? 'text' : 'password';
					target.type = type;
					this.querySelector('i').classList.toggle('fa-eye');
					this.querySelector('i').classList.toggle('fa-eye-slash');
				});
			});

			const passInput = document.getElementById('password');
			const confirmInput = document.getElementById('password_confirmation');

			passInput.addEventListener('input', () => checkStrength(passInput.value));
			confirmInput.addEventListener('input', () => checkStrength(passInput.value));

			document.getElementById('resetForm').addEventListener('submit', function(e) {
				const pass = passInput.value;
				const conf = confirmInput.value;
				const { score, rules } = checkStrength(pass);

				if (pass !== conf) {
					e.preventDefault();
					Swal.fire({ icon: 'error', title: 'Passwords Mismatch', text: 'Please match both passwords' });
				} else if (score < 80 || !Object.values(rules).every(v => v)) {
					e.preventDefault();
					Swal.fire({ icon: 'warning', title: 'Weak Password', text: 'Use a stronger password' });
				}
			});

			checkStrength('');
		});
	</script>
</body>
</html>
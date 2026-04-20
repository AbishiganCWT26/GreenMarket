<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5, user-scalable=yes">
	<title>404 | GreenMarket</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		:root {
			--primary-green: #99c866;
			--dark-green: #77c820ff;
			--body-bg: #f6f8fa;
			--card-bg: #ffffff;
			--text-color: #0f1724;
			--muted: #6b7280;
			--accent-amber: #f59e0b;
			--blue: #3b82f6;
			--purple: #8b5cf6;
			--shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
			--shadow-md: 0 7px 15px rgba(15,23,36,0.08);
			--border: #e5e7eb;
			--radius-sm: 6px;
			--radius-md: 8px;
			--radius-lg: 12px;
		}

		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			background: var(--body-bg);
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 5px;
		}

		.error-container {
			max-width: 500px;
			width: 100%;
			margin: 0 auto;
			animation: fadeInUp 0.4s ease;
		}

		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(20px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.error-card {
			background: var(--card-bg);
			border-radius: var(--radius-lg);
			border: 1px solid var(--border);
			box-shadow: var(--shadow-md);
			overflow: hidden;
			text-align: center;
			padding: 20px 15px;
		}

		.error-icon {
			margin-bottom: 24px;
			display: flex;
			justify-content: center;
		}

		.error-icon img {
			max-width: 180px;
			height: auto;
		}

        .error-code {
			font-size: 70px;
			font-weight: 600;
			color: var(--primary-green);
			line-height: 1;
			margin-bottom: 8px;
			letter-spacing: -3px;
		}

		.error-title {
			font-size: 22px;
			font-weight: 600;
			color: var(--text-color);
			margin-bottom: 12px;
		}

		.error-message {
			font-size: 14px;
			color: var(--muted);
			margin-bottom: 28px;
			line-height: 1.5;
		}

		.action-buttons {
			display: flex;
			gap: 12px;
			justify-content: center;
			flex-wrap: wrap;
			margin-bottom: 28px;
		}

		.btn {
			padding: 10px 24px;
			border-radius: var(--radius-sm);
			font-size: 13px;
			font-weight: 500;
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			gap: 8px;
			transition: all 0.2s ease;
			cursor: pointer;
			border: none;
		}

		.btn-primary {
			background: var(--primary-green);
			color: white;
		}

		.btn-primary:hover {
			background: var(--dark-green);
			transform: translateY(-2px);
			box-shadow: var(--shadow-sm);
		}

		.btn-secondary {
			background: var(--primary-green);
			color: white;
			border: 1px solid var(--border);
		}

		.btn-secondary:hover {
			background: var(--dark-green);
			border-color: var(--primary-green);
			transform: translateY(-2px);
		}

		@media (max-width: 480px) {
			.error-card { padding: 28px 20px; }
			.error-title { font-size: 18px; }
			.error-message { font-size: 12px; }
			.btn { padding: 8px 18px; font-size: 12px; }
			.action-buttons { gap: 8px; }
			.help-links { gap: 12px; }
			.help-link { font-size: 10px; }
			.error-icon img { max-width: 140px; }
		}

		@media (max-width: 380px) {
			.error-card { padding: 20px 16px; }
			.error-title { font-size: 16px; }
			.action-buttons { flex-direction: column; }
			.btn { justify-content: center; }
			.error-icon img { max-width: 120px; }
		}
	</style>
</head>
<body>
	<div class="error-container">
		<div class="error-card">
			<div class="error-icon">
				<img src="{{ asset('assets/icons/Gif/404 error1.gif') }}" alt="404 Error">
			</div>
			<h1 class="error-title">Page Not Found</h1>
			<p class="error-message">
				This page is outside of the universe.<br>
				The page you are trying to access doesn't exist or has been moved.
			</p>
			<div class="action-buttons">
				<a href="{{ url('/') }}" class="btn btn-primary">
					<i class="fas fa-home"></i>
					Back to Home
				</a>
				<button onclick="window.history.back()" class="btn btn-secondary">
					<i class="fas fa-arrow-left"></i>
					Go Back
				</button>
			</div>
		</div>
	</div>
</body>
</html>
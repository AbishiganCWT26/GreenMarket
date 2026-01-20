<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>New Message Received</title>
	<style>
		:root {
			--primary-green: #10B981;
			--dark-green: #059669;
			--body-bg: #f6f8fa;
			--card-bg: #ffffff;
			--text-color: #0f1724;
			--muted: #6b7280;
			--border-color: #e5e7eb;
			--shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
			--shadow-md: 0 4px 6px rgba(15,23,36,0.1);
			--shadow-lg: 0 10px 25px rgba(15,23,36,0.1);
		}

		body {
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
			background-color: var(--body-bg);
			margin: 0;
			padding: 20px;
			color: var(--text-color);
			line-height: 1.6;
		}

		.container {
			max-width: 600px;
			margin: 0 auto;
			animation: fadeIn 0.5s ease-out;
		}

		@keyframes fadeIn {
			from { opacity: 0; transform: translateY(10px); }
			to { opacity: 1; transform: translateY(0); }
		}

		.header {
			text-align: center;
			margin-bottom: 30px;
			padding: 20px 0;
			border-bottom: 2px solid var(--primary-green);
		}

		.header h1 {
			margin: 0 0 10px 0;
			color: var(--text-color);
			font-size: 24px;
		}

		.header p {
			margin: 0;
			color: var(--muted);
			font-size: 14px;
		}

		.message-card {
			background: var(--card-bg);
			border-radius: 12px;
			box-shadow: var(--shadow-md);
			overflow: hidden;
			margin-bottom: 20px;
			transition: transform 0.3s ease, box-shadow 0.3s ease;
		}

		.message-card:hover {
			transform: translateY(-2px);
			box-shadow: var(--shadow-lg);
		}

		.sender-section {
			background: linear-gradient(135deg, #f0f9ff 0%, #e6f7ff 100%);
			padding: 20px;
			display: flex;
			align-items: center;
			gap: 15px;
			border-bottom: 1px solid var(--border-color);
		}

		.avatar {
			width: 50px;
			height: 50px;
			background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			color: white;
			font-weight: 600;
			font-size: 18px;
			transition: transform 0.3s ease;
		}

		.sender-section:hover .avatar {
			transform: scale(1.1) rotate(5deg);
		}

		.sender-info h3 {
			margin: 0 0 5px 0;
			font-size: 18px;
		}

		.sender-info p {
			margin: 0;
			color: var(--muted);
			font-size: 14px;
		}

		.message-body {
			padding: 25px;
		}

		.info-item {
			margin-bottom: 20px;
			padding-bottom: 20px;
			border-bottom: 1px solid var(--border-color);
			transition: padding-left 0.3s ease;
		}

		.info-item:hover {
			padding-left: 10px;
		}

		.info-item:last-child {
			border-bottom: none;
			margin-bottom: 0;
			padding-bottom: 0;
		}

		.info-label {
			display: block;
			font-size: 12px;
			text-transform: uppercase;
			font-weight: 600;
			color: var(--primary-green);
			margin-bottom: 8px;
			letter-spacing: 0.5px;
		}

		.info-value {
			font-size: 15px;
			color: var(--text-color);
		}

		.message-content {
			background: #f8fafc;
			border-radius: 8px;
			padding: 20px;
			margin-top: 25px;
			border-left: 4px solid var(--primary-green);
			transition: all 0.3s ease;
		}

		.message-content:hover {
			background: #f1f5f9;
			transform: translateX(5px);
		}

		.message-text {
			margin: 0;
			font-size: 15px;
			white-space: pre-wrap;
		}

		.footer {
			text-align: center;
			padding: 20px;
			color: var(--muted);
			font-size: 12px;
			border-top: 1px solid var(--border-color);
			margin-top: 30px;
		}

		.timestamp {
			display: inline-flex;
			align-items: center;
			gap: 5px;
			padding: 6px 12px;
			background: #f8fafc;
			border-radius: 20px;
			font-size: 12px;
			color: var(--muted);
			margin-top: 15px;
		}

		.action-button {
			display: inline-block;
			padding: 12px 30px;
			background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
			color: white;
			text-decoration: none;
			border-radius: 25px;
			font-weight: 600;
			font-size: 14px;
			transition: all 0.3s ease;
			border: none;
			cursor: pointer;
			margin-top: 20px;
		}

		.action-button:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
		}

		.action-button:active {
			transform: translateY(0);
		}

		.highlight {
			background: linear-gradient(120deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0) 100%);
			padding: 2px 6px;
			border-radius: 4px;
		}

		@media (max-width: 768px) {
			body {
				padding: 10px;
			}

			.container {
				max-width: 100%;
			}

			.sender-section {
				flex-direction: column;
				text-align: center;
				padding: 15px;
			}

			.avatar {
				width: 60px;
				height: 60px;
				font-size: 20px;
			}

			.message-body {
				padding: 15px;
			}

			.header h1 {
				font-size: 20px;
			}

			.info-value {
				font-size: 14px;
			}

			.message-text {
				font-size: 14px;
			}
		}

		@media (max-width: 480px) {
			.message-body {
				padding: 12px;
			}

			.header {
				padding: 15px 0;
			}

			.header h1 {
				font-size: 18px;
			}

			.action-button {
				width: 100%;
				padding: 12px;
			}
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="header">
			<!-- Logo Section -->
			@php
				$logoPngPath = public_path('assets/images/Logo-4.png');
				$logoSvgPath = public_path('assets/images/Logo-4.svg');
			@endphp

			@if(file_exists($logoPngPath))
				<img src="{{ $message->embed($logoPngPath) }}" alt="GreenMarket Logo" style="max-width: 100px; height: auto; display: block; margin: 0 auto 15px;">
			@elseif(file_exists($logoSvgPath))
				<img src="{{ $message->embed($logoSvgPath) }}" alt="GreenMarket Logo" style="max-width: 100px; height: auto; display: block; margin: 0 auto 15px;">
			@else
				<h2 style="color: var(--primary-green); margin: 0 0 10px;">GreenMarket</h2>
			@endif

			<h1>New Message Received</h1>
			<p>GreenMarket • Contact Form Submission</p>
		</div>

		<div class="message-card">
			<div class="sender-section">
				<div class="avatar">
					{{ substr($data['name'], 0, 1) }}
				</div>
				<div class="sender-info">
					<h3>{{ $data['name'] }}</h3>
					<p>{{ $data['email'] }}</p>
				</div>
			</div>

			<div class="message-body">
				<div class="info-item">
					<span class="info-label">From</span>
					<div class="info-value">{{ $data['name'] }} &lt;{{ $data['email'] }}&gt;</div>
				</div>

				@if(!empty($data['subject']))
				<div class="info-item">
					<span class="info-label">Subject</span>
					<div class="info-value">{{ $data['subject'] }}</div>
				</div>
				@endif

				<div class="info-item">
					<span class="info-label">Received</span>
					<div class="info-value">{{ now()->format('F d, Y \a\t h:i A') }}</div>
				</div>

				<div class="message-content">
					<span class="info-label">Message</span>
					<p class="message-text">{{ $data['message'] }}</p>
				</div>

				<div style="text-align: center; margin-top: 30px;">
					<button class="action-button" onclick="replyToMessage()">
						Reply to {{ $data['name'] }}
					</button>
					<div class="timestamp">
						{{ now()->format('M d, Y • h:i:s A') }}
					</div>
				</div>
			</div>
		</div>

		<div class="footer">
			<p>This is an automated notification from GreenMarket.</p>
			<p>Please do not reply to this email directly.</p>
		</div>
	</div>

	<script>
		function replyToMessage() {
			const email = "{{ $data['email'] }}";
			const subject = "{{ !empty($data['subject']) ? 'Re: ' . $data['subject'] : 'Re: Your Inquiry' }}";
			window.location.href = `mailto:${email}?subject=${encodeURIComponent(subject)}`;
		}

		document.addEventListener('DOMContentLoaded', function() {
			const messageCard = document.querySelector('.message-card');

			messageCard.addEventListener('mouseenter', function() {
				this.style.transform = 'translateY(-4px)';
			});

			messageCard.addEventListener('mouseleave', function() {
				this.style.transform = 'translateY(0)';
			});

			const infoItems = document.querySelectorAll('.info-item');
			infoItems.forEach(item => {
				item.addEventListener('click', function() {
					this.style.backgroundColor = '#f8fafc';
					setTimeout(() => {
						this.style.backgroundColor = '';
					}, 300);
				});
			});

			const actionButton = document.querySelector('.action-button');
			actionButton.addEventListener('mouseenter', function() {
				this.style.transform = 'translateY(-2px) scale(1.02)';
			});

			actionButton.addEventListener('mouseleave', function() {
				this.style.transform = 'translateY(0) scale(1)';
			});
		});
	</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5, user-scalable=yes">
	<title>New Message | GreenMarket</title>
	<style>
		:root {
			--primary-green: #10B981;
			--dark-green: #059669;
			--body-bg: #f0f4f2;
			--card-bg: #ffffff;
			--text-color: #0f1724;
			--muted: #6b7280;
			--accent-amber: #f59e0b;
			--blue: #3b82f6;
			--purple: #8b5cf6;
			--border: #e5e7eb;
			--shadow-sm: 0 1px 3px rgba(15, 23, 36, 0.04);
			--shadow-md: 0 7px 15px rgba(15, 23, 36, 0.08);
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
			background-color: var(--body-bg);
			font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 100vh;
			padding: 24px 16px;
			margin: 0;
		}

		.email-wrap {
			width: 100%;
			max-width: 580px;
			margin: 0 auto;
		}

		.email-card {
			background: var(--card-bg);
			border-radius: 16px;
			overflow: hidden;
			box-shadow:
				0 1px 2px rgba(15, 23, 36, 0.06),
				0 8px 24px rgba(15, 23, 36, 0.10),
				0 20px 48px rgba(15, 23, 36, 0.06);
			border: 1px solid #e9ecef;
		}

		/* ---------- HEADER ---------- */
		.email-head {
			background: linear-gradient(145deg, #059669 0%, #10B981 40%, #34D399 100%);
			padding: 32px 30px 28px;
			text-align: center;
			color: #ffffff;
			position: relative;
			overflow: hidden;
		}

		.email-head::before {
			content: '';
			position: absolute;
			top: -60px;
			right: -60px;
			width: 180px;
			height: 180px;
			background: rgba(255, 255, 255, 0.07);
			border-radius: 50%;
			pointer-events: none;
		}

		.email-head::after {
			content: '';
			position: absolute;
			bottom: -50px;
			left: -40px;
			width: 140px;
			height: 140px;
			background: rgba(255, 255, 255, 0.05);
			border-radius: 50%;
			pointer-events: none;
		}

		.email-logo {
			margin-bottom: 18px;
			position: relative;
			z-index: 1;
		}

		.email-logo img {
			max-width: 64px;
			height: auto;
			border-radius: 12px;
			background: #ffffff;
			padding: 6px;
			box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
		}

		.email-logo h2 {
			color: #ffffff;
			font-size: 20px;
			font-weight: 700;
			letter-spacing: -0.3px;
			margin: 0;
			text-shadow: 0 1px 2px rgba(0, 0, 0, 0.10);
		}

		.email-head h1 {
			font-size: 24px;
			font-weight: 700;
			letter-spacing: -0.4px;
			margin: 0 0 6px;
			position: relative;
			z-index: 1;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
		}

		.email-head h2 {
			font-size: 20px;
			font-weight: 700;
			margin: 0 0 6px;
			position: relative;
			z-index: 1;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
		}

		.email-head p {
			font-size: 13px;
			font-weight: 500;
			opacity: 0.85;
			margin: 0;
			letter-spacing: 0.2px;
			position: relative;
			z-index: 1;
		}

		/* ---------- BODY ---------- */
		.email-body {
			padding: 28px 30px 24px;
			background: #fefefe;
		}

		/* Info Group */
		.info-group {
			background: #fafcfb;
			border: 1px solid #eef1f5;
			border-radius: var(--radius-md);
			padding: 6px 0;
			margin-bottom: 22px;
			overflow: hidden;
		}

		.info-row {
			display: flex;
			align-items: flex-start;
			padding: 11px 18px;
			gap: 12px;
		}

		.info-row + .info-row {
			border-top: 1px solid #eef1f5;
		}

		.info-label {
			font-size: 12px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.6px;
			color: #9ca3af;
			min-width: 70px;
			padding-top: 1px;
		}

		.info-value {
			font-size: 14px;
			font-weight: 500;
			color: #1f2937;
			line-height: 1.5;
			word-break: break-word;
		}

		/* Message Box */
		.message-box {
			background: #fdfdff;
			border: 1px solid #eef1f5;
			border-left: 4px solid #10B981;
			border-radius: 0 var(--radius-md) var(--radius-md) 0;
			padding: 18px 20px;
			margin-bottom: 22px;
		}

		.message-label {
			display: inline-block;
			font-size: 11px;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: 0.8px;
			color: #10B981;
			margin-bottom: 8px;
		}

		.message-text {
			font-size: 15px;
			color: #374151;
			line-height: 1.7;
			white-space: pre-wrap;
			word-break: break-word;
			margin: 0;
		}

		/* ---------- FOOTER ---------- */
		.email-foot {
			background: #f8faf9;
			border-top: 1px solid #eef1f5;
			padding: 16px 30px 18px;
			text-align: center;
		}

		.email-foot p {
			font-size: 11px;
			color: #9ca3af;
			margin: 0;
			line-height: 1.6;
			letter-spacing: 0.2px;
		}

		.email-foot p + p {
			margin-top: 2px;
		}
	</style>
</head>
<body>
	<div class="email-wrap">
		<div class="email-card">
			<div class="email-head">
				<div class="email-logo">
					@php
						$logoPngPath = public_path('assets/images/Logo Green Market.png');
						$logoSvgPath = public_path('assets/images/Logo-4.svg');
					@endphp
					@if(file_exists($logoPngPath))
						<img src="{{ $message->embed($logoPngPath) }}" alt="GreenMarket" style="max-width: 70px; height: auto;">
					@elseif(file_exists($logoSvgPath))
						<img src="{{ $message->embed($logoSvgPath) }}" alt="GreenMarket" style="max-width: 70px; height: auto;">
					@else
						<h2 style="color: white; font-size: 16px; margin: 0;">GreenMarket</h2>
					@endif
				</div>
				<h1>New Message Received</h1>
				<h2>GreenMarket</h2>
				<p>Contact Form Submission</p>
			</div>

			<div class="email-body">

				<div class="info-group">
					<div class="info-row">
						<span class="info-label">From</span>
						<span class="info-value">{{ $data['name'] }} &lt;{{ $data['email'] }}&gt;</span>
					</div>
					@if(!empty($data['subject']))
					<div class="info-row">
						<span class="info-label">Subject</span>
						<span class="info-value">{{ $data['subject'] }}</span>
					</div>
					@endif
					<div class="info-row">
						<span class="info-label">Received</span>
						<span class="info-value">{{ now()->format('F d, Y \a\t h:i:s A') }}</span>
					</div>
				</div>

				<div class="message-box">
					<span class="message-label">Message</span>
					<p class="message-text">{{ $data['message'] }}</p>
				</div>
			</div>

			<div class="email-foot">
				<p>This is an automated notification from GreenMarket.</p>
				<p>Please do not reply to this email directly.</p>
			</div>
		</div>
	</div>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
	<script>
		function replyToMessage() {
			const email = "{{ $data['email'] }}";
			const subject = "{{ !empty($data['subject']) ? 'Re: ' . $data['subject'] : 'Re: Your Inquiry' }}";
			window.location.href = `mailto:${email}?subject=${encodeURIComponent(subject)}`;
		}
	</script>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Welcome to GreenMarket</title>

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
}

body {
	margin: 0;
	padding: 0;
	background: var(--body-bg);
	font-family: Arial, Helvetica, sans-serif;
}

.wrapper {
	width: 100%;
	padding: 25px 0;
}

.container {
	max-width: 620px;
	background: var(--card-bg);
	margin: 0 auto;
	border-radius: 14px;
	overflow: hidden;
	box-shadow: var(--shadow-md);
	animation: fadeIn 0.6s ease;
}

@keyframes fadeIn {
	from { opacity: 0; transform: translateY(12px); }
	to { opacity: 1; transform: translateY(0); }
}

.header {
	background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
	padding: 30px 20px;
	text-align: center;
	color: #000000;
}

.logo {
	width: 120px;
	transition: transform 0.4s ease;
}

.logo:hover {
	transform: scale(1.05) rotate(-2deg);
}

h2 {
	margin: 10px 0 4px;
	font-weight: bold;
}

p {
	margin: 0;
	font-size: 14px;
	opacity: 0.9;
}

.content {
	padding: 25px 20px;
	text-align: center;
}

.badge {
	display: inline-block;
	background: rgba(16,185,129,0.12);
	color: var(--dark-green);
	padding: 6px 14px;
	border-radius: 20px;
	font-size: 11px;
	font-weight: bold;
	margin-bottom: 14px;
}

h1 {
	font-size: 22px;
	margin: 8px 0;
	color: var(--text-color);
}

.text {
	color: var(--muted);
	font-size: 14px;
	line-height: 1.6;
	margin-bottom: 18px;
}

.steps {
	text-align: justify;
	background: #f9fafb;
	border-radius: 12px;
	padding: 14px 16px;
	font-size: 13px;
	color: var(--text-color);
	box-shadow: var(--shadow-sm);
}

.steps div {
	margin: 6px 0;
	display: flex;
	align-items: center;
	gap: 8px;
	transition: transform 0.3s ease;
}

.steps div:hover {
	transform: translateX(4px);
	color: var(--primary-green);
}

.card {
	margin-top: 14px;
	padding: 14px;
	background: #ffffff;
	border-radius: 12px;
	box-shadow: var(--shadow-sm);
	transition: all 0.3s ease;
}

.card:hover {
	box-shadow: var(--shadow-md);
	transform: translateY(-3px);
}

.row {
	display: flex;
	align-items: center;
}

.icon {
	width: 36px;
	height: 36px;
	margin-right: 10px;
}

.label {
	font-size: 11px;
	color: var(--muted);
	text-transform: uppercase;
}

.value {
	font-size: 14px;
	font-weight: bold;
	color: var(--text-color);
}

.button {
	display: inline-block;
	margin-top: 22px;
	background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
	color: #ffffff;
	padding: 12px 26px;
	border-radius: 10px;
	text-decoration: none;
	font-weight: bold;
	font-size: 14px;
	box-shadow: 0 5px 16px rgba(16,185,129,0.25);
	transition: all 0.35s ease;
}

.button:hover {
	transform: translateY(-3px);
	box-shadow: 0 10px 25px rgba(16,185,129,0.35);
}

.footer {
	background: #f9fafb;
	text-align: center;
	padding: 18px;
	font-size: 12px;
	color: var(--muted);
}
</style>
</head>

<body>
<div class="wrapper">
<div class="container">

<div class="header">
    @php
        $logoPngPath = public_path('assets/images/Logo-4.png');
        $logoSvgPath = public_path('assets/images/Logo-4.svg');
    @endphp

    @if(file_exists($logoPngPath))
        <img src="{{ $message->embed($logoPngPath) }}" alt="GreenMarket" class="logo">
    @elseif(file_exists($logoSvgPath))
        <img src="{{ $message->embed($logoSvgPath) }}" alt="GreenMarket" class="logo">
    @else
        <h2 style="color: white;">GreenMarket</h2>
    @endif

	<h2>GreenMarket</h2>
	<p>Fresh Garden Hub Marketplace</p>
</div>

<div class="content">
	<div class="badge">REGISTRATION SUCCESS</div>

	<h1>Welcome to Our Family!</h1>

	<p class="text">
		Hi <strong>{{ $name }}</strong>, your buyer account has been successfully created.
	</p>

	<div class="steps">
		<div>Experience the best of the harvest by shopping for fresh produce directly from local farmers today.
            Our platform makes it easy to browse a wide selection of homegrown goods, ensuring you receive quality products while supporting sustainable agriculture.
            Simply start searching for your favorite items, add them to your cart, and place your first order to enjoy the convenience of farm-to-table delivery.

        </div>
    </div>

	<div class="card">
		<div class="row">
			<img src="https://cdn-icons-png.flaticon.com/512/747/747376.png" class="icon">
			<div>
				<div class="label">Username</div>
				<div class="value">{{ $username }}</div>
			</div>
		</div>
	</div>

	<div class="card">
		<div class="row">
			<img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" class="icon">
			<div>
				<div class="label">Email</div>
				<div class="value">{{ $email }}</div>
			</div>
		</div>
	</div>

	<div class="card">
		<div class="row">
			<img src="https://cdn-icons-png.flaticon.com/512/3064/3064155.png" class="icon">
			<div>
				<div class="label">Password</div>
				<div class="value">{{ $password }}</div>
			</div>
		</div>
	</div>

	<a href="{{ $login_url }}" class="button">
		Access Your Dashboard
	</a>
</div>

<div class="footer">
	<p>Need help? support@smartmarket.com</p>
	<p>&copy; {{ date('Y') }} GreenMarket - GreenMarket</p>
</div>

</div>
</div>
</body>
</html>

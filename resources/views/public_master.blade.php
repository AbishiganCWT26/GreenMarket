<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'GreenMarket')</title>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	@yield('styles')
</head>
<body>

@include('includes.navbar')

<main class="site-main" role="main">
	@yield('content')
</main>

@include('includes.footer')

@yield('scripts')
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>

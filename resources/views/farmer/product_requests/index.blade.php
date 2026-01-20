@extends('farmer.layouts.farmer_master')

@section('title', 'Buyer Product Requests')

@section('page-title', 'Buyer Product Requests')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
	background-color: var(--body-bg);
	font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.requests-wrapper {
	padding: 25px;
	max-width: 1400px;
	margin: 0 auto;
	min-height: 100vh;
}

.requests-header {
	margin-bottom: 40px;
	text-align: center;
	animation: fadeInDown 0.8s ease;
}

.requests-header h2 {
	color: var(--text-color);
	font-size: 2.8rem;
	margin-bottom: 15px;
	font-weight: 800;
	background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	text-shadow: 0 2px 10px rgba(16, 185, 129, 0.1);
	letter-spacing: -0.5px;
}

.requests-header p {
	color: var(--muted);
	font-size: 1.25rem;
	max-width: 800px;
	margin: 0 auto;
	line-height: 1.6;
	font-weight: 400;
}

.filters-container {
	background: var(--card-bg);
	border-radius: 18px;
	padding: 25px;
	margin-bottom: 40px;
	box-shadow: var(--shadow-md);
	border: 1px solid rgba(15,23,36,0.05);
	animation: slideUp 0.6s ease;
}

.search-filters {
	display: grid;
	grid-template-columns: 1fr auto auto;
	gap: 20px;
	align-items: center;
}

.search-box {
	position: relative;
}

.search-box input {
	width: 100%;
	padding: 16px 25px 16px 55px;
	border: 2px solid #e5e7eb;
	border-radius: 14px;
	font-size: 1.05rem;
	transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	background: var(--card-bg);
	color: var(--text-color);
	font-weight: 500;
}

.search-box input:focus {
	outline: none;
	border-color: var(--primary-green);
	box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
	transform: translateY(-1px);
}

.search-box i {
	position: absolute;
	left: 20px;
	top: 50%;
	transform: translateY(-50%);
	color: var(--muted);
	font-size: 1.3rem;
	transition: color 0.3s ease;
}

.search-box input:focus + i {
	color: var(--primary-green);
}

.filter-select {
	padding: 16px 25px;
	border: 2px solid #e5e7eb;
	border-radius: 14px;
	background: var(--card-bg);
	color: var(--text-color);
	font-size: 1.05rem;
	min-width: 220px;
	cursor: pointer;
	transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	font-weight: 500;
	appearance: none;
	background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
	background-repeat: no-repeat;
	background-position: right 20px center;
	background-size: 20px;
}

.filter-select:focus {
	outline: none;
	border-color: var(--primary-green);
	box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
	transform: translateY(-1px);
}

.filter-select:hover {
	border-color: var(--primary-green);
}

.requests-container {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
	gap: 30px;
	animation: fadeIn 0.8s ease;
}

.request-card {
	background: var(--card-bg);
	border-radius: 20px;
	overflow: hidden;
	box-shadow: var(--shadow-md);
	border: 1px solid rgba(15,23,36,0.05);
	transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
	position: relative;
	height: 320px;
	display: flex;
	flex-direction: column;
}

.request-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 5px;
	background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
	transform: scaleX(0);
	transform-origin: left;
	transition: transform 0.4s ease;
}

.request-card:hover::before {
	transform: scaleX(1);
}

.request-card:hover {
	transform: translateY(-10px) scale(1.02);
	box-shadow: 0 25px 50px rgba(15,23,36,0.12);
}

.request-card:active {
	transform: translateY(-5px) scale(1.01);
}

.card-badge {
	position: absolute;
	top: 20px;
	right: 20px;
	padding: 8px 20px;
	border-radius: 25px;
	font-size: 0.8rem;
	font-weight: 800;
	text-transform: uppercase;
	letter-spacing: 0.8px;
	z-index: 2;
	box-shadow: 0 4px 12px rgba(0,0,0,0.1);
	transition: all 0.3s ease;
}

.card-badge:hover {
	transform: scale(1.05);
}

.badge-active {
	background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
	color: white;
}

.badge-urgent {
	background: linear-gradient(135deg, #ef4444, #dc2626);
	color: white;
	animation: pulse 2s infinite;
}

.card-header {
	padding: 25px 25px 20px;
	flex: 1;
	display: flex;
	flex-direction: column;
}

.product-name {
	font-size: 1.6rem;
	font-weight: 800;
	color: var(--text-color);
	margin-bottom: 12px;
	line-height: 1.3;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
	transition: color 0.3s ease;
}

.request-card:hover .product-name {
	color: var(--primary-green);
}

.product-description {
	color: var(--muted);
	font-size: 1rem;
	line-height: 1.5;
	margin-bottom: 20px;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
	flex-grow: 1;
}

.details-grid {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	gap: 15px;
	margin-bottom: 20px;
}

.detail-item {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 14px;
	background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(5, 150, 105, 0.05));
	border-radius: 12px;
	transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	border: 1px solid transparent;
}

.detail-item:hover {
	background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1));
	transform: translateY(-3px);
	border-color: var(--primary-green);
	box-shadow: 0 5px 15px rgba(16, 185, 129, 0.1);
}

.detail-icon {
	width: 40px;
	height: 40px;
	background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
	border-radius: 10px;
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 1.1rem;
	flex-shrink: 0;
	transition: all 0.3s ease;
}

.detail-item:hover .detail-icon {
	transform: rotate(10deg) scale(1.1);
}

.detail-text {
	flex: 1;
	min-width: 0;
}

.detail-text h4 {
	font-size: 0.85rem;
	color: var(--muted);
	margin: 0;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.detail-text p {
	font-size: 1.1rem;
	color: var(--text-color);
	margin: 6px 0 0;
	font-weight: 700;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}

.date-warning {
	display: flex;
	align-items: center;
	gap: 10px;
	padding: 12px 16px;
	background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
	border-radius: 12px;
	margin-bottom: 20px;
	color: var(--accent-amber);
	font-weight: 600;
	font-size: 0.9rem;
	border: 1px solid rgba(245, 158, 11, 0.2);
	animation: shimmer 2s infinite;
}

.date-warning i {
	font-size: 1.2rem;
}

.card-footer {
	padding: 20px 25px;
	background: linear-gradient(to right, rgba(16, 185, 129, 0.03), rgba(5, 150, 105, 0.03));
	border-top: 1px solid rgba(15,23,36,0.05);
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.requested-date {
	font-size: 0.9rem;
	color: var(--muted);
	display: flex;
	align-items: center;
	gap: 8px;
	font-weight: 500;
}

.requested-date i {
	font-size: 1rem;
	color: var(--primary-green);
}

.btn-view-details {
	background: linear-gradient(135deg, var(--blue), var(--purple));
	color: white;
	border: none;
	padding: 12px 28px;
	border-radius: 12px;
	font-size: 1rem;
	font-weight: 700;
	cursor: pointer;
	transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	display: flex;
	align-items: center;
	gap: 10px;
	letter-spacing: 0.5px;
	position: relative;
	overflow: hidden;
}

.btn-view-details::before {
	content: '';
	position: absolute;
	top: 0;
	left: -100%;
	width: 100%;
	height: 100%;
	background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
	transition: left 0.6s ease;
}

.btn-view-details:hover::before {
	left: 100%;
}

.btn-view-details:hover {
	transform: translateY(-3px);
	box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
}

.btn-view-details:active {
	transform: translateY(-1px);
}

.btn-view-details i {
	font-size: 1.1rem;
	transition: transform 0.3s ease;
}

.btn-view-details:hover i {
	transform: translateX(3px);
}

.no-requests {
	text-align: center;
	padding: 100px 20px;
	grid-column: 1 / -1;
	animation: fadeIn 0.8s ease;
}

.no-requests-icon {
	font-size: 6rem;
	color: var(--muted);
	margin-bottom: 30px;
	opacity: 0.2;
	animation: float 3s ease-in-out infinite;
}

.no-requests h3 {
	color: var(--text-color);
	font-size: 2.2rem;
	margin-bottom: 20px;
	font-weight: 800;
}

.no-requests p {
	color: var(--muted);
	font-size: 1.3rem;
	margin-bottom: 40px;
	line-height: 1.6;
}

.loading {
	text-align: center;
	padding: 80px;
	grid-column: 1 / -1;
}

.spinner {
	width: 60px;
	height: 60px;
	border: 5px solid rgba(16, 185, 129, 0.1);
	border-top: 5px solid var(--primary-green);
	border-radius: 50%;
	animation: spin 1s linear infinite;
	margin: 0 auto 25px;
}

@keyframes fadeIn {
	from { opacity: 0; }
	to { opacity: 1; }
}

@keyframes fadeInDown {
	from {
		opacity: 0;
		transform: translateY(-30px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

@keyframes slideUp {
	from {
		opacity: 0;
		transform: translateY(30px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

@keyframes spin {
	0% { transform: rotate(0deg); }
	100% { transform: rotate(360deg); }
}

@keyframes pulse {
	0%, 100% {
		opacity: 1;
		box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
	}
	50% {
		opacity: 0.9;
		box-shadow: 0 4px 20px rgba(239, 68, 68, 0.5);
	}
}

@keyframes float {
	0%, 100% { transform: translateY(0); }
	50% { transform: translateY(-10px); }
}

@keyframes shimmer {
	0% { background-position: -200% center; }
	100% { background-position: 200% center; }
}

@media (min-width: 1200px) {
	.requests-container {
		grid-template-columns: repeat(3, 1fr);
	}
}

@media (max-width: 1199px) and (min-width: 992px) {
	.requests-wrapper {
		padding: 20px;
	}

	.requests-container {
		grid-template-columns: repeat(2, 1fr);
		gap: 25px;
	}

	.search-filters {
		grid-template-columns: 1fr;
		gap: 15px;
	}

	.filter-select {
		width: 100%;
	}
}

@media (max-width: 991px) and (min-width: 768px) {
	.requests-wrapper {
		padding: 15px;
	}

	.requests-container {
		grid-template-columns: 1fr;
		gap: 20px;
	}

	.requests-header h2 {
		font-size: 2.2rem;
	}

	.requests-header p {
		font-size: 1.1rem;
	}

	.filters-container {
		padding: 20px;
	}

	.search-filters {
		grid-template-columns: 1fr;
		gap: 12px;
	}

	.request-card {
		height: 300px;
	}
}

@media (max-width: 767px) {
	.requests-wrapper {
		padding: 12px;
	}

	.requests-header h2 {
		font-size: 1.8rem;
	}

	.requests-header p {
		font-size: 1rem;
	}

	.filters-container {
		padding: 15px;
		margin-bottom: 30px;
	}

	.search-filters {
		grid-template-columns: 1fr;
		gap: 10px;
	}

	.search-box input,
	.filter-select {
		padding: 14px 20px 14px 50px;
		font-size: 1rem;
	}

	.search-box i {
		left: 18px;
	}

	.request-card {
		height: auto;
		min-height: 320px;
	}

	.details-grid {
		grid-template-columns: 1fr;
		gap: 12px;
	}

	.card-footer {
		flex-direction: column;
		gap: 15px;
		align-items: stretch;
		padding: 15px;
	}

	.btn-view-details {
		width: 100%;
		justify-content: center;
		padding: 14px 20px;
	}

	.card-header {
		padding: 20px;
	}

	.no-requests {
		padding: 60px 15px;
	}

	.no-requests-icon {
		font-size: 4rem;
	}

	.no-requests h3 {
		font-size: 1.8rem;
	}

	.no-requests p {
		font-size: 1.1rem;
	}
}

@media (max-width: 480px) {
	.requests-wrapper {
		padding: 10px;
	}

	.requests-header h2 {
		font-size: 1.6rem;
	}

	.requests-header p {
		font-size: 0.95rem;
	}

	.product-name {
		font-size: 1.4rem;
	}

	.product-description {
		font-size: 0.95rem;
	}

	.detail-text p {
		font-size: 1rem;
	}

	.date-warning {
		font-size: 0.85rem;
		padding: 10px 14px;
	}

	.card-badge {
		top: 15px;
		right: 15px;
		padding: 6px 15px;
		font-size: 0.75rem;
	}
}

@media (min-width: 1000px) {
	.requests-container {
		grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
	}
}
</style>
@endsection

@section('content')
<div class="requests-wrapper">
	<div class="requests-header">
		<h2>Buyer Product Requests</h2>
		<p>Browse product requests from buyers and find opportunities to fulfill their needs</p>
	</div>

	<div class="filters-container">
		<div class="search-filters">
			<div class="search-box">
				<i class="fas fa-search"></i>
				<input type="text" id="searchInput" placeholder="Search products...">
			</div>

			<select class="filter-select" id="sortFilter">
				<option value="newest">Newest First</option>
				<option value="nearest_date">Nearest Date First</option>
				<option value="highest_quantity">Highest Quantity</option>
			</select>
		</div>
	</div>

	<div class="requests-container" id="requestsContainer">
		@if($requests->isEmpty())
		<div class="no-requests">
			<div class="no-requests-icon">
				<i class="fas fa-clipboard-list"></i>
			</div>
			<h3>No Requests Available</h3>
			<p>There are currently no product requests from buyers. Check back later!</p>
		</div>
		@else
		@foreach($requests as $request)
		@php
			$daysLeft = \Carbon\Carbon::parse($request->needed_date)->diffInDays(now(), false);
			$isUrgent = $daysLeft >= -3 && $daysLeft <= 0;
			$isWithinMonth = $daysLeft >= -30 && $daysLeft <= 0;
			$buyerInfo = DB::table('buyers')->where('id', $request->buyer_id)->first();
			$status = $isUrgent ? 'urgent' : ($isWithinMonth ? 'active' : 'active');
		@endphp

		<div class="request-card"
			 data-product="{{ strtolower($request->product_name) }}"
			 data-status="{{ $status }}"
			 data-date="{{ $request->needed_date }}"
			 data-quantity="{{ $request->needed_quantity }}">
			<div class="card-badge {{ $isUrgent ? 'badge-urgent' : 'badge-active' }}">
				{{ $isUrgent ? 'Urgent' : 'Active' }}
			</div>

			<div class="card-header">
				<h3 class="product-name">{{ $request->product_name }}</h3>
				@if($request->description)
				<p class="product-description">{{ Str::limit($request->description, 120) }}</p>
				@endif

				<div class="details-grid">
					<div class="detail-item">
						<div class="detail-icon">
							<i class="fas fa-balance-scale"></i>
						</div>
						<div class="detail-text">
							<h4>Quantity Needed</h4>
							<p>{{ number_format($request->needed_quantity, 2) }} {{ $request->unit_of_measure }}</p>
						</div>
					</div>

					<div class="detail-item">
						<div class="detail-icon">
							<i class="fas fa-calendar-day"></i>
						</div>
						<div class="detail-text">
							<h4>Needed By</h4>
							<p>{{ \Carbon\Carbon::parse($request->needed_date)->format('M d, Y') }}</p>
						</div>
					</div>
				</div>

				@if($isUrgent && $daysLeft >= 0)
				<div class="date-warning">
					<i class="fas fa-exclamation-circle"></i>
					<span>Urgent: Needed in {{ abs($daysLeft) }} day{{ abs($daysLeft) != 1 ? 's' : '' }}</span>
				</div>
				@endif
			</div>

			<div class="card-footer">
				<div class="requested-date">
					<i class="fas fa-clock"></i>
					<span>{{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}</span>
				</div>

				<button class="btn-view-details" onclick="viewRequestDetails({{ $request->id }})">
					<i class="fas fa-eye"></i>
					View Details
				</button>
			</div>
		</div>
		@endforeach
		@endif
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function viewRequestDetails(requestId) {
	try {
		const response = await fetch(`/farmer/product-requests/${requestId}/details`);
		const data = await response.json();

		if (data.success) {
			const request = data.request;
			const buyer = data.buyer;

			const neededDate = new Date(request.needed_date);
			const today = new Date();
			const daysLeft = Math.ceil((neededDate - today) / (1000 * 60 * 60 * 24));
			const isUrgent = daysLeft <= 3 && daysLeft >= 0;

			let imageHtml = '<div style="text-align: center; padding: 25px; background: linear-gradient(135deg, #f6f8fa, #e5e7eb); border-radius: 12px; margin: 20px 0;">' +
						  '<i class="fas fa-image" style="font-size: 4rem; color: #6b7280; opacity: 0.3;"></i>' +
						  '<p style="margin-top: 15px; color: #6b7280; font-weight: 500;">No image provided</p></div>';

			if (request.product_image) {
				imageHtml = `<img src="${request.image_url}" alt="${request.product_name}" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 12px; margin: 20px 0; border: 1px solid #e5e7eb;">`;
			}

			const buyerInfo = buyer ? `
				<div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(5, 150, 105, 0.03)); padding: 20px; border-radius: 12px; margin: 25px 0; border: 1px solid rgba(16, 185, 129, 0.1);">
					<h4 style="margin: 0 0 15px 0; color: #10B981; display: flex; align-items: center; gap: 10px; font-weight: 700;">
						<i class="fas fa-user" style="background: #10B981; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;"></i>
						Buyer Information
					</h4>
					<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
						<div style="background: white; padding: 15px; border-radius: 10px; border: 1px solid #e5e7eb;">
							<small style="color: #6b7280; display: block; font-weight: 600; margin-bottom: 5px;">Name</small>
							<p style="margin: 0; font-weight: 700; color: #0f1724;">${buyer.name}</p>
						</div>
						<div style="background: white; padding: 15px; border-radius: 10px; border: 1px solid #e5e7eb;">
							<small style="color: #6b7280; display: block; font-weight: 600; margin-bottom: 5px;">Business Type</small>
							<p style="margin: 0; font-weight: 700; color: #0f1724;">${buyer.business_type ? buyer.business_type.charAt(0).toUpperCase() + buyer.business_type.slice(1) : 'Individual'}</p>
						</div>
						${buyer.primary_mobile ? `<div style="grid-column: 1 / -1;">
							<small style="color: #6b7280; display: block; font-weight: 600; margin-bottom: 5px;">Contact Number</small>
							<p style="margin: 0; font-weight: 700; color: #0f1724; font-size: 1.1rem;">${buyer.primary_mobile}</p>
						</div>` : ''}
						${buyer.residential_address ? `<div style="grid-column: 1 / -1;">
							<small style="color: #6b7280; display: block; font-weight: 600; margin-bottom: 5px;">Address</small>
							<p style="margin: 0; font-weight: 600; color: #0f1724; line-height: 1.5;">${buyer.residential_address}</p>
						</div>` : ''}
					</div>
				</div>
			` : '<div style="text-align: center; padding: 20px; color: #6b7280; font-style: italic;">Buyer information not available</div>';

			Swal.fire({
				title: `<span style="color: #0f1724; font-weight: 800;">${request.product_name}</span>`,
				html: `
					<div style="text-align: left;">
						${imageHtml}

						<div style="margin: 25px 0;">
							${request.description ? `<div style="background: #f9fafb; padding: 20px; border-radius: 12px; border-left: 5px solid #10B981;">
								<h4 style="color: #0f1724; margin: 0 0 10px 0; font-weight: 700; display: flex; align-items: center; gap: 10px;">
									<i class="fas fa-align-left" style="color: #10B981;"></i>
									Product Description
								</h4>
								<p style="color: #4b5563; line-height: 1.6; margin: 0;">${request.description}</p>
							</div>` : ''}
						</div>

						<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin: 30px 0;">
							<div style="background: linear-gradient(135deg, white, #f9fafb); padding: 20px; border-radius: 12px; border: 2px solid #e5e7eb; transition: all 0.3s ease; cursor: default;"
								 onmouseover="this.style.borderColor='#10B981'; this.style.transform='translateY(-3px)'"
								 onmouseout="this.style.borderColor='#e5e7eb'; this.style.transform='translateY(0)'">
								<small style="color: #6b7280; display: block; font-weight: 600; margin-bottom: 8px;">Quantity Needed</small>
								<p style="margin: 5px 0; font-size: 1.4rem; font-weight: 800; color: #0f1724;">
									${parseFloat(request.needed_quantity).toFixed(2)} ${request.unit_of_measure}
								</p>
							</div>

							<div style="background: linear-gradient(135deg, white, #f9fafb); padding: 20px; border-radius: 12px; border: 2px solid ${isUrgent ? '#ef4444' : '#e5e7eb'}; transition: all 0.3s ease; cursor: default;"
								 onmouseover="this.style.borderColor='${isUrgent ? '#dc2626' : '#10B981'}'; this.style.transform='translateY(-3px)'"
								 onmouseout="this.style.borderColor='${isUrgent ? '#ef4444' : '#e5e7eb'}'; this.style.transform='translateY(0)'">
								<small style="color: #6b7280; display: block; font-weight: 600; margin-bottom: 8px;">Needed By</small>
								<p style="margin: 5px 0; font-size: 1.4rem; font-weight: 800; color: ${isUrgent ? '#ef4444' : '#0f1724'};">
									${neededDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
									${isUrgent ? `<br><span style="color: #ef4444; font-size: 0.9rem; font-weight: 700; display: block; margin-top: 5px;">
										<i class="fas fa-exclamation-circle"></i> ${daysLeft} day${daysLeft !== 1 ? 's' : ''} left
									</span>` : ''}
								</p>
							</div>

							<div style="background: linear-gradient(135deg, white, #f9fafb); padding: 20px; border-radius: 12px; border: 2px solid #e5e7eb; transition: all 0.3s ease; cursor: default;"
								 onmouseover="this.style.borderColor='#10B981'; this.style.transform='translateY(-3px)'"
								 onmouseout="this.style.borderColor='#e5e7eb'; this.style.transform='translateY(0)'">
								<small style="color: #6b7280; display: block; font-weight: 600; margin-bottom: 8px;">Expected Price</small>
								<p style="margin: 5px 0; font-size: 1.4rem; font-weight: 800; color: #0f1724;">
									${request.unit_price ? `Rs. ${parseFloat(request.unit_price).toFixed(2)}/${request.unit_of_measure}` : 'Negotiable'}
								</p>
							</div>

							<div style="background: linear-gradient(135deg, white, #f9fafb); padding: 20px; border-radius: 12px; border: 2px solid #e5e7eb; transition: all 0.3s ease; cursor: default;"
								 onmouseover="this.style.borderColor='#10B981'; this.style.transform='translateY(-3px)'"
								 onmouseout="this.style.borderColor='#e5e7eb'; this.style.transform='translateY(0)'">
								<small style="color: #6b7280; display: block; font-weight: 600; margin-bottom: 8px;">Requested On</small>
								<p style="margin: 5px 0; font-size: 1.4rem; font-weight: 800; color: #0f1724;">
									${new Date(request.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
								</p>
							</div>
						</div>

						${buyerInfo}
					</div>
				`,
				width: '900px',
				padding: '40px',
				showCloseButton: true,
				showConfirmButton: false,
				customClass: {
					popup: 'animated zoomIn',
					title: 'request-modal-title'
				},
				background: 'var(--body-bg)'
			});

		} else {
			Swal.fire({
				icon: 'error',
				title: 'Error!',
				text: data.message || 'Failed to load request details',
				confirmButtonColor: '#ef4444',
				background: 'var(--card-bg)',
				color: 'var(--text-color)'
			});
		}

	} catch (error) {
		Swal.fire({
			icon: 'error',
			title: 'Error!',
			text: 'Failed to load request details. Please try again.',
			confirmButtonColor: '#ef4444',
			background: 'var(--card-bg)',
			color: 'var(--text-color)'
		});
	}
}

document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('searchInput');
	const sortFilter = document.getElementById('sortFilter');
	const requestCards = document.querySelectorAll('.request-card');

	function filterRequests() {
		const searchTerm = searchInput.value.toLowerCase();
		const sortValue = sortFilter.value;

		let visibleCards = Array.from(requestCards);

		visibleCards = visibleCards.filter(card => {
			const productName = card.getAttribute('data-product');
			const status = card.getAttribute('data-status');
			const date = new Date(card.getAttribute('data-date'));
			const today = new Date();
			const daysDiff = Math.ceil((date - today) / (1000 * 60 * 60 * 24));
			const isWithinMonth = daysDiff >= -30 && daysDiff <= 0;

			const matchesSearch = productName.includes(searchTerm);
			let matchesStatus = false;

			if (statusValue === 'all') {
				matchesStatus = true;
			} else if (statusValue === 'urgent') {
				const isUrgent = daysDiff >= -3 && daysDiff <= 0;
				matchesStatus = isUrgent;
			} else if (statusValue === 'active') {
				matchesStatus = status === 'active';
			} else if (statusValue === '1month') {
				matchesStatus = isWithinMonth;
			}

			return matchesSearch && matchesStatus;
		});

		visibleCards.sort((a, b) => {
			if (sortValue === 'newest') {
				const dateA = new Date(a.getAttribute('data-date'));
				const dateB = new Date(b.getAttribute('data-date'));
				return dateB - dateA;
			} else if (sortValue === 'nearest_date') {
				const dateA = new Date(a.getAttribute('data-date'));
				const dateB = new Date(b.getAttribute('data-date'));
				return dateA - dateB;
			} else if (sortValue === 'highest_quantity') {
				const qtyA = parseFloat(a.getAttribute('data-quantity'));
				const qtyB = parseFloat(b.getAttribute('data-quantity'));
				return qtyB - qtyA;
			}
			return 0;
		});

		requestCards.forEach(card => {
			card.style.display = 'none';
			card.style.order = '0';
		});

		visibleCards.forEach((card, index) => {
			card.style.display = 'flex';
			card.style.order = index;
			setTimeout(() => {
				card.style.opacity = '1';
				card.style.transform = 'translateY(0)';
			}, index * 50);
		});

		const container = document.getElementById('requestsContainer');
		let noResults = container.querySelector('.no-requests:not(.permanent)');

		if (visibleCards.length === 0) {
			if (!noResults) {
				noResults = document.createElement('div');
				noResults.className = 'no-requests';
				noResults.innerHTML = `
					<div class="no-requests-icon">
						<i class="fas fa-search"></i>
					</div>
					<h3>No Matching Requests</h3>
					<p>Try adjusting your search criteria</p>
				`;
				container.appendChild(noResults);
			}
		} else {
			if (noResults) {
				noResults.remove();
			}
		}
	}

	searchInput.addEventListener('input', filterRequests);
	sortFilter.addEventListener('change', filterRequests);

	requestCards.forEach((card, index) => {
		card.style.animationDelay = `${index * 0.1}s`;

		card.addEventListener('click', function(e) {
			if (!e.target.closest('.btn-view-details')) {
				const requestId = this.getAttribute('data-id');
				if (requestId) {
					viewRequestDetails(requestId);
				}
			}
		});
	});

	filterRequests();
});
</script>
@endsection

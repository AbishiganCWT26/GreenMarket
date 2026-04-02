@extends('facilitator.layouts.facilitator_master')

@section('title', 'Sales Records')
@section('page-title', 'Sales Records')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/sales_index.css') }}">

@endsection

@section('content')
<div class="sales-container">
	<div class="header-section">
		<div class="header-left">
			<div class="header-icon">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M21 11.5V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V11.5" stroke="currentColor" stroke-width="2"/>
					<path d="M12 4V14M12 14L15 11M12 14L9 11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					<rect x="2" y="8" width="20" height="3" stroke="currentColor" stroke-width="2"/>
				</svg>
			</div>
			<div class="header-text">
				<h1>Sales Records</h1>
				<p>Track all completed orders</p>
			</div>
		</div>
	</div>

	<div class="stats-panel">
		<div class="stat-card">
			<div class="stat-icon sales">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<rect x="2" y="7" width="20" height="14" rx="2"/>
					<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
				</svg>
			</div>
			<div class="stat-content">
				<span class="stat-value">{{ number_format($totalSalesCount) }}</span>
				<span class="stat-label">Total Sales</span>
			</div>
		</div>
		<div class="stat-card">
			<div class="stat-icon revenue">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<line x1="12" y1="1" x2="12" y2="23"/>
					<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
				</svg>
			</div>
			<div class="stat-content">
				<span class="stat-value">LKR {{ number_format($totalRevenue, 2) }}</span>
				<span class="stat-label">Total Revenue</span>
			</div>
		</div>
	</div>

	<div class="filter-section">
		<form action="{{ route('facilitator.sales') }}" method="GET" id="searchForm">
			<input type="hidden" name="view" id="viewInput" value="{{ request('view', 'card') }}">
			<input type="hidden" name="per_page" id="perPageInput" value="{{ request('per_page') }}">

			<div class="search-box">
				<div class="search-wrapper">
					<input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search order, buyer, group..." autocomplete="off">
					@if(request('search'))
						<a href="{{ route('facilitator.sales', array_merge(request()->except('search'), ['search' => null])) }}" class="search-clear" title="Clear search">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<line x1="18" y1="6" x2="6" y2="18"></line>
								<line x1="6" y1="6" x2="18" y2="18"></line>
							</svg>
						</a>
					@endif
					<button type="submit">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<circle cx="11" cy="11" r="8"/>
							<line x1="21" y1="21" x2="16.65" y2="16.65"/>
						</svg>
					</button>
				</div>
			</div>

			<div class="filter-grid">
				<div class="filter-item">
					<label>From Date</label>
					<input type="date" name="start_date" value="{{ request('start_date') }}" class="filter-input" onchange="this.form.submit()">
				</div>
				<div class="filter-item">
					<label>To Date</label>
					<input type="date" name="end_date" value="{{ request('end_date') }}" class="filter-input" onchange="this.form.submit()">
				</div>
				<div class="filter-item">
					<a href="{{ route('facilitator.sales') }}" class="reset-btn">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="18" y1="6" x2="6" y2="18"/>
							<line x1="6" y1="6" x2="18" y2="18"/>
						</svg>
						Reset
					</a>
				</div>
			</div>
		</form>
	</div>

	<div class="controls-bar">
		<div class="view-switcher">
			<button class="view-option {{ request('view', 'card') == 'card' ? 'active' : '' }}" onclick="updateView('card')">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<rect x="3" y="3" width="7" height="7"/>
					<rect x="14" y="3" width="7" height="7"/>
					<rect x="3" y="14" width="7" height="7"/>
					<rect x="14" y="14" width="7" height="7"/>
				</svg>
				<span>Cards</span>
			</button>
			<button class="view-option {{ request('view') == 'table' ? 'active' : '' }}" onclick="updateView('table')">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<line x1="3" y1="6" x2="21" y2="6"/>
					<line x1="3" y1="12" x2="21" y2="12"/>
					<line x1="3" y1="18" x2="21" y2="18"/>
				</svg>
				<span>Table</span>
			</button>
		</div>
		<div class="range-info">
			@if($sales->total() > 0)
				{{ $sales->firstItem() }}-{{ $sales->lastItem() }} of {{ $sales->total() }}
			@else
				No records
			@endif
		</div>
	</div>

	@if(request('view', 'card') == 'card')
	<div class="card-view" id="cardView">
		<div class="cards-layout">
			@forelse($sales as $index => $sale)
			<div class="sale-card" data-id="{{ $sale->id }}" style="animation-delay: {{ $index * 0.05 }}s">
				<div class="card-header">
					<div class="order-info">
						<span class="order-number">#{{ $sale->order_number }}</span>
						<span class="order-date">
							<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
								<line x1="16" y1="2" x2="16" y2="6"/>
								<line x1="8" y1="2" x2="8" y2="6"/>
								<line x1="3" y1="10" x2="21" y2="10"/>
							</svg>
							{{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y') }}
						</span>
					</div>
					<div class="status-wrapper">
						<span class="status-indicator status-{{ $sale->order_status }}">{{ $sale->order_status }}</span>
					</div>
				</div>

				<div class="card-content">
					<div class="info-row">
						<div class="info-label">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
								<circle cx="12" cy="7" r="4"/>
							</svg>
							<span>Buyer</span>
						</div>
						<div class="info-value">
							<img src="{{ $sale->buyer_photo ? asset('uploads/profile_pictures/' . $sale->buyer_photo) : asset('assets/images/farmer.png') }}" 
								 class="avatar-xs" 
								 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
							<div class="person-info-column">
								<span class="person-name">{{ $sale->buyer_name }}</span>
								<span class="person-subtext">{{ $sale->buyer_district }}</span>
							</div>
						</div>
					</div>

					<div class="info-row">
						<div class="info-label">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
								<polyline points="9 22 9 12 15 12 15 22"/>
							</svg>
							<span>Group</span>
						</div>
						<div class="info-value">
							<img src="{{ $sale->lead_farmer_photo ? asset('uploads/profile_pictures/' . $sale->lead_farmer_photo) : asset('assets/images/farmer.png') }}" 
								 class="avatar-xs" 
								 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
							{{ $sale->group_name }}
						</div>
					</div>

					<div class="info-row">
						<div class="info-label">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
								<circle cx="12" cy="7" r="4"/>
							</svg>
							<span>Lead</span>
						</div>
						<div class="info-value">{{ $sale->lead_farmer_name }}</div>
					</div>
				</div>

				<div class="card-footer">
					<div class="amount-wrapper">
						<span class="amount-label">Total</span>
						<span class="amount-value">LKR {{ number_format($sale->total_amount, 2) }}</span>
					</div>
					<button class="action-btn" onclick="showSaleDetails({{ $sale->id }})">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<circle cx="12" cy="12" r="3"/>
							<path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7z"/>
						</svg>
						<span>View</span>
					</button>
				</div>
			</div>
			@empty
			<div class="empty-state">
				<div class="empty-icon">
					<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
						<rect x="2" y="7" width="20" height="14" rx="2"/>
						<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
					</svg>
				</div>
				<h3>No Sales Found</h3>
				<p>No records match your search criteria</p>
			</div>
			@endforelse
		</div>
	</div>
	@else
	<div class="table-view" id="tableView">
		<div class="table-responsive">
			<table class="modern-table">
				<thead>
					<tr>
						<th>Order</th>
						<th>Date</th>
						<th>Buyer</th>
						<th>Group</th>
						<th>Amount</th>
						<th>Status</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@forelse($sales as $sale)
					<tr class="table-row" data-id="{{ $sale->id }}">
						<td class="order-cell">
							<span class="order-code">#{{ $sale->order_number }}</span>
						</td>
						<td class="date-cell">
							{{ \Carbon\Carbon::parse($sale->created_at)->format('Y-m-d') }}
						</td>
						<td class="buyer-cell">
							<div class="person-wrapper">
								<img src="{{ $sale->buyer_photo ? asset('uploads/profile_pictures/' . $sale->buyer_photo) : asset('assets/images/farmer.png') }}" 
									 class="person-avatar" 
									 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
								<div class="person-details">
									<span class="person-name">{{ $sale->buyer_name }}</span>
									<span class="person-location">{{ $sale->buyer_district }}</span>
								</div>
							</div>
						</td>
						<td class="group-cell">
							<div class="group-wrapper">
								<img src="{{ $sale->lead_farmer_photo ? asset('uploads/profile_pictures/' . $sale->lead_farmer_photo) : asset('assets/images/farmer.png') }}" 
									 class="person-avatar" 
									 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
								<div class="group-details">
									<span class="group-name">{{ $sale->group_name }}</span>
									<span class="lead-name">{{ $sale->lead_farmer_name }}</span>
								</div>
							</div>
						</td>
						<td class="amount-cell">
							<span class="amount-text">LKR {{ number_format($sale->total_amount, 2) }}</span>
						</td>
						<td class="status-cell">
							<span class="status-indicator status-{{ $sale->order_status }}">{{ $sale->order_status }}</span>
						</td>
						<td class="action-cell">
							<button class="icon-btn" onclick="showSaleDetails({{ $sale->id }})">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<circle cx="12" cy="12" r="3"/>
									<path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7z"/>
								</svg>
							</button>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="7" class="empty-table-cell">
							<div class="empty-state small">
								<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
									<rect x="2" y="7" width="20" height="14" rx="2"/>
									<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
								</svg>
								<p>No sales records found</p>
							</div>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
	@endif

	<div class="pagination-footer">
		{{ $sales->appends(request()->query())->links('vendor.pagination.compact') }}
	</div>
</div>
@endsection

@section('scripts')

<script>
function getPerPage(view) {
	const w = window.innerWidth;
	if (view === 'card') {
		if (w >= 2560) return 12;
		if (w >= 1500) return 12;
		if (w >= 1200) return 9;
		if (w >= 992) return 6;
		if (w >= 768) return 4;
		return 3;
	} else {
		if (w >= 2560) return 15;
		if (w >= 1500) return 15;
		if (w >= 1200) return 10;
		if (w >= 992) return 10;
		if (w >= 768) return 10;
		return 5;
	}
}

function updateView(view) {
	const perPage = getPerPage(view);
	const url = new URL(window.location.href);
	url.searchParams.set('view', view);
	url.searchParams.set('per_page', perPage);
	window.location.href = url.toString();
}

window.addEventListener('load', function() {
	const url = new URL(window.location.href);
	const currentView = url.searchParams.get('view') || 'card';
	const expectedPerPage = getPerPage(currentView);
	
	if (!url.searchParams.get('per_page') || url.searchParams.get('per_page') != expectedPerPage) {
		url.searchParams.set('view', currentView);
		url.searchParams.set('per_page', expectedPerPage);
		window.location.replace(url.toString());
	}

	@if(session('success'))
	Swal.fire({
		@if(file_exists(public_path('assets/icons/Gif/success3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
		title: 'Success!',
		text: '{{ session('success') }}',
		timer: 3000,
		showConfirmButton: false,
		background: '#ffffff',
		iconColor: '#10B981'
	});
	@endif

	@if(session('error'))
	Swal.fire({
		@if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
		title: 'Error!',
		text: '{{ session('error') }}',
		timer: 3000,
		showConfirmButton: false,
		background: '#ffffff',
		iconColor: '#ef4444'
	});
	@endif
});

async function showSaleDetails(id) {
	try {
		const response = await fetch(`/facilitator/sales/${id}/details`);
		const data = await response.json();
		
		if (!data.success) throw new Error(data.message);
		
		const order = data.order;
		const buyer = order.buyer;
		const farmer = order.farmer;
		const leadFarmer = order.lead_farmer;
		const payments = order.payments && order.payments.length > 0 ? order.payments[0] : null;
		
		const profilePlaceholder = "{{ asset('assets/images/farmer.png') }}";
		const itemPlaceholder = "{{ asset('assets/images/no-image.png') }}";
		
		let itemsHtml = '';
		order.order_items.forEach(item => {
			itemsHtml += `
				<div class="detail-item-row">
					<img src="${item.product && item.product.product_photo ? '/uploads/product_images/' + item.product.product_photo : itemPlaceholder}" 
						 class="detail-item-img" onerror="this.src='${itemPlaceholder}'">
					<div class="detail-item-info">
						<div class="detail-item-name">${item.product_name_snapshot}</div>
						<div class="detail-item-cats">
							<span>${item.product && item.product.category ? item.product.category.category_name : 'No Category'}</span> • 
							<span>${item.product && item.product.subcategory ? item.product.subcategory.subcategory_name : 'No Subcategory'}</span>
						</div>
						<div class="detail-item-meta">
							<span>Qty: <strong>${parseFloat(item.quantity_ordered)}</strong></span>
							<span>Price: <strong>LKR ${parseFloat(item.unit_price_snapshot).toLocaleString()}</strong></span>
						</div>
					</div>
					<div class="detail-item-total">LKR ${parseFloat(item.item_total).toLocaleString()}</div>
				</div>
			`;
		});

		Swal.fire({
			title: `<div class="swal-header">Sale Details #${order.order_number}</div>`,
			html: `
				<div class="swal-content">
					<div class="swal-section">
						<div class="swal-section-title">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<circle cx="12" cy="12" r="10"/>
								<line x1="12" y1="8" x2="12" y2="12"/>
								<line x1="12" y1="16" x2="12.01" y2="16"/>
							</svg>
							Order Overview
						</div>
						<div class="overview-panel">
							<div class="overview-item">
								<label>Status</label>
								<span class="status-indicator status-${order.order_status}">${order.order_status}</span>
							</div>
							<div class="overview-item">
								<label>Date</label>
								<span>${new Date(order.created_at).toLocaleDateString()}</span>
							</div>
							<div class="overview-item">
								<label>Total</label>
								<span class="overview-total">LKR ${parseFloat(order.total_amount).toLocaleString()}</span>
							</div>
						</div>
					</div>

					<div class="swal-row">
						<div class="swal-col">
							<div class="swal-section">
								<div class="swal-section-title">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
										<circle cx="12" cy="7" r="4"/>
									</svg>
									Buyer
								</div>
								<div class="user-block">
									<img src="${buyer && buyer.user && buyer.user.profile_photo ? '/uploads/profile_pictures/' + buyer.user.profile_photo : profilePlaceholder}" 
										 class="user-block-avatar" onerror="this.src='${profilePlaceholder}'">
									<div class="user-block-info">
										<div class="user-block-name">${buyer ? buyer.name : 'N/A'}</div>
										<div class="user-block-detail"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="2" width="14" height="20" rx="2"/></svg> ${buyer ? buyer.primary_mobile : 'N/A'}</div>
										<div class="user-block-detail"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> ${buyer ? buyer.residential_address : 'N/A'}</div>
										<div class="user-block-detail"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7h18M3 14h18M12 21V7"/></svg> District: ${buyer ? buyer.district : 'N/A'}</div>
									</div>
								</div>
							</div>
						</div>
						<div class="swal-col">
							<div class="swal-section">
								<div class="swal-section-title">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
										<circle cx="12" cy="7" r="4"/>
									</svg>
									Farmer
								</div>
								<div class="user-block">
									<img src="${farmer && farmer.user && farmer.user.profile_photo ? '/uploads/profile_pictures/' + farmer.user.profile_photo : profilePlaceholder}" 
										 class="user-block-avatar" onerror="this.src='${profilePlaceholder}'">
									<div class="user-block-info">
										<div class="user-block-name">${farmer ? farmer.name : 'N/A'}</div>
										<div class="user-block-detail"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="2" width="14" height="20" rx="2"/></svg> ${farmer ? farmer.primary_mobile : 'N/A'}</div>
										<div class="user-block-detail"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> ${farmer ? farmer.residential_address : 'N/A'}</div>
										<div class="user-block-detail"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2"/></svg> GN: ${farmer ? farmer.grama_niladhari_division : 'N/A'}</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="swal-section">
						<div class="swal-section-title">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
								<polyline points="9 22 9 12 15 12 15 22"/>
							</svg>
							Lead Farmer Group
						</div>
						<div class="lead-block">
							<img src="${leadFarmer && leadFarmer.user && leadFarmer.user.profile_photo ? '/uploads/profile_pictures/' + leadFarmer.user.profile_photo : profilePlaceholder}" 
								 class="lead-block-avatar" onerror="this.src='${profilePlaceholder}'">
							<div class="lead-block-info">
								<div class="lead-block-name">${leadFarmer ? leadFarmer.group_name : 'N/A'} <span>(${leadFarmer ? leadFarmer.group_number : 'N/A'})</span></div>
								<div class="lead-block-meta">
									<span><strong>Lead:</strong> ${leadFarmer ? leadFarmer.name : 'N/A'}</span>
									<span><strong>Contact:</strong> ${leadFarmer ? leadFarmer.primary_mobile : 'N/A'}</span>
								</div>
							</div>
						</div>
					</div>

					<div class="swal-section">
						<div class="swal-section-title">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<rect x="2" y="7" width="20" height="14" rx="2"/>
								<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
							</svg>
							Order Items
						</div>
						<div class="items-list">
							${itemsHtml}
						</div>
					</div>

					<div class="swal-row">
						<div class="swal-col">
							<div class="swal-section">
								<div class="swal-section-title">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<rect x="1" y="4" width="22" height="16" rx="2"/>
										<line x1="1" y1="8" x2="23" y2="8"/>
									</svg>
									Payment
								</div>
								<div class="payment-block">
									<div class="payment-row">
										<label>Method</label>
										<span>${payments ? payments.payment_method : 'N/A'}</span>
									</div>
									<div class="payment-row">
										<label>Status</label>
										<span class="pay-status ${payments ? payments.payment_status : ''}">${payments ? payments.payment_status : 'Pending'}</span>
									</div>
									<div class="payment-row">
										<label>Date</label>
										<span>${payments && payments.payment_date ? new Date(payments.payment_date).toLocaleDateString() : 'N/A'}</span>
									</div>
									<div class="payment-row">
										<label>Amount</label>
										<span class="pay-amount">${payments ? ('LKR ' + parseFloat(payments.amount).toLocaleString()) : 'N/A'}</span>
									</div>
									<div class="payment-row">
										<label>Reference</label>
										<span class="pay-ref">${payments ? (payments.payment_reference || 'N/A') : 'N/A'}</span>
									</div>
								</div>
							</div>
						</div>
						<div class="swal-col">
							<div class="swal-section">
								<div class="swal-section-title">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<rect x="2" y="2" width="20" height="20" rx="2.18"/>
										<line x1="8" y1="2" x2="8" y2="22"/>
										<line x1="16" y1="2" x2="16" y2="22"/>
									</svg>
									Pickup Location
								</div>
								<div class="pickup-block">
									<div class="pickup-address">${order.order_items[0] && order.order_items[0].product ? order.order_items[0].product.pickup_address : 'N/A'}</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			`,
			showConfirmButton: false,
			showCloseButton: true,
			width: '900px',
			padding: '0',
			background: '#f8fafc',
			customClass: {
				popup: 'swal-modern',
				htmlContainer: 'swal-no-pad'
			}
		});
	} catch (error) {
		Swal.fire({
			@if(file_exists(public_path('assets/icons/Gif/Failed2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Failed2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
			title: 'Error',
			text: 'Failed to fetch sale details: ' + error.message,
			background: '#ffffff',
			iconColor: '#ef4444'
		});
	}
}
</script>
@endsection

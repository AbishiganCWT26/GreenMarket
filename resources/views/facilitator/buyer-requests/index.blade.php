@extends('facilitator.layouts.facilitator_master')

@section('title', 'Buyer Requests')
@section('page-title', 'Buyer Product Requests')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/buyer_requests_index.css') }}">
@endsection

@section('content')
<div class="request-app">
	<div class="app-bar">
		<div class="bar-left">
			<div class="bar-icon">
				<i class="fas fa-hand-holding-heart"></i>
			</div>
			<div class="bar-text">
				<h2 class="bar-title">Buyer Requests</h2>
				<p class="bar-subtitle">Product requests from buyers</p>
			</div>
		</div>
	</div>

	<form action="{{ route('facilitator.buyer-requests') }}" method="GET" id="searchForm">
		<input type="hidden" name="view" id="viewInput" value="{{ request('view', 'card') }}">
		<input type="hidden" name="per_page" id="perPageInput" value="{{ request('per_page') }}">
		
		<div class="search-container">
			<input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product or buyer..." autocomplete="off">
			<button type="submit"><i class="fas fa-search"></i></button>
		</div>
	</form>

	<div class="toolbar">
		<div class="view-tabs">
			<button class="view-tab {{ request('view', 'card') == 'card' ? 'active' : '' }}" onclick="updateView('card')">
				<i class="fas fa-th"></i> Cards
			</button>
			<button class="view-tab {{ request('view') == 'table' ? 'active' : '' }}" onclick="updateView('table')">
				<i class="fas fa-table"></i> Table
			</button>
		</div>
		<div class="info-text">
			@if($buyerRequests->total() > 0)
				{{ $buyerRequests->firstItem() }}-{{ $buyerRequests->lastItem() }} of {{ $buyerRequests->total() }}
			@else
				No requests
			@endif
		</div>
	</div>

	@if(request('view', 'card') == 'card')
	<div class="card-view" id="cardView">
		<div class="card-grid">
			@forelse($buyerRequests as $req)
			<div class="request-card" data-id="{{ $req->id }}">
				<div class="card-media">
					<img src="{{ $req->product_image ? asset('uploads/buyer_product_requests/' . $req->product_image) : asset('assets/images/product-placeholder.png') }}" 
						 alt="{{ $req->product_name }}"
						 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
					<div class="media-badge">
						<i class="fas fa-clock"></i> {{ $req->created_at->diffForHumans() }}
					</div>
				</div>
				<div class="card-body">
					<div class="buyer-row">
						<img src="{{ $req->buyer->user->profile_photo ? asset('uploads/profile_pictures/' . $req->buyer->user->profile_photo) : asset('assets/images/farmer.png') }}" 
							 class="buyer-avatar"
							 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
						<div class="buyer-info">
							<h4 class="buyer-name">{{ $req->buyer->name }}</h4>
							@if($req->buyer->business_name)
							<span class="business-name">{{ $req->buyer->business_name }}</span>
							@endif
						</div>
					</div>

					<h3 class="product-name">{{ $req->product_name }}</h3>

					<div class="detail-grid">
						<div class="detail-item">
							<span class="detail-label">Quantity</span>
							<span class="detail-value">{{ $req->needed_quantity }} {{ $req->unit_of_measure }}</span>
						</div>
						<div class="detail-item">
							<span class="detail-label">Budget</span>
							<span class="detail-value price">LKR {{ number_format($req->unit_price, 2) }}</span>
						</div>
						<div class="detail-item">
							<span class="detail-label">Needed</span>
							<span class="detail-value">{{ \Carbon\Carbon::parse($req->needed_date)->format('d M Y') }}</span>
						</div>
					</div>

					@if($req->description)
					<p class="product-desc">{{ $req->description }}</p>
					@endif
				</div>
			</div>
			@empty
			<div class="empty-box">
				<div class="empty-icon">
					<i class="fas fa-hand-holding-heart"></i>
				</div>
				<h3 class="empty-title">No Requests Found</h3>
				<p class="empty-desc">No buyer requests match your search</p>
			</div>
			@endforelse
		</div>
	</div>
	@else
	<div class="table-view" id="tableView">
		<div class="table-wrap">
			<table class="request-table">
				<thead>
					<tr>
						<th>Product</th>
						<th>Buyer</th>
						<th>Quantity</th>
						<th>Budget</th>
						<th>Needed By</th>
					</tr>
				</thead>
				<tbody>
					@forelse($buyerRequests as $req)
					<tr class="request-row" data-id="{{ $req->id }}">
						<td data-label="Product">
							<div class="cell-product">
								<img src="{{ $req->product_image ? asset('uploads/buyer_product_requests/' . $req->product_image) : asset('assets/images/product-placeholder.png') }}" 
									 class="cell-img"
									 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
								<div>
									<strong>{{ $req->product_name }}</strong>
									<small>{{ $req->created_at->diffForHumans() }}</small>
								</div>
							</div>
						</td>
						<td data-label="Buyer">
							<div class="cell-buyer">
								<img src="{{ $req->buyer->user->profile_photo ? asset('uploads/profile_pictures/' . $req->buyer->user->profile_photo) : asset('assets/images/farmer.png') }}" 
									 class="cell-avatar"
									 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
								<div>
									<strong>{{ $req->buyer->name }}</strong>
									@if($req->buyer->business_name)
									<small>{{ $req->buyer->business_name }}</small>
									@endif
								</div>
							</div>
						</td>
						<td data-label="Quantity">{{ $req->needed_quantity }} {{ $req->unit_of_measure }}</td>
						<td data-label="Budget" class="cell-price">LKR {{ number_format($req->unit_price, 2) }}</td>
						<td data-label="Needed By">{{ \Carbon\Carbon::parse($req->needed_date)->format('d M Y') }}</td>
					</tr>
					@empty
					<tr>
						<td colspan="5" class="text-center py-4 text-muted">No buyer requests found</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
	@endif

	<div class="pager">
		{{ $buyerRequests->appends(request()->query())->links('vendor.pagination.compact') }}
	</div>
</div>
@endsection

@section('scripts')
<script>
function getPerPage(view) {
	const w = window.innerWidth;
	if (view === 'card') {
		if (w >= 2560) return 12;
		if (w >= 1500) return 10;
		if (w >= 1200) return 8;
		if (w >= 992) return 6;
		if (w >= 760) return 4;
		return 3;
	} else {
		if (w >= 2560) return 15;
		if (w >= 1500) return 15;
		if (w >= 1200) return 10;
		if (w >= 992) return 10;
		if (w >= 760) return 10;
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
});
</script>
@endsection
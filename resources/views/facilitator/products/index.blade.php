@extends('facilitator.layouts.facilitator_master')

@section('title', 'Product Oversight')
@section('page-title', 'Product Oversight')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/products_index.css') }}">

@endsection

@section('content')
<div class="products-container">
	<div class="header-section">
		<div class="header-left">
			<div class="header-icon">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
					<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" stroke="currentColor" stroke-width="2"/>
				</svg>
			</div>
			<div class="header-text">
				<h1>Product Oversight</h1>
				<p>Manage and monitor all products</p>
			</div>
		</div>
	</div>

	<div class="filter-section">
		<form action="{{ route('facilitator.products') }}" method="GET" id="searchForm">
			<input type="hidden" name="view" id="viewInput" value="{{ request('view', 'card') }}">
			<input type="hidden" name="per_page" id="perPageInput" value="{{ request('per_page') }}">
			
			<div class="search-wrapper">
				<input type="text" name="search" value="{{ request('search') }}" placeholder="Search products, categories, groups..." autocomplete="off">
				@if(request('search'))
					<a href="{{ route('facilitator.products', array_merge(request()->except('search'), ['search' => null])) }}" class="search-clear" title="Clear search">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="18" y1="6" x2="6" y2="18"></line>
							<line x1="6" y1="6" x2="18" y2="18"></line>
						</svg>
					</a>
				@endif
				<button type="submit">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="11" cy="11" r="8"/>
						<line x1="21" y1="21" x2="16.65" y2="16.65"/>
					</svg>
					<span>Search</span>
				</button>
			</div>

			<div class="filter-grid">
				<div class="filter-item">
					<label>Lead Farmer Group</label>
					<select name="lead_farmer_id" class="filter-select" onchange="this.form.submit()">
						<option value="">All Groups</option>
						@foreach($leadFarmers as $lf)
							<option value="{{ $lf->id }}" {{ request('lead_farmer_id') == $lf->id ? 'selected' : '' }}>{{ $lf->group_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="filter-item">
					<label>Category</label>
					<select name="category_id" class="filter-select" onchange="this.form.submit()">
						<option value="">All Categories</option>
						@foreach($categories as $cat)
							<option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
						@endforeach
					</select>
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
			@if($products->total() > 0)
				{{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} products
			@else
				No products found
			@endif
		</div>
	</div>

	@if(request('view', 'card') == 'card')
	<div id="card-view">
		<div class="cards-grid">
			@forelse($products as $index => $product)
				<div class="product-card" data-id="{{ $product->id }}" style="animation-delay: {{ $index * 0.05 }}s">
					<div class="card-media">
						<img src="{{ $product->product_photo ? asset('uploads/product_images/' . $product->product_photo) : asset('assets/images/product-placeholder.png') }}" 
							 alt="{{ $product->product_name }}"
							 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
						<div class="media-badge">
							<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<circle cx="12" cy="12" r="10"/>
								<line x1="12" y1="8" x2="12" y2="16"/>
								<line x1="8" y1="12" x2="16" y2="12"/>
							</svg>
						</div>
					</div>

					<div class="card-content">
						<div class="product-header">
							<h3 class="product-title">{{ $product->product_name }}</h3>
							<div class="product-badges">
								<span class="badge category">{{ $product->category_name }}</span>
								<span class="badge grade">{{ $product->quality_grade }}</span>
							</div>
						</div>

						<div class="info-blocks">
							<div class="info-block">
								<div class="info-icon farmer">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
										<circle cx="12" cy="7" r="4"/>
									</svg>
								</div>
								<div class="info-text">
									<span class="info-label">Farmer</span>
									<span class="info-value">
										<img src="{{ $product->farmer_photo ? asset('uploads/profile_pictures/' . $product->farmer_photo) : asset('assets/images/farmer.png') }}" 
											 class="avatar-xs" 
											 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
										{{ $product->farmer_name }}
									</span>
								</div>
							</div>

							<div class="info-block">
								<div class="info-icon group">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
										<polyline points="9 22 9 12 15 12 15 22"/>
									</svg>
								</div>
								<div class="info-text">
									<span class="info-label">Group</span>
									<span class="info-value">
										<img src="{{ $product->lead_farmer_photo ? asset('uploads/profile_pictures/' . $product->lead_farmer_photo) : asset('assets/images/farmer.png') }}" 
											 class="avatar-xs" 
											 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
										{{ $product->lead_group_name }}
									</span>
								</div>
							</div>
						</div>

						<div class="product-stats">
							<div class="stat-row">
								<div class="stat-item">
									<span class="stat-label">Stock</span>
									<span class="stat-number">{{ $product->quantity }} {{ $product->unit_of_measure }}</span>
								</div>
								<div class="stat-item">
									<span class="stat-label">Price</span>
									<span class="stat-number price">LKR {{ number_format($product->selling_price, 2) }}</span>
								</div>
							</div>
						</div>

						<div class="card-actions">
							<button class="action-btn edit" onclick="openAlertModal({{ $product->id }}, '{{ $product->product_name }}')">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"/>
									<polygon points="18 2 22 6 12 16 8 16 8 12 18 2"/>
								</svg>
								<span>Edit</span>
							</button>
							<button class="action-btn view" onclick="viewProductDetails({{ json_encode($product) }})">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<circle cx="12" cy="12" r="3"/>
									<path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7z"/>
								</svg>
								<span>View</span>
							</button>
						</div>
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
					<h3>No Products Found</h3>
					<p>No products match your search criteria</p>
				</div>
			@endforelse
		</div>
	</div>
	@else
	<div id="table-view">
		<div class="table-responsive">
			<table class="modern-table">
				<thead>
					<tr>
						<th>Product</th>
						<th>Farmer / Group</th>
						<th>Category</th>
						<th>Stock</th>
						<th>Price</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@forelse($products as $product)
						<tr class="table-row" data-id="{{ $product->id }}">
							<td class="product-cell">
								<div class="product-wrapper">
									<img src="{{ $product->product_photo ? asset('uploads/product_images/' . $product->product_photo) : asset('assets/images/product-placeholder.png') }}" 
										 class="product-thumb"
										 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
									<div class="product-info">
										<span class="product-name">{{ $product->product_name }}</span>
										<span class="product-grade">{{ $product->quality_grade }}</span>
									</div>
								</div>
							</td>
							<td class="farmer-cell">
								<div class="farmer-wrapper">
									<div class="farmer-info">
										<img src="{{ $product->farmer_photo ? asset('uploads/profile_pictures/' . $product->farmer_photo) : asset('assets/images/farmer.png') }}" 
											 class="avatar-sm" 
											 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
										<span>{{ $product->farmer_name }}</span>
									</div>
									<div class="group-info">
										<img src="{{ $product->lead_farmer_photo ? asset('uploads/profile_pictures/' . $product->lead_farmer_photo) : asset('assets/images/farmer.png') }}" 
											 class="avatar-sm" 
											 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
										<span>{{ $product->lead_group_name }}</span>
									</div>
								</div>
							</td>
							<td class="category-cell">
								<span class="category-badge">{{ $product->category_name }}</span>
							</td>
							<td class="stock-cell">
								<span class="stock-value">{{ $product->quantity }} {{ $product->unit_of_measure }}</span>
							</td>
							<td class="price-cell">
								<span class="price-value">LKR {{ number_format($product->selling_price, 2) }}</span>
							</td>
							<td class="actions-cell">
								<div class="action-group">
									<button class="icon-btn edit" onclick="openAlertModal({{ $product->id }}, '{{ $product->product_name }}')" title="Edit">
										<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"/>
											<polygon points="18 2 22 6 12 16 8 16 8 12 18 2"/>
										</svg>
									</button>
									<button class="icon-btn view" onclick="viewProductDetails({{ json_encode($product) }})" title="View Details">
										<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<circle cx="12" cy="12" r="3"/>
											<path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7z"/>
										</svg>
									</button>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="empty-table-cell">
								<div class="empty-state small">
									<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
										<rect x="2" y="7" width="20" height="14" rx="2"/>
										<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
									</svg>
									<p>No products found</p>
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
		{{ $products->appends(request()->query())->links('vendor.pagination.compact') }}
	</div>
</div>
@endsection

@section('scripts')

<script>
function getPerPage(view) {
	const w = window.innerWidth;
	if (view === 'card') {
		if (w >= 2560) return 18;
		if (w >= 1500) return 12;
		if (w >= 1200) return 8;
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
		@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
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
		@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
		title: 'Error!',
		text: '{{ session('error') }}',
		timer: 3000,
		showConfirmButton: false,
		background: '#ffffff',
		iconColor: '#ef4444'
	});
	@endif
});

function openAlertModal(productId, productName) {
	Swal.fire({
		title: 'Send Alert',
		html: `<span style="font-size: 0.9rem;">Suggest changes for <strong>${productName}</strong></span>`,
		@if(file_exists(public_path('assets/icons/Gif/alert1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
		input: 'textarea',
		inputPlaceholder: 'Type your message here...',
		inputAttributes: {
			'aria-label': 'Type your message here'
		},
		showCancelButton: true,
		confirmButtonText: 'Send Alert',
		confirmButtonColor: '#f59e0b',
		cancelButtonText: 'Cancel',
		cancelButtonColor: '#6b7280',
		showLoaderOnConfirm: true,
		preConfirm: (message) => {
			if (!message) {
				Swal.showValidationMessage('Please enter a message');
				return;
			}
			return fetch('{{ route("facilitator.products.send-alert") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
				},
				body: JSON.stringify({ product_id: productId, message: message })
			})
			.then(response => {
				return response.json().then(data => {
					if (!response.ok) {
						throw new Error(data.message || 'Network error');
					}
					return data;
				});
			})
			.catch(error => {
				Swal.showValidationMessage(`Request failed: ${error.message}`);
			});
		},
		allowOutsideClick: () => !Swal.isLoading()
	}).then((result) => {
		if (result.isConfirmed && result.value && result.value.success) {
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
				title: 'Sent!',
				text: 'Alert has been sent to the lead farmer.',
				timer: 2000,
				showConfirmButton: false
			});
		}
	});
}

function viewProductDetails(product) {
	const photoUrl = product.product_photo ? `/uploads/product_images/${product.product_photo}` : '/assets/images/product-placeholder.png';
	const formattedPrice = new Intl.NumberFormat('en-LK', { minimumFractionDigits: 2 }).format(product.selling_price);
	
	Swal.fire({
		title: `<div class="swal-header">Product Details</div>`,
		html: `
			<div class="swal-product-content">
				<div class="swal-product-header">
					<img src="${photoUrl}" onerror="this.src='/assets/images/product-placeholder.png'">
					<div>
						<h4>${product.product_name}</h4>
						<p>${product.category_name}${product.subcategory_name ? ' | ' + product.subcategory_name : ''}</p>
					</div>
				</div>
				
				<div class="swal-info-grid">
					<div class="swal-info-item">
						<label>Type / Variant</label>
						<span>${product.type_variant || 'N/A'}</span>
					</div>
					<div class="swal-info-item">
						<label>Quality Grade</label>
						<span>${product.quality_grade}</span>
					</div>
					<div class="swal-info-item">
						<label>Stock</label>
						<span>${product.quantity} ${product.unit_of_measure}</span>
					</div>
					<div class="swal-info-item">
						<label>Price</label>
						<span class="price">LKR ${formattedPrice}</span>
					</div>
					<div class="swal-info-item">
						<label>Farmer</label>
						<span>${product.farmer_name}</span>
					</div>
					<div class="swal-info-item">
						<label>Lead Farmer</label>
						<span>${product.lead_farmer_name || 'N/A'}</span>
					</div>
					<div class="swal-info-item">
						<label>Available From</label>
						<span>${product.expected_availability_date || 'Immediate'}</span>
					</div>
					<div class="swal-info-item">
						<label>Status</label>
						<span>${product.product_status}</span>
					</div>
					<div class="swal-info-item full-width">
						<label>Pickup Address</label>
						<span>${product.pickup_address || 'Not specified'}</span>
						${product.pickup_map_link ? `<a href="${product.pickup_map_link}" target="_blank" class="map-link">View on Map</a>` : ''}
					</div>
					<div class="swal-info-item full-width">
						<label>Description</label>
						<div class="description-box">${product.product_description || 'No description available.'}</div>
					</div>
				</div>
			</div>
		`,
		showConfirmButton: true,
		confirmButtonText: 'Close',
		confirmButtonColor: '#10B981',
		customClass: {
			popup: 'swal-product-modal',
			title: 'swal-product-title',
			htmlContainer: 'swal-product-container'
		},
		width: '450px'
	});
}
</script>
@endsection

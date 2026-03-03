@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Manage Products')
@section('page-title', 'Manage Products')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/manage_products.css') }}">
@endsection

@section('content')
<div class="products-page">
	<div class="products-header">
		<div class="header-content">
			<div class="header-left">
				<h1>
					<i class="fa-solid fa-box"></i>
					Products
				</h1>
				<p class="header-subtitle">Manage your product catalog</p>
			</div>
			<div class="header-right">
				<a href="{{ route('lf.addProduct') }}" class="btn-add-product">
					<i class="fa-solid fa-plus"></i>
					Add Product
				</a>
			</div>
		</div>
	</div>

	<div class="products-container">
		<div class="products-filter-card">
			<div class="filter-header">
				<i class="fa-solid fa-sliders"></i>
				<h3>Filters</h3>
			</div>
			<form method="GET" class="filter-form">
				<input type="hidden" name="view_type" id="view_type_input" value="{{ request('view_type', 'card') }}">
				<div class="filter-grid">
					<div class="filter-group">
						<label>
							<i class="fa-solid fa-search"></i>
							Search
						</label>
						<input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
					</div>
					
					<div class="filter-group">
						<label>
							<i class="fa-solid fa-user"></i>
							Farmer
						</label>
						<select name="farmer_id" class="form-control">
							<option value="">All Farmers</option>
							@foreach($farmers as $farmer)
								<option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
									{{ $farmer->name }}
								</option>
							@endforeach
						</select>
					</div>
					
					<div class="filter-group">
						<label>
							<i class="fa-solid fa-tag"></i>
							Category
						</label>
						<select name="category_id" class="form-control">
							<option value="">All Categories</option>
							@foreach($categories as $category)
								<option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
									{{ $category->category_name }}
								</option>
							@endforeach
						</select>
					</div>
					
					<div class="filter-group">
						<label>
							<i class="fa-solid fa-circle"></i>
							Status
						</label>
						<select name="status" class="form-control">
							<option value="">All Status</option>
							<option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
							<option value="sold_out" {{ request('status') == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
						</select>
					</div>
					
					<div class="filter-actions">
						<button type="submit" class="btn-filter">
							<i class="fa-solid fa-check"></i>
							Apply
						</button>
						<a href="{{ route('lf.manageProducts') }}" class="btn-reset">
							<i class="fa-solid fa-undo"></i>
							Reset
						</a>
					</div>
				</div>
			</form>
		</div>

		@if($products->count() > 0)
		<div class="view-toggle">
			<button class="view-btn active" data-view="card">
				<i class="fa-solid fa-table-cells-large"></i>
				Card
			</button>
			<button class="view-btn" data-view="table">
				<i class="fa-solid fa-table"></i>
				Table
			</button>
		</div>

		<div class="products-content">
			<div class="products-card-view active">
				<div class="products-grid">
					@foreach($products as $product)
					<div class="product-card">
						<div class="product-card-header">
							@if($product->product_photo)
							<img src="{{ asset('uploads/product_images/' . $product->product_photo) }}" 
								 alt="{{ $product->product_name }}"
								 class="product-image"
								 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
							@else
							<div class="product-image-placeholder">
								<i class="fa-solid fa-box"></i>
							</div>
							@endif
							<div class="product-status">
								@if($product->is_available && $product->quantity > 0)
								<span class="status-badge available">
									<i class="fa-solid fa-check"></i>
									In Stock
								</span>
								@else
								<span class="status-badge sold-out">
									<i class="fa-solid fa-ban"></i>
									Sold Out
								</span>
								@endif
							</div>
						</div>
						
						<div class="product-card-body">
							<h3 class="product-name">{{ $product->product_name }}</h3>
							
							@if($product->type_variant)
							<p class="product-variant">{{ $product->type_variant }}</p>
							@endif
							
							<div class="product-info-grid">
								<div class="info-item">
									<i class="fa-solid fa-user"></i>
									<span>{{ $product->farmer->name ?? 'Unknown' }}</span>
								</div>
								<div class="info-item">
									<i class="fa-solid fa-tag"></i>
									<span>{{ $product->category->category_name ?? 'Unknown' }}</span>
								</div>
								<div class="info-item">
									<i class="fa-solid fa-weight"></i>
									<span>{{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</span>
								</div>
								<div class="info-item">
									<i class="fa-solid fa-currency"></i>
									<span>LKR {{ number_format($product->selling_price, 2) }}</span>
								</div>
							</div>
							
							@if($product->product_description)
							<p class="product-description">{{ Str::limit($product->product_description, 60) }}</p>
							@endif
						</div>
						
						<div class="product-card-footer">
							<button class="action-btn view-btn" data-id="{{ $product->id }}">
								<i class="fa-solid fa-eye"></i>
							</button>
							<a href="{{ route('lf.editProduct', $product->id) }}" class="action-btn edit-btn">
								<i class="fa-solid fa-pen"></i>
							</a>
							<button class="action-btn delete-btn delete-product" 
									data-id="{{ $product->id }}" 
									data-name="{{ $product->product_name }}">
								<i class="fa-solid fa-trash"></i>
							</button>
						</div>
					</div>
					@endforeach
				</div>
				
				<div class="products-pagination card-pagination">
					{{ $products->appends(['view_type' => 'card'])->links('pagination.custom') }}
				</div>
			</div>

			<div class="products-table-view">
				<div class="table-responsive">
					<table class="products-table">
						<thead>
							<tr>
								<th>Product</th>
								<th>Farmer</th>
								<th>Category</th>
								<th>Qty</th>
								<th>Price</th>
								<th>Status</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($products as $product)
							<tr>
								<td>
									<div class="table-product-info">
										@if($product->product_photo)
										<img src="{{ asset('uploads/product_images/' . $product->product_photo) }}" 
											 alt="{{ $product->product_name }}"
											 class="table-product-image"
											 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
										@endif
										<div>
											<strong>{{ $product->product_name }}</strong>
											@if($product->type_variant)
											<small>{{ $product->type_variant }}</small>
											@endif
										</div>
									</div>
								</td>
								<td>{{ $product->farmer->name ?? 'Unknown' }}</td>
								<td>{{ $product->category->category_name ?? 'Unknown' }}</td>
								<td>{{ number_format($product->quantity, 1) }} {{ $product->unit_of_measure }}</td>
								<td>LKR {{ number_format($product->selling_price, 2) }}</td>
								<td>
									@if($product->is_available && $product->quantity > 0)
									<span class="table-status available">In Stock</span>
									@else
									<span class="table-status sold-out">Sold Out</span>
									@endif
								</td>
								<td>
									<div class="table-actions">
										<button class="table-action-btn view-btn" data-id="{{ $product->id }}">
											<i class="fa-solid fa-eye"></i>
										</button>
										<a href="{{ route('lf.editProduct', $product->id) }}" class="table-action-btn edit-btn">
											<i class="fa-solid fa-pen"></i>
										</a>
										<button class="table-action-btn delete-btn delete-product" 
												data-id="{{ $product->id }}" 
												data-name="{{ $product->product_name }}">
											<i class="fa-solid fa-trash"></i>
										</button>
									</div>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				
				<div class="products-pagination table-pagination">
					{{ $products->appends(['view_type' => 'table'])->links('pagination.custom') }}
				</div>
			</div>
		</div>
		@else
		<div class="empty-state">
			<div class="empty-state-icon">
				<i class="fa-solid fa-box-open"></i>
			</div>
			<h3>No products found</h3>
			<p>Get started by adding your first product</p>
			<a href="{{ route('lf.addProduct') }}" class="btn-add-first">
				<i class="fa-solid fa-plus"></i>
				Add Product
			</a>
		</div>
		@endif
	</div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
	const viewButtons = $('.view-btn[data-view]');
	const cardView = $('.products-card-view');
	const tableView = $('.products-table-view');
	const viewTypeInput = $('#view_type_input');
	
	const initialView = new URLSearchParams(window.location.search).get('view_type') || 'card';
	if (initialView === 'table') {
		cardView.removeClass('active');
		tableView.addClass('active');
		viewButtons.removeClass('active');
		$('.view-btn[data-view="table"]').addClass('active');
		viewTypeInput.val('table');
	}
	
	viewButtons.on('click', function() {
		const viewType = $(this).data('view');
		viewButtons.removeClass('active');
		$(this).addClass('active');
		viewTypeInput.val(viewType);
		
		const url = new URL(window.location);
		url.searchParams.set('view_type', viewType);
		history.replaceState(null, '', url);
		
		if (viewType === 'card') {
			cardView.addClass('active');
			tableView.removeClass('active');
			reloadWithViewType('card');
		} else {
			cardView.removeClass('active');
			tableView.addClass('active');
			reloadWithViewType('table');
		}
	});

	function reloadWithViewType(viewType) {
		const currentUrl = new URL(window.location);
		currentUrl.searchParams.set('view_type', viewType);
		
		if (currentUrl.searchParams.has('page')) {
			currentUrl.searchParams.delete('page');
		}
		
		window.location.href = currentUrl.toString();
	}

	$(document).on('click', '.delete-product', function() {
		const productId = $(this).data('id');
		const productName = $(this).data('name');

		Swal.fire({
			title: 'Delete Product?',
			html: `<div style="text-align:center">
				<i class="fa-solid fa-trash" style="font-size:32px;color:#ef4444;margin-bottom:12px"></i>
				<p style="font-size:14px;margin-bottom:8px">Delete <strong>${productName}</strong>?</p>
				<p style="font-size:12px;color:#6b7280">This action cannot be undone</p>
			</div>`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#ef4444',
			confirmButtonText: 'Delete',
			cancelButtonText: 'Cancel',
			reverseButtons: true,
			width: '400px'
		}).then((result) => {
			if (result.isConfirmed) {
				const deleteUrl = `/lead-farmer/delete-product/${productId}`;
				
				$.ajax({
					url: deleteUrl,
					type: 'DELETE',
					data: {
						_token: "{{ csrf_token() }}"
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Deleting...',
							text: 'Please wait',
							allowOutsideClick: false,
							didOpen: () => {
								Swal.showLoading();
							}
						});
					},
					success: function(response) {
						if (response.success) {
							Swal.fire({
								icon: 'success',
								title: 'Deleted!',
								text: response.message,
								confirmButtonColor: '#10B981',
								timer: 1500,
								timerProgressBar: true
							}).then(() => {
								location.reload();
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Error!',
								text: response.message,
								confirmButtonColor: '#10B981'
							});
						}
					},
					error: function() {
						Swal.fire({
							icon: 'error',
							title: 'Error!',
							text: 'Something went wrong',
							confirmButtonColor: '#10B981'
						});
					}
				});
			}
		});
	});

	$(document).on('click', '.view-btn:not([data-view])', function() {
		const productId = $(this).data('id');
		
		$.ajax({
			url: `/lead-farmer/product-details/${productId}`,
			type: 'GET',
			success: function(response) {
				if (response.success) {
					Swal.fire({
						title: 'Product Details',
						html: response.html,
						showCloseButton: true,
						showConfirmButton: false,
						width: '500px'
					});
				}
			},
			error: function() {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Failed to load details',
					confirmButtonColor: '#10B981'
				});
			}
		});
	});

	let searchTimeout;
	$('input[name="search"]').on('keyup', function() {
		clearTimeout(searchTimeout);
		searchTimeout = setTimeout(() => {
			$('.filter-form').submit();
		}, 500);
	});

	$('select[name="farmer_id"], select[name="category_id"], select[name="status"]').on('change', function() {
		$('.filter-form').submit();
	});
	
	$(document).on('click', '.pagination a', function(e) {
		e.preventDefault();
		const url = $(this).attr('href');
		const viewType = viewTypeInput.val();
		window.location.href = url + '&view_type=' + viewType;
	});
});
</script>
@endsection
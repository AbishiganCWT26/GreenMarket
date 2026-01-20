@extends('admin.layouts.admin_master')

@section('title', 'Product Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/product-oversight.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="product-management-container">
	<div class="header-section">
		<div class="header-content">
			<h1><i class="fas fa-seedling"></i> Product Management</h1>
			<p>Manage all product listings, monitor inventory, and oversee product information</p>
		</div>
		<div class="header-actions">
			<button class="btn btn-primary" onclick="showAddProductModal()">
				<i class="fas fa-plus-circle"></i> Add New Product
			</button>
		</div>
	</div>

	<div class="filters-section">
		<div class="filter-card">
			<div class="filter-header">
				<i class="fas fa-filter"></i>
				<h3>Filter Products</h3>
			</div>
			<div class="filter-controls-grid">
				<div class="filter-group">
					<label><i class="fas fa-users"></i> Lead Farmer Group</label>
					<select id="filter-lead-farmer" class="filter-select" onchange="loadFarmersByGroup(this.value)">
						<option value="">All Groups</option>
						@foreach ($leadFarmers as $lf)
						<option value="{{ $lf->id }}">{{ $lf->group_name }}</option>
						@endforeach
					</select>
				</div>

				<div class="filter-group">
					<label><i class="fas fa-user"></i> Farmer</label>
					<select id="filter-farmer" class="filter-select" disabled>
						<option value="">Select Group First</option>
					</select>
				</div>

				<div class="filter-group">
					<label><i class="fas fa-tags"></i> Category</label>
					<select id="filter-category" class="filter-select">
						<option value="">All Categories</option>
						@foreach ($categories as $cat)
						<option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
						@endforeach
					</select>
				</div>

				<div class="filter-group">
					<label><i class="fas fa-money-bill-wave"></i> Price Range</label>
					<select id="filter-price" class="filter-select">
						<option value="">All Prices</option>
						<option value="0-100">LKR 0 - 100</option>
						<option value="101-250">LKR 101 - 250</option>
						<option value="251-500">LKR 251 - 500</option>
						<option value="501-1000">LKR 501 - 1000</option>
						<option value="1001+">LKR 1001+</option>
					</select>
				</div>

				<div class="filter-group">
					<label><i class="fas fa-check-circle"></i> Product Status</label>
					<select id="filter-status" class="filter-select">
						<option value="">All Status</option>
						<option value="have it">Have It</option>
						<option value="removed by the admin">Removed by Admin</option>
						<option value="removed by lead farmer">Removed by Lead Farmer</option>
						<option value="removed by facilitator">Removed by Facilitator</option>
					</select>
				</div>

				<div class="filter-group">
					<label><i class="fas fa-box"></i> Stock Availability</label>
					<select id="filter-availability" class="filter-select">
						<option value="">All</option>
						<option value="in-stock">In Stock</option>
						<option value="out-of-stock">Out of Stock</option>
						<option value="coming-soon">Coming Soon</option>
					</select>
				</div>

				<div class="filter-group search-group">
					<label><i class="fas fa-search"></i> Search Products</label>
					<div class="search-container">
						<input type="text" id="search-product" placeholder="Search by product name or category..." class="search-input">
						<button id="search-btn" class="search-btn">
							<i class="fas fa-search"></i>
						</button>
					</div>
				</div>
			</div>

			<div class="filter-actions">
				<button id="apply-filters" class="btn btn-secondary">
					<i class="fas fa-filter"></i> Apply Filters
				</button>
				<button id="reset-filters" class="btn btn-outline">
					<i class="fas fa-redo"></i> Reset All
				</button>
			</div>
		</div>
	</div>

	<div class="stats-section">
		<div class="stat-card">
			<div class="stat-icon total">
				<i class="fas fa-boxes"></i>
			</div>
			<div class="stat-info">
				<h3 id="total-products">0</h3>
				<p>Total Products</p>
			</div>
		</div>

		<div class="stat-card">
			<div class="stat-icon available">
				<i class="fas fa-check-circle"></i>
			</div>
			<div class="stat-info">
				<h3 id="available-products">0</h3>
				<p>Have It</p>
			</div>
		</div>

		<div class="stat-card">
			<div class="stat-icon categories">
				<i class="fas fa-tags"></i>
			</div>
			<div class="stat-info">
				<h3 id="total-categories">0</h3>
				<p>Categories</p>
			</div>
		</div>
	</div>

	<div class="products-section">
		<div class="section-header">
			<h2><i class="fas fa-box-open"></i> Product Listings</h2>
			<div class="products-count">
				<span id="page-counter">Page 1 of 1</span>
			</div>
		</div>

		<div class="products-container" id="products-container">
			<div class="loading-spinner">
				<i class="fas fa-spinner fa-spin"></i>
				<p>Loading products...</p>
			</div>
		</div>

		<div class="pagination-wrapper">
			<div class="pagination-container" id="pagination-container" style="display: none;">
				<div class="paginate left" id="prev-page" data-state="disabled">
					<i></i>
					<i></i>
				</div>
				<div class="counter" id="page-counter-display">1 / 1</div>
				<div class="paginate right" id="next-page" data-state="">
					<i></i>
					<i></i>
				</div>
			</div>
		</div>

		<div class="no-products" id="no-products" style="display: none;">
			<div class="empty-state">
				<i class="fas fa-box-open"></i>
				<h3>No Products Found</h3>
				<p>Try adjusting your filters or add a new product</p>
				<button class="btn btn-primary" onclick="showAddProductModal()">
					<i class="fas fa-plus-circle"></i> Add First Product
				</button>
			</div>
		</div>
	</div>
</div>

<div id="product-modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<h2><i class="fas fa-box"></i> <span id="modal-title">Product Details</span></h2>
			<button class="close-modal" onclick="closeModal()">&times;</button>
		</div>
		<div class="modal-body" id="modal-body">
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	let currentProductId = null;
	let allProducts = [];
	let filteredProducts = [];
	let currentPage = 1;
	let totalPages = 1;
	const perPage = 20;
	let totalProductsCount = 0;

	$(document).ready(function() {
		loadProducts();
		setupEventListeners();
	});

	function loadProducts(page = 1) {
		currentPage = page;
		$('#products-container').html(`
			<div class="loading-spinner">
				<i class="fas fa-spinner fa-spin"></i>
				<p>Loading products...</p>
			</div>
		`);

		const filters = getCurrentFilters();

		$.ajax({
			url: '{{ route("admin.products.paginated") }}',
			method: 'GET',
			data: {
				...filters,
				page: page,
				per_page: perPage
			},
			success: function(response) {
				allProducts = response.products;
				filteredProducts = allProducts;
				totalPages = response.last_page;
				totalProductsCount = response.total;
				renderProducts();
				updatePagination(response.total, response.current_page, response.last_page);
				updateStats(response.total_stats);
			},
			error: function(xhr) {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Failed to load products. Please try again.',
					position: 'center',
					showConfirmButton: true
				});
			}
		});
	}

	function getCurrentFilters() {
		return {
			lead_farmer_id: $('#filter-lead-farmer').val(),
			farmer_id: $('#filter-farmer').val(),
			category_id: $('#filter-category').val(),
			price_range: $('#filter-price').val(),
			product_status: $('#filter-status').val(),
			is_available: $('#filter-availability').val() === 'in-stock' ? 'true' :
						$('#filter-availability').val() === 'out-of-stock' ? 'false' : '',
			coming_soon: $('#filter-availability').val() === 'coming-soon' ? 'true' : '',
			search: $('#search-product').val()
		};
	}

	function loadFarmersByGroup(leadFarmerId) {
		const farmerSelect = $('#filter-farmer');

		if (!leadFarmerId) {
			farmerSelect.html('<option value="">All Farmers</option>');
			farmerSelect.prop('disabled', false);
			return;
		}

		farmerSelect.html('<option value="">Loading farmers...</option>');
		farmerSelect.prop('disabled', true);

		$.ajax({
			url: '{{ route("admin.products.get-farmers", ":leadFarmerId") }}'.replace(':leadFarmerId', leadFarmerId),
			method: 'GET',
			success: function(response) {
				if (response.farmers.length > 0) {
					let options = '<option value="">All Farmers in Group</option>';
					response.farmers.forEach(farmer => {
						options += `<option value="${farmer.id}">${farmer.name}</option>`;
					});
					farmerSelect.html(options);
				} else {
					farmerSelect.html('<option value="">No farmers in this group</option>');
				}
				farmerSelect.prop('disabled', false);
			},
			error: function() {
				farmerSelect.html('<option value="">Error loading farmers</option>');
				farmerSelect.prop('disabled', false);
			}
		});
	}

	function renderProducts() {
		const container = $('#products-container');

		if (filteredProducts.length === 0) {
			$('#no-products').show();
			container.hide();
			$('#pagination-container').hide();
			return;
		}

		$('#no-products').hide();
		container.show();
		renderGridView();
	}

	function renderGridView() {
		const container = $('#products-container');
		let html = '<div class="products-grid">';

		filteredProducts.forEach(product => {
			const imageUrl = product.product_photo
				? '{{ asset("uploads/product_images") }}/' + product.product_photo
				: '{{ asset("assets/images/product-placeholder.png") }}';

			let statusClass = 'have-it';
			let statusText = 'Have It';
			const currentDate = new Date().toISOString().split('T')[0];

			if (product.product_status && product.product_status !== 'have it') {
				statusClass = 'removed';
				switch(product.product_status) {
					case 'removed by the admin':
						statusText = 'Removed by Admin';
						break;
					case 'removed by lead farmer':
						statusText = 'Removed by Lead Farmer';
						break;
					case 'removed by facilitator':
						statusText = 'Removed by Facilitator';
						break;
				}
			} else if (product.is_available === false) {
				statusClass = 'out-of-stock';
				statusText = 'Out of Stock';
			} else if (product.expected_availability_date && product.expected_availability_date > currentDate) {
				statusClass = 'coming-soon';
				statusText = 'Coming Soon';
			} else {
				statusClass = 'have-it';
				statusText = 'Have It';
			}

			html += `
				<div class="product-card" data-id="${product.id}">
					<div class="product-image">
						<img src="${imageUrl}" alt="${product.product_name}"
							onerror="this.src='{{ asset("assets/images/product-placeholder.png") }}'"
							onclick="showProductImage('${imageUrl}', '${product.product_name}')">
						<div class="product-badges">
							<span class="price-badge">LKR ${parseFloat(product.selling_price).toFixed(2)}</span>
							<span class="status-badge ${statusClass}">${statusText}</span>
						</div>
					</div>

					<div class="product-content">
						<div class="product-header">
							<h3 class="product-title" title="${product.product_name}">${product.product_name}</h3>
							<span class="product-category">${product.category_name || 'Uncategorized'}</span>
						</div>

						<div class="product-details">
							<div class="detail-item">
								<i class="fas fa-user"></i>
								<span>${product.farmer_name || 'Unknown Farmer'}</span>
							</div>
							<div class="detail-item">
								<i class="fas fa-users"></i>
								<span>${product.lead_group_name || 'No Group'}</span>
							</div>
							<div class="detail-item">
								<i class="fas fa-balance-scale"></i>
								<span>${product.quantity} ${product.unit_of_measure || 'units'}</span>
							</div>
							<div class="detail-item">
								<i class="fas ${product.is_available ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'}"></i>
								<span>${product.is_available ? 'In Stock' : 'Out of Stock'}</span>
							</div>
						</div>

						<div class="product-description">
							<p>${product.product_description ? product.product_description.substring(0, 80) + '...' : 'No description available'}</p>
						</div>
					</div>

					<div class="product-actions">
						<button class="btn-action view" onclick="viewProductDetails(${product.id})" title="View Details">
							<i class="fas fa-eye"></i>
						</button>
						<button class="btn-action edit" onclick="editProduct(${product.id})" title="Edit Product">
							<i class="fas fa-edit"></i>
						</button>
						<button class="btn-action delete" onclick="showDeleteConfirmation(${product.id})" title="Delete Product">
							<i class="fas fa-trash-alt"></i>
						</button>
					</div>
				</div>
			`;
		});

		html += '</div>';
		container.html(html);
	}

	function showProductImage(imageUrl, productName) {
		Swal.fire({
			title: productName,
			html: `<img src="${imageUrl}" style="max-width: 100%; max-height: 70vh;"
				onerror="this.src='{{ asset("assets/images/product-placeholder.png") }}'">`,
			showCloseButton: true,
			showConfirmButton: false,
			width: '80%',
			position: 'center'
		});
	}

	function updatePagination(total, currentPage, lastPage) {
		const paginationDiv = $('#pagination-container');

		if (total <= perPage) {
			paginationDiv.hide();
			return;
		}

		paginationDiv.show();

		const prevBtn = $('#prev-page');
		const nextBtn = $('#next-page');
		const counterDisplay = $('#page-counter-display');
		const pageCounter = $('#page-counter');

		prevBtn.attr('data-state', currentPage === 1 ? 'disabled' : '');
		nextBtn.attr('data-state', currentPage === lastPage ? 'disabled' : '');

		counterDisplay.text(`${currentPage} / ${lastPage}`);
		pageCounter.text(`Page ${currentPage} of ${lastPage}`);

		prevBtn.off('click').on('click', function() {
			if (currentPage > 1) {
				loadProducts(currentPage - 1);
			}
		});

		nextBtn.off('click').on('click', function() {
			if (currentPage < lastPage) {
				loadProducts(currentPage + 1);
			}
		});
	}

	function changePage(direction) {
		const newPage = currentPage + direction;
		if (newPage >= 1 && newPage <= totalPages) {
			loadProducts(newPage);
		}
	}

	function updateStats(statsData) {
		if (statsData) {
			$('#total-products').text(statsData.total_products);
			$('#available-products').text(statsData.have_it_count);
			$('#total-categories').text(statsData.total_categories);
		} else {
			const haveIt = allProducts.filter(p => p.product_status === 'have it').length;
			const categories = [...new Set(allProducts.map(p => p.category_name))].length;

			$('#total-products').text(totalProductsCount);
			$('#available-products').text(haveIt);
			$('#total-categories').text(categories);
		}
	}

	function setupEventListeners() {
		loadFarmersByGroup($('#filter-lead-farmer').val());

		$('#filter-lead-farmer').change(function() {
			loadFarmersByGroup($(this).val());
		});

		$('.filter-select').change(function() {
			loadProducts(1);
		});

		$('#search-btn').click(function() {
			loadProducts(1);
		});

		$('#search-product').keypress(function(e) {
			if (e.which === 13) loadProducts(1);
		});

		$('#apply-filters').click(function() {
			loadProducts(1);
		});

		$('#reset-filters').click(function() {
			$('.filter-select').val('');
			$('#search-product').val('');
			$('#filter-farmer').html('<option value="">Select Group First</option>').prop('disabled', true);
			loadProducts(1);
		});
	}

	function viewProductDetails(productId) {
		$.ajax({
			url: `{{ url('admin/products') }}/${productId}/details`,
			method: 'GET',
			success: function(response) {
				const product = response.product;
				const imageUrl = product.product_photo
					? '{{ asset("uploads/product_images") }}/' + product.product_photo
					: '{{ asset("assets/images/product-placeholder.png") }}';

				let statusClass = 'have-it';
				let statusText = 'Have It';
				const currentDate = new Date().toISOString().split('T')[0];

				if (product.product_status && product.product_status !== 'have it') {
					statusClass = 'removed';
					switch(product.product_status) {
						case 'removed by the admin':
							statusText = 'Removed by Admin';
							break;
						case 'removed by lead farmer':
							statusText = 'Removed by Lead Farmer';
							break;
						case 'removed by facilitator':
							statusText = 'Removed by Facilitator';
							break;
					}
				} else if (product.is_available === false) {
					statusClass = 'out-of-stock';
					statusText = 'Out of Stock';
				} else if (product.expected_availability_date && product.expected_availability_date > currentDate) {
					statusClass = 'coming-soon';
					statusText = 'Coming Soon';
				}

				let html = `
					<div class="product-detail-view">
						<div class="detail-header">
							<div class="detail-image">
								<img src="${imageUrl}" alt="${product.product_name}"
									onerror="this.src='{{ asset("assets/images/product-placeholder.png") }}'"
									onclick="showProductImage('${imageUrl}', '${product.product_name}')"
									style="cursor: pointer;">
							</div>
							<div class="detail-info">
								<h3>${product.product_name}</h3>
								<div class="detail-meta">
									<span class="category-badge">${product.category_name || 'Uncategorized'}</span>
									<span class="status-badge ${statusClass}">${statusText}</span>
									<span class="price-tag">LKR ${parseFloat(product.selling_price).toFixed(2)}</span>
								</div>
								<div class="detail-rating">
									<span class="grade-badge">${product.quality_grade || 'Standard'} Grade</span>
									<span class="views-count"><i class="fas fa-eye"></i> ${product.views_count || 0} views</span>
									<span class="stock-status ${product.is_available ? 'text-green-500' : 'text-red-500'}">
										<i class="fas ${product.is_available ? 'fa-check-circle' : 'fa-times-circle'}"></i>
										${product.is_available ? 'In Stock' : 'Out of Stock'}
									</span>
								</div>
							</div>
						</div>

						<div class="detail-content">
							<div class="detail-section">
								<h4><i class="fas fa-info-circle"></i> Product Information</h4>
								<div class="info-grid">
									<div class="info-item">
										<label>Description:</label>
										<p>${product.product_description || 'No description provided'}</p>
									</div>
									<div class="info-item">
										<label>Type Variant:</label>
										<p>${product.type_variant || 'Fresh'}</p>
									</div>
									<div class="info-item">
										<label>Quantity Available:</label>
										<p>${product.quantity} ${product.unit_of_measure || 'units'}</p>
									</div>
									<div class="info-item">
										<label>Expected Availability:</label>
										<p>${product.expected_availability_date ? new Date(product.expected_availability_date).toLocaleDateString() : 'Not specified'}</p>
									</div>
								</div>
							</div>

							<div class="detail-section">
								<h4><i class="fas fa-user"></i> Farmer Information</h4>
								<div class="info-grid">
									<div class="info-item">
										<label>Farmer:</label>
										<p>${product.farmer_name || 'Unknown'}</p>
									</div>
									<div class="info-item">
										<label>Contact:</label>
										<p>${product.farmer_mobile || 'Not available'}</p>
									</div>
									<div class="info-item">
										<label>Lead Farmer Group:</label>
										<p>${product.lead_group_name || 'No group'}</p>
									</div>
									<div class="info-item">
										<label>Group Contact:</label>
										<p>${product.lead_farmer_mobile || 'Not available'}</p>
									</div>
								</div>
							</div>

							<div class="detail-section">
								<h4><i class="fas fa-map-marker-alt"></i> Pickup Information</h4>
								<div class="info-grid">
									<div class="info-item full-width">
										<label>Pickup Address:</label>
										<p>${product.pickup_address || 'Not specified'}</p>
									</div>
									${product.pickup_map_link ? `
									<div class="info-item full-width">
										<label>Map Link:</label>
										<a href="${product.pickup_map_link}" target="_blank" class="map-link">
											<i class="fas fa-external-link-alt"></i> View on Map
										</a>
									</div>` : ''}
								</div>
							</div>
						</div>

						<div class="detail-actions">
							<button class="btn btn-primary" onclick="editProduct(${product.id})">
								<i class="fas fa-edit"></i> Edit Product
							</button>
							<button class="btn btn-outline" onclick="closeModal()">
								<i class="fas fa-times"></i> Close
							</button>
						</div>
					</div>
				`;

				$('#modal-title').text('Product Details');
				$('#modal-body').html(html);
				$('#product-modal').show();
			},
			error: function(xhr) {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Failed to load product details.',
					position: 'center',
					showConfirmButton: true
				});
			}
		});
	}

	function editProduct(productId) {
		currentProductId = productId;
		const product = allProducts.find(p => p.id == productId);

		const imageUrl = product.product_photo
			? '{{ asset("uploads/product_images") }}/' + product.product_photo
			: '{{ asset("assets/images/product-placeholder.png") }}';

		let html = `
			<form id="edit-product-form" onsubmit="updateProduct(event)">
				<div class="form-row">
					<div class="form-group">
						<label><i class="fas fa-tag"></i> Product Name *</label>
						<input type="text" name="product_name" value="${product.product_name || ''}" required>
					</div>
					<div class="form-group">
						<label><i class="fas fa-tags"></i> Category</label>
						<input type="text" value="${product.category_name || ''}" disabled>
					</div>
				</div>

				<div class="form-group">
					<label><i class="fas fa-align-left"></i> Description</label>
					<textarea name="product_description" rows="3">${product.product_description || ''}</textarea>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label><i class="fas fa-balance-scale"></i> Quantity *</label>
						<input type="number" name="quantity" value="${product.quantity || 0}" step="0.01" min="0" required>
					</div>
					<div class="form-group">
						<label><i class="fas fa-weight"></i> Unit of Measure</label>
						<input type="text" name="unit_of_measure" value="${product.unit_of_measure || ''}">
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label><i class="fas fa-money-bill-wave"></i> Selling Price (LKR) *</label>
						<input type="number" name="selling_price" value="${product.selling_price || 0}" step="0.01" min="0" required>
					</div>
					<div class="form-group">
						<label><i class="fas fa-star"></i> Quality Grade</label>
						<input type="text" name="quality_grade" value="${product.quality_grade || ''}">
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label><i class="fas fa-toggle-on"></i> Stock Availability</label>
						<select name="is_available" class="status-select">
							<option value="1" ${product.is_available ? 'selected' : ''}>In Stock</option>
							<option value="0" ${!product.is_available ? 'selected' : ''}>Out of Stock</option>
						</select>
					</div>
					<div class="form-group">
						<label><i class="fas fa-flag"></i> Product Status</label>
						<select name="product_status" class="status-select">
							<option value="have it" ${product.product_status === 'have it' ? 'selected' : ''}>Have It</option>
							<option value="removed by the admin" ${product.product_status === 'removed by the admin' ? 'selected' : ''}>Removed by Admin</option>
							<option value="removed by lead farmer" ${product.product_status === 'removed by lead farmer' ? 'selected' : ''}>Removed by Lead Farmer</option>
							<option value="removed by facilitator" ${product.product_status === 'removed by facilitator' ? 'selected' : ''}>Removed by Facilitator</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label><i class="fas fa-calendar"></i> Expected Availability Date</label>
					<input type="date" name="expected_availability_date" value="${product.expected_availability_date || ''}">
				</div>

				<div class="form-group">
					<label><i class="fas fa-image"></i> Product Image</label>
					<div class="image-upload">
						<div class="current-image">
							<img src="${imageUrl}"
								alt="Current Image" onerror="this.src='{{ asset("assets/images/product-placeholder.png") }}'"
								onclick="showProductImage('${imageUrl}', '${product.product_name}')"
								style="cursor: pointer;">
						</div>
						<input type="file" name="product_photo" accept="image/*">
						<small class="help-text">Upload new image to replace current one</small>
					</div>
				</div>

				<div class="form-actions">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-save"></i> Update Product
					</button>
					<button type="button" class="btn btn-outline" onclick="closeModal()">
						<i class="fas fa-times"></i> Cancel
					</button>
				</div>
			</form>
		`;

		$('#modal-title').text('Edit Product');
		$('#modal-body').html(html);
		$('#product-modal').show();
	}

	function updateProduct(event) {
		event.preventDefault();

		const formData = new FormData(event.target);
		formData.append('_method', 'PUT');

		$.ajax({
			url: `{{ url('admin/products') }}/${currentProductId}`,
			method: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			success: function(response) {
				closeModal();
				Swal.fire({
					icon: 'success',
					title: 'Success',
					text: 'Product updated successfully!',
					timer: 2000,
					showConfirmButton: false,
					position: 'center'
				});
				loadProducts(currentPage);
			},
			error: function(xhr) {
				if (xhr.responseJSON && xhr.responseJSON.errors) {
					let errors = '';
					Object.values(xhr.responseJSON.errors).forEach(error => {
						errors += error + '\n';
					});
					Swal.fire({
						icon: 'error',
						title: 'Validation Error',
						text: errors,
						position: 'center',
						showConfirmButton: true
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: 'Failed to update product. Please try again.',
						position: 'center',
						showConfirmButton: true
					});
				}
			}
		});
	}

	function showDeleteConfirmation(productId) {
		currentProductId = productId;
		const product = allProducts.find(p => p.id == productId);

		const imageUrl = product.product_photo
			? '{{ asset("uploads/product_images") }}/' + product.product_photo
			: '{{ asset("assets/images/product-placeholder.png") }}';

		Swal.fire({
			title: 'Confirm Delete',
			html: `
				<div class="delete-confirmation-content">
					<p>Are you sure you want to mark this product as removed? This action will change the product status to "removed by the admin".</p>
					<div class="sms-notification-info">
						<i class="fas fa-sms"></i>
						<small>SMS notifications will be sent to the farmer and lead farmer.</small>
					</div>
					<div class="delete-preview">
						<div class="preview-content">
							<img src="${imageUrl}" alt="${product.product_name}" onerror="this.src='{{ asset("assets/images/product-placeholder.png") }}'">
							<div>
								<strong>${product.product_name}</strong>
								<p>${product.category_name || 'Uncategorized'} • LKR ${parseFloat(product.selling_price).toFixed(2)}</p>
								<p class="text-muted">${product.farmer_name || 'Unknown Farmer'}</p>
							</div>
						</div>
					</div>
				</div>
			`,
			showCancelButton: true,
			confirmButtonText: 'Mark as Removed',
			cancelButtonText: 'Cancel',
			confirmButtonColor: '#ef4444',
			cancelButtonColor: '#6b7280',
			position: 'center',
			width: 500,
			customClass: {
				popup: 'sweet-alert-center'
			}
		}).then((result) => {
			if (result.isConfirmed) {
				confirmDelete();
			}
		});
	}

	function confirmDelete() {
		$.ajax({
			url: `{{ url('admin/products') }}/${currentProductId}`,
			method: 'DELETE',
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			success: function(response) {
				Swal.fire({
					icon: 'success',
					title: 'Success',
					text: 'Product status changed to removed. SMS notifications sent to farmer and lead farmer.',
					timer: 3000,
					showConfirmButton: false,
					position: 'center'
				});
				loadProducts(currentPage);
			},
			error: function(xhr) {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Failed to update product status. Please try again.',
					position: 'center',
					showConfirmButton: true
				});
			}
		});
	}

	function showAddProductModal() {
		window.location.href = '{{ route("admin.products.create") }}';
	}

	function closeModal() {
		$('#product-modal').hide();
		currentProductId = null;
	}

	$(document).on('click', function(event) {
		if ($(event.target).hasClass('modal')) {
			closeModal();
		}
	});
</script>
@endsection

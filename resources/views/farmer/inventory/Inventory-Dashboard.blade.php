@extends('farmer.layouts.farmer_master')

@section('title', 'Inventory Dashboard')
@section('page-title', 'Inventory Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/farmer/Inventory-Dashboard.css') }}">
<style>
/* Search Container */
.search-container{
    display:flex;
    align-items:center;
    border:2px solid #61e6b9ff;
    border-radius:25px;
    overflow:hidden;
    background:white;
    width:100%;
}

/* Input */
.search-container input{
    border:none;
    outline:none;
    padding:10px 15px;
    font-size:14px;
    width: 100%;
}

/* Button */
.search-container button{
    border:none;
    background:#61e6b9ff;
    color:white;
    padding:10px 18px;
    cursor:pointer;
    font-size:14px;
    transition:0.2s;
}

/* Hover */
.search-container button:hover{
    background:#10b981;
}
</style>
@endsection

@section('content')
<div class="inventory-app">
	<div class="app-header">
		<div class="header-left">
			<div class="header-icon">
				<i class="fas fa-boxes"></i>
			</div>
			<div class="header-text">
				<h2 class="header-title">My Inventory</h2>
			</div>
		</div>
		<div class="header-right">
			<button class="header-refresh" onclick="location.reload()">
				<i class="fas fa-sync-alt"></i>
			</button>
		</div>
	</div>

	<div class="stats-container">
		<div class="stat-item">
			<div class="stat-icon green">
				<i class="fas fa-box"></i>
			</div>
			<div class="stat-info">
				<span class="stat-value">{{ $totalProducts ?? 0 }}</span>
				<span class="stat-label">Total Products</span>
			</div>
		</div>
		<div class="stat-item">
			<div class="stat-icon blue">
				<i class="fas fa-coins"></i>
			</div>
			<div class="stat-info">
				<span class="stat-value">LKR {{ number_format($totalInventoryValue ?? 0, 2) }}</span>
				<span class="stat-label">Stock Value</span>
			</div>
		</div>
		<div class="stat-item">
			<div class="stat-icon amber">
				<i class="fas fa-exclamation"></i>
			</div>
			<div class="stat-info">
				<span class="stat-value">{{ $lowStockCount ?? 0 }}</span>
				<span class="stat-label">Low Stock</span>
			</div>
		</div>
		<div class="stat-item">
			<div class="stat-icon red">
				<i class="fas fa-times"></i>
			</div>
			<div class="stat-info">
				<span class="stat-value">{{ $outOfStockCount ?? 0 }}</span>
				<span class="stat-label">Out of Stock</span>
			</div>
		</div>
	</div>

	<div class="tab-container">
		<button class="tab-button active" data-tab="products">
			<i class="fas fa-cubes"></i>
			<span>Products</span>
		</button>
		<button class="tab-button" data-tab="logs">
			<i class="fas fa-history"></i>
			<span>Movements</span>
		</button>
	</div>

	<div class="tab-content active" id="productsTab">
		<div class="filter-wrapper">
			<form class="search-container" onsubmit="return false;">
				<input type="text" id="productSearch" placeholder="Search products...">
				<button type="submit">Search</button>
			</form>
			<div class="filter-actions">
				<select id="statusFilter" class="filter-select">
					<option value="">All Status</option>
					<option value="In Stock">In Stock</option>
					<option value="Low Stock">Low Stock</option>
					<option value="Critical">Critical</option>
					<option value="Out of Stock">Out of Stock</option>
				</select>
				<select id="categoryFilter" class="filter-select">
					<option value="">All Categories</option>
					@foreach($categories ?? [] as $category)
					<option value="{{ $category->id }}">{{ $category->category_name }}</option>
					@endforeach
				</select>
				<button class="filter-btn" id="applyProductFilters">
					<i class="fas fa-check"></i> Apply Filters
				</button>
				<button class="reset-btn" id="resetProductFilters">
					<i class="fas fa-undo"></i> Reset Filters
				</button>
				<button class="pdf-btn" id="exportProductPdf">
					<i class="fas fa-file-pdf"></i> Export PDF
				</button>
			</div>
		</div>

		<div class="view-toggles">
			<button class="view-toggle active" data-view="card">
				<i class="fas fa-th"></i>
			</button>
			<button class="view-toggle" data-view="table">
				<i class="fas fa-list"></i>
			</button>
		</div>

		<div class="view-container">
			<div class="card-view active" id="productCardView">
				<div class="card-grid" id="productCardGrid">
					@foreach($products ?? [] as $product)
					@php
						$status = $product->inventory_status ?? 'Out of Stock';
						$statusClass = strtolower(str_replace(' ', '-', $status));
					@endphp
					<div class="product-card" data-id="{{ $product->id }}"
						 data-name="{{ strtolower($product->product_name) }}"
						 data-category="{{ strtolower($product->category->category_name ?? '') }}"
						 data-category-id="{{ $product->category_id }}"
						 data-status="{{ $status }}">
						<div class="card-image">
							<img src="{{ asset('uploads/product_images/' . ($product->product_photo ?? '')) }}"
								 alt="{{ $product->product_name }}"
								 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
							<span class="status-badge {{ $statusClass }}">{{ $status }}</span>
						</div>
						<div class="card-content">
							<h3 class="card-title">{{ $product->product_name }}</h3>
							<div class="card-details">
								<div class="detail-item">
									<i class="fas fa-tag"></i>
									<span>{{ $product->category->category_name ?? 'N/A' }}</span>
								</div>
								<div class="detail-item">
									<i class="fas fa-weight"></i>
									<span>{{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</span>
								</div>
							</div>

						</div>
					</div>
					@endforeach
				</div>
				<div class="pagination" id="productCardPager"></div>
			</div>

			<div class="table-view" id="productTableView">
				<div class="table-responsive">
					<table class="data-table">
						<thead>
							<tr>
								<th>Product</th>
								<th>Category</th>
								<th>Stock</th>
								<th>Status</th>
								<th>Updated</th>
							</tr>
						</thead>
						<tbody id="productTableBody">
							@foreach($products ?? [] as $product)
							@php
								$status = $product->inventory_status ?? 'Out of Stock';
								$statusClass = strtolower(str_replace(' ', '-', $status));
							@endphp
							<tr class="product-row" data-id="{{ $product->id }}"
								data-name="{{ strtolower($product->product_name) }}"
								data-category="{{ strtolower($product->category->category_name ?? '') }}"
								data-category-id="{{ $product->category_id }}"
								data-status="{{ $status }}">
								<td>
									<div class="table-product">
										<img src="{{ asset('uploads/product_images/' . ($product->product_photo ?? '')) }}"
											 alt="{{ $product->product_name }}"
											 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
										<span>{{ $product->product_name }}</span>
									</div>
								</td>
								<td>{{ $product->category->category_name ?? 'N/A' }}</td>
								<td>
									<div class="table-stock">
										<span>{{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</span>
									</div>
								</td>
								<td>
									<span class="status-badge small {{ $statusClass }}">{{ $status }}</span>
								</td>
								<td>
									<div class="table-date">
										<span class="date">{{ $product->updated_at->format('d M Y') }}</span>
										<small class="time">{{ $product->updated_at->diffForHumans() }}</small>
									</div>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="pagination" id="productTablePager"></div>
			</div>
		</div>
	</div>

	<div class="tab-content" id="logsTab">
		<div class="filter-wrapper">
			<form class="search-container" onsubmit="return false;">
				<input type="text" id="logSearch" placeholder="Search logs...">
				<button type="submit">Search</button>
			</form>
			<div class="filter-actions">
				<select id="activityFilter" class="filter-select">
					<option value="">All Activities</option>
					<option value="order_placed">Order Placed</option>
					<option value="order_cancelled">Order Cancelled</option>
					<option value="payment_confirmed">Payment Confirmed</option>
					<option value="manual_add">Manual Add</option>
					<option value="manual_reduce">Manual Reduce</option>
					<option value="manual_adjust">Manual Adjust</option>
				</select>
				<button class="pdf-btn" id="exportLogPdf">
					<i class="fas fa-file-pdf"></i>
				</button>
			</div>
		</div>

		<div class="view-toggles">
			<button class="view-toggle active" data-view="card">
				<i class="fas fa-th"></i>
			</button>
			<button class="view-toggle" data-view="table">
				<i class="fas fa-list"></i>
			</button>
		</div>

		<div class="view-container">
			<div class="card-view active" id="logCardView">
				<div class="card-grid" id="logCardGrid">
					@foreach($logs ?? [] as $log)
					@php
						$typeClass = str_replace('_', '-', $log->type);
					@endphp
					<div class="log-card" data-id="{{ $log->id }}"
						 data-product="{{ strtolower($log->product->product_name ?? '') }}"
						 data-type="{{ $log->type }}">
						<div class="card-image">
							<img src="{{ asset('uploads/product_images/' . ($log->product->product_photo ?? '')) }}"
								 alt="{{ $log->product->product_name ?? 'Product' }}"
								 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
							<span class="log-badge {{ $typeClass }}">{{ str_replace('_', ' ', $log->type) }}</span>
						</div>
						<div class="card-content">
							<h3 class="card-title">{{ $log->product->product_name ?? 'Unknown' }}</h3>
							<div class="card-details">
								<div class="detail-item">
									<i class="fas fa-exchange-alt"></i>
									<span class="{{ $log->quantity_change > 0 ? 'text-green' : ($log->quantity_change < 0 ? 'text-red' : '') }}">
										{{ $log->quantity_change > 0 ? '+' : '' }}{{ number_format($log->quantity_change, 2) }}
									</span>
								</div>
								<div class="detail-item">
									<i class="fas fa-user-cog"></i>
									<span>{{ $log->user->username ?? 'System' }}</span>
								</div>
								<div class="detail-item">
									<i class="fas fa-clock"></i>
									<span>{{ $log->created_at->format('d M H:i') }}</span>
								</div>
								@if($log->reason)
								<div class="detail-item note">
									<i class="fas fa-sticky-note"></i>
									<span>{{ Str::limit($log->reason, 30) }}</span>
								</div>
								@endif
							</div>
						</div>
					</div>
					@endforeach
				</div>
				<div class="pagination" id="logCardPager"></div>
			</div>

			<div class="table-view" id="logTableView">
				<div class="table-responsive">
					<table class="data-table">
						<thead>
							<tr>
								<th>Date</th>
								<th>Product</th>
								<th>Activity</th>
								<th>Change</th>
								<th>By</th>
							</tr>
						</thead>
						<tbody id="logTableBody">
							@foreach($logs ?? [] as $log)
							@php
								$typeClass = str_replace('_', '-', $log->type);
							@endphp
							<tr class="log-row" data-id="{{ $log->id }}"
								data-product="{{ strtolower($log->product->product_name ?? '') }}"
								data-type="{{ $log->type }}">
								<td>
									<div class="table-date">
										<span>{{ $log->created_at->format('d M Y') }}</span>
										<small>{{ $log->created_at->format('H:i') }}</small>
									</div>
								</td>
								<td>
									<div class="table-product">
										<img src="{{ asset('uploads/product_images/' . ($log->product->product_photo ?? '')) }}"
											 alt="{{ $log->product->product_name ?? 'Product' }}"
											 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
										<span>{{ $log->product->product_name ?? 'Unknown' }}</span>
									</div>
								</td>
								<td>
									<span class="log-badge small {{ $typeClass }}">{{ str_replace('_', ' ', $log->type) }}</span>
								</td>
								<td>
									<span class="{{ $log->quantity_change > 0 ? 'text-green' : ($log->quantity_change < 0 ? 'text-red' : '') }}">
										{{ $log->quantity_change > 0 ? '+' : '' }}{{ number_format($log->quantity_change, 2) }}
									</span>
								</td>
								<td>{{ $log->user->username ?? 'System' }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="pagination" id="logTablePager"></div>
			</div>
		</div>
	</div>

	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('scripts')
<script>
let currentProductView = 'card';
let currentLogView = 'card';
let currentProductPage = 1;
let currentLogPage = 1;
let productPerPage = getPerPage('product');
let logPerPage = getPerPage('log');
let productItems = [];
let logItems = [];
let filteredProducts = [];
let filteredLogs = [];

document.addEventListener('DOMContentLoaded', function() {
	initData();
	setupListeners();
	updateDisplay('product');
	updateDisplay('log');
});

function getPerPage(type) {
	const w = window.innerWidth;
	const view = type === 'product' ? currentProductView : currentLogView;
	
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

function initData() {
	const productCards = document.querySelectorAll('#productCardGrid .product-card');
	const productRows = document.querySelectorAll('#productTableBody .product-row');
	
	productItems = [];
	const productMap = new Map();

	productCards.forEach(c => {
		const id = c.dataset.id;
		if (!productMap.has(id)) {
			productMap.set(id, {
				id: id,
				name: c.dataset.name,
				category: c.dataset.category,
				categoryId: c.dataset.categoryId,
				status: c.dataset.status,
				cardEl: c,
				rowEl: null
			});
		} else {
			productMap.get(id).cardEl = c;
		}
	});

	productRows.forEach(r => {
		const id = r.dataset.id;
		if (productMap.has(id)) {
			productMap.get(id).rowEl = r;
		} else {
			productMap.set(id, {
				id: id,
				name: r.dataset.name,
				category: r.dataset.category,
				categoryId: r.dataset.categoryId,
				status: r.dataset.status,
				cardEl: null,
				rowEl: r
			});
		}
	});

	productItems = Array.from(productMap.values());
	filteredProducts = [...productItems];

	const logCards = document.querySelectorAll('#logCardGrid .log-card');
	const logRows = document.querySelectorAll('#logTableBody .log-row');
	
	logItems = [];
	const logMap = new Map();

	logCards.forEach(c => {
		const id = c.dataset.id;
		if (!logMap.has(id)) {
			logMap.set(id, {
				id: id,
				product: c.dataset.product,
				activityType: c.dataset.type,
				cardEl: c,
				rowEl: null
			});
		} else {
			logMap.get(id).cardEl = c;
		}
	});

	logRows.forEach(r => {
		const id = r.dataset.id;
		if (logMap.has(id)) {
			logMap.get(id).rowEl = r;
		} else {
			logMap.set(id, {
				id: id,
				product: r.dataset.product,
				activityType: r.dataset.type,
				cardEl: null,
				rowEl: r
			});
		}
	});

	logItems = Array.from(logMap.values());
	filteredLogs = [...logItems];
}

function setupListeners() {
	document.querySelectorAll('.tab-button').forEach(btn => {
		btn.addEventListener('click', function() {
			const tab = this.dataset.tab;
			document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
			document.querySelectorAll('.tab-content').forEach(p => p.classList.remove('active'));
			this.classList.add('active');
			document.getElementById(tab + 'Tab').classList.add('active');
		});
	});

	document.querySelectorAll('.view-toggle').forEach(btn => {
		btn.addEventListener('click', function() {
			const parent = this.closest('.tab-content');
			const view = this.dataset.view;
			const type = parent.id === 'productsTab' ? 'product' : 'log';
			
			parent.querySelectorAll('.view-toggle').forEach(b => b.classList.remove('active'));
			this.classList.add('active');
			
			parent.querySelectorAll('.card-view, .table-view').forEach(v => v.classList.remove('active'));
			const viewId = type + (view.charAt(0).toUpperCase() + view.slice(1)) + 'View';
			document.getElementById(viewId).classList.add('active');
			
			if (type === 'product') {
				currentProductView = view;
				productPerPage = getPerPage('product');
				currentProductPage = 1;
				updateDisplay('product');
			} else {
				currentLogView = view;
				logPerPage = getPerPage('log');
				currentLogPage = 1;
				updateDisplay('log');
			}
		});
	});

	document.getElementById('exportProductPdf')?.addEventListener('click', function() {
		const status = document.getElementById('statusFilter').value;
		const category = document.getElementById('categoryFilter').value;
		const search = document.getElementById('productSearch').value;
		
		let url = "{{ route('farmer.inventory.report.products') }}?";
		if (status) url += "status=" + encodeURIComponent(status) + "&";
		if (category) url += "category_id=" + encodeURIComponent(category) + "&";
		if (search) url += "search=" + encodeURIComponent(search);
		
		window.location.href = url;
	});

	document.getElementById('exportLogPdf')?.addEventListener('click', function() {
		const activity = document.getElementById('activityFilter').value;
		const search = document.getElementById('logSearch').value;
		
		let url = "{{ route('farmer.inventory.report.movement') }}?";
		if (activity) url += "type=" + encodeURIComponent(activity) + "&";
		if (search) url += "search=" + encodeURIComponent(search);
		
		window.location.href = url;
	});

	// Product search form submit
	document.querySelector('#productsTab .search-container')?.addEventListener('submit', function(e) {
		e.preventDefault();
		filterProducts(document.getElementById('productSearch').value.toLowerCase());
	});

	// Log search form submit
	document.querySelector('#logsTab .search-container')?.addEventListener('submit', function(e) {
		e.preventDefault();
		filterLogs(document.getElementById('logSearch').value.toLowerCase());
	});

	document.getElementById('applyProductFilters')?.addEventListener('click', filterProductsFromControls);
	document.getElementById('resetProductFilters')?.addEventListener('click', resetProductFilters);

	document.getElementById('activityFilter')?.addEventListener('change', filterLogsFromControls);

	window.addEventListener('resize', function() {
		const newProductPerPage = getPerPage('product');
		const newLogPerPage = getPerPage('log');
		
		if (newProductPerPage !== productPerPage) {
			productPerPage = newProductPerPage;
			currentProductPage = 1;
			updateDisplay('product');
		}
		if (newLogPerPage !== logPerPage) {
			logPerPage = newLogPerPage;
			currentLogPage = 1;
			updateDisplay('log');
		}
	});
}

function filterProducts(term) {
	const status = document.getElementById('statusFilter')?.value;
	const categoryId = document.getElementById('categoryFilter')?.value;

	filteredProducts = productItems.filter(item => {
		let match = true;
		if (term && !item.name.toLowerCase().includes(term)) match = false;
		if (status && item.status !== status) match = false;
		if (categoryId && item.categoryId != categoryId) match = false;
		return match;
	});

	currentProductPage = 1;
	updateDisplay('product');
}

function filterProductsFromControls() {
	const term = document.getElementById('productSearch')?.value.toLowerCase();
	filterProducts(term);
}

function resetProductFilters() {
	if (document.getElementById('productSearch')) document.getElementById('productSearch').value = '';
	if (document.getElementById('statusFilter')) document.getElementById('statusFilter').value = '';
	if (document.getElementById('categoryFilter')) document.getElementById('categoryFilter').value = '';
	filteredProducts = [...productItems];
	currentProductPage = 1;
	updateDisplay('product');
}

function filterLogs(term) {
	const activityType = document.getElementById('activityFilter')?.value;

	filteredLogs = logItems.filter(item => {
		let match = true;
		if (term && !item.product.toLowerCase().includes(term)) match = false;
		if (activityType && item.activityType !== activityType) match = false;
		return match;
	});

	currentLogPage = 1;
	updateDisplay('log');
}

function filterLogsFromControls() {
	const term = document.getElementById('logSearch')?.value.toLowerCase();
	filterLogs(term);
}

function updateDisplay(type) {
	if (type === 'product') {
		const start = (currentProductPage - 1) * productPerPage;
		const end = start + productPerPage;
		const pageItems = filteredProducts.slice(start, end);
		
		productItems.forEach(item => {
			if (item.cardEl) item.cardEl.style.display = 'none';
			if (item.rowEl) item.rowEl.style.display = 'none';
		});
		
		pageItems.forEach(item => {
			if (currentProductView === 'card' && item.cardEl) {
				item.cardEl.style.display = 'block';
			} else if (currentProductView === 'table' && item.rowEl) {
				item.rowEl.style.display = 'table-row';
			}
		});
		
		updatePagination('product', filteredProducts.length, productPerPage, currentProductPage);
	} else {
		const start = (currentLogPage - 1) * logPerPage;
		const end = start + logPerPage;
		const pageItems = filteredLogs.slice(start, end);

		logItems.forEach(item => {
			if (item.cardEl) item.cardEl.style.display = 'none';
			if (item.rowEl) item.rowEl.style.display = 'none';
		});

		pageItems.forEach(item => {
			if (currentLogView === 'card' && item.cardEl) {
				item.cardEl.style.display = 'block';
			} else if (currentLogView === 'table' && item.rowEl) {
				item.rowEl.style.display = 'table-row';
			}
		});

		updatePagination('log', filteredLogs.length, logPerPage, currentLogPage);
	}
}

function updatePagination(type, total, perPage, current) {
	const totalPages = Math.ceil(total / perPage);
	const containerId = type === 'product' ? (currentProductView === 'card' ? 'productCardPager' : 'productTablePager') : (currentLogView === 'card' ? 'logCardPager' : 'logTablePager');
	const container = document.getElementById(containerId);
	
	if (!container) return;
	
	// Clear all pagers for this type first
	if (type === 'product') {
		document.getElementById('productCardPager').innerHTML = '';
		document.getElementById('productTablePager').innerHTML = '';
	} else {
		document.getElementById('logCardPager').innerHTML = '';
		document.getElementById('logTablePager').innerHTML = '';
	}

	if (totalPages <= 1) return;

	let html = '<div class="pagination-list">';
	
	// Prev
	html += `<button class="pagination-btn" ${current === 1 ? 'disabled' : ''} onclick="changePage('${type}', ${current - 1})"><i class="fas fa-chevron-left"></i></button>`;
	
	for (let i = 1; i <= totalPages; i++) {
		if (i === 1 || i === totalPages || (i >= current - 1 && i <= current + 1)) {
			html += `<button class="pagination-btn ${i === current ? 'active' : ''}" onclick="changePage('${type}', ${i})">${i}</button>`;
		} else if (i === current - 2 || i === current + 2) {
			html += '<span class="pagination-dots">...</span>';
		}
	}
	
	// Next
	html += `<button class="pagination-btn" ${current === totalPages ? 'disabled' : ''} onclick="changePage('${type}', ${current + 1})"><i class="fas fa-chevron-right"></i></button>`;
	
	html += '</div>';
	container.innerHTML = html;
}

window.changePage = function(type, page) {
	if (type === 'product') {
		currentProductPage = page;
		updateDisplay('product');
	} else {
		currentLogPage = page;
		updateDisplay('log');
	}
};
</script>
@endsection

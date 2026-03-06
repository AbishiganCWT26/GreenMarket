@extends('admin.layouts.admin_master')

@section('title', 'Inventory Management')
@section('page-title', 'Inventory Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Admin/inventory.css') }}">
@endsection

@section('content')
<div class="inv-dashboard">
	<div class="dash-header">
		<div class="header-left">
			<div class="header-icon">
				<i class="fas fa-boxes"></i>
			</div>
			<div class="header-text">
				<h2 class="header-title">Inventory Management</h2>
				<p class="header-subtitle">Track stock levels and movement history</p>
			</div>
		</div>
		<div class="header-right">
			<a href="{{ route('admin.inventory.report.stock', ['export' => 'pdf']) }}" class="btn-report">
				<i class="fas fa-file-pdf"></i>
				<span>Stock Report</span>
			</a>
			<a href="{{ route('admin.inventory.report.movement', ['export' => 'pdf']) }}" class="btn-report ml-2">
				<i class="fas fa-file-pdf"></i>
				<span>Movement Report</span>
			</a>
		</div>
	</div>

	<div class="stats-grid">
		<div class="stat-card" id="totalSkusCard">
			<div class="stat-info">
				<span class="stat-value" id="totalSkus">0</span>
				<span class="stat-label">Total Products</span>
			</div>
			<div class="stat-icon">
				<i class="fas fa-cubes"></i>
			</div>
		</div>
		<div class="stat-card" id="totalValueCard">
			<div class="stat-info">
				<span class="stat-value" id="totalValue">LKR 0</span>
				<span class="stat-label">On-Hand Value</span>
			</div>
			<div class="stat-icon">
				<i class="fas fa-coins"></i>
			</div>
		</div>
		<div class="stat-card warning" id="lowStockCard">
			<div class="stat-info">
				<span class="stat-value" id="lowStockCount">0</span>
				<span class="stat-label">Low/Critical</span>
			</div>
			<div class="stat-icon">
				<i class="fas fa-exclamation-triangle"></i>
			</div>
		</div>
		<div class="stat-card danger" id="outOfStockCard">
			<div class="stat-info">
				<span class="stat-value" id="outOfStockCount">0</span>
				<span class="stat-label">Out of Stock</span>
			</div>
			<div class="stat-icon">
				<i class="fas fa-times-circle"></i>
			</div>
		</div>
	</div>

	<div class="section-tabs">
        What are you looking for? 
		<button class="tab-btn active" data-tab="oversight">
			<i class="fas fa-eye"></i>
			<span>Stock Oversight</span>
		</button>
        or 
		<button class="tab-btn" data-tab="logs">
			<i class="fas fa-history"></i>
			<span>Movement Logs</span>
		</button>
	</div>

	<div class="tab-pane active" id="oversightPane">
		<div class="filter-bar">
			<div class="search-wrap">
				<i class="fas fa-search search-icon"></i>
				<input type="text" id="oversightSearch" class="search-input" placeholder="Search by product, farmer, group...">
				<button class="search-clear" id="clearOversightSearch">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<div class="filter-group">
				<select id="groupFilter" class="filter-select">
					<option value="">All Groups</option>
					@foreach($leadFarmers as $lf)
					<option value="{{ $lf->id }}" {{ request('lead_farmer_id') == $lf->id ? 'selected' : '' }}>{{ $lf->group_name }}</option>
					@endforeach
				</select>
				<select id="statusFilter" class="filter-select">
					<option value="">All Status</option>
					<option value="In Stock" {{ request('status') == 'In Stock' ? 'selected' : '' }}>In Stock</option>
					<option value="Low Stock" {{ request('status') == 'Low Stock' ? 'selected' : '' }}>Low Stock</option>
					<option value="Critical" {{ request('status') == 'Critical' ? 'selected' : '' }}>Critical</option>
					<option value="Out of Stock" {{ request('status') == 'Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
				</select>
			</div>
		</div>

		<div class="view-switch">
			<button class="view-btn active" data-view="card">
				<i class="fas fa-th"></i> Cards
			</button>
			<button class="view-btn" data-view="table">
				<i class="fas fa-table"></i> Table
			</button>
		</div>

		<div class="view-container">
			<div class="card-view active" id="oversightCardView">
				<div class="card-grid" id="oversightCardGrid">
					@foreach($products as $product)
					<div class="inv-card" data-id="{{ $product->id }}"
						 data-name="{{ strtolower($product->product_name) }}"
						 data-farmer="{{ strtolower($product->farmer->name ?? '') }}"
						 data-group="{{ strtolower($product->leadFarmer->group_name ?? '') }}"
						 data-group-id="{{ $product->leadFarmer->id ?? '' }}"
						 data-status="{{ $product->inventory_status }}"
						 data-price="{{ $product->selling_price }}"
						 data-quantity="{{ $product->quantity }}">
						<div class="card-media">
							<img src="{{ asset('uploads/product_images/' . $product->product_photo) }}"
								 alt="{{ $product->product_name }}"
								 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
							<div class="card-status status-{{ strtolower(str_replace(' ', '-', $product->inventory_status)) }}">
								{{ $product->inventory_status }}
							</div>
						</div>
						<div class="card-body">
							<h3 class="card-title">{{ $product->product_name }}</h3>
							<div class="card-meta">
								<div class="meta-row">
									<i class="fas fa-user-tie"></i>
									<span>{{ $product->leadFarmer->group_name ?? 'N/A' }}</span>
								</div>
								<div class="meta-row">
									<i class="fas fa-user"></i>
									<span>{{ $product->farmer->name ?? 'N/A' }}</span>
								</div>
								<div class="meta-row">
									<i class="fas fa-weight"></i>
									<span>{{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</span>
								</div>
								<div class="meta-row price">
									<i class="fas fa-tag"></i>
									<span>LKR {{ number_format($product->selling_price, 2) }}</span>
								</div>
								<div class="meta-row total">
									<i class="fas fa-coins"></i>
									<span>LKR {{ number_format($product->quantity * $product->selling_price, 2) }}</span>
								</div>
							</div>
						</div>
					</div>
					@endforeach
				</div>
				<div class="pager" id="oversightCardPager"></div>
			</div>

			<div class="table-view" id="oversightTableView">
				<div class="table-wrap">
					<table class="inv-table">
						<thead>
							<tr>
								<th>Product</th>
								<th>Group</th>
								<th>Farmer</th>
								<th>Stock</th>
								<th>Price</th>
								<th>Total Value</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody id="oversightTableBody">
							@foreach($products as $product)
							<tr class="inv-row" data-id="{{ $product->id }}"
								data-name="{{ strtolower($product->product_name) }}"
								data-farmer="{{ strtolower($product->farmer->name ?? '') }}"
								data-group="{{ strtolower($product->leadFarmer->group_name ?? '') }}"
								data-group-id="{{ $product->leadFarmer->id ?? '' }}"
								data-status="{{ $product->inventory_status }}">
								<td>
									<div class="table-product">
										<img src="{{ asset('uploads/product_images/' . $product->product_photo) }}"
											 alt="{{ $product->product_name }}"
											 class="table-img"
											 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
										<div>
											<strong>{{ $product->product_name }}</strong>
										</div>
									</div>
								</td>
								<td>{{ $product->leadFarmer->group_name ?? 'N/A' }}</td>
								<td>{{ $product->farmer->name ?? 'N/A' }}</td>
								<td>{{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</td>
								<td>LKR {{ number_format($product->selling_price, 2) }}</td>
								<td class="price-cell">LKR {{ number_format($product->quantity * $product->selling_price, 2) }}</td>
								<td>
									<span class="table-status status-{{ strtolower(str_replace(' ', '-', $product->inventory_status)) }}">
										{{ $product->inventory_status }}
									</span>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="pager" id="oversightTablePager"></div>
			</div>
		</div>
	</div>

	<div class="tab-pane" id="logsPane">
		<div class="filter-bar">
			<div class="search-wrap">
				<i class="fas fa-search search-icon"></i>
				<input type="text" id="logsSearch" class="search-input" placeholder="Search by product, user, order...">
				<button class="search-clear" id="clearLogsSearch">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<div class="filter-group">
				<select id="activityFilter" class="filter-select">
					<option value="">All Activities</option>
					<option value="order_placed" {{ request('type') == 'order_placed' ? 'selected' : '' }}>Order Placed</option>
					<option value="order_cancelled" {{ request('type') == 'order_cancelled' ? 'selected' : '' }}>Order Cancelled</option>
					<option value="payment_confirmed" {{ request('type') == 'payment_confirmed' ? 'selected' : '' }}>Payment Confirmed</option>
					<option value="manual_add" {{ request('type') == 'manual_add' ? 'selected' : '' }}>Manual Add</option>
					<option value="manual_reduce" {{ request('type') == 'manual_reduce' ? 'selected' : '' }}>Manual Reduce</option>
					<option value="manual_adjust" {{ request('type') == 'manual_adjust' ? 'selected' : '' }}>Manual Adjust</option>
				</select>
			</div>
		</div>

		<div class="view-switch">
			<button class="view-btn active" data-view="card">
				<i class="fas fa-th"></i> Cards
			</button>
			<button class="view-btn" data-view="table">
				<i class="fas fa-table"></i> Table
			</button>
		</div>

		<div class="view-container">
			<div class="card-view active" id="logsCardView">
				<div class="card-grid" id="logsCardGrid">
					@foreach($logs as $log)
					<div class="log-card" data-id="{{ $log->id }}"
						 data-product="{{ strtolower($log->product->product_name ?? '') }}"
						 data-user="{{ strtolower($log->user->username ?? '') }}"
						 data-order="{{ strtolower($log->order->order_number ?? '') }}"
						 data-type="{{ $log->type }}">
						<div class="card-media">
							<img src="{{ asset('uploads/product_images/' . ($log->product->product_photo ?? '')) }}"
								 alt="{{ $log->product->product_name ?? 'Product' }}"
								 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
							<div class="log-badge log-{{ $log->type }}">
								{{ str_replace('_', ' ', $log->type) }}
							</div>
						</div>
						<div class="card-body">
							<h3 class="card-title">{{ $log->product->product_name ?? 'Unknown Product' }}</h3>
							<div class="card-meta">
								<div class="meta-row">
									<i class="fas fa-exchange-alt"></i>
									<span class="qty-change {{ $log->quantity_change > 0 ? 'qty-up' : ($log->quantity_change < 0 ? 'qty-down' : '') }}">
										{{ $log->quantity_change > 0 ? '+' : '' }}{{ number_format($log->quantity_change, 2) }}
									</span>
								</div>
								<div class="meta-row">
									<i class="fas fa-user"></i>
									<span>{{ $log->user->username ?? 'System' }}</span>
								</div>
								@if($log->order)
								<div class="meta-row">
									<i class="fas fa-receipt"></i>
									<span>{{ $log->order->order_number }}</span>
								</div>
								@endif
								<div class="meta-row">
									<i class="fas fa-clock"></i>
									<span>{{ $log->created_at->format('d M Y H:i') }}</span>
								</div>
								@if($log->reason)
								<div class="meta-row note">
									<i class="fas fa-sticky-note"></i>
									<span>{{ Str::limit($log->reason, 40) }}</span>
								</div>
								@endif
							</div>
						</div>
					</div>
					@endforeach
				</div>
				<div class="pager" id="logsCardPager"></div>
			</div>

			<div class="table-view" id="logsTableView">
				<div class="table-wrap">
					<table class="inv-table">
						<thead>
							<tr>
								<th>Product</th>
								<th>Activity</th>
								<th>Qty Change</th>
								<th>Performed By</th>
								<th>Order Ref</th>
								<th>Date & Time</th>
								<th>Notes</th>
							</tr>
						</thead>
						<tbody id="logsTableBody">
							@foreach($logs as $log)
							<tr class="log-row" data-id="{{ $log->id }}"
								data-product="{{ strtolower($log->product->product_name ?? '') }}"
								data-user="{{ strtolower($log->user->username ?? '') }}"
								data-order="{{ strtolower($log->order->order_number ?? '') }}"
								data-type="{{ $log->type }}">
								<td>
									<div class="table-product">
										<img src="{{ asset('uploads/product_images/' . ($log->product->product_photo ?? '')) }}"
											 alt="{{ $log->product->product_name ?? 'Product' }}"
											 class="table-img"
											 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
										<div>
											<strong>{{ $log->product->product_name ?? 'Unknown' }}</strong>
										</div>
									</div>
								</td>
								<td>
									<span class="table-log-badge log-{{ $log->type }}">
										{{ str_replace('_', ' ', $log->type) }}
									</span>
								</td>
								<td>
									<span class="qty-change {{ $log->quantity_change > 0 ? 'qty-up' : ($log->quantity_change < 0 ? 'qty-down' : '') }}">
										{{ $log->quantity_change > 0 ? '+' : '' }}{{ number_format($log->quantity_change, 2) }}
									</span>
								</td>
								<td>{{ $log->user->username ?? 'System' }}</td>
								<td>{{ $log->order->order_number ?? '—' }}</td>
								<td>
									<div class="date-cell">
										<span>{{ $log->created_at->format('d M Y') }}</span>
										<small>{{ $log->created_at->format('H:i') }}</small>
									</div>
								</td>
								<td class="note-cell">{{ Str::limit($log->reason, 30) ?? '—' }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="pager" id="logsTablePager"></div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
let currentOversightView = 'card';
let currentLogsView = 'card';
let currentOversightPage = 1;
let currentLogsPage = 1;
let oversightPerPage = getPerPage('oversight');
let logsPerPage = getPerPage('logs');
let oversightItems = [];
let logsItems = [];
let filteredOversight = [];
let filteredLogs = [];

document.addEventListener('DOMContentLoaded', function() {
	initData();
	setupListeners();
	
	// Trigger initial filtering to match URL params
	filterOversightFromControls();
	filterLogsFromControls();
	
	updateStats();
	updateDisplay('oversight');
	updateDisplay('logs');
});

function getPerPage(type) {
	const w = window.innerWidth;
	const view = type === 'oversight' ? currentOversightView : currentLogsView;
	
	if (view === 'card') {
		if (w >= 2560) return 18;
		if (w >= 1500) return 18;
		if (w >= 1200) return 8;
		if (w >= 992) return 9;
		if (w >= 768) return 4;
		return 3;
	} else {
		if (w >= 2560) return 15;
		if (w >= 1500) return 15;
		if (w >= 1200) return 10;
		if (w >= 992) return 10;
		if (w >= 768) return 10;
		return 10;
	}
}

function initData() {
	// Oversight data
	const cards = document.querySelectorAll('#oversightCardGrid .inv-card');
	const rows = document.querySelectorAll('#oversightTableBody .inv-row');
	
	oversightItems = [];
	cards.forEach(c => {
		const id = c.dataset.id;
		const row = Array.from(rows).find(r => r.dataset.id === id);
		oversightItems.push({
			id: id,
			name: c.dataset.name,
			farmer: c.dataset.farmer,
			group: c.dataset.group,
			groupId: c.dataset.groupId,
			status: c.dataset.status,
			card: c,
			row: row
		});
	});
	filteredOversight = [...oversightItems];

	// Logs data
	const logCards = document.querySelectorAll('#logsCardGrid .log-card');
	const logRows = document.querySelectorAll('#logsTableBody .log-row');
	
	logsItems = [];
	logCards.forEach(c => {
		const id = c.dataset.id;
		const row = Array.from(logRows).find(r => r.dataset.id === id);
		logsItems.push({
			id: id,
			product: c.dataset.product,
			user: c.dataset.user,
			order: c.dataset.order,
			type: c.dataset.type,
			card: c,
			row: row
		});
	});
	filteredLogs = [...logsItems];
}

function setupListeners() {
	// Tab switching
	document.querySelectorAll('.tab-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			const tab = this.dataset.tab;
			document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
			document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
			this.classList.add('active');
			document.getElementById(tab + 'Pane').classList.add('active');
		});
	});

	// View toggles
	document.querySelectorAll('.view-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			const parent = this.closest('.tab-pane');
			const view = this.dataset.view;
			const type = parent.id === 'oversightPane' ? 'oversight' : 'logs';
			
			parent.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
			this.classList.add('active');
			
			parent.querySelectorAll('.card-view, .table-view').forEach(v => v.classList.remove('active'));
			parent.querySelector('.' + view + '-view').classList.add('active');
			
			if (type === 'oversight') {
				currentOversightView = view;
				oversightPerPage = getPerPage('oversight');
				currentOversightPage = 1;
				updateDisplay('oversight');
			} else {
				currentLogsView = view;
				logsPerPage = getPerPage('logs');
				currentLogsPage = 1;
				updateDisplay('logs');
			}
		});
	});

	// Search
	document.getElementById('oversightSearch').addEventListener('input', function() {
		filterOversight(this.value.toLowerCase());
	});
	document.getElementById('logsSearch').addEventListener('input', function() {
		filterLogs(this.value.toLowerCase());
	});

	document.getElementById('clearOversightSearch').addEventListener('click', function() {
		document.getElementById('oversightSearch').value = '';
		filterOversight('');
	});
	document.getElementById('clearLogsSearch').addEventListener('click', function() {
		document.getElementById('logsSearch').value = '';
		filterLogs('');
	});

	// Filters
	document.getElementById('groupFilter').addEventListener('change', filterOversightFromControls);
	document.getElementById('statusFilter').addEventListener('change', filterOversightFromControls);
	document.getElementById('activityFilter').addEventListener('change', filterLogsFromControls);

	window.addEventListener('resize', function() {
		const newOversightPerPage = getPerPage('oversight');
		const newLogsPerPage = getPerPage('logs');
		
		if (newOversightPerPage !== oversightPerPage) {
			oversightPerPage = newOversightPerPage;
			currentOversightPage = 1;
			updateDisplay('oversight');
		}
		if (newLogsPerPage !== logsPerPage) {
			logsPerPage = newLogsPerPage;
			currentLogsPage = 1;
			updateDisplay('logs');
		}
	});
}

function filterOversight(term) {
	const groupId = document.getElementById('groupFilter').value;
	const status = document.getElementById('statusFilter').value;

	filteredOversight = oversightItems.filter(item => {
		let match = true;
		if (term && !item.name.includes(term) && !item.farmer.includes(term) && !item.group.includes(term)) {
			match = false;
		}
		if (groupId && item.groupId !== groupId) match = false;
		if (status && item.status !== status) match = false;
		return match;
	});

	currentOversightPage = 1;
	updateDisplay('oversight');
	updateStats();
}

function filterOversightFromControls() {
	const term = document.getElementById('oversightSearch').value.toLowerCase();
	filterOversight(term);
}

function filterLogs(term) {
	const type = document.getElementById('activityFilter').value;

	filteredLogs = logsItems.filter(item => {
		let match = true;
		if (term) {
			const typeName = item.type ? item.type.replace(/_/g, ' ') : '';
			if (!item.product.includes(term) && 
				!item.user.includes(term) && 
				!item.order.includes(term) &&
				!typeName.includes(term)) {
				match = false;
			}
		}
		if (type && item.type !== type) match = false;
		return match;
	});

	currentLogsPage = 1;
	updateDisplay('logs');
}

function filterLogsFromControls() {
	const term = document.getElementById('logsSearch').value.toLowerCase();
	filterLogs(term);
}

function updateDisplay(type) {
	if (type === 'oversight') {
		const start = (currentOversightPage - 1) * oversightPerPage;
		const end = start + oversightPerPage;
		const pageItems = filteredOversight.slice(start, end);
		
		oversightItems.forEach(item => {
			if (item.card) item.card.style.display = 'none';
			if (item.row) item.row.style.display = 'none';
		});
		
		pageItems.forEach(item => {
			if (currentOversightView === 'card') {
				if (item.card) item.card.style.display = 'block';
			} else {
				if (item.row) item.row.style.display = 'table-row';
			}
		});
		
		updatePagination('oversight', filteredOversight.length, oversightPerPage, currentOversightPage);
	} else {
		const start = (currentLogsPage - 1) * logsPerPage;
		const end = start + logsPerPage;
		const pageItems = filteredLogs.slice(start, end);
		
		logsItems.forEach(item => {
			if (item.card) item.card.style.display = 'none';
			if (item.row) item.row.style.display = 'none';
		});
		
		pageItems.forEach(item => {
			if (currentLogsView === 'card') {
				if (item.card) item.card.style.display = 'block';
			} else {
				if (item.row) item.row.style.display = 'table-row';
			}
		});
		
		updatePagination('logs', filteredLogs.length, logsPerPage, currentLogsPage);
	}
}

function updatePagination(type, total, perPage, current) {
	const pages = Math.ceil(total / perPage);
	const container = document.getElementById(type + (type === 'oversight' ? (currentOversightView === 'card' ? 'CardPager' : 'TablePager') : (currentLogsView === 'card' ? 'CardPager' : 'TablePager')));
	
	if (!container) return;
	
	container.innerHTML = '';
	if (pages <= 1) return;
	
	const list = document.createElement('div');
	list.className = 'pager-list';
	
	if (current > 1) {
		list.appendChild(createPageBtn(type, current - 1, '<i class="fas fa-chevron-left"></i>'));
	}
	
	for (let i = 1; i <= pages; i++) {
		if (i === 1 || i === pages || (i >= current - 1 && i <= current + 1)) {
			const btn = createPageBtn(type, i, i);
			if (i === current) btn.classList.add('active');
			list.appendChild(btn);
		} else if (i === current - 2 || i === current + 2) {
			const dots = document.createElement('span');
			dots.className = 'page-dots';
			dots.textContent = '...';
			list.appendChild(dots);
		}
	}
	
	if (current < pages) {
		list.appendChild(createPageBtn(type, current + 1, '<i class="fas fa-chevron-right"></i>'));
	}
	
	container.appendChild(list);
}

function createPageBtn(type, page, content) {
	const btn = document.createElement('button');
	btn.className = 'page-btn';
	btn.innerHTML = content;
	btn.addEventListener('click', () => {
		if (type === 'oversight') {
			currentOversightPage = page;
			updateDisplay('oversight');
		} else {
			currentLogsPage = page;
			updateDisplay('logs');
		}
	});
	return btn;
}

function updateStats() {
	let totalSkus = oversightItems.length;
	let totalValue = 0;
	let lowStock = 0;
	let outOfStock = 0;

	oversightItems.forEach(item => {
		const card = item.card;
		if (!card) return;
		
		const price = parseFloat(card.dataset.price) || 0;
		const qty = parseFloat(card.dataset.quantity) || 0;
		const status = item.status;

		totalValue += price * qty;
		if (status === 'Low Stock' || status === 'Critical') lowStock++;
		if (status === 'Out of Stock') outOfStock++;
	});

	document.getElementById('totalSkus').textContent = totalSkus;
	document.getElementById('totalValue').textContent = 'LKR ' + totalValue.toFixed(2);
	document.getElementById('lowStockCount').textContent = lowStock;
	document.getElementById('outOfStockCount').textContent = outOfStock;
}
</script>
@endsection
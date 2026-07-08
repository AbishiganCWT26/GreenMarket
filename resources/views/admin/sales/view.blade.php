@extends('admin.layouts.admin_master')

@section('title', 'System Sales & Transactions Log')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="{{ asset('css/Admin/sales-view.css') }}">
@endsection

@section('content')
<div class="sales-container">
	<div class="header-card">
		<h1><i class="fas fa-chart-line"></i> System Sales & Transactions</h1>
		<p>Comprehensive audit log of all orders, sales performance, and transaction analytics across the platform. View all order statuses including pending, confirmed, paid, ready for pickup, completed, cancelled, and refunded.</p>
	</div>

	<div class="filter-card">
		<h3 style="margin: 0 0 20px 0; color: var(--text-color); display: flex; align-items: center; gap: 10px;">
			<i class="fas fa-filter"></i> Filter Sales Data
		</h3>
		<div class="filter-grid">
			<div class="filter-group">
				<label class="filter-label"><i class="fas fa-calendar-alt"></i> Start Date</label>
				<input type="date" id="start-date" class="filter-input" value="{{ request('start_date') }}" max="{{ date('Y-m-d') }}">
			</div>
			<div class="filter-group">
				<label class="filter-label"><i class="fas fa-calendar-alt"></i> End Date</label>
				<input type="date" id="end-date" class="filter-input" value="{{ request('end_date') }}" max="{{ date('Y-m-d') }}">
			</div>
			<div class="filter-group">
				<label class="filter-label"><i class="fas fa-user-friends"></i> Lead Farmer</label>
				<select id="filter-lead-farmer" class="filter-select">
					<option value="">All Lead Farmers</option>
					@foreach($leadFarmers ?? [] as $lf)
						<option value="{{ $lf->id }}" {{ request('lead_farmer_id') == $lf->id ? 'selected' : '' }}>
							{{ $lf->name }} ({{ $lf->group_name }})
						</option>
					@endforeach
				</select>
			</div>
			<div class="filter-group" id="farmer-filter-group" style="display: none;">
				<label class="filter-label"><i class="fas fa-user"></i> Individual Farmer</label>
				<select id="filter-farmer" class="filter-select">
					<option value="">All Farmers</option>
				</select>
			</div>
			<div class="filter-group">
				<label class="filter-label"><i class="fas fa-tag"></i> Status</label>
				<select id="filter-status" class="filter-select">
					<option value="">All Status</option>
					<option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
					<option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
					<option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
					<option value="ready_for_pickup" {{ request('status') == 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
					<option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
					<option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
					<option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
				</select>
			</div>
			<div class="filter-group">
				<label class="filter-label"><i class="fas fa-search"></i> Search</label>
				<input type="text" id="search" class="filter-input" placeholder="Search order number or buyer..." value="{{ request('search') }}">
			</div>
		</div>
		<div class="filter-actions">
			<button onclick="applyFilters()" class="filter-btn apply-btn">
				<i class="fas fa-filter"></i> Apply Filters
			</button>
			<button onclick="resetFilters()" class="filter-btn reset-btn">
				<i class="fas fa-redo"></i> Reset Filters
			</button>
			<button onclick="exportData()" class="filter-btn export-btn">
				<i class="fas fa-download"></i> Export Data
			</button>
		</div>
	</div>

	<div class="sales-card">
		<div class="sales-stats">
			<div class="stat-card">
				<div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
				<div class="stat-value">{{ number_format($totalSales ?? 0) }}</div>
				<div class="stat-label">Total Sales</div>
			</div>
			<div class="stat-card">
				<div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
				<div class="stat-value">LKR {{ number_format($totalAmount ?? 0, 0) }}</div>
				<div class="stat-label">Total Revenue</div>
			</div>
			<div class="stat-card">
				<div class="stat-icon"><i class="fas fa-users"></i></div>
				<div class="stat-value">{{ number_format($uniqueBuyers ?? 0) }}</div>
				<div class="stat-label">Unique Buyers</div>
			</div>
		</div>

		<div class="sales-list-view view-table" id="sales-list-view" data-view="table">
			@if(count($sales) > 0)
			<div class="sales-table-container">
				<table class="sales-table">
					<thead>
						<tr>
							<th>Order ID</th>
							<th>Date</th>
							<th>Buyer</th>
							<th>Lead Farmer</th>
							<th>Total Value (LKR)</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($sales as $sale)
						<tr>
							<td>{{ $sale->order_number }}</td>
							<td>{{ \Carbon\Carbon::parse($sale->created_at)->format('Y-m-d') }}</td>
							<td>{{ $sale->buyer_name ?? 'N/A' }}</td>
							<td>{{ $sale->lead_farmer_name ?? 'N/A' }}</td>
							<td>{{ number_format($sale->total_amount, 2) }}</td>
							<td>
								<span class="status-badge status-{{ $sale->order_status }}">
									{{ ucfirst($sale->order_status) }}
								</span>
							</td>
							<td>
								<a href="{{ route('admin.sales.details', $sale->id) }}" class="action-btn">
									<i class="fas fa-eye"></i> View
								</a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<div class="sales-cards-container">
				@foreach($sales as $sale)
				<div class="sales-record-card">
					<div class="sales-record-header">
						<div>
							<div class="sales-record-title">{{ $sale->order_number }}</div>
							<div class="sales-record-meta">{{ \Carbon\Carbon::parse($sale->created_at)->format('Y-m-d') }}</div>
						</div>
						<span class="status-badge status-{{ $sale->order_status }}">
							{{ ucfirst($sale->order_status) }}
						</span>
					</div>
					<div class="sales-record-body">
						<div class="sales-record-row">
							<span>Buyer</span>
							<strong>{{ $sale->buyer_name ?? 'N/A' }}</strong>
						</div>
						<div class="sales-record-row">
							<span>Lead Farmer</span>
							<strong>{{ $sale->lead_farmer_name ?? 'N/A' }}</strong>
						</div>
						<div class="sales-record-row">
							<span>Total</span>
							<strong>LKR {{ number_format($sale->total_amount, 2) }}</strong>
						</div>
					</div>
					<div class="sales-record-footer">
						<a href="{{ route('admin.sales.details', $sale->id) }}" class="action-btn">
							<i class="fas fa-eye"></i> View Details
						</a>
					</div>
				</div>
				@endforeach
			</div>
			@else
			<div class="no-data">
				<i class="fas fa-database"></i>
				<h3>No Sales Data Found</h3>
				<p>Try adjusting your filters or check back later for new transactions.</p>
			</div>
			@endif
		</div>

		@if($sales->hasPages())
		{{ $sales->links('vendor.pagination.sales-pagination') }}
		@endif
	</div>
</div>
@endsection

@section('scripts')

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const leadFarmerSelect = document.getElementById('filter-lead-farmer');
		const farmerFilterGroup = document.getElementById('farmer-filter-group');
		const farmerSelect = document.getElementById('filter-farmer');

			updateViewMode();
			window.addEventListener('resize', debounce(updateViewMode, 180));

		if (leadFarmerSelect) {
			leadFarmerSelect.addEventListener('change', function() {
				const leadFarmerId = this.value;

				if (leadFarmerId) {
					fetchFarmersByLeadFarmer(leadFarmerId);
					farmerFilterGroup.style.display = 'block';
				} else {
					farmerFilterGroup.style.display = 'none';
					farmerSelect.innerHTML = '<option value="">All Farmers</option>';
				}
			});

			if (leadFarmerSelect.value) {
				fetchFarmersByLeadFarmer(leadFarmerSelect.value);
				farmerFilterGroup.style.display = 'block';
			}
		}

		// Add validation for date inputs
		const startDateInput = document.getElementById('start-date');
		const endDateInput = document.getElementById('end-date');

		if (startDateInput) {
			startDateInput.addEventListener('change', function() {
				if (endDateInput.value && new Date(this.value) > new Date(endDateInput.value)) {
					showError('Invalid Date Range', 'Start date cannot be after end date');
					this.value = '';
				}

				// Ensure date is not after today
				if (this.value > today) {
					showError('Invalid Date', 'Cannot select a date after today');
					this.value = '';
				}
			});
		}

		if (endDateInput) {
			endDateInput.addEventListener('change', function() {
				if (startDateInput.value && new Date(startDateInput.value) > new Date(this.value)) {
					showError('Invalid Date Range', 'End date cannot be before start date');
					this.value = '';
				}

				// Ensure date is not after today
				if (this.value > today) {
					showError('Invalid Date', 'Cannot select a date after today');
					this.value = '';
				}
			});
		}

		animateCards();
	});

	function fetchFarmersByLeadFarmer(leadFarmerId) {
		fetch(`/admin/products/get-farmers-by-lead-farmer/${leadFarmerId}`)
			.then(response => {
				if (!response.ok) throw new Error('Network response was not ok');
				return response.json();
			})
			.then(data => {
				const farmerSelect = document.getElementById('filter-farmer');
				if (!farmerSelect) return;

				farmerSelect.innerHTML = '<option value="">All Farmers</option>';

				if (data.farmers && data.farmers.length > 0) {
					data.farmers.forEach(farmer => {
						const option = document.createElement('option');
						option.value = farmer.id;
						option.textContent = farmer.name;
						farmerSelect.appendChild(option);
					});
				}
			})
			.catch(error => {
				console.error('Error fetching farmers:', error);
				showError('Failed to load farmers');
			});
	}

	function getPerPage(view, width) {
		if (view === 'card') {
			if (width >= 800) return 15;
			if (width >= 500) return 10;
			return 5;
		}

		if (width >= 2560) return 15;
		if (width >= 1500) return 15;
		if (width >= 1200) return 10;
		if (width >= 992) return 10;
		return 10;
	}

	function updateViewMode() {
		const width = window.innerWidth;
		const view = width < 800 ? 'card' : 'table';
		const perPage = getPerPage(view, width);
		const listView = document.getElementById('sales-list-view');

		if (listView) {
			listView.classList.toggle('view-card', view === 'card');
			listView.classList.toggle('view-table', view === 'table');
			listView.setAttribute('data-view', view);
		}

		const url = new URL(window.location.href);
		const currentView = url.searchParams.get('view');
		const currentPerPage = url.searchParams.get('per_page');

		if (currentView !== view || currentPerPage !== String(perPage)) {
			url.searchParams.set('view', view);
			url.searchParams.set('per_page', perPage);
			window.location.href = `${url.pathname}?${url.searchParams.toString()}`;
		}
	}

	function debounce(func, wait) {
		let timeout;
		return function(...args) {
			clearTimeout(timeout);
			timeout = setTimeout(() => func.apply(this, args), wait);
		};
	}

	function applyFilters() {
		const startDate = document.getElementById('start-date')?.value || '';
		const endDate = document.getElementById('end-date')?.value || '';
		const leadFarmerId = document.getElementById('filter-lead-farmer')?.value || '';
		const farmerId = document.getElementById('filter-farmer')?.value || '';
		const status = document.getElementById('filter-status')?.value || '';
		const search = document.getElementById('search')?.value || '';
		const today = new Date().toISOString().split('T')[0];

		// Validate dates are not after today
		if (startDate && startDate > today) {
			showError('Invalid Start Date', 'Cannot select a start date after today');
			return;
		}

		if (endDate && endDate > today) {
			showError('Invalid End Date', 'Cannot select an end date after today');
			return;
		}

		if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
			showError('Invalid Date Range', 'Start date cannot be after end date');
			return;
		}

		showLoading('Applying filters...');

		let url = new URL(window.location.href);
		let params = new URLSearchParams(url.search);

		params.delete('start_date');
		params.delete('end_date');
		params.delete('lead_farmer_id');
		params.delete('farmer_id');
		params.delete('status');
		params.delete('search');

		if (startDate) params.append('start_date', startDate);
		if (endDate) params.append('end_date', endDate);
		if (leadFarmerId) params.append('lead_farmer_id', leadFarmerId);
		if (farmerId) params.append('farmer_id', farmerId);
		if (status) params.append('status', status);
		if (search) params.append('search', search);

		setTimeout(() => {
			window.location.href = `${url.pathname}?${params.toString()}`;
		}, 500);
	}

	function resetFilters() {
		Swal.fire({
			title: 'Reset Filters?',
			text: 'This will clear all filter selections',
			@if(file_exists(public_path('assets/icons/Gif/question3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6B7280',
			confirmButtonText: 'Reset',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				document.getElementById('start-date').value = '';
				document.getElementById('end-date').value = '';
				document.getElementById('filter-lead-farmer').value = '';
				document.getElementById('filter-farmer').value = '';
				document.getElementById('filter-status').value = '';
				document.getElementById('search').value = '';

				const farmerFilterGroup = document.getElementById('farmer-filter-group');
				farmerFilterGroup.style.display = 'none';

				showSuccess('Filters reset successfully');

				setTimeout(() => {
					const url = new URL(window.location.href);
					url.searchParams.delete('start_date');
					url.searchParams.delete('end_date');
					url.searchParams.delete('lead_farmer_id');
					url.searchParams.delete('farmer_id');
					url.searchParams.delete('status');
					url.searchParams.delete('search');
					window.location.href = `${url.pathname}?${url.searchParams.toString()}`;
				}, 800);
			}
		});
	}

	function exportData() {
		Swal.fire({
			title: 'Export Sales Report',
			text: 'Generate PDF report with current filters?',
			@if(file_exists(public_path('assets/icons/Gif/question2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6B7280',
			confirmButtonText: 'Generate PDF',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				exportToPDF();
			}
		});
	}

	function exportToPDF() {
        showLoading('Generating PDF report...');

        const params = new URLSearchParams(window.location.search);

        params.delete('start_date');
        params.delete('end_date');
        params.delete('lead_farmer_id');
        params.delete('farmer_id');
        params.delete('status');
        params.delete('search');

        const startDate = document.getElementById('start-date')?.value || '';
        const endDate = document.getElementById('end-date')?.value || '';
        const leadFarmerId = document.getElementById('filter-lead-farmer')?.value || '';
        const farmerId = document.getElementById('filter-farmer')?.value || '';
        const status = document.getElementById('filter-status')?.value || '';
        const search = document.getElementById('search')?.value || '';

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (leadFarmerId) params.append('lead_farmer_id', leadFarmerId);
        if (farmerId) params.append('farmer_id', farmerId);
        if (status) params.append('status', status);
        if (search) params.append('search', search);

        window.open(`/admin/sales/export/pdf?${params.toString()}`, '_blank');

        setTimeout(() => {
            Swal.close();
            showSuccess('PDF download started');
        }, 1000);
    }

	function animateCards() {
		const cards = document.querySelectorAll('.sales-card, .stat-card');
		cards.forEach((card, index) => {
			card.style.animationDelay = `${index * 0.05}s`;
		});
	}

	function showSuccess(message) {
		const Toast = Swal.mixin({
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
			timer: 2000,
			timerProgressBar: true,
			didOpen: (toast) => {
				toast.addEventListener('mouseenter', Swal.stopTimer);
				toast.addEventListener('mouseleave', Swal.resumeTimer);
			}
		});

		Toast.fire({
			@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
			title: message
		});
	}

	function showError(title, text = '') {
		if (text) {
			Swal.fire({
				title: title,
				text: text,
				@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
				confirmButtonColor: '#EF4444'
			});
		} else {
			const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000,
				timerProgressBar: true,
				didOpen: (toast) => {
					toast.addEventListener('mouseenter', Swal.stopTimer);
					toast.addEventListener('mouseleave', Swal.resumeTimer);
				}
			});

			Toast.fire({
				@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
				title: title
			});
		}
	}

	function showLoading(message) {
		Swal.fire({
			title: message,
			allowOutsideClick: false,
			@if(file_exists(public_path('assets/icons/Gif/loading4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/loading4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'info' @endif,
			didOpen: () => {
				Swal.showLoading();
			}
		});
	}
</script>
@endsection


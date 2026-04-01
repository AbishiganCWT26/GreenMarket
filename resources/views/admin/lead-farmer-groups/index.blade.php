@extends('admin.layouts.admin_master')

@section('title', 'Lead Farmer Groups Activity')
@section('page-title', 'Lead Farmer Groups Activity')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Admin/lead-farmer-groups-index.css') }}">
@endsection

@section('content')
<div class="groups-container">
	<div class="page-header">
		<h1>
			<i class="fa-solid fa-users-between-lines"></i>
			Lead Farmer Groups Activity
		</h1>
		<button class="refresh-btn" id="refreshData">
			<i class="fas fa-sync-alt"></i>
			Refresh
		</button>
	</div>

	@php
		$totalGroups = $paginatedGroups->total();
		$totalSales = $paginatedGroups->sum('total_sales');
		$totalActiveFarmers = $paginatedGroups->sum('active_farmers');
		$totalProducts = $paginatedGroups->sum('total_products');
		$avgSuccessRate = $paginatedGroups->avg('success_rate');
	@endphp

	<div class="stats-grid">
		<div class="stat-card">
			<div class="stat-header">
				<div class="stat-icon">
					<i class="fa-solid fa-users-between-lines"></i>
				</div>
				<div class="stat-value">{{ $totalGroups }}</div>
			</div>
			<div class="stat-label">Total Groups</div>
		</div>

		<div class="stat-card">
			<div class="stat-header">
				<div class="stat-icon">
					<i class="fas fa-chart-line"></i>
				</div>
				<div class="stat-value">{{ number_format($avgSuccessRate, 1) }}%</div>
			</div>
			<div class="stat-label">Avg Success</div>
		</div>

		<div class="stat-card">
			<div class="stat-header">
				<div class="stat-icon">
					<i class="fas fa-money-bill-wave"></i>
				</div>
				<div class="stat-value">LKR {{ number_format($totalSales, 0) }}</div>
			</div>
			<div class="stat-label">Total Revenue</div>
		</div>

		<div class="stat-card">
			<div class="stat-header">
				<div class="stat-icon">
					<i class="fas fa-user-friends"></i>
				</div>
				<div class="stat-value">{{ $totalActiveFarmers }}</div>
			</div>
			<div class="stat-label">Active Farmers</div>
		</div>
	</div>

	<div class="table-view">
		<div class="table-container">
			<div class="table-controls">
				<div class="title">
					<i class="fas fa-medal"></i>
					Performance Ranking
				</div>
				<div class="rows-per-page">
					<span>Show:</span>
					<select id="tableRowsPerPage">
						<option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
						<option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
						<option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
					</select>
				</div>
			</div>

			<div class="table-responsive">
				<table class="groups-table">
					<thead>
						<tr>
							<th class="rank-cell">
								<i class="fas fa-medal"></i>
								Rank
							</th>
							<th class="group-name-cell">
								<i class="fa-solid fa-users-between-lines"></i>
								Group
							</th>
							<th class="success-rate-cell">
								<i class="fas fa-percentage"></i>
								Success
							</th>
							<th class="sales-count">
								<i class="fas fa-shopping-cart"></i>
								Orders
							</th>
							<th class="total-sales">
								<i class="fas fa-money-bill"></i>
								Sales
							</th>
							<th class="farmers-count">
								<i class="fas fa-user-check"></i>
								Farmers
							</th>
							<th class="products-count">
								<i class="fas fa-boxes"></i>
								Products
							</th>
						</tr>
					</thead>
					<tbody>
						@forelse($paginatedGroups as $group)
							@php
								$rankClass = 'rank-4-plus';
								if ($group->rank == 1) $rankClass = 'rank-1';
								elseif ($group->rank == 2) $rankClass = 'rank-2';
								elseif ($group->rank == 3) $rankClass = 'rank-3';
							@endphp

							<tr class="{{ $rankClass }}" data-group-id="{{ $group->id }}">
								<td class="rank-cell">
									<div class="rank-badge">{{ $group->rank }}</div>
								</td>
								<td class="group-name-cell">
									<div class="group-name">{{ $group->group_name }}</div>
									<div class="group-number">{{ $group->group_number }}</div>
								</td>
								<td class="success-rate-cell">
									<div class="success-badge {{ $group->color_class }}">
										<i class="fas fa-{{ $group->success_rate >= 50 ? 'arrow-up' : 'arrow-down' }}"></i>
										{{ $group->success_rate_formatted }}
									</div>
								</td>
								<td class="sales-count">
									{{ $group->sales_count }}
								</td>
								<td class="total-sales">
									{{ $group->total_sales_formatted }}
								</td>
								<td class="farmers-count">
									{{ $group->active_farmers }}
								</td>
								<td class="products-count">
									{{ $group->total_products }}
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="7" class="no-data">
									<i class="fas fa-inbox"></i>
									<p>No groups data available</p>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			<div class="pagination-container">
				<div class="pagination-info">
					Showing <b>{{ $paginatedGroups->firstItem() ?? 0 }}-{{ $paginatedGroups->lastItem() ?? 0 }}</b> of <b>{{ $paginatedGroups->total() }}</b>
				</div>
				<div class="pagination">
					@if ($paginatedGroups->onFirstPage())
						<button disabled><i class="fas fa-chevron-left"></i></button>
					@else
						<button onclick="window.location.href='{{ $paginatedGroups->previousPageUrl() }}'">
							<i class="fas fa-chevron-left"></i>
						</button>
					@endif

					@foreach ($paginatedGroups->getUrlRange(1, $paginatedGroups->lastPage()) as $page => $url)
						@if ($page == $paginatedGroups->currentPage())
							<button class="active">{{ $page }}</button>
						@else
							<button onclick="window.location.href='{{ $url }}'">{{ $page }}</button>
						@endif
					@endforeach

					@if ($paginatedGroups->hasMorePages())
						<button onclick="window.location.href='{{ $paginatedGroups->nextPageUrl() }}'">
							<i class="fas fa-chevron-right"></i>
						</button>
					@else
						<button disabled><i class="fas fa-chevron-right"></i></button>
					@endif
				</div>
				<div class="rows-per-page">
					<span>Per page:</span>
					<select id="tableRowsPerPage2" onchange="changeRowsPerPage(this.value)">
						<option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
						<option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
						<option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
					</select>
				</div>
			</div>

			<div class="print-btn-container">
				<button class="print-btn" id="printTable">
					<i class="fas fa-print"></i>
					Print Report
				</button>
			</div>

			<div class="legend">
				<div class="legend-item">
					<div class="legend-color legend-high"></div>
					<span>High (80%+)</span>
				</div>
				<div class="legend-item">
					<div class="legend-color legend-medium"></div>
					<span>Medium (60-79%)</span>
				</div>
				<div class="legend-item">
					<div class="legend-color legend-low"></div>
					<span>Low (40-59%)</span>
				</div>
				<div class="legend-item">
					<div class="legend-color legend-poor"></div>
					<span>Poor (<40%)</span>
				</div>
			</div>
		</div>
	</div>

	<div class="card-view">
		<div class="table-container">
			<div class="table-controls">
				<div class="title">
					<i class="fas fa-medal"></i>
					Groups Performance
				</div>
				<div class="rows-per-page">
					<span>Show:</span>
					<select id="cardRowsPerPage">
						<option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
						<option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
						<option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20</option>
					</select>
				</div>
			</div>

			<div class="cards-container">
				@forelse($paginatedGroups as $group)
					@php
						$rankClass = 'rank-4-plus';
						if ($group->rank == 1) $rankClass = 'rank-1';
						elseif ($group->rank == 2) $rankClass = 'rank-2';
						elseif ($group->rank == 3) $rankClass = 'rank-3';
					@endphp

					<div class="group-card {{ $rankClass }}" data-group-id="{{ $group->id }}">
						<div class="card-header">
							<div class="card-rank-badge">{{ $group->rank }}</div>
							<div class="card-group-info">
								<div class="card-group-name">{{ $group->group_name }}</div>
								<div class="card-group-number">{{ $group->group_number }}</div>
							</div>
							<div class="card-success-rate {{ $group->color_class }}">
								<i class="fas fa-{{ $group->success_rate >= 50 ? 'arrow-up' : 'arrow-down' }}"></i>
								{{ $group->success_rate_formatted }}
							</div>
						</div>

						<div class="card-body">
							<div class="card-stat">
								<div class="card-stat-label">
									<i class="fas fa-shopping-cart"></i>
									Orders
								</div>
								<div class="card-stat-value">{{ $group->sales_count }}</div>
							</div>
							<div class="card-stat">
								<div class="card-stat-label">
									<i class="fas fa-money-bill"></i>
									Sales
								</div>
								<div class="card-stat-value">{{ $group->total_sales_formatted }}</div>
							</div>
							<div class="card-stat">
								<div class="card-stat-label">
									<i class="fas fa-user-check"></i>
									Farmers
								</div>
								<div class="card-stat-value">{{ $group->active_farmers }}</div>
							</div>
							<div class="card-stat">
								<div class="card-stat-label">
									<i class="fas fa-boxes"></i>
									Products
								</div>
								<div class="card-stat-value">{{ $group->total_products }}</div>
							</div>
						</div>

						<div class="card-footer">
							<div class="card-performance">
								<i class="fas fa-{{ $group->success_rate >= 50 ? 'chart-line' : 'exclamation-triangle' }}"></i>
								<span>{{ $group->success_rate >= 50 ? 'Good' : 'Needs Improvement' }}</span>
							</div>
							<div class="card-actions">
								<button class="card-action-btn view-details" data-group-id="{{ $group->id }}">
									<i class="fas fa-eye"></i>
									View
								</button>
							</div>
						</div>
					</div>
				@empty
					<div class="no-data">
						<i class="fas fa-inbox"></i>
						<p>No groups data available</p>
					</div>
				@endforelse
			</div>

			<div class="pagination-container">
				<div class="pagination-info">
					Showing <b>{{ $paginatedGroups->firstItem() ?? 0 }}-{{ $paginatedGroups->lastItem() ?? 0 }}</b> of <b>{{ $paginatedGroups->total() }}</b>
				</div>
				<div class="pagination">
					@if ($paginatedGroups->onFirstPage())
						<button disabled><i class="fas fa-chevron-left"></i></button>
					@else
						<button onclick="window.location.href='{{ $paginatedGroups->previousPageUrl() }}'">
							<i class="fas fa-chevron-left"></i>
						</button>
					@endif

					@foreach ($paginatedGroups->getUrlRange(1, $paginatedGroups->lastPage()) as $page => $url)
						@if ($page == $paginatedGroups->currentPage())
							<button class="active">{{ $page }}</button>
						@else
							<button onclick="window.location.href='{{ $url }}'">{{ $page }}</button>
						@endif
					@endforeach

					@if ($paginatedGroups->hasMorePages())
						<button onclick="window.location.href='{{ $paginatedGroups->nextPageUrl() }}'">
							<i class="fas fa-chevron-right"></i>
						</button>
					@else
						<button disabled><i class="fas fa-chevron-right"></i></button>
					@endif
				</div>
				<div class="rows-per-page">
					<span>Per page:</span>
					<select id="cardRowsPerPage2" onchange="changeRowsPerPage(this.value)">
						<option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
						<option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
						<option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20</option>
					</select>
				</div>
			</div>

			<div class="print-btn-container">
				<button class="print-btn" id="printTableMobile">
					<i class="fas fa-print"></i>
					Print Report
				</button>
			</div>

			<div class="legend">
				<div class="legend-item">
					<div class="legend-color legend-high"></div>
					<span>High (80%+)</span>
				</div>
				<div class="legend-item">
					<div class="legend-color legend-medium"></div>
					<span>Medium (60-79%)</span>
				</div>
				<div class="legend-item">
					<div class="legend-color legend-low"></div>
					<span>Low (40-59%)</span>
				</div>
				<div class="legend-item">
					<div class="legend-color legend-poor"></div>
					<span>Poor (<40%)</span>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const Swal = window.Swal;

		function showAlert(title, html, icon = 'info', confirmText = 'Close') {
			if (!Swal) {
				alert(title);
				return Promise.resolve({ isConfirmed: true });
			}
			return Swal.fire({
				title: title,
				html: html,
				icon: icon,
				confirmButtonText: confirmText,
				showCancelButton: false,
				allowOutsideClick: true,
				showCloseButton: true,
				width: window.innerWidth < 768 ? '90%' : '400px',
				customClass: {
					popup: 'swal-popup',
					title: 'swal-title',
					htmlContainer: 'swal-html',
					confirmButton: 'swal-confirm'
				},
				buttonsStyling: false
			});
		}

		function showLoading(title = 'Loading...') {
			if (!Swal) {
				console.log(title);
				return { close: function() { console.log('Loading closed'); } };
			}
			return Swal.fire({
				title: title,
				allowOutsideClick: false,
				showConfirmButton: false,
				showCloseButton: false,
				didOpen: () => { Swal.showLoading(); }
			});
		}

		const refreshBtn = document.getElementById('refreshData');

		async function handleRefresh() {
			const originalContent = refreshBtn.innerHTML;
			refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
			refreshBtn.disabled = true;
			const loadingAlert = showLoading('Refreshing...');

			try {
				await new Promise(resolve => setTimeout(resolve, 1000));
				if (loadingAlert && typeof loadingAlert.close === 'function') { loadingAlert.close(); }
				await showAlert('Success', 'Data refreshed successfully', 'success', 'OK');
				window.location.reload();
			} catch (error) {
				console.error('Error:', error);
				if (loadingAlert && typeof loadingAlert.close === 'function') { loadingAlert.close(); }
				await showAlert('Error', 'Failed to refresh data', 'error', 'OK');
			} finally {
				refreshBtn.innerHTML = originalContent;
				refreshBtn.disabled = false;
			}
		}

		async function showGroupDetails(groupId) {
			const groupElement = document.querySelector(`[data-group-id="${groupId}"]`);
			if (!groupElement) return;

			let groupName, groupNumber, successRate, totalSales, salesCount, farmersCount, productsCount, rank;

			if (groupElement.classList.contains('group-card')) {
				groupName = groupElement.querySelector('.card-group-name')?.textContent || 'N/A';
				groupNumber = groupElement.querySelector('.card-group-number')?.textContent || 'N/A';
				successRate = groupElement.querySelector('.card-success-rate')?.textContent?.trim() || '0%';
				rank = groupElement.querySelector('.card-rank-badge')?.textContent || '0';
				const stats = groupElement.querySelectorAll('.card-stat-value');
				salesCount = stats[0]?.textContent || '0';
				totalSales = stats[1]?.textContent || 'LKR 0.00';
				farmersCount = stats[2]?.textContent || '0';
				productsCount = stats[3]?.textContent || '0';
			} else {
				groupName = groupElement.querySelector('.group-name')?.textContent || 'N/A';
				groupNumber = groupElement.querySelector('.group-number')?.textContent || 'N/A';
				successRate = groupElement.querySelector('.success-badge')?.textContent?.trim() || '0%';
				rank = groupElement.querySelector('.rank-badge')?.textContent || '0';
				totalSales = groupElement.querySelector('.total-sales')?.textContent?.trim() || 'LKR 0.00';
				salesCount = groupElement.querySelector('.sales-count')?.textContent?.trim() || '0';
				farmersCount = groupElement.querySelector('.farmers-count')?.textContent?.trim() || '0';
				productsCount = groupElement.querySelector('.products-count')?.textContent?.trim() || '0';
			}

			const html = `
				<div style="font-size: 13px;">
					<div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
						<strong>Group:</strong> ${groupName}<br>
						<strong>Number:</strong> ${groupNumber}<br>
						<strong>Rank:</strong> #${rank}
					</div>
					<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
						<div><strong>Success Rate:</strong><br><span style="font-size: 14px; font-weight: 600;">${successRate}</span></div>
						<div><strong>Total Orders:</strong><br><span style="font-size: 14px; font-weight: 600;">${salesCount}</span></div>
						<div><strong>Total Sales:</strong><br><span style="font-size: 14px; font-weight: 600;">${totalSales}</span></div>
						<div><strong>Active Farmers:</strong><br><span style="font-size: 14px; font-weight: 600;">${farmersCount}</span></div>
						<div><strong>Products:</strong><br><span style="font-size: 14px; font-weight: 600;">${productsCount}</span></div>
					</div>
				</div>
			`;

			await showAlert('Group Details', html, 'info');
		}

		function changeRowsPerPage(value) {
			const url = new URL(window.location.href);
			url.searchParams.set('per_page', value);
			url.searchParams.set('page', 1);
			window.location.href = url.toString();
		}

		// Print functionality
		function printReport() {
			window.print();
		}

		if (refreshBtn) { refreshBtn.addEventListener('click', handleRefresh); }

		const tableRows = document.querySelectorAll('.groups-table tbody tr:not(.no-data)');
		tableRows.forEach(row => {
			row.style.cursor = 'pointer';
			row.addEventListener('click', function(e) {
				const groupId = this.getAttribute('data-group-id');
				if (groupId) showGroupDetails(groupId);
			});
		});

		const viewDetailButtons = document.querySelectorAll('.card-action-btn.view-details');
		viewDetailButtons.forEach(btn => {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				const groupId = this.getAttribute('data-group-id');
				if (groupId) showGroupDetails(groupId);
			});
		});

		const groupCards = document.querySelectorAll('.group-card');
		groupCards.forEach(card => {
			card.addEventListener('click', function(e) {
				if (!e.target.closest('.card-action-btn')) {
					const groupId = this.getAttribute('data-group-id');
					if (groupId) showGroupDetails(groupId);
				}
			});
		});

		// Add print event listeners
		const printBtn = document.getElementById('printTable');
		const printBtnMobile = document.getElementById('printTableMobile');
		
		if (printBtn) {
			printBtn.addEventListener('click', printReport);
		}
		
		if (printBtnMobile) {
			printBtnMobile.addEventListener('click', printReport);
		}

		const tableRowsPerPage = document.getElementById('tableRowsPerPage');
		const tableRowsPerPage2 = document.getElementById('tableRowsPerPage2');
		const cardRowsPerPage = document.getElementById('cardRowsPerPage');
		const cardRowsPerPage2 = document.getElementById('cardRowsPerPage2');

		if (tableRowsPerPage) {
			tableRowsPerPage.value = "{{ request('per_page', 20) }}";
			tableRowsPerPage.addEventListener('change', function() { changeRowsPerPage(this.value); });
		}
		if (tableRowsPerPage2) {
			tableRowsPerPage2.value = "{{ request('per_page', 20) }}";
			tableRowsPerPage2.addEventListener('change', function() { changeRowsPerPage(this.value); });
		}
		if (cardRowsPerPage) {
			cardRowsPerPage.value = "{{ request('per_page', 10) }}";
			cardRowsPerPage.addEventListener('change', function() { changeRowsPerPage(this.value); });
		}
		if (cardRowsPerPage2) {
			cardRowsPerPage2.value = "{{ request('per_page', 10) }}";
			cardRowsPerPage2.addEventListener('change', function() { changeRowsPerPage(this.value); });
		}

		function adjustLayout() {
			const isMobileView = window.innerWidth < 768;
			const tableView = document.querySelector('.table-view');
			const cardView = document.querySelector('.card-view');
			if (tableView && cardView) {
				if (isMobileView) {
					tableView.style.display = 'none';
					cardView.style.display = 'block';
				} else {
					tableView.style.display = 'block';
					cardView.style.display = 'none';
				}
			}
		}

		window.addEventListener('resize', adjustLayout);
		adjustLayout();

		document.addEventListener('keydown', function(e) {
			if (e.ctrlKey && e.key === 'r') { e.preventDefault(); if (refreshBtn) refreshBtn.click(); }
		});
	});
</script>
@endsection

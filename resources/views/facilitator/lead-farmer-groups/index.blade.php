@extends('facilitator.layouts.facilitator_master')

@section('title', 'Lead Farmer Groups')
@section('page-title', 'Lead Farmer Groups Activity')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/lead_farmer_groups_index.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="groups-container">
	<div class="header-section">
		<div class="header-left">
			<div class="header-icon">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					<circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
					<path d="M23 21V19C22.9986 17.1771 21.7652 15.5857 20 15.13M17 3.13C18.7695 3.58317 20.0071 5.178 20.0071 7.005C20.0071 8.832 18.7695 10.4268 17 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				</svg>
			</div>
			<div class="header-text">
				<h1>Lead Farmer Groups</h1>
				<p>Ranked by total sales volume</p>
			</div>
		</div>
	</div>

	<form action="{{ route('facilitator.lead-farmer-groups') }}" method="GET" id="searchForm" class="search-form">
		<input type="hidden" name="view" id="viewInput" value="{{ request('view', 'card') }}">
		<input type="hidden" name="per_page" id="perPageInput" value="{{ request('per_page') }}">
		
		<div class="search-wrapper">
			<input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search by group name or number..." autocomplete="off">
			@if(request('search'))
				<a href="{{ route('facilitator.lead-farmer-groups', array_merge(request()->except('search'), ['search' => null])) }}" class="search-clear" title="Clear search">
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
	</form>

	<div class="view-controls">
		<div class="view-toggler">
			<button type="button" class="view-btn {{ request('view', 'card') == 'card' ? 'active' : '' }}" onclick="updateView('card')">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<rect x="3" y="3" width="7" height="7"/>
					<rect x="14" y="3" width="7" height="7"/>
					<rect x="3" y="14" width="7" height="7"/>
					<rect x="14" y="14" width="7" height="7"/>
				</svg>
				<span>Cards</span>
			</button>
			<button type="button" class="view-btn {{ request('view') == 'table' ? 'active' : '' }}" onclick="updateView('table')">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<line x1="3" y1="6" x2="21" y2="6"/>
					<line x1="3" y1="12" x2="21" y2="12"/>
					<line x1="3" y1="18" x2="21" y2="18"/>
				</svg>
				<span>Table</span>
			</button>
		</div>
	</div>

	@if(request('view', 'card') == 'card')
	<div class="card-view" id="cardView">
		<div class="cards-grid">
			@forelse($paginatedGroups as $index => $group)
			<div class="group-card" data-id="{{ $group->id }}" style="animation-delay: {{ $index * 0.05 }}s">
				<div class="card-rank rank-{{ $group->rank <= 3 ? $group->rank : 'other' }}">
					@if($group->rank == 1)
						<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
							<path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
						</svg>
					@elseif($group->rank == 2)
						<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
							<path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
						</svg>
					@elseif($group->rank == 3)
						<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
							<path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
						</svg>
					@endif
					<span>{{ $group->rank }}</span>
				</div>
				
				<div class="card-media">
					<img src="{{ $group->lead_farmer_photo ? asset('uploads/profile_pictures/' . $group->lead_farmer_photo) : asset('assets/images/farmer.png') }}" 
						 alt="Lead Farmer"
						 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
				</div>

				<div class="card-content">
					<div class="group-identity">
						<span class="group-code">{{ $group->group_number }}</span>
						<h3 class="group-title">{{ $group->group_name }}</h3>
					</div>

					<div class="group-contact">
						<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
							<line x1="12" y1="18" x2="12" y2="18"/>
						</svg>
						<span>{{ $group->primary_mobile }}</span>
					</div>

					<div class="stats-wrapper">
						<div class="stat-block">
							<div class="stat-icon farmers">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
									<circle cx="12" cy="7" r="4"/>
								</svg>
							</div>
							<div class="stat-info">
								<span class="stat-value">{{ $group->total_farmers }}</span>
								<span class="stat-label">Farmers</span>
							</div>
						</div>

						<div class="stat-block">
							<div class="stat-icon products">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<circle cx="9" cy="9" r="7"/>
									<circle cx="15" cy="15" r="7"/>
								</svg>
							</div>
							<div class="stat-info">
								<span class="stat-value">{{ $group->total_products }}</span>
								<span class="stat-label">Products</span>
							</div>
						</div>

						<div class="stat-block">
							<div class="stat-icon orders">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<rect x="3" y="4" width="18" height="16" rx="2"/>
									<line x1="8" y1="10" x2="16" y2="10"/>
								</svg>
							</div>
							<div class="stat-info">
								<span class="stat-value">{{ $group->sales_count }}</span>
								<span class="stat-label">Orders</span>
							</div>
						</div>
					</div>

					<div class="revenue-block">
						<div class="revenue-header">
							<span>Total Revenue</span>
							<span class="revenue-percentage">
								@php
									$maxSales = $paginatedGroups->first()->total_sales ?: 1;
									$percentage = ($group->total_sales / $maxSales) * 100;
								@endphp
								{{ number_format($percentage, 0) }}%
							</span>
						</div>
						<div class="revenue-progress">
							<div class="progress-track">
								<div class="progress-fill" style="width: {{ $percentage }}%"></div>
							</div>
						</div>
						<div class="revenue-amount">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<line x1="12" y1="1" x2="12" y2="23"/>
								<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
							</svg>
							<span>LKR {{ number_format($group->total_sales, 0) }}</span>
						</div>
					</div>
				</div>
			</div>
			@empty
			<div class="empty-state">
				<div class="empty-icon">
					<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
						<circle cx="12" cy="12" r="10"/>
						<line x1="8" y1="12" x2="16" y2="12"/>
						<line x1="12" y1="8" x2="12" y2="16"/>
					</svg>
				</div>
				<h3>No Groups Found</h3>
				<p>No groups match your search criteria</p>
			</div>
			@endforelse
		</div>
	</div>
	@else
	<div class="table-view" id="tableView">
		<div class="table-container">
			<table class="modern-table">
				<thead>
					<tr>
						<th>Rank</th>
						<th>Group</th>
						<th>Contact</th>
						<th>Farmers</th>
						<th>Products</th>
						<th>Orders</th>
						<th>Revenue</th>
					</tr>
				</thead>
				<tbody>
					@forelse($paginatedGroups as $group)
					<tr class="table-row" data-id="{{ $group->id }}">
						<td class="rank-cell">
							<div class="rank-indicator rank-{{ $group->rank <= 3 ? $group->rank : 'other' }}">
								{{ $group->rank }}
							</div>
						</td>
						<td class="group-cell">
							<div class="group-info-wrapper">
								<div class="group-thumb">
									<img src="{{ $group->lead_farmer_photo ? asset('uploads/profile_pictures/' . $group->lead_farmer_photo) : asset('assets/images/farmer.png') }}" 
										 alt=""
										 onerror="this.src='{{ asset('assets/images/farmer.png') }}'">
								</div>
								<div class="group-details">
									<span class="group-name">{{ $group->group_name }}</span>
									<span class="group-number">{{ $group->group_number }}</span>
								</div>
							</div>
						</td>
						<td class="contact-cell">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
							</svg>
							<span>{{ $group->primary_mobile }}</span>
						</td>
						<td class="numeric-cell">
							<div class="numeric-badge">{{ $group->total_farmers }}</div>
						</td>
						<td class="numeric-cell">
							<div class="numeric-badge">{{ $group->total_products }}</div>
						</td>
						<td class="numeric-cell">{{ $group->sales_count }}</td>
						<td class="revenue-cell">
							<span>LKR {{ number_format($group->total_sales, 0) }}</span>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="7" class="empty-table">
							<div class="empty-state small">
								<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
									<circle cx="12" cy="12" r="10"/>
									<line x1="8" y1="12" x2="16" y2="12"/>
								</svg>
								<p>No groups found</p>
							</div>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
	@endif

	<div class="pagination-section">
		{{ $paginatedGroups->appends(request()->query())->links('vendor.pagination.compact1') }}
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
		icon: 'success',
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
		icon: 'error',
		title: 'Error!',
		text: '{{ session('error') }}',
		timer: 3000,
		showConfirmButton: false,
		background: '#ffffff',
		iconColor: '#ef4444'
	});
	@endif
});
</script>
@endsection
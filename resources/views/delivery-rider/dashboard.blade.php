@extends('delivery-rider.layouts.delivery_rider_master')

@section('title', '| Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/delivery-rider/dashboard.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('page-title')
Delivery Rider Dashboard
@endsection

@section('content')
<div class="dashboard-wrap">
	<div class="dashboard-container">
		<div class="welcome-row">
			<div>
				<h2>Welcome back, {{ $rider->name ?? 'Partner' }}!</h2>
				<p><i class="fa-regular fa-calendar"></i> {{ date('l, F j, Y') }}</p>
			</div>
			<div class="status-tool">
				<div class="status-info">
					<span class="status-label">Availability</span>
					<span class="status-value" id="riderStatusText">{{ $rider && $rider->is_online ? 'Online' : 'Offline' }}</span>
				</div>
				<label class="toggle-switch">
					<input type="checkbox" id="riderStatusCheckbox" {{ $rider && $rider->is_online ? 'checked' : '' }}>
					<span class="toggle-slider"></span>
				</label>
			</div>
		</div>
		<div class="stats-grid">
			<a href="{{ route('delivery-rider.incoming-shipments') }}" class="stat-card">
				<div class="stat-icon incoming"><i class="fa-solid fa-truck-ramp-box"></i></div>
				<div class="stat-data">
					<span class="stat-value count-up" data-target="{{ $stats['incoming_shipments'] ?? 0 }}">0</span>
					<span class="stat-label">Incoming Shipments</span>
					<span class="stat-desc">Browse dispatches</span>
				</div>
				<i class="fa-solid fa-arrow-right card-arrow"></i>
			</a>
			<a href="{{ route('delivery-rider.active-deliveries') }}" class="stat-card">
				<div class="stat-icon active"><i class="fa-solid fa-motorcycle"></i></div>
				<div class="stat-data">
					<span class="stat-value count-up" data-target="{{ $stats['active_deliveries'] ?? 0 }}">0</span>
					<span class="stat-label">Active Deliveries</span>
					<span class="stat-desc">Manage deliveries</span>
				</div>
				<i class="fa-solid fa-arrow-right card-arrow"></i>
			</a>
			<a href="{{ route('delivery-rider.completed-deliveries') }}" class="stat-card">
				<div class="stat-icon completed"><i class="fa-solid fa-circle-check"></i></div>
				<div class="stat-data">
					<span class="stat-value count-up" data-target="{{ $stats['completed_today'] ?? 0 }}">0</span>
					<span class="stat-label">Completed Today</span>
					<span class="stat-desc">Past tasks</span>
				</div>
				<i class="fa-solid fa-arrow-right card-arrow"></i>
			</a>
			<a href="{{ route('delivery-rider.profile') }}" class="stat-card">
				<div class="stat-icon earnings"><i class="fa-solid fa-user"></i></div>
				<div class="stat-data">
					<span class="stat-label">My Profile</span>
					<span class="stat-desc">Update profile details</span>
				</div>
				<i class="fa-solid fa-arrow-right card-arrow"></i>
			</a>
		</div>

		<div class="dashboard-grid">
			<!-- Today's Schedule Panel -->
			<div class="panel">
				<div class="panel-head">
					<i class="fa-regular fa-calendar-check"></i>
					<h3>Today's Schedule</h3>
					<span class="panel-badge" id="schedule-badge">{{ $todaysSchedule->count() }}</span>
				</div>
				<div class="panel-body">
					<div class="schedule-list" id="schedule-list">
						@forelse($todaysSchedule as $index => $delivery)
							<div class="schedule-item paged-item" data-page-group="schedule" data-index="{{ $index }}">
								<div class="schedule-info">
									<span class="order-num">#{{ $delivery->order->order_number ?? 'N/A' }}</span>
									<span class="buyer-name">{{ $delivery->order->buyer->name ?? 'N/A' }}</span>
									<span class="destination">{{ $delivery->order->buyer->district ?? 'N/A' }}</span>
								</div>
								<div class="schedule-status">
									@if($delivery->delivery_status === 'assigned')
										<span class="status assigned">Assigned</span>
									@elseif($delivery->delivery_status === 'delivering')
										<span class="status delivering">Delivering</span>
									@elseif($delivery->delivery_status === 'completed')
										<span class="status completed">Completed</span>
									@else
										<span class="status">{{ ucfirst($delivery->delivery_status) }}</span>
									@endif
								</div>
							</div>
						@empty
							<div class="empty-message">No deliveries scheduled today</div>
						@endforelse
					</div>
					<div class="compact-pagination" id="schedule-pagination"></div>
				</div>
			</div>

			<!-- Recent Notifications Panel -->
			<div class="panel">
				<div class="panel-head">
					<i class="fa-regular fa-clock"></i>
					<h3>Recent Notifications</h3>
				</div>
				<div class="panel-body">
					<div class="activity-list" id="activity-list">
						@forelse($recentActivity as $index => $activity)
							<div class="activity-item paged-item" data-page-group="activity" data-index="{{ $index }}">
								<div class="activity-icon">
									@if(str_contains(strtolower($activity->type ?? ''), 'assign') || str_contains(strtolower($activity->title ?? ''), 'assign'))
										<i class="fa-solid fa-truck-ramp-box"></i>
									@elseif(str_contains(strtolower($activity->type ?? ''), 'complete') || str_contains(strtolower($activity->title ?? ''), 'complete'))
										<i class="fa-solid fa-circle-check"></i>
									@else
										<i class="fa-solid fa-bell"></i>
									@endif
								</div>
								<div class="activity-content">
									<h4>{{ $activity->title }}</h4>
									<p>{{ $activity->message }}</p>
									<small>{{ $activity->created_at->diffForHumans() }}</small>
								</div>
							</div>
						@empty
							<div class="empty-message">No recent notifications</div>
						@endforelse
					</div>
					<div class="compact-pagination" id="activity-pagination"></div>
				</div>
			</div>

			<div class="panel">
				<div class="panel-head">
					<i class="fa-solid fa-location-dot"></i>
					<h3>Coverage</h3>
				</div>
				<div class="panel-body">
					<div class="district-list">
						@php
							$assignedDistricts = $rider && $rider->assigned_districts ? json_decode($rider->assigned_districts, true) : [];
						@endphp
						@forelse($assignedDistricts as $district)
							<span class="district-tag">{{ $district }}</span>
						@empty
							<div class="empty-message">No districts assigned</div>
						@endforelse
					</div>
				</div>
			</div>

			<div class="panel">
				<div class="panel-head">
					<i class="fa-solid fa-truck"></i>
					<h3>Vehicle</h3>
				</div>
				<div class="panel-body">
					<div class="vehicle-info">
						<div class="vehicle-icon">
							@if(strtolower($rider->vehicle_type ?? '') === 'motorbike' || strtolower($rider->vehicle_type ?? '') === 'bike')
								<i class="fa-solid fa-motorcycle"></i>
							@elseif(strtolower($rider->vehicle_type ?? '') === 'three_wheeler' || strtolower($rider->vehicle_type ?? '') === 'tuk')
								<i class="fa-solid fa-tricycle"></i>
							@else
								<i class="fa-solid fa-truck-pickup"></i>
							@endif
						</div>
						<div class="vehicle-details">
							<div><strong>Type:</strong> {{ $rider->vehicle_type ?? 'Not set' }}</div>
							<div><strong>Plate:</strong> {{ $rider->vehicle_number ?? 'N/A' }}</div>
							<div><strong>Capacity:</strong> {{ $rider->max_kg_capacity ?? 0 }} KG</div>
						</div>
					</div>
					<a href="{{ route('delivery-rider.profile') }}" class="profile-link">Edit Profile <i class="fa-solid fa-arrow-right"></i></a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

	// ── Compact Pagination Helper ──────────────────────────────────────────
	function initPagination(group, containerId, perPage) {
		const items = document.querySelectorAll(`.paged-item[data-page-group="${group}"]`);
		const container = document.getElementById(containerId);
		if (!items.length || !container) return;

		let current = 1;
		const total = Math.ceil(items.length / perPage);

		function show(page) {
			current = page;
			const start = (page - 1) * perPage;
			const end   = start + perPage;
			items.forEach((el, i) => {
				el.style.display = (i >= start && i < end) ? '' : 'none';
			});
			render();
		}

		function render() {
			container.innerHTML = '';
			if (total <= 1) return;

			const wrap = document.createElement('div');
			wrap.className = 'cp-wrap';

			// Prev
			const prev = document.createElement('button');
			prev.className = 'cp-btn' + (current === 1 ? ' cp-disabled' : '');
			prev.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
			prev.disabled = current === 1;
			prev.addEventListener('click', () => show(current - 1));
			wrap.appendChild(prev);

			// Page info
			const info = document.createElement('span');
			info.className = 'cp-info';
			info.textContent = `${current} / ${total}`;
			wrap.appendChild(info);

			// Next
			const next = document.createElement('button');
			next.className = 'cp-btn' + (current === total ? ' cp-disabled' : '');
			next.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
			next.disabled = current === total;
			next.addEventListener('click', () => show(current + 1));
			wrap.appendChild(next);

			container.appendChild(wrap);
		}

		show(1);
	}

	initPagination('schedule', 'schedule-pagination', 3);
	initPagination('activity', 'activity-pagination', 3);
	// ──────────────────────────────────────────────────────────────────────
	const counters = document.querySelectorAll('.count-up');
	counters.forEach(counter => {
		const target = parseInt(counter.getAttribute('data-target'));
		let current = 0;
		const increment = target / 50;
		const updateCounter = () => {
			current += increment;
			if (current < target) {
				counter.innerText = Math.floor(current);
				setTimeout(updateCounter, 20);
			} else {
				counter.innerText = target;
			}
		};
		updateCounter();
	});

	const statusCheckbox = document.getElementById('riderStatusCheckbox');
	const statusText = document.getElementById('riderStatusText');

	if (statusCheckbox) {
		statusCheckbox.addEventListener('change', function() {
			const isOnline = this.checked;
			statusCheckbox.disabled = true;
			statusText.innerText = 'Updating...';

			fetch("{{ route('delivery-rider.dashboard.toggle-status') }}", {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify({ is_online: isOnline })
			})
			.then(response => response.json())
			.then(data => {
				statusCheckbox.disabled = false;
				if (data.success) {
					statusText.innerText = data.is_online ? 'Online' : 'Offline';
					Swal.fire({
						toast: true,
						position: 'top-end',
						icon: 'success',
						title: data.message,
						showConfirmButton: false,
						timer: 3000
					});
				} else {
					statusCheckbox.checked = !isOnline;
					statusText.innerText = !isOnline ? 'Online' : 'Offline';
					Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to update status' });
				}
			})
			.catch(() => {
				statusCheckbox.disabled = false;
				statusCheckbox.checked = !isOnline;
				statusText.innerText = !isOnline ? 'Online' : 'Offline';
				Swal.fire({ icon: 'error', title: 'Error', text: 'Connection error occurred' });
			});
		});
	}
});
</script>
@endsection
@extends('admin.layouts.admin_master')
@section('title', 'Delivery Rider Assignment')
@section('page-title', 'Delivery Rider Assignment')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/delivery-rider-assignment.css') }}">
@endsection

@section('content')
<div class="assignment-page-header">
    <h2><i class="fa-solid fa-truck-fast"></i> Unassigned Delivery Orders</h2>
</div>

{{-- Stats Cards --}}
@php
    $criticalCount = isset($unassignedOrders) ? $unassignedOrders->where('urgency', 'critical')->count() : 0;
    $highCount = isset($unassignedOrders) ? $unassignedOrders->where('urgency', 'high')->count() : 0;
    $mediumCount = isset($unassignedOrders) ? $unassignedOrders->where('urgency', 'medium')->count() : 0;
    $totalCount = isset($unassignedOrders) ? $unassignedOrders->count() : 0;
@endphp
<div class="assignment-stats">
    <div class="stat-card">
        <div class="stat-icon total"><i class="fa-solid fa-clipboard-list"></i></div>
        <div class="stat-info"><h4>Total Unassigned</h4><span class="stat-value">{{ $totalCount }}</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon critical"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="stat-info"><h4>Past ETA</h4><span class="stat-value">{{ $criticalCount }}</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><i class="fa-solid fa-clock"></i></div>
        <div class="stat-info"><h4>High Urgency (&lt;10 min)</h4><span class="stat-value">{{ $highCount }}</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon safe"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="stat-info"><h4>Medium (10-30 min)</h4><span class="stat-value">{{ $mediumCount }}</span></div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <label><i class="fa-solid fa-filter"></i> Filters:</label>
    <select id="filterDistrict">
        <option value="">All Districts</option>
        @if(isset($districts))
            @foreach($districts as $district)
                <option value="{{ $district }}">{{ $district }}</option>
            @endforeach
        @endif
    </select>
    <select id="filterUrgency">
        <option value="">All Urgency</option>
        <option value="critical">Past ETA (Critical)</option>
        <option value="high">High (&lt;10 min)</option>
        <option value="medium">Medium (10-30 min)</option>
        <option value="low">Safe (&gt;30 min)</option>
    </select>
    <button class="btn-filter" onclick="applyFilters()"><i class="fa-solid fa-search"></i> Filter</button>
    <button class="btn-filter-reset" onclick="resetFilters()"><i class="fa-solid fa-rotate-left"></i> Reset</button>
</div>

{{-- Bulk Actions --}}
<div class="bulk-actions-bar" id="bulkActionsBar">
    <span class="selected-count"><span id="selectedCount">0</span> orders selected</span>
    <select id="bulkRiderSelect"><option value="">Select Rider...</option></select>
    <button class="btn-bulk-assign" onclick="bulkAssign()"><i class="fa-solid fa-users-gear"></i> Assign All Selected</button>
</div>

{{-- Orders Table --}}
<div class="orders-table-wrapper">
    <div class="table-header">
        <h3><i class="fa-solid fa-list-check"></i> Pending Assignments</h3>
    </div>

    @if(!isset($unassignedOrders) || $unassignedOrders->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-circle-check"></i>
            <h4>All Clear!</h4>
            <p>No unassigned delivery orders at this time.</p>
        </div>
    @else
        <table class="orders-table">
            <thead>
                <tr>
                    <th><input type="checkbox" class="order-checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                    <th>Order #</th>
                    <th>District</th>
                    <th>Bus ETA</th>
                    <th>Countdown</th>
                    <th>Urgency</th>
                    <th>Assign Rider</th>
                    <th>Action</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($unassignedOrders as $order)
                <tr class="order-row urgency-{{ $order->urgency }}" 
                    data-district="{{ $order->district ?? '' }}" 
                    data-urgency="{{ $order->urgency }}"
                    data-eta="{{ $order->bus_estimated_arrival_time }}">
                    <td><input type="checkbox" class="order-checkbox row-checkbox" value="{{ $order->id }}" onchange="updateBulkBar()"></td>
                    <td><strong>#{{ $order->order_number ?? $order->id }}</strong></td>
                    <td>{{ $order->district ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->bus_estimated_arrival_time)->format('h:i A') }}</td>
                    <td>
                        <span class="countdown-timer" data-eta="{{ $order->bus_estimated_arrival_time }}">--:--</span>
                    </td>
                    <td>
                        <span class="urgency-badge {{ $order->urgency }}">
                            @if($order->urgency === 'critical') <i class="fa-solid fa-skull-crossbones"></i> Past ETA
                            @elseif($order->urgency === 'high') <i class="fa-solid fa-fire"></i> High
                            @elseif($order->urgency === 'medium') <i class="fa-solid fa-exclamation"></i> Medium
                            @else <i class="fa-solid fa-check"></i> Safe @endif
                        </span>
                    </td>
                    <td>
                        <div class="rider-select-wrapper">
                            <select class="rider-dropdown" data-district="{{ $order->district ?? '' }}" data-order="{{ $order->id }}">
                                <option value="">Select rider...</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <button class="btn-assign" onclick="assignRider({{ $order->id }}, this)" disabled>
                            <i class="fa-solid fa-user-check"></i> Assign
                        </button>
                    </td>
                    <td>
                        <button class="btn-expand" onclick="toggleDetails({{ $order->id }}, this)">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </td>
                </tr>
                <tr class="order-details-row" id="details-{{ $order->id }}">
                    <td colspan="9">
                        <div class="order-details-content">
                            <div class="details-grid">
                                <div class="detail-section">
                                    <h5><i class="fa-solid fa-user"></i> Buyer Details</h5>
                                    <p><strong>Name:</strong> {{ $order->buyer_name ?? 'N/A' }}</p>
                                    <p><strong>Phone:</strong> {{ $order->buyer_phone ?? 'N/A' }}</p>
                                    <p><strong>Address:</strong> {{ $order->delivery_address ?? 'N/A' }}</p>
                                    <p><strong>District:</strong> {{ $order->district ?? 'N/A' }}</p>
                                </div>
                                <div class="detail-section">
                                    <h5><i class="fa-solid fa-box"></i> Product Details</h5>
                                    <p><strong>Items:</strong> {{ $order->product_details ?? 'N/A' }}</p>
                                    <p><strong>Total:</strong> Rs. {{ number_format($order->total_amount ?? 0, 2) }}</p>
                                </div>
                                <div class="detail-section">
                                    <h5><i class="fa-solid fa-bus"></i> Bus Info</h5>
                                    <p><strong>Bus:</strong> {{ $order->bus_details ?? 'N/A' }}</p>
                                    <p><strong>ETA:</strong> {{ \Carbon\Carbon::parse($order->bus_estimated_arrival_time)->format('M d, Y h:i A') }}</p>
                                    <p><strong>Status:</strong> {{ $order->delivery_status ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Assignment History --}}
<div class="history-section">
    <div class="section-header" onclick="toggleHistory()">
        <h3><i class="fa-solid fa-clock-rotate-left"></i> Assignment History</h3>
        <i class="fa-solid fa-chevron-down" id="historyToggleIcon"></i>
    </div>
    <div class="history-body" id="historyBody">
        @if(!isset($assignmentHistory) || $assignmentHistory->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-inbox"></i>
                <h4>No History</h4>
                <p>No manual assignments have been made yet.</p>
            </div>
        @else
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Assigned Rider</th>
                        <th>District</th>
                        <th>Assigned At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignmentHistory as $entry)
                    <tr>
                        <td>#{{ $entry->order_number ?? $entry->id }}</td>
                        <td>{{ $entry->admin_assigned_rider_id }}</td>
                        <td>{{ $entry->district ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($entry->assigned_by_admin_at)->format('M d, h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper to parse assigned_districts and format rider label
    function formatRiderOption(rider) {
        let area = 'All Districts';
        if (rider.assigned_districts) {
            try {
                const parsed = JSON.parse(rider.assigned_districts);
                if (Array.isArray(parsed)) area = parsed.join(', ');
            } catch (e) {
                area = rider.assigned_districts;
            }
        }
        return `${rider.name} (Load: ${rider.current_load}) - ${rider.vehicle_type || 'N/A'} [${area}]`;
    }

    // Load available riders for each per-row dropdown
    document.querySelectorAll('.rider-dropdown').forEach(function(select) {
        const district = select.dataset.district;
        if (district) {
            fetch(`/admin/delivery-assignments/riders/${encodeURIComponent(district)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.riders && data.riders.length > 0) {
                        data.riders.forEach(rider => {
                            const opt = document.createElement('option');
                            opt.value = rider.id;
                            opt.textContent = formatRiderOption(rider);
                            select.appendChild(opt);
                        });
                    } else {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'No riders available';
                        opt.disabled = true;
                        select.appendChild(opt);
                    }
                }).catch(err => {
                    console.error('Failed to load riders for district:', district, err);
                });
        }
        // Enable assign button when rider is selected
        select.addEventListener('change', function() {
            const btn = this.closest('tr').querySelector('.btn-assign');
            btn.disabled = !this.value;
        });
    });

    // Load ALL riders for bulk assignment dropdown
    const bulkSelect = document.getElementById('bulkRiderSelect');
    if (bulkSelect) {
        fetch('/admin/delivery-assignments/all-riders')
            .then(r => r.json())
            .then(data => {
                if (data.riders && data.riders.length > 0) {
                    data.riders.forEach(rider => {
                        const opt = document.createElement('option');
                        opt.value = rider.id;
                        opt.textContent = formatRiderOption(rider);
                        bulkSelect.appendChild(opt);
                    });
                }
            }).catch(err => {
                console.error('Failed to load bulk riders:', err);
            });
    }

    // Real-time countdown timers
    setInterval(updateCountdowns, 1000);
    updateCountdowns();
});

function updateCountdowns() {
    document.querySelectorAll('.countdown-timer').forEach(function(el) {
        const eta = new Date(el.dataset.eta);
        const now = new Date();
        const diff = eta - now;
        const mins = Math.floor(Math.abs(diff) / 60000);
        const secs = Math.floor((Math.abs(diff) % 60000) / 1000);

        if (diff < 0) {
            el.textContent = `-${mins}m ${secs}s`;
            el.className = 'countdown-timer expired';
        } else if (mins < 10) {
            el.textContent = `${mins}m ${secs}s`;
            el.className = 'countdown-timer danger';
        } else if (mins <= 30) {
            el.textContent = `${mins}m ${secs}s`;
            el.className = 'countdown-timer warning';
        } else {
            el.textContent = `${mins}m ${secs}s`;
            el.className = 'countdown-timer safe';
        }
    });
}

function assignRider(shipmentId, btn) {
    const row = btn.closest('tr');
    const riderId = row.querySelector('.rider-dropdown').value;
    if (!riderId) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Assigning...';

    fetch('/admin/delivery-assignments/assign', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' },
        body: JSON.stringify({ shipment_id: shipmentId, rider_id: riderId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Assigned!', text: data.message, timer: 2000, showConfirmButton: false });
            row.style.opacity = '0.4';
            setTimeout(() => row.remove(), 1500);
            const detailsRow = document.getElementById('details-' + shipmentId);
            if (detailsRow) setTimeout(() => detailsRow.remove(), 1500);
        } else {
            Swal.fire({ icon: 'error', title: 'Failed', text: data.message });
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-user-check"></i> Assign';
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.' });
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-user-check"></i> Assign';
    });
}

function toggleDetails(orderId, btn) {
    const row = document.getElementById('details-' + orderId);
    row.classList.toggle('show');
    btn.classList.toggle('expanded');
}

function toggleHistory() {
    document.getElementById('historyBody').classList.toggle('show');
    document.getElementById('historyToggleIcon').style.transform =
        document.getElementById('historyBody').classList.contains('show') ? 'rotate(180deg)' : '';
}

function toggleSelectAll() {
    const checked = document.getElementById('selectAll').checked;
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = checked);
    updateBulkBar();
}

function updateBulkBar() {
    const count = document.querySelectorAll('.row-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('bulkActionsBar').classList.toggle('show', count > 0);
}

function bulkAssign() {
    const riderId = document.getElementById('bulkRiderSelect').value;
    if (!riderId) { Swal.fire({ icon: 'warning', title: 'Select a Rider', text: 'Please select a rider for bulk assignment.' }); return; }
    const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    if (!ids.length) return;

    Swal.fire({ title: 'Confirm Bulk Assign', text: `Assign ${ids.length} orders to selected rider?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#10b981' })
    .then(result => {
        if (result.isConfirmed) {
            ids.forEach(id => {
                fetch('/admin/delivery-assignments/assign', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ shipment_id: id, rider_id: riderId })
                });
            });
            Swal.fire({ icon: 'success', title: 'Bulk Assignment Sent', timer: 2000, showConfirmButton: false });
            setTimeout(() => location.reload(), 2500);
        }
    });
}

function applyFilters() {
    const district = document.getElementById('filterDistrict').value.toLowerCase();
    const urgency = document.getElementById('filterUrgency').value;
    document.querySelectorAll('.order-row').forEach(row => {
        const matchDistrict = !district || (row.dataset.district || '').toLowerCase().includes(district);
        const matchUrgency = !urgency || row.dataset.urgency === urgency;
        row.style.display = (matchDistrict && matchUrgency) ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filterDistrict').value = '';
    document.getElementById('filterUrgency').value = '';
    document.querySelectorAll('.order-row').forEach(row => row.style.display = '');
}
</script>
@endsection

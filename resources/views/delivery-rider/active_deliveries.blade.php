@extends('delivery-rider.layouts.delivery_rider_master')

@section('title', '| Active Deliveries')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/active-deliveries.css') }}">
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/components/empty-state.css') }}">
@endsection

@section('page-title')
    <i class="fa-solid fa-motorcycle text-primary me-2"></i> Active Deliveries
@endsection

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="delivery-list-header d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0">Active Assignments</h4>
            <p class="text-secondary mb-0">Manage packages that are currently in your care for local destination distribution</p>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            @if($deliveries->isNotEmpty())
                <select class="form-select form-select-sm" id="sortDeliveries" style="width: auto; cursor: pointer; border-radius: var(--radius-full); padding-left: 12px; padding-right: 30px;">
                    <option value="newest">Pickup Time (Newest)</option>
                    <option value="oldest">Pickup Time (Oldest)</option>
                    <option value="name">Buyer Name (A-Z)</option>
                </select>
            @endif
            <span class="badge bg-success px-3 py-2 fs-6" style="border-radius: var(--radius-full);">{{ $deliveries->count() }} Active</span>
        </div>
    </div>

    @if($deliveries->isEmpty())
        @include('delivery-rider.components.empty_state', [
            'icon' => 'fa-motorcycle',
            'title' => 'No Active Deliveries',
            'message' => 'You don\'t have any active deliveries assigned to you.',
            'actionHint' => 'Pick up a shipment from Incoming Shipments to start.',
            'actionUrl' => route('delivery-rider.incoming-shipments'),
            'actionText' => 'View Incoming Shipments'
        ])
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="deliveries-container">
            @foreach($deliveries as $delivery)
                <div class="col animate__animated animate__fadeInUp delivery-card-wrapper" 
                     data-time="{{ $delivery->updated_at->timestamp }}" 
                     data-name="{{ strtolower($delivery->order->buyer->name ?? '') }}">
                    @include('delivery-rider.components.active_delivery_card', ['delivery' => $delivery])
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Sort Deliveries ──────────────────────────────────────────────────────
    const sortSelect = document.getElementById('sortDeliveries');
    const container  = document.getElementById('deliveries-container');
    if (sortSelect && container) {
        sortSelect.addEventListener('change', function () {
            const value = this.value;
            const cards = Array.from(container.querySelectorAll('.delivery-card-wrapper'));
            cards.sort((a, b) => {
                if (value === 'oldest') return a.dataset.time - b.dataset.time;
                if (value === 'newest') return b.dataset.time - a.dataset.time;
                if (value === 'name')   return a.dataset.name.localeCompare(b.dataset.name);
                return 0;
            });
            cards.forEach(card => container.appendChild(card));
        });
    }

    // ── Confirm Arrival in District ──────────────────────────────────────────
    document.querySelectorAll('.confirm-arrival-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const deliveryId  = this.dataset.deliveryId;
            const busNumber   = this.dataset.busNumber;
            const district    = this.dataset.district;

            Swal.fire({
                title: '<i class="fa-solid fa-map-pin" style="color:#f59e0b"></i> Confirm Arrival',
                html: `
                    <p style="margin-bottom:8px">Have you collected the products from:</p>
                    <div style="background:#fef3c7;border-radius:8px;padding:10px 16px;margin-bottom:8px;text-align:left">
                        <b><i class="fa-solid fa-bus" style="color:#d97706"></i> Bus:</b> ${busNumber}<br>
                        <b><i class="fa-solid fa-location-dot" style="color:#d97706"></i> District:</b> ${district}
                    </div>
                    <p style="font-size:13px;color:#6b7280">This will update the order status and notify the buyer.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor:  '#6b7280',
                confirmButtonText:  '<i class="fa-solid fa-check me-1"></i> Yes, Confirm Arrival',
                cancelButtonText:   'Cancel',
                reverseButtons: true,
            }).then((result) => {
                if (!result.isConfirmed) return;

                // Show loading
                Swal.fire({
                    title: 'Updating...',
                    text:  'Confirming your arrival in the district.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`/delivery-rider/deliveries/${deliveryId}/confirm-arrival`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                                        || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({})
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // ── Update badge ──────────────────────────────────────
                        const badge = document.getElementById(`status-badge-${deliveryId}`);
                        if (badge) {
                            badge.className = 'status-indicator-arrived delivery-status-badge';
                            badge.innerHTML = '<span class="ping-dot"></span> Arrived in District';
                        }

                        // ── Swap action buttons ───────────────────────────────
                        const actionDiv = document.getElementById(`action-buttons-${deliveryId}`);
                        if (actionDiv) {
                            actionDiv.innerHTML = `
                                <a href="/delivery-rider/deliveries/${deliveryId}" class="btn btn-light btn-sm flex-grow-1 border">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Details
                                </a>
                                <a href="/delivery-rider/deliveries/${deliveryId}#completeDeliveryForm"
                                   class="btn btn-success btn-sm flex-grow-1 fw-semibold">
                                    <i class="fa-solid fa-check-double me-1"></i> Complete Delivery
                                </a>
                            `;
                        }

                        // ── Add arrived banner ────────────────────────────────
                        const card = document.getElementById(`delivery-card-${deliveryId}`);
                        if (card) {
                            card.classList.add('arrived-district-card');
                            const adminBanner = card.querySelector('.admin-assigned-banner');
                            if (adminBanner) adminBanner.remove();
                            if (!card.querySelector('.arrived-district-banner')) {
                                const banner = document.createElement('div');
                                banner.className = 'arrived-district-banner';
                                banner.innerHTML = '<i class="fa-solid fa-location-dot"></i> <span>You\'ve arrived in the buyer\'s district — Ready to complete delivery</span>';
                                card.querySelector('.card-body-details').insertAdjacentElement('beforebegin', banner);
                            }
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Arrival Confirmed!',
                            text: 'The buyer has been notified. You can now complete the delivery.',
                            confirmButtonColor: '#22c55e',
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to confirm arrival.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Network error. Please try again.', 'error');
                });
            });
        });
    });
});
</script>
@endsection


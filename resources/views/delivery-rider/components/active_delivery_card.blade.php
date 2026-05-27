@php
    $status = $delivery->delivery_status;
    $isAdminAssigned = $status === 'rider_assigned' && $delivery->admin_assigned_rider_id;
    $isArrivedDistrict = $status === 'arrived_district';
    $isDelivering = in_array($status, ['delivering', 'rider_assigned']);

    $statusLabel = match($status) {
        'arrived_district' => 'Arrived in District',
        'rider_assigned'   => 'Admin Assigned',
        default            => 'Delivering',
    };
    $statusClass = match($status) {
        'arrived_district' => 'status-indicator-arrived',
        'rider_assigned'   => 'status-indicator-assigned',
        default            => 'status-indicator-active',
    };
@endphp

<div class="delivery-card card-premium {{ $isAdminAssigned ? 'admin-assigned-card' : '' }} {{ $isArrivedDistrict ? 'arrived-district-card' : '' }}"
     id="delivery-card-{{ $delivery->id }}">
    
    <!-- Card Header with Order Badge and Status -->
    <div class="card-header-flex">
        <div class="order-badge">
            <i class="fa-solid fa-file-invoice"></i>
            <span>{{ $delivery->order->order_number ?? 'N/A' }}</span>
        </div>
        <span class="{{ $statusClass }} delivery-status-badge" id="status-badge-{{ $delivery->id }}">
            <span class="ping-dot"></span> {{ $statusLabel }}
        </span>
    </div>

    <!-- Admin Assigned Banner -->
    @if($isAdminAssigned && !$isArrivedDistrict)
        <div class="admin-assigned-banner">
            <i class="fa-solid fa-user-shield"></i>
            <span>Assigned by Admin — Please pick up and deliver this order</span>
        </div>
    @endif

    <!-- Arrived in District Banner -->
    @if($isArrivedDistrict)
        <div class="arrived-district-banner">
            <i class="fa-solid fa-location-dot"></i>
            <span>You've arrived in the buyer's district — Ready to complete delivery</span>
        </div>
    @endif

    <!-- Card Details Section -->
    <div class="card-body-details mt-3">
        <div class="detail-row">
            <span class="detail-label"><i class="fa-solid fa-user"></i> Buyer:</span>
            <span class="detail-value fw-semibold">{{ $delivery->order->buyer->name ?? 'N/A' }}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label"><i class="fa-solid fa-phone"></i> Mobile:</span>
            <span class="detail-value">
                <a href="tel:{{ $delivery->order->buyer->primary_mobile ?? '' }}" class="text-decoration-none fw-medium">
                    {{ $delivery->order->buyer->primary_mobile ?? 'N/A' }}
                </a>
            </span>
        </div>

        <div class="detail-row">
            <span class="detail-label"><i class="fa-solid fa-location-dot"></i> Destination:</span>
            <span class="detail-value text-truncate-2">{{ $delivery->order->buyer->residential_address ?? 'N/A' }}, {{ $delivery->order->buyer->district ?? '' }}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label"><i class="fa-solid fa-map"></i> District:</span>
            <span class="detail-value">{{ $delivery->order->buyer->district ?? 'N/A' }}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label"><i class="fa-solid fa-boxes-packing"></i> Products:</span>
            <span class="detail-value fw-medium">{{ $delivery->order->orderItems->count() ?? 0 }} items</span>
        </div>

        <div class="detail-row">
            <span class="detail-label"><i class="fa-solid fa-money-bill-wave"></i> Order Total:</span>
            <span class="detail-value fw-bold text-success">Rs. {{ number_format($delivery->order->total_amount ?? 0, 2) }}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label"><i class="fa-regular fa-clock"></i> {{ $isAdminAssigned ? 'Assigned At:' : 'Pickup Time:' }}</span>
            <span class="detail-value">{{ $delivery->updated_at->format('M d, h:i A') }}</span>
        </div>
    </div>

    <!-- Card Footer Actions -->
    <div class="card-footer-actions mt-4 border-top">
        <!-- Navigate & Call Buttons -->
        <div class="d-flex gap-2">
            @php
                $address = urlencode(($delivery->order->buyer->residential_address ?? '') . ', ' . ($delivery->order->buyer->district ?? ''));
                $mapLink = $delivery->order->buyer->google_map_link ?? "https://www.google.com/maps/search/?api=1&query={$address}";
            @endphp
            <a href="{{ $mapLink }}" target="_blank" class="btn btn-outline-primary btn-sm flex-grow-1">
                <i class="fa-solid fa-location-arrow me-1"></i> Navigate
            </a>
            <a href="tel:{{ $delivery->order->buyer->primary_mobile ?? '' }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                <i class="fa-solid fa-phone me-1"></i> Call
            </a>
        </div>

        <!-- Action Buttons (Details & Status-dependent Button) -->
        <div class="d-flex gap-2" id="action-buttons-{{ $delivery->id }}">
            @if($isDelivering)
                <!-- Step 1: Rider must confirm arrival first -->
                <a href="{{ route('delivery-rider.delivery-details', $delivery->id) }}" class="btn btn-light btn-sm flex-grow-1 border">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Details
                </a>
                <button type="button"
                        class="btn btn-warning btn-sm flex-grow-1 confirm-arrival-btn fw-semibold"
                        data-delivery-id="{{ $delivery->id }}"
                        data-bus-number="{{ $delivery->busDispatch->bus_number ?? 'N/A' }}"
                        data-district="{{ $delivery->order->buyer->district ?? 'N/A' }}">
                    <i class="fa-solid fa-map-pin me-1"></i> Confirm Arrival
                </button>
            @elseif($isArrivedDistrict)
                <!-- Step 2: Arrival confirmed, now complete delivery -->
                <a href="{{ route('delivery-rider.delivery-details', $delivery->id) }}" class="btn btn-light btn-sm flex-grow-1 border">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Details
                </a>
                <a href="{{ route('delivery-rider.delivery-details', $delivery->id) }}#completeDeliveryForm"
                   class="btn btn-success btn-sm flex-grow-1 fw-semibold">
                    <i class="fa-solid fa-check-double me-1"></i> Complete Delivery
                </a>
            @endif
        </div>
    </div>

</div>

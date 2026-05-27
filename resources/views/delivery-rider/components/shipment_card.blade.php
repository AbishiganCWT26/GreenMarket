@php
    $eta = \Carbon\Carbon::parse($dispatch->estimated_arrival_time);
    $cutoff = $eta->copy()->subMinutes(45);
    $now = \Carbon\Carbon::now();
    $windowOpen = $now->lt($cutoff);
    $minutesLeft = $now->diffInMinutes($cutoff, false);
@endphp

<div class="dispatch-card {{ $minutesLeft < 60 && $minutesLeft > 0 ? 'urgent' : '' }} {{ !$windowOpen ? 'expired' : '' }}"
     id="card-{{ $dispatch->id }}"
     data-dispatch-id="{{ $dispatch->id }}"
     onclick="window.location='{{ route('delivery-rider.shipment-details', $dispatch->id) }}'"
     style="cursor: pointer;">

    <div class="card-top">
        <div class="bus-info">
            <div class="bus-icon-wrap">
                <i class="fa-solid fa-bus"></i>
            </div>
            <div>
                <span class="bus-number">Bus {{ $dispatch->bus_number }}</span>
                <span class="conductor-name">{{ $dispatch->conductor_name }}</span>
            </div>
        </div>
        @include('delivery-rider.components.delivery_status_badge', ['dispatch' => $dispatch])
    </div>

    <div class="card-meta">
        <div class="meta-item">
            <i class="fa-regular fa-clock"></i>
            <div>
                <span class="meta-label">Bus Arrives</span>
                <span class="meta-value">{{ $eta->format('M d, h:i A') }}</span>
            </div>
        </div>
        <div class="meta-item">
            <i class="fa-solid fa-phone"></i>
            <div>
                <span class="meta-label">Conductor</span>
                <a href="tel:{{ $dispatch->conductor_mobile }}" class="meta-value phone-link" onclick="event.stopPropagation();">{{ $dispatch->conductor_mobile }}</a>
            </div>
        </div>
        <div class="meta-item">
            <i class="fa-solid fa-boxes-packing"></i>
            <div>
                <span class="meta-label">Orders</span>
                <span class="meta-value orders-val">{{ $dispatch->total_orders ?? $dispatch->riderDeliveries->count() }} orders</span>
            </div>
        </div>
    </div>

    @include('delivery-rider.components.countdown_timer', ['dispatch' => $dispatch])

    <div class="card-actions">
        <!-- Optional details button inside card -->
        <a href="{{ route('delivery-rider.shipment-details', $dispatch->id) }}" class="btn-view" onclick="event.stopPropagation();">
            <i class="fa-solid fa-eye"></i> Details
        </a>
        
        @include('delivery-rider.components.accept_button', ['dispatch' => $dispatch])
    </div>
</div>

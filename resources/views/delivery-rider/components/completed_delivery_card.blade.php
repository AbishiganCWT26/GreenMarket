<div class="delivery-card completed card-premium h-100 d-flex flex-column">
    <div class="card-header-flex">
        <div class="order-badge completed">
            <i class="fa-solid fa-file-invoice"></i>
            <span>{{ $delivery->order->order_number ?? 'N/A' }}</span>
        </div>
        <span class="status-indicator-completed">
            <i class="fa-solid fa-circle-check"></i> Delivered
        </span>
    </div>

    <div class="card-body-details flex-grow-1 mt-3">
        <div class="d-flex gap-3 mb-3">
            {{-- Reference Image Thumbnail --}}
            <div class="delivery-proof-thumb flex-shrink-0 rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 70px; height: 70px;">
                @if($delivery->delivery_proof)
                    <img src="{{ asset($delivery->delivery_proof) }}" alt="Proof" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <i class="fa-solid fa-image text-muted opacity-50 fa-2x"></i>
                @endif
            </div>
            
            <div class="flex-grow-1">
                <div class="detail-row mb-1">
                    <span class="detail-label text-muted small"><i class="fa-solid fa-user me-1"></i> Buyer:</span>
                    <span class="detail-value fw-semibold text-dark">{{ $delivery->order->buyer->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row mb-1">
                    <span class="detail-label text-muted small"><i class="fa-solid fa-boxes-packing me-1"></i> Items:</span>
                    <span class="detail-value text-dark">{{ $delivery->order->orderItems->sum('quantity') ?? 0 }} products</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label text-muted small"><i class="fa-solid fa-calendar-check me-1"></i> Delivered:</span>
                    <span class="detail-value text-secondary">{{ $delivery->updated_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="detail-row border-top pt-2 mt-2">
            <span class="detail-label"><i class="fa-solid fa-hand-holding-dollar"></i> Your Earnings:</span>
            <span class="detail-value text-success fw-bold">
                @php
                    $itemsTotal = $delivery->order ? $delivery->order->orderItems->sum('item_total') : 0;
                    $deliveryFee = $delivery->order ? ($delivery->order->total_amount - $itemsTotal) : 0;
                @endphp
                Rs. {{ number_format(max(0, $deliveryFee), 2) }}
            </span>
        </div>
    </div>

    <div class="card-footer-actions mt-3 pt-3 border-top mt-auto">
        <a href="{{ route('delivery-rider.completed-delivery-details', $delivery->id) }}" class="btn btn-outline-secondary btn-sm w-100 text-dark">
            <i class="fa-solid fa-magnifying-glass me-1"></i> View Full Details
        </a>
    </div>
</div>

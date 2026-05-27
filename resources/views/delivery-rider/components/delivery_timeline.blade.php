<div class="delivery-timeline-container card-premium p-4 mt-4">
    <h5 class="fw-bold mb-4"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Delivery Progress</h5>
    
    <div class="timeline">
        @php
            $status = strtolower($status ?? 'assigned');
            $orderedActive = true;
            $dispatchedActive = in_array($status, ['dispatched', 'delivering', 'completed']);
            $deliveringActive = in_array($status, ['delivering', 'completed']);
            $completedActive = $status === 'completed';
        @endphp

        <!-- Milestone 1 -->
        <div class="timeline-item {{ $orderedActive ? 'active' : '' }}">
            <div class="timeline-badge">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h6 class="fw-bold mb-0">Order Placed & Confirmed</h6>
                </div>
                <div class="timeline-body text-secondary mt-1">
                    <small>Order has been approved and matches delivery allocation criteria.</small>
                </div>
            </div>
        </div>

        <!-- Milestone 2 -->
        <div class="timeline-item {{ $dispatchedActive ? 'active' : '' }}">
            <div class="timeline-badge">
                <i class="fa-solid fa-bus"></i>
            </div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h6 class="fw-bold mb-0">Dispatched via Bus Logistics</h6>
                </div>
                <div class="timeline-body text-secondary mt-1">
                    <small>Items have been handed over to conductor and are currently in transit on the bus route.</small>
                </div>
            </div>
        </div>

        <!-- Milestone 3 -->
        <div class="timeline-item {{ $deliveringActive ? 'active' : '' }}">
            <div class="timeline-badge">
                <i class="fa-solid fa-motorcycle"></i>
            </div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h6 class="fw-bold mb-0">Out for Local Delivery</h6>
                </div>
                <div class="timeline-body text-secondary mt-1">
                    <small>Rider has claimed the package and is actively delivering to the client's destination.</small>
                </div>
            </div>
        </div>

        <!-- Milestone 4 -->
        <div class="timeline-item {{ $completedActive ? 'active' : '' }}">
            <div class="timeline-badge">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h6 class="fw-bold mb-0">Delivered Successfully</h6>
                </div>
                <div class="timeline-body text-secondary mt-1">
                    <small>Handed over to the buyer and completed transaction processing.</small>
                </div>
            </div>
        </div>
    </div>
</div>

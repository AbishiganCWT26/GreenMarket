@extends('delivery-rider.layouts.delivery_rider_master')

@section('title', '| Shipment Details')

@section('styles')
	<link rel="stylesheet" href="{{ asset('css/delivery-rider/shipment-details.css') }}">
@endsection

@section('page-title')
	Shipment Details
@endsection

@section('content')
@php
	$eta     = \Carbon\Carbon::parse($dispatch->estimated_arrival_time);
	$cutoff  = $eta->copy()->subMinutes(45);
	$now     = \Carbon\Carbon::now();
	$open    = $now->lt($cutoff);
	$created = \Carbon\Carbon::parse($dispatch->created_at);
@endphp

<div class="sd-wrap">

	<div class="sd-back-row">
		<a href="{{ route('delivery-rider.incoming-shipments') }}" class="sd-back-link">
			<i class="fa-solid fa-arrow-left"></i> Incoming Shipments
		</a>
		<span class="sd-dispatch-id">Dispatch #{{ $dispatch->id }}</span>
	</div>

	<div class="sd-grid">

		<!-- LEFT: Buyer Info -->
		<div class="sd-left">

			<div class="sd-panel">
				<div class="sd-section-head">
					<i class="fa-solid fa-map-marker-alt"></i>
					<h3>Delivery Destinations <span class="sd-count">{{ $dispatch->riderDeliveries->count() }}</span></h3>
				</div>
				<div class="sd-orders-list">
					@foreach($dispatch->riderDeliveries as $delivery)
						@php
							$address = $delivery->order->buyer->residential_address ?? 'N/A';
							$district = $delivery->order->buyer->district ?? '';
							$fullAddress = $address . ', ' . $district;
							$phone = $delivery->order->buyer->primary_mobile ?? 'N/A';
						@endphp
						<div class="sd-order-card">
							<div class="sd-order-info-wrap">
								<div class="sd-order-left">
									<div class="sd-order-num">#{{ $delivery->order->order_number ?? 'N/A' }}</div>
									<div class="sd-order-buyer">
										<i class="fa-solid fa-user"></i>
										{{ $delivery->order->buyer->name ?? 'N/A' }}
									</div>
									<div class="sd-order-contact">
										<a href="tel:{{ $phone }}" class="sd-phone-link">
											<i class="fa-solid fa-phone"></i> {{ $phone }}
										</a>
									</div>
									<div class="sd-order-address">
										<i class="fa-solid fa-location-dot"></i>
										{{ $fullAddress }}
									</div>
								</div>
								<div class="sd-order-right">
									<div class="sd-order-items">{{ $delivery->order->orderItems->count() }} items</div>
									<div class="sd-order-total">Rs. {{ number_format($delivery->order->total_amount ?? 0, 2) }}</div>
								</div>
							</div>
							
							<div class="sd-map-container mt-3">
								<iframe 
									width="100%" 
									height="150" 
									style="border:0; border-radius: var(--radius-sm);" 
									loading="lazy" 
									allowfullscreen 
									referrerpolicy="no-referrer-when-downgrade" 
									src="https://www.google.com/maps?q={{ urlencode($fullAddress) }}&output=embed">
								</iframe>
							</div>
						</div>
					@endforeach
				</div>
			</div>

			@php
				$mergedItems = collect();
				$grandTotal = 0;
				foreach($dispatch->riderDeliveries as $delivery) {
					if($delivery->order) {
						$mergedItems = $mergedItems->merge($delivery->order->orderItems);
					}
				}
			@endphp

			@if($mergedItems->count())
				<div class="sd-panel">
					<div class="sd-section-head">
						<i class="fa-solid fa-boxes-packing"></i>
						<h3>All Products <span class="sd-count">{{ $mergedItems->count() }}</span></h3>
					</div>
					<div class="sd-items-table-wrap">
						<table class="sd-items-table">
							<thead>
								<tr>
									<th>Product</th>
									<th>Qty</th>
									<th>Unit</th>
									<th class="text-end">Total</th>
								</tr>
							</thead>
							<tbody>
								@foreach($mergedItems as $item)
									@php
										$qty = $item->quantity_ordered ?? $item->quantity ?? 0;
										$price = $item->unit_price_snapshot ?? $item->unit_price ?? 0;
										$itemTotal = $qty * $price;
										$grandTotal += $itemTotal;
									@endphp
									<tr>
										<td>{{ $item->product->name ?? $item->product_name_snapshot ?? 'N/A' }}</td>
										<td>{{ $qty }}</td>
										<td>Rs. {{ number_format($price, 2) }}</td>
										<td class="text-end">Rs. {{ number_format($itemTotal, 2) }}</td>
									</tr>
								@endforeach
							</tbody>
							<tfoot>
								<tr class="sd-table-total-row">
									<td colspan="3" class="text-end fw-bold">Grand Total:</td>
									<td class="text-end fw-bold sd-total-highlight">Rs. {{ number_format($grandTotal, 2) }}</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			@endif

		</div>

		<!-- RIGHT: Bus Info -->
		<div class="sd-right">

			<div class="sd-panel">
				<div class="sd-panel-head">
					<div class="sd-bus-icon">
						<i class="fa-solid fa-bus"></i>
					</div>
					<div>
						<div class="sd-bus-number">Bus {{ $dispatch->bus_number }}</div>
						<div class="sd-status-tag in-transit">
							<i class="fa-solid fa-circle-dot"></i> In Transit
						</div>
					</div>
					<span class="broadcast-tag">
						<i class="fa-solid fa-satellite-dish"></i> Broadcast
					</span>
				</div>

				@if($dispatch->bus_image)
					<div class="sd-bus-img">
						@if(Str::startsWith($dispatch->bus_image, 'uploads/'))
							<img src="{{ asset($dispatch->bus_image) }}" alt="Bus photo">
						@else
							<img src="{{ asset('storage/' . $dispatch->bus_image) }}" alt="Bus photo">
						@endif
					</div>
				@endif

				<div class="sd-info-list">
					<div class="sd-info-row">
						<span class="sd-info-label"><i class="fa-solid fa-hashtag"></i> Bus Number</span>
						<span class="sd-info-value">{{ $dispatch->bus_number }}</span>
					</div>
					<div class="sd-info-row">
						<span class="sd-info-label"><i class="fa-solid fa-user-tie"></i> Conductor</span>
						<span class="sd-info-value">{{ $dispatch->conductor_name }}</span>
					</div>
					<div class="sd-info-row">
						<span class="sd-info-label"><i class="fa-solid fa-phone"></i> Contact</span>
						<a href="tel:{{ $dispatch->conductor_mobile }}" class="sd-info-value sd-phone-link">
							<i class="fa-solid fa-phone"></i> {{ $dispatch->conductor_mobile }}
						</a>
					</div>
					<div class="sd-info-row">
						<span class="sd-info-label"><i class="fa-solid fa-seedling"></i> Lead Farmer</span>
						<span class="sd-info-value">{{ $dispatch->leadFarmer->leadFarmer->name ?? $dispatch->leadFarmer->username ?? 'System Admin' }}</span>
					</div>
				</div>
			</div>

			<div class="sd-panel sd-countdown-panel">
				<div class="sd-timeline">
					<div class="sd-timeline-item">
						<i class="fa-solid fa-box-open text-primary"></i>
						<span>Dispatched: {{ $created->format('h:i A') }}</span>
					</div>
					<div class="sd-timeline-item">
						<i class="fa-solid fa-bus text-warning"></i>
						<span>Bus ETA: {{ $eta->format('h:i A') }}</span>
					</div>
				</div>

				@if($open)
					<div class="sd-cdown-label mt-3">
						<i class="fa-solid fa-hourglass-half"></i>
						Accept window closes in
					</div>
					<div class="sd-cdown-timer"
						 id="sd-timer"
						 data-cutoff="{{ $cutoff->toIso8601String() }}">
						<div class="sd-time-block">
							<span class="sd-time-num" id="sd-h">00</span>
							<span class="sd-time-unit">h</span>
						</div>
						<span class="sd-time-sep">:</span>
						<div class="sd-time-block">
							<span class="sd-time-num" id="sd-m">00</span>
							<span class="sd-time-unit">m</span>
						</div>
						<span class="sd-time-sep">:</span>
						<div class="sd-time-block">
							<span class="sd-time-num" id="sd-s">00</span>
							<span class="sd-time-unit">s</span>
						</div>
					</div>
					<div class="sd-cutoff-time">Cutoff Time: {{ $cutoff->format('h:i A') }}</div>
				@endif
			</div>

			<div class="sd-accept-area">
				@if($open)
					<button class="sd-btn-accept" id="sd-accept-btn"
							data-dispatch="{{ $dispatch->id }}"
							data-bus="{{ $dispatch->bus_number }}"
							data-eta="{{ $eta->format('M d, h:i A') }}">
						<i class="fa-solid fa-circle-check"></i>
						Accept & Claim This Shipment
					</button>
					<form id="sd-accept-form" action="{{ route('delivery-rider.shipments.accept', $dispatch->id) }}" method="POST" style="display:none;">
						@csrf
					</form>
				@else
					<div class="sd-lockout-banner">
						<i class="fa-solid fa-circle-exclamation sd-lockout-icon"></i>
						<div class="sd-lockout-text">
							<strong>Acceptance window closed.</strong><br>
							This shipment is being escalated to Admin.
						</div>
					</div>
					<button class="sd-btn-accept sd-btn-disabled mt-2" disabled>
						<i class="fa-solid fa-ban"></i>
						Window Closed — Cannot Accept
					</button>
				@endif
			</div>

		</div>

	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

	const gifBase = '/assets/icons/Gif/';

	const acceptBtn = document.getElementById('sd-accept-btn');
	if (acceptBtn) {
		acceptBtn.addEventListener('click', function () {
			const bus = acceptBtn.dataset.bus;
			const eta = acceptBtn.dataset.eta;
			Swal.fire({
				title: 'Accept Shipment?',
				html: `You will pick up from <strong>Bus ${bus}</strong> arriving at <strong>${eta}</strong>.`,
				imageUrl: gifBase + 'Order Confirmation1.gif',
				imageWidth: 90,
				imageHeight: 90,
				showCancelButton: true,
				confirmButtonColor: '#10B981',
				cancelButtonColor: '#6b7280',
				confirmButtonText: '<i class="fa-solid fa-circle-check"></i> Yes, Accept',
				cancelButtonText: 'Cancel',
				customClass: { popup: 'swal-compact' }
			}).then(function (result) {
				if (result.isConfirmed) {
					acceptBtn.disabled = true;
					acceptBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Accepting...';
					document.getElementById('sd-accept-form').submit();
				}
			});
		});
	}

	@if(session('success'))
		Swal.fire({
			title: 'Shipment Accepted!',
			text: '{{ session('success') }}',
			imageUrl: gifBase + 'success1.gif',
			imageWidth: 90,
			imageHeight: 90,
			confirmButtonColor: '#10B981',
			confirmButtonText: 'Got it',
			customClass: { popup: 'swal-compact' }
		});
	@endif

	@if(session('error'))
		Swal.fire({
			title: 'Action Failed',
			text: '{{ session('error') }}',
			imageUrl: gifBase + 'Failed1.gif',
			imageWidth: 90,
			imageHeight: 90,
			confirmButtonColor: '#ef4444',
			confirmButtonText: 'Close',
			customClass: { popup: 'swal-compact' }
		});
	@endif

	const timer = document.getElementById('sd-timer');
	if (timer) {
		const cutoff = new Date(timer.dataset.cutoff);
		const hEl = document.getElementById('sd-h');
		const mEl = document.getElementById('sd-m');
		const sEl = document.getElementById('sd-s');
		const panel = timer.closest('.sd-countdown-panel');

		const tick = setInterval(function () {
			const diff = Math.floor((cutoff - Date.now()) / 1000);
			if (diff <= 0) {
				clearInterval(tick);
				timer.innerHTML = '<span class="sd-expired-text">Window Closed</span>';
				if (acceptBtn) { acceptBtn.disabled = true; acceptBtn.className = 'sd-btn-accept sd-btn-disabled'; acceptBtn.innerHTML = '<i class="fa-solid fa-ban"></i> Window Closed'; }
				return;
			}
			const h = Math.floor(diff / 3600);
			const m = Math.floor((diff % 3600) / 60);
			const s = diff % 60;
			if (hEl) hEl.textContent = String(h).padStart(2, '0');
			if (mEl) mEl.textContent = String(m).padStart(2, '0');
			if (sEl) sEl.textContent = String(s).padStart(2, '0');

			const minLeft = diff / 60;
			if (panel) {
				panel.classList.toggle('urgent', minLeft < 60 && minLeft > 0);
				panel.classList.toggle('critical', minLeft < 10);
			}
		}, 1000);
		tick();
	}
});
</script>
@endsection

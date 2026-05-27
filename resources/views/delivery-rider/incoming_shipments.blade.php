@extends('delivery-rider.layouts.delivery_rider_master')

@section('title', '| Incoming Shipments')

@section('styles')

	<link rel="stylesheet" href="{{ asset('css/delivery-rider/incoming-shipments.css') }}">
	<link rel="stylesheet" href="{{ asset('css/delivery-rider/components/shipment-card.css') }}">
	<link rel="stylesheet" href="{{ asset('css/delivery-rider/components/countdown-timer.css') }}">
	<link rel="stylesheet" href="{{ asset('css/delivery-rider/components/accept-button.css') }}">
	<link rel="stylesheet" href="{{ asset('css/delivery-rider/components/delivery-status-badge.css') }}">
	<link rel="stylesheet" href="{{ asset('css/delivery-rider/components/empty-state.css') }}">
@endsection

@section('page-title')
	Incoming Shipments
@endsection

@section('content')
<div class="is-wrap">

	<div class="is-header">
		<div class="is-header-left">
			<div class="is-title-row">
				<i class="fa-solid fa-truck-ramp-box"></i>
				<h2>Available Dispatches</h2>
				<span class="is-count-badge" id="dispatch-count">{{ $dispatches->count() }}</span>
			</div>
			<p class="is-subtitle">Claim shipments in your assigned districts to start delivery</p>
		</div>
		<div class="is-header-right">
			@foreach(json_decode($rider->assigned_districts ?? '[]', true) as $district)
				<span class="district-pill">
					<i class="fa-solid fa-location-dot"></i> {{ $district }}
				</span>
			@endforeach
			<button class="refresh-btn" id="refreshBtn" title="Refresh list">
				<i class="fa-solid fa-arrows-rotate" id="refreshIcon"></i>
				<span>Refresh</span>
			</button>
		</div>
	</div>

	<div id="shipments-container">
		@if($dispatches->isEmpty())
			@include('delivery-rider.components.empty_state', [
				'icon' => 'fa-box-open',
				'title' => 'No Incoming Shipments',
				'message' => 'There are no pending dispatches in your districts right now.',
				'actionHint' => 'Stay online to receive new broadcast alerts.',
				'actionUrl' => route('delivery-rider.dashboard'),
				'actionText' => 'Back to Dashboard'
			])
		@else
			<div class="dispatch-grid" id="dispatch-grid">
				@foreach($dispatches as $dispatch)
					@include('delivery-rider.components.shipment_card', ['dispatch' => $dispatch])
				@endforeach
			</div>
		@endif
	</div>

</div>

<form id="accept-form" method="POST" style="display:none;">
	@csrf
</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

	const gifBase = '/assets/icons/Gif/';

	function swalSuccess(title, text) {
		Swal.fire({
			title: title,
			text: text,
			imageUrl: gifBase + 'success1.gif',
			imageWidth: 90,
			imageHeight: 90,
			imageAlt: 'Success',
			confirmButtonColor: '#10B981',
			confirmButtonText: 'Got it',
			customClass: { popup: 'swal-compact' }
		});
	}

	function swalError(title, text) {
		Swal.fire({
			title: title,
			text: text,
			imageUrl: gifBase + 'Failed1.gif',
			imageWidth: 90,
			imageHeight: 90,
			imageAlt: 'Error',
			confirmButtonColor: '#ef4444',
			confirmButtonText: 'Close',
			customClass: { popup: 'swal-compact' }
		});
	}

	function swalConfirm(busNumber, eta, onConfirm) {
		Swal.fire({
			title: 'Accept Shipment?',
			html: `You will be responsible for picking up from <strong>Bus ${busNumber}</strong> arriving at <strong>${eta}</strong>.`,
			imageUrl: gifBase + 'Order Confirmation1.gif',
			imageWidth: 90,
			imageHeight: 90,
			imageAlt: 'Confirm',
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6b7280',
			confirmButtonText: '<i class="fa-solid fa-circle-check"></i> Yes, Accept',
			cancelButtonText: 'Cancel',
			customClass: { popup: 'swal-compact' }
		}).then(result => {
			if (result.isConfirmed) onConfirm();
		});
	}

	document.querySelectorAll('.ajax-accept-btn').forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.stopPropagation(); // Prevent card click
			const dispatchId = btn.dataset.dispatchId;
			const busNumber  = btn.dataset.bus;
			const eta        = btn.dataset.eta;
			const url        = btn.dataset.url;
			const card       = document.getElementById('card-' + dispatchId);

			swalConfirm(busNumber, eta, function () {
				// Optimistic UI update
				const originalHtml = btn.innerHTML;
				btn.disabled = true;
				btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Accepting...';
				if (card) card.style.opacity = '0.7';

				fetch(url, {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}',
						'Accept': 'application/json',
						'Content-Type': 'application/json'
					}
				})
				.then(response => response.json().then(data => ({ status: response.status, body: data })))
				.then(({ status, body }) => {
					if (status === 200 && body.success) {
						// Success
						btn.innerHTML = '<i class="fa-solid fa-check-double"></i> Accepted!';
						btn.classList.remove('status-active');
						btn.classList.add('status-delivered'); // Green style
						
						Swal.fire({
							title: 'Success!',
							text: body.message,
							icon: 'success',
							timer: 2000,
							showConfirmButton: false
						}).then(() => {
							if (body.redirect) window.location.href = body.redirect;
						});
					} else {
						// Revert optimistic UI
						btn.disabled = false;
						btn.innerHTML = originalHtml;
						if (card) card.style.opacity = '1';
						
						if (status === 409) {
							// Already claimed or expired
							btn.disabled = true;
							btn.innerHTML = '<i class="fa-solid fa-user-lock"></i> Claimed';
							btn.className = 'btn-accept status-claimed';
						}
						
						Swal.fire({
							title: 'Failed',
							text: body.message || 'Something went wrong.',
							icon: 'error',
							confirmButtonColor: '#ef4444'
						});
					}
				})
				.catch(err => {
					btn.disabled = false;
					btn.innerHTML = originalHtml;
					if (card) card.style.opacity = '1';
					console.error(err);
				});
			});
		});
	});

	@if(session('success'))
		swalSuccess('Shipment Accepted!', '{{ session('success') }}');
	@endif

	@if(session('error'))
		swalError('Action Failed', '{{ session('error') }}');
	@endif

	document.getElementById('refreshBtn')?.addEventListener('click', function () {
		const icon = document.getElementById('refreshIcon');
		icon.classList.add('spinning');
		setTimeout(() => window.location.reload(), 400);
	});

	function startCountdowns() {
		document.querySelectorAll('.countdown-timer').forEach(function (timer) {
			const id      = timer.id.replace('timer-', '');
			const cutoff  = new Date(timer.dataset.cutoff);
			const hEl     = document.getElementById('h-' + id);
			const mEl     = document.getElementById('m-' + id);
			const sEl     = document.getElementById('s-' + id);
			const wrap    = document.getElementById('countdown-wrap-' + id);
			const card    = document.getElementById('card-' + id);
			const acceptBtn = document.getElementById('accept-' + id);

			const tick = setInterval(function () {
				const diff = Math.floor((cutoff - Date.now()) / 1000);

				if (diff <= 0) {
					clearInterval(tick);
					wrap.innerHTML = `<div class="window-closed"><i class="fa-solid fa-lock"></i><span>Acceptance window closed</span></div>`;
					if (acceptBtn) { acceptBtn.disabled = true; acceptBtn.className = 'btn-accept disabled'; acceptBtn.innerHTML = '<i class="fa-solid fa-ban"></i> Closed'; }
					if (card) card.classList.add('expired');
					return;
				}

				const h = Math.floor(diff / 3600);
				const m = Math.floor((diff % 3600) / 60);
				const s = diff % 60;

				if (hEl) hEl.textContent = String(h).padStart(2, '0');
				if (mEl) mEl.textContent = String(m).padStart(2, '0');
				if (sEl) sEl.textContent = String(s).padStart(2, '0');

				const totalMin = diff / 60;
				if (card) {
					card.classList.toggle('urgent', totalMin < 60 && totalMin > 0);
					card.classList.toggle('critical', totalMin < 10);
				}
			}, 1000);

			tick();
		});
	}

	startCountdowns();
});
</script>
@endsection

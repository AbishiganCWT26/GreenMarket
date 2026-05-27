@extends('delivery-rider.layouts.delivery_rider_master')

@section('title', '| Delivery Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/delivery-rider/delivery-details.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('page-title')
Delivery Assignment
@endsection

@section('content')
@php
	$buyer = $delivery->order->buyer ?? null;
	$order = $delivery->order ?? null;
	$bus = $delivery->busDispatch ?? null;
	$items = $order ? $order->orderItems : collect();
	$address = ($buyer->residential_address ?? '') . ', ' . ($buyer->district ?? '');
	$mapQuery = urlencode($address);
	$isAdminAssigned = $delivery->delivery_status === 'rider_assigned' && $delivery->admin_assigned_rider_id;
@endphp

<div class="delivery-wrap">
	<div class="delivery-container">

		{{-- Flash Messages --}}
		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
				<i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
				<i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<div class="delivery-top">
			<a href="{{ route('delivery-rider.active-deliveries') }}" class="delivery-back">
				<i class="fa-solid fa-arrow-left"></i> Back
			</a>
			<div class="delivery-badge">
				<i class="fa-regular fa-receipt"></i>
				<span>#{{ $order->order_number ?? 'N/A' }}</span>
			</div>
			@if($isAdminAssigned)
				<div class="delivery-status admin">Admin Assigned</div>
			@else
				<div class="delivery-status active">Delivering</div>
			@endif
			<div class="delivery-time">
				<i class="fa-regular fa-clock"></i>
				<span>{{ $isAdminAssigned ? 'Assigned' : 'Picked up' }} {{ $delivery->updated_at->diffForHumans() }}</span>
			</div>
		</div>

		@if($isAdminAssigned)
			<div class="delivery-alert">
				<i class="fa-solid fa-user-shield"></i>
				<span>This delivery was assigned to you by an Admin. Please pick up from bus station and deliver to the buyer.</span>
			</div>
		@endif

		<div class="delivery-main">
			<div class="delivery-left">
				<div class="delivery-card">
					<div class="card-header">
						<i class="fa-regular fa-user"></i>
						<span>Buyer Details</span>
					</div>
					<div class="card-body">
						<div class="buyer-row">
							<div class="buyer-avatar">
								@if($buyer && $buyer->user && $buyer->user->profile_photo)
									<img src="{{ asset('uploads/profile_pictures/' . $buyer->user->profile_photo) }}" alt="Profile" class="avatar-img buyer-img-trigger">
								@else
									<span>{{ strtoupper(substr($buyer->name ?? 'B', 0, 1)) }}</span>
								@endif
							</div>
							<div class="buyer-details">
								<div class="buyer-name">{{ $buyer->name ?? 'N/A' }}</div>
								<div class="buyer-role">Customer / Buyer</div>
							</div>
						</div>
						<div class="info-group">
							<div class="info-item">
								<span class="info-label"><i class="fa-solid fa-phone"></i> Phone Number</span>
								<span class="info-value"><a href="tel:{{ $buyer->primary_mobile ?? '' }}">{{ $buyer->primary_mobile ?? 'N/A' }}</a></span>
							</div>
							<div class="info-item">
								<span class="info-label"><i class="fa-brands fa-whatsapp"></i> WhatsApp Number</span>
								<span class="info-value">
									@if($buyer->whatsapp_number ?? false)
										<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $buyer->whatsapp_number) }}" target="_blank">{{ $buyer->whatsapp_number }}</a>
									@else
										<span class="muted">Not Provided</span>
									@endif
								</span>
							</div>
							<div class="info-item">
								<span class="info-label"><i class="fa-regular fa-envelope"></i> Email</span>
								<span class="info-value">{{ $buyer->user->email ?? 'N/A' }}</span>
							</div>
							<div class="info-item full">
								<span class="info-label"><i class="fa-solid fa-location-dot"></i> Address</span>
								<span class="info-value">{{ $address }}</span>
							</div>
							<div class="info-item full">
								<span class="info-label"><i class="fa-solid fa-location-dot"></i> Google Map Link</span>
								<span class="info-value">
									<a href="{{ $buyer->google_map_link ?? '#' }}" target="_blank">{{ $buyer->google_map_link ?? 'Not Provided' }}</a>
								</span>
							</div>
						</div>
						<div class="action-group">
							<a href="{{ $buyer->google_map_link ?? 'https://www.google.com/maps/search/?api=1&query=' . $mapQuery }}" target="_blank" class="action-btn map">
								<i class="fa-solid fa-location-arrow"></i> Navigate
							</a>
							<a href="tel:{{ $buyer->primary_mobile ?? '' }}" class="action-btn call">
								<i class="fa-solid fa-phone"></i> Call
							</a>
						</div>
					</div>
				</div>

				<div class="delivery-card">
					<div class="card-header">
						<i class="fa-solid fa-bus"></i>
						<span>Dispatch Info</span>
					</div>
					<div class="card-body">
						@if($bus && $bus->bus_image)
							<div class="bus-img">
								<img src="{{ asset('uploads/bus_photo/' . basename($bus->bus_image)) }}" alt="Bus Image" class="bus-img-trigger">
							</div>
						@endif
						<div class="dispatch-row">
							<div class="dispatch-item">
								<label>Bus Number</label>
								<strong><i class="fa-solid fa-bus"></i> {{ $bus->bus_number ?? 'N/A' }}</strong>
							</div>
							<div class="dispatch-item">
								<label>Conductor</label>
								<strong><i class="fa-solid fa-user-tie"></i> {{ $bus->conductor_name ?? 'N/A' }}</strong>
							</div>
							<div class="dispatch-item">
								<label>Contact</label>
								<strong><a href="tel:{{ $bus->conductor_mobile ?? '' }}">{{ $bus->conductor_mobile ?? 'N/A' }}</a></strong>
							</div>
							<div class="dispatch-item">
								<label>Pickup</label>
								<strong>{{ $delivery->updated_at->format('M d, h:i A') }}</strong>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="delivery-right">
				<div class="delivery-card">
					<div class="card-header">
						<i class="fa-solid fa-carrot"></i>
						<span>Products</span>
						<span class="card-badge">{{ $items->count() }} items</span>
					</div>
					<div class="card-body no-pad">
						<div class="product-list">
							@forelse($items as $idx => $item)
								<label class="product-item">
									<input type="checkbox" class="product-check">
									<div class="product-thumb">
										@if($item->product && !empty($item->product->product_photo) && $item->product->product_photo !== 'product-placeholder.png' && file_exists(public_path('uploads/product_images/' . $item->product->product_photo)))
											<img src="{{ asset('uploads/product_images/' . $item->product->product_photo) }}">
										@else
											<img src="{{ asset('assets/images/product-placeholder.png') }}">
										@endif
									</div>
									<div class="product-detail">
										<div class="product-title">{{ $item->product_name_snapshot ?? 'Product' }}</div>
										<div class="product-quantity">{{ number_format($item->quantity_ordered, 2) }} {{ $item->product->unit_of_measure ?? 'units' }} × Rs. {{ number_format($item->unit_price_snapshot, 2) }}</div>
									</div>
									<div class="product-price">Rs. {{ number_format($item->item_total, 2) }}</div>
								</label>
							@empty
								<div class="empty-items">No items in this order.</div>
							@endforelse
						</div>
						@if($items->isNotEmpty())
							<div class="product-total">
								<span>Grand Total</span>
								<strong>Rs. {{ number_format($items->sum('item_total'), 2) }}</strong>
							</div>
						@endif
					</div>
				</div>

				<div class="delivery-card complete">
					<div class="card-header">
						<i class="fa-solid fa-clipboard-check"></i>
						<span>Complete Delivery</span>
					</div>
					<div class="card-body">
						<div class="complete-note">
							<i class="fa-solid fa-circle-info"></i>
							<span>Upload proof of delivery before submitting.</span>
						</div>
						<form id="completeForm" action="{{ route('delivery-rider.delivery.complete', $delivery->id) }}" method="POST" enctype="multipart/form-data">
							@csrf
							<div class="upload-field">
								<label><i class="fa-solid fa-camera"></i> Delivery Proof <span class="req">*</span></label>
								<div class="upload-area" id="uploadZone">
									<input type="file" name="delivery_proof" id="proofInput" accept="image/jpeg,image/png,image/jpg,application/pdf" hidden required>
									<div class="upload-place" id="uploadPlaceholder">
										<i class="fa-solid fa-cloud-arrow-up"></i>
										<span>Click or drag to upload</span>
										<small>JPG, PNG or PDF — Max 5MB</small>
									</div>
									<div class="upload-prev" id="uploadPreview" style="display:none;">
										<img id="previewImg" src="">
										<button type="button" class="remove-btn" id="removeImage"><i class="fa-solid fa-xmark"></i> Remove</button>
										<span id="fileName"></span>
									</div>
								</div>
							</div>
							<div class="notes-field">
								<label><i class="fa-solid fa-comment-dots"></i> Delivery Notes <span class="opt">(Optional)</span></label>
								<textarea name="notes" rows="3" placeholder="e.g., Left with neighbor, Buyer not home..."></textarea>
								<div class="char-count"><span id="charCount">0</span>/500</div>
							</div>
							<button type="button" class="submit-btn" id="completeBtn">
								<i class="fa-solid fa-check-circle"></i> Mark as Completed
							</button>
						</form>
					</div>
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
	const zone = document.getElementById('uploadZone');
	const input = document.getElementById('proofInput');
	const placeholder = document.getElementById('uploadPlaceholder');
	const preview = document.getElementById('uploadPreview');
	const previewImg = document.getElementById('previewImg');
	const removeBtn = document.getElementById('removeImage');
	const fileName = document.getElementById('fileName');

	zone.addEventListener('click', () => input.click());
	zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('dragover'); });
	zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
	zone.addEventListener('drop', (e) => {
		e.preventDefault();
		zone.classList.remove('dragover');
		if (e.dataTransfer.files.length) {
			input.files = e.dataTransfer.files;
			showPreview(e.dataTransfer.files[0]);
		}
	});

	input.addEventListener('change', function() { if (this.files[0]) showPreview(this.files[0]); });

	function showPreview(file) {
		if (file.size > 5 * 1024 * 1024) {
			Swal.fire({ imageUrl: '{{ asset("assets/icons/Gif/error6.gif") }}', imageWidth: 60, imageHeight: 60, title: 'File Too Large', text: 'Maximum file size is 5MB.', confirmButtonColor: '#10B981' });
			input.value = '';
			return;
		}
		fileName.textContent = file.name;
		if (file.type.startsWith('image/')) {
			const reader = new FileReader();
			reader.onload = (e) => previewImg.src = e.target.result;
			reader.readAsDataURL(file);
			previewImg.style.display = 'block';
		} else {
			previewImg.style.display = 'none';
		}
		placeholder.style.display = 'none';
		preview.style.display = 'flex';
	}

	removeBtn.addEventListener('click', (e) => {
		e.stopPropagation();
		input.value = '';
		placeholder.style.display = 'flex';
		preview.style.display = 'none';
		previewImg.src = '';
		fileName.textContent = '';
	});

	const notesField = document.querySelector('textarea[name="notes"]');
	const charCount = document.getElementById('charCount');
	if (notesField) {
		notesField.addEventListener('input', () => { charCount.textContent = notesField.value.length; });
	}

	document.getElementById('completeBtn').addEventListener('click', function() {
		if (!input.files || !input.files[0]) {
			Swal.fire({ imageUrl: '{{ asset("assets/icons/Gif/alert3.gif") }}', imageWidth: 60, imageHeight: 60, title: 'Proof Required', text: 'Please upload a delivery proof image.', confirmButtonColor: '#10B981' });
			return;
		}
		Swal.fire({
			title: 'Confirm Delivery',
			text: 'This action cannot be undone. The order will be marked as delivered.',
			imageUrl: '{{ asset("assets/icons/Gif/Order Confirmation1.gif") }}',
			imageWidth: 80,
			imageHeight: 80,
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, Complete',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				const btn = document.getElementById('completeBtn');
				btn.disabled = true;
				btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
				document.getElementById('completeForm').submit();
			}
		});
	});

	document.querySelectorAll('.product-check').forEach(cb => {
		cb.addEventListener('change', function() {
			this.closest('.product-item').classList.toggle('checked', this.checked);
		});
	});

	document.querySelectorAll('.buyer-img-trigger, .bus-img-trigger').forEach(img => {
		img.addEventListener('click', function() {
			const isBuyer = this.classList.contains('buyer-img-trigger');
			const title = isBuyer ? 'Buyer Profile Photo' : 'Bus Photo';
			Swal.fire({
				title: title,
				imageUrl: this.src,
				imageAlt: title,
				showCloseButton: true,
				showConfirmButton: false,
				background: '#ffffff',
				padding: '1rem',
				customClass: { popup: 'swal-image-popup', image: 'swal-responsive-image' }
			});
		});
	});

	@if(session('success'))
		Swal.fire({ imageUrl: '{{ asset("assets/icons/Gif/success5.gif") }}', imageWidth: 60, imageHeight: 60, title: 'Success!', text: '{{ session("success") }}', timer: 3000, showConfirmButton: false });
	@endif
	@if(session('error'))
		Swal.fire({ imageUrl: '{{ asset("assets/icons/Gif/error6.gif") }}', imageWidth: 60, imageHeight: 60, title: 'Error', text: '{{ session("error") }}', confirmButtonColor: '#10B981' });
	@endif
});
</script>
@endsection
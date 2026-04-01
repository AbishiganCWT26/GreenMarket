@extends('farmer.layouts.farmer_master')

@section('title', 'My Complaints')
@section('page-title', 'My Complaints')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/farmer/complaints-list.css') }}">
@endsection

@section('content')
<div class="complaint-dashboard">
	<div class="dashboard-header">
		<div class="header-left">
			<div class="header-icon">
				<i class="fas fa-file-contract"></i>
			</div>
			<div class="header-text">
				<h2 class="header-title">My Complaints</h2>
				<p class="header-subtitle">Track and manage your complaints</p>
			</div>
		</div>
		<div class="header-right">
			<a href="{{ route('farmer.complaints.create') }}" class="btn-primary">
				<i class="fas fa-plus"></i>
				<span>New Complaint</span>
			</a>
		</div>
	</div>

	<div class="stats-grid">
		<div class="stat-card stat-total">
			<div class="stat-info">
				<span class="stat-value">{{ $totalComplaints }}</span>
				<span class="stat-label">Total</span>
			</div>
			<div class="stat-icon">
				<i class="fas fa-clipboard-list"></i>
			</div>
		</div>
		<div class="stat-card stat-open">
			<div class="stat-info">
				<span class="stat-value">{{ $openComplaints }}</span>
				<span class="stat-label">Open</span>
			</div>
			<div class="stat-icon">
				<i class="fas fa-clock"></i>
			</div>
		</div>
		<div class="stat-card stat-progress">
			<div class="stat-info">
				<span class="stat-value">{{ $inProgressComplaints }}</span>
				<span class="stat-label">In Progress</span>
			</div>
			<div class="stat-icon">
				<i class="fas fa-spinner"></i>
			</div>
		</div>
		<div class="stat-card stat-resolved">
			<div class="stat-info">
				<span class="stat-value">{{ $resolvedComplaints }}</span>
				<span class="stat-label">Resolved</span>
			</div>
			<div class="stat-icon">
				<i class="fas fa-check-circle"></i>
			</div>
		</div>
	</div>

	<div class="complaints-card">
		<div class="card-header">
			<div class="card-title">
				<i class="fas fa-list"></i>
				<h3>Complaints List</h3>
			</div>
			<div class="card-actions">
				<a href="{{ route('farmer.dashboard') }}" class="btn-secondary">
					<i class="fas fa-arrow-left"></i>
					<span>Back</span>
				</a>
			</div>
		</div>

		@if($complaints->count() > 0)
		<div class="table-responsive">
			<table class="complaints-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Type</th>
						<th>Order</th>
						<th>Date</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					@foreach($complaints as $index => $complaint)
					<tr class="complaint-row" style="animation-delay: {{ $index * 0.05 }}s">
						<td data-label="ID">
							<span class="id-badge">#{{ $complaint->id }}</span>
						</td>
						<td data-label="Type">
							<span class="type-badge type-{{ $complaint->complaint_type }}">
								<i class="fas fa-{{ getComplaintIcon($complaint->complaint_type) }}"></i>
								{{ formatComplaintType($complaint->complaint_type) }}
							</span>
						</td>
						<td data-label="Order">
							@if($complaint->related_order_id)
							<span class="order-ref">
								<i class="fas fa-receipt"></i>
								{{ $complaint->order_number ?? '#' . $complaint->related_order_id }}
							</span>
							@else
							<span class="order-na">—</span>
							@endif
						</td>
						<td data-label="Date">
							<span class="date-value">{{ \Carbon\Carbon::parse($complaint->created_at)->format('d M, Y') }}</span>
							<small class="time-value">{{ \Carbon\Carbon::parse($complaint->created_at)->format('h:i A') }}</small>
						</td>
						<td data-label="Status">
							<span class="status-badge status-{{ $complaint->status }}">
								<i class="fas fa-{{ getStatusIcon($complaint->status) }}"></i>
								{{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
							</span>
						</td>
						<td data-label="Actions">
							<div class="action-group">
								<button class="action-btn view" onclick="viewComplaint({{ $complaint->id }})" title="View">
									<i class="fas fa-eye"></i>
								</button>
								@if($complaint->status == 'new')
								<button class="action-btn edit" onclick="editComplaint({{ $complaint->id }})" title="Edit">
									<i class="fas fa-pen"></i>
								</button>
								<button class="action-btn delete" onclick="deleteComplaint({{ $complaint->id }})" title="Delete">
									<i class="fas fa-trash"></i>
								</button>
								@endif
							</div>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		<div class="pagination-wrap">
			<div class="pagination-info">
				Showing {{ $complaints->firstItem() }} to {{ $complaints->lastItem() }} of {{ $complaints->total() }}
			</div>
			<div class="pagination">
				@if($complaints->onFirstPage())
				<span class="page-link disabled"><i class="fas fa-chevron-left"></i></span>
				@else
				<a href="{{ $complaints->previousPageUrl() }}" class="page-link"><i class="fas fa-chevron-left"></i></a>
				@endif

				@foreach(range(1, $complaints->lastPage()) as $page)
				@if($page == $complaints->currentPage())
				<span class="page-link active">{{ $page }}</span>
				@else
				<a href="{{ $complaints->url($page) }}" class="page-link">{{ $page }}</a>
				@endif
				@endforeach

				@if($complaints->hasMorePages())
				<a href="{{ $complaints->nextPageUrl() }}" class="page-link"><i class="fas fa-chevron-right"></i></a>
				@else
				<span class="page-link disabled"><i class="fas fa-chevron-right"></i></span>
				@endif
			</div>
		</div>
		@else
		<div class="empty-state">
			<div class="empty-icon">
				<i class="fas fa-file-alt"></i>
			</div>
			<h3 class="empty-title">No Complaints Yet</h3>
			<p class="empty-desc">You haven't filed any complaints. Create your first complaint to get started.</p>
			<a href="{{ route('farmer.complaints.create') }}" class="btn-primary">
				<i class="fas fa-plus"></i>
				File Complaint
			</a>
		</div>
		@endif
	</div>
</div>
@endsection

@php
function getComplaintIcon($type) {
	$icons = [
		'payment_delay' => 'clock',
		'payment_missing' => 'money-bill-wave',
		'wrong_data_entry' => 'database',
		'product_quality' => 'star-half-alt',
		'wrong_location' => 'map-pin',
		'farmer_contact' => 'phone-alt',
		'availability_issue' => 'box-open',
		'payment_issue' => 'credit-card',
		'invoice_error' => 'file-invoice',
		'category_misclassification' => 'tags',
		'farmer_no_show' => 'user-slash',
		'product_photo_mismatch' => 'images',
		'request_ignored' => 'bell-slash',
		'filter_issue' => 'filter',
		'vague_instructions' => 'map',
		'payment_technical' => 'bug',
		'other' => 'ellipsis-h'
	];
	return $icons[$type] ?? 'exclamation-circle';
}

function formatComplaintType($type) {
	$types = [
		'payment_delay' => 'Payment Delay',
		'payment_missing' => 'Missing Payment',
		'wrong_data_entry' => 'Wrong Data',
		'product_quality' => 'Product Quality',
		'wrong_location' => 'Wrong Location',
		'farmer_contact' => 'Farmer Contact',
		'availability_issue' => 'Availability',
		'payment_issue' => 'Payment Issue',
		'invoice_error' => 'Invoice Error',
		'category_misclassification' => 'Wrong Category',
		'farmer_no_show' => 'Farmer No Show',
		'product_photo_mismatch' => 'Photo Mismatch',
		'request_ignored' => 'Request Ignored',
		'filter_issue' => 'Filter Issue',
		'vague_instructions' => 'Vague Instructions',
		'payment_technical' => 'Technical Glitch',
		'other' => 'Other Issue'
	];
	return $types[$type] ?? ucfirst(str_replace('_', ' ', $type));
}

function getStatusIcon($status) {
	$icons = [
		'new' => 'circle-plus',
		'in_progress' => 'spinner',
		'resolved' => 'check-circle',
		'rejected' => 'times-circle'
	];
	return $icons[$status] ?? 'circle-info';
}
@endphp

@section('scripts')

<script>
function viewComplaint(id) {
	Swal.fire({
		title: 'Loading...',
		text: 'Please wait',
		allowOutsideClick: false,
		showConfirmButton: false,
		didOpen: () => Swal.showLoading()
	});

	$.ajax({
		url: '{{ url("farmer/complaints/view") }}/' + id,
		type: 'GET',
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		success: function(res) {
			Swal.close();
			if (res.success) {
				const c = res.complaint;
				const icons = {
					'new': 'circle-plus', 'in_progress': 'spinner fa-spin',
					'resolved': 'check-circle', 'rejected': 'times-circle'
				};
				const typeIcons = {
					'payment_delay': 'clock', 'payment_missing': 'money-bill-wave',
					'wrong_data_entry': 'database', 'other': 'ellipsis-h'
				};

				Swal.fire({
					title: 'Complaint Details',
					html: `
						<div class="detail-modal">
							<div class="detail-header">
								<span class="detail-id">#${c.id}</span>
								<span class="detail-status status-${c.status}">
									<i class="fas fa-${icons[c.status] || 'circle-info'}"></i>
									${c.status.replace('_', ' ')}
								</span>
							</div>
							<div class="detail-body">
								<div class="detail-row">
									<i class="fas fa-tag"></i>
									<strong>Type:</strong>
									<span>${c.complaint_type.replace('_', ' ')}</span>
								</div>
								<div class="detail-row">
									<i class="fas fa-calendar"></i>
									<strong>Filed:</strong>
									<span>${c.created_at_formatted}</span>
								</div>
								<div class="detail-row">
									<i class="fas fa-sync"></i>
									<strong>Updated:</strong>
									<span>${c.updated_at_formatted}</span>
								</div>
								<div class="detail-desc">
									<strong>Description:</strong>
									<p>${c.description}</p>
								</div>
							</div>
						</div>
					`,
					width: 500,
					confirmButtonText: 'Close',
					confirmButtonColor: '#10B981'
				});
			} else {
				Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Error', text: res.message || 'Failed to load' });
			}
		},
		error: () => {
			Swal.close();
			Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Error', text: 'Failed to load complaint' });
		}
	});
}

function editComplaint(id) {
	Swal.fire({
		title: 'Loading...',
		text: 'Please wait',
		allowOutsideClick: false,
		showConfirmButton: false,
		didOpen: () => Swal.showLoading()
	});

	$.ajax({
		url: '{{ url("farmer/complaints/view") }}/' + id,
		type: 'GET',
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		success: function(res) {
			Swal.close();
			if (res.success) {
				const c = res.complaint;
				if (c.status !== 'new') {
					Swal.fire({
						@if(file_exists(public_path('assets/icons/Gif/alert1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
						title: 'Cannot Edit',
						text: 'Only new complaints can be edited',
						confirmButtonColor: '#10B981'
					});
					return;
				}

				const types = [
					{value: 'payment_delay', text: 'Payment Delay'},
					{value: 'payment_missing', text: 'Missing Payment'},
					{value: 'wrong_data_entry', text: 'Wrong Data Entry'},
					{value: 'other', text: 'Other Issue'}
				];

				let options = '';
				types.forEach(t => {
					options += `<option value="${t.value}" ${t.value === c.complaint_type ? 'selected' : ''}>${t.text}</option>`;
				});

				Swal.fire({
					title: 'Edit Complaint',
					html: `
						<div class="edit-form">
							<div class="form-group">
								<label>Type</label>
								<select id="edit_type" class="form-control">${options}</select>
							</div>
							<div class="form-group">
								<label>Description</label>
								<textarea id="edit_desc" class="form-control" rows="4">${c.description}</textarea>
							</div>
						</div>
					`,
					showCancelButton: true,
					confirmButtonText: 'Update',
					cancelButtonText: 'Cancel',
					confirmButtonColor: '#10B981',
					preConfirm: () => {
						const type = document.getElementById('edit_type').value;
						const desc = document.getElementById('edit_desc').value;
						if (!type || !desc) {
							Swal.showValidationMessage('All fields required');
							return false;
						}
						if (desc.length < 10) {
							Swal.showValidationMessage('Description too short');
							return false;
						}
						return { complaint_type: type, description: desc };
					}
				}).then((r) => {
					if (r.isConfirmed) {
						updateComplaint(id, r.value);
					}
				});
			}
		}
	});
}

function updateComplaint(id, data) {
	Swal.fire({
		title: 'Updating...',
		text: 'Please wait',
		allowOutsideClick: false,
		showConfirmButton: false,
		didOpen: () => Swal.showLoading()
	});

	$.ajax({
		url: '{{ url("farmer/complaints/update") }}/' + id,
		type: 'PUT',
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		data: data,
		success: function(res) {
			if (res.success) {
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
					title: 'Updated',
					text: 'Complaint updated successfully',
					timer: 1500,
					showConfirmButton: false
				}).then(() => location.reload());
			} else {
				Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Error', text: res.message || 'Update failed' });
			}
		},
		error: () => {
			Swal.close();
			Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Error', text: 'Update failed' });
		}
	});
}

function deleteComplaint(id) {
	Swal.fire({
		title: 'Delete Complaint',
		text: 'This action cannot be undone',
		@if(file_exists(public_path('assets/icons/Gif/alert1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
		showCancelButton: true,
		confirmButtonColor: '#ef4444',
		cancelButtonColor: '#6b7280',
		confirmButtonText: 'Delete',
		showLoaderOnConfirm: true,
		preConfirm: () => {
			return $.ajax({
				url: '{{ url("farmer/complaints/delete") }}/' + id,
				type: 'DELETE',
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
			}).then(res => {
				if (!res.success) throw new Error(res.message || 'Delete failed');
				return res;
			}).catch(err => {
				Swal.showValidationMessage(err.responseJSON?.message || 'Delete failed');
			});
		}
	}).then((r) => {
		if (r.isConfirmed) {
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
				title: 'Deleted',
				text: 'Complaint deleted successfully',
				timer: 1500,
				showConfirmButton: false
			}).then(() => location.reload());
		}
	});
}

document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.stat-card').forEach((c, i) => {
		c.style.animation = `fadeInUp 0.4s ease ${i * 0.1}s both`;
	});
});
</script>
@endsection

@extends('admin.layouts.admin_master')

@section('title', 'Complaint Details')
@section('page-title', 'Complaint Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Admin/complaints-show.css') }}">
@endsection

@section('content')
<div class="complaint-detail-app">
	<div class="detail-header">
		<div class="header-left">
			<a href="{{ route('admin.complaints.index') }}" class="btn-back">
				<i class="fas fa-arrow-left"></i>
			</a>
			<div class="header-icon">
				<i class="fas fa-flag"></i>
			</div>
			<div class="header-text">
				<h2 class="header-title">Complaint #{{ str_pad($complaint->id, 6, '0', STR_PAD_LEFT) }}</h2>
				<p class="header-subtitle">View and manage complaint details</p>
			</div>
		</div>
		<div class="header-right">
			<span class="header-status status-{{ $complaint->status }}">
				<i class="fas fa-circle"></i>
				{{ str_replace('_', ' ', ucfirst($complaint->status)) }}
			</span>
		</div>
	</div>

	<div class="detail-grid">
		<div class="detail-card main-card">
			<div class="card-title">
				<i class="fas fa-info-circle"></i>
				<h3>Complaint Information</h3>
			</div>
			<div class="card-body">
				<div class="info-grid">
					<div class="info-item">
						<span class="info-label">ID</span>
						<span class="info-value">#{{ str_pad($complaint->id, 6, '0', STR_PAD_LEFT) }}</span>
					</div>
					<div class="info-item">
						<span class="info-label">Type</span>
						<span class="info-value">
							<i class="fas fa-tag"></i>
							{{ str_replace('_', ' ', ucfirst($complaint->complaint_type)) }}
						</span>
					</div>
					<div class="info-item">
						<span class="info-label">Created</span>
						<span class="info-value">
							<i class="fas fa-calendar"></i>
							{{ $complaint->created_at->format('M d, Y') }}
						</span>
					</div>
					<div class="info-item">
						<span class="info-label">Last Updated</span>
						<span class="info-value">
							<i class="fas fa-clock"></i>
							{{ $complaint->updated_at->format('M d, Y') }}
						</span>
					</div>
				</div>

				<div class="divider"></div>

				<div class="description-section">
					<h4 class="section-subtitle">
						<i class="fas fa-file-alt"></i>
						Description
					</h4>
					<p class="description-text">{{ $complaint->description }}</p>
				</div>

				@if($complaint->reason)
				<div class="reason-section">
					<h4 class="section-subtitle">
						<i class="fas fa-exclamation-circle"></i>
						Reason
					</h4>
					<p class="reason-text">{{ $complaint->reason }}</p>
				</div>
				@endif
			</div>
		</div>

		<div class="detail-card users-card">
			<div class="card-title">
				<i class="fas fa-users"></i>
				<h3>Users Involved</h3>
			</div>
			<div class="card-body">
				<div class="user-item">
					<div class="user-avatar">
						{{ substr($complaint->complainant->username ?? 'U', 0, 1) }}
					</div>
					<div class="user-details">
						<span class="user-label">Complainant</span>
						<span class="user-name">{{ $complaint->complainant->username ?? 'Unknown User' }}</span>
						<span class="user-role">{{ $complaint->complainant_role }}</span>
					</div>
				</div>

				@if($complaint->against_user_id)
				<div class="user-item">
					<div class="user-avatar against">
						<i class="fas fa-user-slash"></i>
					</div>
					<div class="user-details">
						<span class="user-label">Against User</span>
						<span class="user-name">{{ $complaint->againstUser->username ?? 'Unknown User' }}</span>
					</div>
				</div>
				@endif

				@if($complaint->related_order_id)
				<div class="order-item">
					<div class="order-icon">
						<i class="fas fa-receipt"></i>
					</div>
					<div class="order-details">
						<span class="order-label">Related Order</span>
						<span class="order-id">#ORD-{{ str_pad($complaint->related_order_id, 6, '0', STR_PAD_LEFT) }}</span>
					</div>
				</div>
				@endif
			</div>
		</div>

		<div class="detail-card status-card">
			<div class="card-title">
				<i class="fas fa-sliders-h"></i>
				<h3>Update Status</h3>
			</div>
			<div class="card-body">
				<div class="current-status">
					<span class="status-label">Current Status</span>
					<span class="status-badge status-{{ $complaint->status }}" id="currentStatusBadge">
						<i class="fas fa-circle"></i>
						{{ str_replace('_', ' ', ucfirst($complaint->status)) }}
					</span>
				</div>

				<div class="status-options">
					<button class="status-option-btn" data-status="new" {{ $complaint->status == 'new' ? 'disabled' : '' }}>
						<i class="fas fa-circle-plus"></i>
						<span>New</span>
					</button>
					<button class="status-option-btn" data-status="in_progress" {{ $complaint->status == 'in_progress' ? 'disabled' : '' }}>
						<i class="fas fa-spinner"></i>
						<span>In Progress</span>
					</button>
					<button class="status-option-btn" data-status="resolved" {{ $complaint->status == 'resolved' ? 'disabled' : '' }}>
						<i class="fas fa-check-circle"></i>
						<span>Resolved</span>
					</button>
					<button class="status-option-btn" data-status="rejected" {{ $complaint->status == 'rejected' ? 'disabled' : '' }}>
						<i class="fas fa-times-circle"></i>
						<span>Rejected</span>
					</button>
				</div>

				@if($complaint->resolved_by_facilitator_id)
				<div class="resolution-info">
					<div class="divider"></div>
					<h4 class="section-subtitle">
						<i class="fas fa-check-double"></i>
						Resolution Details
					</h4>
					<div class="info-item">
						<span class="info-label">Resolved By</span>
						<span class="info-value">{{ $complaint->resolvedBy->username ?? 'Facilitator' }}</span>
					</div>
					<div class="info-item">
						<span class="info-label">Resolved At</span>
						<span class="info-value">{{ $complaint->resolved_at ? $complaint->resolved_at->format('M d, Y H:i') : 'N/A' }}</span>
					</div>
				</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const complaintId = {{ $complaint->id }};

document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.status-option-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			if (this.disabled) return;
			const newStatus = this.dataset.status;
			updateStatus(newStatus);
		});
	});
});

function updateStatus(newStatus) {
	const statusText = newStatus.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

	Swal.fire({
		title: 'Update Status',
		html: `
			<div style="text-align: left; padding: 10px;">
				<p>Are you sure you want to change the status to <strong>${statusText}</strong>?</p>
				<p style="color: var(--muted); font-size: 0.85rem; margin-top: 8px;">This action can be changed later.</p>
			</div>
		`,
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#10B981',
		cancelButtonColor: '#6b7280',
		confirmButtonText: 'Yes, update',
		cancelButtonText: 'Cancel'
	}).then((result) => {
		if (result.isConfirmed) {
			performStatusUpdate(newStatus);
		}
	});
}

function performStatusUpdate(newStatus) {
	Swal.fire({
		title: 'Updating Status',
		text: 'Please wait...',
		allowOutsideClick: false,
		didOpen: () => {
			Swal.showLoading();
		}
	});

	fetch(`/admin/complaints/${complaintId}/update-status`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': csrfToken,
			'Accept': 'application/json'
		},
		body: JSON.stringify({ status: newStatus })
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Network response was not ok');
		}
		return response.json();
	})
	.then(data => {
		Swal.close();
		if (data.success) {
			updateStatusUI(newStatus);
			showSuccess(data.message || 'Status updated successfully');
		} else {
			showError(data.message || 'Failed to update status');
		}
	})
	.catch(error => {
		Swal.close();
		console.error('Error:', error);
		showError('An error occurred while updating status');
	});
}

function updateStatusUI(newStatus) {
	const badge = document.getElementById('currentStatusBadge');
	const headerStatus = document.querySelector('.header-status');
	const statusText = newStatus.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

	// Update badge
	if (badge) {
		badge.className = `status-badge status-${newStatus}`;
		badge.innerHTML = `<i class="fas fa-circle"></i> ${statusText}`;
	}

	// Update header status
	if (headerStatus) {
		headerStatus.className = `header-status status-${newStatus}`;
		headerStatus.innerHTML = `<i class="fas fa-circle"></i> ${statusText}`;
	}

	// Enable/disable buttons
	document.querySelectorAll('.status-option-btn').forEach(btn => {
		btn.disabled = btn.dataset.status === newStatus;
		if (btn.dataset.status === newStatus) {
			btn.classList.add('active');
		} else {
			btn.classList.remove('active');
		}
	});

	// Show/hide resolution info if needed
	if (newStatus === 'resolved' || newStatus === 'rejected') {
		// Could reload to show resolution info if needed
		setTimeout(() => location.reload(), 1500);
	}
}

function showSuccess(message) {
	Swal.fire({
		icon: 'success',
		title: 'Success',
		text: message,
		confirmButtonColor: '#10B981',
		timer: 2000,
		showConfirmButton: false
	});
}

function showError(message) {
	Swal.fire({
		icon: 'error',
		title: 'Error',
		text: message,
		confirmButtonColor: '#10B981'
	});
}
</script>
@endsection
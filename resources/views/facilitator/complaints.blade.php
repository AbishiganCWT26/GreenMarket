@extends('facilitator.layouts.facilitator_master')

@section('title', 'Complaints Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/complaints.css') }}">

@endsection

@section('content')
<div class="complaints-dashboard">
	<div class="complaints-container">
		<div class="stats-row">
			<div class="stat-card new" onclick="filterByStatus('new')">
				<div class="stat-icon">
					<i class="fa-regular fa-file-lines"></i>
				</div>
				<div class="stat-info">
					<span class="stat-value">{{ $complaintStats['new'] }}</span>
					<span class="stat-label">New</span>
				</div>
			</div>
			<div class="stat-card progress1" onclick="filterByStatus('in_progress')">
				<div class="stat-icon">
					<i class="fa-regular fa-clock"></i>
				</div>
				<div class="stat-info">
					<span class="stat-value">{{ $complaintStats['in_progress'] }}</span>
					<span class="stat-label">In Progress</span>
				</div>
			</div>
			<div class="stat-card resolved" onclick="filterByStatus('resolved')">
				<div class="stat-icon">
					<i class="fa-regular fa-circle-check"></i>
				</div>
				<div class="stat-info">
					<span class="stat-value">{{ $complaintStats['resolved'] }}</span>
					<span class="stat-label">Resolved</span>
				</div>
			</div>
			<div class="stat-card rejected" onclick="filterByStatus('rejected')">
				<div class="stat-icon">
					<i class="fa-regular fa-circle-xmark"></i>
				</div>
				<div class="stat-info">
					<span class="stat-value">{{ $complaintStats['rejected'] }}</span>
					<span class="stat-label">Rejected</span>
				</div>
			</div>
		</div>

		<div class="action-bar">
			<div class="search-container">
				<input type="text" id="searchInput" placeholder="Search by ID, subject, complainant, against..." autocomplete="off">
				<button type="button" id="searchBtn">
					<i class="fa-solid fa-magnifying-glass"></i>
				</button>
			</div>
			<div class="view-toggle">
				<button class="view-btn active" id="cardViewBtn" onclick="setViewMode('card')">
					<i class="fa-solid fa-grip"></i>
				</button>
				<button class="view-btn" id="tableViewBtn" onclick="setViewMode('table')">
					<i class="fa-solid fa-table"></i>
				</button>
			</div>
			<div class="filter-group">
				<select id="statusFilter" class="filter-select" onchange="applyFilters()">
					<option value="">All Status</option>
					<option value="new">New</option>
					<option value="in_progress">In Progress</option>
					<option value="resolved">Resolved</option>
					<option value="rejected">Rejected</option>
				</select>
			</div>
			<button class="btn-refresh" onclick="refreshComplaints()">
				<i class="fa-solid fa-rotate"></i>
			</button>
		</div>

		<div id="complaintsContainer">
			<div class="complaints-grid" id="complaintsGrid"></div>
			<div class="table-view" id="tableView" style="display: none;"></div>
		</div>

		<div class="pagination-wrapper" id="paginationWrapper"></div>
	</div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">


<script>
let currentView = localStorage.getItem('complaintViewMode') || 'card';
let complaintsData = @json($complaints);
let currentPage = 1;
let itemsPerPage = 12;
let filteredComplaints = [];
let searchTerm = '';
let currentStatusFilter = '';

document.addEventListener('DOMContentLoaded', function() {
	setViewMode(currentView);
	setupSearch();
});

function setupSearch() {
	const searchInput = document.getElementById('searchInput');
	const searchBtn = document.getElementById('searchBtn');

	const performSearch = () => {
		searchTerm = searchInput.value.toLowerCase().trim();
		filterComplaints();
	};

	searchInput.addEventListener('keyup', function(e) {
		if (e.key === 'Enter') {
			performSearch();
		}
	});

	searchBtn.addEventListener('click', performSearch);
}

function filterComplaints() {
	let complaints = complaintsData;
	const statusFilter = document.getElementById('statusFilter').value;

	if (statusFilter) {
		complaints = complaints.filter(c => c.status === statusFilter);
	}

	if (searchTerm) {
		complaints = complaints.filter(complaint => {
			const searchableFields = [
				complaint.id.toString(),
				complaint.complaint_type,
				complaint.description,
				getDisplayName(complaint.complainant, complaint.complainant_role),
				getDisplayName(complaint.against_user, complaint.against_user_role || (complaint.against_user ? complaint.against_user.role : ''))
			].map(field => (field || '').toLowerCase());

			return searchableFields.some(field => field.includes(searchTerm));
		});
	}

	filteredComplaints = complaints;
	currentPage = 1;
	renderCurrentView();
}

function filterByStatus(status) {
	document.getElementById('statusFilter').value = status;
	applyFilters();
}

function applyFilters() {
	filterComplaints();
}

function setViewMode(mode) {
	currentView = mode;
	localStorage.setItem('complaintViewMode', mode);

	document.getElementById('cardViewBtn').classList.toggle('active', mode === 'card');
	document.getElementById('tableViewBtn').classList.toggle('active', mode === 'table');

	calculateItemsPerPage();
	filterComplaints();
}

function calculateItemsPerPage() {
	const width = window.innerWidth;

	if (currentView === 'card') {
		if (width >= 2560) itemsPerPage = 18;
		else if (width >= 1500) itemsPerPage = 12;
		else if (width >= 1200) itemsPerPage = 8;
		else if (width >= 992) itemsPerPage = 6;
		else if (width >= 768) itemsPerPage = 4;
		else itemsPerPage = 3;
	} else {
		if (width >= 2560) itemsPerPage = 15;
		else if (width >= 1500) itemsPerPage = 15;
		else if (width >= 1200) itemsPerPage = 10;
		else if (width >= 992) itemsPerPage = 10;
		else if (width >= 768) itemsPerPage = 10;
		else itemsPerPage = 5;
	}
}

function renderCurrentView() {
	if (currentView === 'card') {
		renderCardView();
	} else {
		renderTableView();
	}
}

function renderCardView() {
	const start = (currentPage - 1) * itemsPerPage;
	const end = start + itemsPerPage;
	const pageComplaints = filteredComplaints.slice(start, end);

	let html = '';

	if (pageComplaints.length === 0) {
		html = `
			<div class="empty-state">
				<i class="fa-regular fa-face-smile"></i>
				<h3>No Complaints Found</h3>
				<p>Try adjusting your search or filters</p>
			</div>
		`;
	} else {
		pageComplaints.forEach(complaint => {
			const statusClass = getStatusClass(complaint.status);
			const statusIcon = getStatusIcon(complaint.status);
			const statusLabel = getStatusLabel(complaint.status);
			const complainantName = getDisplayName(complaint.complainant, complaint.complainant_role);
			const againstName = getDisplayName(complaint.against_user, complaint.against_user_role || (complaint.against_user ? complaint.against_user.role : ''));

			html += `
				<div class="complaint-card" data-complaint-id="${complaint.id}">
					<div class="card-header">
						<span class="complaint-id">#${complaint.id}</span>
						<span class="status-badge ${statusClass}">
							<i class="fa-solid ${statusIcon}"></i>
							${statusLabel}
						</span>
					</div>
					<div class="card-body">
						<h3 class="complaint-subject">${escapeHtml(complaint.complaint_type.replace(/_/g, ' ').toUpperCase())}</h3>
						<div class="user-info">
							<div class="user-row">
								<i class="fa-regular fa-user"></i>
								<span><strong>Complainant:</strong> ${escapeHtml(complainantName)}</span>
							</div>
							<div class="user-row">
								<i class="fa-regular fa-user"></i>
								<span><strong>Against:</strong> ${escapeHtml(againstName)}</span>
							</div>
						</div>
						<div class="description-preview">
							${escapeHtml(complaint.description).substring(0, 120)}${complaint.description.length > 120 ? '...' : ''}
						</div>
						<div class="meta-info">
							<div class="meta-item">
								<i class="fa-regular fa-calendar"></i>
								<span>${formatDate(complaint.created_at)}</span>
							</div>
							<div class="meta-item">
								<i class="fa-regular fa-clock"></i>
								<span>${timeAgo(complaint.created_at)}</span>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<button class="btn-icon view" onclick="viewComplaint(${complaint.id})" title="View Details">
							<i class="fa-regular fa-eye"></i>
						</button>
						${complaint.status === 'new' || complaint.status === 'in_progress' ? `
							<button class="btn-icon edit" onclick="updateComplaintStatus(${complaint.id})" title="Update Status">
								<i class="fa-regular fa-pen-to-square"></i>
							</button>
						` : ''}
					</div>
				</div>
			`;
		});
	}

	document.getElementById('complaintsGrid').innerHTML = html;
	document.getElementById('complaintsGrid').style.display = 'grid';
	document.getElementById('tableView').style.display = 'none';
	renderPagination();
}

function renderTableView() {
	const start = (currentPage - 1) * itemsPerPage;
	const end = start + itemsPerPage;
	const pageComplaints = filteredComplaints.slice(start, end);

	let html = '';

	if (pageComplaints.length === 0) {
		html = `
			<div class="empty-state">
				<i class="fa-regular fa-face-smile"></i>
				<h3>No Complaints Found</h3>
				<p>Try adjusting your search or filters</p>
			</div>
		`;
	} else {
		html = '<table class="complaints-table"><thead><tr><th>ID</th><th>Subject</th><th>Complainant</th><th>Against</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody>';

		pageComplaints.forEach(complaint => {
			const statusClass = getStatusClass(complaint.status);
			const statusIcon = getStatusIcon(complaint.status);
			const statusLabel = getStatusLabel(complaint.status);
			const complainantName = getDisplayName(complaint.complainant, complaint.complainant_role);
			const againstName = getDisplayName(complaint.against_user, complaint.against_user_role || (complaint.against_user ? complaint.against_user.role : ''));

			html += `
				<tr class="complaint-row" data-complaint-id="${complaint.id}">
					<td><span class="complaint-id-table">#${complaint.id}</span></td>
					<td class="subject-cell">${escapeHtml(complaint.complaint_type.replace(/_/g, ' ').toUpperCase())}</td>
					<td>${escapeHtml(complainantName)}</td>
					<td>${escapeHtml(againstName)}</td>
					<td>
						<span class="status-tag ${statusClass}">
							<i class="fa-solid ${statusIcon}"></i>
							${statusLabel}
						</span>
					</td>
					<td>
						<div class="table-date">${formatDate(complaint.created_at)}</div>
						<small>${timeAgo(complaint.created_at)}</small>
					</td>
					<td>
						<div class="table-actions">
							<button class="table-btn view" onclick="viewComplaint(${complaint.id})" title="View Details">
								<i class="fa-regular fa-eye"></i>
							</button>
							${complaint.status === 'new' || complaint.status === 'in_progress' ? `
								<button class="table-btn edit" onclick="updateComplaintStatus(${complaint.id})" title="Update Status">
									<i class="fa-regular fa-pen-to-square"></i>
								</button>
							` : ''}
						</div>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table>';
	}

	document.getElementById('tableView').innerHTML = html;
	document.getElementById('tableView').style.display = 'block';
	document.getElementById('complaintsGrid').style.display = 'none';
	renderPagination();
}

function getStatusClass(status) {
	const classes = {
		'new': 'new',
		'in_progress': 'progress',
		'resolved': 'resolved',
		'rejected': 'rejected'
	};
	return classes[status] || 'new';
}

function getStatusIcon(status) {
	const icons = {
		'new': 'fa-circle-plus',
		'in_progress': 'fa-spinner',
		'resolved': 'fa-circle-check',
		'rejected': 'fa-circle-xmark'
	};
	return icons[status] || 'fa-circle-question';
}

function getStatusLabel(status) {
	const labels = {
		'new': 'New',
		'in_progress': 'In Progress',
		'resolved': 'Resolved',
		'rejected': 'Rejected'
	};
	return labels[status] || status;
}

function formatDate(date) {
	return new Date(date).toLocaleDateString('en-US', { 
		day: 'numeric', 
		month: 'short', 
		year: 'numeric' 
	});
}

function formatDateTime(date) {
	return new Date(date).toLocaleDateString('en-US', { 
		day: 'numeric', 
		month: 'short', 
		year: 'numeric',
		hour: '2-digit',
		minute: '2-digit'
	});
}

function timeAgo(date) {
	const seconds = Math.floor((new Date() - new Date(date)) / 1000);
	const intervals = {
		year: 31536000,
		month: 2592000,
		week: 604800,
		day: 86400,
		hour: 3600,
		minute: 60
	};

	for (const [unit, secondsInUnit] of Object.entries(intervals)) {
		const interval = Math.floor(seconds / secondsInUnit);
		if (interval >= 1) {
			return interval + ' ' + unit + (interval === 1 ? '' : 's') + ' ago';
		}
	}
	return 'just now';
}

function escapeHtml(text) {
	if (text === null || text === undefined) return '';
	const div = document.createElement('div');
	div.textContent = text;
	return div.innerHTML;
}

function getDisplayName(user, role) {
	if (!user) return 'Unknown User';
	
	// If the user object directly has a name (unlikely based on current relations)
	if (user.name) return user.name;

	// Check relations based on role
	const targetRole = role || user.role;
	
	if (targetRole === 'farmer' && user.farmer) return user.farmer.name;
	if (targetRole === 'buyer' && user.buyer) return user.buyer.name;
	if (targetRole === 'lead_farmer' && user.lead_farmer) return user.lead_farmer.name;
	if (targetRole === 'facilitator' && user.facilitator) return user.facilitator.name;
	
    // Fallback to username if no "real name" found
	return user.username || 'Unknown User';
}

function renderPagination() {
	const totalPages = Math.ceil(filteredComplaints.length / itemsPerPage);
	let html = '';

	if (totalPages > 1) {
		html = '<div class="pagination"><ul>';

		html += `<li class="${currentPage === 1 ? 'disabled' : ''}">
			<a href="#" onclick="changePage(${currentPage - 1}); return false;">
				<i class="fa-solid fa-chevron-left"></i>
			</a>
		</li>`;

		let startPage = Math.max(1, currentPage - 2);
		let endPage = Math.min(totalPages, startPage + 4);

		if (endPage - startPage < 4) {
			startPage = Math.max(1, endPage - 4);
		}

		for (let i = startPage; i <= endPage; i++) {
			html += `<li class="${currentPage === i ? 'active' : ''}">
				<a href="#" onclick="changePage(${i}); return false;">${i}</a>
			</li>`;
		}

		html += `<li class="${currentPage === totalPages ? 'disabled' : ''}">
			<a href="#" onclick="changePage(${currentPage + 1}); return false;">
				<i class="fa-solid fa-chevron-right"></i>
			</a>
		</li>`;

		html += '</ul></div>';
	}

	document.getElementById('paginationWrapper').innerHTML = html;
}

function changePage(page) {
	if (page < 1 || page > Math.ceil(filteredComplaints.length / itemsPerPage)) return;
	currentPage = page;
	renderCurrentView();
}

window.addEventListener('resize', function() {
	calculateItemsPerPage();
	if (filteredComplaints.length > 0) {
		renderCurrentView();
	}
});

function refreshComplaints() {
	Swal.fire({
		title: 'Refreshing...',
		html: '<div class="spinner"></div>',
		showConfirmButton: false,
		allowOutsideClick: false
	});

	setTimeout(() => {
		window.location.reload();
	}, 800);
}

function viewComplaint(complaintId) {
	const complaint = complaintsData.find(c => c.id === complaintId);

	if (!complaint) {
		Swal.fire({ title: 'Error', html: 'Complaint not found', @if(file_exists(public_path('assets/icons/Gif/error3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif });
		return;
	}

	const statusClass = getStatusClass(complaint.status);
	const statusIcon = getStatusIcon(complaint.status);
	const statusLabel = getStatusLabel(complaint.status);
	const complainantName = getDisplayName(complaint.complainant, complaint.complainant_role);
	const againstName = getDisplayName(complaint.against_user, complaint.against_user_role || (complaint.against_user ? complaint.against_user.role : ''));

	Swal.fire({
		title: 'Complaint Details',
		width: '700px',
		html: `
			<div class="complaint-detail-view">
				<div class="detail-header">
					<span class="complaint-id-large">#${complaint.id}</span>
					<span class="status-badge ${statusClass}">
						<i class="fa-solid ${statusIcon}"></i>
						${statusLabel}
					</span>
				</div>

				<div class="detail-section">
					<h4><i class="fa-regular fa-rectangle-list"></i> Type</h4>
					<p>${escapeHtml(complaint.complaint_type.replace(/_/g, ' ').toUpperCase())}</p>
				</div>

				<div class="detail-section">
					<h4><i class="fa-regular fa-message"></i> Description</h4>
					<div class="description-full">${escapeHtml(complaint.description)}</div>
				</div>

				<div class="detail-row">
					<div class="detail-card">
						<i class="fa-regular fa-user"></i>
						<strong>Complainant</strong>
						<span>${escapeHtml(complainantName)}</span>
						${complaint.complainant?.email ? `<small>${complaint.complainant.email}</small>` : ''}
					</div>
					<div class="detail-card">
						<i class="fa-regular fa-user"></i>
						<strong>Against</strong>
						<span>${escapeHtml(againstName)}</span>
						${complaint.against_user?.email ? `<small>${complaint.against_user.email}</small>` : ''}
					</div>
				</div>

				<div class="detail-footer">
					<div class="footer-item">
						<i class="fa-regular fa-calendar"></i>
						<span>Created: ${formatDateTime(complaint.created_at)}</span>
					</div>
					${complaint.updated_at !== complaint.created_at ? `
						<div class="footer-item">
							<i class="fa-regular fa-pen-to-square"></i>
							<span>Updated: ${formatDateTime(complaint.updated_at)}</span>
						</div>
					` : ''}
					${complaint.resolved_at ? `
						<div class="footer-item">
							<i class="fa-regular fa-circle-check"></i>
							<span>Resolved: ${formatDateTime(complaint.resolved_at)}</span>
						</div>
					` : ''}
				</div>
			</div>
		`,
		confirmButtonText: 'Close',
		confirmButtonColor: '#10B981'
	});
}

function updateComplaintStatus(complaintId) {
	const complaint = complaintsData.find(c => c.id === complaintId);

	if (!complaint) {
		Swal.fire({ title: 'Error', html: 'Complaint not found', @if(file_exists(public_path('assets/icons/Gif/error3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif });
		return;
	}

	Swal.fire({
		title: 'Update Complaint Status',
		html: `
			<div class="status-update-form">
				<div class="form-group">
					<label>Current Status: <strong>${getStatusLabel(complaint.status)}</strong></label>
				</div>
				<div class="form-group">
					<label>New Status</label>
					<select id="newStatus" class="form-control">
						<option value="new" ${complaint.status === 'new' ? 'selected' : ''}>New</option>
						<option value="in_progress" ${complaint.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
						<option value="resolved" ${complaint.status === 'resolved' ? 'selected' : ''}>Resolved</option>
						<option value="rejected" ${complaint.status === 'rejected' ? 'selected' : ''}>Rejected</option>
					</select>
				</div>
				<div class="form-group" id="resolutionNoteGroup" style="display: none;">
					<label>Resolution Note</label>
					<textarea id="resolutionNote" class="form-control" rows="3" placeholder="Add resolution details..."></textarea>
				</div>
			</div>
		`,
		showCancelButton: true,
		confirmButtonText: 'Update',
		confirmButtonColor: '#10B981',
		cancelButtonText: 'Cancel',
		didOpen: () => {
			const statusSelect = document.getElementById('newStatus');
			const resolutionGroup = document.getElementById('resolutionNoteGroup');

			statusSelect.addEventListener('change', function() {
				if (this.value === 'resolved' || this.value === 'rejected') {
					resolutionGroup.style.display = 'block';
				} else {
					resolutionGroup.style.display = 'none';
				}
			});

			if (statusSelect.value === 'resolved' || statusSelect.value === 'rejected') {
				resolutionGroup.style.display = 'block';
			}
		},
		preConfirm: () => {
			const newStatus = document.getElementById('newStatus').value;
			const resolutionNote = document.getElementById('resolutionNote')?.value;

			if ((newStatus === 'resolved' || newStatus === 'rejected') && !resolutionNote) {
				Swal.showValidationMessage('Please provide a resolution note');
				return false;
			}

			return { status: newStatus, resolution_note: resolutionNote };
		}
	}).then(result => {
		if (result.isConfirmed) {
			submitStatusUpdate(complaintId, result.value);
		}
	});
}

function submitStatusUpdate(complaintId, data) {
	Swal.fire({
		title: 'Updating...',
		html: '<div class="spinner"></div>',
		showConfirmButton: false,
		allowOutsideClick: false
	});

	fetch(`/facilitator/complaints/${complaintId}/update-status`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		},
		body: JSON.stringify(data)
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			Swal.fire({
				title: 'Success!',
				text: 'Complaint status updated successfully',
				@if(file_exists(public_path('assets/icons/Gif/success6.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success6.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
				confirmButtonColor: '#10B981'
			}).then(() => {
				window.location.reload();
			});
		} else {
			Swal.fire({ title: 'Error', html: data.message || 'Update failed', @if(file_exists(public_path('assets/icons/Gif/Failed3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Failed3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif });
		}
	})
	.catch(() => {
		Swal.fire({ title: 'Error', html: 'Update failed', @if(file_exists(public_path('assets/icons/Gif/Failed3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Failed3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif });
	});
}
</script>
@endsection

@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Manage Farmers')

@section('page-title', 'Manage Farmers')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/manage_farmers.css') }}">
@endsection

@section('content')
<div class="manage-farmers-container">
	<div class="header-section">
		<div class="header-left">
			<h1>
				<i class="fas fa-users"></i>
				Manage Farmers
			</h1>
			<p class="subtitle">
				<span class="badge-count">{{ $farmers->count() }}</span> farmers
			</p>
		</div>
		<div class="header-right">
			<a href="{{ route('lf.registerFarmer') }}" class="btn-add">
				<i class="fas fa-user-plus"></i> Add New
			</a>
		</div>
	</div>

	<div class="filters-section">
		<div class="search-container">
			<div class="search-box">
				<i class="fas fa-search"></i>
				<input type="text" id="searchInput" placeholder="Search farmers...">
				<button class="btn-clear-search" id="clearSearch">
					<i class="fas fa-times"></i>
				</button>
			</div>
		</div>

		<div class="filter-controls">
			<div class="filter-group">
				<label><i class="fas fa-map-marker-alt"></i> District</label>
				<select id="districtFilter">
					<option value="">All</option>
					@foreach($districts as $district)
					<option value="{{ $district }}">{{ $district }}</option>
					@endforeach
				</select>
			</div>

			<div class="filter-group">
				<label><i class="fas fa-circle"></i> Status</label>
				<select id="statusFilter">
					<option value="">All</option>
					<option value="active">Active</option>
					<option value="inactive">Inactive</option>
				</select>
			</div>

			<div class="filter-group">
				<label><i class="fas fa-credit-card"></i> Payment</label>
				<select id="paymentFilter">
					<option value="">All</option>
					<option value="bank">Bank</option>
					<option value="ezcash">EzCash</option>
					<option value="mcash">mCash</option>
					<option value="all">All Methods</option>
				</select>
			</div>
		</div>
	</div>

	<div class="view-controls">
		<div class="view-toggle">
			<button class="view-btn active" data-view="card">
				<i class="fas fa-th-large"></i> Cards
			</button>
			<button class="view-btn" data-view="table">
				<i class="fas fa-table"></i> Table
			</button>
		</div>
		<div class="per-page-selector">
			<label>Show:</label>
			<select id="perPageSelect">
				<option value="15">15</option>
				<option value="30">30</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select>
		</div>
	</div>

	<div class="farmers-container">
		<div class="farmers-grid" id="cardView">
			@foreach($farmers as $farmer)
			<div class="farmer-card" data-id="{{ $farmer->id }}" 
				 data-name="{{ strtolower($farmer->name) }}" 
				 data-nic="{{ strtolower($farmer->nic_no) }}" 
				 data-phone="{{ $farmer->primary_mobile }}"
				 data-district="{{ $farmer->district }}"
				 data-status="{{ $farmer->is_active ? 'active' : 'inactive' }}"
				 data-payment="{{ $farmer->preferred_payment }}">
				<div class="card-header">
					<div class="profile-section">
						<div class="profile-img">
							<img src="{{ asset('uploads/profile_pictures/' . ($farmer->user->profile_photo ?? 'default-avatar.png')) }}" 
								 alt="{{ $farmer->name }}"
								 onerror="this.src='{{ asset('assets/images/default-avatar.png') }}'">
						</div>
						<div class="profile-info">
							<h3>{{ $farmer->name }}</h3>
							<p><i class="fas fa-at"></i> {{ $farmer->user->username ?? 'N/A' }}</p>
						</div>
					</div>
					<div class="status-badge {{ $farmer->is_active ? 'active' : 'inactive' }}">
						<i class="fas fa-circle"></i>
						{{ $farmer->is_active ? 'Active' : 'Inactive' }}
					</div>
				</div>

				<div class="card-body">
					<div class="info-row">
						<i class="fas fa-phone"></i>
						<span>{{ $farmer->primary_mobile }}</span>
					</div>
					<div class="info-row">
						<i class="fas fa-credit-card"></i>
						<span>
							@if($farmer->preferred_payment == 'bank')
								Bank Transfer
							@elseif($farmer->preferred_payment == 'ezcash')
								EzCash
							@elseif($farmer->preferred_payment == 'mcash')
								mCash
							@elseif($farmer->preferred_payment == 'all')
								All Methods
							@endif
						</span>
					</div>
					<div class="info-row">
						<i class="fas fa-box"></i>
						<span>{{ $farmer->products->count() }} Products</span>
					</div>
					<div class="info-row">
						<i class="fas fa-map-marker-alt"></i>
						<span>{{ $farmer->district }}</span>
					</div>
				</div>

				<div class="card-footer">
					<button class="btn-action btn-view" onclick="viewFarmer({{ $farmer->id }})">
						<i class="fas fa-eye"></i> View
					</button>
					<a href="{{ route('lf.editFarmer', $farmer->id) }}" class="btn-action btn-edit">
						<i class="fas fa-edit"></i> Edit
					</a>
					<button class="btn-action btn-delete" onclick="deleteFarmer({{ $farmer->id }}, '{{ $farmer->name }}')">
						<i class="fas fa-trash"></i> Delete
					</button>
				</div>
			</div>
			@endforeach
		</div>

		<div class="farmers-table-container" id="tableView" style="display: none;">
			<div class="table-responsive">
				<table class="farmers-table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Username</th>
							<th>Phone</th>
							<th>Products</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($farmers as $farmer)
						<tr class="farmer-row" data-id="{{ $farmer->id }}" 
							data-name="{{ strtolower($farmer->name) }}" 
							data-nic="{{ strtolower($farmer->nic_no) }}" 
							data-phone="{{ $farmer->primary_mobile }}"
							data-district="{{ $farmer->district }}"
							data-status="{{ $farmer->is_active ? 'active' : 'inactive' }}">
							<td>
								<div class="farmer-name-cell">
									<strong>{{ $farmer->name }}</strong>
									<small>{{ $farmer->district }}</small>
								</div>
							</td>
							<td>{{ $farmer->user->username ?? 'N/A' }}</td>
							<td>{{ $farmer->primary_mobile }}</td>
							<td>
								<span class="product-count">{{ $farmer->products->count() }}</span>
							</td>
							<td>
								<span class="status-indicator {{ $farmer->is_active ? 'active' : 'inactive' }}">
									<i class="fas fa-circle"></i>
									{{ $farmer->is_active ? 'Active' : 'Inactive' }}
								</span>
							</td>
							<td>
								<div class="table-actions">
									<button class="btn-icon btn-view" onclick="viewFarmer({{ $farmer->id }})" title="View">
										<i class="fas fa-eye"></i>
									</button>
									<a href="{{ route('lf.editFarmer', $farmer->id) }}" class="btn-icon btn-edit" title="Edit">
										<i class="fas fa-edit"></i>
									</a>
									<button class="btn-icon btn-delete" onclick="deleteFarmer({{ $farmer->id }}, '{{ $farmer->name }}')" title="Delete">
										<i class="fas fa-trash"></i>
									</button>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="pagination-container">
		<div class="pagination-info" id="paginationInfo">
			Showing {{ $farmers->count() }} of {{ $farmers->count() }} farmers
		</div>
		<div class="pagination-controls">
			<button class="pagination-btn" id="prevPage" disabled>
				<i class="fas fa-chevron-left"></i> Prev
			</button>
			<div class="pagination-numbers" id="paginationNumbers"></div>
			<button class="pagination-btn" id="nextPage" disabled>
				Next <i class="fas fa-chevron-right"></i>
			</button>
		</div>
	</div>
</div>

<div id="farmerModal" class="farmer-modal">
	<div class="modal-content">
		<div class="modal-header">
			<h3><i class="fas fa-user"></i> Farmer Details</h3>
			<button class="modal-close" onclick="closeModal()">
				<i class="fas fa-times"></i>
			</button>
		</div>
		<div class="modal-body" id="farmerDetailsContent">
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentView = 'card';
let currentPage = 1;
let itemsPerPage = 15;
let allFarmers = [];
let filteredFarmers = [];

document.addEventListener('DOMContentLoaded', function() {
	initializeFarmersData();
	setupEventListeners();
	setupPagination();
	updateDisplay();
});

function initializeFarmersData() {
	const cardElements = document.querySelectorAll('.farmer-card');
	const tableRows = document.querySelectorAll('.farmer-row');
	
	allFarmers = [];
	cardElements.forEach(card => {
		allFarmers.push({
			element: card,
			name: card.dataset.name,
			nic: card.dataset.nic,
			phone: card.dataset.phone,
			district: card.dataset.district,
			status: card.dataset.status,
			payment: card.dataset.payment
		});
	});
	
	tableRows.forEach(row => {
		allFarmers.push({
			element: row,
			name: row.dataset.name,
			nic: row.dataset.nic,
			phone: row.dataset.phone,
			district: row.dataset.district,
			status: row.dataset.status,
			payment: row.dataset.payment
		});
	});
	
	filteredFarmers = [...allFarmers];
}

function setupEventListeners() {
	document.getElementById('searchInput').addEventListener('input', handleSearch);
	document.getElementById('clearSearch').addEventListener('click', clearSearch);
	document.getElementById('districtFilter').addEventListener('change', applyFilters);
	document.getElementById('statusFilter').addEventListener('change', applyFilters);
	document.getElementById('paymentFilter').addEventListener('change', applyFilters);
	document.getElementById('perPageSelect').addEventListener('change', handlePerPageChange);
	
	document.querySelectorAll('.view-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			switchView(this.dataset.view);
		});
	});
	
	document.getElementById('prevPage').addEventListener('click', goToPreviousPage);
	document.getElementById('nextPage').addEventListener('click', goToNextPage);
}

function handleSearch() {
	const searchTerm = document.getElementById('searchInput').value.toLowerCase();
	applyFilters();
	
	if (searchTerm) {
		filteredFarmers = filteredFarmers.filter(farmer => 
			farmer.name.includes(searchTerm) ||
			farmer.nic.includes(searchTerm) ||
			farmer.phone.includes(searchTerm)
		);
	}
	
	currentPage = 1;
	updateDisplay();
}

function clearSearch() {
	document.getElementById('searchInput').value = '';
	applyFilters();
	currentPage = 1;
	updateDisplay();
}

function applyFilters() {
	const district = document.getElementById('districtFilter').value.toLowerCase();
	const status = document.getElementById('statusFilter').value;
	const payment = document.getElementById('paymentFilter').value;
	
	filteredFarmers = allFarmers.filter(farmer => {
		let match = true;
		
		if (district && farmer.district.toLowerCase() !== district) {
			match = false;
		}
		
		if (status && farmer.status !== status) {
			match = false;
		}
		
		if (payment && farmer.payment !== payment) {
			match = false;
		}
		
		return match;
	});
	
	currentPage = 1;
	updateDisplay();
}

function switchView(view) {
	currentView = view;
	document.querySelectorAll('.view-btn').forEach(btn => {
		btn.classList.remove('active');
	});
	document.querySelector(`.view-btn[data-view="${view}"]`).classList.add('active');
	
	document.getElementById('cardView').style.display = view === 'card' ? 'grid' : 'none';
	document.getElementById('tableView').style.display = view === 'table' ? 'block' : 'none';
	
	updatePagination();
}

function handlePerPageChange(e) {
	itemsPerPage = parseInt(e.target.value);
	currentPage = 1;
	updateDisplay();
}

function setupPagination() {
	updatePagination();
}

function updatePagination() {
	const totalPages = Math.ceil(filteredFarmers.length / itemsPerPage);
	const paginationNumbers = document.getElementById('paginationNumbers');
	const prevBtn = document.getElementById('prevPage');
	const nextBtn = document.getElementById('nextPage');
	
	paginationNumbers.innerHTML = '';
	
	for (let i = 1; i <= totalPages; i++) {
		const btn = document.createElement('button');
		btn.className = `pagination-number ${i === currentPage ? 'active' : ''}`;
		btn.textContent = i;
		btn.addEventListener('click', () => goToPage(i));
		paginationNumbers.appendChild(btn);
	}
	
	prevBtn.disabled = currentPage === 1;
	nextBtn.disabled = currentPage === totalPages || totalPages === 0;
	
	const start = (currentPage - 1) * itemsPerPage + 1;
	const end = Math.min(currentPage * itemsPerPage, filteredFarmers.length);
	document.getElementById('paginationInfo').textContent = 
		`Showing ${start}-${end} of ${filteredFarmers.length} farmers`;
}

function goToPage(page) {
	currentPage = page;
	updateDisplay();
}

function goToPreviousPage() {
	if (currentPage > 1) {
		currentPage--;
		updateDisplay();
	}
}

function goToNextPage() {
	const totalPages = Math.ceil(filteredFarmers.length / itemsPerPage);
	if (currentPage < totalPages) {
		currentPage++;
		updateDisplay();
	}
}

function updateDisplay() {
	const startIndex = (currentPage - 1) * itemsPerPage;
	const endIndex = startIndex + itemsPerPage;
	const currentFarmers = filteredFarmers.slice(startIndex, endIndex);
	
	if (currentView === 'card') {
		updateCardView(currentFarmers);
	} else {
		updateTableView(currentFarmers);
	}
	
	updatePagination();
}

function updateCardView(farmers) {
	const cardView = document.getElementById('cardView');
	const allCards = document.querySelectorAll('.farmer-card');
	
	allCards.forEach(card => {
		card.style.display = 'none';
	});
	
	farmers.forEach(farmer => {
		if (farmer.element.classList.contains('farmer-card')) {
			farmer.element.style.display = 'block';
		}
	});
	
	cardView.style.display = 'grid';
}

function updateTableView(farmers) {
	const tableView = document.getElementById('tableView');
	const allRows = document.querySelectorAll('.farmer-row');
	
	allRows.forEach(row => {
		row.style.display = 'none';
	});
	
	farmers.forEach(farmer => {
		if (farmer.element.classList.contains('farmer-row')) {
			farmer.element.style.display = 'table-row';
		}
	});
	
	tableView.style.display = 'block';
}

function viewFarmer(farmerId) {
	fetch(`/lead-farmer/farmer-details/${farmerId}`, {
		method: 'GET',
		headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}',
			'Content-Type': 'application/json',
			'Accept': 'application/json'
		}
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Network response was not ok');
		}
		return response.json();
	})
	.then(data => {
		if (data.success) {
			showFarmerDetails(data.farmer);
		} else {
			throw new Error(data.message || 'Failed to load farmer details');
		}
	})
	.catch(error => {
		console.error('Error:', error);
		Swal.fire({
			icon: 'error',
			title: 'Error',
			text: 'Failed to load farmer details. Please try again.',
			confirmButtonColor: '#10B981'
		});
	});
}

function showFarmerDetails(farmer) {
	let paymentDetailsHtml = '';
	
	if (farmer.preferred_payment === 'bank') {
		paymentDetailsHtml = `
			<div class="detail-section">
				<h4><i class="fas fa-university"></i> Bank Details</h4>
				<div class="detail-row">
					<span>Bank Name:</span>
					<span>${farmer.bank_name || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>Bank Branch:</span>
					<span>${farmer.bank_branch || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>Account Holder:</span>
					<span>${farmer.account_holder_name || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>Account Number:</span>
					<span>${farmer.account_number || 'N/A'}</span>
				</div>
			</div>
		`;
	} else if (farmer.preferred_payment === 'ezcash') {
		paymentDetailsHtml = `
			<div class="detail-section">
				<h4><i class="fas fa-mobile-alt"></i> EzCash Details</h4>
				<div class="detail-row">
					<span>Mobile Number:</span>
					<span>${farmer.ezcash_mobile || 'N/A'}</span>
				</div>
			</div>
		`;
	} else if (farmer.preferred_payment === 'mcash') {
		paymentDetailsHtml = `
			<div class="detail-section">
				<h4><i class="fas fa-mobile-alt"></i> mCash Details</h4>
				<div class="detail-row">
					<span>Mobile Number:</span>
					<span>${farmer.mcash_mobile || 'N/A'}</span>
				</div>
			</div>
		`;
	} else if (farmer.preferred_payment === 'all') {
		paymentDetailsHtml = `
			<div class="detail-section">
				<h4><i class="fas fa-wallet"></i> Payment Details</h4>
				<div class="detail-row">
					<span>Bank Name:</span>
					<span>${farmer.bank_name || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>Bank Branch:</span>
					<span>${farmer.bank_branch || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>Account Holder:</span>
					<span>${farmer.account_holder_name || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>Account Number:</span>
					<span>${farmer.account_number || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>EzCash Mobile:</span>
					<span>${farmer.ezcash_mobile || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>mCash Mobile:</span>
					<span>${farmer.mcash_mobile || 'N/A'}</span>
				</div>
			</div>
		`;
	}

	const html = `
		<div class="modal-profile">
			<div class="profile-image">
				<img src="${farmer.profile_photo_url}" alt="${farmer.name}">
			</div>
			<div class="profile-info">
				<h2>${farmer.name}</h2>
				<div class="profile-status ${farmer.is_active ? 'active' : 'inactive'}">
					<i class="fas fa-circle"></i>
					${farmer.is_active ? 'Active' : 'Inactive'}
				</div>
			</div>
		</div>
		
		<div class="modal-details">
			<div class="detail-section">
				<h4><i class="fas fa-id-card"></i> Personal Information</h4>
				<div class="detail-row">
					<span>NIC Number:</span>
					<span>${farmer.nic_no || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>Username:</span>
					<span>${farmer.username || 'N/A'}</span>
				</div>
			</div>
			
			<div class="detail-section">
				<h4><i class="fas fa-phone"></i> Contact Information</h4>
				<div class="detail-row">
					<span>Primary Mobile:</span>
					<span>${farmer.primary_mobile || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>WhatsApp:</span>
					<span>${farmer.whatsapp_number || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>Email:</span>
					<span>${farmer.email || 'N/A'}</span>
				</div>
			</div>
			
			<div class="detail-section">
				<h4><i class="fas fa-map-marker-alt"></i> Address Information</h4>
				<div class="detail-row">
					<span>Residential Address:</span>
					<span>${farmer.residential_address || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>District:</span>
					<span>${farmer.district || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>GN Division:</span>
					<span>${farmer.grama_niladhari_division || 'N/A'}</span>
				</div>
				<div class="detail-row">
					<span>Google Maps:</span>
					<span>
						<a href="${farmer.address_map_link || '#'}" target="_blank">
							${farmer.address_map_link || 'N/A'}
						</a>
					</span>
				</div>
			</div>
			
			${paymentDetailsHtml}
			
			<div class="detail-section">
				<h4><i class="fas fa-chart-bar"></i> Statistics</h4>
				<div class="detail-row">
					<span>Total Products:</span>
					<span>${farmer.products_count || 0}</span>
				</div>
				<div class="detail-row">
					<span>Active Products:</span>
					<span>${farmer.active_products_count || 0}</span>
				</div>
				<div class="detail-row">
					<span>Last Updated:</span>
					<span>${farmer.updated_at_formatted || 'N/A'}</span>
				</div>
			</div>
		</div>
	`;
	
	document.getElementById('farmerDetailsContent').innerHTML = html;
	document.getElementById('farmerModal').classList.add('show');
	document.body.style.overflow = 'hidden';
}

function closeModal() {
	document.getElementById('farmerModal').classList.remove('show');
	document.body.style.overflow = 'auto';
}

function deleteFarmer(farmerId, farmerName) {
	Swal.fire({
		title: 'Deactivate Farmer?',
		html: `
			<div style="text-align: left;">
				<p>Are you sure you want to deactivate <strong>${farmerName}</strong>?</p>
				<p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem;">
					<i class="fas fa-exclamation-triangle"></i>
					This will set farmer status to inactive
				</p>
			</div>
		`,
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Yes, deactivate',
		cancelButtonText: 'Cancel',
		confirmButtonColor: '#10B981',
		cancelButtonColor: '#6B7280',
		reverseButtons: true
	}).then((result) => {
		if (result.isConfirmed) {
			fetch(`/lead-farmer/farmers/${farmerId}`, {
				method: 'DELETE',
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}',
					'Content-Type': 'application/json',
					'Accept': 'application/json'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					Swal.fire({
						icon: 'success',
						title: 'Deactivated!',
						text: `${farmerName} has been deactivated successfully.`,
						confirmButtonColor: '#10B981',
						timer: 2000,
						showConfirmButton: true
					}).then(() => {
						location.reload();
					});
				} else {
					throw new Error(data.message || 'Failed to deactivate farmer');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Failed to deactivate farmer. Please try again.',
					confirmButtonColor: '#10B981'
				});
			});
		}
	});
}

window.onclick = function(event) {
	const modal = document.getElementById('farmerModal');
	if (event.target === modal) {
		closeModal();
	}
}
</script>
@endsection
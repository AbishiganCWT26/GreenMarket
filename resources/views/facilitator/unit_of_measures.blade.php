@extends('facilitator.layouts.facilitator_master')

@section('title', 'Unit of Measures')
@section('page-title', 'Standards & Measurements')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/unit_of_measures.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="unit-dashboard">
	<div class="unit-header">
		<div class="header-info">
			<div class="header-icon">
				<i class="fas fa-scale-balanced"></i>
			</div>
			<div class="header-text">
				<h2 class="header-title">Unit of Measures</h2>
				<p class="header-subtitle">Manage measurement standards</p>
			</div>
		</div>
		<div class="header-actions">
			<button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addUomModal">
				<i class="fas fa-plus"></i>
				<span>New Unit</span>
			</button>
		</div>
	</div>

	<div class="unit-stats">
		<div class="stat-item">
			<div class="stat-value">{{ $units->count() }}</div>
			<div class="stat-label">Total Units</div>
		</div>
		<div class="stat-item">
			<div class="stat-value">{{ $units->where('is_active', true)->count() }}</div>
			<div class="stat-label">Active</div>
		</div>
		<div class="stat-item">
			<div class="stat-value">{{ $units->max('display_order') ?? 0 }}</div>
			<div class="stat-label">Max Order</div>
		</div>
	</div>

	<div class="unit-search-section">
		<div class="search-wrapper">
			<i class="fas fa-search search-icon"></i>
			<input type="text" id="uomSearch" class="search-input" placeholder="Search by name or description...">
			<button class="search-clear" id="clearSearch">
				<i class="fas fa-times"></i>
			</button>
		</div>
	</div>

	<div class="unit-grid" id="uomGrid">
		@forelse($units as $unit)
		<div class="unit-card" data-id="{{ $unit->id }}" data-name="{{ strtolower($unit->standard_value) }}" data-desc="{{ strtolower($unit->description ?? '') }}">
			<div class="card-header">
				<div class="card-icon">
					<i class="fas fa-weight-hanging"></i>
				</div>
				<div class="card-status">
					<span class="status-dot"></span>
					<span class="status-text">Active</span>
				</div>
			</div>
			<div class="card-body">
				<h3 class="card-title">{{ $unit->standard_value }}</h3>
				<p class="card-desc">{{ $unit->description ? Str::limit($unit->description, 40) : 'No description' }}</p>
				<div class="card-meta">
					<div class="meta-item">
						<i class="fas fa-sort-numeric-up"></i>
						<span>Order {{ $unit->display_order }}</span>
					</div>
					<div class="meta-item">
						<i class="fas fa-calendar-alt"></i>
						<span>{{ \Carbon\Carbon::parse($unit->created_at)->format('d M, Y') }}</span>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button class="btn-edit" onclick="editUnit({{ $unit->id }}, '{{ addslashes($unit->standard_value) }}', '{{ addslashes($unit->description) }}', {{ $unit->display_order }})">
					<i class="fas fa-pen"></i>
					<span>Edit</span>
				</button>
			</div>
		</div>
		@empty
		<div class="empty-state">
			<div class="empty-icon">
				<i class="fas fa-ruler"></i>
			</div>
			<h3 class="empty-title">No Units Found</h3>
			<p class="empty-desc">Get started by creating your first unit of measure</p>
			<button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addUomModal">
				<i class="fas fa-plus"></i>
				Create Unit
			</button>
		</div>
		@endforelse
	</div>

	<div class="unit-pagination" id="pagination"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addUomModal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="fas fa-plus-circle"></i>
					Add New Unit
				</h5>
				<button type="button" class="modal-close" data-bs-dismiss="modal">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<form id="addUomForm" action="{{ route('facilitator.unit-of-measures.store') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<label class="form-label">
							Unit Name <span class="required">*</span>
						</label>
						<input type="text" class="form-control" name="standard_value" required placeholder="e.g., Kilogram, Liter">
					</div>
					<div class="form-group">
						<label class="form-label">Description</label>
						<textarea class="form-control" name="description" rows="2" placeholder="Optional description"></textarea>
					</div>
					<input type="hidden" name="standard_type" value="unit_of_measure">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn-save">Create Unit</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editUomModal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="fas fa-edit"></i>
					Edit Unit
				</h5>
				<button type="button" class="modal-close" data-bs-dismiss="modal">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<form id="editUomForm" method="POST">
				@csrf
				@method('PUT')
				<div class="modal-body">
					<input type="hidden" name="id" id="edit_id">
					<div class="form-group">
						<label class="form-label">
							Unit Name <span class="required">*</span>
						</label>
						<input type="text" class="form-control" name="standard_value" id="edit_standard_value" required>
					</div>
					<div class="form-group">
						<label class="form-label">Description</label>
						<textarea class="form-control" name="description" id="edit_description" rows="2"></textarea>
					</div>
					<div class="form-group">
						<label class="form-label">Display Order</label>
						<input type="number" class="form-control" name="display_order" id="edit_display_order" min="1">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn-save">Update Unit</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Loader -->
<div class="loader-overlay" id="loadingOverlay">
	<div class="loader-spinner"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentPage = 1;
let itemsPerPage = 12;
let allItems = [];
let filteredItems = [];

function showLoader() {
	document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoader() {
	document.getElementById('loadingOverlay').style.display = 'none';
}

function showSuccess(msg) {
	Swal.fire({
		icon: 'success',
		title: 'Success',
		text: msg,
		confirmButtonColor: '#10B981',
		timer: 1500,
		showConfirmButton: false,
		background: '#ffffff',
		color: '#0f1724'
	});
}

function showError(msg) {
	Swal.fire({
		icon: 'error',
		title: 'Error',
		text: msg,
		confirmButtonColor: '#10B981',
		background: '#ffffff',
		color: '#0f1724'
	});
}

function editUnit(id, name, desc, order) {
	document.getElementById('edit_id').value = id;
	document.getElementById('edit_standard_value').value = name;
	document.getElementById('edit_description').value = desc || '';
	document.getElementById('edit_display_order').value = order;
	document.getElementById('editUomForm').action = `/facilitator/unit-of-measures/${id}/update`;
	new bootstrap.Modal(document.getElementById('editUomModal')).show();
}

function getItemsPerPage() {
	const width = window.innerWidth;
	if (width >= 2560) return 20;
	if (width >= 1500) return 18;
	if (width >= 1200) return 16;
	if (width >= 992) return 12;
	if (width >= 768) return 10;
	if (width >= 576) return 8;
	return 6;
}

function renderPagination() {
	const total = filteredItems.length || allItems.length;
	const pages = Math.ceil(total / itemsPerPage);
	const pager = document.getElementById('pagination');
	
	if (pages <= 1) {
		pager.innerHTML = '';
		return;
	}

	let html = '<div class="pagination">';
	
	if (currentPage > 1) {
		html += `<button class="page-btn prev" onclick="changePage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></button>`;
	}
	
	for (let i = 1; i <= pages; i++) {
		if (i === 1 || i === pages || (i >= currentPage - 1 && i <= currentPage + 1)) {
			html += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
		} else if (i === currentPage - 2 || i === currentPage + 2) {
			html += '<span class="page-dots">...</span>';
		}
	}
	
	if (currentPage < pages) {
		html += `<button class="page-btn next" onclick="changePage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></button>`;
	}
	
	html += '</div>';
	pager.innerHTML = html;
}

function changePage(page) {
	currentPage = page;
	renderItems();
}

function renderItems() {
	const start = (currentPage - 1) * itemsPerPage;
	const end = start + itemsPerPage;
	const data = filteredItems.length ? filteredItems : allItems;
	const pageItems = data.slice(start, end);
	const grid = document.getElementById('uomGrid');
	
	grid.innerHTML = '';

	if (pageItems.length === 0) {
		grid.innerHTML = `
			<div class="empty-state">
				<div class="empty-icon">
					<i class="fas fa-ruler"></i>
				</div>
				<h3 class="empty-title">No Results Found</h3>
				<p class="empty-desc">Try adjusting your search criteria</p>
			</div>
		`;
		document.getElementById('pagination').innerHTML = '';
		return;
	}

	pageItems.forEach(item => {
		const card = document.createElement('div');
		card.className = 'unit-card';
		card.dataset.id = item.id;
		card.dataset.name = item.name;
		card.dataset.desc = item.desc;

		const date = new Date(item.created_at).toLocaleDateString('en-GB', { 
			day: '2-digit', 
			month: 'short', 
			year: 'numeric' 
		});

		card.innerHTML = `
			<div class="card-header">
				<div class="card-icon">
					<i class="fas fa-weight-hanging"></i>
				</div>
				<div class="card-status">
					<span class="status-dot"></span>
					<span class="status-text">Active</span>
				</div>
			</div>
			<div class="card-body">
				<h3 class="card-title">${item.name}</h3>
				<p class="card-desc">${item.desc ? (item.desc.length > 40 ? item.desc.substring(0,40)+'...' : item.desc) : 'No description'}</p>
				<div class="card-meta">
					<div class="meta-item">
						<i class="fas fa-sort-numeric-up"></i>
						<span>Order ${item.order}</span>
					</div>
					<div class="meta-item">
						<i class="fas fa-calendar-alt"></i>
						<span>${date}</span>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button class="btn-edit" onclick="editUnit(${item.id}, '${item.name.replace(/'/g, "\\'")}', '${(item.desc || '').replace(/'/g, "\\'")}', ${item.order})">
					<i class="fas fa-pen"></i>
					<span>Edit</span>
				</button>
			</div>
		`;
		grid.appendChild(card);
	});

	renderPagination();
}

function loadItems() {
	const cards = document.querySelectorAll('.unit-card');
	allItems = Array.from(cards).map(card => ({
		id: card.dataset.id,
		name: card.dataset.name,
		desc: card.dataset.desc,
		order: card.querySelector('.meta-item:first-child span').textContent.replace('Order', '').trim(),
		created_at: card.querySelector('.meta-item:last-child span').textContent
	}));
	itemsPerPage = getItemsPerPage();
	renderItems();
}

const searchInput = document.getElementById('uomSearch');
const clearBtn = document.getElementById('clearSearch');

function performSearch() {
	const term = searchInput.value.toLowerCase().trim();
	
	if (!term) {
		filteredItems = [];
		currentPage = 1;
		renderItems();
		return;
	}

	filteredItems = allItems.filter(item => 
		item.name.includes(term) || 
		(item.desc && item.desc.includes(term))
	);
	currentPage = 1;
	renderItems();
}

searchInput?.addEventListener('input', performSearch);
clearBtn?.addEventListener('click', () => {
	searchInput.value = '';
	filteredItems = [];
	currentPage = 1;
	renderItems();
});

window.addEventListener('resize', () => {
	const newItemsPerPage = getItemsPerPage();
	if (newItemsPerPage !== itemsPerPage) {
		itemsPerPage = newItemsPerPage;
		currentPage = 1;
		renderItems();
	}
});

document.addEventListener('DOMContentLoaded', () => {
	loadItems();

	document.getElementById('addUomForm')?.addEventListener('submit', function(e) {
		e.preventDefault();
		showLoader();
		
		fetch(this.action, {
			method: 'POST',
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}',
				'Accept': 'application/json'
			},
			body: new FormData(this)
		})
		.then(res => res.json())
		.then(data => {
			hideLoader();
			if (data.success) {
				showSuccess(data.message);
				bootstrap.Modal.getInstance(document.getElementById('addUomModal')).hide();
				setTimeout(() => location.reload(), 1500);
			} else {
				showError(data.message || 'Failed to add unit');
			}
		})
		.catch(() => {
			hideLoader();
			showError('An error occurred');
		});
	});

	document.getElementById('editUomForm')?.addEventListener('submit', function(e) {
		e.preventDefault();
		showLoader();
		
		fetch(this.action, {
			method: 'POST',
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}',
				'X-HTTP-Method-Override': 'PUT',
				'Accept': 'application/json'
			},
			body: new FormData(this)
		})
		.then(res => res.json())
		.then(data => {
			hideLoader();
			if (data.success) {
				showSuccess(data.message);
				bootstrap.Modal.getInstance(document.getElementById('editUomModal')).hide();
				setTimeout(() => location.reload(), 1500);
			} else {
				showError(data.message || 'Failed to update unit');
			}
		})
		.catch(() => {
			hideLoader();
			showError('An error occurred');
		});
	});
});
</script>
@endsection
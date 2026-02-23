@extends('facilitator.layouts.facilitator_master')

@section('title', 'Unit of Measures')
@section('page-title', 'Standards & Measurements')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/unit_of_measures.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="uom-app">
	<div class="uom-bar">
		<div class="bar-section">
			<h2 class="bar-title">
				<i class="fas fa-ruler"></i>
				Units
			</h2>
			<span class="bar-count">{{ $units->count() }}</span>
		</div>
		<div class="bar-section">
			<button class="btn-add" data-bs-toggle="modal" data-bs-target="#addUomModal">
				<i class="fas fa-plus"></i>
			</button>
		</div>
	</div>

	<div class="uom-search">
		<i class="fas fa-search search-icon"></i>
		<input type="text" id="uomSearch" class="search-field" placeholder="Search units...">
		<button class="search-clear" id="clearSearch">
			<i class="fas fa-times"></i>
		</button>
	</div>

	<div class="uom-grid" id="uomGrid">
		@forelse($units as $unit)
		<div class="grid-unit" data-id="{{ $unit->id }}" data-name="{{ strtolower($unit->standard_value) }}" data-desc="{{ strtolower($unit->description ?? '') }}">
			<div class="unit-head">
				<div class="unit-badge"></div>
				<button class="unit-edit" onclick="editUnit({{ $unit->id }}, '{{ addslashes($unit->standard_value) }}', '{{ addslashes($unit->description) }}', {{ $unit->display_order }})">
					<i class="fas fa-pen"></i>
				</button>
			</div>
			<div class="unit-body">
				<h3 class="unit-name">{{ $unit->standard_value }}</h3>
				<p class="unit-desc">{{ Str::limit($unit->description, 30) ?? '—' }}</p>
				<div class="unit-meta">
					<span><i class="fas fa-sort"></i> {{ $unit->display_order }}</span>
					<span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($unit->created_at)->format('d/m/y') }}</span>
				</div>
			</div>
		</div>
		@empty
		<div class="empty-box">
			<i class="fas fa-ruler"></i>
			<p>No units</p>
			<button class="btn-add sm" data-bs-toggle="modal" data-bs-target="#addUomModal">Add</button>
		</div>
		@endforelse
	</div>

	<div class="uom-pager" id="pagination"></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addUomModal" tabindex="-1">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-head">
				<h5 class="modal-heading">New Unit</h5>
				<button type="button" class="modal-close" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
			</div>
			<form id="addUomForm" action="{{ route('facilitator.unit-of-measures.store') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="field">
						<label class="field-label">Name <span class="required">*</span></label>
						<input type="text" class="field-input" name="standard_value" required placeholder="e.g., kg">
					</div>
					<div class="field">
						<label class="field-label">Description</label>
						<textarea class="field-input" name="description" rows="2" placeholder="Optional"></textarea>
					</div>
					<input type="hidden" name="standard_type" value="unit_of_measure">
				</div>
				<div class="modal-foot">
					<button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn-save">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editUomModal" tabindex="-1">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-head">
				<h5 class="modal-heading">Edit Unit</h5>
				<button type="button" class="modal-close" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
			</div>
			<form id="editUomForm" method="POST">
				@csrf
				@method('PUT')
				<div class="modal-body">
					<input type="hidden" name="id" id="edit_id">
					<div class="field">
						<label class="field-label">Name <span class="required">*</span></label>
						<input type="text" class="field-input" name="standard_value" id="edit_standard_value" required>
					</div>
					<div class="field">
						<label class="field-label">Description</label>
						<textarea class="field-input" name="description" id="edit_description" rows="2"></textarea>
					</div>
					<div class="field">
						<label class="field-label">Order</label>
						<input type="number" class="field-input" name="display_order" id="edit_display_order" min="1">
					</div>
				</div>
				<div class="modal-foot">
					<button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn-save">Update</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Loader -->
<div class="loader" id="loadingOverlay">
	<div class="spinner"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentPage = 1;
let perPage = 12;
let items = [];
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
		text: msg,
		showConfirmButton: false,
		timer: 1200,
		width: '280px',
		padding: '1em'
	});
}

function showError(msg) {
	Swal.fire({
		icon: 'error',
		text: msg,
		confirmButtonColor: '#10B981',
		width: '280px',
		padding: '1em'
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

function getPerPage() {
	const w = window.innerWidth;
	if (w >= 1500) return 16;
	if (w >= 1200) return 16;
	if (w >= 992) return 8;
	if (w >= 768) return 8;
	if (w >= 576) return 4;
	return 4;
}

function renderPagination() {
	const total = (filteredItems.length || items.length);
	const pages = Math.ceil(total / perPage);
	const pager = document.getElementById('pagination');
	
	if (pages <= 1) {
		pager.innerHTML = '';
		return;
	}

	let html = '<div class="pager-list">';
	
	if (currentPage > 1) {
		html += `<button class="pager-btn" onclick="goToPage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></button>`;
	}
	
	for (let i = 1; i <= pages; i++) {
		if (i === 1 || i === pages || (i >= currentPage - 1 && i <= currentPage + 1)) {
			html += `<button class="pager-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
		} else if (i === currentPage - 2 || i === currentPage + 2) {
			html += '<span class="pager-dots">...</span>';
		}
	}
	
	if (currentPage < pages) {
		html += `<button class="pager-btn" onclick="goToPage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></button>`;
	}
	
	html += '</div>';
	pager.innerHTML = html;
}

function goToPage(page) {
	currentPage = page;
	renderItems();
}

function renderItems() {
	const start = (currentPage - 1) * perPage;
	const end = start + perPage;
	const data = filteredItems.length ? filteredItems : items;
	const pageItems = data.slice(start, end);
	const grid = document.getElementById('uomGrid');
	
	grid.innerHTML = '';

	if (pageItems.length === 0) {
		grid.innerHTML = `
			<div class="empty-box">
				<i class="fas fa-ruler"></i>
				<p>No results</p>
			</div>
		`;
		document.getElementById('pagination').innerHTML = '';
		return;
	}

	pageItems.forEach(item => {
		const div = document.createElement('div');
		div.className = 'grid-unit';
		div.dataset.id = item.id;
		div.dataset.name = item.name;
		div.dataset.desc = item.desc;

		const date = new Date(item.created_at).toLocaleDateString('en-GB', { 
			day: '2-digit', 
			month: '2-digit', 
			year: '2-digit' 
		});

		div.innerHTML = `
			<div class="unit-head">
				<div class="unit-badge"></div>
				<button class="unit-edit" onclick="editUnit(${item.id}, '${item.name.replace(/'/g, "\\'")}', '${(item.desc || '').replace(/'/g, "\\'")}', ${item.order})">
					<i class="fas fa-pen"></i>
				</button>
			</div>
			<div class="unit-body">
				<h3 class="unit-name">${item.name}</h3>
				<p class="unit-desc">${item.desc ? (item.desc.length > 30 ? item.desc.substring(0,30)+'...' : item.desc) : '—'}</p>
				<div class="unit-meta">
					<span><i class="fas fa-sort"></i> ${item.order}</span>
					<span><i class="fas fa-calendar"></i> ${date}</span>
				</div>
			</div>
		`;
		grid.appendChild(div);
	});

	renderPagination();
}

function loadItems() {
	const cards = document.querySelectorAll('.grid-unit');
	items = Array.from(cards).map(card => ({
		id: card.dataset.id,
		name: card.dataset.name,
		desc: card.dataset.desc,
		order: card.querySelector('.unit-meta span:first-child').textContent.replace('Order:', '').trim(),
		created_at: card.querySelector('.unit-meta span:last-child').textContent
	}));
	perPage = getPerPage();
	renderItems();
}

const search = document.getElementById('uomSearch');
const clear = document.getElementById('clearSearch');

function doSearch() {
	const term = search.value.toLowerCase().trim();
	
	if (!term) {
		filteredItems = [];
		currentPage = 1;
		renderItems();
		return;
	}

	filteredItems = items.filter(item => 
		item.name.includes(term) || 
		(item.desc && item.desc.includes(term))
	);
	currentPage = 1;
	renderItems();
}

search?.addEventListener('input', doSearch);
clear?.addEventListener('click', () => {
	search.value = '';
	filteredItems = [];
	currentPage = 1;
	renderItems();
});

window.addEventListener('resize', () => {
	const newPerPage = getPerPage();
	if (newPerPage !== perPage) {
		perPage = newPerPage;
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
				setTimeout(() => location.reload(), 1200);
			} else {
				showError(data.message || 'Failed');
			}
		})
		.catch(() => {
			hideLoader();
			showError('Error');
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
				setTimeout(() => location.reload(), 1200);
			} else {
				showError(data.message || 'Failed');
			}
		})
		.catch(() => {
			hideLoader();
			showError('Error');
		});
	});
});
</script>
@endsection
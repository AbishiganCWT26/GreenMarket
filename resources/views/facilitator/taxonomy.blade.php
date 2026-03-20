@extends('facilitator.layouts.facilitator_master')

@section('title', 'Taxonomy Management')
@section('page-title', 'Category Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/taxonomy.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="taxo-container">
	<div class="header-bar">
		<div class="header-left">
			<h1 class="page-title1">
				<i class="fas fa-diagram-project"></i>
				Category Structure
			</h1>
			<p class="page-desc">Manage product categories, subcategories, and product examples</p>
		</div>
		<div class="header-right">
			<button class="btn-add" data-bs-toggle="modal" data-bs-target="#addCategoryFullModal">
				<i class="fas fa-plus"></i>
				<span>Add Category</span>
			</button>
		</div>
	</div>

	<div class="search-section">
		<div class="search-box">
			<i class="fas fa-search search-icon"></i>
			<input type="text" id="taxonomySearch" class="search-input" placeholder="Search categories, subcategories, products...">
			<button class="search-clear" id="clearSearch">
				<i class="fas fa-times"></i>
			</button>
		</div>
	</div>

	<div class="action-bar">
		<div class="item-counter" id="itemsCount">Loading items...</div>
		<div class="action-group">
			<button class="action-btn" id="collapseAll" title="Collapse All">
				<i class="fas fa-compress-alt"></i>
			</button>
			<button class="action-btn" id="expandAll" title="Expand All">
				<i class="fas fa-expand-alt"></i>
			</button>
		</div>
	</div>

	<div class="tree-view" id="taxonomyTree">
		@forelse($categories as $category)
		<div class="tree-item level-0" data-id="{{ $category->id }}" data-name="{{ strtolower($category->category_name) }}" data-description="{{ strtolower($category->description ?? '') }}">
			<div class="item-header" onclick="toggleItem(this)">
				<div class="item-info">
					<div class="item-icon">
						@if($category->icon_filename)
						<img src="{{ asset('assets/images/taxonomy-icons/' . $category->icon_filename) }}" alt="{{ $category->category_name }}">
						@else
						<i class="fas fa-folder"></i>
						@endif
					</div>
					<div class="item-content">
						<div class="item-title">
							<span class="item-name">{{ $category->category_name }}</span>
							@if(!$category->is_active)
							<span class="status-badge inactive">Inactive</span>
							@endif
						</div>
						@if($category->description)
						<div class="item-desc">{{ $category->description }}</div>
						@endif
						<div class="item-stats">
							<span class="stat">
								<i class="fas fa-list"></i>
								{{ $category->subcategories->count() }} sub
							</span>
							<span class="stat">
								<i class="fas fa-box"></i>
								@php
									$total = 0;
									foreach($category->subcategories as $sub) {
										$total += $sub->productExamples->count();
									}
								@endphp
								{{ $total }} prod
							</span>
							<span class="stat">
								<i class="fas fa-sort-numeric-up"></i>
								{{ $category->display_order }}
							</span>
						</div>
					</div>
				</div>
				<div class="item-actions">
					<button class="action-icon" onclick="event.stopPropagation(); addSubcategory({{ $category->id }})" title="Add Subcategory">
						<i class="fas fa-plus"></i>
					</button>
					<button class="action-icon" onclick="event.stopPropagation(); editCategory({{ $category->id }}, '{{ addslashes($category->category_name) }}', '{{ addslashes($category->description) }}')" title="Edit">
						<i class="fas fa-edit"></i>
					</button>
					<i class="fas fa-chevron-down toggle-icon"></i>
				</div>
			</div>

			<div class="item-children">
				@foreach($category->subcategories as $subcategory)
				<div class="tree-item level-1" data-id="{{ $subcategory->id }}" data-name="{{ strtolower($subcategory->subcategory_name) }}" data-description="{{ strtolower($subcategory->description ?? '') }}" data-parent="{{ $category->category_name }}">
					<div class="item-header" onclick="toggleItem(this)">
						<div class="item-info">
							<div class="item-icon">
								<i class="fas fa-folder-open"></i>
							</div>
							<div class="item-content">
								<div class="item-title">
									<span class="item-name">{{ $subcategory->subcategory_name }}</span>
									@if(!$subcategory->is_active)
									<span class="status-badge inactive">Inactive</span>
									@endif
								</div>
								@if($subcategory->description)
								<div class="item-desc">{{ $subcategory->description }}</div>
								@endif
								<div class="item-stats">
									<span class="stat">
										<i class="fas fa-box"></i>
										{{ $subcategory->productExamples->count() }} prod
									</span>
									<span class="stat">
										<i class="fas fa-sort-numeric-up"></i>
										{{ $subcategory->display_order }}
									</span>
								</div>
							</div>
						</div>
						<div class="item-actions">
							<button class="action-icon" onclick="event.stopPropagation(); addProduct({{ $subcategory->id }})" title="Add Product">
								<i class="fas fa-plus"></i>
							</button>
							<button class="action-icon" onclick="event.stopPropagation(); editSubcategory({{ $subcategory->id }}, '{{ addslashes($subcategory->subcategory_name) }}', '{{ addslashes($subcategory->description) }}', {{ $subcategory->category_id }})" title="Edit">
								<i class="fas fa-edit"></i>
							</button>
							<i class="fas fa-chevron-down toggle-icon"></i>
						</div>
					</div>

					<div class="item-children">
						@foreach($subcategory->productExamples as $product)
						<div class="tree-item level-2" data-id="{{ $product->id }}" data-name="{{ strtolower($product->product_name) }}" data-description="{{ strtolower($product->description ?? '') }}" data-parent="{{ $subcategory->subcategory_name }}">
							<div class="item-header">
								<div class="item-info">
									<div class="item-icon">
										<i class="fas fa-cube"></i>
									</div>
									<div class="item-content">
										<div class="item-title">
											<span class="item-name">{{ $product->product_name }}</span>
											@if(!$product->is_active)
											<span class="status-badge inactive">Inactive</span>
											@endif
										</div>
										@if($product->description)
										<div class="item-desc">{{ $product->description }}</div>
										@endif
										<div class="item-stats">
											<span class="stat">
												<i class="fas fa-hashtag"></i>
												{{ $product->id }}
											</span>
											<span class="stat">
												<i class="fas fa-sort-numeric-up"></i>
												{{ $product->display_order }}
											</span>
										</div>
									</div>
								</div>
								<div class="item-actions">
									<button class="action-icon" onclick="event.stopPropagation(); editProduct({{ $product->id }}, '{{ addslashes($product->product_name) }}', '{{ addslashes($product->description) }}', {{ $product->subcategory_id }})" title="Edit">
										<i class="fas fa-edit"></i>
									</button>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>
				@endforeach
			</div>
		</div>
		@empty
		<div class="empty-view">
			<i class="fas fa-folder-open"></i>
			<h3>No Categories Yet</h3>
			<p>Start by adding your first product category</p>
			<button class="btn-add" data-bs-toggle="modal" data-bs-target="#addCategoryFullModal">
				<i class="fas fa-plus"></i>
				Add First Category
			</button>
		</div>
		@endforelse
	</div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryFullModal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="fas fa-plus-circle"></i>
					Add New Category
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<form id="addCategoryFullForm" method="POST" action="{{ route('facilitator.taxonomy.category.store') }}" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="form-card">
						<div class="form-title">
							<i class="fas fa-folder"></i>
							<span>Main Category Details</span>
						</div>
						<div class="form-row">
							<div class="form-group">
								<label class="form-label">
									Category Name <span class="required">*</span>
								</label>
								<input type="text" class="form-control" name="category_name" id="full_category_name" required placeholder="e.g., Fresh Fruits">
							</div>
							<div class="form-group">
								<label class="form-label">Description</label>
								<input type="text" class="form-control" name="description" id="full_description" rows="2" placeholder="Brief description">
							</div>
							<div class="form-group">
								<label class="form-label">
									Category Icon <span class="required">*</span>
									<small>(PNG only, max 5MB)</small>
								</label>
								<input type="file" class="form-control" name="category_icon" id="full_category_icon" accept=".png" required>
								<div id="iconPreview" class="icon-preview" style="display:none;">
									<img id="iconPreviewImg" src="" alt="Icon Preview">
								</div>
							</div>
						</div>
					</div>

					<div class="form-card">
						<div class="form-title">
							<i class="fas fa-list"></i>
							<span>Sub-Category Examples</span>
							<button type="button" class="btn-add-mini" onclick="addSubcategoryField()">
								<i class="fas fa-plus"></i> Add
							</button>
						</div>
						<div id="subcategories-container"></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn-primary">
						<i class="fas fa-save"></i>
						Save Category
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
	<div class="spinner"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let subcategoryCount = 0;
let productCounts = {};

function toggleItem(header) {
	const item = header.closest('.tree-item');
	const children = item.querySelector('.item-children');
	const icon = header.querySelector('.toggle-icon');

	if (children) {
		children.classList.toggle('show');
		icon.classList.toggle('fa-chevron-down');
		icon.classList.toggle('fa-chevron-up');
	}
}

document.getElementById('collapseAll')?.addEventListener('click', function() {
	document.querySelectorAll('.item-children.show').forEach(child => {
		child.classList.remove('show');
		const icon = child.closest('.tree-item')?.querySelector('.toggle-icon');
		if (icon) {
			icon.classList.remove('fa-chevron-up');
			icon.classList.add('fa-chevron-down');
		}
	});
});

document.getElementById('expandAll')?.addEventListener('click', function() {
	document.querySelectorAll('.item-children:not(.show)').forEach(child => {
		child.classList.add('show');
		const icon = child.closest('.tree-item')?.querySelector('.toggle-icon');
		if (icon) {
			icon.classList.remove('fa-chevron-down');
			icon.classList.add('fa-chevron-up');
		}
	});
});

function showLoading() {
	document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
	document.getElementById('loadingOverlay').style.display = 'none';
}

function showSuccess(msg) {
	Swal.fire({
		icon: 'success',
		title: 'Success',
		text: msg,
		confirmButtonColor: '#10B981',
		timer: 1500,
		showConfirmButton: false
	});
}

function showError(msg) {
	Swal.fire({
		icon: 'error',
		title: 'Error',
		text: msg,
		confirmButtonColor: '#10B981'
	});
}

function addSubcategory(categoryId) {
	Swal.fire({
		title: 'Add Subcategory',
		width: '550px',
		html: `
			<div class="text-start">
				<div class="mb-2">
					<label class="form-label">Subcategory Name *</label>
					<input type="text" class="form-control" id="subcategoryName" placeholder="e.g., Tropical Fruits">
				</div>
				<div class="mb-2">
					<label class="form-label">Description</label>
					<input type="text" class="form-control" id="subcategoryDesc" rows="2" placeholder="Description">
				</div>
				<div class="mb-2 d-flex justify-content-between align-items-center">
					<label class="form-label mb-0">Products * (min 2)</label>
					<button type="button" class="btn btn-sm btn-primary" onclick="addSwalProductField()">
						<i class="fas fa-plus"></i> Add
					</button>
				</div>
				<div id="swalProductsContainer">
					<div class="swal-product-item mb-2">
						<input type="text" class="form-control mb-1 swal-product-name" placeholder="Product Name 1 *" required>
						<input type="text" class="form-control swal-product-desc" rows="1" placeholder="Product Description 1">
					</div>
					<div class="swal-product-item mb-2">
						<input type="text" class="form-control mb-1 swal-product-name" placeholder="Product Name 2 *" required>
						<input type="text" class="form-control swal-product-desc" rows="1" placeholder="Product Description 2">
					</div>
				</div>
			</div>
		`,
		showCancelButton: true,
		confirmButtonText: 'Add',
		confirmButtonColor: '#10B981',
		cancelButtonColor: '#6b7280',
		didOpen: () => {
			window.addSwalProductField = () => {
				const container = document.getElementById('swalProductsContainer');
				const div = document.createElement('div');
				div.className = 'swal-product-item mb-2 d-flex gap-2 align-items-start';
				div.innerHTML = `
					<div class="flex-grow-1">
						<input type="text" class="form-control mb-1 swal-product-name" placeholder="Product Name *">
						<input type="text" class="form-control swal-product-desc" rows="1" placeholder="Product Description">
					</div>
					<button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">
						<i class="fas fa-times"></i>
					</button>
				`;
				container.appendChild(div);
			};
		},
		preConfirm: () => {
			const name = document.getElementById('subcategoryName').value;
			if (!name) {
				Swal.showValidationMessage('Subcategory name required');
				return false;
			}
			const productItems = document.querySelectorAll('.swal-product-item');
			const products = [];
			let allNamesProvided = true;
			productItems.forEach(item => {
				const pName = item.querySelector('.swal-product-name').value;
				const pDesc = item.querySelector('.swal-product-desc').value;
				if (pName) {
					products.push({ name: pName, description: pDesc });
				} else {
					allNamesProvided = false;
				}
			});
			if (products.length < 2) {
				Swal.showValidationMessage('At least 2 products required');
				return false;
			}
			if (!allNamesProvided) {
				Swal.showValidationMessage('All products must have a name');
				return false;
			}
			return {
				name: name,
				description: document.getElementById('subcategoryDesc').value,
				categoryId: categoryId,
				products: products
			};
		}
	}).then(result => {
		if (result.isConfirmed) {
			showLoading();
			fetch('{{ route("facilitator.taxonomy.subcategory.store") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify(result.value)
			})
			.then(res => res.json())
			.then(data => {
				hideLoading();
				if (data.success) {
					showSuccess(data.message);
					setTimeout(() => location.reload(), 1500);
				} else {
					showError(data.message || 'Failed');
				}
			})
			.catch(() => {
				hideLoading();
				showError('Error adding subcategory');
			});
		}
	});
}

function addProduct(subcategoryId) {
	Swal.fire({
		title: 'Add Product',
		html: `
			<div class="text-start">
				<div class="mb-2">
					<label class="form-label">Product Name *</label>
					<input type="text" class="form-control" id="productName" placeholder="e.g., TJC Mango">
				</div>
				<div class="mb-2">
					<label class="form-label">Description</label>
					<textarea class="form-control" id="productDesc" rows="2" placeholder="Description">
				</div>
			</div>
		`,
		showCancelButton: true,
		confirmButtonText: 'Add',
		confirmButtonColor: '#10B981',
		cancelButtonColor: '#6b7280',
		preConfirm: () => {
			const name = document.getElementById('productName').value;
			if (!name) {
				Swal.showValidationMessage('Product name required');
				return false;
			}
			return {
				name: name,
				description: document.getElementById('productDesc').value,
				subcategoryId: subcategoryId
			};
		}
	}).then(result => {
		if (result.isConfirmed) {
			showLoading();
			fetch('{{ route("facilitator.taxonomy.product.store") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify(result.value)
			})
			.then(res => res.json())
			.then(data => {
				hideLoading();
				if (data.success) {
					showSuccess(data.message);
					setTimeout(() => location.reload(), 1500);
				} else {
					showError(data.message || 'Failed');
				}
			})
			.catch(() => {
				hideLoading();
				showError('Error adding product');
			});
		}
	});
}

function editCategory(id, name, desc) {
	Swal.fire({
		title: 'Edit Category',
		width: '500px',
		html: `
			<div class="text-start">
				<div class="mb-2">
					<label class="form-label">Category Name *</label>
					<input type="text" class="form-control" id="editCatName" value="${name}">
				</div>
				<div class="mb-2">
					<label class="form-label">Description</label>
					<input type="text" class="form-control" id="editCatDesc" value="${desc || ''}">
				</div>
				<div class="mb-2">
					<label class="form-label">
						Category Icon <small>(Optional, PNG only, max 5MB)</small>
					</label>
					<input type="file" class="form-control" id="editCatIcon" accept=".png">
					<div id="editIconPreview" class="icon-preview mt-2" style="display:none;">
						<img id="editIconPreviewImg" src="" alt="Icon Preview" style="max-width: 50px;">
					</div>
				</div>
			</div>
		`,
		showCancelButton: true,
		confirmButtonText: 'Update',
		confirmButtonColor: '#10B981',
		cancelButtonColor: '#6b7280',
		didOpen: () => {
			document.getElementById('editCatIcon').onchange = function(e) {
				const file = e.target.files[0];
				const preview = document.getElementById('editIconPreview');
				const img = document.getElementById('editIconPreviewImg');
				if (file) {
					if (file.type !== 'image/png') {
						showError('Only PNG allowed');
						this.value = '';
						return;
					}
					const reader = new FileReader();
					reader.onload = e => {
						img.src = e.target.result;
						preview.style.display = 'block';
					};
					reader.readAsDataURL(file);
				}
			};
		},
		preConfirm: () => {
			const newName = document.getElementById('editCatName').value;
			if (!newName) {
				Swal.showValidationMessage('Category name required');
				return false;
			}
			
			const formData = new FormData();
			formData.append('id', id);
			formData.append('name', newName);
			formData.append('description', document.getElementById('editCatDesc').value);
			
			const iconFile = document.getElementById('editCatIcon').files[0];
			if (iconFile) {
				formData.append('category_icon', iconFile);
			}
			
			return formData;
		}
	}).then(result => {
		if (result.isConfirmed) {
			showLoading();
			fetch('{{ route("facilitator.taxonomy.category.update") }}', {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: result.value
			})
			.then(res => res.json())
			.then(data => {
				hideLoading();
				if (data.success) {
					showSuccess(data.message);
					setTimeout(() => location.reload(), 1500);
				} else {
					showError(data.message || 'Failed');
				}
			})
			.catch(() => {
				hideLoading();
				showError('Error updating category');
			});
		}
	});
}

function editSubcategory(id, name, desc, catId) {
	Swal.fire({
		title: 'Edit Subcategory',
		html: `
			<div class="text-start">
				<div class="mb-2">
					<label class="form-label">Subcategory Name *</label>
					<input type="text" class="form-control" id="editName" value="${name}">
				</div>
				<div class="mb-2">
					<label class="form-label">Description</label>
					<input type="text" class="form-control" id="editDesc" rows="2">${desc || ''}
				</div>
			</div>
		`,
		showCancelButton: true,
		confirmButtonText: 'Update',
		confirmButtonColor: '#10B981',
		cancelButtonColor: '#6b7280',
		preConfirm: () => {
			const newName = document.getElementById('editName').value;
			if (!newName) {
				Swal.showValidationMessage('Subcategory name required');
				return false;
			}
			return {
				id: id,
				name: newName,
				description: document.getElementById('editDesc').value,
				category_id: catId
			};
		}
	}).then(result => {
		if (result.isConfirmed) {
			showLoading();
			fetch('{{ route("facilitator.taxonomy.subcategory.update") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify(result.value)
			})
			.then(res => res.json())
			.then(data => {
				hideLoading();
				if (data.success) {
					showSuccess(data.message);
					setTimeout(() => location.reload(), 1500);
				} else {
					showError(data.message || 'Failed');
				}
			})
			.catch(() => {
				hideLoading();
				showError('Error updating subcategory');
			});
		}
	});
}

function editProduct(id, name, desc, subId) {
	Swal.fire({
		title: 'Edit Product',
		html: `
			<div class="text-start">
				<div class="mb-2">
					<label class="form-label">Product Name *</label>
					<input type="text" class="form-control" id="editName" value="${name}">
				</div>
				<div class="mb-2">
					<label class="form-label">Description</label>
					<input type="text" class="form-control" id="editDesc" rows="2">${desc || ''}
				</div>
			</div>
		`,
		showCancelButton: true,
		confirmButtonText: 'Update',
		confirmButtonColor: '#10B981',
		cancelButtonColor: '#6b7280',
		preConfirm: () => {
			const newName = document.getElementById('editName').value;
			if (!newName) {
				Swal.showValidationMessage('Product name required');
				return false;
			}
			return {
				id: id,
				name: newName,
				description: document.getElementById('editDesc').value,
				subcategory_id: subId
			};
		}
	}).then(result => {
		if (result.isConfirmed) {
			showLoading();
			fetch('{{ route("facilitator.taxonomy.product.update") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify(result.value)
			})
			.then(res => res.json())
			.then(data => {
				hideLoading();
				if (data.success) {
					showSuccess(data.message);
					setTimeout(() => location.reload(), 1500);
				} else {
					showError(data.message || 'Failed');
				}
			})
			.catch(() => {
				hideLoading();
				showError('Error updating product');
			});
		}
	});
}

function addSubcategoryField() {
	const container = document.getElementById('subcategories-container');
	const index = subcategoryCount++;
	productCounts[index] = 0;

	const html = `
		<div class="subcategory-card" data-index="${index}">
			<div class="subcategory-header">
				<span class="subcategory-title">Subcategory #${index + 1}</span>
				<button type="button" class="btn-remove" onclick="removeSubcategory(this, ${index})">
					<i class="fas fa-trash"></i>
				</button>
			</div>
			<div class="subcategory-body">
				<div class="form-row">
					<div class="form-group">
						<label class="form-label">Name *</label>
						<input type="text" class="form-control" name="subcategories[${index}][name]" required>
					</div>
					<div class="form-group">
						<label class="form-label">Description</label>
						<input type="text" class="form-control" name="subcategories[${index}][description]" rows="1">
					</div>
				</div>
				<div class="products-header">
					<span>Products <small>(min 2)</small></span>
					<button type="button" class="btn-add-mini" onclick="addProductField(${index})">
						<i class="fas fa-plus"></i> Add
					</button>
				</div>
				<div class="products-list" id="products-${index}"></div>
			</div>
		</div>
	`;

	container.insertAdjacentHTML('beforeend', html);

	for (let i = 0; i < 2; i++) {
		addProductField(index);
	}
}

function addProductField(subIdx) {
	const container = document.getElementById(`products-${subIdx}`);
	const prodIdx = productCounts[subIdx]++;

	const html = `
		<div class="product-card">
			<div class="form-row">
				<div class="form-group">
					<label class="form-label">Name *</label>
					<input type="text" class="form-control" name="subcategories[${subIdx}][products][${prodIdx}][name]" required>
				</div>
				<div class="form-group">
					<label class="form-label">Description</label>
					<input type="text" class="form-control" name="subcategories[${subIdx}][products][${prodIdx}][description]" rows="1">
				</div>
				<button type="button" class="btn-remove-mini" onclick="removeProductField(this, ${subIdx})">
					<i class="fas fa-times"></i>
				</button>
			</div>
		</div>
	`;

	container.insertAdjacentHTML('beforeend', html);
}

function removeSubcategory(btn, idx) {
	const cards = document.querySelectorAll('.subcategory-card');
	if (cards.length <= 1) {
		Swal.fire('Error', 'At least one subcategory required', 'error');
		return;
	}
	btn.closest('.subcategory-card').remove();
	delete productCounts[idx];
}

function removeProductField(btn, subIdx) {
	const container = document.getElementById(`products-${subIdx}`);
	if (container.children.length <= 2) {
		Swal.fire('Error', 'At least 2 products required', 'error');
		return;
	}
	btn.closest('.product-card').remove();
}

document.getElementById('full_category_icon')?.addEventListener('change', function(e) {
	const file = e.target.files[0];
	const preview = document.getElementById('iconPreview');
	const img = document.getElementById('iconPreviewImg');

	if (file) {
		if (file.type !== 'image/png') {
			Swal.fire('Invalid', 'Only PNG allowed', 'error');
			this.value = '';
			preview.style.display = 'none';
			return;
		}
		if (file.size > 5 * 1024 * 1024) {
			Swal.fire('Too Large', 'Max 5MB', 'error');
			this.value = '';
			preview.style.display = 'none';
			return;
		}
		const reader = new FileReader();
		reader.onload = e => {
			img.src = e.target.result;
			preview.style.display = 'block';
		};
		reader.readAsDataURL(file);
	} else {
		preview.style.display = 'none';
	}
});

document.getElementById('addCategoryFullForm')?.addEventListener('submit', function(e) {
	e.preventDefault();

	if (!document.getElementById('full_category_icon').files.length) {
		Swal.fire('Error', 'Category icon required', 'error');
		return;
	}

	const subCards = document.querySelectorAll('.subcategory-card');
	if (!subCards.length) {
		Swal.fire('Error', 'At least one subcategory required', 'error');
		return;
	}

	let valid = true;
	subCards.forEach((card, i) => {
		const products = card.querySelectorAll('.product-card');
		if (products.length < 2) {
			valid = false;
			Swal.fire('Error', `Subcategory #${i+1} needs at least 2 products`, 'error');
		}
	});

	if (!valid) return;

	showLoading();
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
		hideLoading();
		if (data.success) {
			showSuccess(data.message);
			bootstrap.Modal.getInstance(document.getElementById('addCategoryFullModal')).hide();
			setTimeout(() => location.reload(), 1500);
		} else {
			showError(data.message || 'Failed');
		}
	})
	.catch(() => {
		hideLoading();
		showError('Error adding category');
	});
});

document.getElementById('addCategoryFullModal')?.addEventListener('show.bs.modal', function() {
	document.getElementById('subcategories-container').innerHTML = '';
	subcategoryCount = 0;
	productCounts = {};
	addSubcategoryField();
});

const searchInput = document.getElementById('taxonomySearch');
const clearBtn = document.getElementById('clearSearch');
const itemsCount = document.getElementById('itemsCount');

function updateItemsCount() {
	const visible = document.querySelectorAll('.tree-item[style*="display: block"], .tree-item:not([style*="display: none"])').length;
	itemsCount.textContent = `Showing ${visible} items`;
}

function performSearch() {
	const term = searchInput.value.toLowerCase().trim();
	const items = document.querySelectorAll('.tree-item');

	if (!term) {
		items.forEach(i => i.style.display = 'block');
		document.querySelectorAll('.item-children').forEach(c => c.classList.remove('show'));
		updateItemsCount();
		return;
	}

	items.forEach(item => {
		const name = item.dataset.name || '';
		const desc = item.dataset.description || '';
		const parent = item.dataset.parent || '';

		if (name.includes(term) || desc.includes(term) || parent.includes(term)) {
			item.style.display = 'block';
			
			// Show all parents of the matching item
			let parentItem = item.parentElement ? item.parentElement.closest('.tree-item') : null;
			while (parentItem) {
				parentItem.style.display = 'block';
				const children = parentItem.querySelector('.item-children');
				if (children) children.classList.add('show');
				parentItem = parentItem.parentElement ? parentItem.parentElement.closest('.tree-item') : null;
			}
		} else {
			item.style.display = 'none';
		}
	});

	updateItemsCount();
}

searchInput?.addEventListener('input', performSearch);
clearBtn?.addEventListener('click', function() {
	searchInput.value = '';
	performSearch();
});

updateItemsCount();
</script>
@endsection
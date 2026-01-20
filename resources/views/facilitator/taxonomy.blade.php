@extends('facilitator.layouts.facilitator_master')

@section('title', 'Taxonomy Management')
@section('page-title', 'Taxonomy & Standards Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/taxonomy.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="page-header-card">
            <div class="header-content">
                <h4><i class="fa-solid fa-layer-group me-2"></i> Product Taxonomy</h4>
                <p class="text-muted mb-0">Manage product categories, subcategories, and product examples</p>
            </div>
            <button class="btn-add-new" data-bs-toggle="modal" data-bs-target="#addCategoryFullModal">
                <i class="fa-solid fa-plus"></i> Add Category
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-sitemap me-2"></i> Category Structure</h5>
                <div class="header-actions">
                    <button class="btn-action" title="Collapse All" id="collapseAll">
                        <i class="fa-solid fa-compress"></i>
                    </button>
                    <button class="btn-action" title="Expand All" id="expandAll">
                        <i class="fa-solid fa-expand"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($categories->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-layer-group fa-3x text-muted mb-3"></i>
                    <h5>No Categories Yet</h5>
                    <p class="text-muted">Start by adding your first product category</p>
                    <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fa-solid fa-plus"></i> Add First Category
                    </button>
                </div>
                @else
                <div class="taxonomy-tree" id="taxonomyTree">
                    @foreach($categories as $category)
                    <div class="taxonomy-item level-0" data-id="{{ $category->id }}">
                        <div class="item-header" onclick="toggleItem(this)">
                            <div class="item-info">
                                <div class="item-icon">
                                    <i class="fa-solid fa-folder"></i>
                                </div>
                                <div class="item-content">
                                    <h6 class="item-title">{{ $category->category_name }}
                                        <span class="badge bg-secondary">Order: {{ $category->display_order }}</span>
                                        @if(!$category->is_active)
                                        <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </h6>
                                    @if($category->description)
                                    <p class="item-desc">{{ $category->description }}</p>
                                    @endif
                                    <div class="item-meta">
                                        <span class="badge bg-light text-dark">
                                            <i class="fa-solid fa-list me-1"></i>
                                            {{ $category->subcategories->count() }} Subcategories
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <i class="fa-solid fa-box me-1"></i>
                                            @php
                                                $totalProducts = 0;
                                                foreach($category->subcategories as $sub) {
                                                    $totalProducts += $sub->productExamples->count();
                                                }
                                            @endphp
                                            {{ $totalProducts }} Products
                                        </span>
                                        <span class="badge bg-info">
                                            <i class="fa-solid fa-sort-numeric-up me-1"></i>
                                            Display Order: {{ $category->display_order }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="item-actions">
                                <button class="btn-action" title="Add Subcategory" onclick="event.stopPropagation(); addSubcategory({{ $category->id }})">
                                    <i class="fa-solid fa-plus-circle"></i>
                                </button>
                                <button class="btn-action" title="Edit" onclick="event.stopPropagation(); editCategory({{ $category->id }}, '{{ addslashes($category->category_name) }}', '{{ addslashes($category->description) }}')">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <span class="toggle-icon">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </div>
                        </div>

                        <div class="item-children">
                            @foreach($category->subcategories as $subcategory)
                            <div class="taxonomy-item level-1" data-id="{{ $subcategory->id }}">
                                <div class="item-header" onclick="toggleItem(this)">
                                    <div class="item-info">
                                        <div class="item-icon">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </div>
                                        <div class="item-content">
                                            <h6 class="item-title">{{ $subcategory->subcategory_name }}
                                                <span class="badge bg-secondary">Order: {{ $subcategory->display_order }}</span>
                                                @if(!$subcategory->is_active)
                                                <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </h6>
                                            @if($subcategory->description)
                                            <p class="item-desc">{{ $subcategory->description }}</p>
                                            @endif
                                            <div class="item-meta">
                                                <span class="badge bg-light text-dark">
                                                    <i class="fa-solid fa-box me-1"></i>
                                                    {{ $subcategory->productExamples->count() }} Products
                                                </span>
                                                <span class="badge bg-info">
                                                    <i class="fa-solid fa-sort-numeric-up me-1"></i>
                                                    Display Order: {{ $subcategory->display_order }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-actions">
                                        <button class="btn-action" title="Add Product" onclick="event.stopPropagation(); addProduct({{ $subcategory->id }})">
                                            <i class="fa-solid fa-plus-circle"></i>
                                        </button>
                                        <button class="btn-action" title="Edit" onclick="event.stopPropagation(); editSubcategory({{ $subcategory->id }}, '{{ addslashes($subcategory->subcategory_name) }}', '{{ addslashes($subcategory->description) }}', {{ $subcategory->category_id }})">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <span class="toggle-icon">
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="item-children">
                                    @foreach($subcategory->productExamples as $product)
                                    <div class="taxonomy-item level-2" data-id="{{ $product->id }}">
                                        <div class="item-header">
                                            <div class="item-info">
                                                <div class="item-icon">
                                                    <i class="fa-solid fa-box"></i>
                                                </div>
                                                <div class="item-content">
                                                    <h6 class="item-title">{{ $product->product_name }}
                                                        <span class="badge bg-secondary">Order: {{ $product->display_order }}</span>
                                                        @if(!$product->is_active)
                                                        <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </h6>
                                                    @if($product->description)
                                                    <p class="item-desc">{{ $product->description }}</p>
                                                    @endif
                                                    <div class="item-meta">
                                                        <span class="badge bg-light text-dark">
                                                            <i class="fa-solid fa-hashtag me-1"></i>
                                                            ID: {{ $product->id }}
                                                        </span>
                                                        <span class="badge bg-info">
                                                            <i class="fa-solid fa-sort-numeric-up me-1"></i>
                                                            Display Order: {{ $product->display_order }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="item-actions">
                                                <button class="btn-action" title="Edit" onclick="editProduct({{ $product->id }}, '{{ addslashes($product->product_name) }}', '{{ addslashes($product->description) }}', {{ $product->subcategory_id }})">
                                                    <i class="fa-solid fa-edit"></i>
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
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<!-- Add Category with Subcategories and Products Modal -->
<div class="modal fade" id="addCategoryFullModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-plus-circle me-2"></i>Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryFullForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Main Category Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Category Name *</label>
                                        <input type="text" class="form-control" name="category_name" id="full_category_name" required placeholder="e.g., Fresh Fruits">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" id="full_description" rows="2" placeholder="Brief description of this category"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6>Sub-Category Examples *</h6>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addSubcategoryField()">
                                        <i class="fa-solid fa-plus"></i> Add Subcategory
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="subcategories-container">
                                        <!-- Subcategory fields will be added here -->
                                        <div class="subcategory-item mb-4 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Subcategory #1</h6>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeSubcategory(this)" data-index="0">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Subcategory Name *</label>
                                                    <input type="text" class="form-control subcategory-name" name="subcategories[0][name]" required placeholder="e.g., Tropical Fruits">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control subcategory-desc" name="subcategories[0][description]" rows="1" placeholder="Description"></textarea>
                                                </div>
                                            </div>

                                            <div class="products-section">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">Specific Products * (Minimum 2 required)</h6>
                                                    <button type="button" class="btn btn-sm btn-success" onclick="addProductField(this, 0)">
                                                        <i class="fa-solid fa-plus"></i> Add Product
                                                    </button>
                                                </div>
                                                <div class="products-container" data-index="0">
                                                    <div class="product-item mb-3 p-2 border">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Product Name *</label>
                                                                <input type="text" class="form-control product-name" name="subcategories[0][products][0][name]" required placeholder="e.g., TJC Mango">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Description</label>
                                                                <textarea class="form-control product-desc" name="subcategories[0][products][0][description]" rows="1" placeholder="Product description"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="product-item mb-3 p-2 border">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Product Name *</label>
                                                                <input type="text" class="form-control product-name" name="subcategories[0][products][1][name]" required placeholder="e.g., Ambarella">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Description</label>
                                                                <textarea class="form-control product-desc" name="subcategories[0][products][1][description]" rows="1" placeholder="Product description"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-save me-2"></i>Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// Toastr configuration
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "3000"
};

// Show loading
function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

// Hide loading
function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

// Toggle item function
function toggleItem(element) {
    const parent = element.closest('.taxonomy-item');
    const children = parent.querySelector('.item-children');
    const toggleIcon = parent.querySelector('.toggle-icon i');

    if (children) {
        children.classList.toggle('show');
        toggleIcon.classList.toggle('fa-chevron-down');
        toggleIcon.classList.toggle('fa-chevron-up');

        if (children.classList.contains('show')) {
            parent.style.marginBottom = '1rem';
        } else {
            parent.style.marginBottom = '0.5rem';
        }
    }
}

// Collapse all
document.getElementById('collapseAll').addEventListener('click', function() {
    document.querySelectorAll('.item-children.show').forEach(child => {
        child.classList.remove('show');
        const toggleIcon = child.closest('.taxonomy-item').querySelector('.toggle-icon i');
        if (toggleIcon) {
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.add('fa-chevron-down');
        }
    });
});

// Expand all
document.getElementById('expandAll').addEventListener('click', function() {
    document.querySelectorAll('.item-children:not(.show)').forEach(child => {
        child.classList.add('show');
        const toggleIcon = child.closest('.taxonomy-item').querySelector('.toggle-icon i');
        if (toggleIcon) {
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
        }
    });
});

// Add subcategory
function addSubcategory(categoryId) {
    Swal.fire({
        title: 'Add Subcategory',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Subcategory Name *</label>
                    <input type="text" class="form-control swal2-input" id="subcategoryName" placeholder="e.g., Tropical Fruits" required>
                    <div class="form-text">Display order will be automatically assigned.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control swal2-textarea" id="subcategoryDesc" rows="3" placeholder="Description of this subcategory"></textarea>
                </div>
                <input type="hidden" id="categoryId" value="${categoryId}">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add Subcategory',
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6b7280',
        background: '#ffffff',
        color: '#0f1724',
        width: '500px',
        preConfirm: () => {
            const name = document.getElementById('subcategoryName').value;
            if (!name) {
                Swal.showValidationMessage('Subcategory name is required');
                return false;
            }
            return {
                name: name,
                description: document.getElementById('subcategoryDesc').value,
                categoryId: document.getElementById('categoryId').value
            };
        }
    }).then(result => {
        if (result.isConfirmed) {
            const data = result.value;
            showLoading();

            fetch('{{ route("facilitator.taxonomy.subcategory.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccess(data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showError(data.message || 'Failed to add subcategory');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError('Error adding subcategory: ' + error.message);
            });
        }
    });
}

// Add product
function addProduct(subcategoryId) {
    Swal.fire({
        title: 'Add Product Example',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Product Name *</label>
                    <input type="text" class="form-control swal2-input" id="productName" placeholder="e.g., TJC Mango" required>
                    <div class="form-text">Display order will be automatically assigned.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control swal2-textarea" id="productDesc" rows="3" placeholder="Description of this product"></textarea>
                </div>
                <input type="hidden" id="subcategoryId" value="${subcategoryId}">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add Product',
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6b7280',
        background: '#ffffff',
        color: '#0f1724',
        width: '500px',
        preConfirm: () => {
            const name = document.getElementById('productName').value;
            if (!name) {
                Swal.showValidationMessage('Product name is required');
                return false;
            }
            return {
                name: name,
                description: document.getElementById('productDesc').value,
                subcategoryId: document.getElementById('subcategoryId').value
            };
        }
    }).then(result => {
        if (result.isConfirmed) {
            const data = result.value;
            showLoading();

            fetch('{{ route("facilitator.taxonomy.product.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccess(data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showError(data.message || 'Failed to add product');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError('Error adding product: ' + error.message);
            });
        }
    });
}

// Edit category
function editCategory(id, name, description) {
    Swal.fire({
        title: 'Edit Category',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Category Name *</label>
                    <input type="text" class="form-control swal2-input" id="editCategoryName" value="${name}" required>
                    <div class="form-text">Only name and description can be edited.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control swal2-textarea" id="editCategoryDesc" rows="3">${description || ''}</textarea>
                </div>
                <input type="hidden" id="categoryId" value="${id}">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update Category',
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6b7280',
        background: '#ffffff',
        color: '#0f1724',
        width: '500px',
        preConfirm: () => {
            const name = document.getElementById('editCategoryName').value;
            if (!name) {
                Swal.showValidationMessage('Category name is required');
                return false;
            }
            return {
                id: document.getElementById('categoryId').value,
                name: name,
                description: document.getElementById('editCategoryDesc').value
            };
        }
    }).then(result => {
        if (result.isConfirmed) {
            const data = result.value;
            showLoading();

            fetch('{{ route("facilitator.taxonomy.category.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccess(data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showError(data.message || 'Failed to update category');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError('Error updating category: ' + error.message);
            });
        }
    });
}

// Edit subcategory
function editSubcategory(id, name, description, categoryId) {
    Swal.fire({
        title: 'Edit Subcategory',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Subcategory Name *</label>
                    <input type="text" class="form-control swal2-input" id="editSubcategoryName" value="${name}" required>
                    <div class="form-text">Only name and description can be edited.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control swal2-textarea" id="editSubcategoryDesc" rows="3">${description || ''}</textarea>
                </div>
                <input type="hidden" id="subcategoryId" value="${id}">
                <input type="hidden" id="editCategoryId" value="${categoryId}">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update Subcategory',
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6b7280',
        background: '#ffffff',
        color: '#0f1724',
        width: '500px',
        preConfirm: () => {
            const name = document.getElementById('editSubcategoryName').value;
            if (!name) {
                Swal.showValidationMessage('Subcategory name is required');
                return false;
            }
            return {
                id: document.getElementById('subcategoryId').value,
                name: name,
                description: document.getElementById('editSubcategoryDesc').value,
                category_id: document.getElementById('editCategoryId').value
            };
        }
    }).then(result => {
        if (result.isConfirmed) {
            const data = result.value;
            showLoading();

            fetch('{{ route("facilitator.taxonomy.subcategory.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccess(data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showError(data.message || 'Failed to update subcategory');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError('Error updating subcategory: ' + error.message);
            });
        }
    });
}

// Edit product
function editProduct(id, name, description, subcategoryId) {
    Swal.fire({
        title: 'Edit Product',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Product Name *</label>
                    <input type="text" class="form-control swal2-input" id="editProductName" value="${name}" required>
                    <div class="form-text">Only name and description can be edited.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control swal2-textarea" id="editProductDesc" rows="3">${description || ''}</textarea>
                </div>
                <input type="hidden" id="productId" value="${id}">
                <input type="hidden" id="editSubcategoryId" value="${subcategoryId}">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update Product',
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6b7280',
        background: '#ffffff',
        color: '#0f1724',
        width: '500px',
        preConfirm: () => {
            const name = document.getElementById('editProductName').value;
            if (!name) {
                Swal.showValidationMessage('Product name is required');
                return false;
            }
            return {
                id: document.getElementById('productId').value,
                name: name,
                description: document.getElementById('editProductDesc').value,
                subcategory_id: document.getElementById('editSubcategoryId').value
            };
        }
    }).then(result => {
        if (result.isConfirmed) {
            const data = result.value;
            showLoading();

            fetch('{{ route("facilitator.taxonomy.product.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccess(data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showError(data.message || 'Failed to update product');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError('Error updating product: ' + error.message);
            });
        }
    });
}

// Utility functions
function showSuccess(message) {
    toastr.success(message);
}

function showError(message) {
    toastr.error(message);
}

// Handle form submissions - FIXED
document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    showLoading();

    fetch(this.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        if (data.success) {
            showSuccess(data.message);
            // Close modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
            if (modal) {
                modal.hide();
            }

            // Clear form
            document.getElementById('addCategoryForm').reset();

            // Reload page after delay
            setTimeout(() => location.reload(), 1500);
        } else {
            showError(data.message || 'Failed to add category');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showError('Error adding category: ' + error.message);
    });
});

// ========== DYNAMIC FIELDS FUNCTIONS ==========

let subcategoryCount = 1;
let productCounts = {0: 2}; // Track product counts per subcategory

function addSubcategoryField() {
    const container = document.getElementById('subcategories-container');
    const newIndex = subcategoryCount++;
    productCounts[newIndex] = 0; // Initialize product count for this subcategory

    const subcategoryHTML = `
        <div class="subcategory-item mb-4 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Subcategory #${newIndex + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSubcategory(this)" data-index="${newIndex}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Subcategory Name *</label>
                    <input type="text" class="form-control subcategory-name" name="subcategories[${newIndex}][name]" required placeholder="e.g., Tropical Fruits">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control subcategory-desc" name="subcategories[${newIndex}][description]" rows="1" placeholder="Description"></textarea>
                </div>
            </div>

            <div class="products-section">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Specific Products * (Minimum 2 required)</h6>
                    <button type="button" class="btn btn-sm btn-success" onclick="addProductField(this, ${newIndex})">
                        <i class="fa-solid fa-plus"></i> Add Product
                    </button>
                </div>
                <div class="products-container" data-index="${newIndex}">
                    <!-- Products will be added here -->
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', subcategoryHTML);

    // Add two initial products for the new subcategory
    const productsContainer = container.querySelector(`.products-container[data-index="${newIndex}"]`);
    addProductField({closest: () => productsContainer}, newIndex);
    addProductField({closest: () => productsContainer}, newIndex);
}

function addProductField(button, subcategoryIndex) {
    const productsContainer = button.closest('.products-section').querySelector('.products-container');
    const productIndex = productCounts[subcategoryIndex] || 0;
    productCounts[subcategoryIndex] = productIndex + 1;

    const productHTML = `
        <div class="product-item mb-3 p-2 border">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label">Product Name *</label>
                    <input type="text" class="form-control product-name" name="subcategories[${subcategoryIndex}][products][${productIndex}][name]" required placeholder="e.g., Product Name">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Description</label>
                    <textarea class="form-control product-desc" name="subcategories[${subcategoryIndex}][products][${productIndex}][description]" rows="1" placeholder="Product description"></textarea>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-danger mt-3" onclick="removeProduct(this)">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    productsContainer.insertAdjacentHTML('beforeend', productHTML);
}

function removeSubcategory(button) {
    const subcategoryItem = button.closest('.subcategory-item');
    const index = parseInt(button.getAttribute('data-index'));

    if (document.querySelectorAll('.subcategory-item').length <= 1) {
        Swal.fire('Error', 'At least one subcategory is required!', 'error');
        return;
    }

    subcategoryItem.remove();
    delete productCounts[index];

    // Renumber remaining subcategories
    const subcategories = document.querySelectorAll('.subcategory-item');
    subcategories.forEach((item, idx) => {
        const header = item.querySelector('h6');
        header.textContent = `Subcategory #${idx + 1}`;
    });
}

function removeProduct(button) {
    const productItem = button.closest('.product-item');
    const productsContainer = productItem.closest('.products-container');
    const subcategoryIndex = parseInt(productsContainer.getAttribute('data-index'));

    if (productsContainer.querySelectorAll('.product-item').length <= 2) {
        Swal.fire('Error', 'At least two products are required for each subcategory!', 'error');
        return;
    }

    productItem.remove();
}

// Form submission handler for full form
document.getElementById('addCategoryFullForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Validate at least one subcategory
    const subcategoryItems = document.querySelectorAll('.subcategory-item');
    if (subcategoryItems.length === 0) {
        Swal.fire('Error', 'At least one subcategory is required!', 'error');
        return;
    }

    // Validate each subcategory has at least 2 products
    let isValid = true;
    subcategoryItems.forEach((item, index) => {
        const productCount = item.querySelectorAll('.product-item').length;
        if (productCount < 2) {
            isValid = false;
            Swal.fire('Error', `Subcategory #${index + 1} must have at least 2 products!`, 'error');
            return;
        }
    });

    if (!isValid) return;

    // Collect form data
    const formData = new FormData(this);
    const data = {};

    // Convert FormData to JSON
    for (let [key, value] of formData.entries()) {
        // Handle nested arrays
        if (key.includes('[') && key.includes(']')) {
            const keys = key.split(/\[|\]/).filter(k => k);
            let current = data;

            for (let i = 0; i < keys.length - 1; i++) {
                const k = keys[i];
                const nextKey = keys[i + 1];

                if (!current[k]) {
                    current[k] = isNaN(parseInt(nextKey)) ? {} : [];
                }
                current = current[k];
            }

            current[keys[keys.length - 1]] = value;
        } else {
            data[key] = value;
        }
    }

    // Send request
    showLoading();

    fetch('{{ route("facilitator.taxonomy.category.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        if (data.success) {
            showSuccess(data.message);
            // Close modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryFullModal'));
            if (modal) {
                modal.hide();
            }

            // Clear form
            document.getElementById('addCategoryFullForm').reset();
            document.getElementById('subcategories-container').innerHTML = '';

            // Reset counters
            subcategoryCount = 1;
            productCounts = {0: 2};

            // Reload page after delay
            setTimeout(() => location.reload(), 1500);
        } else {
            showError(data.message || 'Failed to add category');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showError('Error adding category: ' + error.message);
    });
});

// Initialize first subcategory and products when modal opens
document.getElementById('addCategoryFullModal').addEventListener('show.bs.modal', function() {
    // Clear any existing content
    document.getElementById('subcategories-container').innerHTML = '';

    // Reset counters
    subcategoryCount = 1;
    productCounts = {0: 2};

    // Add first subcategory
    addSubcategoryField();
});
</script>
@endsection

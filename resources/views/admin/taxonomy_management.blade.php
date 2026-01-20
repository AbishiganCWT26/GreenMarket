@extends('admin.layouts.admin_master')

@section('title', 'Product Taxonomy Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/taxonomy-manager.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .category-icon {
        width: 24px;
        height: 24px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 8px;
        vertical-align: middle;
    }

    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }

    .file-input-label {
        display: inline-block;
        padding: 8px 15px;
        background: #f3f4f6;
        color: #374151;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.9rem;
        border: 1px solid #d1d5db;
    }

    .file-input-label:hover {
        background: #e5e7eb;
    }

    .hierarchy-indicator {
        display: inline-block;
        width: 20px;
        height: 20px;
        line-height: 20px;
        text-align: center;
        background: #f3f4f6;
        border-radius: 4px;
        margin-right: 8px;
        font-size: 0.8rem;
        color: var(--muted);
    }

    .badge-count {
        background: rgba(59, 130, 246, 0.1);
        color: var(--blue);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 500;
        margin-left: 5px;
    }
</style>
@endsection

@section('content')
<div class="taxonomy-manager">
    <div class="taxonomy-header">
        <h1><i class="fas fa-sitemap"></i> Product Taxonomy Management</h1>
        <p>Manage the complete hierarchical structure of product categories</p>
    </div>

    <div class="search-container">
        <input type="text"
               id="globalSearch"
               class="search-input"
               placeholder="Search categories, subcategories, or products...">
        <i class="fas fa-search search-icon"></i>
    </div>

    <!-- Main Categories Table -->
    <div class="table-container">
        <div class="table-header">
            <h3><i class="fas fa-layer-group"></i> Main Categories</h3>
            <div class="table-actions">
                <button class="action-btn btn-add" onclick="openAddForm('main')">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th style="width: 80px;">Sub-Cats</th>
                        <th style="width: 80px;">Products</th>
                        <th style="width: 80px;">Order</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="mainCategoriesList">
                    <tr>
                        <td colspan="8" class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Loading categories...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sub-Categories Table -->
    <div class="table-container">
        <div class="table-header">
            <h3><i class="fas fa-folder"></i> Sub-Categories</h3>
            <div class="table-actions">
                <button class="action-btn btn-add" onclick="openAddForm('sub')">
                    <i class="fas fa-plus"></i> Add Sub-Category
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Sub-Category</th>
                        <th>Main Category</th>
                        <th>Description</th>
                        <th style="width: 80px;">Products</th>
                        <th style="width: 80px;">Order</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="subCategoriesList">
                    <tr>
                        <td colspan="8" class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Loading sub-categories...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Specific Products Table -->
    <div class="table-container">
        <div class="table-header">
            <h3><i class="fas fa-seedling"></i> Specific Products</h3>
            <div class="table-actions">
                <button class="action-btn btn-add" onclick="openAddForm('product')">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Product</th>
                        <th>Sub-Category</th>
                        <th>Main Category</th>
                        <th>Description</th>
                        <th style="width: 80px;">Order</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="productsList">
                    <tr>
                        <td colspan="8" class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Loading products...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-container" id="addFormContainer" style="display: none;">
        <div id="formContent"></div>
    </div>
</div>

<div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="editFormContent"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let categories = [];
    let subcategories = [];
    let products = [];
    let searchTerm = '';

    document.addEventListener('DOMContentLoaded', function() {
        loadAllData();

        document.getElementById('globalSearch').addEventListener('input', function(e) {
            searchTerm = e.target.value.toLowerCase();
            filterData();
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('close') || e.target.id === 'editModal') {
                closeModal();
            }
        });
    });

    function loadAllData() {
        Promise.all([
            fetchData('{{ route("admin.taxonomy.categories.data") }}'),
            fetchData('{{ route("admin.taxonomy.subcategories.data") }}'),
            fetchData('{{ route("admin.taxonomy.products.data") }}')
        ]).then(([cats, subs, prods]) => {
            categories = cats;
            subcategories = subs;
            products = prods;

            renderCategories();
            renderSubcategories();
            renderProducts();
        }).catch(error => {
            console.error('Error loading data:', error);
            Swal.fire('Error', 'Failed to load taxonomy data', 'error');
        });
    }

    function fetchData(url) {
        return fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .catch(error => {
                console.error(`Error fetching ${url}:`, error);
                return [];
            });
    }

    function filterData() {
        if (!searchTerm.trim()) {
            renderCategories();
            renderSubcategories();
            renderProducts();
            return;
        }

        const filteredCats = categories.filter(cat =>
            cat.category_name.toLowerCase().includes(searchTerm) ||
            (cat.description && cat.description.toLowerCase().includes(searchTerm))
        );

        const filteredSubs = subcategories.filter(sub =>
            sub.subcategory_name.toLowerCase().includes(searchTerm) ||
            sub.category_name.toLowerCase().includes(searchTerm) ||
            (sub.description && sub.description.toLowerCase().includes(searchTerm))
        );

        const filteredProds = products.filter(prod =>
            prod.product_name.toLowerCase().includes(searchTerm) ||
            prod.subcategory_name.toLowerCase().includes(searchTerm) ||
            prod.category_name.toLowerCase().includes(searchTerm) ||
            (prod.description && prod.description.toLowerCase().includes(searchTerm))
        );

        renderCategories(filteredCats);
        renderSubcategories(filteredSubs);
        renderProducts(filteredProds);
    }

    function renderCategories(data = categories) {
        const container = document.getElementById('mainCategoriesList');

        if (data.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>No main categories found</p>
                        <button class="action-btn btn-add" onclick="openAddForm('main')">
                            <i class="fas fa-plus"></i> Add First Category
                        </button>
                    </td>
                </tr>
            `;
            return;
        }

        container.innerHTML = data.map((cat, index) => {
            const productCount = cat.product_count || 0;
            const subcategoryCount = cat.subcategory_count || 0;
            const iconUrl = cat.icon_filename ?
                `/assets/images/taxonomy-icons/${cat.icon_filename}` :
                null;

            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <div style="display: flex; align-items: center;">
                            ${iconUrl ? `
                                <img src="${iconUrl}" alt="${cat.category_name}" class="category-icon"
                                     onerror="this.style.display='none';">
                            ` : '<i class="fas fa-folder-open" style="color: var(--primary-green); margin-right: 8px;"></i>'}
                            <span style="font-weight: 500;">${cat.category_name}</span>
                        </div>
                    </td>
                    <td class="description-cell">${cat.description || '-'}</td>
                    <td><span class="badge-count">${subcategoryCount}</span></td>
                    <td><span class="badge-count">${productCount}</span></td>
                    <td>${cat.display_order}</td>
                    <td><span class="status-badge ${cat.is_active ? 'status-active' : 'status-inactive'}">
                        ${cat.is_active ? 'Active' : 'Inactive'}
                    </span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-edit" onclick="editItem('category', ${cat.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn btn-delete" onclick="deleteItem('category', ${cat.id})"
                                    ${productCount > 0 ? 'disabled title="Cannot delete: Has products"' : 'title="Delete"'}>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function renderSubcategories(data = subcategories) {
        const container = document.getElementById('subCategoriesList');

        if (data.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="fas fa-folder"></i>
                        <p>No sub-categories found</p>
                        <button class="action-btn btn-add" onclick="openAddForm('sub')">
                            <i class="fas fa-plus"></i> Add First Sub-Category
                        </button>
                    </td>
                </tr>
            `;
            return;
        }

        container.innerHTML = data.map((sub, index) => {
            const productCount = sub.product_count || 0;
            const productExampleCount = sub.product_example_count || 0;

            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-folder" style="color: var(--blue); margin-right: 8px;"></i>
                            <span style="font-weight: 500;">${sub.subcategory_name}</span>
                        </div>
                    </td>
                    <td>${sub.category_name}</td>
                    <td class="description-cell">${sub.description || '-'}</td>
                    <td><span class="badge-count">${productExampleCount}</span></td>
                    <td>${sub.display_order}</td>
                    <td><span class="status-badge ${sub.is_active ? 'status-active' : 'status-inactive'}">
                        ${sub.is_active ? 'Active' : 'Inactive'}
                    </span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-edit" onclick="editItem('subcategory', ${sub.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn btn-delete" onclick="deleteItem('subcategory', ${sub.id})"
                                    ${productCount > 0 ? 'disabled title="Cannot delete: Has products"' : 'title="Delete"'}>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function renderProducts(data = products) {
        const container = document.getElementById('productsList');

        if (data.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="fas fa-seedling"></i>
                        <p>No specific products found</p>
                        <button class="action-btn btn-add" onclick="openAddForm('product')">
                            <i class="fas fa-plus"></i> Add First Product
                        </button>
                    </td>
                </tr>
            `;
            return;
        }

        container.innerHTML = data.map((prod, index) => {
            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-seedling" style="color: var(--purple); margin-right: 8px;"></i>
                            <span style="font-weight: 500;">${prod.product_name}</span>
                        </div>
                    </td>
                    <td>${prod.subcategory_name}</td>
                    <td>${prod.category_name}</td>
                    <td class="description-cell">${prod.description || '-'}</td>
                    <td>${prod.display_order}</td>
                    <td><span class="status-badge ${prod.is_active ? 'status-active' : 'status-inactive'}">
                        ${prod.is_active ? 'Active' : 'Inactive'}
                    </span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-edit" onclick="editItem('product', ${prod.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn btn-delete" onclick="deleteItem('product', ${prod.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function openAddForm(type) {
        let formHTML = '';
        const formContainer = document.getElementById('addFormContainer');
        const formContent = document.getElementById('formContent');

        switch(type) {
            case 'main':
                formHTML = getMainCategoryForm();
                break;
            case 'sub':
                formHTML = getSubCategoryForm();
                break;
            case 'product':
                formHTML = getProductForm();
                break;
        }

        formContent.innerHTML = formHTML;
        formContainer.style.display = 'block';
        formContainer.scrollIntoView({ behavior: 'smooth' });

        if (type === 'sub' || type === 'product') {
            loadParentSelects();
        }
    }

    function getMainCategoryForm() {
        return `
            <h3><i class="fas fa-plus-circle"></i> Add Main Category</h3>

            <div class="rule-highlight">
                <div class="rule-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Category Creation Rules
                </div>
                <div class="requirements-list">
                    <div class="requirement-item">
                        <i class="fas fa-check-circle requirement-icon"></i>
                        Must provide at least 1 Sub-Category
                    </div>
                    <div class="requirement-item">
                        <i class="fas fa-check-circle requirement-icon"></i>
                        Must provide at least 2 Specific Products across subcategories
                    </div>
                    <div class="requirement-item">
                        <i class="fas fa-check-circle requirement-icon"></i>
                        Cannot create empty main category
                    </div>
                </div>
            </div>

            <form id="mainCategoryForm" onsubmit="saveMainCategory(event)" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Category Name *</label>
                    <input type="text" class="form-input" name="category_name" required
                           placeholder="e.g., Fresh Fruit, Fresh Vegetables">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" name="description"
                              placeholder="Brief description of this category" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Category Icon Image (PNG only, max 2MB)</label>
                    <div class="image-upload-area" id="imageUploadArea" onclick="document.getElementById('categoryImage').click()">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">Click to upload or drag and drop</div>
                        <div class="upload-text" style="font-size: 0.8rem; color: #9ca3af;">
                            PNG format only, max 2MB
                        </div>
                    </div>
                    <input type="file" id="categoryImage" name="image" accept=".png" style="display: none;"
                           onchange="previewImage(this, 'categoryPreview')">
                    <div id="categoryPreview" class="image-preview-container" style="display: none;">
                        <img class="preview-image" id="previewCategoryImage" src="" alt="Preview">
                        <div class="preview-info">
                            <div style="font-weight: 500; font-size: 0.9rem;" id="previewFileName"></div>
                            <div style="font-size: 0.8rem; color: var(--muted);" id="previewFileSize"></div>
                        </div>
                        <div class="remove-image" onclick="removeImage('categoryImage', 'categoryPreview')">
                            <i class="fas fa-times"></i> Remove
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Display Order</label>
                    <input type="number" class="form-input" name="display_order" value="0" min="0">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-sitemap"></i> Sub-Categories (Minimum: 1)
                    </label>
                    <div id="subcategoriesContainer">
                        <div class="subcategory-item">
                            <input type="text" class="form-input" name="subcategories[]"
                                   placeholder="Sub-category name (e.g., Tropical, Citrus)" required>
                            <button type="button" class="action-btn btn-delete"
                                    onclick="removeSubcategory(this)" style="margin-top: 5px;">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    </div>
                    <button type="button" class="action-btn btn-add" onclick="addSubcategory()" style="margin-top: 5px;">
                        <i class="fas fa-plus"></i> Add Another Sub-Category
                    </button>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-seedling"></i> Specific Products (Minimum: 2)
                    </label>
                    <div id="productsContainer">
                        <div class="product-item">
                            <input type="text" class="form-input" name="products[0][name]"
                                   placeholder="Product name (e.g., TJC Mango)" required>
                            <select class="form-select" name="products[0][subcategory_index]" style="margin-top: 5px;" required>
                                <option value="">Select Sub-Category</option>
                            </select>
                            <button type="button" class="action-btn btn-delete"
                                    onclick="removeProduct(this)" style="margin-top: 5px;">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                        <div class="product-item">
                            <input type="text" class="form-input" name="products[1][name]"
                                   placeholder="Another product name" required>
                            <select class="form-select" name="products[1][subcategory_index]" style="margin-top: 5px;" required>
                                <option value="">Select Sub-Category</option>
                            </select>
                            <button type="button" class="action-btn btn-delete"
                                    onclick="removeProduct(this)" style="margin-top: 5px;">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    </div>
                    <button type="button" class="action-btn btn-add" onclick="addProduct()" style="margin-top: 5px;">
                        <i class="fas fa-plus"></i> Add Another Product
                    </button>
                </div>

                <div class="step-buttons">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Category
                    </button>
                    <button type="button" class="btn-secondary" onclick="closeForm()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        `;
    }

    function previewImage(input, previewContainerId) {
        const file = input.files[0];
        if (!file) return;

        if (file.type !== 'image/png') {
            Swal.fire('Error', 'Only PNG images are allowed', 'error');
            input.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('Error', 'Image size should be less than 2MB', 'error');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewCategoryImage');
            const container = document.getElementById(previewContainerId);
            const fileName = document.getElementById('previewFileName');
            const fileSize = document.getElementById('previewFileSize');

            preview.src = e.target.result;
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            container.style.display = 'flex';
        }
        reader.readAsDataURL(file);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function removeImage(inputId, previewContainerId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewContainerId).style.display = 'none';
    }

    function addSubcategory() {
        const container = document.getElementById('subcategoriesContainer');
        const index = container.children.length;
        const div = document.createElement('div');
        div.className = 'subcategory-item';
        div.style.marginBottom = '10px';
        div.innerHTML = `
            <input type="text" class="form-input" name="subcategories[]"
                   placeholder="Sub-category name" required>
            <button type="button" class="action-btn btn-delete"
                    onclick="removeSubcategory(this)" style="margin-top: 5px;">
                <i class="fas fa-times"></i> Remove
            </button>
        `;
        container.appendChild(div);
    }

    function removeSubcategory(button) {
        const container = document.getElementById('subcategoriesContainer');
        if (container.children.length > 1) {
            button.parentElement.remove();
            updateProductSubcategorySelects();
        } else {
            Swal.fire('Warning', 'You must have at least one sub-category', 'warning');
        }
    }

    function addProduct() {
        const container = document.getElementById('productsContainer');
        const index = container.children.length;
        const div = document.createElement('div');
        div.className = 'product-item';
        div.style.marginBottom = '10px';
        div.innerHTML = `
            <input type="text" class="form-input" name="products[${index}][name]"
                   placeholder="Product name" required>
            <select class="form-select" name="products[${index}][subcategory_index]" style="margin-top: 5px;" required>
                <option value="">Select Sub-Category</option>
            </select>
            <button type="button" class="action-btn btn-delete"
                    onclick="removeProduct(this)" style="margin-top: 5px;">
                <i class="fas fa-times"></i> Remove
            </button>
        `;
        container.appendChild(div);
        updateProductSubcategorySelects();
    }

    function removeProduct(button) {
        const container = document.getElementById('productsContainer');
        if (container.children.length > 2) {
            button.parentElement.remove();
            reindexProductForms();
        } else {
            Swal.fire('Warning', 'You must have at least two products', 'warning');
        }
    }

    function reindexProductForms() {
        const container = document.getElementById('productsContainer');
        const items = container.querySelectorAll('.product-item');
        items.forEach((item, index) => {
            const nameInput = item.querySelector('input[type="text"]');
            const select = item.querySelector('select');
            const deleteBtn = item.querySelector('.action-btn');

            nameInput.name = `products[${index}][name]`;
            select.name = `products[${index}][subcategory_index]`;
        });
    }

    function updateProductSubcategorySelects() {
        const selects = document.querySelectorAll('select[name^="products["]');
        const subcatInputs = document.querySelectorAll('input[name="subcategories[]"]');
        const subcatNames = Array.from(subcatInputs)
            .map(input => input.value.trim())
            .filter(name => name);

        selects.forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">Select Sub-Category</option>' +
                subcatNames.map((name, index) =>
                    `<option value="${index}" ${select.value == index ? 'selected' : ''}>${name}</option>`
                ).join('');
        });
    }

    document.addEventListener('input', function(e) {
        if (e.target.name === 'subcategories[]') {
            updateProductSubcategorySelects();
        }
    });

    async function saveMainCategory(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        const subcategories = Array.from(formData.getAll('subcategories[]')).filter(s => s.trim());
        const products = [];

        // Collect products data
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('products[') && key.endsWith('][name]')) {
                const index = key.match(/\[(\d+)\]/)[1];
                const productName = value;
                const subcategoryIndex = formData.get(`products[${index}][subcategory_index]`);

                if (productName.trim() && subcategoryIndex !== null) {
                    products.push({
                        name: productName.trim(),
                        subcategory_index: parseInt(subcategoryIndex)
                    });
                }
            }
        }

        if (subcategories.length < 1) {
            Swal.fire('Validation Error', 'Must have at least 1 sub-category', 'error');
            return;
        }

        if (products.length < 2) {
            Swal.fire('Validation Error', 'Must have at least 2 specific products', 'error');
            return;
        }

        if (products.some(p => p.subcategory_index === null || p.subcategory_index === undefined)) {
            Swal.fire('Validation Error', 'All products must be assigned to a sub-category', 'error');
            return;
        }

        const imageFile = formData.get('image');
        if (imageFile && imageFile.size > 0) {
            if (imageFile.type !== 'image/png') {
                Swal.fire('Error', 'Only PNG images are allowed', 'error');
                return;
            }
            if (imageFile.size > 2 * 1024 * 1024) {
                Swal.fire('Error', 'Image size should be less than 2MB', 'error');
                return;
            }
        }

        const data = new FormData();
        data.append('category_name', formData.get('category_name'));
        data.append('description', formData.get('description'));
        data.append('display_order', formData.get('display_order'));

        if (imageFile && imageFile.size > 0) {
            data.append('image', imageFile);
        }

        data.append('subcategories', JSON.stringify(subcategories));
        data.append('products', JSON.stringify(products));

        try {
            const response = await fetch('{{ route("admin.taxonomy.save.main") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: data
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Main category created successfully',
                    showConfirmButton: false,
                    timer: 2000
                });

                closeForm();
                loadAllData();
            } else {
                throw new Error(result.message || 'Failed to save category');
            }
        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        }
    }

    function getSubCategoryForm() {
        return `
            <h3><i class="fas fa-plus-circle"></i> Add Sub-Category</h3>

            <div class="rule-highlight">
                <div class="rule-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Sub-Category Creation Rules
                </div>
                <div class="requirements-list">
                    <div class="requirement-item">
                        <i class="fas fa-check-circle requirement-icon"></i>
                        Must be assigned to a Main Category
                    </div>
                    <div class="requirement-item">
                        <i class="fas fa-check-circle requirement-icon"></i>
                        Must provide at least 2 Specific Products
                    </div>
                    <div class="requirement-item">
                        <i class="fas fa-check-circle requirement-icon"></i>
                        Cannot create empty sub-category
                    </div>
                </div>
            </div>

            <form id="subCategoryForm" onsubmit="saveSubCategory(event)">
                <div class="form-group">
                    <label class="form-label">Main Category *</label>
                    <select class="form-select" name="category_id" required>
                        <option value="">Select Main Category</option>
                        ${categories.map(cat =>
                            `<option value="${cat.id}">${cat.category_name}</option>`
                        ).join('')}
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Sub-Category Name *</label>
                    <input type="text" class="form-input" name="subcategory_name" required
                           placeholder="e.g., Tropical, Leafy Greens">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" name="description"
                              placeholder="Brief description" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-seedling"></i> Specific Products (Minimum: 2)
                    </label>
                    <div id="subcatProductsContainer">
                        <div class="product-item">
                            <input type="text" class="form-input" name="products[]"
                                   placeholder="Product name (e.g., TJC Mango)" required>
                            <button type="button" class="action-btn btn-delete"
                                    onclick="removeSubcatProduct(this)" style="margin-top: 5px;">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                        <div class="product-item">
                            <input type="text" class="form-input" name="products[]"
                                   placeholder="Another product name" required>
                            <button type="button" class="action-btn btn-delete"
                                    onclick="removeSubcatProduct(this)" style="margin-top: 5px;">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    </div>
                    <button type="button" class="action-btn btn-add" onclick="addSubcatProduct()" style="margin-top: 5px;">
                        <i class="fas fa-plus"></i> Add Another Product
                    </button>
                </div>

                <div class="form-group">
                    <label class="form-label">Display Order</label>
                    <input type="number" class="form-input" name="display_order" value="0" min="0">
                </div>

                <div class="step-buttons">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Sub-Category
                    </button>
                    <button type="button" class="btn-secondary" onclick="closeForm()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        `;
    }

    function addSubcatProduct() {
        const container = document.getElementById('subcatProductsContainer');
        const div = document.createElement('div');
        div.className = 'product-item';
        div.style.marginBottom = '10px';
        div.innerHTML = `
            <input type="text" class="form-input" name="products[]"
                   placeholder="Product name" required>
            <button type="button" class="action-btn btn-delete"
                    onclick="removeSubcatProduct(this)" style="margin-top: 5px;">
                <i class="fas fa-times"></i> Remove
            </button>
        `;
        container.appendChild(div);
    }

    function removeSubcatProduct(button) {
        const container = document.getElementById('subcatProductsContainer');
        if (container.children.length > 2) {
            button.parentElement.remove();
        } else {
            Swal.fire('Warning', 'You must have at least two products', 'warning');
        }
    }

    async function saveSubCategory(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        const products = Array.from(formData.getAll('products[]')).filter(p => p.trim());

        if (products.length < 2) {
            Swal.fire('Validation Error', 'Must have at least 2 specific products', 'error');
            return;
        }

        const data = {
            category_id: formData.get('category_id'),
            subcategory_name: formData.get('subcategory_name'),
            description: formData.get('description'),
            display_order: formData.get('display_order'),
            products: products
        };

        try {
            const response = await fetch('{{ route("admin.taxonomy.save.subcategory") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Sub-category created successfully',
                    showConfirmButton: false,
                    timer: 2000
                });

                closeForm();
                loadAllData();
            } else {
                throw new Error(result.message || 'Failed to save sub-category');
            }
        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        }
    }

    function getProductForm() {
        return `
            <h3><i class="fas fa-plus-circle"></i> Add Specific Product</h3>

            <form id="productForm" onsubmit="saveProduct(event)">
                <div class="form-group">
                    <label class="form-label">Main Category *</label>
                    <select class="form-select" id="mainCategorySelect" required
                            onchange="loadSubcategoriesByCategory(this.value)">
                        <option value="">Select Main Category</option>
                        ${categories.map(cat =>
                            `<option value="${cat.id}">${cat.category_name}</option>`
                        ).join('')}
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Sub-Category *</label>
                    <select class="form-select" id="subCategorySelect" name="subcategory_id" required disabled>
                        <option value="">First select a Main Category</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" class="form-input" name="product_name" required
                           placeholder="e.g., TJC Mango, Woodapple Jam">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" name="description"
                              placeholder="Product details, specifications" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Display Order</label>
                    <input type="number" class="form-input" name="display_order" value="0" min="0">
                </div>

                <div class="step-buttons">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                    <button type="button" class="btn-secondary" onclick="closeForm()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        `;
    }

    async function loadSubcategoriesByCategory(categoryId) {
        const subSelect = document.getElementById('subCategorySelect');

        if (!categoryId) {
            subSelect.innerHTML = '<option value="">First select a Main Category</option>';
            subSelect.disabled = true;
            return;
        }

        try {
            const response = await fetch(`/admin/taxonomy/subcategories/${categoryId}`);
            const subcategories = await response.json();

            subSelect.innerHTML = '<option value="">Select Sub-Category</option>' +
                subcategories.map(sub =>
                    `<option value="${sub.id}">${sub.subcategory_name}</option>`
                ).join('');

            subSelect.disabled = false;
        } catch (error) {
            console.error('Error loading subcategories:', error);
            subSelect.innerHTML = '<option value="">Error loading subcategories</option>';
        }
    }

    async function saveProduct(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        const data = {
            subcategory_id: formData.get('subcategory_id'),
            product_name: formData.get('product_name'),
            description: formData.get('description'),
            display_order: formData.get('display_order')
        };

        try {
            const response = await fetch('{{ route("admin.taxonomy.save.product") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Product added successfully',
                    showConfirmButton: false,
                    timer: 2000
                });

                closeForm();
                loadAllData();
            } else {
                throw new Error(result.message || 'Failed to save product');
            }
        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        }
    }

    function closeForm() {
        document.getElementById('addFormContainer').style.display = 'none';
        document.getElementById('formContent').innerHTML = '';
    }

    async function editItem(type, id) {
        try {
            let url = '';
            let title = '';

            switch(type) {
                case 'category':
                    url = '{{ route("admin.taxonomy.edit.category", ":id") }}'.replace(':id', id);
                    title = 'Edit Main Category';
                    break;
                case 'subcategory':
                    url = '{{ route("admin.taxonomy.edit.subcategory", ":id") }}'.replace(':id', id);
                    title = 'Edit Sub-Category';
                    break;
                case 'product':
                    url = '{{ route("admin.taxonomy.edit.product", ":id") }}'.replace(':id', id);
                    title = 'Edit Product';
                    break;
            }

            const response = await fetch(url);
            const data = await response.json();

            let formHTML = '';

            if (type === 'category') {
                const iconUrl = data.icon_filename ?
                    `/assets/images/taxonomy-icons/${data.icon_filename}` : null;

                formHTML = `
                    <h3><i class="fas fa-edit"></i> ${title}</h3>
                    <form onsubmit="updateItem('${type}', ${id}, event)" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">Category Name</label>
                            <input type="text" class="form-input" name="category_name"
                                   value="${data.category_name}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea class="form-textarea" name="description" rows="3">${data.description || ''}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category Icon Image (PNG only)</label>
                            ${iconUrl ? `
                                <div class="image-preview-container">
                                    <img class="preview-image" src="${iconUrl}" alt="Current Icon">
                                    <div class="preview-info">
                                        <div style="font-weight: 500; font-size: 0.9rem;">Current Image</div>
                                        <div style="font-size: 0.8rem; color: var(--muted);">${data.icon_filename}</div>
                                    </div>
                                </div>
                            ` : ''}
                            <div class="image-upload-area" onclick="document.getElementById('editCategoryImage').click()">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="upload-text">Click to upload new image</div>
                                <div class="upload-text" style="font-size: 0.8rem; color: #9ca3af;">
                                    PNG format only, max 2MB
                                </div>
                            </div>
                            <input type="file" id="editCategoryImage" name="image" accept=".png" style="display: none;"
                                   onchange="previewEditImage(this, 'editCategoryPreview')">
                            <div id="editCategoryPreview" class="image-preview-container" style="display: none;">
                                <img class="preview-image" id="previewEditCategoryImage" src="" alt="Preview">
                                <div class="preview-info">
                                    <div style="font-weight: 500; font-size: 0.9rem;" id="previewEditFileName"></div>
                                    <div style="font-size: 0.8rem; color: var(--muted);" id="previewEditFileSize"></div>
                                </div>
                                <div class="remove-image" onclick="removeImage('editCategoryImage', 'editCategoryPreview')">
                                    <i class="fas fa-times"></i> Remove
                                </div>
                            </div>
                            <input type="hidden" name="current_image" value="${data.icon_filename || ''}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-input" name="display_order"
                                   value="${data.display_order}" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="is_active">
                                <option value="1" ${data.is_active ? 'selected' : ''}>Active</option>
                                <option value="0" ${!data.is_active ? 'selected' : ''}>Inactive</option>
                            </select>
                        </div>
                        <div class="step-buttons">
                            <button type="submit" class="btn-primary">Update</button>
                            <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                        </div>
                    </form>
                `;
            } else if (type === 'subcategory') {
                formHTML = `
                    <h3><i class="fas fa-edit"></i> ${title}</h3>
                    <form onsubmit="updateItem('${type}', ${id}, event)">
                        <div class="form-group">
                            <label class="form-label">Main Category</label>
                            <select class="form-select" name="category_id" required>
                                ${categories.map(cat => `
                                    <option value="${cat.id}" ${cat.id == data.category_id ? 'selected' : ''}>
                                        ${cat.category_name}
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sub-Category Name</label>
                            <input type="text" class="form-input" name="subcategory_name"
                                   value="${data.subcategory_name}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea class="form-textarea" name="description" rows="3">${data.description || ''}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-input" name="display_order"
                                   value="${data.display_order}" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="is_active">
                                <option value="1" ${data.is_active ? 'selected' : ''}>Active</option>
                                <option value="0" ${!data.is_active ? 'selected' : ''}>Inactive</option>
                            </select>
                        </div>
                        <div class="step-buttons">
                            <button type="submit" class="btn-primary">Update</button>
                            <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                        </div>
                    </form>
                `;
            } else if (type === 'product') {
                formHTML = `
                    <h3><i class="fas fa-edit"></i> ${title}</h3>
                    <form onsubmit="updateItem('${type}', ${id}, event)">
                        <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-input" name="product_name"
                                   value="${data.product_name}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea class="form-textarea" name="description" rows="3">${data.description || ''}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-input" name="display_order"
                                   value="${data.display_order}" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="is_active">
                                <option value="1" ${data.is_active ? 'selected' : ''}>Active</option>
                                <option value="0" ${!data.is_active ? 'selected' : ''}>Inactive</option>
                            </select>
                        </div>
                        <div class="step-buttons">
                            <button type="submit" class="btn-primary">Update</button>
                            <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                        </div>
                    </form>
                `;
            }

            document.getElementById('editFormContent').innerHTML = formHTML;
            document.getElementById('editModal').style.display = 'block';

        } catch (error) {
            console.error('Error editing item:', error);
            Swal.fire('Error', 'Failed to load item for editing', 'error');
        }
    }

    function previewEditImage(input, previewContainerId) {
        const file = input.files[0];
        if (!file) return;

        if (file.type !== 'image/png') {
            Swal.fire('Error', 'Only PNG images are allowed', 'error');
            input.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('Error', 'Image size should be less than 2MB', 'error');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewEditCategoryImage');
            const container = document.getElementById(previewContainerId);
            const fileName = document.getElementById('previewEditFileName');
            const fileSize = document.getElementById('previewEditFileSize');

            preview.src = e.target.result;
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            container.style.display = 'flex';
        }
        reader.readAsDataURL(file);
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
        document.getElementById('editFormContent').innerHTML = '';
    }

    async function updateItem(type, id, e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        try {
            let url = '';
            switch(type) {
                case 'category':
                    url = '{{ route("admin.taxonomy.update.category", ":id") }}'.replace(':id', id);
                    break;
                case 'subcategory':
                    url = '{{ route("admin.taxonomy.update.subcategory", ":id") }}'.replace(':id', id);
                    break;
                case 'product':
                    url = '{{ route("admin.taxonomy.update.product", ":id") }}'.replace(':id', id);
                    break;
            }

            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Item updated successfully',
                    showConfirmButton: false,
                    timer: 1500
                });

                closeModal();
                loadAllData();
            } else {
                throw new Error(result.message || 'Failed to update item');
            }
        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        }
    }

    async function deleteItem(type, id) {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: `This will delete the ${type} and all associated data!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        });

        if (!result.isConfirmed) return;

        try {
            let url = '';
            switch(type) {
                case 'category':
                    url = '{{ route("admin.taxonomy.delete.category", ":id") }}'.replace(':id', id);
                    break;
                case 'subcategory':
                    url = '{{ route("admin.taxonomy.delete.subcategory", ":id") }}'.replace(':id', id);
                    break;
                case 'product':
                    url = '{{ route("admin.taxonomy.delete.product", ":id") }}'.replace(':id', id);
                    break;
            }

            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Item has been deleted successfully',
                    showConfirmButton: false,
                    timer: 1500
                });

                loadAllData();
            } else {
                throw new Error(data.message || 'Failed to delete item');
            }
        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        }
    }

    function loadParentSelects() {
        updateProductSubcategorySelects();
    }

    // Initialize drag and drop for image upload
    document.addEventListener('DOMContentLoaded', function() {
        const imageUploadArea = document.getElementById('imageUploadArea');
        if (imageUploadArea) {
            imageUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            imageUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            imageUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const fileInput = document.getElementById('categoryImage');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            });
        }
    });
</script>
@endsection

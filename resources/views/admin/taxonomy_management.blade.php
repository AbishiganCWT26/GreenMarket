@extends('admin.layouts.admin_master')

@section('title', 'Product Taxonomy Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Admin/taxonomy-manager.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="taxonomy-manager">
	<div class="tx-header">
		<div class="tx-header-row">
			<div class="tx-header-icon"><i class="fas fa-sitemap"></i></div>
			<div>
				<h1>Product Taxonomy Management</h1>
				<p>Manage the complete hierarchical structure of product categories</p>
			</div>
		</div>
	</div>

	<div class="tx-search">
		<i class="fas fa-search tx-search-icon-left"></i>
		<input type="text" id="globalSearch" placeholder="Search categories, subcategories, or products...">
		<span class="tx-search-clear" id="globalSearchClear" onclick="clearGlobalSearch()"><i class="fas fa-times"></i></span>
	</div>

	<div class="tx-section">
		<div class="tx-section-head">
			<div class="tx-section-title"><i class="fas fa-layer-group"></i><span>Main Categories</span></div>
			<div class="tx-section-actions">
				<button class="tx-icon-btn view-toggle" id="categoryViewToggle" onclick="toggleView('category')" title="Switch view"><i class="fas fa-table"></i></button>
				<button class="tx-btn-add" onclick="openAddForm('main')"><i class="fas fa-plus"></i><span>Add</span></button>
			</div>
		</div>
		<div class="tx-section-body">
			<div class="tx-table-wrap is-active" id="categoryTableWrap">
				<table class="tx-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Category</th>
							<th>Description</th>
							<th>Subs</th>
							<th>Products</th>
							<th>Order</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody id="mainCategoriesList">
						<tr><td colspan="8" class="tx-state"><i class="fas fa-spinner fa-spin"></i>Loading categories...</td></tr>
					</tbody>
				</table>
			</div>
			<div class="tx-card-grid" id="categoryCardGrid"></div>
			<div class="tx-pagination hidden" id="categoryPagination"></div>
		</div>
	</div>

	<div class="tx-section">
		<div class="tx-section-head">
			<div class="tx-section-title"><i class="fas fa-folder"></i><span>Sub-Categories</span></div>
			<div class="tx-section-actions">
				<button class="tx-icon-btn view-toggle" id="subcategoryViewToggle" onclick="toggleView('subcategory')" title="Switch view"><i class="fas fa-table"></i></button>
				<button class="tx-btn-add" onclick="openAddForm('sub')"><i class="fas fa-plus"></i><span>Add</span></button>
			</div>
		</div>
		<div class="tx-section-body">
			<div class="tx-table-wrap is-active" id="subcategoryTableWrap">
				<table class="tx-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Sub-Category</th>
							<th>Main Category</th>
							<th>Description</th>
							<th>Products</th>
							<th>Order</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody id="subCategoriesList">
						<tr><td colspan="8" class="tx-state"><i class="fas fa-spinner fa-spin"></i>Loading sub-categories...</td></tr>
					</tbody>
				</table>
			</div>
			<div class="tx-card-grid" id="subcategoryCardGrid"></div>
			<div class="tx-pagination hidden" id="subcategoryPagination"></div>
		</div>
	</div>

	<div class="tx-section">
		<div class="tx-section-head">
			<div class="tx-section-title"><i class="fas fa-seedling"></i><span>Specific Products</span></div>
			<div class="tx-section-actions">
				<button class="tx-icon-btn view-toggle" id="productViewToggle" onclick="toggleView('product')" title="Switch view"><i class="fas fa-table"></i></button>
				<button class="tx-btn-add" onclick="openAddForm('product')"><i class="fas fa-plus"></i><span>Add</span></button>
			</div>
		</div>
		<div class="tx-section-body">
			<div class="tx-table-wrap is-active" id="productTableWrap">
				<table class="tx-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Product</th>
							<th>Sub-Category</th>
							<th>Main Category</th>
							<th>Description</th>
							<th>Order</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody id="productsList">
						<tr><td colspan="8" class="tx-state"><i class="fas fa-spinner fa-spin"></i>Loading products...</td></tr>
					</tbody>
				</table>
			</div>
			<div class="tx-card-grid" id="productCardGrid"></div>
			<div class="tx-pagination hidden" id="productPagination"></div>
		</div>
	</div>

	<div class="tx-form-panel" id="addFormContainer" style="display: none;">
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
@php
    $swalConfigData = [
        'editing1' => file_exists(public_path('assets/icons/Gif/editing1.gif')) ? ['imageUrl' => asset('assets/icons/Gif/editing1.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'error'],
        'error1' => file_exists(public_path('assets/icons/Gif/error1.gif')) ? ['imageUrl' => asset('assets/icons/Gif/error1.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'error'],
        'error2' => file_exists(public_path('assets/icons/Gif/error2.gif')) ? ['imageUrl' => asset('assets/icons/Gif/error2.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'error'],
        'error5' => file_exists(public_path('assets/icons/Gif/error5.gif')) ? ['imageUrl' => asset('assets/icons/Gif/error5.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'error'],
        'alert1_error' => file_exists(public_path('assets/icons/Gif/alert1.gif')) ? ['imageUrl' => asset('assets/icons/Gif/alert1.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'error'],
        'alert2_warning' => file_exists(public_path('assets/icons/Gif/alert2.gif')) ? ['imageUrl' => asset('assets/icons/Gif/alert2.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'warning'],
        'alert2_error' => file_exists(public_path('assets/icons/Gif/alert2.gif')) ? ['imageUrl' => asset('assets/icons/Gif/alert2.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'error'],
        'alert4_warning' => file_exists(public_path('assets/icons/Gif/alert4.gif')) ? ['imageUrl' => asset('assets/icons/Gif/alert4.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'warning'],
        'alert4_error' => file_exists(public_path('assets/icons/Gif/alert4.gif')) ? ['imageUrl' => asset('assets/icons/Gif/alert4.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'error'],
        'success4' => file_exists(public_path('assets/icons/Gif/success4.gif')) ? ['imageUrl' => asset('assets/icons/Gif/success4.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'success'],
        'success2' => file_exists(public_path('assets/icons/Gif/success2.gif')) ? ['imageUrl' => asset('assets/icons/Gif/success2.gif'), 'imageWidth' => 60, 'imageHeight' => 60] : ['icon' => 'success'],
    ];
@endphp
<script type="application/json" id="swal-config-data">
{!! json_encode($swalConfigData) !!}
</script>
<script>
    const swalConfig = JSON.parse(document.getElementById('swal-config-data').textContent);

    function getSwalIcon(key) {
        return swalConfig[key] || { icon: 'info' };
    }

	let categories = [];
	let subcategories = [];
	let products = [];
	let searchTerm = '';
	let displayedCategories = [];
	let displayedSubcategories = [];
	let displayedProducts = [];
	let categoryPage = 1;
	let subcategoryPage = 1;
	let productPage = 1;
	let categoryView = 'table';
	let subcategoryView = 'table';
	let productView = 'table';
	let resizeTimer = null;

	document.addEventListener('DOMContentLoaded', function() {
		loadAllData();

		document.getElementById('globalSearch').addEventListener('input', function(e) {
			searchTerm = e.target.value.toLowerCase();
			document.getElementById('globalSearchClear').classList.toggle('show', searchTerm.length > 0);
			categoryPage = 1;
			subcategoryPage = 1;
			productPage = 1;
			filterData();
		});

		document.addEventListener('click', function(e) {
			if (e.target.classList.contains('close') || e.target.id === 'editModal') {
				closeModal();
			}
		});

		window.addEventListener('resize', function() {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function() {
				renderCategories(displayedCategories);
				renderSubcategories(displayedSubcategories);
				renderProducts(displayedProducts);
			}, 150);
		});
	});

	function clearGlobalSearch() {
		document.getElementById('globalSearch').value = '';
		searchTerm = '';
		document.getElementById('globalSearchClear').classList.remove('show');
		categoryPage = 1;
		subcategoryPage = 1;
		productPage = 1;
		filterData();
	}

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
			Swal.fire({ title: 'Error', html: 'Failed to load taxonomy data', ...getSwalIcon('error1') });
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

	function getEffectiveView(section, view) {
		if (window.innerWidth < 800) return 'card';
		return view;
	}

	function getPageSize(view) {
		const w = window.innerWidth;
		if (view === 'card') {
			if (w >= 800) return 15;
			if (w >= 500) return 10;
			return 5;
		} else {
			if (w >= 2560) return 15;
			if (w >= 1500) return 14;
			if (w >= 1200) return 10;
			if (w >= 992) return 10;
			return 10;
		}
	}

	function toggleView(section) {
		if (window.innerWidth < 800) return;
		if (section === 'category') {
			categoryView = categoryView === 'table' ? 'card' : 'table';
			categoryPage = 1;
			renderCategories(displayedCategories);
		} else if (section === 'subcategory') {
			subcategoryView = subcategoryView === 'table' ? 'card' : 'table';
			subcategoryPage = 1;
			renderSubcategories(displayedSubcategories);
		} else if (section === 'product') {
			productView = productView === 'table' ? 'card' : 'table';
			productPage = 1;
			renderProducts(displayedProducts);
		}
	}

	function updateViewChrome(section, effectiveView, tableWrapId, cardGridId, toggleBtnId) {
		const tableWrap = document.getElementById(tableWrapId);
		const cardGrid = document.getElementById(cardGridId);
		const toggleBtn = document.getElementById(toggleBtnId);

		if (effectiveView === 'card') {
			tableWrap.classList.remove('is-active');
			cardGrid.classList.add('is-active');
		} else {
			tableWrap.classList.add('is-active');
			cardGrid.classList.remove('is-active');
		}

		if (window.innerWidth < 800) {
			toggleBtn.classList.add('hidden');
			cardGrid.classList.add('force-card');
		} else {
			toggleBtn.classList.remove('hidden');
			cardGrid.classList.remove('force-card');
			toggleBtn.innerHTML = effectiveView === 'card' ? '<i class="fas fa-table"></i>' : '<i class="fas fa-th-large"></i>';
		}
	}

	function paginate(data, page, pageSize) {
		const totalPages = Math.max(1, Math.ceil(data.length / pageSize));
		const safePage = Math.min(Math.max(1, page), totalPages);
		const start = (safePage - 1) * pageSize;
		return {
			pageItems: data.slice(start, start + pageSize),
			totalPages: totalPages,
			currentPage: safePage
		};
	}

	function renderPaginationBar(containerId, currentPage, totalPages, onChange) {
		const container = document.getElementById(containerId);

		if (totalPages <= 1) {
			container.classList.add('hidden');
			container.innerHTML = '';
			return;
		}

		container.classList.remove('hidden');

		let html = '';
		html += `<button class="tx-page-btn" ${currentPage <= 1 ? 'disabled' : ''} onclick="${onChange}(${currentPage - 1})"><i class="fas fa-chevron-left"></i></button>`;
		html += `<span class="tx-page-info">Page ${currentPage} of ${totalPages}</span>`;
		html += `<button class="tx-page-btn" ${currentPage >= totalPages ? 'disabled' : ''} onclick="${onChange}(${currentPage + 1})"><i class="fas fa-chevron-right"></i></button>`;

		container.innerHTML = html;
	}

	function goToCategoryPage(page) {
		categoryPage = page;
		renderCategories(displayedCategories);
	}

	function goToSubcategoryPage(page) {
		subcategoryPage = page;
		renderSubcategories(displayedSubcategories);
	}

	function goToProductPage(page) {
		productPage = page;
		renderProducts(displayedProducts);
	}

	function renderCategories(data = categories) {
		displayedCategories = data;
		const effectiveView = getEffectiveView('category', categoryView);
		const pageSize = getPageSize(effectiveView);
		const result = paginate(data, categoryPage, pageSize);
		categoryPage = result.currentPage;

		updateViewChrome('category', effectiveView, 'categoryTableWrap', 'categoryCardGrid', 'categoryViewToggle');

		const tableBody = document.getElementById('mainCategoriesList');
		const cardGrid = document.getElementById('categoryCardGrid');

		if (data.length === 0) {
			const emptyHtml = `
				<i class="fas fa-folder-open"></i>
				<p>No main categories found</p>
				<button class="tx-btn-add" onclick="openAddForm('main')"><i class="fas fa-plus"></i><span>Add First Category</span></button>
			`;
			tableBody.innerHTML = `<tr><td colspan="8" class="tx-state">${emptyHtml}</td></tr>`;
			cardGrid.innerHTML = `<div class="tx-state">${emptyHtml}</div>`;
			renderPaginationBar('categoryPagination', 1, 1, 'goToCategoryPage');
			return;
		}

		tableBody.innerHTML = result.pageItems.map((cat, index) => {
			const productCount = cat.product_count || 0;
			const subcategoryCount = cat.subcategory_count || 0;
			const iconUrl = cat.icon_filename ? `/assets/images/taxonomy-icons/${cat.icon_filename}` : null;
			const thumb = iconUrl ? `<img src="${iconUrl}" alt="${cat.category_name}" class="tx-thumb" onerror="this.outerHTML='<span class=\\'tx-thumb-fallback\\'><i class=\\'fas fa-folder-open\\'></i></span>';">` : `<span class="tx-thumb-fallback"><i class="fas fa-folder-open"></i></span>`;

			return `
				<tr>
					<td>${(categoryPage - 1) * pageSize + index + 1}</td>
					<td><div class="tx-cell-title">${thumb}<span>${cat.category_name}</span></div></td>
					<td class="tx-desc">${cat.description || '-'}</td>
					<td><span class="tx-badge tx-badge-blue">${subcategoryCount}</span></td>
					<td><span class="tx-badge tx-badge-blue">${productCount}</span></td>
					<td>${cat.display_order}</td>
					<td><span class="tx-status ${cat.is_active ? 'tx-status-on' : 'tx-status-off'}">${cat.is_active ? 'Active' : 'Inactive'}</span></td>
					<td>
						<div class="tx-actions">
							<button class="tx-action edit" onclick="editCategory(${cat.id}, '${cat.category_name.replace(/'/g, "\\'")}', '${(cat.description || '').replace(/'/g, "\\'")}', '${cat.icon_filename || ''}', ${cat.display_order})" title="Edit"><i class="fas fa-pen"></i></button>
							<button class="tx-action del" onclick="deleteItem('category', ${cat.id})" ${productCount > 0 ? 'disabled title="Cannot delete: Has products"' : 'title="Delete"'}><i class="fas fa-trash"></i></button>
						</div>
					</td>
				</tr>
			`;
		}).join('');

		cardGrid.innerHTML = result.pageItems.map((cat, index) => {
			const productCount = cat.product_count || 0;
			const subcategoryCount = cat.subcategory_count || 0;
			const iconUrl = cat.icon_filename ? `/assets/images/taxonomy-icons/${cat.icon_filename}` : null;
			const thumb = iconUrl ? `<img src="${iconUrl}" alt="${cat.category_name}" class="tx-thumb" onerror="this.outerHTML='<span class=\\'tx-thumb-fallback\\'><i class=\\'fas fa-folder-open\\'></i></span>';">` : `<span class="tx-thumb-fallback"><i class="fas fa-folder-open"></i></span>`;

			return `
				<div class="tx-item-card">
					<div class="tx-item-card-top">${thumb}<span class="tx-item-card-name">${cat.category_name}</span></div>
					<div class="tx-item-card-desc">${cat.description || 'No description'}</div>
					<div class="tx-item-card-meta">
						<span class="tx-badge tx-badge-blue">${subcategoryCount} subs</span>
						<span class="tx-badge tx-badge-blue">${productCount} products</span>
						<span class="tx-status ${cat.is_active ? 'tx-status-on' : 'tx-status-off'}">${cat.is_active ? 'Active' : 'Inactive'}</span>
					</div>
					<div class="tx-item-card-foot">
						<span class="tx-page-info">Order: ${cat.display_order}</span>
						<div class="tx-actions">
							<button class="tx-action edit" onclick="editCategory(${cat.id}, '${cat.category_name.replace(/'/g, "\\'")}', '${(cat.description || '').replace(/'/g, "\\'")}', '${cat.icon_filename || ''}', ${cat.display_order})" title="Edit"><i class="fas fa-pen"></i></button>
							<button class="tx-action del" onclick="deleteItem('category', ${cat.id})" ${productCount > 0 ? 'disabled title="Cannot delete: Has products"' : 'title="Delete"'}><i class="fas fa-trash"></i></button>
						</div>
					</div>
				</div>
			`;
		}).join('');

		renderPaginationBar('categoryPagination', result.currentPage, result.totalPages, 'goToCategoryPage');
	}

	function renderSubcategories(data = subcategories) {
		displayedSubcategories = data;
		const effectiveView = getEffectiveView('subcategory', subcategoryView);
		const pageSize = getPageSize(effectiveView);
		const result = paginate(data, subcategoryPage, pageSize);
		subcategoryPage = result.currentPage;

		updateViewChrome('subcategory', effectiveView, 'subcategoryTableWrap', 'subcategoryCardGrid', 'subcategoryViewToggle');

		const tableBody = document.getElementById('subCategoriesList');
		const cardGrid = document.getElementById('subcategoryCardGrid');

		if (data.length === 0) {
			const emptyHtml = `
				<i class="fas fa-folder"></i>
				<p>No sub-categories found</p>
				<button class="tx-btn-add" onclick="openAddForm('sub')"><i class="fas fa-plus"></i><span>Add First Sub-Category</span></button>
			`;
			tableBody.innerHTML = `<tr><td colspan="8" class="tx-state">${emptyHtml}</td></tr>`;
			cardGrid.innerHTML = `<div class="tx-state">${emptyHtml}</div>`;
			renderPaginationBar('subcategoryPagination', 1, 1, 'goToSubcategoryPage');
			return;
		}

		tableBody.innerHTML = result.pageItems.map((sub, index) => {
			const productCount = sub.product_count || 0;
			const productExampleCount = sub.product_example_count || 0;

			return `
				<tr>
					<td>${(subcategoryPage - 1) * pageSize + index + 1}</td>
					<td><div class="tx-cell-title"><span class="tx-thumb-fallback"><i class="fas fa-folder"></i></span><span>${sub.subcategory_name}</span></div></td>
					<td>${sub.category_name}</td>
					<td class="tx-desc">${sub.description || '-'}</td>
					<td><span class="tx-badge tx-badge-blue">${productExampleCount}</span></td>
					<td>${sub.display_order}</td>
					<td><span class="tx-status ${sub.is_active ? 'tx-status-on' : 'tx-status-off'}">${sub.is_active ? 'Active' : 'Inactive'}</span></td>
					<td>
						<div class="tx-actions">
							<button class="tx-action edit" onclick="editSubcategory(${sub.id}, '${sub.subcategory_name.replace(/'/g, "\\'")}', '${(sub.description || '').replace(/'/g, "\\'")}', ${sub.category_id}, ${sub.display_order})" title="Edit"><i class="fas fa-pen"></i></button>
							<button class="tx-action del" onclick="deleteItem('subcategory', ${sub.id})" ${productCount > 0 ? 'disabled title="Cannot delete: Has products"' : 'title="Delete"'}><i class="fas fa-trash"></i></button>
						</div>
					</td>
				</tr>
			`;
		}).join('');

		cardGrid.innerHTML = result.pageItems.map((sub, index) => {
			const productCount = sub.product_count || 0;
			const productExampleCount = sub.product_example_count || 0;

			return `
				<div class="tx-item-card">
					<div class="tx-item-card-top"><span class="tx-thumb-fallback"><i class="fas fa-folder"></i></span><span class="tx-item-card-name">${sub.subcategory_name}</span></div>
					<div class="tx-item-card-desc">${sub.description || sub.category_name}</div>
					<div class="tx-item-card-meta">
						<span class="tx-badge tx-badge-blue">${productExampleCount} products</span>
						<span class="tx-status ${sub.is_active ? 'tx-status-on' : 'tx-status-off'}">${sub.is_active ? 'Active' : 'Inactive'}</span>
					</div>
					<div class="tx-item-card-foot">
						<span class="tx-page-info">${sub.category_name}</span>
						<div class="tx-actions">
							<button class="tx-action edit" onclick="editSubcategory(${sub.id}, '${sub.subcategory_name.replace(/'/g, "\\'")}', '${(sub.description || '').replace(/'/g, "\\'")}', ${sub.category_id}, ${sub.display_order})" title="Edit"><i class="fas fa-pen"></i></button>
							<button class="tx-action del" onclick="deleteItem('subcategory', ${sub.id})" ${productCount > 0 ? 'disabled title="Cannot delete: Has products"' : 'title="Delete"'}><i class="fas fa-trash"></i></button>
						</div>
					</div>
				</div>
			`;
		}).join('');

		renderPaginationBar('subcategoryPagination', result.currentPage, result.totalPages, 'goToSubcategoryPage');
	}

	function renderProducts(data = products) {
		displayedProducts = data;
		const effectiveView = getEffectiveView('product', productView);
		const pageSize = getPageSize(effectiveView);
		const result = paginate(data, productPage, pageSize);
		productPage = result.currentPage;

		updateViewChrome('product', effectiveView, 'productTableWrap', 'productCardGrid', 'productViewToggle');

		const tableBody = document.getElementById('productsList');
		const cardGrid = document.getElementById('productCardGrid');

		if (data.length === 0) {
			const emptyHtml = `
				<i class="fas fa-seedling"></i>
				<p>No specific products found</p>
				<button class="tx-btn-add" onclick="openAddForm('product')"><i class="fas fa-plus"></i><span>Add First Product</span></button>
			`;
			tableBody.innerHTML = `<tr><td colspan="8" class="tx-state">${emptyHtml}</td></tr>`;
			cardGrid.innerHTML = `<div class="tx-state">${emptyHtml}</div>`;
			renderPaginationBar('productPagination', 1, 1, 'goToProductPage');
			return;
		}

		tableBody.innerHTML = result.pageItems.map((prod, index) => {
			return `
				<tr>
					<td>${(productPage - 1) * pageSize + index + 1}</td>
					<td><div class="tx-cell-title"><span class="tx-thumb-fallback"><i class="fas fa-seedling"></i></span><span>${prod.product_name}</span></div></td>
					<td>${prod.subcategory_name}</td>
					<td>${prod.category_name}</td>
					<td class="tx-desc">${prod.description || '-'}</td>
					<td>${prod.display_order}</td>
					<td><span class="tx-status ${prod.is_active ? 'tx-status-on' : 'tx-status-off'}">${prod.is_active ? 'Active' : 'Inactive'}</span></td>
					<td>
						<div class="tx-actions">
							<button class="tx-action edit" onclick="editProductExample(${prod.id}, '${prod.product_name.replace(/'/g, "\\'")}', '${(prod.description || '').replace(/'/g, "\\'")}', ${prod.subcategory_id}, ${prod.display_order})" title="Edit"><i class="fas fa-pen"></i></button>
							<button class="tx-action del" onclick="deleteItem('product', ${prod.id})" title="Delete"><i class="fas fa-trash"></i></button>
						</div>
					</td>
				</tr>
			`;
		}).join('');

		cardGrid.innerHTML = result.pageItems.map((prod, index) => {
			return `
				<div class="tx-item-card">
					<div class="tx-item-card-top"><span class="tx-thumb-fallback"><i class="fas fa-seedling"></i></span><span class="tx-item-card-name">${prod.product_name}</span></div>
					<div class="tx-item-card-desc">${prod.description || 'No description'}</div>
					<div class="tx-item-card-meta">
						<span class="tx-badge tx-badge-blue">${prod.subcategory_name}</span>
						<span class="tx-status ${prod.is_active ? 'tx-status-on' : 'tx-status-off'}">${prod.is_active ? 'Active' : 'Inactive'}</span>
					</div>
					<div class="tx-item-card-foot">
						<span class="tx-page-info">${prod.category_name}</span>
						<div class="tx-actions">
							<button class="tx-action edit" onclick="editProductExample(${prod.id}, '${prod.product_name.replace(/'/g, "\\'")}', '${(prod.description || '').replace(/'/g, "\\'")}', ${prod.subcategory_id}, ${prod.display_order})" title="Edit"><i class="fas fa-pen"></i></button>
							<button class="tx-action del" onclick="deleteItem('product', ${prod.id})" title="Delete"><i class="fas fa-trash"></i></button>
						</div>
					</div>
				</div>
			`;
		}).join('');

		renderPaginationBar('productPagination', result.currentPage, result.totalPages, 'goToProductPage');
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
							<div style="font-size: 0.8rem; color: var(--tx-muted);" id="previewFileSize"></div>
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
							<button type="button" class="tx-btn-add" style="background: var(--tx-red); box-shadow: none; margin-top: 5px;"
									onclick="removeSubcategory(this)">
								<i class="fas fa-times"></i> Remove
							</button>
						</div>
					</div>
					<button type="button" class="tx-btn-add" onclick="addSubcategory()" style="margin-top: 5px;">
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
							<button type="button" class="tx-btn-add" style="background: var(--tx-red); box-shadow: none; margin-top: 5px;"
									onclick="removeProduct(this)">
								<i class="fas fa-times"></i> Remove
							</button>
						</div>
						<div class="product-item">
							<input type="text" class="form-input" name="products[1][name]"
								   placeholder="Another product name" required>
							<select class="form-select" name="products[1][subcategory_index]" style="margin-top: 5px;" required>
								<option value="">Select Sub-Category</option>
							</select>
							<button type="button" class="tx-btn-add" style="background: var(--tx-red); box-shadow: none; margin-top: 5px;"
									onclick="removeProduct(this)">
								<i class="fas fa-times"></i> Remove
							</button>
						</div>
					</div>
					<button type="button" class="tx-btn-add" onclick="addProduct()" style="margin-top: 5px;">
						<i class="fas fa-plus"></i> Add Another Product
					</button>
				</div>

				<div class="tx-step-buttons">
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
			Swal.fire({ title: 'Error', html: 'Only PNG images are allowed', ...getSwalIcon('alert1_error') });
			input.value = '';
			return;
		}

		if (file.size > 2 * 1024 * 1024) {
			Swal.fire({ title: 'Error', html: 'Image size should be less than 2MB', ...getSwalIcon('alert1_error') });
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
		// Flag that existing icon should be removed from server
		var removeFlag = document.getElementById('removeIcon');
		if (removeFlag) {
			removeFlag.value = '1';
		}
	}

	function addSubcategory() {
		const container = document.getElementById('subcategoriesContainer');
		const div = document.createElement('div');
		div.className = 'subcategory-item';
		div.style.marginBottom = '10px';
		div.innerHTML = `
			<input type="text" class="form-input" name="subcategories[]"
				   placeholder="Sub-category name" required>
			<button type="button" class="tx-btn-add" style="background: var(--tx-red); box-shadow: none; margin-top: 5px;"
					onclick="removeSubcategory(this)">
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
			Swal.fire({ title: 'Warning', html: 'You must have at least one sub-category', ...getSwalIcon('alert4_warning') });
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
			<button type="button" class="tx-btn-add" style="background: var(--tx-red); box-shadow: none; margin-top: 5px;"
					onclick="removeProduct(this)">
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
			Swal.fire({ title: 'Warning', html: 'You must have at least two products', ...getSwalIcon('alert4_warning') });
		}
	}

	function reindexProductForms() {
		const container = document.getElementById('productsContainer');
		const items = container.querySelectorAll('.product-item');
		items.forEach((item, index) => {
			const nameInput = item.querySelector('input[type="text"]');
			const select = item.querySelector('select');

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
			Swal.fire({ title: 'Validation Error', html: 'Must have at least 1 sub-category', ...getSwalIcon('alert4_error') });
			return;
		}

		if (products.length < 2) {
			Swal.fire({ title: 'Validation Error', html: 'Must have at least 2 specific products', ...getSwalIcon('alert4_error') });
			return;
		}

		if (products.some(p => p.subcategory_index === null || p.subcategory_index === undefined)) {
			Swal.fire({ title: 'Validation Error', html: 'All products must be assigned to a sub-category', ...getSwalIcon('alert4_error') });
			return;
		}

		const imageFile = formData.get('image');
		if (imageFile && imageFile.size > 0) {
			if (imageFile.type !== 'image/png') {
				Swal.fire({ title: 'Error', html: 'Only PNG images are allowed', ...getSwalIcon('alert2_error') });
				return;
			}
			if (imageFile.size > 2 * 1024 * 1024) {
				Swal.fire({ title: 'Error', html: 'Image size should be less than 2MB', ...getSwalIcon('alert2_error') });
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

		subcategories.forEach((subcat, index) => {
			data.append(`subcategories[${index}]`, subcat);
		});

		products.forEach((product, index) => {
			data.append(`products[${index}][name]`, product.name);
			data.append(`products[${index}][subcategory_index]`, product.subcategory_index);
		});

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
					...getSwalIcon('success4'),
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
			Swal.fire({ title: 'Error', html: error.message, ...getSwalIcon('error2') });
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
							<button type="button" class="tx-btn-add" style="background: var(--tx-red); box-shadow: none; margin-top: 5px;"
									onclick="removeSubcatProduct(this)">
								<i class="fas fa-times"></i> Remove
							</button>
						</div>
						<div class="product-item">
							<input type="text" class="form-input" name="products[]"
								   placeholder="Another product name" required>
							<button type="button" class="tx-btn-add" style="background: var(--tx-red); box-shadow: none; margin-top: 5px;"
									onclick="removeSubcatProduct(this)">
								<i class="fas fa-times"></i> Remove
							</button>
						</div>
					</div>
					<button type="button" class="tx-btn-add" onclick="addSubcatProduct()" style="margin-top: 5px;">
						<i class="fas fa-plus"></i> Add Another Product
					</button>
				</div>

				<div class="form-group">
					<label class="form-label">Display Order</label>
					<input type="number" class="form-input" name="display_order" value="0" min="0">
				</div>

				<div class="tx-step-buttons">
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
			<button type="button" class="tx-btn-add" style="background: var(--tx-red); box-shadow: none; margin-top: 5px;"
					onclick="removeSubcatProduct(this)">
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
			Swal.fire({ title: 'Warning', html: 'You must have at least two products', ...getSwalIcon('alert2_warning') });
		}
	}

	async function saveSubCategory(e) {
		e.preventDefault();
		const form = e.target;
		const formData = new FormData(form);

		const products = Array.from(formData.getAll('products[]')).filter(p => p.trim());

		if (products.length < 2) {
			Swal.fire({ title: 'Validation Error', html: 'Must have at least 2 specific products', ...getSwalIcon('alert2_error') });
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
					...getSwalIcon('success4'),
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
			Swal.fire({ title: 'Error', html: error.message, ...getSwalIcon('error2') });
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

				<div class="tx-step-buttons">
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
					...getSwalIcon('success4'),
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
			Swal.fire({ title: 'Error', html: error.message, ...getSwalIcon('error5') });
		}
	}

	function closeForm() {
		document.getElementById('addFormContainer').style.display = 'none';
		document.getElementById('formContent').innerHTML = '';
	}

	function editCategory(id, name, description, iconFilename, displayOrder = 0) {
		const iconUrl = iconFilename ? `/assets/images/taxonomy-icons/${iconFilename}` : null;

		Swal.fire({
			title: 'Edit Category',
			html: `
				<div class="text-start">
					<div class="mb-3">
						<label class="form-label">Category Name *</label>
						<input type="text" class="form-control swal2-input" id="editCategoryName" value="${name}" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Description</label>
						<textarea class="form-control swal2-textarea" id="editCategoryDesc" rows="3">${description || ''}</textarea>
					</div>

					<div class="mb-3">
						<label class="form-label">Display Order</label>
						<input type="number" class="form-control swal2-input" id="editCategoryOrder" value="${displayOrder}" min="0" required>
					</div>

					<div class="mb-3">
						<label class="form-label">Category Icon Image (PNG only, max 2MB)</label>
						<div class="image-upload-area" id="editImageUploadArea" onclick="document.getElementById('editCategoryImage').click()">
							<div class="upload-icon">
								<i class="fas fa-cloud-upload-alt"></i>
							</div>
							<div class="upload-text">Click to change icon</div>
							<div class="upload-text" style="font-size: 0.8rem; color: #9ca3af;">
								PNG format only, max 2MB
							</div>
						</div>
						<input type="file" id="editCategoryImage" name="image" accept=".png" style="display: none;"
							   onchange="previewEditImage(this, 'editCategoryPreview')">

						<div id="editCategoryPreview" class="image-preview-container" style="${iconUrl ? 'display: flex;' : 'display: none;'}">
							<img class="preview-image" id="previewEditCategoryImage" src="${iconUrl || ''}" alt="Preview">
							<div class="preview-info">
								<div style="font-weight: 500; font-size: 0.9rem;" id="previewEditFileName">${iconFilename || ''}</div>
								<div style="font-size: 0.8rem; color: var(--tx-muted);" id="previewEditFileSize"></div>
							</div>
							<div class="remove-image" onclick="removeImage('editCategoryImage', 'editCategoryPreview')">
								<i class="fas fa-times"></i> Remove
							</div>
						</div>
					</div>

					<input type="hidden" id="categoryId" value="${id}">
					<input type="hidden" id="removeIcon" value="0">
				</div>
			`,
			...getSwalIcon('editing1'),
			showCancelButton: true,
			confirmButtonText: 'Update Category',
			confirmButtonColor: '#0ea5a4',
			cancelButtonColor: '#78829a',
			background: '#ffffff',
			color: '#1e2230',
			width: 'auto',
			didOpen: () => {
				const editArea = document.getElementById('editImageUploadArea');
				if (editArea) {
					editArea.addEventListener('dragover', function(e) {
						e.preventDefault();
						this.classList.add('dragover');
					});
					editArea.addEventListener('dragleave', function(e) {
						e.preventDefault();
						this.classList.remove('dragover');
					});
					editArea.addEventListener('drop', function(e) {
						e.preventDefault();
						this.classList.remove('dragover');
						const fileInput = document.getElementById('editCategoryImage');
						if (e.dataTransfer.files.length > 0) {
							fileInput.files = e.dataTransfer.files;
							fileInput.dispatchEvent(new Event('change'));
						}
					});
				}
			},
			preConfirm: () => {
				const name = document.getElementById('editCategoryName').value;
				if (!name) {
					Swal.showValidationMessage('Category name is required');
					return false;
				}

				const formData = new FormData();
				formData.append('id', document.getElementById('categoryId').value);
				formData.append('name', name);
				formData.append('description', document.getElementById('editCategoryDesc').value);
				formData.append('display_order', document.getElementById('editCategoryOrder').value);

				const imageFile = document.getElementById('editCategoryImage').files[0];
				if (imageFile) {
					formData.append('image', imageFile);
				}

				// Send remove_icon flag if user clicked Remove
				var removeIconVal = document.getElementById('removeIcon').value;
				formData.append('remove_icon', removeIconVal);

				return formData;
			}
		}).then(result => {
			if (result.isConfirmed) {
				const formData = result.value;
				showLoading();

				fetch('{{ route("admin.taxonomy.update.category") }}', {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					body: formData
				})
				.then(response => response.json())
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

	function editSubcategory(id, name, description, categoryId, displayOrder = 0) {
		Swal.fire({
			title: 'Edit Subcategory',
			html: `
				<div class="text-start">
					<div class="mb-3">
						<label class="form-label">Subcategory Name *</label>
						<input type="text" class="form-control swal2-input" id="editSubcategoryName" value="${name}" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Description</label>
						<textarea class="form-control swal2-textarea" id="editSubcategoryDesc" rows="3">${description || ''}</textarea>
					</div>
					<div class="mb-3">
						<label class="form-label">Display Order</label>
						<input type="number" class="form-control swal2-input" id="editSubcategoryOrder" value="${displayOrder}" min="0" required>
					</div>
					<input type="hidden" id="subcategoryId" value="${id}">
					<input type="hidden" id="editCategoryId" value="${categoryId}">
				</div>
			`,
			...getSwalIcon('editing1'),
			showCancelButton: true,
			confirmButtonText: 'Update Subcategory',
			confirmButtonColor: '#0ea5a4',
			cancelButtonColor: '#78829a',
			background: '#ffffff',
			color: '#1e2230',
			width: 'auto',
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
					category_id: document.getElementById('editCategoryId').value,
					display_order: document.getElementById('editSubcategoryOrder').value
				};
			}
		}).then(result => {
			if (result.isConfirmed) {
				const data = result.value;
				showLoading();

				fetch('{{ route("admin.taxonomy.update.subcategory") }}', {
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

	function editProductExample(id, name, description, subcategoryId, displayOrder = 0) {
		Swal.fire({
			title: 'Edit Product',
			html: `
				<div class="text-start">
					<div class="mb-3">
						<label class="form-label">Product Name *</label>
						<input type="text" class="form-control swal2-input" id="editProductName" value="${name}" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Description</label>
						<textarea class="form-control swal2-textarea" id="editProductDesc" rows="3">${description || ''}</textarea>
					</div>
					<div class="mb-3">
						<label class="form-label">Display Order</label>
						<input type="number" class="form-control swal2-input" id="editProductOrder" value="${displayOrder}" min="0" required>
					</div>
					<input type="hidden" id="productId" value="${id}">
					<input type="hidden" id="editSubcategoryId" value="${subcategoryId}">
				</div>
			`,
			...getSwalIcon('editing1'),
			showCancelButton: true,
			confirmButtonText: 'Update Product',
			confirmButtonColor: '#0ea5a4',
			cancelButtonColor: '#78829a',
			background: '#ffffff',
			color: '#1e2230',
			width: 'auto',
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
					subcategory_id: document.getElementById('editSubcategoryId').value,
					display_order: document.getElementById('editProductOrder').value
				};
			}
		}).then(result => {
			if (result.isConfirmed) {
				const data = result.value;
				showLoading();

				fetch('{{ route("admin.taxonomy.update.product.example") }}', {
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

	function previewEditImage(input, previewContainerId) {
		const file = input.files[0];
		if (!file) return;

		if (file.type !== 'image/png') {
			Swal.fire({ title: 'Error', html: 'Only PNG images are allowed', ...getSwalIcon('alert2_error') });
			input.value = '';
			return;
		}

		if (file.size > 2 * 1024 * 1024) {
			Swal.fire({ title: 'Error', html: 'Image size should be less than 2MB', ...getSwalIcon('alert2_error') });
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

	function deleteItem(type, id) {
		let url = '';
		let title = '';
		let text = '';

		switch(type) {
			case 'category':
				url = '{{ route("admin.taxonomy.delete.category", ":id") }}'.replace(':id', id);
				title = 'Delete Category?';
				text = 'Are you sure you want to delete this category? This action cannot be undone.';
				break;
			case 'subcategory':
				url = '{{ route("admin.taxonomy.delete.subcategory", ":id") }}'.replace(':id', id);
				title = 'Delete Sub-category?';
				text = 'Are you sure you want to delete this sub-category? This action cannot be undone.';
				break;
			case 'product':
				url = '{{ route("admin.taxonomy.delete.product", ":id") }}'.replace(':id', id);
				title = 'Delete Product?';
				text = 'Are you sure you want to delete this product? This action cannot be undone.';
				break;
			default:
				console.error('Invalid delete type');
				return;
		}

		Swal.fire({
			title: title,
			text: text,
			...getSwalIcon('alert2_warning'),
			showCancelButton: true,
			confirmButtonColor: '#ef4444',
			cancelButtonColor: '#78829a',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.isConfirmed) {
				showLoading();

				fetch(url, {
					method: 'DELETE',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}',
						'Content-Type': 'application/json'
					}
				})
				.then(response => {
					if (!response.ok) {
						return response.json().then(data => { throw new Error(data.message || 'Failed to delete item'); });
					}
					return response.json();
				})
				.then(data => {
					hideLoading();
					if (data.success) {
						showSuccess(data.message);
						loadAllData();
					} else {
						showError(data.message || 'Failed to delete item');
					}
				})
				.catch(error => {
					hideLoading();
					console.error('Error:', error);
					showError(error.message || 'An error occurred while deleting');
				});
			}
		});
	}

	function showLoading() {
		Swal.showLoading();
	}

	function hideLoading() {
		Swal.close();
	}

	function showSuccess(message) {
		Swal.fire({
			...getSwalIcon('success2'),
			title: 'Success!',
			text: message,
			showConfirmButton: false,
			timer: 2000
		});
	}

	function showError(message) {
		Swal.fire({
			...getSwalIcon('error2'),
			title: 'Error!',
			text: message
		});
	}

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
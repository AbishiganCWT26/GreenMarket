@extends('admin.layouts.admin_master')

@section('title', 'Buyer Requests')
@section('page-title', 'Buyer Product Requests')

@section('styles')
<style>
	:root {
		--primary-green: #10B981;
		--dark-green: #059669;
		--accent-amber: #f59e0b;
		--blue: #3b82f6;
		--purple: #8b5cf6;
		--yellow: #f59e0b;
		--body-bg: #f6f8fa;
		--card-bg: #ffffff;
		--text-color: #0f1724;
		--muted: #6b7280;
		--shadow-sm: 0 1px 2px rgba(15,23,36,0.03);
		--shadow-md: 0 3px 6px rgba(15,23,36,0.05);
	}

	.buyer-requests-container {
		padding: 12px;
		background: var(--body-bg);
		min-height: calc(100vh - 140px);
	}

	.page-header {
		background: var(--card-bg);
		border-radius: 6px;
		padding: 10px 14px;
		margin-bottom: 14px;
		box-shadow: var(--shadow-sm);
		border: 1px solid #e1e4ed;
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-wrap: wrap;
		gap: 10px;
	}

	.page-header h2 {
		font-size: 14px;
		font-weight: 600;
		color: var(--text-color);
		margin: 0;
		display: flex;
		align-items: center;
		gap: 6px;
	}

	.page-header h2 i {
		color: var(--primary-green);
		font-size: 12px;
	}

	.header-controls {
		display: flex;
		gap: 8px;
		align-items: center;
		flex-wrap: wrap;
	}

	.search-container {
		position: relative;
	}

	.search-input {
		width: 180px;
		height: 24px;
		padding: 0 26px 0 10px;
		border: 1px solid #d1d5db;
		border-radius: 4px;
		font-size: 11px;
		transition: all 0.2s;
		background: var(--card-bg);
		color: var(--text-color);
	}

	.search-input:focus {
		outline: none;
		border-color: var(--primary-green);
		box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
	}

	.search-btn {
		position: absolute;
		right: 6px;
		top: 50%;
		transform: translateY(-50%);
		background: none;
		border: none;
		color: var(--muted);
		cursor: pointer;
		padding: 0;
		width: 12px;
		height: 12px;
		font-size: 10px;
	}

	.view-toggle {
		display: flex;
		background: var(--card-bg);
		border: 1px solid #d1d5db;
		border-radius: 4px;
		overflow: hidden;
	}

	.view-toggle-btn {
		padding: 4px 8px;
		background: none;
		border: none;
		font-size: 11px;
		color: var(--muted);
		cursor: pointer;
		transition: all 0.2s;
		display: flex;
		align-items: center;
		gap: 4px;
	}

	.view-toggle-btn.active {
		background: var(--primary-green);
		color: white;
	}

	.view-toggle-btn:hover:not(.active) {
		background: #f3f4f6;
	}

	.view-toggle-btn i {
		font-size: 10px;
	}

	.content-area {
		background: var(--card-bg);
		border-radius: 6px;
		padding: 0;
		box-shadow: var(--shadow-sm);
		border: 1px solid #e1e4ed;
		overflow: hidden;
		animation: fadeIn 0.3s ease;
	}

	@keyframes fadeIn {
		from { opacity: 0; transform: translateY(8px); }
		to { opacity: 1; transform: translateY(0); }
	}

	.loading-spinner {
		text-align: center;
		padding: 25px;
	}

	.loading-spinner i {
		font-size: 18px;
		color: var(--primary-green);
		animation: spin 1s linear infinite;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

	.empty-state {
		text-align: center;
		padding: 40px 15px;
		color: var(--muted);
	}

	.empty-state i {
		font-size: 32px;
		margin-bottom: 10px;
		color: #d1d5db;
	}

	.empty-state h3 {
		font-size: 13px;
		font-weight: 600;
		margin-bottom: 6px;
		color: var(--text-color);
	}

	.empty-state p {
		font-size: 11px;
		max-width: 320px;
		margin: 0 auto;
	}

	.pagination-container {
		background: var(--card-bg);
		border-top: 1px solid #e1e4ed;
		padding: 12px 15px;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.pagination-wrapper {
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.pagination-btn {
		width: 22px;
		height: 22px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 3px;
		border: 1px solid #d1d5db;
		background: var(--card-bg);
		color: var(--text-color);
		font-size: 11px;
		cursor: pointer;
		transition: all 0.2s;
	}

	.pagination-btn:hover:not(:disabled) {
		background: #f3f4f6;
		border-color: var(--primary-green);
		color: var(--primary-green);
	}

	.pagination-btn:disabled {
		opacity: 0.5;
		cursor: not-allowed;
	}

	.pagination-pages {
		display: flex;
		gap: 4px;
	}

	.page-number {
		min-width: 22px;
		height: 22px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 3px;
		border: 1px solid transparent;
		background: var(--card-bg);
		color: var(--text-color);
		font-size: 11px;
		cursor: pointer;
		transition: all 0.2s;
	}

	.page-number:hover {
		background: #f3f4f6;
		border-color: #d1d5db;
	}

	.page-number.active {
		background: var(--primary-green);
		color: white;
		border-color: var(--primary-green);
	}

	.pagination-info {
		font-size: 11px;
		color: var(--muted);
		margin-left: 10px;
	}

	@media (max-width: 1200px) {
		.buyer-requests-container {
			padding: 0;
		}

		.search-input {
			width: 160px;
		}
	}

	@media (max-width: 992px) {
		.page-header {
			padding: 10px 12px;
		}

		.page-header h2 {
			font-size: 13px;
		}

		.header-controls {
			gap: 6px;
		}

		.search-input {
			width: 150px;
			height: 22px;
			font-size: 10px;
		}
	}

	@media (max-width: 768px) {
		.page-header {
			flex-direction: column;
			align-items: stretch;
			gap: 8px;
		}

		.header-controls {
			justify-content: space-between;
		}

		.search-input {
			width: 100%;
			max-width: 200px;
		}
	}

	@media (max-width: 600px) {
		.buyer-requests-container {
			padding: 0;
		}

		.page-header {
			padding: 8px 10px;
			margin-bottom: 12px;
		}

		.header-controls {
			flex-direction: column;
			align-items: stretch;
		}

		.search-container {
			width: 100%;
		}

		.search-input {
			width: 100%;
			max-width: none;
			height: 24px;
		}

		.view-toggle {
			width: 100%;
			justify-content: center;
		}

		.view-toggle-btn {
			flex: 1;
			justify-content: center;
		}
	}

	@media (max-width: 480px) {
		.page-header h2 {
			font-size: 12px;
		}

		.view-toggle-btn span {
			display: none;
		}

		.view-toggle-btn i {
			margin: 0;
		}

		.pagination-wrapper {
			flex-wrap: wrap;
			justify-content: center;
			gap: 6px;
		}

		.pagination-info {
			flex-basis: 100%;
			text-align: center;
			margin: 6px 0 0 0;
		}
	}
</style>
@endsection

@section('content')
<div class="buyer-requests-container">
	<div class="page-header">
		<h2>
			<i class="fas fa-handshake"></i>
			Buyer Product Requests
		</h2>

		<div class="header-controls">
			<div class="search-container">
				<input type="text"
					   id="searchInput"
					   class="search-input"
					   placeholder="Search requests..."
					   value="{{ request('search') }}">
				<button type="button" id="searchBtn" class="search-btn">
					<i class="fas fa-search"></i>
				</button>
			</div>

			<div class="view-toggle">
				<button type="button"
						id="tableViewBtn"
						class="view-toggle-btn active"
						data-view="table">
					<i class="fas fa-table"></i>
					<span>Table</span>
				</button>
				<button type="button"
						id="cardViewBtn"
						class="view-toggle-btn"
						data-view="card">
					<i class="fas fa-th-large"></i>
					<span>Cards</span>
				</button>
			</div>
		</div>
	</div>

	<div id="contentArea" class="content-area">
		<div id="loadingSpinner" class="loading-spinner" style="display: none;">
			<i class="fas fa-spinner"></i>
			<p style="margin-top: 8px; font-size: 11px; color: var(--muted);">Loading...</p>
		</div>

		<div id="requestsContent">
			@include('admin.buyer-requests.table-view')
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const searchInput = document.getElementById('searchInput');
		const searchBtn = document.getElementById('searchBtn');
		const tableViewBtn = document.getElementById('tableViewBtn');
		const cardViewBtn = document.getElementById('cardViewBtn');
		const contentArea = document.getElementById('contentArea');
		const loadingSpinner = document.getElementById('loadingSpinner');
		const requestsContent = document.getElementById('requestsContent');

		let currentView = 'table';
		let currentPage = 1;
		let isLoading = false;
		let searchTimeout;

		function showLoading() {
			loadingSpinner.style.display = 'block';
			contentArea.style.minHeight = '200px';
		}

		function hideLoading() {
			loadingSpinner.style.display = 'none';
		}

		function updateView(view) {
			if (isLoading || currentView === view) return;

			currentView = view;
			currentPage = 1;

			tableViewBtn.classList.toggle('active', view === 'table');
			cardViewBtn.classList.toggle('active', view === 'card');

			loadRequests();
		}

		function loadRequests(page = 1) {
			if (isLoading) return;

			isLoading = true;
			showLoading();

			const search = searchInput.value.trim();
			const url = new URL(window.location.href);
			url.searchParams.set('view', currentView);
			url.searchParams.set('page', page);

			if (search) {
				url.searchParams.set('search', search);
			} else {
				url.searchParams.delete('search');
			}

			fetch(url, {
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'Accept': 'text/html'
				}
			})
			.then(response => {
				if (!response.ok) throw new Error('Network response was not ok');
				return response.text();
			})
			.then(html => {
				requestsContent.innerHTML = html;
				currentPage = page;
				attachEventListeners();
				scrollToTop();
			})
			.catch(error => {
				console.error('Error loading requests:', error);
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Failed to load requests. Please try again.',
					timer: 2000,
					showConfirmButton: false
				});
			})
			.finally(() => {
				isLoading = false;
				hideLoading();
			});
		}

		function searchRequests() {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(() => {
				currentPage = 1;
				loadRequests();
			}, 500);
		}

		function scrollToTop() {
			window.scrollTo({
				top: contentArea.offsetTop - 80,
				behavior: 'smooth'
			});
		}

		function attachEventListeners() {
			const deleteButtons = document.querySelectorAll('.delete-btn');
			deleteButtons.forEach(btn => {
				btn.addEventListener('click', handleDelete);
			});

			const paginationLinks = document.querySelectorAll('.pagination-link');
			paginationLinks.forEach(link => {
				link.addEventListener('click', function(e) {
					e.preventDefault();
					const page = this.getAttribute('data-page');
					if (page) {
						loadRequests(parseInt(page));
					}
				});
			});

			const prevBtn = document.querySelector('.pagination-prev');
			const nextBtn = document.querySelector('.pagination-next');

			if (prevBtn) {
				prevBtn.addEventListener('click', function(e) {
					e.preventDefault();
					if (!this.classList.contains('disabled')) {
						const prevPage = currentPage - 1;
						if (prevPage >= 1) {
							loadRequests(prevPage);
						}
					}
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', function(e) {
					e.preventDefault();
					if (!this.classList.contains('disabled')) {
						const nextPage = currentPage + 1;
						const totalPages = parseInt(this.getAttribute('data-total') || '1');
						if (nextPage <= totalPages) {
							loadRequests(nextPage);
						}
					}
				});
			}
		}

		function handleDelete(e) {
			e.preventDefault();
			const button = e.currentTarget;
			const requestId = button.getAttribute('data-id');
			const productName = button.getAttribute('data-product');

			Swal.fire({
				title: 'Delete Request',
				html: `Are you sure you want to delete the request for<br><strong>"${productName}"</strong>?<br><br>
					   <small class="text-muted">This will notify the buyer via email and SMS.</small>`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#10B981',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'Cancel',
				reverseButtons: true,
				showLoaderOnConfirm: true,
				preConfirm: () => {
					return fetch(`/admin/buyer-requests/${requestId}`, {
						method: 'DELETE',
						headers: {
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
							'Accept': 'application/json'
						}
					})
					.then(response => {
						if (!response.ok) {
							throw new Error('Delete failed');
						}
						return response.json();
					})
					.catch(error => {
						Swal.showValidationMessage('Request failed. Please try again.');
					});
				}
			}).then((result) => {
				if (result.isConfirmed) {
					if (result.value && result.value.success) {
						Swal.fire({
							icon: 'success',
							title: 'Deleted!',
							text: result.value.message,
							timer: 2000,
							showConfirmButton: false
						}).then(() => {
							loadRequests(currentPage);
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'Failed to delete the request.',
							timer: 2000,
							showConfirmButton: false
						});
					}
				}
			});
		}

		searchBtn.addEventListener('click', searchRequests);
		searchInput.addEventListener('input', searchRequests);
		searchInput.addEventListener('keypress', function(e) {
			if (e.key === 'Enter') {
				searchRequests();
			}
		});

		tableViewBtn.addEventListener('click', () => updateView('table'));
		cardViewBtn.addEventListener('click', () => updateView('card'));

		attachEventListeners();

		window.addEventListener('resize', function() {
			if (window.innerWidth <= 600 && currentView === 'table') {
				updateView('card');
			} else if (window.innerWidth > 600 && currentView === 'card') {
				updateView('table');
			}
		});

		if (window.innerWidth <= 600 && currentView === 'table') {
			updateView('card');
		}
	});
</script>
@endsection


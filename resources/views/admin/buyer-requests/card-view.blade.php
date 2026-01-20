@if($buyerRequests->count() > 0)
<div class="cards-grid">
	@foreach($buyerRequests as $request)
	<div class="request-card">
		<div class="card-header">
			<div class="product-image-container">
				<img src="{{ $request->image_url }}"
					 alt="{{ $request->product_name }}"
					 class="product-image-card"
					 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
			</div>
			<div class="card-header-info">
				<h3 class="product-title">{{ $request->product_name }}</h3>
				<div class="buyer-badge">
					<i class="fas fa-user fa-xs"></i>
					<span>{{ $request->buyer->name }}</span>
				</div>
			</div>
		</div>

		<div class="card-body">
			@if($request->description)
			<p class="card-description">{{ Str::limit($request->description, 80) }}</p>
			@endif

			<div class="card-details-grid">
				<div class="detail-item">
					<i class="fas fa-balance-scale fa-xs"></i>
					<div class="detail-content">
						<span class="detail-label">Quantity</span>
						<span class="detail-value">{{ $request->formatted_quantity }}</span>
					</div>
				</div>

				<div class="detail-item">
					<i class="fas fa-calendar-alt fa-xs"></i>
					<div class="detail-content">
						<span class="detail-label">Needed Date</span>
						<span class="detail-value">{{ $request->formatted_date }}</span>
					</div>
				</div>

				<div class="detail-item">
					<i class="fas fa-tag fa-xs"></i>
					<div class="detail-content">
						<span class="detail-label">Price</span>
						<span class="detail-value">{{ $request->formatted_price }}</span>
					</div>
				</div>

				@if($request->buyer->business_name)
				<div class="detail-item">
					<i class="fas fa-building fa-xs"></i>
					<div class="detail-content">
						<span class="detail-label">Business</span>
						<span class="detail-value">{{ $request->buyer->business_name }}</span>
					</div>
				</div>
				@endif
			</div>
		</div>

		<div class="card-footer">
			<button type="button"
					class="card-action-btn delete-btn"
					data-id="{{ $request->id }}"
					data-product="{{ $request->product_name }}"
					title="Delete Request">
				<i class="fas fa-trash-alt fa-xs"></i>
				<span>Delete</span>
			</button>
		</div>
	</div>
	@endforeach
</div>

<style>
	.cards-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
		gap: 16px;
		padding: 20px;
	}

	.request-card {
		background: #ffffff;
		border-radius: 8px;
		overflow: hidden;
		box-shadow: 0 2px 8px rgba(15, 23, 36, 0.08);
		border: 1px solid #e1e4ed;
		transition: all 0.3s ease;
		display: flex;
		flex-direction: column;
		min-height: 300px;
	}

	.request-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 8px 20px rgba(15, 23, 36, 0.12);
		border-color: #10B981;
	}

	.card-header {
		padding: 12px;
		background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
		border-bottom: 1px solid #e1e4ed;
		display: flex;
		align-items: center;
		gap: 12px;
	}

	.product-image-container {
		width: 50px;
		height: 50px;
		border-radius: 6px;
		overflow: hidden;
		background: #f3f4f6;
		flex-shrink: 0;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.product-image-card {
		width: 100%;
		height: 100%;
		object-fit: cover;
		transition: transform 0.3s ease;
	}

	.request-card:hover .product-image-card {
		transform: scale(1.1);
	}

	.card-header-info {
		flex: 1;
		min-width: 0;
	}

	.product-title {
		font-size: 0.9rem;
		font-weight: 600;
		color: #0f1724;
		margin: 0 0 4px 0;
		line-height: 1.3;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
	}

	.buyer-badge {
		display: inline-flex;
		align-items: center;
		gap: 4px;
		background: rgba(16, 185, 129, 0.1);
		color: #10B981;
		padding: 2px 8px;
		border-radius: 12px;
		font-size: 0.7rem;
		font-weight: 500;
	}

	.buyer-badge i {
		font-size: 0.6rem;
	}

	.card-body {
		padding: 12px;
		flex: 1;
		display: flex;
		flex-direction: column;
		gap: 12px;
	}

	.card-description {
		font-size: 0.75rem;
		color: #6b7280;
		line-height: 1.4;
		margin: 0;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 3;
		-webkit-box-orient: vertical;
	}

	.card-details-grid {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 10px;
		margin-top: auto;
	}

	.detail-item {
		display: flex;
		align-items: flex-start;
		gap: 6px;
	}

	.detail-item i {
		color: #10B981;
		margin-top: 2px;
		font-size: 0.7rem;
	}

	.detail-content {
		display: flex;
		flex-direction: column;
		gap: 2px;
	}

	.detail-label {
		font-size: 0.65rem;
		color: #6b7280;
		text-transform: uppercase;
		letter-spacing: 0.3px;
	}

	.detail-value {
		font-size: 0.75rem;
		font-weight: 600;
		color: #0f1724;
		line-height: 1.2;
	}

	.card-footer {
		padding: 12px;
		border-top: 1px solid #e1e4ed;
		background: #f8f9fa;
		display: flex;
		justify-content: flex-end;
	}

	.card-action-btn {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 6px 12px;
		border: 1px solid #e1e4ed;
		background: #ffffff;
		color: #ef4444;
		border-radius: 4px;
		font-size: 0.75rem;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s ease;
	}

	.card-action-btn:hover {
		background: #ef4444;
		color: white;
		border-color: #ef4444;
		transform: translateY(-1px);
		box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
	}

	.card-action-btn i {
		font-size: 0.7rem;
	}

	.pagination-container {
		background: #f8f9fa;
		border-top: 1px solid #e1e4ed;
		padding: 12px 16px;
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
		width: 24px;
		height: 24px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 4px;
		border: 1px solid #d1d5db;
		background: #ffffff;
		color: #0f1724;
		font-size: 0.7rem;
		cursor: pointer;
		transition: all 0.2s;
	}

	.pagination-btn:hover:not(:disabled) {
		background: #f3f4f6;
		border-color: #10B981;
		color: #10B981;
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
		min-width: 24px;
		height: 24px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 4px;
		border: 1px solid transparent;
		background: #ffffff;
		color: #0f1724;
		font-size: 0.7rem;
		cursor: pointer;
		transition: all 0.2s;
	}

	.page-number:hover {
		background: #f3f4f6;
		border-color: #d1d5db;
	}

	.page-number.active {
		background: #10B981;
		color: white;
		border-color: #10B981;
	}

	.pagination-info {
		font-size: 0.7rem;
		color: #6b7280;
		margin-left: 12px;
	}

	@media (max-width: 1200px) {
		.cards-grid {
			grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
			gap: 12px;
			padding: 16px;
		}
	}

	@media (max-width: 992px) {
		.cards-grid {
			grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
			gap: 10px;
			padding: 14px;
		}

		.card-header {
			padding: 10px;
			gap: 10px;
		}

		.product-image-container {
			width: 45px;
			height: 45px;
		}

		.product-title {
			font-size: 0.85rem;
		}
	}

	@media (max-width: 768px) {
		.cards-grid {
			grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
			gap: 8px;
			padding: 12px;
		}

		.card-body {
			padding: 10px;
		}

		.card-details-grid {
			grid-template-columns: 1fr;
			gap: 8px;
		}
	}

	@media (max-width: 600px) {
		.cards-grid {
			grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
			gap: 8px;
			padding: 10px;
		}

		.request-card {
			min-height: 280px;
		}

		.card-header {
			flex-direction: column;
			text-align: center;
			gap: 8px;
			padding: 10px;
		}

		.product-image-container {
			width: 60px;
			height: 60px;
			margin: 0 auto;
		}
	}

	@media (max-width: 480px) {
		.cards-grid {
			grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
			gap: 6px;
			padding: 8px;
		}

		.request-card {
			min-height: 260px;
		}

		.product-title {
			font-size: 0.8rem;
		}

		.card-action-btn span {
			display: none;
		}

		.card-action-btn {
			padding: 6px;
			justify-content: center;
			width: 100%;
		}
	}
</style>

<div class="pagination-container">
	<div class="pagination-wrapper">
		<button class="pagination-btn pagination-prev {{ $buyerRequests->onFirstPage() ? 'disabled' : '' }}"
				{{ $buyerRequests->onFirstPage() ? 'disabled' : '' }}>
			<i class="fas fa-chevron-left fa-xs"></i>
		</button>

		<div class="pagination-pages">
			@php
				$currentPage = $buyerRequests->currentPage();
				$lastPage = $buyerRequests->lastPage();
				$startPage = max(1, $currentPage - 2);
				$endPage = min($lastPage, $currentPage + 2);

				if($startPage > 1) {
					echo '<span class="page-number">1</span>';
					if($startPage > 2) echo '<span class="page-dots">...</span>';
				}

				for($i = $startPage; $i <= $endPage; $i++) {
					$activeClass = $i == $currentPage ? 'active' : '';
					echo '<span class="page-number ' . $activeClass . ' pagination-link" data-page="' . $i . '">' . $i . '</span>';
				}

				if($endPage < $lastPage) {
					if($endPage < $lastPage - 1) echo '<span class="page-dots">...</span>';
					echo '<span class="page-number pagination-link" data-page="' . $lastPage . '">' . $lastPage . '</span>';
				}
			@endphp
		</div>

		<button class="pagination-btn pagination-next {{ $buyerRequests->hasMorePages() ? '' : 'disabled' }}"
				data-total="{{ $buyerRequests->lastPage() }}"
				{{ $buyerRequests->hasMorePages() ? '' : 'disabled' }}>
			<i class="fas fa-chevron-right fa-xs"></i>
		</button>

		<div class="pagination-info">
			Showing {{ $buyerRequests->firstItem() ?? 0 }}-{{ $buyerRequests->lastItem() ?? 0 }} of {{ $buyerRequests->total() }} requests
		</div>
	</div>
</div>
@else
<div class="empty-state">
	<i class="fas fa-inbox"></i>
	<h3>No Buyer Requests Found</h3>
	<p>There are no product requests matching your search criteria.</p>
</div>
@endif

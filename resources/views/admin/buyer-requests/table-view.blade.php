@if($buyerRequests->count() > 0)
<div class="table-responsive">
    <table class="buyer-requests-table">
        <thead>
            <tr>
                <th style="width: 30px;"></th>
                <th>Product</th>
                <th>Buyer</th>
                <th>Quantity</th>
                <th>Needed Date</th>
                <th>Price</th>
                <th style="width: 60px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($buyerRequests as $request)
            <tr class="request-row" data-id="{{ $request->id }}">
                <td class="product-image-cell">
                    <div class="product-image-wrapper">
                        <img src="{{ $request->image_url }}"
                             alt="{{ $request->product_name }}"
                             class="product-image"
                             onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
                    </div>
                </td>
                <td class="product-info">
                    <div class="product-name">{{ $request->product_name }}</div>
                    @if($request->description)
                    <div class="product-description">{{ Str::limit($request->description, 50) }}</div>
                    @endif
                </td>
                <td class="buyer-info">
                    <div class="buyer-name">{{ $request->buyer->name }}</div>
                    @if($request->buyer->business_name)
                    <div class="business-name">{{ $request->buyer->business_name }}</div>
                    @endif
                </td>
                <td class="quantity-info">
                    {{ $request->formatted_quantity }}
                </td>
                <td class="date-info">
                    {{ $request->formatted_date }}
                </td>
                <td class="price-info">
                    {{ $request->formatted_price }}
                </td>
                <td class="actions-cell">
                    <button type="button"
                            class="action-btn delete-btn"
                            data-id="{{ $request->id }}"
                            data-product="{{ $request->product_name }}"
                            title="Delete Request">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
    .table-responsive {
        overflow-x: auto;
    }

    .buyer-requests-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.7rem;
    }

    .buyer-requests-table thead th {
        background: #f8f9fa;
        padding: 6px 8px;
        text-align: left;
        font-weight: 600;
        color: #0f1724;
        border-bottom: 1px solid #e1e4ed;
        height: 28px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .buyer-requests-table tbody tr {
        height: 36px;
        transition: all 0.2s ease;
        border-bottom: 1px solid #e1e4ed;
    }

    .buyer-requests-table tbody tr:hover {
        background: #f8f9fa;
        transform: translateX(1px);
    }

    .buyer-requests-table td {
        padding: 5px 8px;
        vertical-align: middle;
    }

    .product-image-cell {
        padding-right: 0;
    }

    .product-image-wrapper {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        overflow: hidden;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .request-row:hover .product-image {
        transform: scale(1.05);
    }

    .product-info {
        min-width: 150px;
        max-width: 200px;
    }

    .product-name {
        font-weight: 600;
        color: #0f1724;
        margin-bottom: 1px;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    .product-description {
        color: #6b7280;
        font-size: 0.65rem;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .buyer-info {
        min-width: 120px;
    }

    .buyer-name {
        font-weight: 500;
        color: #0f1724;
        margin-bottom: 1px;
        font-size: 0.75rem;
    }

    .business-name {
        color: #10B981;
        font-size: 0.65rem;
        background: rgba(16, 185, 129, 0.1);
        padding: 1px 4px;
        border-radius: 2px;
        display: inline-block;
    }

    .quantity-info {
        font-weight: 600;
        color: #0f1724;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .date-info {
        color: #6b7280;
        font-size: 0.7rem;
        white-space: nowrap;
    }

    .price-info {
        font-weight: 600;
        color: #10B981;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .actions-cell {
        text-align: center;
    }

    .action-btn {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 1px solid #e1e4ed;
        background: #ffffff;
        color: #ef4444;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 0.6rem;
    }

    .action-btn:hover {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
        transform: scale(1.05);
        box-shadow: 0 1px 4px rgba(239, 68, 68, 0.2);
    }

    .pagination-container {
        background: #f8f9fa;
        border-top: 1px solid #e1e4ed;
        padding: 8px 12px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .pagination-wrapper {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pagination-btn {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #0f1724;
        font-size: 0.6rem;
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
        gap: 3px;
    }

    .page-number {
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
        border: 1px solid transparent;
        background: #ffffff;
        color: #0f1724;
        font-size: 0.6rem;
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
        font-size: 0.6rem;
        color: #6b7280;
        margin-left: 8px;
    }

    @media (max-width: 992px) {
        .buyer-requests-table {
            font-size: 0.65rem;
        }

        .buyer-requests-table td {
            padding: 4px 6px;
        }

        .product-info {
            min-width: 140px;
        }
    }

    @media (max-width: 768px) {
        .buyer-requests-table thead th:nth-child(4),
        .buyer-requests-table td:nth-child(4),
        .buyer-requests-table thead th:nth-child(5),
        .buyer-requests-table td:nth-child(5) {
            display: none;
        }

        .product-info {
            min-width: 130px;
        }
    }

    @media (max-width: 600px) {
        .buyer-requests-table {
            display: none;
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

@extends('buyer.layouts.buyer_master')

@section('title', 'Unpaid Delivery Orders')
@section('page-title', 'Unpaid Delivery Orders')

@section('styles')
<style>
    :root {
        --primary-green: #10b981;
        --secondary-green: #059669;
        --glass-bg: rgba(255, 255, 255, 0.9);
        --glass-border: rgba(16, 185, 129, 0.2);
    }

    .unpaid-container {
        padding: 20px;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .order-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        margin-bottom: 25px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .order-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(16, 185, 129, 0.1);
    }

    .farmer-header {
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        padding: 15px 25px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .farmer-info h4 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .order-meta {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .product-list {
        padding: 20px;
    }

    .product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #eee;
    }

    .product-item:last-child {
        border-bottom: none;
    }

    .product-details h6 {
        margin: 0;
        color: #333;
    }

    .product-details small {
        color: #666;
    }

    .item-total-price {
        font-weight: 600;
        color: var(--primary-green);
    }

    .order-footer {
        padding: 20px 25px;
        background: rgba(16, 185, 129, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .total-due {
        display: flex;
        flex-direction: column;
    }

    .total-due span:first-child {
        font-size: 0.8rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .total-due .amount {
        font-size: 1.5rem;
        font-weight: 800;
        color: #2d3748;
    }

    .action-btns {
        display: flex;
        gap: 12px;
    }

    .btn-bank {
        background: white;
        color: var(--primary-green);
        border: 2px solid var(--primary-green);
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-bank:hover {
        background: var(--primary-green);
        color: white;
    }

    .btn-upload {
        background: var(--primary-green);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        transition: all 0.3s ease;
    }

    .btn-upload:hover {
        background: var(--secondary-green);
        transform: scale(1.05);
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-icon {
        font-size: 5rem;
        color: #cbd5e0;
        margin-bottom: 20px;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 24px;
        border: none;
        overflow: hidden;
    }

    .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid #edf2f7;
        padding: 20px 30px;
    }

    .modal-body {
        padding: 30px;
    }

    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
    }

    .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .drop-zone {
        border: 2px dashed #cbd5e0;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .drop-zone:hover {
        border-color: var(--primary-green);
        background: rgba(16, 185, 129, 0.02);
    }

    .bg-soft-success {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .ls-1 {
        letter-spacing: 1px;
    }

    .custom-toast {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background: #333;
        color: white;
        padding: 10px 20px;
        border-radius: 30px;
        z-index: 10001;
        transition: transform 0.3s ease;
        font-size: 0.9rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .custom-toast.show {
        transform: translateX(-50%) translateY(0);
    }

    /* Search Container */
    .search-container {
        display: flex;
        align-items: center;
        border: 2px solid #61e6b9ff;
        border-radius: 25px;
        overflow: hidden;
        background: white;
        width: 100%;
        box-shadow: 0 4px 15px rgba(97, 230, 185, 0.1);
        transition: all 0.3s ease;
    }

    .search-container:focus-within {
        box-shadow: 0 4px 20px rgba(97, 230, 185, 0.2);
        transform: translateY(-2px);
    }

    /* Input */
    .search-container input {
        border: none;
        outline: none;
        padding: 10px 15px;
        font-size: 14px;
        width: 100%;
    }

    /* Button */
    .search-container button {
        border: none;
        background: #61e6b9ff;
        color: white;
        padding: 10px 18px;
        cursor: pointer;
        font-size: 14px;
        transition: 0.2s;
    }

    /* Hover */
    .search-container button:hover {
        background: #10b981;
    }

    .filter-wrapper {
        margin-bottom: 40px;
    }

    .filter-title {
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        margin-left: 15px;
    }

    .no-results {
        text-align: center;
        padding: 60px 20px;
        display: none;
    }
</style>
@endsection

@section('content')
<div class="unpaid-container">
    @if(empty($orders))
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <h3>No pending payments</h3>
            <p class="text-muted">You don't have any unpaid delivery orders at the moment.</p>
            <a href="{{ route('buyer.browseProducts') }}" class="btn btn-primary mt-3">
                <i class="fas fa-shopping-bag me-2"></i> Start Shopping
            </a>
        </div>
    @else
        <div class="filter-wrapper">
            <div class="row g-4">
                <div class="col-lg-8">
                    <span class="filter-title"><i class="fas fa-search me-1"></i> Smart Search</span>
                    <div class="search-container">
                        <input type="text" id="smartSearch" placeholder="Search by Order ID (ORD-...) or Product Name..." onkeyup="filterOrders()">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="col-lg-4">
                    <span class="filter-title"><i class="fas fa-calendar-alt me-1"></i> Filter by Date</span>
                    <div class="search-container">
                        <input type="date" id="dateFilter" onchange="filterOrders()" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        <button><i class="fas fa-filter"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div id="orders-list">
            @foreach($orders as $baseOrderNumber => $order)
                <div class="order-group mb-5 animate__animated animate__fadeIn" 
                     data-main-order-id="{{ $baseOrderNumber }}" 
                     data-date="{{ \Carbon\Carbon::parse($order['created_at'])->format('Y-m-d') }}">
                    <div class="col-12 col-xl-10 mx-auto">
                    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
                        <div>
                            <small class="text-muted text-uppercase fw-bold ls-1 d-block" style="font-size: 0.7rem;">Main Order Number</small>
                            <h3 class="mb-0 fw-bold" style="color: #1e293b; letter-spacing: -0.5px;">{{ $baseOrderNumber }}</h3>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-soft-success text-success rounded-pill px-3 py-2">
                                <i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        @foreach($order['farmer_groups'] as $farmerId => $group)
                            <div class="col-12 mb-4 order-card-wrapper" 
                                 data-sub-order-id="{{ $group['order_number'] }}" 
                                 data-products="{{ collect($group['items'])->pluck('product_name_snapshot')->implode(', ') }}">
                                <div class="order-card">
                                    <div class="farmer-header">
                                        <div class="farmer-info">
                                            <h4><i class="fas fa-hashtag me-1"></i> {{ $group['order_number'] }}</h4>
                                            <div class="order-meta">  
                                                <span><i class="fas fa-clock me-1"></i> {{ \Carbon\Carbon::parse($order['created_at'])->format('h:i A') }}</span>
                                            </div>
                                        </div>
                                        <div class="badge bg-white text-dark rounded-pill px-3">Section: Farmer #{{ $loop->iteration }}</div>
                                    </div>

                                    <div class="product-list">
                                        @foreach($group['items'] as $item)
                                            <div class="product-item">
                                                <div class="product-details">
                                                    <h6>{{ $item->product_name_snapshot }}</h6>
                                                    <small>{{ number_format($item->quantity_ordered, 2) }} x Rs. {{ number_format($item->unit_price_snapshot, 2) }}</small>
                                                </div>
                                                <div class="item-total-price">
                                                    Rs. {{ number_format($item->item_total, 2) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="order-footer">
                                        <div class="total-due">
                                            <span>Subtotal for this seller</span>
                                            <div class="amount">Rs. {{ number_format($group['total'], 2) }}</div>
                                        </div>
                                        <div class="action-btns">
                                            <button class="btn btn-bank" onclick="showBankDetails({{ json_encode($group['lead_farmer']) }}, '{{ $group['order_number'] }}', '{{ \Carbon\Carbon::parse($order['created_at'])->toIso8601String() }}', '{{ $group['order_id'] }}', {{ $group['total'] }})">
                                                <i class="fas fa-university me-2"></i> Bank Details
                                            </button>
                                            <button class="btn btn-upload" onclick="openUploadModal('{{ $group['order_id'] }}', '{{ $group['order_number'] }}', {{ $group['total'] }})">
                                                <i class="fas fa-file-upload me-2"></i> Upload Slip
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div id="no-results" class="no-results">
            <div class="empty-icon">
                <i class="fas fa-search-minus"></i>
            </div>
            <h3>No matching orders found</h3>
            <p class="text-muted">Try adjusting your search terms or date filter.</p>
            <button class="btn btn-link text-success fw-bold" onclick="resetFilters()">Reset All Filters</button>
        </div>
    @endif
</div>

<!-- Upload Slip Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-cloud-upload-alt me-2 text-primary"></i> Upload Payment Slip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="order_id" id="modal_order_id">
                <div class="modal-body">
                    <div class="alert alert-info mb-4" style="border-radius: 12px; background: #ebf8ff; border: 1px solid #bee3f8; color: #2b6cb0;">
                        <i class="fas fa-info-circle me-2"></i> 
                        Uploading slip for <strong><span id="modal_order_number"></span></strong> 
                        (Amount: <strong>Rs. <span id="modal_amount"></span></strong>)
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction ID / Reference</label>
                        <input type="text" name="transaction_id" class="form-control" placeholder="Enter bank transaction reference number" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Transaction Date</label>
                            <input type="date" name="transaction_date" class="form-control" required max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Transaction Time</label>
                            <input type="time" name="transaction_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Slip Image</label>
                        <div class="drop-zone" id="dropZone" onclick="document.getElementById('slipInput').click()">
                            <i class="fas fa-image"></i>
                            <p id="dropZoneText">Click or Drag & Drop payment slip here</p>
                            <small class="text-muted">JPG, PNG, or PDF allowed (Max 5MB)</small>
                            <input type="file" name="payment_slip" id="slipInput" accept="image/*,.pdf" style="display: none" required>
                        </div>
                        <div id="imagePreview" class="mt-3 text-center" style="display: none">
                            <img id="previewImg" src="#" alt="Preview" style="max-width: 100%; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #edf2f7; padding: 20px 30px;">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-upload px-5">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterOrders() {
        const searchTerm = document.getElementById('smartSearch').value.toLowerCase().trim();
        const dateTerm = document.getElementById('dateFilter').value;
        const groups = document.querySelectorAll('.order-group');
        const noResults = document.getElementById('no-results');
        let anyVisible = false;

        groups.forEach(group => {
            const mainOrderId = group.getAttribute('data-main-order-id').toLowerCase();
            const groupDate = group.getAttribute('data-date');
            const cards = group.querySelectorAll('.order-card-wrapper');
            
            let groupVisible = false;
            
            // Check if search matches main order ID
            const mainIdMatch = mainOrderId.includes(searchTerm);
            // Check if date matches
            const dateMatch = !dateTerm || groupDate === dateTerm;

            cards.forEach(card => {
                const subOrderId = card.getAttribute('data-sub-order-id').toLowerCase();
                const products = card.getAttribute('data-products').toLowerCase();
                
                // Card is visible if (main ID or sub ID or product name matches search) AND date matches
                const searchMatch = mainIdMatch || subOrderId.includes(searchTerm) || products.includes(searchTerm);
                
                if (searchMatch && dateMatch) {
                    card.style.display = 'block';
                    groupVisible = true;
                } else {
                    card.style.display = 'none';
                }
            });

            if (groupVisible) {
                group.style.display = 'block';
                anyVisible = true;
            } else {
                group.style.display = 'none';
            }
        });

        noResults.style.display = anyVisible ? 'none' : 'block';
    }

    function resetFilters() {
        document.getElementById('smartSearch').value = '';
        document.getElementById('dateFilter').value = '';
        filterOrders();
    }

    let countdownInterval;

    function updateCountdown(createdAt, orderId, orderNumber) {
        const deadline = new Date(new Date(createdAt).getTime() + 24 * 60 * 60 * 1000);
        const now = new Date();
        const diff = deadline - now;
        
        const countdownEl = document.getElementById('payment-countdown');
        if (!countdownEl) return;
        
        if (diff <= 0) {
            countdownEl.innerText = "Expired";
            countdownEl.classList.add('text-danger');
            clearInterval(countdownInterval);
            deleteExpiredOrderAJAX(orderId);
            return;
        }
        
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        countdownEl.innerText = `${hours}h ${minutes}m ${seconds}s`;

        // Trigger SMS at 1:30:00 (5400 seconds remaining)
        const diffSeconds = Math.floor(diff / 1000);
        if (diffSeconds > 5395 && diffSeconds <= 5400 && !countdownEl.dataset.smsTriggered) {
            countdownEl.dataset.smsTriggered = 'true';
            triggerSmsNotification(orderId, orderNumber);
        }
    }

    async function deleteExpiredOrderAJAX(orderId) {
        try {
            const response = await fetch('{{ route("buyer.deleteExpiredOrder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ order_id: orderId })
            });
            const data = await response.json();
            if (data.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Order Expired',
                    text: 'The payment deadline has passed. This order has been removed.',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    location.reload();
                });
            }
        } catch (error) {
            console.error('AJAX deletion failed:', error);
        }
    }

    async function triggerSmsNotification(orderId, orderNumber) {
        try {
            const response = await fetch('{{ route("buyer.sendUnpaidOrderSMS") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    order_id: orderId,
                    order_number: orderNumber
                })
            });
            const data = await response.json();
            console.log('SMS notification status:', data.message);
        } catch (error) {
            console.error('SMS trigger failed:', error);
        }
    }

    function showToast(message) {
        let toast = document.getElementById('custom-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'custom-toast';
            toast.className = 'custom-toast';
            document.body.appendChild(toast);
        }
        toast.innerText = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    function showBankDetails(farmer, orderNumber, createdAt, orderId, amount) {
        if (!farmer) {
            Swal.fire({
                title: 'Error',
                text: 'Seller bank details not available.',
                @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'Error' @endif,
            });
            return;
        }
        const bankName = farmer.bank_name || 'N/A';
        const accHolder = farmer.account_holder_name || 'N/A';
        const accNumber = farmer.account_number || 'N/A';
        const branch = farmer.bank_branch || 'N/A';

        Swal.fire({
            title: '<h4 class="fw-bold mb-0">Bank Transfer Details</h4>',
            html: `
                <div class="text-start mt-3 p-3 bg-light rounded-4">
                    <div class="mb-3 pb-2 border-bottom">
                        <small class="text-muted d-block text-uppercase small ls-1">Lead Farmer / Beneficiary</small>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">${farmer.name}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase small ls-1">Bank Name</small>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">${bankName}</span>
                            <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="copyToClipboard('${bankName.replace(/'/g, "\\'")}', this)"><i class="far fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase small ls-1">Account Holder</small>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">${accHolder}</span>
                            <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="copyToClipboard('${accHolder.replace(/'/g, "\\'")}', this)"><i class="far fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase small ls-1">Account Number</small>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-success" style="font-size: 1.2rem; letter-spacing: 1px;">${accNumber}</span>
                            <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="copyToClipboard('${accNumber.replace(/'/g, "\\'")}', this)"><i class="far fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase small ls-1">Branch</small>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">${branch}</span>
                            <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="copyToClipboard('${branch.replace(/'/g, "\\'")}', this)"><i class="far fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="mb-1">
                        <small class="text-muted d-block text-uppercase small ls-1">Reference Instruction</small>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">${orderNumber}</span>
                            <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="copyToClipboard('${orderNumber}', this)"><i class="far fa-copy"></i></button>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning mt-3 small text-start" style="border-radius: 12px">
                    <i class="fas fa-clock me-2"></i> <strong>Deadline:</strong> Do the payment within <span id="payment-countdown" class="fw-bold text-danger">--h --m --s</span>.<br>
                    <i class="fas fa-coins me-2"></i> <strong>Note:</strong> Pay the exact amount only (Rs. ${parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}). <br>
                    <i class="fas fa-ban me-2"></i> <strong>Note:</strong> Avoid overpayment or underpayment.
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Got it',
            confirmButtonColor: '#10b981',
            customClass: {
                popup: 'swal-popup-compact',
                title: 'text-start ps-4 pt-4'
            },
            didOpen: () => {
                updateCountdown(createdAt, orderId, orderNumber);
                clearInterval(countdownInterval);
                countdownInterval = setInterval(() => updateCountdown(createdAt, orderId, orderNumber), 1000);
            },
            willClose: () => {
                clearInterval(countdownInterval);
            }
        });
    }

    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.add('text-success');
            btn.classList.remove('btn-outline-primary');
            
            showToast('Copied to clipboard');
            
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('text-success');
                btn.classList.add('btn-outline-primary');
            }, 2000);
        });
    }

    function openUploadModal(orderId, orderNumber, amount) {
        document.getElementById('modal_order_id').value = orderId;
        document.getElementById('modal_order_number').innerText = orderNumber;
        document.getElementById('modal_amount').innerText = amount.toLocaleString('en-US', {minimumFractionDigits: 2});
        
        // Reset form
        document.getElementById('uploadForm').reset();
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('dropZoneText').innerText = 'Click or Drag & Drop payment slip here';
        
        const myModal = new bootstrap.Modal(document.getElementById('uploadModal'));
        myModal.show();
    }

    // Image preview logic
    document.getElementById('slipInput').onchange = evt => {
        const [file] = evt.target.files;
        if (file) {
            if (file.type.startsWith('image/')) {
                document.getElementById('previewImg').src = URL.createObjectURL(file);
                document.getElementById('imagePreview').style.display = 'block';
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
            document.getElementById('dropZoneText').innerText = file.name;
        }
    }

    // Form submission
    document.getElementById('uploadForm').onsubmit = async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

        try {
            const response = await fetch('{{ route("buyer.uploadPaymentSlip") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Submitted!',
                    text: data.message,
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: data.message || 'Something went wrong',
                    confirmButtonColor: '#ef4444'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Submit Payment';
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'A network error occurred. Please try again.',
                confirmButtonColor: '#ef4444'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Payment';
        }
    };
</script>
@endsection

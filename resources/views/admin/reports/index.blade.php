@extends('admin.layouts.admin_master')

@section('title', 'Reports Dashboard')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2><i class="fas fa-chart-bar"></i> Reports Dashboard</h2>
        <p>Generate and view system reports</p>
    </div>
</div>

<div class="reports-container">
    <div class="filter-section">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchReports" placeholder="Search reports...">
        </div>
        <div class="filter-actions">
            <button class="btn-filter active" data-filter="all">All</button>
            <button class="btn-filter" data-filter="financial">Financial</button>
            <button class="btn-filter" data-filter="sales">Sales</button>
            <button class="btn-filter" data-filter="inventory">Inventory</button>
            <button class="btn-filter" data-filter="users">Users</button>
            <button class="btn-filter" data-filter="cod">COD</button>
        </div>
    </div>

    <div class="reports-grid" id="reportsGrid">
        @php
            $reports = [
                [
                    'id' => 'order-history',
                    'title' => 'Order History',
                    'description' => 'All orders with status and details',
                    'category' => 'sales',
                    'icon' => 'fas fa-history',
                    'color' => 'var(--blue)'
                ],
                [
                    'id' => 'pending-pickup',
                    'title' => 'Pending Pickup',
                    'description' => 'Orders awaiting buyer pickup',
                    'category' => 'sales',
                    'icon' => 'fas fa-truck-loading',
                    'color' => 'var(--accent-amber)'
                ],
                [
                    'id' => 'sales-volume',
                    'title' => 'Sales Volume',
                    'description' => 'Sales performance over time',
                    'category' => 'sales',
                    'icon' => 'fas fa-chart-line',
                    'color' => 'var(--primary-accent)'
                ],
                [
                    'id' => 'sales-payment',
                    'title' => 'Sales Reconciliation',
                    'description' => 'Sales vs payments reconciliation',
                    'category' => 'financial',
                    'icon' => 'fas fa-money-check-alt',
                    'color' => 'var(--purple)'
                ],
                [
                    'id' => 'system-financial',
                    'title' => 'Financial Summary',
                    'description' => 'System financial snapshot',
                    'category' => 'financial',
                    'icon' => 'fas fa-file-invoice-dollar',
                    'color' => 'var(--blue)'
                ],
                [
                    'id' => 'daily-cash',
                    'title' => 'Daily Cash',
                    'description' => 'Cash inflow and COD amounts',
                    'category' => 'cod',
                    'icon' => 'fas fa-cash-register',
                    'color' => 'var(--accent-amber)'
                ],
                [
                    'id' => 'cash-collection-delay',
                    'title' => 'Collection Delay',
                    'description' => 'Delayed cash collection tracking',
                    'category' => 'cod',
                    'icon' => 'fas fa-clock',
                    'color' => 'var(--purple)'
                ],
                [
                    'id' => 'cod-exception',
                    'title' => 'COD Exception',
                    'description' => 'Cash payment anomalies',
                    'category' => 'cod',
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => '#ef4444'
                ],
                [
                    'id' => 'inventory-stock',
                    'title' => 'Inventory Stock',
                    'description' => 'Available products overview',
                    'category' => 'inventory',
                    'icon' => 'fas fa-boxes',
                    'color' => 'var(--primary-accent)'
                ],
                [
                    'id' => 'category-performance',
                    'title' => 'Category Performance',
                    'description' => 'Category sales analysis',
                    'category' => 'inventory',
                    'icon' => 'fas fa-chart-pie',
                    'color' => 'var(--blue)'
                ],
                [
                    'id' => 'stock-movement',
                    'title' => 'Stock Movement',
                    'description' => 'Inventory changes over time',
                    'category' => 'inventory',
                    'icon' => 'fas fa-warehouse',
                    'color' => 'var(--accent-amber)'
                ],
                [
                    'id' => 'group-performance',
                    'title' => 'Group Performance',
                    'description' => 'Lead Farmer groups analysis',
                    'category' => 'users',
                    'icon' => 'fas fa-users',
                    'color' => 'var(--purple)'
                ],
                [
                    'id' => 'farmer-registration',
                    'title' => 'Farmer Registration',
                    'description' => 'New farmers tracking',
                    'category' => 'users',
                    'icon' => 'fas fa-user-plus',
                    'color' => 'var(--primary-accent)'
                ],
                [
                    'id' => 'system-adoption',
                    'title' => 'System Adoption',
                    'description' => 'User adoption metrics',
                    'category' => 'users',
                    'icon' => 'fas fa-chart-user',
                    'color' => 'var(--blue)'
                ],
                [
                    'id' => 'user-access',
                    'title' => 'User Access',
                    'description' => 'Access patterns monitoring',
                    'category' => 'users',
                    'icon' => 'fas fa-user-shield',
                    'color' => 'var(--accent-amber)'
                ],
                [
                    'id' => 'data-quality',
                    'title' => 'Data Quality',
                    'description' => 'Missing fields identification',
                    'category' => 'users',
                    'icon' => 'fas fa-database',
                    'color' => 'var(--purple)'
                ],
                [
                    'id' => 'dispute-feedback',
                    'title' => 'Dispute Log',
                    'description' => 'Complaints tracking',
                    'category' => 'users',
                    'icon' => 'fas fa-comments',
                    'color' => '#ef4444'
                ],
                [
                    'id' => 'regional-cod',
                    'title' => 'Regional Performance',
                    'description' => 'Geographical analysis',
                    'category' => 'sales',
                    'icon' => 'fas fa-map-marked-alt',
                    'color' => 'var(--primary-accent)'
                ],
                [
                    'id' => 'quality-grade',
                    'title' => 'Quality Grade',
                    'description' => 'Grade performance analysis',
                    'category' => 'inventory',
                    'icon' => 'fas fa-star',
                    'color' => 'var(--accent-amber)'
                ],
                [
                    'id' => 'order-fulfillment',
                    'title' => 'Fulfillment Timeline',
                    'description' => 'Order fulfillment tracking',
                    'category' => 'sales',
                    'icon' => 'fas fa-calendar-check',
                    'color' => 'var(--blue)'
                ],
                [
                    'id' => 'financial-audit',
                    'title' => 'Financial Audit',
                    'description' => 'Transaction audit report',
                    'category' => 'financial',
                    'icon' => 'fas fa-search-dollar',
                    'color' => 'var(--purple)'
                ],
                [
                    'id' => 'inventory-cash-reconciliation',
                    'title' => 'Inventory vs Cash',
                    'description' => 'Inventory-cash reconciliation',
                    'category' => 'financial',
                    'icon' => 'fas fa-balance-scale',
                    'color' => 'var(--primary-accent)'
                ],
                [
                    'id' => 'farmer-payment-delay',
                    'title' => 'Payment Delay Risk',
                    'description' => 'Farmer payment delays',
                    'category' => 'cod',
                    'icon' => 'fas fa-exclamation-circle',
                    'color' => '#ef4444'
                ],
                [
                    'id' => 'geographic-sales',
                    'title' => 'Geographic Sales',
                    'description' => 'Regional sales density',
                    'category' => 'sales',
                    'icon' => 'fas fa-globe-asia',
                    'color' => 'var(--accent-amber)'
                ],
                [
                    'id' => 'buyer-payment-behavior',
                    'title' => 'Payment Behavior',
                    'description' => 'Buyer payment patterns',
                    'category' => 'financial',
                    'icon' => 'fas fa-user-clock',
                    'color' => 'var(--blue)'
                ],
                [
                    'id' => 'product-taxonomy',
                    'title' => 'Product Taxonomy',
                    'description' => 'Category performance',
                    'category' => 'inventory',
                    'icon' => 'fas fa-tags',
                    'color' => 'var(--purple)'
                ],
                [
                    'id' => 'cod-payment',
                    'title' => 'COD Payment',
                    'description' => 'COD payment tracking',
                    'category' => 'cod',
                    'icon' => 'fas fa-file-invoice',
                    'color' => 'var(--primary-accent)'
                ],
                [
                    'id' => 'cod-revenue',
                    'title' => 'COD Revenue Forecast',
                    'description' => 'COD revenue analysis',
                    'category' => 'cod',
                    'icon' => 'fas fa-chart-area',
                    'color' => 'var(--accent-amber)'
                ]
            ];
        @endphp

        @foreach($reports as $report)
        <div class="report-card" data-category="{{ $report['category'] }}" data-title="{{ strtolower($report['title']) }}">
            <div class="card-header" style="background: {{ $report['color'] }}15; border-left: 3px solid {{ $report['color'] }};">
                <div class="header-icon" style="color: {{ $report['color'] }};">
                    <i class="{{ $report['icon'] }}"></i>
                </div>
                <h3>{{ $report['title'] }}</h3>
                <span class="badge" style="background: {{ $report['color'] }};">{{ ucfirst($report['category']) }}</span>
            </div>
            <div class="card-body">
                <p>{{ $report['description'] }}</p>
                <div class="card-stats">
                    <div class="stat">
                        <i class="fas fa-file-pdf"></i>
                        <span>PDF</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-chart-bar"></i>
                        <span>Charts</span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn-view" onclick="viewReport('{{ $report['id'] }}')">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn-download" onclick="downloadPDF('{{ $report['id'] }}')">
                    <i class="fas fa-download"></i> PDF
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <div class="no-results" id="noResults" style="display: none;">
        <i class="fas fa-search"></i>
        <h3>No Reports Found</h3>
        <p>Try adjusting your search or filter</p>
    </div>
</div>

<div class="modal" id="dateFilterModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-filter"></i> Filter Report Data</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="dateFilterForm">
                <input type="hidden" id="selectedReportId">
                <div class="form-group">
                    <label for="from_date"><i class="fas fa-calendar-alt"></i> From Date</label>
                    <input type="date" id="from_date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                </div>
                <div class="form-group">
                    <label for="to_date"><i class="fas fa-calendar-alt"></i> To Date</label>
                    <input type="date" id="to_date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-file-export"></i> Output Format</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="format" value="view" checked>
                            <span>View in Browser</span>
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="format" value="pdf">
                            <span>Download PDF</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-generate" onclick="generateReport()">
                <i class="fas fa-play"></i> Generate
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentReportId = null;

function viewReport(reportId) {
    currentReportId = reportId;
    document.getElementById('selectedReportId').value = reportId;
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('from_date').max = today;
    document.getElementById('to_date').max = today;
    document.getElementById('dateFilterModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('dateFilterModal').style.display = 'none';
}

function generateReport() {
    const reportId = document.getElementById('selectedReportId').value;
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    const format = document.querySelector('input[name="format"]:checked').value;

    if (!reportId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Please select a report',
            confirmButtonColor: '#10B981',
            background: 'var(--card-bg)',
            color: 'var(--text-dark)'
        });
        return;
    }

    if (!fromDate || !toDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Please select both date ranges',
            confirmButtonColor: '#10B981',
            background: 'var(--card-bg)',
            color: 'var(--text-dark)'
        });
        return;
    }

    if (new Date(fromDate) > new Date(toDate)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Date',
            text: 'From date cannot be after to date',
            confirmButtonColor: '#10B981',
            background: 'var(--card-bg)',
            color: 'var(--text-dark)'
        });
        return;
    }

    if (format === 'pdf') {
        downloadPDF(reportId, fromDate, toDate);
    } else {
        window.location.href = `/admin/reports/view/${reportId}?from_date=${fromDate}&to_date=${toDate}`;
    }

    closeModal();
}

function downloadPDF(reportId, fromDate = null, toDate = null) {
    fromDate = fromDate || document.getElementById('from_date').value;
    toDate = toDate || document.getElementById('to_date').value;

    const url = `/admin/reports/pdf/${reportId}?from_date=${fromDate}&to_date=${toDate}`;
    window.open(url, '_blank');
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchReports');
    const filterButtons = document.querySelectorAll('.btn-filter');
    const reportCards = document.querySelectorAll('.report-card');
    const noResults = document.getElementById('noResults');

    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.max = today;
    });

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let visibleCount = 0;

        reportCards.forEach(card => {
            const title = card.getAttribute('data-title');
            const category = card.getAttribute('data-category');
            const currentFilter = document.querySelector('.btn-filter.active').getAttribute('data-filter');

            const matchesSearch = title.includes(searchTerm);
            const matchesFilter = currentFilter === 'all' || category === currentFilter;

            if (matchesSearch && matchesFilter) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            noResults.style.display = 'flex';
        } else {
            noResults.style.display = 'none';
        }
    });

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            let visibleCount = 0;
            const searchTerm = searchInput.value.toLowerCase();

            reportCards.forEach(card => {
                const title = card.getAttribute('data-title');
                const category = card.getAttribute('data-category');

                const matchesSearch = title.includes(searchTerm);
                const matchesFilter = filter === 'all' || category === filter;

                if (matchesSearch && matchesFilter) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                noResults.style.display = 'flex';
            } else {
                noResults.style.display = 'none';
            }
        });
    });

    document.addEventListener('click', function(event) {
        if (event.target.id === 'dateFilterModal') {
            closeModal();
        }
    });

    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');

    fromDateInput.addEventListener('change', function() {
        toDateInput.min = this.value;
    });

    toDateInput.addEventListener('change', function() {
        if (this.value < fromDateInput.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Date Error',
                text: 'To date cannot be before from date',
                confirmButtonColor: '#10B981',
                background: 'var(--card-bg)',
                color: 'var(--text-dark)'
            });
            this.value = fromDateInput.value;
        }
    });
});
</script>

<style>
.reports-container {
    padding: 12px;
    animation: fadeIn 0.3s ease;
}

.page-header {
    background: var(--card-bg);
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    box-shadow: var(--shadow-sm);
    border-left: 3px solid var(--primary-accent);
    transition: transform 0.3s ease;
}

.page-header:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.header-content h2 {
    color: var(--text-dark);
    font-size: 18px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.header-content p {
    color: var(--muted);
    font-size: 12px;
}

.filter-section {
    background: var(--card-bg);
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    box-shadow: var(--shadow-sm);
}

.search-box {
    position: relative;
    margin-bottom: 12px;
}

.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: 13px;
}

.search-box input {
    width: 100%;
    padding: 10px 12px 10px 36px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 13px;
    transition: all 0.3s ease;
    background: var(--card-bg);
    color: var(--text-dark);
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary-accent);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.filter-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.btn-filter {
    padding: 6px 12px;
    border: 1px solid #e5e7eb;
    background: var(--card-bg);
    color: var(--text-dark);
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-filter:hover {
    background: var(--primary-accent);
    color: white;
    border-color: var(--primary-accent);
    transform: translateY(-1px);
}

.btn-filter.active {
    background: var(--primary-accent);
    color: white;
    border-color: var(--primary-accent);
}

.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 16px;
}

.report-card {
    background: var(--card-bg);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    border: 1px solid #f3f4f6;
    animation: slideUp 0.4s ease;
    animation-fill-mode: both;
    display: flex;
    flex-direction: column;
}

.report-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-accent);
}

.report-card:nth-child(odd) {
    animation-delay: 0.1s;
}

.report-card:nth-child(even) {
    animation-delay: 0.2s;
}

.card-header {
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    position: relative;
}

.header-icon {
    font-size: 20px;
    background: white;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-xs);
    flex-shrink: 0;
}

.card-header h3 {
    flex: 1;
    font-size: 14px;
    color: var(--text-dark);
    margin: 0;
    line-height: 1.4;
    font-weight: 600;
}

.badge {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    flex-shrink: 0;
}

.card-body {
    padding: 0 16px 16px;
    flex: 1;
}

.card-body p {
    color: var(--muted);
    font-size: 12px;
    line-height: 1.5;
    margin-bottom: 12px;
}

.card-stats {
    display: flex;
    gap: 16px;
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
}

.stat {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    color: var(--muted);
}

.stat i {
    color: var(--primary-accent);
    font-size: 12px;
}

.card-footer {
    padding: 12px 16px;
    background: #f9fafb;
    border-top: 1px solid #f3f4f6;
    display: flex;
    gap: 8px;
}

.btn-view, .btn-download {
    flex: 1;
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-view {
    background: var(--primary-accent);
    color: white;
}

.btn-view:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.btn-download {
    background: var(--card-bg);
    color: var(--text-dark);
    border: 1px solid #e5e7eb;
}

.btn-download:hover {
    background: var(--primary-accent);
    color: white;
    border-color: var(--primary-accent);
    transform: translateY(-1px);
}

.no-results {
    text-align: center;
    padding: 48px 16px;
    background: var(--card-bg);
    border-radius: 8px;
    box-shadow: var(--shadow-sm);
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

.no-results i {
    font-size: 36px;
    color: var(--muted);
    margin-bottom: 16px;
}

.no-results h3 {
    color: var(--text-dark);
    margin-bottom: 8px;
    font-size: 16px;
    font-weight: 600;
}

.no-results p {
    color: var(--muted);
    font-size: 13px;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: fadeIn 0.3s ease;
    backdrop-filter: blur(4px);
}

.modal-content {
    background: var(--card-bg);
    border-radius: 8px;
    max-width: 420px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-md);
    animation: slideUp 0.3s ease;
}

.modal-header {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-header h3 {
    color: var(--text-dark);
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.close-modal {
    background: none;
    border: none;
    font-size: 20px;
    color: var(--muted);
    cursor: pointer;
    transition: color 0.3s ease;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
}

.close-modal:hover {
    color: var(--text-dark);
    background: #f3f4f6;
}

.modal-body {
    padding: 16px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    color: var(--text-dark);
    font-weight: 500;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 13px;
    transition: all 0.3s ease;
    background: var(--card-bg);
    color: var(--text-dark);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-accent);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-control:invalid {
    border-color: #ef4444;
}

.radio-group {
    display: flex;
    gap: 16px;
}

.radio-label {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    color: var(--text-dark);
    font-size: 13px;
}

.radio-label input[type="radio"] {
    margin: 0;
}

.modal-footer {
    padding: 16px;
    border-top: 1px solid #f3f4f6;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.btn-cancel, .btn-generate {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cancel {
    background: var(--card-bg);
    color: var(--text-dark);
    border: 1px solid #e5e7eb;
}

.btn-cancel:hover {
    background: #f3f4f6;
    transform: translateY(-1px);
}

.btn-generate {
    background: var(--primary-accent);
    color: white;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-generate:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (min-width: 1200px) {
    .reports-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 1199px) and (min-width: 992px) {
    .reports-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    .filter-actions {
        overflow-x: auto;
        padding-bottom: 8px;
    }
}

@media (max-width: 991px) and (min-width: 768px) {
    .reports-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }
    .modal-content {
        max-width: 90%;
    }
}

@media (max-width: 767px) {
    .reports-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }
    .filter-actions {
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .btn-filter {
        white-space: nowrap;
    }
    .modal-content {
        max-width: 95%;
    }
    .radio-group {
        flex-direction: column;
        gap: 8px;
    }
}

@media (max-width: 480px) {
    .reports-grid {
        grid-template-columns: 1fr;
    }
    .card-footer {
        flex-direction: column;
    }
    .btn-view, .btn-download {
        width: 100%;
    }
    .page-header {
        padding: 12px;
    }
    .header-content h2 {
        font-size: 16px;
    }
    .filter-section {
        padding: 12px;
    }
}
</style>
@endsection

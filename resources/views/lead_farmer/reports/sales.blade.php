@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Sales Reports')

@section('page-title', 'Sales Reports')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use App\Models\Order;

    // Allowed statuses (used for the dropdown)
    $allowedStatuses = ['paid', 'completed', 'confirmed', 'ready_for_pickup', 'Processing order'];
    $leadFarmerId = Auth::user()->leadFarmer->id;

    // Base query – always restrict to the allowed statuses (default)
    $baseQuery = Order::where('lead_farmer_id', $leadFarmerId)
        ->whereIn('order_status', $allowedStatuses);

    // If a specific status is selected, apply it
    if (request()->filled('order_status') && in_array(request('order_status'), $allowedStatuses)) {
        $baseQuery->where('order_status', request('order_status'));
    }

    // Date filters
    if (request()->filled('start_date')) {
        $baseQuery->whereDate('created_at', '>=', request('start_date'));
    }
    if (request()->filled('end_date')) {
        $baseQuery->whereDate('created_at', '<=', request('end_date'));
    }

    // Daily sales data
    $salesData = (clone $baseQuery)
        ->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total_amount) as total_sales')
        )
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date', 'desc')
        ->get();

    // Monthly summary data
    $monthlySummary = (clone $baseQuery)
        ->select(
            DB::raw('EXTRACT(MONTH FROM created_at) as month'),
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total_amount) as total_sales')
        )
        ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'), DB::raw('EXTRACT(YEAR FROM created_at)'))
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

    // Total records count for each table (used for pagination info)
    $monthlyCount = $monthlySummary->count();
    $dailyCount = $salesData->count();
@endphp

{{-- Link the external print CSS --}}
<link rel="stylesheet" href="{{ asset('css/lead_farmer/sales_PDF_report.css') }}" media="print">

<style>
    /* Styling for the export buttons */
    .export-buttons {
        margin-top: 2rem;
        text-align: center;
    }
    .export-buttons .btn {
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .export-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    /* Hide filter form, summary cards, export buttons in print */
    @media print {
        .no-print {
            display: none !important;
        }
        #salesFilterForm, .row.mb-4 .card .row.mb-4, .export-buttons {
            display: none !important;
        }
        /* Show all rows in print (override pagination hiding) */
        .pagination-hidden {
            display: table-row !important;
        }
        /* Hide pagination controls in print */
        .pagination-controls {
            display: none !important;
        }
    }

    /* Hide filter summary on screen ONLY – shown in print via external CSS */
    @media screen {
        .print-only {
            display: none;
        }
    }

    /* Pagination styles (Bootstrap-like) */
    .pagination-wrapper {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        margin-top: 15px;
        font-size: 14px;
    }
    .pagination-info {
        color: #6c757d;
    }
    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pagination-controls .per-page-select {
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #ced4da;
        background: white;
        font-size: 14px;
        margin-left: 10px;
    }
    .pagination-controls ul.pagination {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        gap: 4px;
    }
    .pagination-controls ul.pagination li {
        display: inline-block;
    }
    .pagination-controls ul.pagination li a,
    .pagination-controls ul.pagination li span {
        display: inline-block;
        padding: 6px 12px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        background: white;
        color: #0f1724;
        text-decoration: none;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pagination-controls ul.pagination li a:hover {
        background: #e9ecef;
        border-color: #dee2e6;
    }
    .pagination-controls ul.pagination li.active span {
        background: #10B981;
        color: white;
        border-color: #10B981;
    }
    .pagination-controls ul.pagination li.disabled span {
        color: #6c757d;
        pointer-events: none;
        background: #f8f9fa;
        border-color: #dee2e6;
    }
    /* Hide rows not on current page */
    .pagination-hidden {
        display: none;
    }
</style>

<div class="container-fluid" id="salesReportContainer">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i> Sales Reports
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form id="salesFilterForm" method="GET" action="" class="row mb-4 g-3 no-print">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date"
                                   class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date"
                                   class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="order_status" class="form-label">Order Status</label>
                            <select name="order_status" id="order_status" class="form-control">
                                <option value="">All Statuses</option>
                                @foreach($allowedStatuses as $status)
                                    <option value="{{ $status }}" {{ request('order_status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('lf.reports.sales') }}" class="btn btn-secondary">
                                <i class="fas fa-sync me-1"></i> Reset
                            </a>
                        </div>
                    </form>

                    <!-- Summary Cards (hidden in print) -->
                    <div class="row mb-4 no-print">
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Sales</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                LKR {{ number_format($monthlySummary->sum('total_sales'), 2) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $monthlySummary->sum('order_count') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Average Order Value</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                @if($monthlySummary->sum('order_count') > 0)
                                                    LKR {{ number_format($monthlySummary->sum('total_sales') / $monthlySummary->sum('order_count'), 2) }}
                                                @else
                                                    LKR 0.00
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Months Active</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $monthlySummary->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===================== PRINTABLE AREA ===================== -->
                    <div id="printable-area">
                        <!-- Filter Summary (appears only in print) -->
                        <div class="filter-summary print-only">
                            <div class="filter-item">
                                <strong>Start Date:</strong>
                                <span>{{ request('start_date') ?: 'N/A' }}</span>
                            </div>
                            <div class="filter-item">
                                <strong>End Date:</strong>
                                <span>{{ request('end_date') ?: 'N/A' }}</span>
                            </div>
                            <div class="filter-item">
                                <strong>Order Status:</strong>
                                <span>{{ request('order_status') ? ucfirst(request('order_status')) : 'All Statuses' }}</span>
                            </div>
                        </div>

                        <!-- Monthly Sales Table with Pagination -->
                        <div class="card shadow mb-4" id="monthlyTableWrapper">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-calendar me-1"></i> Monthly Sales Summary
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($monthlySummary->count() > 0)
                                <div class="table-responsive">
                                    <table id="monthlySalesTable" class="table table-bordered" width="100%" cellspacing="0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Month</th>
                                                <th>Year</th>
                                                <th>Orders</th>
                                                <th>Total Sales</th>
                                                <th>Average Order</th>
                                            </tr>
                                        </thead>
                                        <tbody id="monthlyTableBody">
                                            @foreach($monthlySummary as $month)
                                            <tr>
                                                <td>{{ DateTime::createFromFormat('!m', $month->month)->format('F') }}</td>
                                                <td>{{ $month->year }}</td>
                                                <td>{{ $month->order_count }}</td>
                                                <td>LKR {{ number_format($month->total_sales, 2) }}</td>
                                                <td>LKR {{ number_format($month->order_count > 0 ? $month->total_sales / $month->order_count : 0, 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <!-- Total row: always displayed, not paginated -->
                                            <tr class="table-primary" id="monthlyTotalRow">
                                                <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                                <td><strong>{{ $monthlySummary->sum('order_count') }}</strong></td>
                                                <td><strong>LKR {{ number_format($monthlySummary->sum('total_sales'), 2) }}</strong></td>
                                                <td><strong>
                                                    @if($monthlySummary->sum('order_count') > 0)
                                                        LKR {{ number_format($monthlySummary->sum('total_sales') / $monthlySummary->sum('order_count'), 2) }}
                                                    @else
                                                        LKR 0.00
                                                    @endif
                                                </strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination Controls for Monthly Table -->
                                <div class="pagination-wrapper no-print" id="monthlyPagination">
                                    <div class="pagination-info">
                                        Showing <span id="monthlyStart">0</span> to <span id="monthlyEnd">0</span> of <span id="monthlyTotal">{{ $monthlyCount }}</span> entries
                                    </div>
                                    <div class="pagination-controls">
                                        <select class="per-page-select" id="monthlyPerPage">
                                            <option value="5">5</option>
                                            <option value="10" selected>10</option>
                                            <option value="15">15</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                        <ul class="pagination" id="monthlyPaginationList">
                                            <!-- Dynamically generated -->
                                        </ul>
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <p class="text-muted">No sales data available</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Daily Sales Table with Pagination -->
                        <div class="card shadow" id="dailyTableWrapper">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-calendar-day me-1"></i> Daily Sales
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($salesData->count() > 0)
                                <div class="table-responsive">
                                    <table id="dailySalesTable" class="table table-bordered" width="100%" cellspacing="0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Orders</th>
                                                <th>Total Sales</th>
                                                <th>Average Order</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dailyTableBody">
                                            @foreach($salesData as $day)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($day->date)->format('Y-m-d') }}</td>
                                                <td>{{ $day->order_count }}</td>
                                                <td>LKR {{ number_format($day->total_sales, 2) }}</td>
                                                <td>LKR {{ number_format($day->order_count > 0 ? $day->total_sales / $day->order_count : 0, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination Controls for Daily Table -->
                                <div class="pagination-wrapper no-print" id="dailyPagination">
                                    <div class="pagination-info">
                                        Showing <span id="dailyStart">0</span> to <span id="dailyEnd">0</span> of <span id="dailyTotal">{{ $dailyCount }}</span> entries
                                    </div>
                                    <div class="pagination-controls">
                                        <select class="per-page-select" id="dailyPerPage">
                                            <option value="5">5</option>
                                            <option value="10" selected>10</option>
                                            <option value="15">15</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                        <ul class="pagination" id="dailyPaginationList">
                                            <!-- Dynamically generated -->
                                        </ul>
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <p class="text-muted">No daily sales data available</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- ===================== END PRINTABLE AREA ===================== -->

                    <!-- Export Options -->
                    <div class="export-buttons no-print">
                        <button class="btn btn-success me-2" onclick="exportToCSV()">
                            <i class="fas fa-file-csv me-1"></i> Export to CSV
                        </button>
                        <button class="btn btn-danger" onclick="printReport()">
                            <i class="fas fa-print me-1"></i> Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Ensure SweetAlert2 and Toastr are loaded -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

{{-- Pre-resolve all conditionals in PHP to avoid IDE JS linter errors --}}
@php
    $swalIcons = [
        'calendarAlert' => file_exists(public_path('assets/icons/Gif/Calender alert1.gif'))
            ? ['imageUrl' => asset('assets/icons/Gif/Calender alert1.gif'), 'imageWidth' => 80, 'imageHeight' => 80]
            : ['icon' => 'error'],
        'missingDates' => file_exists(public_path('assets/icons/Gif/Missing Dates1.gif'))
            ? ['imageUrl' => asset('assets/icons/Gif/Missing Dates1.gif'), 'imageWidth' => 80, 'imageHeight' => 80]
            : ['icon' => 'warning'],
        'loading' => file_exists(public_path('assets/icons/Gif/loading3.gif'))
            ? ['imageUrl' => asset('assets/icons/Gif/loading3.gif'), 'imageWidth' => 80, 'imageHeight' => 80]
            : ['icon' => 'info', 'showLoading' => true],
        'success' => file_exists(public_path('assets/icons/Gif/success6.gif'))
            ? ['imageUrl' => asset('assets/icons/Gif/success6.gif'), 'imageWidth' => 60, 'imageHeight' => 60]
            : ['icon' => 'success'],
        'error' => file_exists(public_path('assets/icons/Gif/error5.gif'))
            ? ['imageUrl' => asset('assets/icons/Gif/error5.gif'), 'imageWidth' => 60, 'imageHeight' => 60]
            : ['icon' => 'error'],
    ];
@endphp

<script id="swal-icons-data" type="application/json">
    {!! json_encode($swalIcons) !!}
</script>

<script>
    const SWAL_ICONS = JSON.parse(document.getElementById('swal-icons-data').textContent);
</script>

<script>
// ----- PAGINATION FUNCTIONS -----
function initPagination(tableId, tbodyId, paginationId, startSpanId, endSpanId, totalSpanId, perPageSelectId, listId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr:not(#monthlyTotalRow)'); // Exclude total row for monthly if present
    const totalRows = rows.length;
    if (totalRows === 0) return;

    const perPageSelect = document.getElementById(perPageSelectId);
    const list = document.getElementById(listId);

    function render(page, perPage) {
        // Calculate start/end indices
        const start = (page - 1) * perPage;
        const end = Math.min(start + perPage, totalRows);
        // Hide all rows first
        rows.forEach(row => row.classList.add('pagination-hidden'));
        // Show rows in range
        for (let i = start; i < end; i++) {
            rows[i].classList.remove('pagination-hidden');
        }
        // Update info
        document.getElementById(startSpanId).textContent = totalRows === 0 ? 0 : start + 1;
        document.getElementById(endSpanId).textContent = end;
        document.getElementById(totalSpanId).textContent = totalRows;

        // Build pagination links
        const totalPages = Math.ceil(totalRows / perPage);
        let html = '';
        // Previous
        html += `<li class="page-item ${page <= 1 ? 'disabled' : ''}">
            <span class="page-link" data-page="${page - 1}"><i class="fas fa-chevron-left"></i></span>
        </li>`;
        // Page numbers
        for (let p = 1; p <= totalPages; p++) {
            html += `<li class="page-item ${p === page ? 'active' : ''}">
                <span class="page-link" data-page="${p}">${p}</span>
            </li>`;
        }
        // Next
        html += `<li class="page-item ${page >= totalPages ? 'disabled' : ''}">
            <span class="page-link" data-page="${page + 1}"><i class="fas fa-chevron-right"></i></span>
        </li>`;
        list.innerHTML = html;

        // Attach click events to page links
        list.querySelectorAll('.page-link[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const newPage = parseInt(this.dataset.page);
                if (newPage < 1 || newPage > totalPages) return;
                render(newPage, perPage);
            });
        });
    }

    // Initial render
    let initialPerPage = parseInt(perPageSelect.value);
    render(1, initialPerPage);

    // Per page change
    perPageSelect.addEventListener('change', function() {
        const newPerPage = parseInt(this.value);
        render(1, newPerPage);
    });
}

// Initialize both paginations
function initAllPagination() {
    if (document.getElementById('monthlySalesTable')) {
        initPagination(
            'monthlySalesTable',
            'monthlyTableBody',
            'monthlyPagination',
            'monthlyStart',
            'monthlyEnd',
            'monthlyTotal',
            'monthlyPerPage',
            'monthlyPaginationList'
        );
    }
    if (document.getElementById('dailySalesTable')) {
        initPagination(
            'dailySalesTable',
            'dailyTableBody',
            'dailyPagination',
            'dailyStart',
            'dailyEnd',
            'dailyTotal',
            'dailyPerPage',
            'dailyPaginationList'
        );
    }
}
// ----- END PAGINATION -----

// ----- EXPORT FUNCTIONS (unchanged, they use full table data) -----
function exportToCSV() {
    let csv = [];
    
    function processTable(tableId, title) {
        let table = document.getElementById(tableId);
        if (table) {
            csv.push('"' + title + '"');
            // Get all rows including hidden ones (they are in DOM)
            let rows = table.querySelectorAll("tr");
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                for (let j = 0; j < cols.length; j++) {
                    let text = cols[j].innerText.replace(/"/g, '""').replace(/\n/g, ' ').trim();
                    row.push('"' + text + '"');
                }
                csv.push(row.join(","));
            }
            csv.push(""); // Empty line after table
        }
    }
    
    processTable("monthlySalesTable", "Monthly Sales Summary");
    processTable("dailySalesTable", "Daily Sales");
    
    if (csv.length === 0) {
        toastr.warning('No data to export.');
        return;
    }
    
    let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    let downloadLink = document.createElement("a");
    downloadLink.download = "Sales_Report_" + new Date().toISOString().split('T')[0] + ".csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
    
    toastr.success('CSV exported successfully!');
}

function printReport() {
    window.print();
}

// ----- Date helpers and validation (unchanged) -----
function setDefaultDates() {
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    if (startInput && !startInput.value) {
        const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
        startInput.value = firstDay.toISOString().split('T')[0];
    }
    if (endInput && !endInput.value) {
        endInput.value = new Date().toISOString().split('T')[0];
    }
}

function validateOnLoad() {
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    if (startInput && endInput && startInput.value && endInput.value) {
        const startDate = new Date(startInput.value);
        const endDate = new Date(endInput.value);
        if (startDate > endDate) {
            Swal.fire(Object.assign({}, SWAL_ICONS.calendarAlert, {
                title: 'Invalid Date Range',
                html: 'The Start Date is after the End Date.<br>Please correct the dates and filter again.',
                confirmButtonColor: '#d33',
                customClass: { popup: 'swal-popup-compact' }
            }));
        }
    }
}

// ----- AJAX Filter Submission -----
function handleFilterSubmit(e) {
    e.preventDefault();

    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const statusSelect = document.getElementById('order_status');

    const startVal = startInput ? startInput.value : '';
    const endVal = endInput ? endInput.value : '';
    const statusVal = statusSelect ? statusSelect.value : '';

    if (!startVal || !endVal) {
        Swal.fire(Object.assign({}, SWAL_ICONS.missingDates, {
            title: 'Missing Dates',
            text: 'Please select both a Start Date and an End Date to filter.',
            confirmButtonColor: '#3085d6',
            customClass: { popup: 'swal-popup-compact' }
        }));
        return;
    }

    const startDate = new Date(startVal);
    const endDate = new Date(endVal);
    if (startDate > endDate) {
        Swal.fire(Object.assign({}, SWAL_ICONS.calendarAlert, {
            title: 'Invalid Date Range',
            text: 'The Start Date must be before or equal to the End Date.',
            confirmButtonColor: '#d33',
            customClass: { popup: 'swal-popup-compact' }
        }));
        return;
    }

    const loadingConfig = Object.assign({}, SWAL_ICONS.loading, {
        title: 'Filtering Data...',
        text: 'Please wait while we fetch the records.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: { popup: 'swal-popup-compact' }
    });
    if (SWAL_ICONS.loading.showLoading) {
        loadingConfig.didOpen = () => Swal.showLoading();
    }
    Swal.fire(loadingConfig);

    const params = new URLSearchParams();
    params.set('start_date', startVal);
    params.set('end_date', endVal);
    if (statusVal) params.set('order_status', statusVal);
    const targetUrl = window.location.pathname + '?' + params.toString();

    fetch(targetUrl, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response failed with status: ' + response.status);
        return response.text();
    })
    .then(html => {
        const container = document.getElementById('salesReportContainer');
        if (container) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('salesReportContainer');
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            }
        }

        setDefaultDates();
        attachFormListener();
        // Re-initialize pagination after DOM update
        initAllPagination();

        const si = document.getElementById('start_date');
        const ei = document.getElementById('end_date');
        const sti = document.getElementById('order_status');
        if (si) si.value = startVal;
        if (ei) ei.value = endVal;
        if (sti) sti.value = statusVal;

        window.history.pushState({}, '', targetUrl);

        Swal.fire(Object.assign({}, SWAL_ICONS.success, {
            title: 'Data Filtered!',
            text: 'Sales report updated successfully.',
            timer: 2500,
            showConfirmButton: false,
            customClass: { popup: 'swal-popup-compact' }
        }));
    })
    .catch(error => {
        console.error('Filter Error:', error);
        Swal.fire(Object.assign({}, SWAL_ICONS.error, {
            title: 'Server Error',
            text: 'Something went wrong while fetching the data. Please try again.',
            confirmButtonColor: '#d33',
            customClass: { popup: 'swal-popup-compact' }
        }));
    });
}

function handleDateChange(e) {
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    if (startInput && endInput && startInput.value && endInput.value) {
        const startDate = new Date(startInput.value);
        const endDate = new Date(endInput.value);
        if (startDate > endDate) {
            let errorMsg = 'The Start Date must be before or equal to the End Date.';
            if (e.target.id === 'start_date') {
                errorMsg = 'The Start Date cannot be after the End Date.';
            } else if (e.target.id === 'end_date') {
                errorMsg = 'The End Date cannot be before the Start Date.';
            }
            Swal.fire(Object.assign({}, SWAL_ICONS.calendarAlert, {
                title: 'Invalid Date Selection',
                text: errorMsg,
                confirmButtonColor: '#d33',
                customClass: { popup: 'swal-popup-compact' }
            })).then(() => {
                e.target.value = '';
            });
        }
    }
}

function attachFormListener() {
    const form = document.getElementById('salesFilterForm');
    if (form) {
        form.removeEventListener('submit', handleFilterSubmit);
        form.addEventListener('submit', handleFilterSubmit);
    }
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    if (startInput && endInput) {
        startInput.removeEventListener('change', handleDateChange);
        endInput.removeEventListener('change', handleDateChange);
        startInput.addEventListener('change', handleDateChange);
        endInput.addEventListener('change', handleDateChange);
    }
}

// ----- Initialise on page load -----
document.addEventListener('DOMContentLoaded', function() {
    setDefaultDates();
    validateOnLoad();
    attachFormListener();
    initAllPagination();
});
</script>
@endsection
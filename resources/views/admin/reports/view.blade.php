@extends('admin.layouts.admin_master')

@section('title', $reportTitle)

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2><i class="fas fa-file-alt"></i> {{ $reportTitle }}</h2>
        <div class="header-actions">
            <button class="btn-export" onclick="exportToPDF()">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('admin.reports.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    @if(isset($filters))
    <div class="filter-display">
        <div class="filter-tags">
            @if(isset($filters['from_date']) && isset($filters['to_date']))
            <span class="filter-tag">
                <i class="fas fa-calendar"></i>
                {{ date('M d, Y', strtotime($filters['from_date'])) }} - {{ date('M d, Y', strtotime($filters['to_date'])) }}
            </span>
            @endif
            <span class="filter-tag">
                <i class="fas fa-database"></i>
                {{ count($data) }} Records
            </span>
            <span class="filter-tag">
                <i class="fas fa-clock"></i>
                {{ now()->format('h:i A') }}
            </span>
        </div>
    </div>
    @endif
</div>

<div class="report-view-container">
    <div class="report-toolbar">
        <div class="toolbar-left">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchTable" placeholder="Search in report...">
            </div>
        </div>
        <div class="toolbar-right">
            <div class="view-controls">
                <button class="btn-view active" data-view="table">
                    <i class="fas fa-table"></i> Table
                </button>
                <button class="btn-view" data-view="cards">
                    <i class="fas fa-th-large"></i> Cards
                </button>
            </div>
            <div class="export-options">
                <button class="btn-export-csv" onclick="exportToCSV()">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button class="btn-export-excel" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
            </div>
        </div>
    </div>

    <div class="report-content">
        <div class="view-table active" id="tableView">
            @if(count($data) > 0)
            <div class="table-responsive">
                <table class="report-data-table" id="reportTable">
                    <thead>
                        <tr>
                            @if($reportType == 'order-history')
                                <th>Order ID</th>
                                <th>Order Number</th>
                                <th>Buyer</th>
                                <th>Farmer</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Payment</th>
                            @elseif($reportType == 'inventory-stock')
                                <th>Product</th>
                                <th>Farmer</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Grade</th>
                                <th>Price</th>
                                <th>Avail Date</th>
                                <th>Status</th>

                            @elseif($reportType == 'system-adoption')
                                <th>Role</th>
                                <th>Total</th>
                                <th>Active</th>
                                <th>Within 7 days (Week)</th>
                                <th>Within 30 days (Month)</th>
                            @elseif($reportType == 'group-performance')
                                <th>Lead Farmer</th>
                                <th>Group</th>
                                <th>Farmers</th>
                                <th>Active</th>
                                <th>Qty Sold</th>
                                <th>Sales</th>

                            @elseif($reportType == 'regional-performance')
                                <th>Region</th>
                                <th>Total Farmers</th>
                                <th>Active Farmers</th>
                                <th>Total Products</th>
                                <th>Active Buyers</th>
                                <th>Total Orders</th>
                                <th>Total Sales</th>
                            @else
                                @if(count($data) > 0)
                                    @php
                                        // Handle both array and collection
                                        $firstItem = is_array($data) ? $data[0] : $data->first();
                                        $keys = is_object($firstItem) ? array_keys((array)$firstItem) : array_keys((array)$firstItem);
                                    @endphp
                                    @foreach($keys as $key)
                                        <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                    @endforeach
                                @endif
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if(in_array($reportType, ['data-quality']))
                            @php
                                $usersMissing = $data['users']->missing_email ?? 0;
                                $usersTotal = $data['users']->total ?? 1;
                                $usersCompletion = round((1 - ($usersMissing / $usersTotal)) * 100, 2);

                                $farmersMissingNIC = $data['farmers']->missing_nic ?? 0;
                                $farmersMissingMaps = $data['farmers']->missing_map_links ?? 0;
                                $farmersMissingPayment = $data['farmers']->missing_payment_details ?? 0;
                                $farmersTotal = $data['farmers']->total ?? 1;
                                $farmersTotalMissing = $farmersMissingNIC + $farmersMissingMaps + $farmersMissingPayment;
                                $farmersCompletion = round((1 - ($farmersTotalMissing / ($farmersTotal * 3))) * 100, 2);

                                $productsMissingPhotos = $data['products']->missing_photos ?? 0;
                                $productsMissingMaps = $data['products']->missing_pickup_maps ?? 0;
                                $productsTotal = $data['products']->total ?? 1;
                                $productsTotalMissing = $productsMissingPhotos + $productsMissingMaps;
                                $productsCompletion = round((1 - ($productsTotalMissing / ($productsTotal * 2))) * 100, 2);
                            @endphp
                            <tr>
                                <td>Users</td>
                                <td>{{ $data['users']->total ?? 0 }}</td>
                                <td>{{ $usersMissing }}</td>
                                <td>{{ $usersCompletion }}%</td>
                                <td>{{ $data['users']->never_logged_in ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Farmers</td>
                                <td>{{ $data['farmers']->total ?? 0 }}</td>
                                <td>{{ $farmersTotalMissing }}</td>
                                <td>{{ $farmersCompletion }}%</td>
                                <td>{{ $farmersMissingNIC }}</td>
                            </tr>
                            <tr>
                                <td>Products</td>
                                <td>{{ $data['products']->total ?? 0 }}</td>
                                <td>{{ $productsTotalMissing }}</td>
                                <td>{{ $productsCompletion }}%</td>
                                <td>{{ $productsMissingMaps }}</td>
                            </tr>
                        @else
                            @foreach($data as $row)
                            <tr>
                                @if($reportType == 'order-history')
                                    <td>{{ $row->order_id }}</td>
                                    <td>{{ $row->order_number }}</td>
                                    <td>{{ $row->buyer_name }}</td>
                                    <td>{{ $row->farmer_name }}</td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $row->order_status)) }}">
                                            {{ ucfirst($row->order_status) }}
                                        </span>
                                    </td>
                                    <td>{{ date('M d, Y', strtotime($row->created_at)) }}</td>
                                    <td class="numeric">Rs. {{ number_format($row->total_amount, 2) }}</td>
                                    <td>{{ $row->payment_method ?? 'N/A' }}</td>
                                @elseif($reportType == 'inventory-stock')
                                    <td>{{ $row->product_name }}</td>
                                    <td>{{ $row->farmer_name }}</td>
                                    <td>{{ $row->quantity }}</td>
                                    <td>{{ $row->unit_of_measure }}</td>
                                    <td>{{ $row->quality_grade }}</td>
                                    <td class="numeric">Rs. {{ number_format($row->selling_price, 2) }}</td>
                                    <td>{{ $row->expected_availability_date ? date('M d, Y', strtotime($row->expected_availability_date)) : 'N/A' }}</td>
                                    <td>
                                        <span class="status-badge {{ $row->product_status == 'available' ? 'status-active' : 'status-pending' }}">
                                            {{ ucfirst($row->product_status) }}
                                        </span>
                                    </td>

                                @elseif($reportType == 'system-adoption')
                                    <td>{{ ucfirst(str_replace('_', ' ', $row->role)) }}</td>
                                    <td>{{ $row->total_users }}</td>
                                    <td>{{ $row->active_users }}</td>
                                    <td>{{ $row->new_registrations_week }}</td>
                                    <td>{{ $row->active_accounts }}</td>
                                @elseif($reportType == 'group-performance')
                                    <td>{{ $row->lead_farmer_name }}</td>
                                    <td>{{ $row->group_name }}</td>
                                    <td>{{ $row->total_farmers_managed }}</td>
                                    <td>{{ $row->active_farmers }}</td>
                                    <td>{{ $row->total_quantity_sold }}</td>
                                    <td class="numeric">Rs. {{ number_format($row->total_revenue, 2) }}</td>
                                @else
                                    @if(is_object($row))
                                        @php $rowArray = (array)$row; @endphp
                                        @foreach($rowArray as $key => $value)
                                            @php
                                                $isCurrency = preg_match('/revenue|amount|price|sales|total_sales|total_revenue|revenue/i', $key);
                                            @endphp
                                            @if(is_numeric($value))
                                                @if($isCurrency)
                                                    <td class="numeric">Rs. {{ number_format($value, 2) }}</td>
                                                @else
                                                    <td>{{ number_format($value, 2) }}</td>
                                                @endif
                                            @elseif(strtotime($value) && !is_numeric($value) && strlen($value) > 8)
                                                <td>{{ date('M d, Y', strtotime($value)) }}</td>
                                            @else
                                                <td>{{ $value ?? 'N/A' }}</td>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach($row as $key => $value)
                                            @php
                                                $isCurrency = preg_match('/revenue|amount|price|sales|total_sales|total_revenue|revenue/i', $key);
                                            @endphp
                                            @if(is_numeric($value))
                                                @if($isCurrency)
                                                    <td class="numeric">Rs. {{ number_format($value, 2) }}</td>
                                                @else
                                                    <td>{{ number_format($value, 2) }}</td>
                                                @endif
                                            @elseif(strtotime($value) && !is_numeric($value) && strlen($value) > 8)
                                                <td>{{ date('M d, Y', strtotime($value)) }}</td>
                                            @else
                                                <td>{{ $value ?? 'N/A' }}</td>
                                            @endif
                                        @endforeach
                                    @endif
                                @endif
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @else
            <div class="no-data">
                <i class="fas fa-database"></i>
                <h3>No Data Available</h3>
                <p>No records found for selected criteria</p>
            </div>
            @endif
        </div>

        <div class="view-cards" id="cardsView" style="display: none;">
            @if(count($data) > 0 && !in_array($reportType, ['data-quality']))
            <div class="cards-grid">
                @foreach($data as $row)
                <div class="data-card">
                    <div class="card-header">
                        <h4>
                            @if($reportType == 'order-history')
                                Order #{{ $row->order_number }}
                            @elseif($reportType == 'inventory-stock')
                                {{ $row->product_name }}

                            @elseif($reportType == 'group-performance')
                                {{ $row->lead_farmer_name }}
                            @else
                                Record #{{ $loop->iteration }}
                            @endif
                        </h4>
                        @if($reportType == 'order-history')
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $row->order_status)) }}">
                                {{ ucfirst($row->order_status) }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($reportType == 'order-history')
                            <div class="card-item">
                                <span class="label">Buyer:</span>
                                <span class="value">{{ $row->buyer_name }}</span>
                            </div>
                            <div class="card-item">
                                <span class="label">Farmer:</span>
                                <span class="value">{{ $row->farmer_name }}</span>
                            </div>
                            <div class="card-item">
                                <span class="label">Amount:</span>
                                <span class="value">Rs. {{ number_format($row->total_amount, 2) }}</span>
                            </div>
                            <div class="card-item">
                                <span class="label">Date:</span>
                                <span class="value">{{ date('M d, Y', strtotime($row->created_at)) }}</span>
                            </div>
                        @elseif($reportType == 'inventory-stock')
                            <div class="card-item">
                                <span class="label">Farmer:</span>
                                <span class="value">{{ $row->farmer_name }}</span>
                            </div>
                            <div class="card-item">
                                <span class="label">Qty:</span>
                                <span class="value">{{ $row->quantity }} {{ $row->unit_of_measure }}</span>
                            </div>
                            <div class="card-item">
                                <span class="label">Price:</span>
                                <span class="value">Rs. {{ number_format($row->selling_price, 2) }}</span>
                            </div>
                            <div class="card-item">
                                <span class="label">Grade:</span>
                                <span class="value">{{ $row->quality_grade }}</span>
                            </div>
                        @elseif($reportType == 'group-performance')
                            <div class="card-item">
                                <span class="label">Group:</span>
                                <span class="value">{{ $row->group_name }}</span>
                            </div>
                            <div class="card-item">
                                <span class="label">Farmers:</span>
                                <span class="value">{{ $row->total_farmers_managed }} ({{ $row->active_farmers }} active)</span>
                            </div>
                            <div class="card-item">
                                <span class="label">Sales:</span>
                                <span class="value">Rs. {{ number_format($row->total_revenue, 2) }}</span>
                            </div>
                            <div class="card-item">
                                <span class="label">Qty:</span>
                                <span class="value">{{ $row->total_quantity_sold }} units</span>
                            </div>
                        @else
                            @if(is_object($row))
                                @php $rowArray = (array)$row; @endphp
                                @foreach($rowArray as $key => $value)
                                    @if(!in_array($key, ['id', 'created_at', 'updated_at']))
                                    <div class="card-item">
                                        <span class="label">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="value">
                                            @php
                                                $isCurrency = preg_match('/revenue|amount|price|sales|total_sales|total_revenue|revenue/i', $key);
                                            @endphp
                                            @if(is_numeric($value))
                                                @if($isCurrency)
                                                    Rs. {{ number_format($value, 2) }}
                                                @else
                                                    {{ number_format($value, 2) }}
                                                @endif
                                            @elseif(strtotime($value) && !is_numeric($value) && strlen($value) > 8)
                                                {{ date('M d, Y', strtotime($value)) }}
                                            @else
                                                {{ $value ?? 'N/A' }}
                                            @endif
                                        </span>
                                    </div>
                                    @endif
                                @endforeach
                            @else
                                @foreach($row as $key => $value)
                                    @if(!in_array($key, ['id', 'created_at', 'updated_at']))
                                    <div class="card-item">
                                        <span class="label">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="value">
                                            @php
                                                $isCurrency = preg_match('/revenue|amount|price|sales|total_sales|total_revenue|revenue/i', $key);
                                            @endphp
                                            @if(is_numeric($value))
                                                @if($isCurrency)
                                                    Rs. {{ number_format($value, 2) }}
                                                @else
                                                    {{ number_format($value, 2) }}
                                                @endif
                                            @elseif(strtotime($value) && !is_numeric($value) && strlen($value) > 8)
                                                {{ date('M d, Y', strtotime($value)) }}
                                            @else
                                                {{ $value ?? 'N/A' }}
                                            @endif
                                        </span>
                                    </div>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="no-data">
                <i class="fas fa-database"></i>
                <h3>No Data Available</h3>
                <p>No records found for selected criteria</p>
            </div>
            @endif
        </div>
    </div>

    @if(count($data) > 0 && !in_array($reportType, ['data-quality']))
    <div class="report-summary">
        <div class="summary-card">
            <h4><i class="fas fa-chart-pie"></i> Summary</h4>
            <div class="summary-stats">
                <div class="summary-item">
                    <span class="label">Records</span>
                    <span class="value">{{ count($data) }}</span>
                </div>
                @if($reportType == 'order-history')
                <div class="summary-item">
                    <span class="label">Total</span>
                    <span class="value">Rs. {{ number_format(collect($data)->sum('total_amount'), 2) }}</span>
                </div>
                @endif
                @if($reportType == 'inventory-stock')
                <div class="summary-item">
                    <span class="label">Quantity</span>
                    <span class="value">{{ number_format(collect($data)->sum('quantity'), 2) }}</span>
                </div>
                @endif
                @if($reportType == 'system-adoption')
                <div class="summary-item">
                    <span class="label">Users</span>
                    <span class="value">{{ collect($data)->sum('total_users') }}</span>
                </div>
                @endif
                <div class="summary-item">
                    <span class="label">Period</span>
                    <span class="value">
                        @if(isset($filters['from_date']) && isset($filters['to_date']))
                            {{ date('M d, Y', strtotime($filters['from_date'])) }} - {{ date('M d, Y', strtotime($filters['to_date'])) }}
                        @else
                            All Time
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTable');
    const table = document.getElementById('reportTable');
    const viewButtons = document.querySelectorAll('.btn-view');
    const tableView = document.getElementById('tableView');
    const cardsView = document.getElementById('cardsView');

    if (searchInput && table) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });
    }

    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const view = this.getAttribute('data-view');

            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            if (view === 'table') {
                tableView.style.display = 'block';
                cardsView.style.display = 'none';
            } else {
                tableView.style.display = 'none';
                cardsView.style.display = 'block';
            }
        });
    });
});

function exportToPDF() {
    const reportId = '{{ $reportType }}';
    const fromDate = '{{ $filters["from_date"] ?? "" }}';
    const toDate = '{{ $filters["to_date"] ?? "" }}';

    const url = `/admin/reports/pdf/${reportId}?from_date=${fromDate}&to_date=${toDate}`;
    window.open(url, '_blank');
}

function exportToCSV() {
    const table = document.getElementById('reportTable');

    if (!table) {
        Swal.fire({
            @if(file_exists(public_path('assets/icons/Gif/alert2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
            title: 'Export Failed',
            text: 'No data available to export',
            confirmButtonColor: '#10B981',
            background: 'var(--card-bg)',
            color: 'var(--text-color)'
        });
        return;
    }

    let csv = [];

    for (let i = 0; i < table.rows.length; i++) {
        let row = [];
        for (let j = 0; j < table.rows[i].cells.length; j++) {
            let cell = table.rows[i].cells[j];
            let text = cell.textContent.replace(/,/g, '').replace(/Rs\.\s*/g, '');
            row.push(text);
        }
        csv.push(row.join(','));
    }

    const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "{{ $reportTitle }}.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    Swal.fire({
        @if(file_exists(public_path('assets/icons/Gif/success4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
        title: 'Export Complete',
        text: 'CSV file has been downloaded',
        confirmButtonColor: '#10B981',
        background: 'var(--card-bg)',
        color: 'var(--text-color)',
        timer: 1500
    });
}

function exportToExcel() {
    Swal.fire({
        title: 'Export to Excel',
        text: 'This feature will be available soon.',
        @if(file_exists(public_path('assets/icons/Gif/info2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/info2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'info' @endif,
        confirmButtonColor: '#10B981',
        background: 'var(--card-bg)',
        color: 'var(--text-color)',
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Export as CSV'
    }).then((result) => {
        if (result.isDismissed && result.dismiss === 'cancel') {
            exportToCSV();
        }
    });
}
</script>

<style>
:root {
    --primary-green: #10B981;
    --dark-green: #059669;
    --body-bg: #f6f8fa;
    --card-bg: #ffffff;
    --text-color: #0f1724;
    --muted: #6b7280;
    --accent-amber: #f59e0b;
    --blue: #3b82f6;
    --purple: #8b5cf6;
    --shadow-sm: 0 2px 4px rgba(15, 23, 36, 0.04);
    --shadow-md: 0 4px 8px rgba(15, 23, 36, 0.06);
    --shadow-lg: 0 8px 16px rgba(15, 23, 36, 0.08);
    --shadow-hover: 0 12px 24px -8px rgba(16, 185, 129, 0.2);
}

.report-view-container {
    padding: 12px;
    animation: fadeIn 0.3s ease;
}

.page-header {
    background: var(--card-bg);
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    box-shadow: var(--shadow-sm);
    border-left: 3px solid var(--primary-green);
    transition: transform 0.3s ease;
}

.page-header:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    flex-wrap: wrap;
    gap: 12px;
}

.header-content h2 {
    color: var(--text-color);
    font-size: 18px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.header-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-export, .btn-print, .btn-back {
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}

.btn-export {
    background: var(--primary-green);
    color: white;
}

.btn-export:hover {
    background: var(--dark-green);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.2);
}

.btn-print {
    background: var(--blue);
    color: white;
}

.btn-print:hover {
    background: #2563eb;
}

.btn-back {
    background: var(--card-bg);
    color: var(--text-color);
    border: 1px solid #e5e7eb;
}

.btn-back:hover {
    background: #f3f4f6;
    transform: translateY(-1px);
    border-color: var(--primary-green);
}

.filter-display {
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
    animation: slideDown 0.3s ease;
}

.filter-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.filter-tag {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: all 0.3s ease;
}

.filter-tag:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(16, 185, 129, 0.2);
}

.report-toolbar {
    background: var(--card-bg);
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow-sm);
    flex-wrap: wrap;
    gap: 12px;
    animation: slideUp 0.3s ease;
}

.toolbar-left, .toolbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: 12px;
}

.search-box input {
    padding: 8px 10px 8px 28px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 12px;
    width: 180px;
    transition: all 0.3s ease;
    background: var(--card-bg);
    color: var(--text-color);
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    width: 220px;
}

.view-controls {
    display: flex;
    gap: 4px;
    background: #f3f4f6;
    padding: 2px;
    border-radius: 6px;
}

.btn-view {
    padding: 6px 12px;
    border: none;
    background: none;
    color: var(--text-color);
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}

.btn-view:hover {
    background: #e5e7eb;
    transform: scale(1.05);
}

.btn-view.active {
    background: var(--primary-green);
    color: white;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

.export-options {
    display: flex;
    gap: 8px;
}

.btn-export-csv, .btn-export-excel {
    padding: 6px 12px;
    border: 1px solid #e5e7eb;
    background: var(--card-bg);
    color: var(--text-color);
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}

.btn-export-csv:hover {
    background: var(--primary-green);
    color: white;
    border-color: var(--primary-green);
    transform: translateY(-1px);
}

.btn-export-excel:hover {
    background: var(--dark-green);
    color: white;
    border-color: var(--dark-green);
    transform: translateY(-1px);
}

.report-content {
    background: var(--card-bg);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    margin-bottom: 16px;
    animation: fadeInUp 0.4s ease;
}

.table-responsive {
    overflow-x: auto;
    max-height: 400px;
    overflow-y: auto;
}

.report-data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    min-width: 600px;
}

.report-data-table th {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    color: var(--text-color);
    font-weight: 600;
    text-align: left;
    padding: 10px 12px;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 10;
    font-size: 11px;
}

.report-data-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #f3f4f6;
    color: var(--text-color);
    transition: background-color 0.2s ease;
    font-size: 12px;
}

.report-data-table tr:hover {
    background: #f0f9ff;
}

.report-data-table tr:hover td {
    background: #f0f9ff;
}

.numeric {
    text-align: right;
    font-family: 'Courier New', monospace;
    font-weight: 500;
    color: var(--dark-green);
}

.status-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.status-active {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}
.status-pending {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}
.status-completed {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
}
.status-cancelled {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.warning {
    color: #dc2626;
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: 500;
    font-size: 10px;
}

.success {
    color: var(--dark-green);
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: 500;
    font-size: 10px;
}

.no-data {
    text-align: center;
    padding: 40px 12px;
    color: var(--muted);
    animation: fadeIn 0.3s ease;
}

.no-data i {
    font-size: 32px;
    margin-bottom: 12px;
    color: #e5e7eb;
    animation: pulse 2s infinite;
}

.no-data h3 {
    color: var(--text-color);
    margin-bottom: 8px;
    font-size: 16px;
    font-weight: 600;
}

.no-data p {
    color: var(--muted);
    font-size: 13px;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px;
    padding: 12px;
    animation: fadeIn 0.3s ease;
}

.data-card {
    background: var(--card-bg);
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
    transition: all 0.3s ease;
    animation: slideUp 0.3s ease;
    animation-fill-mode: both;
}

.data-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-green);
}

.data-card:nth-child(odd) {
    animation-delay: 0.1s;
}

.data-card:nth-child(even) {
    animation-delay: 0.2s;
}

.card-header {
    background: linear-gradient(135deg, #f9fafb, #f3f4f6);
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h4 {
    color: var(--text-color);
    font-size: 12px;
    margin: 0;
    font-weight: 600;
}

.card-body {
    padding: 12px;
}

.card-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
}

.card-item:hover {
    background: #f9fafb;
    padding-left: 4px;
    padding-right: 4px;
    border-radius: 3px;
}

.card-item:last-child {
    border-bottom: none;
}

.card-item .label {
    color: var(--muted);
    font-size: 11px;
    font-weight: 500;
}

.card-item .value {
    color: var(--text-color);
    font-size: 12px;
    font-weight: 600;
    text-align: right;
}

.report-summary {
    background: var(--card-bg);
    border-radius: 8px;
    padding: 16px;
    box-shadow: var(--shadow-sm);
    animation: slideUp 0.3s ease;
}

.summary-card h4 {
    color: var(--text-color);
    font-size: 14px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
}

.summary-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
}

.summary-item {
    background: linear-gradient(135deg, #f9fafb, #f3f4f6);
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #f3f4f6;
    transition: all 0.3s ease;
}

.summary-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
    border-color: var(--primary-green);
}

.summary-item .label {
    display: block;
    color: var(--muted);
    font-size: 11px;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.summary-item .value {
    display: block;
    color: var(--text-color);
    font-size: 16px;
    font-weight: 700;
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

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

@media (min-width: 1200px) {
    .cards-grid {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    }
}

@media (max-width: 1199px) and (min-width: 992px) {
    .cards-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }
    .report-toolbar {
        flex-wrap: wrap;
    }
}

@media (max-width: 991px) and (min-width: 768px) {
    .cards-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }
    .header-content {
        flex-direction: column;
        align-items: flex-start;
    }
    .header-actions {
        width: 100%;
        justify-content: flex-start;
    }
}

@media (max-width: 767px) {
    .report-view-container {
        padding: 8px;
    }

    .cards-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 8px;
        padding: 8px;
    }

    .report-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .toolbar-left, .toolbar-right {
        width: 100%;
        justify-content: space-between;
    }

    .search-box input {
        width: 100%;
    }

    .search-box input:focus {
        width: 100%;
    }

    .summary-stats {
        grid-template-columns: 1fr;
    }

    .page-header {
        padding: 12px;
    }

    .header-content h2 {
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }

    .header-actions {
        flex-direction: column;
        width: 100%;
    }

    .btn-export, .btn-print, .btn-back {
        width: 100%;
        justify-content: center;
    }

    .export-options {
        width: 100%;
        justify-content: center;
    }

    .view-controls {
        width: 100%;
        justify-content: center;
    }

    .filter-tags {
        justify-content: center;
    }

    .filter-tag {
        font-size: 10px;
        padding: 3px 6px;
    }
}

@media print {
    .report-toolbar,
    .header-actions,
    .filter-display,
    .report-summary {
        display: none !important;
    }

    .report-content {
        box-shadow: none !important;
        border: none !important;
    }

    body {
        font-size: 10px !important;
        background: white !important;
    }

    .report-view-container {
        padding: 0 !important;
    }

    .report-data-table {
        font-size: 10px !important;
    }
}
</style>
@endsection

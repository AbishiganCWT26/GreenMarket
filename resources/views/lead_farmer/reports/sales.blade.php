@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Sales Reports')

@section('page-title', 'Sales Reports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i> Sales Reports
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Date Range Filter -->
                    <form method="GET" action="" class="row mb-4">
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
                            <label for="filter" class="form-label">Filter By</label>
                            <select name="filter" id="filter" class="form-control">
                                <option value="">All Time</option>
                                <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ request('filter') == 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>This Month</option>
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

                    <!-- Summary Cards -->
                    <div class="row mb-4">
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

                    <!-- Monthly Sales Table -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-calendar me-1"></i> Monthly Sales Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($monthlySummary->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Month</th>
                                            <th>Year</th>
                                            <th>Orders</th>
                                            <th>Total Sales</th>
                                            <th>Average Order</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthlySummary as $month)
                                        <tr>
                                            <td>{{ DateTime::createFromFormat('!m', $month->month)->format('F') }}</td>
                                            <td>{{ $month->year }}</td>
                                            <td>{{ $month->order_count }}</td>
                                            <td>LKR {{ number_format($month->total_sales, 2) }}</td>
                                            <td>LKR {{ number_format($month->order_count > 0 ? $month->total_sales / $month->order_count : 0, 2) }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="table-primary">
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
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No sales data available</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Daily Sales Table -->
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-calendar-day me-1"></i> Daily Sales
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($salesData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Orders</th>
                                            <th>Total Sales</th>
                                            <th>Average Order</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No daily sales data available</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Export Options -->
                    <div class="mt-4 text-center">
                        <button class="btn btn-success me-2" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-1"></i> Export to Excel
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

@push('scripts')
<script>
function exportToExcel() {
    toastr.info('Excel export feature coming soon!');
}

function printReport() {
    window.print();
}

document.addEventListener('DOMContentLoaded', function() {
    // Set default dates for filter
    const today = new Date().toISOString().split('T')[0];
    const firstDayOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];

    if (!document.getElementById('start_date').value) {
        document.getElementById('start_date').value = firstDayOfMonth;
    }

    if (!document.getElementById('end_date').value) {
        document.getElementById('end_date').value = today;
    }
});
</script>
@endpush

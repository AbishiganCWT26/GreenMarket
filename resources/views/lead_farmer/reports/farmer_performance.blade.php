@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Farmer Performance Reports')

@section('page-title', 'Farmer Performance Reports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i> Farmer Performance Reports
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Farmers</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $farmers->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                                Active Farmers</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $farmers->where('products_count', '>', 0)->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                                                Total Products Listed</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $farmers->sum('products_count') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box fa-2x text-gray-300"></i>
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
                                                Total Quantity Listed</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($farmers->sum('products_sum_quantity'), 2) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-weight-hanging fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Farmer Performance Table -->
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list me-1"></i> Farmer Performance Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($farmers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Farmer</th>
                                            <th>Contact</th>
                                            <th>Products Listed</th>
                                            <th>Total Quantity</th>
                                            <th>Total Orders</th>
                                            <th>Total Sales</th>
                                            <th>Avg. Sale/Product</th>
                                            <th>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($farmers as $farmer)
                                        @php
                                            $sales = $farmerSales[$farmer->id] ?? null;
                                            $totalOrders = $sales ? $sales->total_orders : 0;
                                            $totalSales = $sales ? $sales->total_sales : 0;
                                            $performance = $farmer->products_count > 0 ? ($totalOrders / $farmer->products_count) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <strong>{{ $farmer->name }}</strong><br>
                                                        <small class="text-muted">{{ $farmer->nic_no }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $farmer->primary_mobile }}<br>
                                                <small class="text-muted">{{ $farmer->email }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $farmer->products_count > 0 ? 'success' : 'secondary' }}">
                                                    {{ $farmer->products_count }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ number_format($farmer->products_sum_quantity, 2) }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $totalOrders > 0 ? 'info' : 'secondary' }}">
                                                    {{ $totalOrders }}
                                                </span>
                                            </td>
                                            <td class="text-end">LKR {{ number_format($totalSales, 2) }}</td>
                                            <td class="text-center">
                                                @if($farmer->products_count > 0)
                                                    {{ number_format($totalOrders / $farmer->products_count, 2) }}
                                                @else
                                                    0.00
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar
                                                        @if($performance >= 80) bg-success
                                                        @elseif($performance >= 50) bg-info
                                                        @elseif($performance >= 20) bg-warning
                                                        @else bg-danger
                                                        @endif"
                                                        role="progressbar"
                                                        style="width: {{ min($performance, 100) }}%"
                                                        aria-valuenow="{{ $performance }}"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        {{ number_format($performance, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr class="table-primary">
                                            <td colspan="2" class="text-end"><strong>Totals:</strong></td>
                                            <td class="text-center"><strong>{{ $farmers->sum('products_count') }}</strong></td>
                                            <td class="text-center"><strong>{{ number_format($farmers->sum('products_sum_quantity'), 2) }}</strong></td>
                                            <td class="text-center"><strong>{{ $farmerSales->sum('total_orders') }}</strong></td>
                                            <td class="text-end"><strong>LKR {{ number_format($farmerSales->sum('total_sales'), 2) }}</strong></td>
                                            <td class="text-center">
                                                <strong>
                                                    @if($farmers->sum('products_count') > 0)
                                                        {{ number_format($farmerSales->sum('total_orders') / $farmers->sum('products_count'), 2) }}
                                                    @else
                                                        0.00
                                                    @endif
                                                </strong>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Performance Insights -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Top Performing Farmers</h6>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $topFarmers = $farmers->sortByDesc(function($farmer) use ($farmerSales) {
                                                    $sales = $farmerSales[$farmer->id] ?? null;
                                                    return $sales ? $sales->total_sales : 0;
                                                })->take(3);
                                            @endphp

                                            @foreach($topFarmers as $farmer)
                                            @php
                                                $sales = $farmerSales[$farmer->id] ?? null;
                                                $totalSales = $sales ? $sales->total_sales : 0;
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="mb-1">{{ $farmer->name }}</h6>
                                                    <small class="text-muted">{{ $farmer->products_count }} products</small>
                                                </div>
                                                <div class="text-end">
                                                    <strong class="text-success">LKR {{ number_format($totalSales, 2) }}</strong><br>
                                                    <small class="text-muted">Total Sales</small>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Farmers Needing Attention</h6>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $inactiveFarmers = $farmers->where('products_count', 0)->take(3);
                                            @endphp

                                            @if($inactiveFarmers->count() > 0)
                                                @foreach($inactiveFarmers as $farmer)
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h6 class="mb-1 text-danger">{{ $farmer->name }}</h6>
                                                        <small class="text-muted">No products listed</small>
                                                    </div>
                                                    <div>
                                                        <span class="badge bg-danger">Inactive</span>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @else
                                                <p class="text-success text-center mb-0">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    All farmers are active!
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No farmer data available</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Export Options -->
                    <div class="mt-4 text-center">
                        <button class="btn btn-success me-2" onclick="exportPerformanceToExcel()">
                            <i class="fas fa-file-excel me-1"></i> Export to Excel
                        </button>
                        <button class="btn btn-danger" onclick="printPerformanceReport()">
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
function exportPerformanceToExcel() {
    toastr.info('Excel export feature coming soon!');
}

function printPerformanceReport() {
    window.print();
}
</script>
@endpush

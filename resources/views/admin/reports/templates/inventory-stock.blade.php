@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-boxes"></i> Current Inventory Status
    </div>

    @if(count($data) > 0)
        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ count($data) }}</div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format(collect($data)->sum('quantity'), 2) }}</div>
                <div class="stat-label">Total Quantity</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format(collect($data)->sum(function($item) { return $item->quantity * $item->selling_price; }), 2) }}</div>
                <div class="stat-label">Inventory Value</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ collect($data)->where('product_status', 'available')->count() }}</div>
                <div class="stat-label">Available Products</div>
            </div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Farmer Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Quality Grade</th>
                    <th>Selling Price</th>
                    <th>Availability Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $product)
                    <tr>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->farmer_name }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>{{ $product->unit_of_measure }}</td>
                        <td>{{ $product->quality_grade }}</td>
                        <td class="numeric">Rs. {{ number_format($product->selling_price, 2) }}</td>
                        <td>
                            @if($product->expected_availability_date)
                                {{ date('M d, Y', strtotime($product->expected_availability_date)) }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $product->product_status == 'available' ? 'status-active' : 'status-pending' }}">
                                {{ ucfirst($product->product_status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Inventory by Quality Grade</div>
                <div class="chart-placeholder">
                    Chart: Quality Grade Distribution
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-box-open"></i>
            <h3>No Inventory Data</h3>
            <p>No products found in inventory</p>
        </div>
    @endif
@endsection

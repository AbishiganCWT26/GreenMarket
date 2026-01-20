@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-file-invoice"></i> COD Payment Reconciliation Report
    </div>

    @if(count($data) > 0)
        @php
            $totalOrders = count($data);
            $totalOrderAmount = collect($data)->sum('order_amount');
            $totalRecordedPayment = collect($data)->sum('recorded_payment');
            $totalVariance = collect($data)->sum('variance');
            $perfectMatches = collect($data)->where('variance', 0)->count();
            $overpayments = collect($data)->where('variance', '<', 0)->count();
            $underpayments = collect($data)->where('variance', '>', 0)->count();
            $reconciliationRate = ($perfectMatches / max($totalOrders, 1)) * 100;
            $collectionEfficiency = ($totalRecordedPayment / max($totalOrderAmount, 1)) * 100;
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalOrders }}</div>
                <div class="stat-label">COD Orders</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">Rs. {{ number_format($totalOrderAmount, 2) }}</div>
                <div class="stat-label">Order Amount</div>
            </div>
            <div class="stat-card {{ $collectionEfficiency >= 95 ? 'success' : 'warning' }}">
                <div class="stat-value">Rs. {{ number_format($totalRecordedPayment, 2) }}</div>
                <div class="stat-label">Recorded Payment</div>
            </div>
            <div class="stat-card {{ $totalVariance == 0 ? 'success' : 'warning' }}">
                <div class="stat-value">Rs. {{ number_format(abs($totalVariance), 2) }}</div>
                <div class="stat-label">Total Variance</div>
            </div>
        </div>

        <div class="highlight {{ $reconciliationRate >= 95 ? 'success' : ($reconciliationRate >= 85 ? 'info' : 'warning') }}">
            <h4>💰 COD Reconciliation Status</h4>
            <p>
                Reconciliation Rate: <strong>{{ number_format($reconciliationRate, 1) }}%</strong><br>
                Collection Efficiency: <strong>{{ number_format($collectionEfficiency, 1) }}%</strong><br>
                Variance Analysis: <strong>{{ $underpayments }} Under</strong> | <strong>{{ $overpayments }} Over</strong> | <strong>{{ $perfectMatches }} Perfect</strong><br>
                Net Position:
                <strong>
                    @if($totalVariance > 0)
                        <span class="warning">-Rs. {{ number_format($totalVariance, 2) }} (Shortfall)</span>
                    @elseif($totalVariance < 0)
                        <span class="info">+Rs. {{ number_format(abs($totalVariance), 2) }} (Excess)</span>
                    @else
                        <span class="success">Perfect Balance</span>
                    @endif
                </strong>
            </p>
        </div>

        @if($underpayments > 0 || $overpayments > 0)
        <div class="highlight warning">
            <h4>⚠️ Reconciliation Issues Detected</h4>
            <p>
                {{ $underpayments + $overpayments }} orders require reconciliation<br>
                Total amount in dispute: <strong>Rs. {{ number_format(abs(collect($data)->where('variance', '!=', 0)->sum('variance')), 2) }}</strong><br>
                @if($underpayments > 0)
                    {{ $underpayments }} orders underpaid totaling <strong>Rs. {{ number_format(collect($data)->where('variance', '>', 0)->sum('variance'), 2) }}</strong><br>
                @endif
                @if($overpayments > 0)
                    {{ $overpayments }} orders overpaid totaling <strong>Rs. {{ number_format(abs(collect($data)->where('variance', '<', 0)->sum('variance')), 2) }}</strong>
                @endif
            </p>
        </div>
        @endif

        <table class="report-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer Name</th>
                    <th>Farmer Name</th>
                    <th>Order Amount</th>
                    <th>Recorded Payment</th>
                    <th>Variance</th>
                    <th>Payment Date</th>
                    <th>Reconciliation Status</th>
                    <th>Variance Type</th>
                    <th>Action Required</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $order)
                    <tr>
                        <td>{{ $order->order_id }}</td>
                        <td>{{ $order->buyer_name }}</td>
                        <td>{{ $order->farmer_name }}</td>
                        <td class="numeric">Rs. {{ number_format($order->order_amount, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($order->recorded_payment, 2) }}</td>
                        <td class="numeric">
                            @if($order->variance > 0)
                                <span class="warning">-Rs. {{ number_format($order->variance, 2) }}</span>
                            @elseif($order->variance < 0)
                                <span class="info">+Rs. {{ number_format(abs($order->variance), 2) }}</span>
                            @else
                                <span class="success">Rs. 0.00</span>
                            @endif
                        </td>
                        <td>{{ date('M d, Y', strtotime($order->payment_date)) }}</td>
                        <td>
                            @if($order->variance == 0)
                                <span class="success">✅ Reconciled</span>
                            @elseif(abs($order->variance) <= 10)
                                <span class="info">⚠️ Minor Issue</span>
                            @elseif(abs($order->variance) <= 100)
                                <span class="warning">⚠️ Moderate Issue</span>
                            @else
                                <span class="warning">❌ Major Issue</span>
                            @endif
                        </td>
                        <td>
                            @if($order->variance > 0)
                                <span class="warning">Underpayment</span>
                            @elseif($order->variance < 0)
                                <span class="info">Overpayment</span>
                            @else
                                <span class="success">Perfect Match</span>
                            @endif
                        </td>
                        <td>
                            @if($order->variance > 500)
                                <span class="warning">URGENT: Collect</span>
                            @elseif($order->variance > 100)
                                <span class="warning">Follow-up</span>
                            @elseif($order->variance > 0)
                                <span class="info">Review</span>
                            @elseif($order->variance < -100)
                                <span class="info">Verify Overpayment</span>
                            @else
                                <span class="success">Complete</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Perfect Reconciliation Rate</div>
                <div class="grid-value">{{ number_format($reconciliationRate, 1) }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Collection Efficiency</div>
                <div class="grid-value">{{ number_format($collectionEfficiency, 1) }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Avg Variance per Order</div>
                <div class="grid-value">Rs. {{ number_format(collect($data)->avg('variance'), 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Top 3 Variances</div>
                <div class="grid-value">
                    @php
                        $topVariances = collect($data)->sortByDesc(function($o) { return abs($o->variance); })->take(3);
                    @endphp
                    @foreach($topVariances as $order)
                        #{{ $order->order_id }}: Rs. {{ number_format(abs($order->variance), 2) }}
                        @if(!$loop->last)<br>@endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-search"></i>
            <p><strong>🔍 Reconciliation Procedures:</strong></p>
            <ol>
                <li><strong>Underpayments (> Rs. 100):</strong> Contact buyer for balance collection within 48 hours</li>
                <li><strong>Overpayments (> Rs. 100):</strong> Verify payment and arrange refund if confirmed</li>
                <li><strong>Minor Variances (≤ Rs. 100):</strong> Document and monitor for patterns</li>
                <li><strong>Repeated Issues:</strong> Review buyer/farmer payment history</li>
                <li><strong>System Updates:</strong> Correct records after verification</li>
            </ol>
        </div>

        <div class="note">
            <i class="fas fa-clipboard-check"></i>
            <p><strong>Process Improvement:</strong>
                1. Implement payment verification at pickup<br>
                2. Train lead farmers on accurate payment recording<br>
                3. Set up automated variance alerts<br>
                4. Monthly reconciliation reviews<br>
                5. Escalate unresolved variances after 7 days
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">COD Payment Variance Analysis</div>
                <div class="chart-placeholder">
                    Chart: Order Amount vs Recorded Payment Comparison
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-check-double"></i>
            <h3>Perfect Reconciliation</h3>
            <p>All COD payments match order amounts exactly</p>
        </div>
    @endif
@endsection

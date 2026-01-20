@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-calendar-check"></i> Order Fulfillment Timeline Analysis
    </div>

    @if(count($data) > 0)
        @php
            $totalOrders = count($data);
            $avgDuration = collect($data)->avg('total_duration');
            $fastestOrder = collect($data)->sortBy('total_duration')->first();
            $slowestOrder = collect($data)->sortByDesc('total_duration')->first();
            $onTimeOrders = collect($data)->where('total_duration', '<=', 3)->count();
            $delayedOrders = collect($data)->where('total_duration', '>', 7)->count();
            $onTimeRate = ($onTimeOrders / max($totalOrders, 1)) * 100;
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalOrders }}</div>
                <div class="stat-label">Orders Tracked</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">{{ $onTimeOrders }}</div>
                <div class="stat-label">On-Time Orders</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $delayedOrders }}</div>
                <div class="stat-label">Delayed Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($avgDuration, 1) }}</div>
                <div class="stat-label">Avg Days</div>
            </div>
        </div>

        <div class="highlight {{ $onTimeRate >= 90 ? 'success' : ($onTimeRate >= 75 ? 'info' : 'warning') }}">
            <h4>⏱️ Fulfillment Performance</h4>
            <p>
                On-Time Rate: <strong>{{ number_format($onTimeRate, 1) }}%</strong><br>
                Average Fulfillment Time: <strong>{{ number_format($avgDuration, 1) }} days</strong><br>
                @if($onTimeRate >= 90)
                    ✅ Excellent fulfillment performance
                @elseif($onTimeRate >= 75)
                    ⚠️ Good, but room for improvement
                @else
                    ❌ Fulfillment delays need attention
                @endif
                <br>
                {{ $delayedOrders }} orders took more than 7 days to complete
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Payment Date</th>
                    <th>Pickup Date</th>
                    <th>Completion Date</th>
                    <th>Total Duration</th>
                    <th>Payment Delay</th>
                    <th>Pickup Delay</th>
                    <th>Status</th>
                    <th>Bottleneck</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $order)
                    @php
                        $paymentDelay = $order->payment_date ? Carbon\Carbon::parse($order->order_date)->diffInDays(Carbon\Carbon::parse($order->payment_date)) : null;
                        $pickupDelay = $order->pickup_date && $order->payment_date ? Carbon\Carbon::parse($order->payment_date)->diffInDays(Carbon\Carbon::parse($order->pickup_date)) : null;
                        $completionDelay = $order->completion_date && $order->pickup_date ? Carbon\Carbon::parse($order->pickup_date)->diffInDays(Carbon\Carbon::parse($order->completion_date)) : null;

                        $bottleneck = '';
                        if($paymentDelay > 2) $bottleneck = 'Payment';
                        elseif($pickupDelay > 2) $bottleneck = 'Pickup';
                        elseif($completionDelay > 1) $bottleneck = 'Completion';
                        else $bottleneck = 'Efficient';
                    @endphp
                    <tr>
                        <td>#{{ $order->order_id }}</td>
                        <td>{{ date('M d', strtotime($order->order_date)) }}</td>
                        <td>
                            @if($order->payment_date)
                                {{ date('M d', strtotime($order->payment_date)) }}
                                @if($paymentDelay > 2)
                                    <br><small class="warning">(+{{ $paymentDelay }}d)</small>
                                @endif
                            @else
                                <span class="warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($order->pickup_date)
                                {{ date('M d', strtotime($order->pickup_date)) }}
                                @if($pickupDelay > 2)
                                    <br><small class="warning">(+{{ $pickupDelay }}d)</small>
                                @endif
                            @else
                                <span class="warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($order->completion_date)
                                {{ date('M d', strtotime($order->completion_date)) }}
                            @else
                                <span class="warning">Ongoing</span>
                            @endif
                        </td>
                        <td>
                            @if($order->total_duration <= 2)
                                <span class="success">{{ $order->total_duration }} days</span>
                            @elseif($order->total_duration <= 5)
                                <span class="info">{{ $order->total_duration }} days</span>
                            @else
                                <span class="warning">{{ $order->total_duration }} days</span>
                            @endif
                        </td>
                        <td>
                            @if($paymentDelay <= 1)
                                <span class="success">Fast</span>
                            @elseif($paymentDelay <= 3)
                                <span class="info">Normal</span>
                            @else
                                <span class="warning">Slow</span>
                            @endif
                        </td>
                        <td>
                            @if($pickupDelay <= 1)
                                <span class="success">Fast</span>
                            @elseif($pickupDelay <= 3)
                                <span class="info">Normal</span>
                            @else
                                <span class="warning">Slow</span>
                            @endif
                        </td>
                        <td>
                            @if($order->total_duration <= 3)
                                <span class="success">✅ On Time</span>
                            @elseif($order->total_duration <= 7)
                                <span class="info">⚠️ Delayed</span>
                            @else
                                <span class="warning">❌ Excessive</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $bottleneck == 'Efficient' ? 'success' : 'warning' }}">
                                {{ $bottleneck }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Avg Payment Processing</div>
                <div class="grid-value">
                    @php
                        $avgPaymentDelay = collect($data)->filter(function($o) { return $o->payment_date; })->avg(function($o) {
                            return Carbon\Carbon::parse($o->order_date)->diffInDays(Carbon\Carbon::parse($o->payment_date));
                        });
                    @endphp
                    {{ number_format($avgPaymentDelay, 1) }} days
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Avg Pickup Time</div>
                <div class="grid-value">
                    @php
                        $avgPickupDelay = collect($data)->filter(function($o) { return $o->pickup_date && $o->payment_date; })->avg(function($o) {
                            return Carbon\Carbon::parse($o->payment_date)->diffInDays(Carbon\Carbon::parse($o->pickup_date));
                        });
                    @endphp
                    {{ number_format($avgPickupDelay, 1) }} days
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Most Common Bottleneck</div>
                <div class="grid-value">
                    @php
                        $bottlenecks = [];
                        foreach($data as $order) {
                            $paymentDelay = $order->payment_date ? Carbon\Carbon::parse($order->order_date)->diffInDays(Carbon\Carbon::parse($order->payment_date)) : null;
                            $pickupDelay = $order->pickup_date && $order->payment_date ? Carbon\Carbon::parse($order->payment_date)->diffInDays(Carbon\Carbon::parse($order->pickup_date)) : null;

                            if($paymentDelay > 2) $bottleneck = 'Payment';
                            elseif($pickupDelay > 2) $bottleneck = 'Pickup';
                            else $bottleneck = 'Efficient';

                            $bottlenecks[$bottleneck] = ($bottlenecks[$bottleneck] ?? 0) + 1;
                        }
                        arsort($bottlenecks);
                        $topBottleneck = array_key_first($bottlenecks);
                    @endphp
                    {{ $topBottleneck }} ({{ $bottlenecks[$topBottleneck] ?? 0 }})
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">SLA Compliance Rate</div>
                <div class="grid-value">{{ number_format($onTimeRate, 1) }}%</div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-tachometer-alt"></i>
            <p><strong>Process Optimization:</strong>
                1. Address identified bottlenecks in fulfillment process<br>
                2. Implement reminders for payment and pickup stages<br>
                3. Set up automated status updates for buyers<br>
                4. Train lead farmers on timely order processing<br>
                5. Monitor fulfillment metrics weekly
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Fulfillment Timeline Distribution</div>
                <div class="chart-placeholder">
                    Chart: Order Processing Stages Analysis
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-shipping-fast"></i>
            <h3>No Fulfillment Data</h3>
            <p>No order fulfillment timeline data available</p>
        </div>
    @endif
@endsection

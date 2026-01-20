@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-search-dollar"></i> Financial Audit & Transaction Verification
    </div>

    @if(count($data) > 0)
        @php
            $totalTransactions = count($data);
            $totalAmount = collect($data)->sum('amount');
            $completedPayments = collect($data)->where('payment_status', 'completed')->count();
            $failedPayments = collect($data)->where('payment_status', 'failed')->count();
            $pendingPayments = collect($data)->where('payment_status', 'pending')->count();
            $codTransactions = collect($data)->where('payment_method', 'COD')->count();
            $onlineTransactions = collect($data)->where('payment_method', 'online')->count();
            $successRate = ($completedPayments / max($totalTransactions, 1)) * 100;
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalTransactions }}</div>
                <div class="stat-label">Transactions</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($totalAmount, 2) }}</div>
                <div class="stat-label">Total Amount</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">{{ $completedPayments }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $failedPayments }}</div>
                <div class="stat-label">Failed</div>
            </div>
        </div>

        <div class="highlight {{ $successRate >= 95 ? 'success' : ($successRate >= 85 ? 'info' : 'warning') }}">
            <h4>💰 Transaction Audit Summary</h4>
            <p>
                Transaction Success Rate: <strong>{{ number_format($successRate, 1) }}%</strong><br>
                Total Transaction Value: <strong>Rs. {{ number_format($totalAmount, 2) }}</strong><br>
                Payment Method Split: <strong>{{ $codTransactions }} COD</strong> | <strong>{{ $onlineTransactions }} Online</strong><br>
                @if($successRate >= 95)
                    ✅ Excellent transaction reliability
                @elseif($successRate >= 85)
                    ⚠️ Good, but monitor failures
                @else
                    ❌ High failure rate requires investigation
                @endif
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Order Number</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Verified By</th>
                    <th>Verification</th>
                    <th>Audit Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $transaction)
                    <tr>
                        <td>#{{ $transaction->transaction_id }}</td>
                        <td>{{ $transaction->order_number }}</td>
                        <td class="numeric">Rs. {{ number_format($transaction->amount, 2) }}</td>
                        <td>
                            @if($transaction->payment_method == 'COD')
                                <span class="info">💰 COD</span>
                            @else
                                <span class="success">💳 Online</span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->payment_status == 'completed')
                                <span class="success">✅ Completed</span>
                            @elseif($transaction->payment_status == 'pending')
                                <span class="info">🔄 Pending</span>
                            @elseif($transaction->payment_status == 'failed')
                                <span class="warning">❌ Failed</span>
                            @else
                                <span class="warning">❓ Unknown</span>
                            @endif
                        </td>
                        <td>{{ date('M d, Y', strtotime($transaction->payment_date)) }}</td>
                        <td>{{ $transaction->verified_by ?? 'System' }}</td>
                        <td>
                            @if($transaction->verified_by)
                                <span class="success">✅ Verified</span>
                            @else
                                <span class="warning">⚠️ Unverified</span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->payment_status == 'completed' && $transaction->verified_by)
                                <span class="success">Audited</span>
                            @elseif($transaction->payment_status == 'completed')
                                <span class="info">Needs Audit</span>
                            @elseif($transaction->payment_status == 'failed')
                                <span class="warning">Investigate</span>
                            @else
                                <span class="info">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->payment_status == 'failed')
                                <span class="warning">Review</span>
                            @elseif(!$transaction->verified_by && $transaction->payment_status == 'completed')
                                <span class="info">Verify</span>
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
                <div class="grid-label">Avg Transaction Value</div>
                <div class="grid-value">Rs. {{ number_format($totalAmount / max($totalTransactions, 1), 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">COD vs Online Split</div>
                <div class="grid-value">
                    {{ number_format(($codTransactions / max($totalTransactions, 1)) * 100, 1) }}% COD
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Verification Rate</div>
                <div class="grid-value">
                    @php
                        $verifiedCount = collect($data)->where('verified_by', '!=', null)->count();
                        $verificationRate = ($verifiedCount / max($completedPayments, 1)) * 100;
                    @endphp
                    {{ number_format($verificationRate, 1) }}%
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Audit Coverage</div>
                <div class="grid-value">
                    @php
                        $auditedCount = collect($data)->where('payment_status', 'completed')->where('verified_by', '!=', null)->count();
                        $auditCoverage = ($auditedCount / max($completedPayments, 1)) * 100;
                    @endphp
                    {{ number_format($auditCoverage, 1) }}%
                </div>
            </div>
        </div>

        <div class="note warning">
            <i class="fas fa-exclamation-triangle"></i>
            <p><strong>⚠️ Audit Findings & Recommendations:</strong></p>
            <ol>
                <li><strong>Unverified Transactions:</strong> {{ $completedPayments - $verifiedCount }} completed payments need verification</li>
                <li><strong>Failed Payments:</strong> {{ $failedPayments }} failed transactions require investigation</li>
                <li><strong>Pending Transactions:</strong> {{ $pendingPayments }} pending payments need follow-up</li>
                <li><strong>Audit Gaps:</strong> {{ $completedPayments - $auditedCount }} completed transactions lack audit trail</li>
            </ol>
        </div>

        <div class="note">
            <i class="fas fa-clipboard-check"></i>
            <p><strong>Audit Procedures:</strong>
                1. Verify all completed transactions within 48 hours<br>
                2. Investigate failed transactions immediately<br>
                3. Document audit findings for each transaction<br>
                4. Reconcile payment records with bank statements<br>
                5. Maintain audit trail for financial compliance
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Transaction Status Distribution & Verification Status</div>
                <div class="chart-placeholder">
                    Chart: Financial Audit Metrics
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-file-invoice-dollar"></i>
            <h3>No Transaction Data</h3>
            <p>No financial transaction data available for audit</p>
        </div>
    @endif
@endsection

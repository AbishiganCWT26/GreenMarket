@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-comments"></i> Dispute & Feedback Analysis
    </div>

    @if(count($data) > 0)
        @php
            $totalComplaints = count($data);
            $resolvedComplaints = collect($data)->where('status', 'resolved')->count();
            $pendingComplaints = collect($data)->where('status', 'new')->count();
            $inProgressComplaints = collect($data)->where('status', 'in_progress')->count();
            $avgResolutionTime = collect($data)->where('resolution_days', '>', 0)->avg('resolution_days');
            $resolutionRate = ($resolvedComplaints / max($totalComplaints, 1)) * 100;
            $commonTypes = collect($data)->groupBy('complaint_type')->map->count()->sortDesc()->take(5);
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalComplaints }}</div>
                <div class="stat-label">Total Cases</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">{{ $resolvedComplaints }}</div>
                <div class="stat-label">Resolved</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $pendingComplaints }}</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card info">
                <div class="stat-value">{{ number_format($avgResolutionTime, 1) }}</div>
                <div class="stat-label">Avg Resolution Days</div>
            </div>
        </div>

        <div class="highlight {{ $resolutionRate >= 80 ? 'success' : ($resolutionRate >= 60 ? 'info' : 'warning') }}">
            <h4>🛡️ Dispute Resolution Performance</h4>
            <p>
                Resolution Rate: <strong>{{ number_format($resolutionRate, 1) }}%</strong><br>
                Average Resolution Time: <strong>{{ number_format($avgResolutionTime, 1) }} days</strong><br>
                @if($resolutionRate >= 80 && $avgResolutionTime <= 3)
                    ✅ Excellent resolution performance
                @elseif($resolutionRate >= 60)
                    ⚠️ Good, but improvements possible
                @else
                    ❌ Resolution performance needs attention
                @endif
                <br>
                {{ $pendingComplaints }} cases awaiting resolution
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Case ID</th>
                    <th>Complainant</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Resolution Days</th>
                    <th>Feedback Rating</th>
                    <th>Resolved By</th>
                    <th>Priority</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $case)
                    <tr>
                        <td>#{{ $case->complaint_id }}</td>
                        <td>{{ $case->complainant }}</td>
                        <td>
                            @php
                                $typeIcons = [
                                    'payment_issue' => '💰',
                                    'product_quality' => '⭐',
                                    'wrong_location' => '📍',
                                    'farmer_contact' => '📞',
                                    'availability_issue' => '📦',
                                    'invoice_error' => '🧾',
                                    'other' => '❓'
                                ];
                            @endphp
                            {{ $typeIcons[$case->complaint_type] ?? '📝' }} {{ ucwords(str_replace('_', ' ', $case->complaint_type)) }}
                        </td>
                        <td>
                            @if($case->status == 'resolved')
                                <span class="success">✅ Resolved</span>
                            @elseif($case->status == 'in_progress')
                                <span class="info">🔄 In Progress</span>
                            @else
                                <span class="warning">🕒 Pending</span>
                            @endif
                        </td>
                        <td>{{ date('M d, Y', strtotime($case->created_at)) }}</td>
                        <td>
                            @if($case->resolution_days)
                                @if($case->resolution_days <= 2)
                                    <span class="success">{{ $case->resolution_days }} days</span>
                                @elseif($case->resolution_days <= 5)
                                    <span class="info">{{ $case->resolution_days }} days</span>
                                @else
                                    <span class="warning">{{ $case->resolution_days }} days</span>
                                @endif
                            @else
                                <span class="warning">Ongoing</span>
                            @endif
                        </td>
                        <td>
                            @if($case->rating)
                                @if($case->rating >= 4)
                                    <span class="success">⭐⭐⭐⭐⭐ ({{ $case->rating }}/5)</span>
                                @elseif($case->rating >= 3)
                                    <span class="info">⭐⭐⭐ ({{ $case->rating }}/5)</span>
                                @else
                                    <span class="warning">⭐ ({{ $case->rating }}/5)</span>
                                @endif
                            @else
                                <span class="info">No rating</span>
                            @endif
                        </td>
                        <td>{{ $case->resolved_by ?? 'Not assigned' }}</td>
                        <td>
                            @if($case->status == 'new' && now()->diffInDays(Carbon\Carbon::parse($case->created_at)) > 3)
                                <span class="warning">HIGH</span>
                            @elseif($case->status == 'new')
                                <span class="info">MEDIUM</span>
                            @else
                                <span class="success">LOW</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Most Common Issue Types</div>
                <div class="grid-value">
                    @foreach($commonTypes as $type => $count)
                        {{ ucwords(str_replace('_', ' ', $type)) }}: {{ $count }}
                        @if(!$loop->last)<br>@endif
                    @endforeach
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">SLA Compliance Rate</div>
                <div class="grid-value">
                    @php
                        $slaCompliant = collect($data)->where('resolution_days', '<=', 5)->where('status', 'resolved')->count();
                        $slaRate = $resolvedComplaints > 0 ? ($slaCompliant / $resolvedComplaints) * 100 : 0;
                    @endphp
                    {{ number_format($slaRate, 1) }}%
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Customer Satisfaction</div>
                <div class="grid-value">
                    @php
                        $ratedCases = collect($data)->where('rating', '>', 0);
                        $avgRating = $ratedCases->count() > 0 ? $ratedCases->avg('rating') : 0;
                        $satisfactionRate = ($avgRating / 5) * 100;
                    @endphp
                    {{ number_format($satisfactionRate, 1) }}%
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Pending > 5 Days</div>
                <div class="grid-value">
                    {{ collect($data)->where('status', '!=', 'resolved')->filter(function($case) {
                        return now()->diffInDays(Carbon\Carbon::parse($case->created_at)) > 5;
                    })->count() }}
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-headset"></i>
            <p><strong>Service Improvement Plan:</strong>
                1. Address common complaint types with targeted solutions<br>
                2. Implement 24-hour response SLA for new complaints<br>
                3. Train facilitators on conflict resolution techniques<br>
                4. Create knowledge base for common issues<br>
                5. Monitor satisfaction ratings for quality assurance
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Complaint Type Distribution & Resolution Trends</div>
                <div class="chart-placeholder">
                    Chart: Dispute Analysis
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-smile"></i>
            <h3>No Dispute Cases</h3>
            <p>No disputes or feedback cases recorded in this period</p>
        </div>
    @endif
@endsection

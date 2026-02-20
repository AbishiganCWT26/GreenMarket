@extends('facilitator.layouts.facilitator_master')

@section('title', 'User Complaints')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/facilitator-dashboard.css') }}">
<style>
    .complaints-container {
        padding: 25px;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-section h1 {
        font-size: 28px;
        color: var(--text-color);
        margin: 0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-section h1 i {
        color: var(--primary-green);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-info h3 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: var(--text-color);
    }

    .stat-info p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .complaints-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }

    .complaint-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
    }

    .complaint-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-green);
    }

    .complaint-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--primary-green);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .complaint-card:hover::before {
        opacity: 1;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f1f5f9;
    }

    .complaint-id {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .complaint-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-color);
        margin: 0;
        line-height: 1.4;
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-new { background: #eff6ff; color: #3b82f6; }
    .status-in_progress { background: #fffbeb; color: #d97706; }
    .status-resolved { background: #f0fdf4; color: #16a34a; }
    .status-rejected { background: #fef2f2; color: #dc2626; }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .detail-label {
        color: #64748b;
        font-weight: 500;
    }

    .detail-value {
        color: var(--text-color);
        font-weight: 600;
    }

    .complaint-description {
        background: #f8fafc;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
        font-size: 14px;
        color: #334155;
        line-height: 1.6;
        border-left: 3px solid #cbd5e1;
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
        font-size: 12px;
        color: #94a3b8;
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px;
        background: white;
        border-radius: 12px;
        border: 2px dashed #e2e8f0;
    }

    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="complaints-container">
    <div class="header-section">
        <h1><i class="fa-solid fa-triangle-exclamation"></i> User Complaints</h1>
    </div>

    <!-- Stats Section -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fa-solid fa-circle-plus"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $complaintStats['new'] }}</h3>
                <p>New</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fffbeb; color: #d97706;">
                <i class="fa-solid fa-spinner"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $complaintStats['in_progress'] }}</h3>
                <p>In Progress</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $complaintStats['resolved'] }}</h3>
                <p>Resolved</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef2f2; color: #dc2626;">
                <i class="fa-solid fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $complaintStats['rejected'] }}</h3>
                <p>Rejected</p>
            </div>
        </div>
    </div>

    @if($complaints->count() > 0)
    <div class="complaints-grid">
        @foreach($complaints as $complaint)
        <div class="complaint-card">
            <div class="card-header">
                <div>
                    <div class="complaint-id">#{{ $complaint->id }}</div>
                    <h3 class="complaint-title">{{ Str::limit($complaint->subject, 30) }}</h3>
                </div>
                <span class="status-badge status-{{ $complaint->status }}">
                    @if($complaint->status == 'new') <i class="fa-solid fa-circle-plus"></i>
                    @elseif($complaint->status == 'in_progress') <i class="fa-solid fa-spinner"></i>
                    @elseif($complaint->status == 'resolved') <i class="fa-solid fa-check"></i>
                    @else <i class="fa-solid fa-times"></i>
                    @endif
                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                </span>
            </div>

            <div class="complaint-details">
                <div class="detail-row">
                    <span class="detail-label">Complainant:</span>
                    <span class="detail-value">{{ $complaint->complainant ? $complaint->complainant->name : 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Against:</span>
                    <span class="detail-value">{{ $complaint->againstUser ? $complaint->againstUser->name : 'N/A' }}</span>
                </div>
                
                <div class="complaint-description">
                    {{ Str::limit($complaint->description, 100) }}
                </div>
            </div>

            <div class="card-footer">
                <div class="timestamp">
                    <i class="fa-regular fa-clock"></i> {{ $complaint->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <i class="fa-solid fa-check-double"></i>
        <h3>No Complaints Found</h3>
        <p>There are no complaints to display at this time.</p>
    </div>
    @endif
</div>
@endsection

@extends('facilitator.layouts.facilitator_master')

@section('title', 'Facilitator Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/facilitator-dashboard.css') }}">
@endsection

@section('content')
<div class="welcome-card">
    <div class="welcome-content">
        <h2>
            <i class="fa-solid fa-hand-wave" style="color: var(--primary-green);"></i>
            Welcome back, {{ $facilitator->name ?? 'Facilitator' }}!
        </h2>
        <p>Field Officer Dashboard - Manage system standards and user support</p>
    </div>
    <div class="welcome-icon">
        <i class="fa-solid fa-hands-helping"></i>
    </div>
</div>

<div class="stat-cards-row">
    <div class="stat-card" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
        <div class="stat-icon">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $totalCategories ?? 0 }}</h3>
            <p>Categories</p>
        </div>
        <div class="stat-arrow">
            <i class="fa-solid fa-arrow-right"></i>
        </div>
    </div>

    <div class="stat-card" style="background: linear-gradient(135deg, var(--blue), #1e40af);">
        <div class="stat-icon">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $totalUsers ?? 0 }}</h3>
            <p>Users</p>
        </div>
        <div class="stat-arrow">
            <i class="fa-solid fa-arrow-right"></i>
        </div>
    </div>

    <div class="stat-card" style="background: linear-gradient(135deg, var(--accent-amber), #b45309);">
        <div class="stat-icon">
            <i class="fa-solid fa-flag"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $pendingComplaints ?? 0 }}</h3>
            <p>Pending</p>
        </div>
        <div class="stat-arrow">
            <i class="fa-solid fa-arrow-right"></i>
        </div>
    </div>

    <div class="stat-card" style="background: linear-gradient(135deg, var(--purple), #6d28d9);">
        <div class="stat-icon">
            <i class="fa-solid fa-ruler-combined"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $systemStandards['units'] ?? 0 }}</h3>
            <p>Standards</p>
        </div>
        <div class="stat-arrow">
            <i class="fa-solid fa-arrow-right"></i>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <div class="card-header">
            <h4>
                <i class="fa-solid fa-bolt"></i>
                Quick Actions
            </h4>
            <button class="btn-refresh" id="refreshStats">
                <i class="fa-solid fa-rotate"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="quick-actions-grid">
                <div class="quick-action-card" onclick="window.location.href='{{ route('facilitator.taxonomy') }}'">
                    <div class="action-icon" style="background: rgba(16,185,129,0.1);">
                        <i class="fa-solid fa-layer-group" style="color: var(--primary-green);"></i>
                    </div>
                    <div class="action-content">
                        <h5>Manage Category</h5>
                        <p>Add/edit product categories</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>

                <div class="quick-action-card" onclick="window.location.href='{{ route('facilitator.users') }}'">
                    <div class="action-icon" style="background: rgba(59,130,246,0.1);">
                        <i class="fa-solid fa-user-gear" style="color: var(--blue);"></i>
                    </div>
                    <div class="action-content">
                        <h5>Manage Users</h5>
                        <p>View and manage users</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>

                <div class="quick-action-card" onclick="showComingSoon('System Standards')">
                    <div class="action-icon" style="background: rgba(139,92,246,0.1);">
                        <i class="fa-solid fa-ruler-combined" style="color: var(--purple);"></i>
                    </div>
                    <div class="action-content">
                        <h5>System Standards</h5>
                        <p>Manage units and grades</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>

                <div class="quick-action-card" onclick="showComingSoon('Reports')">
                    <div class="action-icon" style="background: rgba(245,158,11,0.1);">
                        <i class="fa-solid fa-file-chart-column" style="color: var(--accent-amber);"></i>
                    </div>
                    <div class="action-content">
                        <h5>Generate Reports</h5>
                        <p>View performance reports</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h4>
                <i class="fa-solid fa-clock-rotate-left"></i>
                Recent Activities
            </h4>
        </div>
        <div class="card-body">
            <div class="activity-list">
                @if(isset($recentActivities) && count($recentActivities) > 0)
                    @foreach($recentActivities as $activity)
                    <div class="activity-item">
                        <div class="activity-icon">
                            @if($activity->notification_type == 'admin_alert')
                                <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                            @else
                                <i class="fa-solid fa-info-circle text-info"></i>
                            @endif
                        </div>
                        <div class="activity-content">
                            <h6>{{ $activity->title }}</h6>
                            <p>{{ Str::limit($activity->message, 60) }}</p>
                            <small>{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fa-solid fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted" style="font-size: 0.8rem;">No recent activities</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function showComingSoon(feature) {
    Swal.fire({
        title: `${feature} Coming Soon!`,
        text: 'This feature is currently under development.',
        icon: 'info',
        confirmButtonColor: '#10B981',
        background: '#ffffff',
        color: '#0f1724',
        timer: 3000,
        showConfirmButton: true,
        confirmButtonText: 'OK'
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('refreshStats');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.style.transform = 'rotate(180deg)';
            
            setTimeout(() => {
                this.style.transform = '';
                Swal.fire({
                    icon: 'success',
                    title: 'Refreshed!',
                    text: 'Statistics updated successfully',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }, 500);
        });
    }

    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.addEventListener('click', function() {
            const titles = ['Categories', 'Users', 'Complaints', 'Standards'];
            Swal.fire({
                title: titles[index],
                text: `View detailed ${titles[index].toLowerCase()} information`,
                icon: 'info',
                confirmButtonColor: '#10B981',
                background: '#ffffff'
            });
        });
    });
});
</script>
@endsection
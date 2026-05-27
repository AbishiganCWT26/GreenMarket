@extends('delivery-rider.layouts.delivery_rider_master')

@section('title', '| Completed Deliveries')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/completed-deliveries.css') }}">
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/components/empty-state.css') }}">
@endsection

@section('page-title')
    <i class="fa-solid fa-square-check text-primary me-2"></i> Completed Deliveries History
@endsection

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Stats Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-premium stats-card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stats-icon bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 20px;">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-bold text-uppercase">Today</div>
                    <div class="fs-4 fw-bold text-dark">{{ $stats['today'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium stats-card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stats-icon bg-light-success text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 20px;">
                    <i class="fa-solid fa-calendar-week"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-bold text-uppercase">This Week</div>
                    <div class="fs-4 fw-bold text-dark">{{ $stats['week'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium stats-card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stats-icon bg-light-info text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 20px;">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-bold text-uppercase">This Month</div>
                    <div class="fs-4 fw-bold text-dark">{{ $stats['month'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="delivery-list-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Delivery History</h4>
            <p class="text-secondary mb-0">Review previously completed jobs and track accumulated platform earnings</p>
        </div>
        
        {{-- Filters and Search Form --}}
        <form action="{{ route('delivery-rider.completed-deliveries') }}" method="GET" class="d-flex flex-column flex-md-row gap-2 align-items-md-center filters-form">
            <div class="input-group input-group-sm w-auto">
                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search order or buyer..." value="{{ request('search') }}">
            </div>
            
            <select name="date_filter" class="form-select form-select-sm w-auto" id="dateFilterSelect">
                <option value="all" {{ request('date_filter') == 'all' ? 'selected' : '' }}>All Time</option>
                <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>This Month</option>
                <option value="custom" {{ request('date_filter') == 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>

            <div class="custom-date-range d-flex gap-2 align-items-center {{ request('date_filter') == 'custom' ? '' : 'd-none' }}" id="customDateRange">
                <input type="date" name="start_date" class="form-control form-control-sm w-auto" value="{{ request('start_date') }}">
                <span class="text-muted small">to</span>
                <input type="date" name="end_date" class="form-control form-control-sm w-auto" value="{{ request('end_date') }}">
            </div>

            <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
            @if(request()->anyFilled(['search', 'date_filter', 'start_date', 'end_date']))
                <a href="{{ route('delivery-rider.completed-deliveries') }}" class="btn btn-light btn-sm px-3 border text-muted">Clear</a>
            @endif
        </form>
    </div>

    @if($deliveries->isEmpty())
        @include('delivery-rider.components.empty_state', [
            'icon' => 'fa-folder-open',
            'title' => 'No Completed Jobs',
            'message' => request()->anyFilled(['search', 'date_filter']) 
                            ? 'No deliveries match your current filters.' 
                            : 'You haven\'t completed any deliveries yet. When you complete an active assignment, it will appear here.',
            'actionHint' => request()->anyFilled(['search', 'date_filter']) ? '' : 'Go to Active Deliveries to complete your tasks.',
            'actionUrl' => request()->anyFilled(['search', 'date_filter']) ? route('delivery-rider.completed-deliveries') : route('delivery-rider.active-deliveries'),
            'actionText' => request()->anyFilled(['search', 'date_filter']) ? 'Clear Filters' : 'Go to Active Deliveries'
        ])
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
            @foreach($deliveries as $delivery)
                <div class="col animate__animated animate__fadeInUp">
                    @include('delivery-rider.components.completed_delivery_card', ['delivery' => $delivery])
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $deliveries->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateFilter = document.getElementById('dateFilterSelect');
    const customRange = document.getElementById('customDateRange');
    
    if (dateFilter && customRange) {
        dateFilter.addEventListener('change', function() {
            if (this.value === 'custom') {
                customRange.classList.remove('d-none');
            } else {
                customRange.classList.add('d-none');
            }
        });
    }
});
</script>
@endsection

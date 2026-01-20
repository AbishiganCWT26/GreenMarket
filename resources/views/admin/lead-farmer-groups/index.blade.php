@extends('admin.layouts.admin_master')

@section('title', 'Lead Farmer Groups Activity')
@section('page-title', 'Lead Farmer Groups Activity')

@section('styles')
<style>
    :root {
        --primary-green: #10B981;
        --dark-green: #059669;
        --body-bg: #f8fafc;
        --card-bg: #ffffff;
        --text-color: #1e293b;
        --muted: #64748b;
        --blue: #3b82f6;
        --purple: #8b5cf6;
        --yellow: #f59e0b;
        --shadow-sm: 0 1px 3px rgba(15,23,36,0.08);
        --shadow-md: 0 2px 8px rgba(15,23,36,0.1);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 10px;
        --transition: all 0.2s ease;
    }

    .groups-container {
        padding: 3px;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding: 12px 16px;
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border-left: 4px solid var(--primary-green);
    }

    .page-header h1 {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-color);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .page-header h1 i {
        color: var(--primary-green);
        font-size: 18px;
    }

    .refresh-btn {
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: var(--radius-sm);
        font-weight: 500;
        font-size: 12px;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .refresh-btn:hover {
        background: var(--dark-green);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: var(--card-bg);
        border-radius: var(--radius-md);
        padding: 12px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        border-top: 3px solid var(--primary-green);
        min-height: 70px;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-card:nth-child(2) { border-color: var(--blue); }
    .stat-card:nth-child(3) { border-color: var(--yellow); }
    .stat-card:nth-child(4) { border-color: var(--purple); }

    .stat-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .stat-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        background: rgba(16, 185, 129, 0.1);
        color: var(--primary-green);
    }

    .stat-card:nth-child(2) .stat-icon {
        background: rgba(59, 130, 246, 0.1);
        color: var(--blue);
    }

    .stat-card:nth-child(3) .stat-icon {
        background: rgba(245, 158, 11, 0.1);
        color: var(--yellow);
    }

    .stat-card:nth-child(4) .stat-icon {
        background: rgba(139, 92, 246, 0.1);
        color: var(--purple);
    }

    .stat-value {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-color);
        line-height: 1;
    }

    .stat-label {
        font-size: 11px;
        color: var(--muted);
        font-weight: 500;
        margin-top: 2px;
    }

    .table-view {
        display: block;
    }

    .table-container {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        margin-bottom: 20px;
    }

    .table-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .table-controls .title {
        font-weight: 600;
        font-size: 14px;
        color: var(--text-color);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .rows-per-page {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .rows-per-page span {
        font-size: 12px;
        color: var(--muted);
    }

    .rows-per-page select {
        padding: 4px 8px;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-sm);
        background: white;
        color: var(--text-color);
        font-size: 12px;
        cursor: pointer;
    }

    .table-responsive {
        overflow-x: auto;
        max-height: 400px;
        overflow-y: auto;
    }

    .groups-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
        font-size: 12px;
    }

    .groups-table thead {
        background: #f1f5f9;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .groups-table th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        color: var(--text-color);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .groups-table th i {
        margin-right: 4px;
        font-size: 12px;
        opacity: 0.8;
    }

    .groups-table tbody tr {
        transition: var(--transition);
        border-bottom: 1px solid #f1f5f9;
    }

    .groups-table tbody tr:hover {
        background: #f8fafc;
    }

    .groups-table td {
        padding: 10px 12px;
        color: var(--text-color);
        font-size: 12px;
        vertical-align: middle;
        height: 48px;
    }

    .rank-cell {
        text-align: center;
        width: 50px;
    }

    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-weight: 700;
        font-size: 11px;
    }

    .rank-1 .rank-badge {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: white;
    }

    .rank-2 .rank-badge {
        background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
        color: white;
    }

    .rank-3 .rank-badge {
        background: linear-gradient(135deg, #CD7F32, #A0522D);
        color: white;
    }

    .rank-4-plus .rank-badge {
        background: #f1f5f9;
        color: var(--muted);
    }

    .group-name-cell {
        min-width: 160px;
        max-width: 200px;
    }

    .group-name {
        font-weight: 600;
        color: var(--text-color);
        font-size: 13px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .group-number {
        font-size: 11px;
        color: var(--muted);
        margin-top: 2px;
    }

    .success-rate-cell {
        min-width: 90px;
    }

    .success-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 11px;
    }

    .success-high {
        color: var(--primary-green);
        background: rgba(16, 185, 129, 0.1);
    }

    .success-medium {
        color: var(--yellow);
        background: rgba(245, 158, 11, 0.1);
    }

    .success-low {
        color: #f97316;
        background: rgba(249, 115, 22, 0.1);
    }

    .success-poor {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
    }

    .success-badge i {
        font-size: 10px;
    }

    .sales-count, .farmers-count, .products-count {
        text-align: center;
        min-width: 70px;
        font-weight: 600;
        font-size: 13px;
    }

    .sales-count { color: var(--blue); }
    .farmers-count { color: var(--purple); }
    .products-count { color: var(--yellow); }

    .total-sales {
        text-align: right;
        min-width: 100px;
        font-weight: 700;
        color: var(--primary-green);
        font-size: 13px;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: var(--muted);
    }

    .no-data i {
        font-size: 32px;
        margin-bottom: 12px;
        opacity: 0.3;
    }

    .no-data p {
        font-size: 14px;
        margin: 0;
    }

    .card-view {
        display: none;
    }

    .cards-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 20px;
    }

    .group-card {
        background: var(--card-bg);
        border-radius: var(--radius-md);
        padding: 12px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        border-left: 3px solid var(--primary-green);
        animation: slideIn 0.3s ease forwards;
        opacity: 0;
        transform: translateY(10px);
    }

    @keyframes slideIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .group-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .rank-1 { border-color: #FFD700; }
    .rank-2 { border-color: #C0C0C0; }
    .rank-3 { border-color: #CD7F32; }

    .card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }

    .card-rank-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        flex-shrink: 0;
    }

    .rank-1 .card-rank-badge {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: white;
    }

    .rank-2 .card-rank-badge {
        background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
        color: white;
    }

    .rank-3 .card-rank-badge {
        background: linear-gradient(135deg, #CD7F32, #A0522D);
        color: white;
    }

    .rank-4-plus .card-rank-badge {
        background: #f1f5f9;
        color: var(--muted);
    }

    .card-group-info {
        flex: 1;
        min-width: 0;
    }

    .card-group-name {
        font-weight: 600;
        color: var(--text-color);
        font-size: 14px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .card-group-number {
        font-size: 11px;
        color: var(--muted);
        margin-top: 2px;
    }

    .card-success-rate {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 11px;
        flex-shrink: 0;
    }

    .card-body {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 12px;
    }

    .card-stat {
        display: flex;
        flex-direction: column;
        padding: 8px;
        background: #f8fafc;
        border-radius: var(--radius-sm);
    }

    .card-stat-label {
        font-size: 10px;
        color: var(--muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .card-stat-label i {
        font-size: 10px;
    }

    .card-stat-value {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-color);
        line-height: 1;
    }

    .card-stat:nth-child(1) .card-stat-value { color: var(--blue); }
    .card-stat:nth-child(2) .card-stat-value { color: var(--primary-green); }
    .card-stat:nth-child(3) .card-stat-value { color: var(--purple); }
    .card-stat:nth-child(4) .card-stat-value { color: var(--yellow); }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 8px;
        border-top: 1px solid #f1f5f9;
    }

    .card-performance {
        font-size: 11px;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .card-performance i {
        font-size: 12px;
    }

    .card-actions {
        display: flex;
        gap: 8px;
    }

    .card-action-btn {
        padding: 4px 8px;
        border-radius: var(--radius-sm);
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 4px;
        border: 1px solid #cbd5e1;
        background: white;
        color: var(--text-color);
    }

    .card-action-btn:hover {
        background: #f8fafc;
        border-color: var(--primary-green);
        color: var(--primary-green);
    }

    .card-action-btn.view-details {
        background: var(--primary-green);
        border-color: var(--primary-green);
        color: white;
    }

    .card-action-btn.view-details:hover {
        background: var(--dark-green);
        border-color: var(--dark-green);
    }

    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: var(--card-bg);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .pagination-info {
        font-size: 12px;
        color: var(--muted);
    }

    .pagination-info b {
        color: var(--text-color);
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .pagination button {
        min-width: 28px;
        height: 28px;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-sm);
        background: white;
        color: var(--text-color);
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
    }

    .pagination button:hover:not(:disabled) {
        background: var(--primary-green);
        border-color: var(--primary-green);
        color: white;
    }

    .pagination button.active {
        background: var(--primary-green);
        border-color: var(--primary-green);
        color: white;
        font-weight: 600;
    }

    .pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .legend {
        display: flex;
        justify-content: center;
        gap: 16px;
        padding: 12px;
        background: #f8fafc;
        border-radius: var(--radius-md);
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: var(--muted);
    }

    .legend-color {
        width: 10px;
        height: 10px;
        border-radius: 2px;
    }

    .legend-high { background: var(--primary-green); }
    .legend-medium { background: var(--yellow); }
    .legend-low { background: #f97316; }
    .legend-poor { background: #ef4444; }

    .print-btn-container {
        display: flex;
        justify-content: center;
        padding: 12px;
        border-top: 1px solid #e2e8f0;
    }

    .print-btn {
        background: var(--muted);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .print-btn:hover {
        background: #4b5563;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    /* Staggered animations */
    .group-card:nth-child(1) { animation-delay: 0.05s; }
    .group-card:nth-child(2) { animation-delay: 0.1s; }
    .group-card:nth-child(3) { animation-delay: 0.15s; }
    .group-card:nth-child(4) { animation-delay: 0.2s; }
    .group-card:nth-child(5) { animation-delay: 0.25s; }
    .group-card:nth-child(6) { animation-delay: 0.3s; }
    .group-card:nth-child(7) { animation-delay: 0.35s; }
    .group-card:nth-child(8) { animation-delay: 0.4s; }
    .group-card:nth-child(9) { animation-delay: 0.45s; }
    .group-card:nth-child(10) { animation-delay: 0.5s; }

    /* Responsive Design */
    @media (min-width: 1200px) {
        .groups-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (min-width: 992px) and (max-width: 1199px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .cards-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 768px) and (max-width: 991px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .refresh-btn {
            width: 100%;
            justify-content: center;
        }

        .table-view {
            display: none !important;
        }

        .card-view {
            display: block !important;
        }

        .cards-container {
            grid-template-columns: 1fr;
        }

        .pagination-container {
            flex-direction: column;
            align-items: stretch;
        }

        .pagination {
            justify-content: center;
        }
    }

    @media (max-width: 767px) {
        .groups-container {
            padding: 8px;
        }

        .page-header {
            padding: 10px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-view {
            display: none !important;
        }

        .card-view {
            display: block !important;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .card-success-rate {
            align-self: flex-start;
        }

        .card-footer {
            flex-direction: column;
            gap: 8px;
        }

        .card-actions {
            width: 100%;
            justify-content: center;
        }

        .legend {
            justify-content: flex-start;
            gap: 12px;
        }

        .pagination-container {
            flex-direction: column;
            align-items: stretch;
        }

        .pagination {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .page-header h1 {
            font-size: 14px;
        }

        .refresh-btn {
            padding: 5px 10px;
            font-size: 11px;
        }

        .stat-card {
            padding: 10px;
            min-height: 60px;
        }

        .stat-value {
            font-size: 18px;
        }

        .card-body {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .card-stat {
            padding: 6px;
        }

        .pagination button {
            min-width: 26px;
            height: 26px;
            font-size: 11px;
        }

        .print-btn {
            padding: 5px 10px;
            font-size: 11px;
        }
    }
</style>
@endsection

@section('content')
<div class="groups-container">
    <div class="page-header">
        <h1>
            <i class="fa-solid fa-users-between-lines"></i>
            Lead Farmer Groups Activity
        </h1>
        <button class="refresh-btn" id="refreshData">
            <i class="fas fa-sync-alt"></i>
            Refresh
        </button>
    </div>

    @php
        $totalGroups = $paginatedGroups->total();
        $totalSales = $paginatedGroups->sum('total_sales');
        $totalActiveFarmers = $paginatedGroups->sum('active_farmers');
        $totalProducts = $paginatedGroups->sum('total_products');
        $avgSuccessRate = $paginatedGroups->avg('success_rate');
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fa-solid fa-users-between-lines"></i>
                </div>
                <div class="stat-value">{{ $totalGroups }}</div>
            </div>
            <div class="stat-label">Total Groups</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">{{ number_format($avgSuccessRate, 1) }}%</div>
            </div>
            <div class="stat-label">Avg Success</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">LKR {{ number_format($totalSales, 0) }}</div>
            </div>
            <div class="stat-label">Total Revenue</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-value">{{ $totalActiveFarmers }}</div>
            </div>
            <div class="stat-label">Active Farmers</div>
        </div>
    </div>

    <!-- Table View -->
    <div class="table-view">
        <div class="table-container">
            <div class="table-controls">
                <div class="title">
                    <i class="fas fa-medal"></i>
                    Performance Ranking
                </div>
                <div class="rows-per-page">
                    <span>Show:</span>
                    <select id="tableRowsPerPage">
                        <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="groups-table">
                    <thead>
                        <tr>
                            <th class="rank-cell">
                                <i class="fas fa-medal"></i>
                                Rank
                            </th>
                            <th class="group-name-cell">
                                <i class="fa-solid fa-users-between-lines"></i>
                                Group
                            </th>
                            <th class="success-rate-cell">
                                <i class="fas fa-percentage"></i>
                                Success
                            </th>
                            <th class="sales-count">
                                <i class="fas fa-shopping-cart"></i>
                                Orders
                            </th>
                            <th class="total-sales">
                                <i class="fas fa-money-bill"></i>
                                Sales
                            </th>
                            <th class="farmers-count">
                                <i class="fas fa-user-check"></i>
                                Farmers
                            </th>
                            <th class="products-count">
                                <i class="fas fa-boxes"></i>
                                Products
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedGroups as $group)
                            @php
                                $rankClass = 'rank-4-plus';
                                if ($group->rank == 1) $rankClass = 'rank-1';
                                elseif ($group->rank == 2) $rankClass = 'rank-2';
                                elseif ($group->rank == 3) $rankClass = 'rank-3';
                            @endphp

                            <tr class="{{ $rankClass }}" data-group-id="{{ $group->id }}">
                                <td class="rank-cell">
                                    <div class="rank-badge">{{ $group->rank }}</div>
                                </td>
                                <td class="group-name-cell">
                                    <div class="group-name">{{ $group->group_name }}</div>
                                    <div class="group-number">{{ $group->group_number }}</div>
                                </td>
                                <td class="success-rate-cell">
                                    <div class="success-badge {{ $group->color_class }}">
                                        <i class="fas fa-{{ $group->success_rate >= 50 ? 'arrow-up' : 'arrow-down' }}"></i>
                                        {{ $group->success_rate_formatted }}
                                    </div>
                                </td>
                                <td class="sales-count">
                                    {{ $group->sales_count }}
                                </td>
                                <td class="total-sales">
                                    {{ $group->total_sales_formatted }}
                                </td>
                                <td class="farmers-count">
                                    {{ $group->active_farmers }}
                                </td>
                                <td class="products-count">
                                    {{ $group->total_products }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="no-data">
                                    <i class="fas fa-inbox"></i>
                                    <p>No groups data available</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                <div class="pagination-info">
                    Showing <b>{{ $paginatedGroups->firstItem() ?? 0 }}-{{ $paginatedGroups->lastItem() ?? 0 }}</b> of <b>{{ $paginatedGroups->total() }}</b>
                </div>
                <div class="pagination">
                    @if ($paginatedGroups->onFirstPage())
                        <button disabled><i class="fas fa-chevron-left"></i></button>
                    @else
                        <button onclick="window.location.href='{{ $paginatedGroups->previousPageUrl() }}'">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    @endif

                    @foreach ($paginatedGroups->getUrlRange(1, $paginatedGroups->lastPage()) as $page => $url)
                        @if ($page == $paginatedGroups->currentPage())
                            <button class="active1">{{ $page }}</button>
                        @else
                            <button onclick="window.location.href='{{ $url }}'">{{ $page }}</button>
                        @endif
                    @endforeach

                    @if ($paginatedGroups->hasMorePages())
                        <button onclick="window.location.href='{{ $paginatedGroups->nextPageUrl() }}'">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    @else
                        <button disabled><i class="fas fa-chevron-right"></i></button>
                    @endif
                </div>
                <div class="rows-per-page">
                    <span>Per page:</span>
                    <select id="tableRowsPerPage2" onchange="changeRowsPerPage(this.value)">
                        <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </div>

            <div class="print-btn-container">
                <button class="print-btn" id="printTable">
                    <i class="fas fa-print"></i>
                    Print Report
                </button>
            </div>

            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color legend-high"></div>
                    <span>High (80%+)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-medium"></div>
                    <span>Medium (60-79%)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-low"></div>
                    <span>Low (40-59%)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-poor"></div>
                    <span>Poor (<40%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card View -->
    <div class="card-view">
        <div class="table-container">
            <div class="table-controls">
                <div class="title">
                    <i class="fas fa-medal"></i>
                    Groups Performance
                </div>
                <div class="rows-per-page">
                    <span>Show:</span>
                    <select id="cardRowsPerPage">
                        <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20</option>
                    </select>
                </div>
            </div>

            <div class="cards-container">
                @forelse($paginatedGroups as $group)
                    @php
                        $rankClass = 'rank-4-plus';
                        if ($group->rank == 1) $rankClass = 'rank-1';
                        elseif ($group->rank == 2) $rankClass = 'rank-2';
                        elseif ($group->rank == 3) $rankClass = 'rank-3';
                    @endphp

                    <div class="group-card {{ $rankClass }}" data-group-id="{{ $group->id }}">
                        <div class="card-header">
                            <div class="card-rank-badge">{{ $group->rank }}</div>
                            <div class="card-group-info">
                                <div class="card-group-name">{{ $group->group_name }}</div>
                                <div class="card-group-number">{{ $group->group_number }}</div>
                            </div>
                            <div class="card-success-rate {{ $group->color_class }}">
                                <i class="fas fa-{{ $group->success_rate >= 50 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ $group->success_rate_formatted }}
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="card-stat">
                                <div class="card-stat-label">
                                    <i class="fas fa-shopping-cart"></i>
                                    Orders
                                </div>
                                <div class="card-stat-value">{{ $group->sales_count }}</div>
                            </div>
                            <div class="card-stat">
                                <div class="card-stat-label">
                                    <i class="fas fa-money-bill"></i>
                                    Sales
                                </div>
                                <div class="card-stat-value">{{ $group->total_sales_formatted }}</div>
                            </div>
                            <div class="card-stat">
                                <div class="card-stat-label">
                                    <i class="fas fa-user-check"></i>
                                    Farmers
                                </div>
                                <div class="card-stat-value">{{ $group->active_farmers }}</div>
                            </div>
                            <div class="card-stat">
                                <div class="card-stat-label">
                                    <i class="fas fa-boxes"></i>
                                    Products
                                </div>
                                <div class="card-stat-value">{{ $group->total_products }}</div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="card-performance">
                                <i class="fas fa-{{ $group->success_rate >= 50 ? 'chart-line' : 'exclamation-triangle' }}"></i>
                                <span>{{ $group->success_rate >= 50 ? 'Good' : 'Needs Improvement' }}</span>
                            </div>
                            <div class="card-actions">
                                <button class="card-action-btn view-details" data-group-id="{{ $group->id }}">
                                    <i class="fas fa-eye"></i>
                                    View
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="no-data">
                        <i class="fas fa-inbox"></i>
                        <p>No groups data available</p>
                    </div>
                @endforelse
            </div>

            <div class="pagination-container">
                <div class="pagination-info">
                    Showing <b>{{ $paginatedGroups->firstItem() ?? 0 }}-{{ $paginatedGroups->lastItem() ?? 0 }}</b> of <b>{{ $paginatedGroups->total() }}</b>
                </div>
                <div class="pagination">
                    @if ($paginatedGroups->onFirstPage())
                        <button disabled><i class="fas fa-chevron-left"></i></button>
                    @else
                        <button onclick="window.location.href='{{ $paginatedGroups->previousPageUrl() }}'">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    @endif

                    @foreach ($paginatedGroups->getUrlRange(1, $paginatedGroups->lastPage()) as $page => $url)
                        @if ($page == $paginatedGroups->currentPage())
                            <button class="active1">{{ $page }}</button>
                        @else
                            <button onclick="window.location.href='{{ $url }}'">{{ $page }}</button>
                        @endif
                    @endforeach

                    @if ($paginatedGroups->hasMorePages())
                        <button onclick="window.location.href='{{ $paginatedGroups->nextPageUrl() }}'">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    @else
                        <button disabled><i class="fas fa-chevron-right"></i></button>
                    @endif
                </div>
                <div class="rows-per-page">
                    <span>Per page:</span>
                    <select id="cardRowsPerPage2" onchange="changeRowsPerPage(this.value)">
                        <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20</option>
                    </select>
                </div>
            </div>

            <div class="print-btn-container">
                <button class="print-btn" id="printTableMobile">
                    <i class="fas fa-print"></i>
                    Print Report
                </button>
            </div>

            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color legend-high"></div>
                    <span>High (80%+)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-medium"></div>
                    <span>Medium (60-79%)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-low"></div>
                    <span>Low (40-59%)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-poor"></div>
                    <span>Poor (<40%)</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const Swal = window.Swal;

        function showAlert(title, html, icon = 'info', confirmText = 'Close') {
            if (!Swal) {
                alert(title);
                return Promise.resolve({ isConfirmed: true });
            }

            return Swal.fire({
                title: title,
                html: html,
                icon: icon,
                confirmButtonText: confirmText,
                showCancelButton: false,
                allowOutsideClick: true,
                showCloseButton: true,
                width: window.innerWidth < 768 ? '90%' : '400px',
                customClass: {
                    popup: 'swal-popup',
                    title: 'swal-title',
                    htmlContainer: 'swal-html',
                    confirmButton: 'swal-confirm'
                },
                buttonsStyling: false
            });
        }

        function showLoading(title = 'Loading...') {
            if (!Swal) {
                console.log(title);
                return {
                    close: function() {
                        console.log('Loading closed');
                    }
                };
            }

            return Swal.fire({
                title: title,
                allowOutsideClick: false,
                showConfirmButton: false,
                showCloseButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        const refreshBtn = document.getElementById('refreshData');
        const printBtn = document.getElementById('printTable');
        const printBtnMobile = document.getElementById('printTableMobile');

        async function handleRefresh() {
            const originalContent = refreshBtn.innerHTML;

            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            refreshBtn.disabled = true;

            const loadingAlert = showLoading('Refreshing...');

            try {
                await new Promise(resolve => setTimeout(resolve, 1000));

                if (loadingAlert && typeof loadingAlert.close === 'function') {
                    loadingAlert.close();
                }

                await showAlert('Success', 'Data refreshed successfully', 'success', 'OK');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);

                if (loadingAlert && typeof loadingAlert.close === 'function') {
                    loadingAlert.close();
                }

                await showAlert('Error', 'Failed to refresh data', 'error', 'OK');
            } finally {
                refreshBtn.innerHTML = originalContent;
                refreshBtn.disabled = false;
            }
        }

        function printReport() {
            const isMobileView = window.innerWidth < 768;
            const printContent = isMobileView
                ? document.querySelector('.cards-container').cloneNode(true)
                : document.querySelector('.table-responsive').cloneNode(true);

            const printWindow = window.open('', '_blank', 'width=900,height=600');

            let printHTML = '';

            if (isMobileView) {
                printHTML = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Lead Farmer Groups Report</title>
                        <style>
                            body { font-family: Arial; margin: 15px; font-size: 12px; }
                            h1 { color: #10B981; font-size: 16px; border-bottom: 2px solid #10B981; padding-bottom: 5px; }
                            .print-date { color: #666; font-size: 11px; margin-bottom: 15px; }
                            .print-card { border: 1px solid #ddd; border-radius: 6px; padding: 10px; margin-bottom: 10px; }
                            .card-header { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
                            .card-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
                            .stat-item { font-size: 11px; }
                            .stat-label { font-weight: 600; color: #666; font-size: 10px; }
                        </style>
                    </head>
                    <body>
                        <h1>Lead Farmer Groups Report</h1>
                        <div class="print-date">Generated: ${new Date().toLocaleString()}</div>
                        <div class="card-grid">
                `;

                printContent.querySelectorAll('.group-card').forEach(card => {
                    const groupName = card.querySelector('.card-group-name')?.textContent || 'N/A';
                    const groupNumber = card.querySelector('.card-group-number')?.textContent || 'N/A';
                    const rank = card.querySelector('.card-rank-badge')?.textContent || '0';
                    const successRate = card.querySelector('.card-success-rate')?.textContent?.trim() || '0%';

                    const stats = card.querySelectorAll('.card-stat-value');
                    const salesCount = stats[0]?.textContent || '0';
                    const totalSales = stats[1]?.textContent || 'LKR 0.00';
                    const farmersCount = stats[2]?.textContent || '0';
                    const productsCount = stats[3]?.textContent || '0';

                    printHTML += `
                        <div class="print-card">
                            <div class="card-header">
                                <strong style="font-size: 13px;">${groupName}</strong>
                                <span style="font-size: 10px; color: #666;">(${groupNumber})</span>
                                <span style="margin-left: auto; font-weight: 600;">${successRate}</span>
                            </div>
                            <div class="card-stats">
                                <div class="stat-item">
                                    <div class="stat-label">Orders</div>
                                    <div>${salesCount}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Sales</div>
                                    <div>${totalSales}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Farmers</div>
                                    <div>${farmersCount}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Products</div>
                                    <div>${productsCount}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                printHTML += `
                        </div>
                        <script>
                            window.onload = function() {
                                window.print();
                                setTimeout(() => window.close(), 100);
                            }
                        <\/script>
                    </body>
                    </html>
                `;
            } else {
                printHTML = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Lead Farmer Groups Report</title>
                        <style>
                            body { font-family: Arial; margin: 15px; font-size: 12px; }
                            h1 { color: #10B981; font-size: 16px; border-bottom: 2px solid #10B981; padding-bottom: 5px; }
                            .print-date { color: #666; font-size: 11px; margin-bottom: 15px; }
                            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                            th { background-color: #f8fafc; text-align: left; padding: 8px; border-bottom: 2px solid #ddd; font-weight: 600; }
                            td { padding: 6px 8px; border-bottom: 1px solid #eee; }
                        </style>
                    </head>
                    <body>
                        <h1>Lead Farmer Groups Report</h1>
                        <div class="print-date">Generated: ${new Date().toLocaleString()}</div>
                        ${printContent.innerHTML}
                        <script>
                            window.onload = function() {
                                window.print();
                                setTimeout(() => window.close(), 100);
                            }
                        <\/script>
                    </body>
                    </html>
                `;
            }

            printWindow.document.write(printHTML);
            printWindow.document.close();
        }

        async function showGroupDetails(groupId) {
            const groupElement = document.querySelector(`[data-group-id="${groupId}"]`);
            if (!groupElement) return;

            let groupName, groupNumber, successRate, totalSales, salesCount, farmersCount, productsCount, rank;

            if (groupElement.classList.contains('group-card')) {
                groupName = groupElement.querySelector('.card-group-name')?.textContent || 'N/A';
                groupNumber = groupElement.querySelector('.card-group-number')?.textContent || 'N/A';
                successRate = groupElement.querySelector('.card-success-rate')?.textContent?.trim() || '0%';
                rank = groupElement.querySelector('.card-rank-badge')?.textContent || '0';

                const stats = groupElement.querySelectorAll('.card-stat-value');
                salesCount = stats[0]?.textContent || '0';
                totalSales = stats[1]?.textContent || 'LKR 0.00';
                farmersCount = stats[2]?.textContent || '0';
                productsCount = stats[3]?.textContent || '0';
            } else {
                groupName = groupElement.querySelector('.group-name')?.textContent || 'N/A';
                groupNumber = groupElement.querySelector('.group-number')?.textContent || 'N/A';
                successRate = groupElement.querySelector('.success-badge')?.textContent?.trim() || '0%';
                rank = groupElement.querySelector('.rank-badge')?.textContent || '0';
                totalSales = groupElement.querySelector('.total-sales')?.textContent?.trim() || 'LKR 0.00';
                salesCount = groupElement.querySelector('.sales-count')?.textContent?.trim() || '0';
                farmersCount = groupElement.querySelector('.farmers-count')?.textContent?.trim() || '0';
                productsCount = groupElement.querySelector('.products-count')?.textContent?.trim() || '0';
            }

            const html = `
                <div style="font-size: 13px;">
                    <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                        <strong>Group:</strong> ${groupName}<br>
                        <strong>Number:</strong> ${groupNumber}<br>
                        <strong>Rank:</strong> #${rank}
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                        <div>
                            <strong>Success Rate:</strong><br>
                            <span style="font-size: 14px; font-weight: 600;">${successRate}</span>
                        </div>
                        <div>
                            <strong>Total Orders:</strong><br>
                            <span style="font-size: 14px; font-weight: 600;">${salesCount}</span>
                        </div>
                        <div>
                            <strong>Total Sales:</strong><br>
                            <span style="font-size: 14px; font-weight: 600;">${totalSales}</span>
                        </div>
                        <div>
                            <strong>Active Farmers:</strong><br>
                            <span style="font-size: 14px; font-weight: 600;">${farmersCount}</span>
                        </div>
                        <div>
                            <strong>Products:</strong><br>
                            <span style="font-size: 14px; font-weight: 600;">${productsCount}</span>
                        </div>
                    </div>
                </div>
            `;

            await showAlert('Group Details', html, 'info');
        }

        function changeRowsPerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }

        // Event Listeners
        if (refreshBtn) {
            refreshBtn.addEventListener('click', handleRefresh);
        }

        if (printBtn) {
            printBtn.addEventListener('click', printReport);
        }
        if (printBtnMobile) {
            printBtnMobile.addEventListener('click', printReport);
        }

        // Table row click
        const tableRows = document.querySelectorAll('.groups-table tbody tr:not(.no-data)');
        tableRows.forEach(row => {
            row.style.cursor = 'pointer';
            row.addEventListener('click', function(e) {
                const groupId = this.getAttribute('data-group-id');
                if (groupId) showGroupDetails(groupId);
            });
        });

        // Card view details button
        const viewDetailButtons = document.querySelectorAll('.card-action-btn.view-details');
        viewDetailButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const groupId = this.getAttribute('data-group-id');
                if (groupId) showGroupDetails(groupId);
            });
        });

        // Card click (except buttons)
        const groupCards = document.querySelectorAll('.group-card');
        groupCards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('.card-action-btn')) {
                    const groupId = this.getAttribute('data-group-id');
                    if (groupId) showGroupDetails(groupId);
                }
            });
        });

        // Rows per page
        const tableRowsPerPage = document.getElementById('tableRowsPerPage');
        const tableRowsPerPage2 = document.getElementById('tableRowsPerPage2');
        const cardRowsPerPage = document.getElementById('cardRowsPerPage');
        const cardRowsPerPage2 = document.getElementById('cardRowsPerPage2');

        if (tableRowsPerPage) {
            tableRowsPerPage.value = "{{ request('per_page', 20) }}";
            tableRowsPerPage.addEventListener('change', function() {
                changeRowsPerPage(this.value);
            });
        }

        if (tableRowsPerPage2) {
            tableRowsPerPage2.value = "{{ request('per_page', 20) }}";
            tableRowsPerPage2.addEventListener('change', function() {
                changeRowsPerPage(this.value);
            });
        }

        if (cardRowsPerPage) {
            cardRowsPerPage.value = "{{ request('per_page', 10) }}";
            cardRowsPerPage.addEventListener('change', function() {
                changeRowsPerPage(this.value);
            });
        }

        if (cardRowsPerPage2) {
            cardRowsPerPage2.value = "{{ request('per_page', 10) }}";
            cardRowsPerPage2.addEventListener('change', function() {
                changeRowsPerPage(this.value);
            });
        }

        // Responsive layout
        function adjustLayout() {
            const isMobileView = window.innerWidth < 768;
            const tableView = document.querySelector('.table-view');
            const cardView = document.querySelector('.card-view');

            if (tableView && cardView) {
                if (isMobileView) {
                    tableView.style.display = 'none';
                    cardView.style.display = 'block';
                } else {
                    tableView.style.display = 'block';
                    cardView.style.display = 'none';
                }
            }
        }

        window.addEventListener('resize', adjustLayout);
        adjustLayout();

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                if (refreshBtn) refreshBtn.click();
            }
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                if (printBtn) printBtn.click();
                else if (printBtnMobile) printBtnMobile.click();
            }
        });
    });
</script>
@endsection

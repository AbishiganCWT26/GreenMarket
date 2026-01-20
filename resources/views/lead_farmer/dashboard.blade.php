@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Dashboard Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Farmers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalFarmers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeProducts }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOrders }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingOrders }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('lf.registerFarmer') }}" class="btn btn-success btn-block">
                                <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                Register Farmer
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('lf.addProduct') }}" class="btn btn-info btn-block">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                Add Product
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('lf.orders') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i><br>
                                View Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('lf.manageProducts') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                Manage Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Notifications -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Buyer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('lf.order.details', $order->id) }}">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->buyer->name }}</td>
                                    <td>LKR {{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $order->order_status == 'pending' ? 'warning' : ($order->order_status == 'paid' ? 'success' : 'info') }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('lf.orders') }}" class="btn btn-sm btn-primary mt-2">View All Orders</a>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No orders yet</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Notifications</h6>
                </div>
                <div class="card-body">
                    @if($recentNotifications->count() > 0)
                    <div class="list-group">
                        @foreach($recentNotifications as $notification)
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ Str::limit($notification->title, 40) }}</h6>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($notification->message, 60) }}</p>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-bell fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No notifications</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/dashboard.css') }}">
@endsection

@section('content')
<div class="container-fluid">
<div class="dashboard-stats">
<div class="stat-card">
<div class="stat-icon farmers">
<i class="fa-solid fa-users"></i>
</div>
<div class="stat-content">
<div class="stat-label">Total Farmers</div>
<div class="stat-value">{{ $totalFarmers }}</div>
<div class="stat-trend">
<i class="fa-solid fa-arrow-up"></i>
<span>Active farmers</span>
</div>
</div>
</div>

<div class="stat-card">
<div class="stat-icon products">
<i class="fa-solid fa-boxes"></i>
</div>
<div class="stat-content">
<div class="stat-label">Active Products</div>
<div class="stat-value">{{ $activeProducts }}</div>
<div class="stat-trend">
<i class="fa-solid fa-box"></i>
<span>In inventory</span>
</div>
</div>
</div>

<div class="stat-card">
<div class="stat-icon orders">
<i class="fa-solid fa-shopping-cart"></i>
</div>
<div class="stat-content">
<div class="stat-label">Total Orders</div>
<div class="stat-value">{{ $totalOrders }}</div>
<div class="stat-trend">
<i class="fa-solid fa-clock"></i>
<span>All time</span>
</div>
</div>
</div>

<div class="stat-card">
<div class="stat-icon pending">
<i class="fa-solid fa-clock"></i>
</div>
<div class="stat-content">
<div class="stat-label">Pending Orders</div>
<div class="stat-value">{{ $pendingOrders }}</div>
<div class="stat-trend">
<i class="fa-solid fa-hourglass-half"></i>
<span>Awaiting action</span>
</div>
</div>
</div>
</div>

<div class="quick-actions">
<div class="quick-actions-header">
<i class="fa-solid fa-bolt"></i>
<h3>Quick Actions</h3>
</div>
<div class="quick-actions-grid">
<a href="{{ route('lf.registerFarmer') }}" class="quick-action-item">
<i class="fa-solid fa-user-plus"></i>
<span>Register Farmer</span>
</a>
<a href="{{ route('lf.addProduct') }}" class="quick-action-item">
<i class="fa-solid fa-plus-circle"></i>
<span>Add Product</span>
</a>
<a href="{{ route('lf.orders') }}" class="quick-action-item">
<i class="fa-solid fa-shopping-cart"></i>
<span>View Orders</span>
</a>
<a href="{{ route('lf.manageProducts') }}" class="quick-action-item">
<i class="fa-solid fa-box-open"></i>
<span>Manage Products</span>
</a>
</div>
</div>

<div class="recent-orders">
<div class="recent-orders-header">
<div class="recent-orders-header-left">
<i class="fa-solid fa-clock-rotate-left"></i>
<h3>Recent Orders</h3>
</div>
<a href="{{ route('lf.orders') }}" class="view-all-link">
<span>View All</span>
<i class="fa-solid fa-arrow-right"></i>
</a>
</div>

@if($recentOrders->take(5)->count() > 0)
<div class="table-responsive">
<table class="orders-table">
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
@foreach($recentOrders->take(5) as $order)
<tr onclick="window.location='{{ route('lf.orders.view', $order->id) }}'">
<td>
<a href="{{ route('lf.orders.view', $order->id) }}" class="order-number">
{{ $order->order_number }}
</a>
</td>
<td>{{ $order->buyer->name ?? 'N/A' }}</td>
<td>LKR {{ number_format($order->total_amount, 2) }}</td>
<td>
<span class="badge-status badge-{{ $order->order_status == 'pending' ? 'pending' : ($order->order_status == 'paid' ? 'paid' : ($order->order_status == 'completed' ? 'completed' : 'info')) }}">
{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
</span>
</td>
<td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@else
<div class="empty-state">
<i class="fa-solid fa-shopping-cart"></i>
<p>No orders yet</p>
</div>
@endif
</div>
</div>

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
Swal.fire({
icon: 'success',
title: 'Success!',
text: '{{ session('success') }}',
confirmButtonColor: '#10B981',
timer: 3000,
showConfirmButton: true,
background: '#ffffff',
color: '#0f1724'
});
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
Swal.fire({
icon: 'error',
title: 'Error!',
text: '{{ session('error') }}',
confirmButtonColor: '#ef4444',
timer: 3000,
showConfirmButton: true,
background: '#ffffff',
color: '#0f1724'
});
});
</script>
@endif

@if($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function() {
Swal.fire({
icon: 'error',
title: 'Validation Error',
text: '{{ $errors->first() }}',
confirmButtonColor: '#ef4444',
timer: 3000,
showConfirmButton: true,
background: '#ffffff',
color: '#0f1724'
});
});
</script>
@endif
@endsection
@extends('delivery-rider.layouts.delivery_rider_master')

@section('title', '| Completed Delivery Details')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/completed-delivery-details.css') }}">
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/components.css') }}">
@endsection

@section('page-title')
    <i class="fa-solid fa-circle-check text-success me-2"></i> Completed Delivery Details
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('delivery-rider.completed-deliveries') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to History
        </a>
    </div>

    <div class="row g-4">
        <!-- Logistics/Delivery Confirmation Panel -->
        <div class="col-lg-5">
            <div class="card-premium p-4">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <h5 class="fw-bold mb-0 text-success"><i class="fa-solid fa-clipboard-check me-1"></i> Handover Record</h5>
                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Completed</span>
                </div>

                <div class="consignee-profile-header d-flex align-items-center mb-4">
                    <div class="avatar-placeholder rounded-circle bg-light d-flex align-items-center justify-content-center text-success fw-bold fs-4 me-3" style="width: 60px; height: 60px;">
                        {{ strtoupper(substr($delivery->order->buyer->name ?? 'B', 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 fs-5 text-dark">{{ $delivery->order->buyer->name ?? 'N/A' }}</h6>
                        <small class="text-secondary">Buyer / Customer</small>
                    </div>
                </div>

                <div class="details-list">
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary fw-medium">Order Number</span>
                        <span class="fw-bold text-dark">#{{ $delivery->order->order_number ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary fw-medium">Primary Mobile</span>
                        <span><a href="tel:{{ $delivery->order->buyer->primary_mobile ?? '' }}" class="fw-bold text-primary">{{ $delivery->order->buyer->primary_mobile ?? 'N/A' }}</a></span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary fw-medium">Completed Date & Time</span>
                        <span class="fw-bold text-dark">{{ $delivery->updated_at->format('Y-m-d h:i A') }}</span>
                    </div>
                    <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary fw-medium">Delivery Rider Earnings</span>
                        <span class="fw-bold text-success">
                            @php
                                $itemsTotal = $delivery->order ? $delivery->order->orderItems->sum('item_total') : 0;
                                $deliveryFee = $delivery->order ? ($delivery->order->total_amount - $itemsTotal) : 0;
                            @endphp
                            Rs. {{ number_format(max(0, $deliveryFee), 2) }}
                        </span>
                    </div>
                    <div class="detail-item py-3 border-bottom">
                        <span class="text-secondary fw-medium d-block mb-1">Destination Address</span>
                        <span class="fw-semibold text-dark">{{ $delivery->order->buyer->residential_address ?? 'N/A' }}, {{ $delivery->order->buyer->district ?? '' }}</span>
                    </div>
                </div>
            </div>

            <!-- Timeline Component -->
            @include('delivery-rider.components.delivery_timeline', ['status' => $delivery->delivery_status])
        </div>

        <!-- Package Items List Panel -->
        <div class="col-lg-7">
            <div class="card-premium p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-3">Associated Logistics Dispatch</h5>
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <small class="text-secondary d-block">Bus Number</small>
                        <strong class="text-dark"><i class="fa-solid fa-bus text-muted me-1"></i> {{ $delivery->busDispatch->bus_number ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <small class="text-secondary d-block">Conductor Name</small>
                        <strong class="text-dark"><i class="fa-solid fa-user-tie text-muted me-1"></i> {{ $delivery->busDispatch->conductor_name ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-secondary d-block">Conductor Contact</small>
                        <strong><a href="tel:{{ $delivery->busDispatch->conductor_mobile ?? '' }}" class="text-primary"><i class="fa-solid fa-phone text-muted me-1"></i> {{ $delivery->busDispatch->conductor_mobile ?? 'N/A' }}</a></strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-secondary d-block">Arrived At Hub</small>
                        <strong class="text-secondary">{{ $delivery->busDispatch->updated_at ? $delivery->busDispatch->updated_at->format('Y-m-d h:i A') : 'N/A' }}</strong>
                    </div>
                </div>
            </div>

            @if($delivery->order && $delivery->order->orderItems)
                @include('delivery-rider.components.product_list', ['items' => $delivery->order->orderItems])
            @endif
        </div>
    </div>
</div>
@endsection

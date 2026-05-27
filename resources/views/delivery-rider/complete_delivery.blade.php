@extends('delivery-rider.layouts.delivery_rider_master')

@section('title', '| Complete Delivery')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/complete-delivery.css') }}">
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/components.css') }}">
@endsection

@section('page-title')
    <i class="fa-solid fa-check-double text-success me-2"></i> Complete Delivery Assignment
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('delivery-rider.delivery-details', $delivery->id) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Cancel & Back to Details
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card-premium p-4 animate__animated animate__fadeInUp">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <h5 class="fw-bold mb-0 text-success"><i class="fa-solid fa-clipboard-check me-2"></i> Handover Confirmation</h5>
                    <span class="badge bg-light text-secondary">#{{ $delivery->order->order_number ?? 'N/A' }}</span>
                </div>

                <div class="alert alert-info border-0 rounded-3 mb-4">
                    <div class="d-flex">
                        <i class="fa-solid fa-circle-info fa-lg me-2 mt-1"></i>
                        <div>
                            <strong>Delivery Handover:</strong> Please ensure that you have handed the package to the customer and received confirmation or payment (if applicable) before submitting this form.
                        </div>
                    </div>
                </div>

                <form action="{{ route('delivery-rider.delivery.complete', $delivery->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold text-secondary">Buyer Name</label>
                                <input type="text" class="form-control bg-light border-0 fw-bold" value="{{ $delivery->order->buyer->name ?? 'N/A' }}" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold text-secondary">Buyer Contact Number</label>
                                <input type="text" class="form-control bg-light border-0 fw-bold" value="{{ $delivery->order->buyer->primary_mobile ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="delivery_proof" class="form-label fw-bold text-dark">
                            <i class="fa-solid fa-camera me-1 text-primary"></i> Upload Proof of Delivery (Optional)
                        </label>
                        <input type="file" name="delivery_proof" id="delivery_proof" class="form-control" accept="image/*">
                        <div class="form-text text-secondary mt-1">Upload a photo of the signature sheet, delivered parcel at destination, or a receipt. Allowed formats: PNG, JPG, JPEG (Max 5MB).</div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold text-dark">
                            <i class="fa-solid fa-comment-dots me-1 text-primary"></i> Delivery Remarks / Notes (Optional)
                        </label>
                        <textarea class="form-control" name="notes" id="notes" rows="4" placeholder="Enter any handover observations, cash collection details, or remarks about customer receipt..."></textarea>
                    </div>

                    <div class="pt-3 border-top mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('delivery-rider.active-deliveries') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            <i class="fa-solid fa-circle-check me-1"></i> Submit Handover Confirmation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

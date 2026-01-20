@extends('buyer.layouts.buyer_master')

@section('title', 'File Complaint')
@section('page-title', 'File a Complaint')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/buyer/complaints.css') }}">
@endsection

@section('content')
<div class="complaint-container">
    <div class="complaint-header">
        <h1>
            <i class="fa-solid fa-headset"></i>
            File a Complaint
        </h1>
        <p>Report any issues you're facing with orders, products, or system functionality. Your complaint will be reviewed by our admin team.</p>
    </div>

    <div class="complaint-card">
        <form id="complaintForm" class="complaint-form" method="POST" action="{{ route('buyer.complaints.store') }}">
            @csrf

            <div class="form-section">
                <div class="form-header">
                    <i class="fa-solid fa-exclamation-circle"></i>
                    <h3>Complaint Details</h3>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-tag"></i>
                        Complaint Type <span class="required">*</span>
                    </label>
                    <div class="radio-grid">
                        <div class="radio-card">
                            <input type="radio" id="product_quality" name="complaint_type" value="product_quality" required>
                            <label for="product_quality">
                                <i class="fa-solid fa-star-half-stroke"></i>
                                <span class="radio-title">Product Quality</span>
                                <span class="radio-desc">Items don't match description or are damaged</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="wrong_location" name="complaint_type" value="wrong_location" required>
                            <label for="wrong_location">
                                <i class="fa-solid fa-map-location-dot"></i>
                                <span class="radio-title">Wrong Location</span>
                                <span class="radio-desc">Pickup location information is inaccurate</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="farmer_contact" name="complaint_type" value="farmer_contact" required>
                            <label for="farmer_contact">
                                <i class="fa-solid fa-phone-slash"></i>
                                <span class="radio-title">Farmer Contact</span>
                                <span class="radio-desc">Cannot reach farmer using provided number</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="availability_issue" name="complaint_type" value="availability_issue" required>
                            <label for="availability_issue">
                                <i class="fa-solid fa-box-open"></i>
                                <span class="radio-title">Product Availability</span>
                                <span class="radio-desc">Product not ready despite payment confirmation</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="payment_issue" name="complaint_type" value="payment_issue" required>
                            <label for="payment_issue">
                                <i class="fa-solid fa-credit-card"></i>
                                <span class="radio-title">Payment Issue</span>
                                <span class="radio-desc">Money deducted but order not confirmed</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="invoice_error" name="complaint_type" value="invoice_error" required>
                            <label for="invoice_error">
                                <i class="fa-solid fa-file-invoice"></i>
                                <span class="radio-title">Invoice Error</span>
                                <span class="radio-desc">Wrong prices or missing details in invoice</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="category_misclassification" name="complaint_type" value="category_misclassification" required>
                            <label for="category_misclassification">
                                <i class="fa-solid fa-tags"></i>
                                <span class="radio-title">Wrong Category</span>
                                <span class="radio-desc">Product doesn't match selected category</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="farmer_no_show" name="complaint_type" value="farmer_no_show" required>
                            <label for="farmer_no_show">
                                <i class="fa-solid fa-user-slash"></i>
                                <span class="radio-title">Farmer No Show</span>
                                <span class="radio-desc">Farmer not at pickup location on time</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="product_photo_mismatch" name="complaint_type" value="product_photo_mismatch" required>
                            <label for="product_photo_mismatch">
                                <i class="fa-solid fa-images"></i>
                                <span class="radio-title">Photo Mismatch</span>
                                <span class="radio-desc">Product looks different from uploaded image</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="request_ignored" name="complaint_type" value="request_ignored" required>
                            <label for="request_ignored">
                                <i class="fa-solid fa-bell-slash"></i>
                                <span class="radio-title">Request Ignored</span>
                                <span class="radio-desc">No response to product requests</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="filter_issue" name="complaint_type" value="filter_issue" required>
                            <label for="filter_issue">
                                <i class="fa-solid fa-filter"></i>
                                <span class="radio-title">Filter Issue</span>
                                <span class="radio-desc">Cannot filter/search for quality grades</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="vague_instructions" name="complaint_type" value="vague_instructions" required>
                            <label for="vague_instructions">
                                <i class="fa-solid fa-map"></i>
                                <span class="radio-title">Vague Instructions</span>
                                <span class="radio-desc">Pickup instructions too vague for logistics</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="payment_technical" name="complaint_type" value="payment_technical" required>
                            <label for="payment_technical">
                                <i class="fa-solid fa-bug"></i>
                                <span class="radio-title">Technical Glitch</span>
                                <span class="radio-desc">Payment gateway fails repeatedly</span>
                            </label>
                        </div>

                        <div class="radio-card">
                            <input type="radio" id="other_issue" name="complaint_type" value="other" required>
                            <label for="other_issue">
                                <i class="fa-solid fa-ellipsis-h"></i>
                                <span class="radio-title">Other Issue</span>
                                <span class="radio-desc">Any other problem not listed</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-shopping-cart"></i>
                        Related Order (Optional)
                    </label>
                    <div class="select-wrapper">
                        <select name="related_order_id" id="related_order_id" class="form-control">
                            <option value="">Select Order</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}">
                                    Order #{{ $order->order_number }} - {{ $order->farmer_name ?? 'Farmer' }} - {{ number_format($order->total_amount, 2) }} LKR
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <span class="help-text">
                        <i class="fa-solid fa-info-circle"></i>
                        Select the order related to this complaint (if applicable)
                    </span>
                </div>
            </div>

            <div class="form-section">
                <div class="form-header">
                    <i class="fa-solid fa-align-left"></i>
                    <h3>Complaint Description</h3>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-file-alt"></i>
                        Description <span class="required">*</span>
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        class="form-control"
                        placeholder="Please provide detailed information about your complaint. Include dates, order numbers, and any relevant details."
                        minlength="20"
                        maxlength="2000"
                        rows="5"
                        required
                    ></textarea>
                    <div class="char-counter">
                        <span id="charCount">0</span> / 2000 characters
                    </div>
                    <span class="help-text">
                        <i class="fa-solid fa-info-circle"></i>
                        Minimum 20 characters, maximum 2000 characters
                    </span>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('buyer.complaints.list') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-times"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span class="btn-text">Submit Complaint</span>
                    <span class="loading-spinner" style="display: none;">
                        <i class="fa-solid fa-spinner fa-spin"></i> Processing...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Character counter
    $('#description').on('input', function() {
        const charCount = $(this).val().length;
        $('#charCount').text(charCount);

        if (charCount < 20) {
            $(this).css('border-color', '#f59e0b');
        } else if (charCount > 2000) {
            $(this).css('border-color', '#ef4444');
        } else {
            $(this).css('border-color', '#10B981');
        }
    });

    // Radio card selection effect
    $('.radio-card input').on('change', function() {
        $('.radio-card').removeClass('selected');
        $(this).closest('.radio-card').addClass('selected');
    });

    // Form submission
    $('#complaintForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = $('#submitBtn');
        const btnText = submitBtn.find('.btn-text');
        const spinner = submitBtn.find('.loading-spinner');

        // Show loading state
        btnText.hide();
        spinner.show();
        submitBtn.prop('disabled', true);

        Swal.fire({
            title: 'Submitting Complaint',
            text: 'Please wait while we process your complaint...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.close();

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Complaint Submitted!',
                        html: `
                            <div class="success-message">
                                <i class="fa-solid fa-check-circle success-icon"></i>
                                <h3>Thank You for Reporting!</h3>
                                <p>Your complaint has been successfully submitted. Our team will review it shortly.</p>
                                <div class="success-details">
                                    <p><i class="fa-solid fa-bell"></i> Complaint ID: #${response.complaint_id}</p>
                                    <p><i class="fa-solid fa-clock"></i> Submitted: Just now</p>
                                </div>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'View Complaints',
                        confirmButtonColor: '#10B981',
                        showCancelButton: true,
                        cancelButtonText: 'File Another',
                        customClass: {
                            popup: 'animate__animated animate__fadeInUp'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("buyer.complaints.list") }}';
                        } else if (result.isDismissed) {
                            form[0].reset();
                            $('.radio-card').removeClass('selected');
                            $('#charCount').text('0');
                            resetForm();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: response.message || 'Failed to submit complaint. Please try again.',
                        confirmButtonColor: '#10B981'
                    });
                    resetForm();
                }
            },
            error: function(xhr) {
                Swal.close();

                let errorMessage = 'An error occurred while submitting your complaint.';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors)[0][0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Submission Error',
                    html: `
                        <div class="error-message">
                            <i class="fa-solid fa-exclamation-triangle error-icon"></i>
                            <h3>Error Occurred</h3>
                            <p>${errorMessage}</p>
                        </div>
                    `,
                    confirmButtonColor: '#10B981'
                });
                resetForm();
            }
        });

        function resetForm() {
            btnText.show();
            spinner.hide();
            submitBtn.prop('disabled', false);
        }
    });

    // Form control focus effects
    $('.form-control').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });

    // Select focus effect
    $('select').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });
});
</script>
@endsection

@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/edit_product.css') }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="edit-product-container">
    <div class="edit-header">
        <div class="header-left">
            <h2>
                <i class="fa-solid fa-edit"></i>
                Edit Product
                <span class="product-name">{{ $product->product_name }}</span>
            </h2>
            <p class="product-code">Product ID: #{{ $product->id }}</p>
        </div>
        <div class="header-right">
            <div class="lock-status unlocked">
                <i class="fa-solid fa-edit"></i>
                <span>Editing Product Details</span>
            </div>
        </div>
    </div>

    <div class="edit-form-wrapper">
        <form id="editProductForm" method="POST" enctype="multipart/form-data" action="{{ route('lf.updateProduct', $product->id) }}">
            @csrf
            
            <div class="form-grid">
                <div class="form-section card-animation">
                    <div class="section-header">
                        <i class="fa-solid fa-info-circle"></i>
                        <h3>Product Information</h3>
                    </div>
                    <div class="section-body">
                        <div class="form-group">
                            <label for="product_name" class="form-label">
                                <i class="fa-solid fa-tag"></i> Product Name *
                            </label>
                            <input type="text" name="product_name" id="product_name" 
                                   class="form-control free-edit @error('product_name') is-invalid @enderror"
                                   value="{{ old('product_name', $product->product_name) }}" 
                                  >
                            @error('product_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="type_variant" class="form-label">
                                <i class="fa-solid fa-list"></i> Type / Variant
                            </label>
                            <input type="text" name="type_variant" id="type_variant" 
                                   class="form-control free-edit @error('type_variant') is-invalid @enderror"
                                   value="{{ old('type_variant', $product->type_variant) }}"
                                  >
                            @error('type_variant')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="product_description" class="form-label">
                                <i class="fa-solid fa-align-left"></i> Product Description
                            </label>
                            <textarea name="product_description" id="product_description" 
                                      class="form-control free-edit @error('product_description') is-invalid @enderror"
                                      rows="3">{{ old('product_description', $product->product_description) }}</textarea>
                            @error('product_description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-camera"></i> Product Photo
                            </label>
                            <div class="photo-preview-container" id="photoPreviewContainer">
                                <img src="{{ $product->product_photo ? asset('uploads/product_images/' . $product->product_photo) : asset('assets/images/product-placeholder.png') }}" 
                                     alt="Product Photo" class="photo-preview" id="photoPreview" onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
                                <div class="change-photo-overlay">
                                    <i class="fa-solid fa-camera me-2"></i>Click to Change Photo
                                </div>
                                <input type="file" name="product_photo" id="product_photo" class="d-none" accept="image/jpeg,image/png">
                            </div>
                            <small class="form-text text-muted">Max file size: 5MB. Supported formats: JPG, PNG</small>
                        </div>
                    </div>
                </div>

                <div class="form-section card-animation">
                    <div class="section-header">
                        <i class="fa-solid fa-tags"></i>
                        <h3>Product Classification</h3>
                        <span class="otp-badge">OTP Required</span>
                    </div>
                    <div class="section-body">
                        <div class="form-group sensitive-field" data-field="farmer">
                            <label for="farmer_id" class="form-label">
                                <i class="fa-solid fa-user"></i> Farmer *
                            </label>
                            <select name="farmer_id" id="farmer_id" 
                                    class="form-control sensitive-select @error('farmer_id') is-invalid @enderror"
                                   >
                                <option value="">Select Farmer</option>
                                @foreach($farmers as $farmer)
                                    <option value="{{ $farmer->id }}" 
                                        {{ old('farmer_id', $product->farmer_id) == $farmer->id ? 'selected' : '' }}>
                                        {{ $farmer->name }} - {{ $farmer->primary_mobile }}
                                    </option>
                                @endforeach
                            </select>
                            @error('farmer_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group sensitive-field" data-field="category">
                            <label for="category_id" class="form-label">
                                <i class="fa-solid fa-layer-group"></i> Main Category *
                            </label>
                            <select name="category_id" id="category_id" 
                                    class="form-control sensitive-select @error('category_id') is-invalid @enderror"
                                   >
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group sensitive-field" data-field="subcategory">
                            <label for="subcategory_id" class="form-label">
                                <i class="fa-solid fa-sitemap"></i> Subcategory *
                            </label>
                            <select name="subcategory_id" id="subcategory_id" 
                                    class="form-control sensitive-select @error('subcategory_id') is-invalid @enderror"
                                   >
                                <option value="">Select Subcategory</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}" 
                                        {{ old('subcategory_id', $product->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                        {{ $subcategory->subcategory_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subcategory_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group sensitive-field" data-field="specific">
                            <label for="product_examples_id" class="form-label">
                                <i class="fa-solid fa-apple-alt"></i> Specific Product *
                            </label>
                            <select name="product_examples_id" id="product_examples_id" 
                                    class="form-control sensitive-select @error('product_examples_id') is-invalid @enderror"
                                   >
                                <option value="">Select Product</option>
                                @foreach($productExamples as $example)
                                    <option value="{{ $example->id }}" 
                                        {{ old('product_examples_id', $product->product_examples_id) == $example->id ? 'selected' : '' }}>
                                        {{ $example->product_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_examples_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-section card-animation">
                    <div class="section-header">
                        <i class="fa-solid fa-chart-line"></i>
                        <h3>Quantity & Pricing</h3>
                    </div>
                    <div class="section-body">
                        <div class="form-group">
                            <label for="quantity" class="form-label">
                                <i class="fa-solid fa-boxes"></i> Quantity *
                            </label>
                            <input type="number" name="quantity" id="quantity" 
                                   class="form-control free-edit @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', $product->quantity) }}" 
                                   step="0.01" min="0"
                                  >
                            @error('quantity')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="unit_of_measure" class="form-label">
                                <i class="fa-solid fa-weight"></i> Unit of Measure *
                            </label>
                            <select name="unit_of_measure" id="unit_of_measure" 
                                    class="form-control free-edit @error('unit_of_measure') is-invalid @enderror"
                                   >
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit }}" 
                                        {{ old('unit_of_measure', $product->unit_of_measure) == $unit ? 'selected' : '' }}>
                                        {{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_of_measure')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="quality_grade" class="form-label">
                                <i class="fa-solid fa-star"></i> Quality Grade
                            </label>
                            <select name="quality_grade" id="quality_grade" 
                                    class="form-control free-edit @error('quality_grade') is-invalid @enderror"
                                   >
                                <option value="">Select Grade</option>
                                @foreach($grades as $grade)
                                    <option value="{{ $grade }}" 
                                        {{ old('quality_grade', $product->quality_grade) == $grade ? 'selected' : '' }}>
                                        {{ $grade }}
                                    </option>
                                @endforeach
                            </select>
                            @error('quality_grade')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group sensitive-field" data-field="price">
                            <label for="selling_price" class="form-label">
                                <i class="fa-solid fa-money-bill"></i> Selling Price (Per Unit) *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" name="selling_price" id="selling_price" 
                                       class="form-control @error('selling_price') is-invalid @enderror"
                                       value="{{ old('selling_price', $product->selling_price) }}" 
                                       step="0.01" min="0"
                                      >
                            </div>
                            @error('selling_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group sensitive-field" data-field="availability">
                            <label for="expected_availability_date" class="form-label">
                                <i class="fa-solid fa-calendar-day"></i> Availability Date *
                            </label>
                            <input type="date" name="expected_availability_date" id="expected_availability_date"
                                   class="form-control @error('expected_availability_date') is-invalid @enderror"
                                   value="{{ old('expected_availability_date', $product->expected_availability_date ? $product->expected_availability_date->format('Y-m-d') : '') }}" 
                                  >
                            @error('expected_availability_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <div class="form-hint">When will this product be available for pickup?</div>
                        </div>
                    </div>
                </div>

                <div class="form-section card-animation">
                    <div class="section-header">
                        <i class="fa-solid fa-location-dot"></i>
                        <h3>Pickup Location</h3>
                    </div>
                    <div class="section-body">
                        <div class="form-group">
                            <label for="pickup_address" class="form-label">
                                <i class="fa-solid fa-map-marker-alt"></i> Pickup Address
                            </label>
                            <textarea name="pickup_address" id="pickup_address" 
                                      class="form-control free-edit @error('pickup_address') is-invalid @enderror"
                                      rows="3">{{ old('pickup_address', $product->pickup_address) }}</textarea>
                            @error('pickup_address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pickup_map_link" class="form-label">
                                <i class="fa-solid fa-map"></i> Google Map Link
                            </label>
                            <input type="url" name="pickup_map_link" id="pickup_map_link" 
                                   class="form-control free-edit @error('pickup_map_link') is-invalid @enderror"
                                   value="{{ old('pickup_map_link', $product->pickup_map_link) }}"
                                   placeholder="https://maps.google.com/?q=..."
                                  >
                            @error('pickup_map_link')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <div class="form-hint">Paste the Google Maps link for pickup location</div>
                        </div>
                    </div>
                </div>

                <div class="form-section card-animation status-section">
                    <div class="section-header">
                        <i class="fa-solid fa-toggle-on"></i>
                        <h3>Status</h3>
                    </div>
                    <div class="section-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input free-edit" type="checkbox" 
                                   name="is_available" id="is_available" value="1"
                                   {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                                  >
                            <label class="form-check-label" for="is_available">
                                <i class="fa-solid fa-check-circle"></i>
                                Make product available for purchase
                            </label>
                        </div>
                        <div class="form-hint">When checked, this product will be visible to buyers</div>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <input type="hidden" name="otp_verified" id="otpVerified" value="0">
                <input type="hidden" name="otp_code" id="otpCode" value="">
                
                <button type="button" class="btn-back" onclick="window.history.back()">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </button>
                
                <div class="action-buttons">
                    <button type="reset" class="btn-reset">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </button>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fa-solid fa-floppy-disk"></i> Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>

    <center>
    <div id="otpModal" style="display: none;">
        <div class="otp-modal-content">
            <div class="otp-header">
                <i class="fa-solid fa-shield-alt"></i>
                <h4>OTP Verification Required</h4>
            </div>
            <div class="otp-body">
                <p>An OTP has been sent to the farmer's mobile number:</p>
                <p class="farmer-mobile">{{ $currentFarmerMobile }}</p>
                <p>Please enter the 6-digit OTP to proceed with the changes.</p>
                
                <div class="otp-input-container">
                    <input type="text" id="otpDigit1" maxlength="1" class="otp-digit">
                    <input type="text" id="otpDigit2" maxlength="1" class="otp-digit">
                    <input type="text" id="otpDigit3" maxlength="1" class="otp-digit">
                    <input type="text" id="otpDigit4" maxlength="1" class="otp-digit">
                    <input type="text" id="otpDigit5" maxlength="1" class="otp-digit">
                    <input type="text" id="otpDigit6" maxlength="1" class="otp-digit">
                </div>
                
                <div class="otp-timer">
                    <i class="fa-solid fa-clock"></i>
                    <span id="otpTimer">10:00</span>
                </div>
                
                <div class="otp-actions">
                    <button type="button" id="resendOtp" class="btn-resend">
                        <i class="fa-solid fa-redo"></i> Resend OTP
                    </button>
                    <button type="button" id="verifyOtp" class="btn-verify">
                        <i class="fa-solid fa-check"></i> Verify OTP
                    </button>
                    <button type="button" id="cancelOtp" class="btn-cancel">
                        <i class="fa-solid fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    </center>
</div>
@endsection

@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editProductForm');
    const submitBtn = document.getElementById('submitBtn');
    const otpModal = document.getElementById('otpModal');
    const otpVerified = document.getElementById('otpVerified');
    const otpCode = document.getElementById('otpCode');
    const productId = {{ $product->id }};
    const isLocked = false;
    
    let originalValues = {};
    let otpTimer = null;
    let otpTimeLeft = 600;
    let otpSent = false;
    let currentOtp = '';
    let sensitiveFieldsChanged = false;
    
    const sensitiveFields = ['farmer_id', 'category_id', 'subcategory_id', 'product_examples_id', 'selling_price', 'expected_availability_date'];
    
    function initializeForm() {
        
        sensitiveFields.forEach(field => {
            const element = document.querySelector(`[name="${field}"]`);
            if (element) {
                originalValues[field] = element.value;
                
                element.addEventListener('change', function() {
                    checkSensitiveChanges();
                });
            }
        });
        
        document.getElementById('product_photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
        
        document.getElementById('photoPreviewContainer').addEventListener('click', function() {
            document.getElementById('product_photo').click();
        });
        
        document.getElementById('category_id').addEventListener('change', function() {
            const categoryId = this.value;
            if (categoryId) {
                fetch(`/lead-farmer/get-subcategories/${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        const subcategorySelect = document.getElementById('subcategory_id');
                        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                        data.forEach(subcategory => {
                            subcategorySelect.innerHTML += `<option value="${subcategory.id}">${subcategory.subcategory_name}</option>`;
                        });
                    });
            }
        });
        
        document.getElementById('subcategory_id').addEventListener('change', function() {
            const subcategoryId = this.value;
            if (subcategoryId) {
                fetch(`/lead-farmer/get-product-examples/${subcategoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        const productSelect = document.getElementById('product_examples_id');
                        productSelect.innerHTML = '<option value="">Select Product</option>';
                        data.forEach(product => {
                            productSelect.innerHTML += `<option value="${product.id}">${product.product_name}</option>`;
                        });
                    });
            }
        });
        
        const otpDigits = document.querySelectorAll('.otp-digit');
        otpDigits.forEach((digit, index) => {
            digit.addEventListener('input', function(e) {
                if (this.value.length === 1) {
                    if (index < otpDigits.length - 1) {
                        otpDigits[index + 1].focus();
                    }
                }
            });
            
            digit.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    otpDigits[index - 1].focus();
                }
            });
        });
        
        document.getElementById('resendOtp').addEventListener('click', function() {
            sendOtp();
        });
        
        document.getElementById('verifyOtp').addEventListener('click', function() {
            verifyOtp();
        });
        
        document.getElementById('cancelOtp').addEventListener('click', function() {
            hideOtpModal();
        });
    }
    
    function checkSensitiveChanges() {
        sensitiveFieldsChanged = false;
        
        sensitiveFields.forEach(field => {
            const element = document.querySelector(`[name="${field}"]`);
            if (element && element.value !== originalValues[field]) {
                sensitiveFieldsChanged = true;
            }
        });
        
        return sensitiveFieldsChanged;
    }
    
    function sendOtp() {
        fetch(`/lead-farmer/product/${productId}/send-otp`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                otpSent = true;
                startOtpTimer();
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/otp sent success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/otp sent success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                    title: 'OTP Sent!',
                    text: 'OTP has been sent to farmer\'s mobile number',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/error6.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error6.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                    title: 'Failed',
                    text: data.message || 'Failed to send OTP',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/error6.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error6.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                title: 'Error',
                text: 'Failed to send OTP. Please try again.',
            });
        });
    }
    
    function verifyOtp() {
        const otpDigits = document.querySelectorAll('.otp-digit');
        const enteredOtp = Array.from(otpDigits).map(digit => digit.value).join('');
        
        if (enteredOtp.length !== 6) {
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/alert4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
                title: 'Incomplete OTP',
                text: 'Please enter all 6 digits',
            });
            return;
        }
        
        fetch(`/lead-farmer/product/${productId}/verify-otp`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ otp: enteredOtp })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                otpVerified.value = '1';
                otpCode.value = enteredOtp;
                hideOtpModal();
                submitForm();
            } else {
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/Invalid otp1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Invalid otp1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                    title: 'Invalid OTP',
                    text: data.message || 'The OTP you entered is invalid or expired',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/Invalid otp1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Invalid otp1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                title: 'Error',
                text: 'Failed to verify OTP. Please try again.',
            });
        });
    }
    
    function showOtpModal() {
        otpModal.style.display = 'block';
        if (!otpSent) {
            sendOtp();
        }
    }
    
    function hideOtpModal() {
        otpModal.style.display = 'none';
        const otpDigits = document.querySelectorAll('.otp-digit');
        otpDigits.forEach(digit => digit.value = '');
    }
    
    function startOtpTimer() {
        otpTimeLeft = 600;
        updateOtpTimer();
        
        if (otpTimer) {
            clearInterval(otpTimer);
        }
        
        otpTimer = setInterval(() => {
            otpTimeLeft--;
            updateOtpTimer();
            
            if (otpTimeLeft <= 0) {
                clearInterval(otpTimer);
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/alert2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
                    title: 'OTP Expired',
                    text: 'The OTP has expired. Please request a new one.',
                });
            }
        }, 1000);
    }
    
    function updateOtpTimer() {
        const minutes = Math.floor(otpTimeLeft / 60);
        const seconds = otpTimeLeft % 60;
        document.getElementById('otpTimer').textContent = 
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    
    function submitForm() {
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/success5.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success5.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                    title: 'Success!',
                    text: data.message || 'Product updated successfully',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/lead-farmer/manage-products';
                    }
                });
            } else {
                if (data.requires_otp) {
                    showOtpModal();
                } else {
                    Swal.fire({
                        @if(file_exists(public_path('assets/icons/Gif/Failed1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Failed1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                        title: 'Error',
                        text: data.message || 'Failed to update product',
                        footer: data.errors ? Object.values(data.errors).join('<br>') : ''
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/error5.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error5.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                title: 'Error',
                text: 'An error occurred. Please try again.',
            });
        });
    }
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        checkSensitiveChanges();
        
        if (sensitiveFieldsChanged) {
            showOtpModal();
        } else {
            submitForm();
        }
    });
    
    initializeForm();
    
    const cards = document.querySelectorAll('.card-animation');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>
@endsection

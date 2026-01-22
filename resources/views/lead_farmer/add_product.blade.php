@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Add New Product')
@section('page-title', 'Add New Product')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/add_product.css') }}">
@endsection

@section('content')
<div class="add-product-container">
    <div class="card product-form-card animate__animated animate__fadeIn">
        <div class="card-header">
            <div class="header-content">
                <i class="fa-solid fa-plus-circle accent-icon"></i>
                <h2>Add New Product</h2>
            </div>
            <div class="header-description">
                Add products for farmers in your group
            </div>
        </div>
        
        <div class="card-body">
            <form id="addProductForm" action="{{ route('lf.storeProduct') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-step active" data-step="1">
                    <div class="step-header">
                        <div class="step-number">1</div>
                        <h4>Product Information</h4>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="farmer_id" class="form-label">
                                <i class="fa-solid fa-user-farmer"></i> Farmer *
                            </label>
                            <select name="farmer_id" id="farmer_id" class="form-control" required>
                                <option value="">Select Farmer</option>
                                @foreach($farmers as $farmer)
                                    <option value="{{ $farmer->id }}">{{ $farmer->name }} ({{ $farmer->nic_no }})</option>
                                @endforeach
                            </select>
                            <div class="form-hint">Select the farmer who owns this product</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="product_name" class="form-label">
                                <i class="fa-solid fa-tag"></i> Product Name *
                            </label>
                            <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Enter product name" required>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="type_variant" class="form-label">
                                <i class="fa-solid fa-leaf"></i> Type/Variant
                            </label>
                            <input type="text" name="type_variant" id="type_variant" class="form-control" placeholder="e.g., Organic, Hybrid">
                        </div>
                        
                        <div class="form-group">
                            <label for="product_description" class="form-label">
                                <i class="fa-solid fa-file-lines"></i> Product Description
                            </label>
                            <textarea name="product_description" id="product_description" class="form-control" rows="3" placeholder="Describe the product features, quality, etc."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="form-step" data-step="2">
                    <div class="step-header">
                        <div class="step-number">2</div>
                        <h4>Product Photo</h4>
                    </div>
                    
                    <div class="form-group photo-upload-container">
                        <div class="upload-area" id="uploadArea">
                            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                            <p class="upload-text">Click or drag to upload product photo</p>
                            <p class="upload-subtext">Max file size: 5MB. Supported formats: JPG, PNG</p>
                            <input type="file" name="product_photo" id="product_photo" accept="image/jpeg,image/png" class="file-input">
                        </div>
                        <div class="preview-container" id="previewContainer">
                            <img id="imagePreview" src="" alt="Preview" style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-preview" id="removePreview">
                                <i class="fa-solid fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="form-step" data-step="3">
                    <div class="step-header">
                        <div class="step-number">3</div>
                        <h4>Product Classification</h4>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="category_id" class="form-label">
                                <i class="fa-solid fa-layer-group"></i> Main Category *
                            </label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">Select Main Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="subcategory_id" class="form-label">
                                <i class="fa-solid fa-folder"></i> Subcategory *
                            </label>
                            <select name="subcategory_id" id="subcategory_id" class="form-control" required disabled>
                                <option value="">Select Subcategory</option>
                            </select>
                            <div class="form-hint">Select main category first</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="product_examples_id" class="form-label">
                            <i class="fa-solid fa-box"></i> Specific Product *
                        </label>
                        <select name="product_examples_id" id="product_examples_id" class="form-control" required disabled>
                            <option value="">Select Specific Product</option>
                        </select>
                        <div class="form-hint">Select subcategory first</div>
                    </div>
                </div>
                
                <div class="form-step" data-step="4">
                    <div class="step-header">
                        <div class="step-number">4</div>
                        <h4>Quantity & Pricing</h4>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="quantity" class="form-label">
                                <i class="fa-solid fa-scale-balanced"></i> Quantity *
                            </label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="0" step="0.01" placeholder="e.g., 100" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="unit_of_measure" class="form-label">
                                <i class="fa-solid fa-ruler"></i> Unit of Measure *
                            </label>
                            <select name="unit_of_measure" id="unit_of_measure" class="form-control" required>
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit }}">{{ strtoupper($unit) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="quality_grade" class="form-label">
                                <i class="fa-solid fa-star"></i> Quality Grade
                            </label>
                            <select name="quality_grade" id="quality_grade" class="form-control">
                                <option value="">Select Grade</option>
                                @foreach($grades as $grade)
                                    <option value="{{ $grade }}">{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="selling_price" class="form-label">
                                <i class="fa-solid fa-tag"></i> Selling Price (Per Unit) *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input type="number" name="selling_price" id="selling_price" class="form-control" min="0" step="0.01" placeholder="e.g., 150.00" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="expected_availability_date" class="form-label">
                            <i class="fa-solid fa-calendar-day"></i> Availability Date *
                        </label>
                        <input type="date" name="expected_availability_date" id="expected_availability_date" class="form-control" required>
                        <div class="form-hint">When will this product be available for pickup?</div>
                    </div>
                </div>
                
                <div class="form-step" data-step="5">
                    <div class="step-header">
                        <div class="step-number">5</div>
                        <h4>Pickup Location</h4>
                    </div>
                    
                    <div class="form-group">
                        <label for="pickup_address" class="form-label">
                            <i class="fa-solid fa-location-dot"></i> Pickup Address
                        </label>
                        <textarea name="pickup_address" id="pickup_address" class="form-control" rows="3" placeholder="Leave blank to use farmer's address"></textarea>
                        <div class="form-hint">If left empty, farmer's residential address will be used</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pickup_map_link" class="form-label">
                            <i class="fa-solid fa-map-location-dot"></i> Google Map Link
                        </label>
                        <input type="url" name="pickup_map_link" id="pickup_map_link" class="form-control" placeholder="https://maps.google.com/...">
                        <div class="form-hint">If left empty, farmer's map link will be used</div>
                    </div>
                </div>
                
                <div class="form-final-step">
                    <div class="form-check">
                        <input type="checkbox" name="is_available" id="is_available" class="form-check-input" checked>
                        <label for="is_available" class="form-check-label">
                            <i class="fa-solid fa-toggle-on"></i> Make product available for purchase
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline-secondary" id="prevBtn">
                            <i class="fa-solid fa-arrow-left"></i> Previous
                        </button>
                        
                        <button type="button" class="btn btn-primary" id="nextBtn">
                            Next <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fa-solid fa-check"></i> Save Product
                        </button>
                    </div>
                    
                    <div class="step-indicator">
                        <div class="step-dot active" data-step="1"></div>
                        <div class="step-dot" data-step="2"></div>
                        <div class="step-dot" data-step="3"></div>
                        <div class="step-dot" data-step="4"></div>
                        <div class="step-dot" data-step="5"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="form-guide">
    <div class="guide-card animate__animated animate__fadeInRight">
        <div class="guide-header">
            <i class="fa-solid fa-lightbulb"></i>
            <h5>Quick Tips</h5>
        </div>
        <ul class="guide-list">
            <li><i class="fa-solid fa-circle-check text-success"></i> Fill all required fields marked with *</li>
            <li><i class="fa-solid fa-circle-check text-success"></i> Product photos increase buyer interest</li>
            <li><i class="fa-solid fa-circle-check text-success"></i> Accurate classification helps buyers find products</li>
            <li><i class="fa-solid fa-circle-check text-success"></i> Set realistic availability dates</li>
            <li><i class="fa-solid fa-circle-check text-success"></i> Review all information before saving</li>
        </ul>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 5;
    
    function showStep(step) {
        $('.form-step').removeClass('active').addClass('animate__animated animate__fadeOut');
        setTimeout(() => {
            $('.form-step').removeClass('animate__animated animate__fadeOut').hide();
            $(`.form-step[data-step="${step}"]`).show().addClass('animate__animated animate__fadeIn active');
            $('.step-dot').removeClass('active');
            $(`.step-dot[data-step="${step}"]`).addClass('active');
            updateButtons();
        }, 300);
    }
    
    function updateButtons() {
        $('#prevBtn').toggle(currentStep > 1);
        $('#nextBtn').toggle(currentStep < totalSteps);
        $('#submitBtn').toggle(currentStep === totalSteps);
    }
    
    $('#nextBtn').click(function() {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        }
    });
    
    $('#prevBtn').click(function() {
        currentStep--;
        showStep(currentStep);
    });
    
    $('.step-dot').click(function() {
        const step = $(this).data('step');
        if (validateStep(currentStep)) {
            currentStep = step;
            showStep(currentStep);
        }
    });
    
    function validateStep(step) {
        let isValid = true;
        const stepElement = $(`.form-step[data-step="${step}"]`);
        
        stepElement.find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Required Field',
                    text: 'Please fill all required fields before proceeding.',
                    confirmButtonColor: '#10B981'
                });
                
                return false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        return isValid;
    }
    
    $('#category_id').on('change', function() {
        const categoryId = $(this).val();
        const subcategorySelect = $('#subcategory_id');
        const productExamplesSelect = $('#product_examples_id');
        
        if (categoryId) {
            $.ajax({
                url: "{{ route('lf.getSubcategories', ':categoryId') }}".replace(':categoryId', categoryId),
                type: 'GET',
                beforeSend: function() {
                    subcategorySelect.prop('disabled', true).html('<option value="">Loading...</option>');
                    productExamplesSelect.prop('disabled', true).html('<option value="">Select Specific Product</option>');
                },
                success: function(data) {
                    subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
                    $.each(data, function(index, subcategory) {
                        subcategorySelect.append(
                            `<option value="${subcategory.id}">${subcategory.subcategory_name}</option>`
                        );
                    });
                    subcategorySelect.prop('disabled', false);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load subcategories',
                        confirmButtonColor: '#10B981'
                    });
                }
            });
        } else {
            subcategorySelect.prop('disabled', true).html('<option value="">Select Subcategory</option>');
            productExamplesSelect.prop('disabled', true).html('<option value="">Select Specific Product</option>');
        }
    });
    
    $('#subcategory_id').on('change', function() {
        const subcategoryId = $(this).val();
        const productExamplesSelect = $('#product_examples_id');
        
        if (subcategoryId) {
            $.ajax({
                url: "{{ route('lf.getProductExamples', ':subcategoryId') }}".replace(':subcategoryId', subcategoryId),
                type: 'GET',
                beforeSend: function() {
                    productExamplesSelect.prop('disabled', true).html('<option value="">Loading...</option>');
                },
                success: function(data) {
                    productExamplesSelect.empty().append('<option value="">Select Specific Product</option>');
                    $.each(data, function(index, example) {
                        productExamplesSelect.append(
                            `<option value="${example.id}">${example.product_name}</option>`
                        );
                    });
                    productExamplesSelect.prop('disabled', false);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load specific products',
                        confirmButtonColor: '#10B981'
                    });
                }
            });
        } else {
            productExamplesSelect.prop('disabled', true).html('<option value="">Select Specific Product</option>');
        }
    });
    
    const uploadArea = $('#uploadArea');
    const fileInput = $('#product_photo');
    const previewContainer = $('#previewContainer');
    const imagePreview = $('#imagePreview');
    const removePreview = $('#removePreview');
    
    uploadArea.on('click', function() {
        fileInput.click();
    });
    
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        uploadArea.addClass('dragover');
    });
    
    uploadArea.on('dragleave', function() {
        uploadArea.removeClass('dragover');
    });
    
    uploadArea.on('drop', function(e) {
        e.preventDefault();
        uploadArea.removeClass('dragover');
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });
    
    fileInput.on('change', function() {
        if (this.files.length > 0) {
            handleFile(this.files[0]);
        }
    });
    
    function handleFile(file) {
        if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File',
                text: 'Please upload only JPG or PNG images',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Maximum file size is 5MB',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.attr('src', e.target.result).show();
            previewContainer.show();
            uploadArea.hide();
        };
        reader.readAsDataURL(file);
    }
    
    removePreview.on('click', function() {
        fileInput.val('');
        imagePreview.hide();
        previewContainer.hide();
        uploadArea.show();
    });
    
    $('#expected_availability_date').attr('min', new Date().toISOString().split('T')[0]);
    
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateStep(currentStep)) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please complete all required fields',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        Swal.fire({
            title: 'Saving Product',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const formData = new FormData(this);
        formData.append('is_available', $('#is_available').is(':checked') ? '1' : '0');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        html: `
                            <div class="text-center">
                                <i class="fa-solid fa-circle-check text-success mb-3" style="font-size: 48px;"></i>
                                <h4>Product Added Successfully!</h4>
                                <p class="mb-3"><strong>${response.product_name}</strong> has been added to the system.</p>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-outline-success me-2" id="addAnotherBtn">
                                        <i class="fa-solid fa-plus"></i> Add New Product
                                    </button>
                                    <button type="button" class="btn btn-primary" id="viewProductsBtn">
                                        <i class="fa-solid fa-eye"></i> View Products
                                    </button>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCloseButton: true,
                        allowOutsideClick: false
                    }).then(() => {
                        // Handle button clicks in the modal
                        $('#addAnotherBtn').on('click', function() {
                            resetForm();
                            Swal.close();
                        });
                        
                        $('#viewProductsBtn').on('click', function() {
                            window.location.href = "{{ route('lf.manageProducts') }}";
                        });
                    });
                    
                    // Also attach event listeners when modal opens
                    $(document).on('click', '#addAnotherBtn', function() {
                        resetForm();
                        Swal.close();
                    });
                    
                    $(document).on('click', '#viewProductsBtn', function() {
                        window.location.href = "{{ route('lf.manageProducts') }}";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to add product',
                        confirmButtonColor: '#10B981'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to add product';
                let errorDetails = '';
                
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    errorMessage = 'Validation Error';
                    errorDetails = Object.values(errors).map(error => error[0]).join('<br>');
                } else if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.message || errorMessage;
                    if (xhr.responseJSON.error_details) {
                        errorDetails = `
                            <div class="text-start mt-3">
                                <small class="text-muted">
                                    Error: ${xhr.responseJSON.error_details.message}<br>
                                    File: ${xhr.responseJSON.error_details.file}<br>
                                    Line: ${xhr.responseJSON.error_details.line}
                                </small>
                            </div>
                        `;
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: errorMessage,
                    html: errorDetails || 'An unexpected error occurred. Please try again.',
                    confirmButtonColor: '#10B981',
                    showCancelButton: xhr.status !== 422,
                    cancelButtonText: 'Close',
                    confirmButtonText: 'Try Again',
                    showCloseButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Retry submission
                        $('#addProductForm').submit();
                    }
                });
            }
        });
    });

    function resetForm() {
        $('#addProductForm')[0].reset();
        fileInput.val('');
        imagePreview.hide();
        previewContainer.hide();
        uploadArea.show();
        $('#subcategory_id, #product_examples_id').prop('disabled', true).empty();
        currentStep = 1;
        showStep(currentStep);
        
        // Reset the is_available checkbox
        $('#is_available').prop('checked', true);
        
        // Reset form validation classes
        $('.form-control').removeClass('is-invalid');
        
        // Focus on first field
        $('#product_name').focus();
        
        // Show success message
        toastr.success('Form reset successfully. You can add a new product.');
    }
    
    showStep(currentStep);
});
</script>
@endsection
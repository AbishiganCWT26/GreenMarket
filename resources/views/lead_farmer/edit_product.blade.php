@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/add_product.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="add-product-container">
    <div class="add-product-card">
        <div class="card-header">
            <h3>
                <i class="fas fa-edit"></i>
                Edit Product: {{ $product->product_name }}
            </h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('lf.updateProduct', $product->id) }}" enctype="multipart/form-data" id="productForm" class="product-form">
                @csrf

                <!-- Product Information Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Product Information
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product_name" class="form-label required">
                                <i class="fas fa-tag"></i> Product Name
                            </label>
                            <input type="text" name="product_name" id="product_name"
                                   class="form-control @error('product_name') is-invalid @enderror"
                                   value="{{ old('product_name', $product->product_name) }}" required
                                   placeholder="Enter product name">
                            @error('product_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="type_variant" class="form-label">
                                <i class="fas fa-layer-group"></i> Type/Variant
                            </label>
                            <input type="text" name="type_variant" id="type_variant"
                                   class="form-control" value="{{ old('type_variant', $product->type_variant) }}"
                                   placeholder="e.g., Dried, Fresh, Pickled">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="product_description" class="form-label">
                            <i class="fas fa-align-left"></i> Product Description
                        </label>
                        <textarea name="product_description" id="product_description"
                                  class="form-control" rows="3"
                                  placeholder="Enter product description">{{ old('product_description', $product->product_description) }}</textarea>
                    </div>
                </div>

                <!-- Product Photo Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-camera"></i>
                        Product Photo
                    </div>
                    
                    <div class="form-group">
                        <div class="mb-2">
                            @if($product->product_photo)
                                <img src="{{ asset('storage/product_photos/' . $product->product_photo) }}" 
                                     alt="Current Photo" style="max-width: 150px; border-radius: 8px;">
                                <p class="small text-muted">Current photo</p>
                            @endif
                        </div>
                        <div class="custom-file">
                            <input type="file" name="product_photo" id="product_photo"
                                   class="custom-file-input @error('product_photo') is-invalid @enderror"
                                   accept="image/*">
                            <label class="custom-file-label" for="product_photo">Change product photo</label>
                            @error('product_photo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Max file size: 2MB. Supported formats: JPG, PNG, GIF. Leave blank to keep current photo.
                        </div>
                        <div id="photo-preview" class="mt-2"></div>
                    </div>
                </div>

                <!-- Farmer & Category Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-sitemap"></i>
                        Classification
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="farmer_id" class="form-label required">
                                <i class="fas fa-user"></i> Farmer
                            </label>
                            <select name="farmer_id" id="farmer_id"
                                    class="form-control @error('farmer_id') is-invalid @enderror" required>
                                <option value="">Select Farmer</option>
                                @foreach($farmers as $farmer)
                                    <option value="{{ $farmer->id }}" {{ old('farmer_id', $product->farmer_id) == $farmer->id ? 'selected' : '' }}>
                                        {{ $farmer->name }} ({{ $farmer->nic_no }})
                                    </option>
                                @endforeach
                            </select>
                            @error('farmer_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id" class="form-label required">
                                <i class="fas fa-folder"></i> Category
                            </label>
                            <select name="category_id" id="category_id"
                                    class="form-control @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="subcategory_id" class="form-label required">
                                <i class="fas fa-folder-open"></i> Subcategory
                            </label>
                            <select name="subcategory_id" id="subcategory_id"
                                    class="form-control @error('subcategory_id') is-invalid @enderror" required>
                                <option value="">Select Subcategory</option>
                            </select>
                            @error('subcategory_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="product_examples_id" class="form-label required">
                                <i class="fas fa-leaf"></i> Specific Product
                            </label>
                            <select name="product_examples_id" id="product_examples_id"
                                    class="form-control @error('product_examples_id') is-invalid @enderror" required>
                                <option value="">Select Specific Product</option>
                            </select>
                            <small class="form-text text-muted">Select the type of product you are adding</small>
                            @error('product_examples_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Quantity & Pricing Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-balance-scale"></i>
                        Quantity & Pricing
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="quantity" class="form-label required">
                                <i class="fas fa-weight-hanging"></i> Quantity
                            </label>
                            <input type="number" name="quantity" id="quantity"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', $product->quantity) }}" step="0.01" min="0" required
                                   placeholder="Enter quantity">
                            @error('quantity')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="unit_of_measure" class="form-label required">
                                <i class="fas fa-ruler"></i> Unit of Measure
                            </label>
                            <select name="unit_of_measure" id="unit_of_measure"
                                    class="form-control @error('unit_of_measure') is-invalid @enderror" required>
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit }}" {{ old('unit_of_measure', $product->unit_of_measure) == $unit ? 'selected' : '' }}>
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
                                <i class="fas fa-star"></i> Quality Grade
                            </label>
                            <select name="quality_grade" id="quality_grade" class="form-control">
                                <option value="">Select Grade</option>
                                @foreach($grades as $grade)
                                    <option value="{{ $grade }}" {{ old('quality_grade', $product->quality_grade) == $grade ? 'selected' : '' }}>
                                        {{ $grade }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="selling_price" class="form-label required">
                                <i class="fas fa-money-bill-wave"></i> Selling Price (Per Unit)
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">LKR</span>
                                </div>
                                <input type="number" name="selling_price" id="selling_price"
                                       class="form-control @error('selling_price') is-invalid @enderror"
                                       value="{{ old('selling_price', $product->selling_price) }}" step="0.01" min="0" required
                                       placeholder="Enter price">
                                @error('selling_price')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="expected_availability_date" class="form-label required">
                                <i class="fas fa-calendar-alt"></i> Availability Date
                            </label>
                            <input type="date" name="expected_availability_date" id="expected_availability_date"
                                   class="form-control @error('expected_availability_date') is-invalid @enderror"
                                   value="{{ old('expected_availability_date', $product->expected_availability_date ? $product->expected_availability_date->format('Y-m-d') : '') }}" required>
                            @error('expected_availability_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pickup Location Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-map-marker-alt"></i>
                        Pickup Location
                    </div>
                    
                    <div class="form-group">
                        <label for="pickup_address" class="form-label">
                            <i class="fas fa-home"></i> Pickup Address
                        </label>
                        <textarea name="pickup_address" id="pickup_address"
                                  class="form-control" rows="2"
                                  placeholder="Leave blank to use farmer's address">{{ old('pickup_address', $product->pickup_address) }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="pickup_map_link" class="form-label">
                            <i class="fas fa-map-marked-alt"></i> Google Map Link
                        </label>
                        <input type="url" name="pickup_map_link" id="pickup_map_link"
                               class="form-control" value="{{ old('pickup_map_link', $product->pickup_map_link) }}"
                               placeholder="https://maps.google.com/...">
                    </div>
                </div>

                <!-- Availability Section -->
                <div class="form-section">
                    <div class="form-check">
                        <input type="checkbox" name="is_available" id="is_available"
                               class="form-check-input" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_available">
                            Make product available for purchase
                        </label>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                    <a href="{{ route('lf.manageProducts') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // File input preview
    $('#product_photo').on('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File too large',
                    text: 'Maximum file size is 2MB',
                    confirmButtonColor: '#10B981'
                });
                $(this).val('');
                $('#photo-preview').html('');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#photo-preview').html(`
                    <img src="${e.target.result}"
                         alt="Product Preview" style="max-width: 150px; border-radius: 8px;">
                `);
            }
            reader.readAsDataURL(file);
            
            $('.custom-file-label').text(file.name);
        } else {
            $('#photo-preview').html('');
            $('.custom-file-label').text('Choose product photo');
        }
    });

    // Load subcategories when category changes
    $('#category_id').on('change', function() {
        const categoryId = $(this).val();
        const subcategorySelect = $('#subcategory_id');
        const exampleSelect = $('#product_examples_id');
        
        console.log('Category Selection Changed:', categoryId);

        // Clear dependent dropdowns
        subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
        exampleSelect.empty().append('<option value="">Select Specific Product</option>');

        if (categoryId) {
            console.log('Fetching subcategories for ID:', categoryId);
            subcategorySelect.addClass('loading');
            $.ajax({
                url: "{{ route('lf.getSubcategories', ':categoryId') }}".replace(':categoryId', categoryId),
                type: 'GET',
                success: function(data) {
                    console.log('Subcategories data received:', data);
                    subcategorySelect.removeClass('loading');
                    
                    if (data && data.length > 0) {
                        $.each(data, function(index, subcategory) {
                            subcategorySelect.append(
                                `<option value="${subcategory.id}">${subcategory.subcategory_name}</option>`
                            );
                        });
                        if (typeof toastr !== 'undefined') toastr.success('Subcategories loaded');
                    } else {
                        subcategorySelect.append('<option value="" disabled>No subcategories found</option>');
                        if (typeof toastr !== 'undefined') toastr.warning('No subcategories found for this category');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error (Subcategories):', error);
                    console.error('Status:', status);
                    console.error('Response Text:', xhr.responseText);
                    subcategorySelect.removeClass('loading').empty().append('<option value="">Error loading</option>');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load subcategories. Please refresh the page.',
                            confirmButtonColor: '#10B981'
                        });
                    }
                }
            });
        }
    });

    // Load product examples when subcategory changes
    $('#subcategory_id').on('change', function() {
        const subcategoryId = $(this).val();
        const exampleSelect = $('#product_examples_id');
        
        console.log('Subcategory Selection Changed:', subcategoryId);

        // Clear dependent dropdowns
        exampleSelect.empty().append('<option value="">Select Specific Product</option>');

        if (subcategoryId) {
            console.log('Fetching product examples for subcategory:', subcategoryId);
            exampleSelect.addClass('loading');
            $.ajax({
                url: "{{ route('lf.getProductExamples', ':subcategoryId') }}".replace(':subcategoryId', subcategoryId),
                type: 'GET',
                success: function(data) {
                    console.log('Product examples data received:', data);
                    exampleSelect.removeClass('loading');
                    
                    if (data && data.length > 0) {
                        $.each(data, function(index, item) {
                            exampleSelect.append(
                                `<option value="${item.id}">${item.product_name}</option>`
                            );
                        });
                        if (typeof toastr !== 'undefined') toastr.success('Specific products loaded');
                    } else {
                        exampleSelect.append('<option value="" disabled>No specific products found</option>');
                        if (typeof toastr !== 'undefined') toastr.warning('No specific products found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error (Examples):', error);
                    console.error('Response:', xhr.responseText);
                    exampleSelect.removeClass('loading').empty().append('<option value="">Error loading</option>');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load specific products.',
                            confirmButtonColor: '#10B981'
                        });
                    }
                }
            });
        }
    });

    // Handle Specific Product selection
    $('#product_examples_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const selectedValue = $(this).val();
        console.log('Specific Product Selection Changed:', selectedValue);
        
        if (selectedValue) {
            const productNameInput = $('#product_name');
            productNameInput.val(selectedOption.text().trim());
            console.log('Auto-filled product name:', selectedOption.text());
        }
    });

    // Initialize dropdowns for existing product
    const initialCategoryId = "{{ old('category_id', $product->category_id) }}";
    const initialSubcategoryId = "{{ old('subcategory_id', $product->subcategory_id) }}";
    const initialExampleId = "{{ old('product_examples_id', $product->product_examples_id) }}";

    if (initialCategoryId) {
        $('#category_id').trigger('change', [initialSubcategoryId, initialExampleId]);
    }

    // Form validation and submission
    $('#productForm').on('submit', function(e) {
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        
        // Simple validation
        const requiredFields = $('#productForm').find('[required]');
        let isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Missing Information',
                text: 'Please fill in all required fields marked with *',
                confirmButtonColor: '#10B981'
            });
            return;
        }
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
    });
});
</script>
@endpush

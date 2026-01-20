@extends('admin.layouts.admin_master')

@section('title', 'Add New Product')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/product-oversight.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.create-product-form {
    max-width: 900px;
    margin: 0 auto;
}

.form-card {
    background: var(--card-bg);
    border-radius: var(--radius-lg);
    padding: 30px;
    box-shadow: var(--shadow-md);
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #e5e7eb;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    color: var(--text-color);
}

.section-header i {
    font-size: 20px;
    color: var(--primary-green);
}

.section-header h3 {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}

.form-select {
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: var(--radius-sm);
    font-size: 14px;
    transition: var(--transition);
    background: white;
    width: 100%;
    cursor: pointer;
}

.form-select:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="url"],
textarea {
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: var(--radius-sm);
    font-size: 14px;
    transition: var(--transition);
    width: 100%;
    font-family: inherit;
}

input:focus,
textarea:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.error-message {
    color: #ef4444;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}

.image-upload-area {
    width: 100%;
}

.upload-preview {
    width: 100%;
    height: 200px;
    border: 2px dashed #d1d5db;
    border-radius: var(--radius-sm);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    color: var(--muted);
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.upload-preview:hover {
    border-color: var(--primary-green);
    background: #f0fdf4;
}

.upload-preview.dragover {
    border-color: var(--primary-green);
    background: #f0fdf4;
}

.upload-preview i {
    font-size: 48px;
    color: #9ca3af;
}

.upload-preview p {
    margin: 0;
    font-weight: 500;
}

.upload-preview span {
    font-size: 12px;
}

.upload-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: white;
    opacity: 0;
    transition: var(--transition);
}

.upload-preview:hover .preview-overlay {
    opacity: 1;
}

.preview-overlay i {
    font-size: 24px;
    color: white;
}

.preview-overlay span {
    font-size: 14px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #e5e7eb;
}

@media (max-width: 768px) {
    .form-card {
        padding: 20px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection

@section('content')
<div class="product-management-container">
    <div class="header-section">
        <div class="header-content">
            <h1><i class="fas fa-plus-circle"></i> Add New Product</h1>
            <p>Create a new product listing for farmers</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline" style="background: white; color: var(--dark-green);">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>
    </div>

    <div class="create-product-form">
        <div class="form-card">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="createProductForm">
                @csrf

                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-info-circle"></i>
                        <h3>Basic Information</h3>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-users"></i> Lead Farmer Group *</label>
                            <select name="lead_farmer_id" required class="form-select" id="leadFarmerSelect" onchange="loadFarmersByLeadFarmer(this.value)">
                                <option value="">Select Lead Farmer Group</option>
                                @foreach($leadFarmers as $lf)
                                <option value="{{ $lf->id }}" {{ old('lead_farmer_id') == $lf->id ? 'selected' : '' }}>{{ $lf->group_name }}</option>
                                @endforeach
                            </select>
                            @error('lead_farmer_id')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Farmer *</label>
                            <select name="farmer_id" required class="form-select" id="farmerSelect" disabled>
                                <option value="">Select Lead Farmer Group First</option>
                            </select>
                            @error('farmer_id')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Product Name *</label>
                        <input type="text" name="product_name" value="{{ old('product_name') }}" required placeholder="Enter product name">
                        @error('product_name')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> Product Description</label>
                        <textarea name="product_description" rows="4" placeholder="Enter product description">{{ old('product_description') }}</textarea>
                        @error('product_description')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-tags"></i>
                        <h3>Category & Classification</h3>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-folder"></i> Category *</label>
                            <select name="category_id" required class="form-select">
                                <option value="">Select Category</option>
                                @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->category_name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-folder-open"></i> Subcategory *</label>
                            <select name="subcategory_id" required class="form-select">
                                <option value="">Select Subcategory</option>
                                @foreach($subcategories as $sc)
                                <option value="{{ $sc->id }}" {{ old('subcategory_id') == $sc->id ? 'selected' : '' }}>{{ $sc->subcategory_name }}</option>
                                @endforeach
                            </select>
                            @error('subcategory_id')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-leaf"></i> Type Variant</label>
                            <select name="type_variant" class="form-select">
                                <option value="fresh" {{ old('type_variant') == 'fresh' ? 'selected' : '' }}>Fresh</option>
                                <option value="dried" {{ old('type_variant') == 'dried' ? 'selected' : '' }}>Dried</option>
                                <option value="pickled" {{ old('type_variant') == 'pickled' ? 'selected' : '' }}>Pickled</option>
                                <option value="canned" {{ old('type_variant') == 'canned' ? 'selected' : '' }}>Canned</option>
                                <option value="processed" {{ old('type_variant') == 'processed' ? 'selected' : '' }}>Processed</option>
                                <option value="other" {{ old('type_variant') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-check-circle"></i> Availability</label>
                            <select name="is_available" class="form-select">
                                <option value="1" selected>Available</option>
                                <option value="0">Unavailable</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-balance-scale"></i>
                        <h3>Quantity & Pricing</h3>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-weight"></i> Quantity *</label>
                            <input type="number" name="quantity" value="{{ old('quantity', 0) }}" step="0.01" min="0" required placeholder="0.00">
                            @error('quantity')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-ruler"></i> Unit of Measure *</label>
                            <select name="unit_of_measure" required class="form-select">
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->standard_value }}" {{ old('unit_of_measure') == $unit->standard_value ? 'selected' : '' }}>{{ $unit->standard_value }}</option>
                                @endforeach
                            </select>
                            @error('unit_of_measure')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-star"></i> Quality Grade *</label>
                            <select name="quality_grade" required class="form-select">
                                <option value="">Select Grade</option>
                                @foreach($grades as $grade)
                                <option value="{{ $grade->standard_value }}" {{ old('quality_grade') == $grade->standard_value ? 'selected' : '' }}>{{ $grade->standard_value }}</option>
                                @endforeach
                            </select>
                            @error('quality_grade')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-money-bill-wave"></i> Selling Price (LKR) *</label>
                            <input type="number" name="selling_price" value="{{ old('selling_price', 0) }}" step="0.01" min="0" required placeholder="0.00">
                            @error('selling_price')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>Availability & Location</h3>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Expected Availability Date</label>
                        <input type="date" name="expected_availability_date" value="{{ old('expected_availability_date') }}">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Pickup Address *</label>
                        <textarea name="pickup_address" rows="3" required placeholder="Enter full pickup address">{{ old('pickup_address') }}</textarea>
                        @error('pickup_address')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-map"></i> Pickup Map Link (Optional)</label>
                        <input type="url" name="pickup_map_link" value="{{ old('pickup_map_link') }}" placeholder="https://maps.google.com/...">
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-image"></i>
                        <h3>Product Image</h3>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-upload"></i> Product Photo</label>
                        <div class="image-upload-area">
                            <div class="upload-preview" id="imagePreview">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload or drag and drop</p>
                                <span>PNG, JPG, GIF up to 4MB</span>
                            </div>
                            <input type="file" name="product_photo" id="productPhoto" accept="image/*" style="display: none;">
                            @error('product_photo')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const imagePreview = $('#imagePreview');
        const productPhoto = $('#productPhoto');

        imagePreview.click(function() {
            productPhoto.click();
        });

        productPhoto.change(function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.html(`
                        <img src="${e.target.result}" alt="Preview">
                        <div class="preview-overlay">
                            <i class="fas fa-sync-alt"></i>
                            <span>Change Image</span>
                        </div>
                    `);
                }
                reader.readAsDataURL(file);
            }
        });

        imagePreview.on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });

        imagePreview.on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        });

        imagePreview.on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                productPhoto[0].files = files;
                productPhoto.trigger('change');
            }
        });

        $('#createProductForm').submit(function(e) {
            const form = $(this);
            const farmerSelect = $('#farmerSelect');

            // Validate farmer selection
            if (!farmerSelect.val()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select a farmer.'
                });
                return;
            }

            if (form.find('.error-message').length > 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Form Errors',
                    text: 'Please fix all errors before submitting.'
                });
            }
        });

        // Initialize farmer dropdown if lead farmer is already selected
        const selectedLeadFarmerId = "{{ old('lead_farmer_id') }}";
        if (selectedLeadFarmerId) {
            loadFarmersByLeadFarmer(selectedLeadFarmerId);
        }
    });

    function loadFarmersByLeadFarmer(leadFarmerId) {
        const farmerSelect = $('#farmerSelect');

        if (!leadFarmerId) {
            farmerSelect.html('<option value="">Select Lead Farmer Group First</option>');
            farmerSelect.prop('disabled', true);
            return;
        }

        farmerSelect.html('<option value="">Loading farmers...</option>');
        farmerSelect.prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.products.get-farmers", ":leadFarmerId") }}'.replace(':leadFarmerId', leadFarmerId),
            method: 'GET',
            success: function(response) {
                if (response.farmers.length > 0) {
                    let options = '<option value="">Select Farmer</option>';
                    const oldFarmerId = "{{ old('farmer_id') }}";
                    response.farmers.forEach(farmer => {
                        const selected = oldFarmerId == farmer.id ? 'selected' : '';
                        options += `<option value="${farmer.id}" ${selected}>${farmer.name}</option>`;
                    });
                    farmerSelect.html(options);
                } else {
                    farmerSelect.html('<option value="">No farmers in this group</option>');
                }
                farmerSelect.prop('disabled', false);
            },
            error: function() {
                farmerSelect.html('<option value="">Error loading farmers</option>');
                farmerSelect.prop('disabled', false);
            }
        });
    }
</script>
@endsection

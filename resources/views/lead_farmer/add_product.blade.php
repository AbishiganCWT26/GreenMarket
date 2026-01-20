@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Add New Product')
@section('page-title', 'Add New Product')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/add_product.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Product</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('lf.storeProduct') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Product Name & Description -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_name">Product Name *</label>
                                    <input type="text" name="product_name" id="product_name"
                                           class="form-control @error('product_name') is-invalid @enderror"
                                           value="{{ old('product_name') }}" required>
                                    @error('product_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type_variant">Type/Variant</label>
                                    <input type="text" name="type_variant" id="type_variant"
                                           class="form-control" value="{{ old('type_variant') }}"
                                           placeholder="e.g., Dried, Fresh, Pickled">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="product_description">Product Description</label>
                            <textarea name="product_description" id="product_description"
                                      class="form-control" rows="3">{{ old('product_description') }}</textarea>
                        </div>

                        <!-- Product Photo -->
                        <div class="form-group">
                            <label for="product_photo">Product Photo</label>
                            <div class="custom-file">
                                <input type="file" name="product_photo" id="product_photo"
                                       class="custom-file-input @error('product_photo') is-invalid @enderror"
                                       accept="image/*">
                                <label class="custom-file-label" for="product_photo">Choose file</label>
                                @error('product_photo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Max file size: 2MB. Supported formats: JPG, PNG, GIF</small>
                            <div id="photo-preview" class="mt-2"></div>
                        </div>

                        <!-- Farmer Selection -->
                        <div class="form-group">
                            <label for="farmer_id">Farmer *</label>
                            <select name="farmer_id" id="farmer_id"
                                    class="form-control @error('farmer_id') is-invalid @enderror" required>
                                <option value="">Select Farmer</option>
                                @foreach($farmers as $farmer)
                                    <option value="{{ $farmer->id }}" {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                        {{ $farmer->name }} ({{ $farmer->nic_no }})
                                    </option>
                                @endforeach
                            </select>
                            @error('farmer_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Category & Subcategory -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">Category *</label>
                                    <select name="category_id" id="category_id"
                                            class="form-control @error('category_id') is-invalid @enderror" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subcategory_id">Subcategory *</label>
                                    <select name="subcategory_id" id="subcategory_id"
                                            class="form-control @error('subcategory_id') is-invalid @enderror" required>
                                        <option value="">Select Subcategory</option>
                                        <!-- Will be populated via AJAX -->
                                    </select>
                                    @error('subcategory_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Quantity & Units -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantity">Quantity *</label>
                                    <input type="number" name="quantity" id="quantity"
                                           class="form-control @error('quantity') is-invalid @enderror"
                                           value="{{ old('quantity') }}" step="0.01" min="0" required>
                                    @error('quantity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unit_of_measure">Unit of Measure *</label>
                                    <select name="unit_of_measure" id="unit_of_measure"
                                            class="form-control @error('unit_of_measure') is-invalid @enderror" required>
                                        <option value="">Select Unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit }}" {{ old('unit_of_measure') == $unit ? 'selected' : '' }}>
                                                {{ $unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_of_measure')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quality_grade">Quality Grade</label>
                                    <select name="quality_grade" id="quality_grade" class="form-control">
                                        <option value="">Select Grade</option>
                                        @foreach($grades as $grade)
                                            <option value="{{ $grade }}" {{ old('quality_grade') == $grade ? 'selected' : '' }}>
                                                {{ $grade }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Price & Availability -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selling_price">Selling Price (Per Unit) *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">LKR</span>
                                        </div>
                                        <input type="number" name="selling_price" id="selling_price"
                                               class="form-control @error('selling_price') is-invalid @enderror"
                                               value="{{ old('selling_price') }}" step="0.01" min="0" required>
                                        @error('selling_price')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expected_availability_date">Availability Date *</label>
                                    <input type="date" name="expected_availability_date" id="expected_availability_date"
                                           class="form-control @error('expected_availability_date') is-invalid @enderror"
                                           value="{{ old('expected_availability_date') }}" required>
                                    @error('expected_availability_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Pickup Location -->
                        <div class="form-group">
                            <label for="pickup_address">Pickup Address</label>
                            <textarea name="pickup_address" id="pickup_address"
                                      class="form-control" rows="2"
                                      placeholder="Leave blank to use farmer's address">{{ old('pickup_address') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="pickup_map_link">Pickup Location Google Map Link</label>
                            <input type="url" name="pickup_map_link" id="pickup_map_link"
                                   class="form-control" value="{{ old('pickup_map_link') }}"
                                   placeholder="https://maps.google.com/...">
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="is_available" id="is_available"
                                       class="form-check-input" value="1" checked>
                                <label class="form-check-label" for="is_available">
                                    Make product available for purchase
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Product
                            </button>
                            <a href="{{ route('lf.manageProducts') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
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
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#photo-preview').html(`
                    <img src="${e.target.result}"
                         style="max-width: 200px; max-height: 200px; border-radius: 5px;"
                         alt="Preview">
                `);
            }
            reader.readAsDataURL(file);
        }
    });

    // Load subcategories when category changes
    $('#category_id').on('change', function() {
        const categoryId = $(this).val();
        const subcategorySelect = $('#subcategory_id');

        if (categoryId) {
            $.ajax({
                url: "{{ route('lf.getSubcategories', ':categoryId') }}".replace(':categoryId', categoryId),
                type: 'GET',
                success: function(data) {
                    subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
                    $.each(data, function(index, subcategory) {
                        subcategorySelect.append(
                            `<option value="${subcategory.id}">${subcategory.subcategory_name}</option>`
                        );
                    });
                },
                error: function() {
                    subcategorySelect.empty().append('<option value="">Error loading subcategories</option>');
                }
            });
        } else {
            subcategorySelect.empty().append('<option value="">Select Subcategory</option>');
        }
    });

    // Initialize subcategories if category is pre-selected
    @if(old('category_id'))
        $('#category_id').trigger('change');
        setTimeout(function() {
            $('#subcategory_id').val('{{ old("subcategory_id") }}');
        }, 500);
    @endif
});
</script>
@endpush

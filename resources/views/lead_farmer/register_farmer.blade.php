@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Register Farmer')

@section('page-title', 'Register Farmer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i> Register New Farmer
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('lf.storeFarmer') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">
                                    <i class="fas fa-id-card me-1"></i> Basic Information
                                </h6>
                                <hr class="mt-1">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nic_no" class="form-label">NIC Number *</label>
                                <input type="text" name="nic_no" id="nic_no"
                                       class="form-control @error('nic_no') is-invalid @enderror"
                                       value="{{ old('nic_no') }}" required>
                                @error('nic_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="profile_photo" class="form-label">Profile Photo</label>
                                <div class="custom-file">
                                    <input type="file" name="profile_photo" id="profile_photo"
                                           class="custom-file-input @error('profile_photo') is-invalid @enderror"
                                           accept="image/*">
                                    <label class="custom-file-label" for="profile_photo">Choose file</label>
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Max file size: 2MB. Supported formats: JPG, PNG</small>
                                <div id="photo-preview" class="mt-2"></div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">
                                    <i class="fas fa-phone me-1"></i> Contact Information
                                </h6>
                                <hr class="mt-1">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="primary_mobile" class="form-label">Primary Mobile Number *</label>
                                <input type="text" name="primary_mobile" id="primary_mobile"
                                       class="form-control @error('primary_mobile') is-invalid @enderror"
                                       value="{{ old('primary_mobile') }}" required>
                                @error('primary_mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                                <input type="text" name="whatsapp_number" id="whatsapp_number"
                                       class="form-control @error('whatsapp_number') is-invalid @enderror"
                                       value="{{ old('whatsapp_number') }}">
                                @error('whatsapp_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">
                                    <i class="fas fa-home me-1"></i> Address Information
                                </h6>
                                <hr class="mt-1">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="residential_address" class="form-label">Residential Address *</label>
                                <textarea name="residential_address" id="residential_address"
                                          class="form-control @error('residential_address') is-invalid @enderror"
                                          rows="3" required>{{ old('residential_address') }}</textarea>
                                @error('residential_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="district" class="form-label">District *</label>
                                <select name="district" id="district"
                                        class="form-control @error('district') is-invalid @enderror" required>
                                    <option value="">Select District</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district }}" {{ old('district') == $district ? 'selected' : '' }}>
                                            {{ $district }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="grama_niladhari_division" class="form-label">Grama Niladhari Division *</label>
                                <input type="text" name="grama_niladhari_division" id="grama_niladhari_division"
                                       class="form-control @error('grama_niladhari_division') is-invalid @enderror"
                                       value="{{ old('grama_niladhari_division') }}" required>
                                @error('grama_niladhari_division')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="address_map_link" class="form-label">Google Maps Link</label>
                                <input type="url" name="address_map_link" id="address_map_link"
                                       class="form-control @error('address_map_link') is-invalid @enderror"
                                       value="{{ old('address_map_link') }}"
                                       placeholder="https://maps.google.com/...">
                                @error('address_map_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Optional: Link to location on Google Maps for pickup directions</small>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">
                                    <i class="fas fa-money-bill-wave me-1"></i> Payment Information
                                </h6>
                                <hr class="mt-1">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="preferred_payment" class="form-label">Preferred Payment Method *</label>
                                <select name="preferred_payment" id="preferred_payment"
                                        class="form-control @error('preferred_payment') is-invalid @enderror" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="bank" {{ old('preferred_payment') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="ezcash" {{ old('preferred_payment') == 'ezcash' ? 'selected' : '' }}>EzCash</option>
                                    <option value="mcash" {{ old('preferred_payment') == 'mcash' ? 'selected' : '' }}>mCash</option>
                                    <option value="all" {{ old('preferred_payment') == 'all' ? 'selected' : '' }}>All Methods</option>
                                </select>
                                @error('preferred_payment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Bank Details (shown when bank or all is selected) -->
                            <div class="col-md-12 mb-3" id="bankDetails">
                                <h6 class="text-info">
                                    <i class="fas fa-university me-1"></i> Bank Account Details
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="bank_name" class="form-label">Bank Name</label>
                                        <input type="text" name="bank_name" id="bank_name"
                                               class="form-control @error('bank_name') is-invalid @enderror"
                                               value="{{ old('bank_name') }}">
                                        @error('bank_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label for="bank_branch" class="form-label">Bank Branch</label>
                                        <input type="text" name="bank_branch" id="bank_branch"
                                               class="form-control @error('bank_branch') is-invalid @enderror"
                                               value="{{ old('bank_branch') }}">
                                        @error('bank_branch')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label for="account_holder_name" class="form-label">Account Holder Name</label>
                                        <input type="text" name="account_holder_name" id="account_holder_name"
                                               class="form-control @error('account_holder_name') is-invalid @enderror"
                                               value="{{ old('account_holder_name') }}">
                                        @error('account_holder_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label for="account_number" class="form-label">Account Number</label>
                                        <input type="text" name="account_number" id="account_number"
                                               class="form-control @error('account_number') is-invalid @enderror"
                                               value="{{ old('account_number') }}">
                                        @error('account_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- EzCash Details -->
                            <div class="col-md-12 mb-3" id="ezcashDetails">
                                <h6 class="text-info">
                                    <i class="fas fa-mobile-alt me-1"></i> EzCash Details
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="ezcash_mobile" class="form-label">EzCash Mobile Number</label>
                                        <input type="text" name="ezcash_mobile" id="ezcash_mobile"
                                               class="form-control @error('ezcash_mobile') is-invalid @enderror"
                                               value="{{ old('ezcash_mobile') }}">
                                        @error('ezcash_mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- mCash Details -->
                            <div class="col-md-12 mb-3" id="mcashDetails">
                                <h6 class="text-info">
                                    <i class="fas fa-mobile-alt me-1"></i> mCash Details
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="mcash_mobile" class="form-label">mCash Mobile Number</label>
                                        <input type="text" name="mcash_mobile" id="mcash_mobile"
                                               class="form-control @error('mcash_mobile') is-invalid @enderror"
                                               value="{{ old('mcash_mobile') }}">
                                        @error('mcash_mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('lf.manageFarmers') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Back to Farmers List
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Register Farmer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .custom-file {
        position: relative;
        display: block;
    }

    .custom-file-input {
        position: relative;
        z-index: 2;
        width: 100%;
        height: calc(1.5em + 0.75rem + 2px);
        margin: 0;
        opacity: 0;
    }

    .custom-file-label {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1;
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        display: flex;
        align-items: center;
    }

    .custom-file-label::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 3;
        display: block;
        height: calc(1.5em + 0.75rem);
        padding: 0.375rem 0.75rem;
        line-height: 1.5;
        color: #495057;
        content: "Browse";
        background-color: #e9ecef;
        border-left: inherit;
        border-radius: 0 0.25rem 0.25rem 0;
        display: flex;
        align-items: center;
    }

    #photo-preview img {
        max-width: 150px;
        max-height: 150px;
        border-radius: 5px;
        border: 1px solid #ddd;
        padding: 3px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File input preview
        const profilePhotoInput = document.getElementById('profile_photo');
        const photoPreview = document.getElementById('photo-preview');

        if (profilePhotoInput) {
            profilePhotoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        photoPreview.innerHTML = `
                            <img src="${e.target.result}"
                                 alt="Profile Photo Preview"
                                 class="img-thumbnail">
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Payment method toggle
        const paymentMethodSelect = document.getElementById('preferred_payment');
        const bankDetails = document.getElementById('bankDetails');
        const ezcashDetails = document.getElementById('ezcashDetails');
        const mcashDetails = document.getElementById('mcashDetails');

        function togglePaymentDetails() {
            const value = paymentMethodSelect.value;

            // Hide all first
            bankDetails.style.display = 'none';
            ezcashDetails.style.display = 'none';
            mcashDetails.style.display = 'none';

            // Show based on selection
            if (value === 'bank' || value === 'all') {
                bankDetails.style.display = 'block';
            }
            if (value === 'ezcash' || value === 'all') {
                ezcashDetails.style.display = 'block';
            }
            if (value === 'mcash' || value === 'all') {
                mcashDetails.style.display = 'block';
            }
        }

        // Initial toggle
        togglePaymentDetails();

        // Add event listener
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', togglePaymentDetails);
        }

        // Custom file input label
        const fileInputs = document.querySelectorAll('.custom-file-input');
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                const fileName = this.files[0] ? this.files[0].name : 'Choose file';
                const label = this.nextElementSibling;
                label.textContent = fileName;
            });
        });

        // Auto-fill account holder name if empty
        const nameInput = document.getElementById('name');
        const accountHolderInput = document.getElementById('account_holder_name');

        if (nameInput && accountHolderInput) {
            nameInput.addEventListener('blur', function() {
                if (!accountHolderInput.value) {
                    accountHolderInput.value = this.value;
                }
            });
        }
    });
</script>
@endpush

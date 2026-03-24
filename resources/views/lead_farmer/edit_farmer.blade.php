@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Edit Farmer')

@section('page-title', 'Edit Farmer')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/farmer_registation.css') }}">
<style>
    .nic-status {
        margin-top: 5px;
        font-size: 0.85rem;
        font-weight: 500;
        display: block;
    }
    .nic-status.valid {
        color: #10B981 !important;
    }
    .nic-status.invalid {
        color: #ef4444 !important;
    }
    .error-text {
        color: #ef4444 !important;
        font-size: 0.85rem !important;
        margin-top: 5px !important;
        font-weight: 500 !important;
        display: block !important;
    }
    .text-success {
        color: #10B981 !important;
    }
    .text-danger {
        color: #ef4444 !important;
    }
</style>
@endsection

@section('content')
<div class="registration-container">
    <div class="registration-card">
        <div class="card-header">
            <i class="fas fa-user-edit"></i>
            <h2>Edit Farmer</h2>
            <p class="subtitle">Update farmer information</p>
        </div>

        <form id="farmerEditForm" action="{{ route('lf.updateFarmer', $farmer->id) }}" method="POST" enctype="multipart/form-data" class="registration-form">
            @csrf
            
            <div class="form-grid">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-user-circle"></i>
                        <h3>Basic Information</h3>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="form-label required-field">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <div class="input-with-icon">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $farmer->name) }}" 
                                   placeholder="Enter farmer's full name" required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nic_no" class="form-label required-field">
                            <i class="fas fa-id-card"></i> NIC Number
                        </label>
                        <div class="nic-input-container">
                            <div class="input-with-icon">
                                <i class="fas fa-id-card"></i>
                                <input type="text" class="form-control @error('nic_no') is-invalid @enderror"
                                       id="nic_no" name="nic_no" value="{{ old('nic_no', $farmer->nic_no) }}"
                                       placeholder="Enter NIC (e.g., 123456789V or 200123456789)"
                                       pattern="^([0-9]{9}[xXvV]|[0-9]{12})$"
                                       title="Enter valid NIC number (9 digits with letter or 12 digits)"
                                       required>
                            </div>
                            <div class="nic-format">
                                <i class="fas fa-info-circle"></i>
                                Format: 123456789V (old) or 200123456789 (new)
                            </div>
                            <div class="nic-status" id="nicStatus"></div>
                        </div>
                        @error('nic_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="fas fa-at"></i> Username
                        </label>
                        <div class="input-with-icon">
                            <input type="text" class="form-control" 
                                   id="username" value="{{ $farmer->user->username ?? 'N/A' }}" 
                                   disabled readonly>
                        </div>
                        <small class="form-text text-muted">Username cannot be changed</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-toggle-on"></i> Status
                        </label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', $farmer->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active Farmer</label>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-address-book"></i>
                        <h3>Contact Information</h3>
                    </div>
                    
                    <div class="form-group">
                        <label for="primary_mobile" class="form-label required-field">
                            <i class="fas fa-mobile-alt"></i> Primary Mobile Number
                        </label>
                        <div class="input-with-icon">
                            <input type="tel" class="form-control @error('primary_mobile') is-invalid @enderror" 
                                   id="primary_mobile" name="primary_mobile" 
                                   value="{{ old('primary_mobile', $farmer->primary_mobile) }}" 
                                   placeholder="e.g., 0771234567" pattern="[0-9]{10}" 
                                   title="Enter 10 digit mobile number" required>
                        </div>
                        @error('primary_mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="whatsapp_number" class="form-label">
                            <i class="fab fa-whatsapp"></i> WhatsApp Number
                        </label>
                        <div class="input-with-icon">
                            <input type="tel" class="form-control @error('whatsapp_number') is-invalid @enderror" 
                                   id="whatsapp_number" name="whatsapp_number" 
                                   value="{{ old('whatsapp_number', $farmer->whatsapp_number) }}" 
                                   placeholder="e.g., 0771234567" pattern="[0-9]{10}">
                        </div>
                        @error('whatsapp_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-with-icon">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" 
                                   value="{{ old('email', $farmer->email) }}" 
                                   placeholder="farmer@email.com">
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="profile_photo" class="form-label">
                            <i class="fas fa-camera"></i> Profile Photo
                        </label>
                        <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" 
                               id="profile_photo" name="profile_photo" 
                               accept="image/*" onchange="previewProfilePhoto(event)">
                        <div class="form-text">Max file size: 5MB. Allowed: JPG, PNG, GIF</div>
                        @error('profile_photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="profile-preview-container">
                        <div class="profile-preview" id="profilePreview">
                            @php
                                $profileUrl = asset('assets/images/farmer.png');
                                if ($farmer->user && $farmer->user->profile_photo) {
                                    $photoPath = 'uploads/profile_pictures/' . $farmer->user->profile_photo;
                                    if (file_exists(public_path($photoPath))) {
                                        $profileUrl = asset($photoPath);
                                    }
                                }
                            @endphp
                            <img src="{{ $profileUrl }}" alt="Profile Preview" id="profilePreviewImg">
                            <div class="preview-overlay">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>Address Information</h3>
                    </div>
                    
                    <div class="form-group">
                        <label for="residential_address" class="form-label required-field">
                            <i class="fas fa-home"></i> Residential Address
                        </label>
                        <textarea class="form-control @error('residential_address') is-invalid @enderror" 
                                  id="residential_address" name="residential_address" 
                                  rows="3" placeholder="Enter complete residential address" required>{{ old('residential_address', $farmer->residential_address) }}</textarea>
                        @error('residential_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="district" class="form-label required-field">
                            <i class="fas fa-map"></i> District
                        </label>
                        <select class="form-select @error('district') is-invalid @enderror" 
                                id="district" name="district" required>
                            <option value="">Select District</option>
                            <!-- Options will be populated by JS -->
                        </select>
                        @error('district')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="divisional_secretariat" class="form-label required-field">
                            <i class="fas fa-building"></i> Divisional Secretariat
                        </label>
                        <select class="form-select @error('divisional_secretariat') is-invalid @enderror" 
                                id="divisional_secretariat" name="divisional_secretariat" required>
                            <option value="">Select Divisional Secretariat</option>
                            <!-- Options will be populated by JS -->
                        </select>
                        @error('divisional_secretariat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="grama_niladhari_division" class="form-label required-field">
                            <i class="fas fa-landmark"></i> Grama Niladhari Division
                        </label>
                        <select class="form-select @error('grama_niladhari_division') is-invalid @enderror" 
                                id="grama_niladhari_division" name="grama_niladhari_division" required>
                            <option value="">Select GN Division</option>
                            <!-- Options will be populated by JS -->
                        </select>
                        @error('grama_niladhari_division')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="gn_division_code" class="form-label required-field">
                            <i class="fas fa-barcode"></i> GN Division Code
                        </label>
                        <select class="form-select @error('gn_division_code') is-invalid @enderror" 
                                id="gn_division_code" name="gn_division_code" required disabled>
                            <option value="">Select DS First</option>
                        </select>
                        @error('gn_division_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="address_map_link" class="form-label required-field">
                            <i class="fas fa-map-marked-alt"></i> Google Maps Link
                        </label>
                        <div class="input-with-icon">
                            <input type="url" class="form-control @error('address_map_link') is-invalid @enderror" 
                                   id="address_map_link" name="address_map_link" 
                                   value="{{ old('address_map_link', $farmer->address_map_link) }}" 
                                   placeholder="https://maps.google.com/?q=..." 
                                   pattern="https?://.*" required>
                            <div class="input-hint">
                                <i class="fas fa-info-circle"></i>
                                Required: Link to location on Google Maps for pickup directions
                            </div>
                        </div>
                        @error('address_map_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Payment Information Section -->
                <div class="form-section full-width">
                    <div class="section-header">
                        <i class="fas fa-money-bill-wave"></i>
                        <h3>Payment Information</h3>
                    </div>
                    
                    <div class="form-group">
                        <label for="preferred_payment" class="form-label required-field">
                            <i class="fas fa-credit-card"></i> Preferred Payment Method
                        </label>
                        <select class="form-select @error('preferred_payment') is-invalid @enderror" 
                                id="preferred_payment" name="preferred_payment" required>
                            <option value="">Select Payment Method</option>
                            <option value="bank" {{ old('preferred_payment', $farmer->preferred_payment) == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="ezcash" {{ old('preferred_payment', $farmer->preferred_payment) == 'ezcash' ? 'selected' : '' }}>EzCash</option>
                            <option value="mcash" {{ old('preferred_payment', $farmer->preferred_payment) == 'mcash' ? 'selected' : '' }}>mCash</option>
                            <option value="all" {{ old('preferred_payment', $farmer->preferred_payment) == 'all' ? 'selected' : '' }}>All Methods</option>
                        </select>
                        @error('preferred_payment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bank Details Section -->
                    <div id="bankDetails" class="payment-details-section" style="display: none;">
                        <h4 class="payment-section-title">
                            <i class="fas fa-university"></i> Bank Account Details
                        </h4>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="bank_name" class="form-label required-field">Bank Name</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                       id="bank_name" name="bank_name" 
                                       value="{{ old('bank_name', $farmer->bank_name) }}" 
                                       placeholder="e.g., Commercial Bank">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="bank_branch" class="form-label required-field">Bank Branch</label>
                                <input type="text" class="form-control @error('bank_branch') is-invalid @enderror" 
                                       id="bank_branch" name="bank_branch" 
                                       value="{{ old('bank_branch', $farmer->bank_branch) }}" 
                                       placeholder="e.g., Colombo 03">
                                @error('bank_branch')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="account_holder_name" class="form-label required-field">Account Holder Name</label>
                                <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror" 
                                       id="account_holder_name" name="account_holder_name" 
                                       value="{{ old('account_holder_name', $farmer->account_holder_name) }}" 
                                       placeholder="Account holder's name as in bank">
                                @error('account_holder_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="account_number" class="form-label required-field">Account Number</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                       id="account_number" name="account_number" 
                                       value="{{ old('account_number', $farmer->account_number) }}" 
                                       placeholder="e.g., 1234567890">
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- EzCash Details Section -->
                    <div id="ezcashDetails" class="payment-details-section" style="display: none;">
                        <h4 class="payment-section-title">
                            <i class="fas fa-mobile-alt"></i> EzCash Details
                        </h4>
                        <div class="form-group">
                            <label for="ezcash_mobile" class="form-label required-field">EzCash Mobile Number</label>
                            <div class="input-with-icon">
                                <input type="tel" class="form-control @error('ezcash_mobile') is-invalid @enderror" 
                                       id="ezcash_mobile" name="ezcash_mobile" 
                                       value="{{ old('ezcash_mobile', $farmer->ezcash_mobile) }}" 
                                       placeholder="e.g., 0771234567" pattern="[0-9]{10}"
                                       maxlength="10" minlength="10" inputmode="numeric">
                            </div>
                            <div id="ezcash_error" class="error-text" style="display: none;">EzCash number must be exactly 10 digits and start with 074, 076 or 077</div>
                            @error('ezcash_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- mCash Details Section -->
                    <div id="mcashDetails" class="payment-details-section" style="display: none;">
                        <h4 class="payment-section-title">
                            <i class="fas fa-mobile-alt"></i> mCash Details
                        </h4>
                        <div class="form-group">
                            <label for="mcash_mobile" class="form-label required-field">mCash Mobile Number</label>
                            <div class="input-with-icon">
                                <input type="tel" class="form-control @error('mcash_mobile') is-invalid @enderror" 
                                       id="mcash_mobile" name="mcash_mobile" 
                                       value="{{ old('mcash_mobile', $farmer->mcash_mobile) }}" 
                                       placeholder="e.g., 0771234567" pattern="[0-9]{10}"
                                       maxlength="10" minlength="10" inputmode="numeric">
                            </div>
                            <div id="mcash_error" class="error-text" style="display: none;">mCash number must be exactly 10 digits and start with 070 or 071</div>
                            @error('mcash_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('lf.manageFarmers') }}" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Farmers List
                </a>
                <button type="submit" class="btn btn-register" id="submitBtn">
                    <i class="fas fa-save"></i> Update Farmer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/gn-data.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // GN Hierarchy Logic
    const districtSelect = $('#district');
    const dsSelect = $('#divisional_secretariat');
    const gndSelect = $('#grama_niladhari_division');
    const codeInput = $('#gn_division_code');

    const savedDistrict = "{{ old('district', $farmer->district) }}";
    const savedDS = "{{ old('divisional_secretariat', $farmer->divisional_secretariat) }}";
    const savedGND = "{{ old('grama_niladhari_division', $farmer->grama_niladhari_division) }}";

    function populateDistricts() {
        if (typeof gnData !== 'undefined') {
            districtSelect.empty().append('<option value="">Select District</option>');
            Object.keys(gnData).sort().forEach(dist => {
                const selected = dist === savedDistrict ? 'selected' : '';
                districtSelect.append(`<option value="${dist}" ${selected}>${dist}</option>`);
            });
            
            if (savedDistrict) {
                populateDS(savedDistrict, savedDS);
            }
        }
    }

    function populateDS(dist, selectedDS = '') {
        dsSelect.empty().append('<option value="" disabled selected>Select DS</option>').prop('disabled', false);
        gndSelect.empty().append('<option value="" disabled selected>Select DS First</option>').prop('disabled', true);
        codeInput.empty().append('<option value="" disabled selected>Select DS First</option>').prop('disabled', true);
        
        if (gnData[dist]) {
            Object.keys(gnData[dist]).sort().forEach(ds => {
                const selected = ds === selectedDS ? 'selected' : '';
                dsSelect.append(`<option value="${ds}" ${selected}>${ds}</option>`);
            });
            
            if (selectedDS) {
                populateGND(dist, selectedDS, savedGND);
            }
        }
    }

    function populateGND(dist, ds, selectedGND = '') {
        gndSelect.empty().append('<option value="" disabled selected>Select GN Division</option>').prop('disabled', false);
        codeInput.empty().append('<option value="" disabled selected>Select GN Code</option>').prop('disabled', false);
        
        if (gnData[dist] && gnData[dist][ds]) {
            const sortedGN = gnData[dist][ds].sort((a,b) => a.name.localeCompare(b.name));
            sortedGN.forEach(gn => {
                const selected = gn.name === selectedGND ? 'selected' : '';
                gndSelect.append(`<option value="${gn.name}" data-code="${gn.code}" ${selected}>${gn.name}</option>`);
                
                const codeSelected = gn.name === selectedGND ? 'selected' : '';
                codeInput.append(`<option value="${gn.code}" data-name="${gn.name}" ${codeSelected}>${gn.code}</option>`);
            });
        }
    }

    populateDistricts();

    districtSelect.on('change', function() {
        populateDS($(this).val());
    });

    dsSelect.on('change', function() {
        populateGND(districtSelect.val(), $(this).val());
    });

    gndSelect.on('change', function() {
        const code = $(this).find('option:selected').data('code');
        codeInput.val(code);
    });

    codeInput.on('change', function() {
        const name = $(this).find('option:selected').data('name');
        gndSelect.val(name);
    });

    const nicInput = document.getElementById('nic_no');
    const nicStatus = document.getElementById('nicStatus');
    const preferredPayment = document.getElementById('preferred_payment');
    const profilePhotoInput = document.getElementById('profile_photo');
    const profilePreviewImg = document.getElementById('profilePreviewImg');
    const form = document.getElementById('farmerEditForm');

    function validateNIC(nic) {
        if (!nic) return false;
        nic = nic.trim().toUpperCase();
        const oldNicPattern = /^[0-9]{9}[VX]$/;
        const newNicPattern = /^[0-9]{12}$/;
        if (oldNicPattern.test(nic)) {
            const days = parseInt(nic.substr(2, 3));
            if (days > 500) {
                return days <= 866;
            }
            return days > 0 && days <= 366;
        }
        if (newNicPattern.test(nic)) {
            const year = parseInt(nic.substr(0, 4));
            const days = parseInt(nic.substr(4, 3));
            if (days > 500) {
                return days <= 866;
            }
            return year >= 1900 && year <= 2100 && days > 0 && days <= 366;
        }
        return false;
    }

    function formatNIC(nic) {
        if (!nic) return '';
        nic = nic.trim().toUpperCase();
        return nic;
    }

    function updateNICStatus() {
        const nicValue = nicInput.value.trim();
        if (!nicValue) {
            nicStatus.innerHTML = '';
            nicStatus.className = 'nic-status';
            return;
        }

        const formattedNIC = formatNIC(nicValue);
        if (formattedNIC !== nicValue) {
            nicInput.value = formattedNIC;
        }

        if (validateNIC(formattedNIC)) {
            nicStatus.innerHTML = '<i class="fas fa-check-circle"></i> Valid NIC format';
            nicStatus.className = 'nic-status valid';
        } else {
            nicStatus.innerHTML = '<i class="fas fa-exclamation-circle"></i> Invalid NIC format';
            nicStatus.className = 'nic-status invalid';
        }
    }

    nicInput.addEventListener('input', updateNICStatus);

    function togglePaymentDetails() {
        const value = preferredPayment.value;
        
        document.getElementById('bankDetails').style.display = 'none';
        document.getElementById('ezcashDetails').style.display = 'none';
        document.getElementById('mcashDetails').style.display = 'none';
        
        const bankFields = ['bank_name', 'bank_branch', 'account_holder_name', 'account_number'];
        const ezcashField = 'ezcash_mobile';
        const mcashField = 'mcash_mobile';
        
        bankFields.forEach(field => {
            document.getElementById(field).required = false;
        });
        document.getElementById(ezcashField).required = false;
        document.getElementById(mcashField).required = false;

        switch(value) {
            case 'bank':
                document.getElementById('bankDetails').style.display = 'block';
                bankFields.forEach(field => {
                    document.getElementById(field).required = true;
                });
                break;
            case 'ezcash':
                document.getElementById('ezcashDetails').style.display = 'block';
                document.getElementById(ezcashField).required = true;
                break;
            case 'mcash':
                document.getElementById('mcashDetails').style.display = 'block';
                document.getElementById(mcashField).required = true;
                break;
            case 'all':
                document.getElementById('bankDetails').style.display = 'block';
                document.getElementById('ezcashDetails').style.display = 'block';
                document.getElementById('mcashDetails').style.display = 'block';
                bankFields.forEach(field => {
                    document.getElementById(field).required = true;
                });
                document.getElementById(ezcashField).required = true;
                document.getElementById(mcashField).required = true;
                break;
        }
    }

    function previewProfilePhoto(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File too large',
                    text: 'Profile photo must be less than 5MB',
                    confirmButtonColor: '#10B981'
                });
                profilePhotoInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreviewImg.src = e.target.result;
                profilePreviewImg.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    window.previewProfilePhoto = previewProfilePhoto;

    function validateForm() {
        const nicValue = nicInput.value.trim();
        if (nicValue && !validateNIC(nicValue)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid NIC',
                text: 'Please enter a valid NIC number.',
                confirmButtonColor: '#10B981'
            });
            return false;
        }

        const ezcash = document.getElementById('ezcash_mobile').value.trim();
        const mcash = document.getElementById('mcash_mobile').value.trim();
        const paymentMethod = preferredPayment.value;

        if (paymentMethod === 'ezcash' || paymentMethod === 'all') {
            if (!ezcash) {
                Swal.fire({ icon: 'error', title: 'Missing EzCash', text: 'Please enter EzCash mobile number' });
                return false;
            }
            if (!/^(074|076|077)[0-9]{7}$/.test(ezcash)) {
                Swal.fire({ icon: 'error', title: 'Invalid EzCash', text: 'EzCash number must be exactly 10 digits and start with 074, 076, or 077' });
                return false;
            }
        }
        if (paymentMethod === 'mcash' || paymentMethod === 'all') {
            if (!mcash) {
                Swal.fire({ icon: 'error', title: 'Missing mCash', text: 'Please enter mCash mobile number' });
                return false;
            }
            if (!/^(070|071)[0-9]{7}$/.test(mcash)) {
                Swal.fire({ icon: 'error', title: 'Invalid mCash', text: 'mCash number must be exactly 10 digits and start with 070 or 071' });
                return false;
            }
        }

        if (paymentMethod === 'bank') {
            const bankFields = ['bank_name', 'bank_branch', 'account_holder_name', 'account_number'];
            for (const field of bankFields) {
                if (!document.getElementById(field).value.trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Information',
                        text: `Please fill in all ${field.replace('_', ' ')} fields.`,
                        confirmButtonColor: '#10B981'
                    });
                    return false;
                }
            }
        } else if (paymentMethod === 'ezcash') {
            if (!ezcash) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please enter EzCash mobile number.',
                    confirmButtonColor: '#10B981'
                });
                return false;
            }
        } else if (paymentMethod === 'mcash') {
            if (!mcash) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please enter mCash mobile number.',
                    confirmButtonColor: '#10B981'
                });
                return false;
            }
        } else if (paymentMethod === 'all') {
            const allFields = ['bank_name', 'bank_branch', 'account_holder_name', 'account_number', 'ezcash_mobile', 'mcash_mobile'];
            for (const field of allFields) {
                if (!document.getElementById(field).value.trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Information',
                        text: `Please fill in all ${field.replace('_', ' ')} fields.`,
                        confirmButtonColor: '#10B981'
                    });
                    return false;
                }
            }
        }

        return true;
    }

    document.getElementById('ezcash_mobile').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10); // Remove non-digits and limit to 10
        const val = this.value;
        if (val && (val.length !== 10 || !/^(074|076|077)/.test(val))) {
            document.getElementById('ezcash_error').style.display = 'block';
        } else {
            document.getElementById('ezcash_error').style.display = 'none';
        }
    });

    document.getElementById('mcash_mobile').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10); // Remove non-digits and limit to 10
        const val = this.value;
        if (val && (val.length !== 10 || !/^(070|071)/.test(val))) {
            document.getElementById('mcash_error').style.display = 'block';
        } else {
            document.getElementById('mcash_error').style.display = 'none';
        }
    });

    preferredPayment.addEventListener('change', togglePaymentDetails);
    
    async function submitForm(otp = null) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        const formData = new FormData(form);
        if (otp) {
            formData.append('otp', otp);
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Farmer Updated Successfully!',
                    text: data.message || 'Farmer details have been updated.',
                    confirmButtonColor: '#10B981'
                }).then(() => {
                    window.location.href = '{{ route("lf.manageFarmers") }}';
                });
            } else if (data.requires_otp) {
                // Sensitive changes detected, request OTP
                requestOtp();
            } else {
                // Validation error or other failure
                handleErrors(data);
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Operation Failed',
                text: 'An unexpected error occurred. Please try again.',
                confirmButtonColor: '#10B981'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Farmer';
        }
    }

    async function requestOtp() {
        Swal.fire({
            title: 'OTP Verification',
            text: 'Sending OTP to your mobile number...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData(form);
        try {
            const response = await fetch('{{ route("lf.farmer.sendUpdateOtp", $farmer->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await response.json();

            if (data.success) {
                const { value: otp } = await Swal.fire({
                    title: 'Enter OTP',
                    text: 'OTP has been sent to the farmer mobile number. Please enter it below to confirm changes.',
                    input: 'text',
                    inputAttributes: {
                        maxlength: 6,
                        autocapitalize: 'off',
                        autocorrect: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Verify & Update',
                    confirmButtonColor: '#10B981',
                    preConfirm: (otp) => {
                        if (!otp || otp.length !== 6 || isNaN(otp)) {
                            Swal.showValidationMessage('Please enter a valid 6-digit OTP');
                        }
                        return otp;
                    }
                });

                if (otp) {
                    submitForm(otp);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Send OTP',
                    text: data.message || 'An error occurred while sending the OTP.',
                    confirmButtonColor: '#10B981'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Could not connect to the server. Please check your connection.',
                confirmButtonColor: '#10B981'
            });
        }
    }

    function handleErrors(data) {
        let errorMessage = data.message || 'Update failed';
        if (data.errors) {
            let errorList = '<ul style="text-align: left; margin-top: 10px;">';
            for (const [field, messages] of Object.entries(data.errors)) {
                const fieldName = field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                messages.forEach(msg => {
                    errorList += `<li><strong>${fieldName}:</strong> ${msg}</li>`;
                });
            }
            errorList += '</ul>';
            Swal.fire({
                icon: 'error',
                title: 'Validation Failed',
                html: `<p>Please fix the following errors:</p>${errorList}`,
                confirmButtonColor: '#10B981'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: errorMessage,
                confirmButtonColor: '#10B981'
            });
        }
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return false;
        }

        submitForm();
    });

    // Initialize on page load
    togglePaymentDetails();
    updateNICStatus();
});
</script>
@endsection

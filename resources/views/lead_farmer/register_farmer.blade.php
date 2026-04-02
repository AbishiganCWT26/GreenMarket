@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Register Farmer')

@section('page-title', 'Register Farmer')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/farmer_registation.css') }}">
<script src="{{ asset('js/form-validation.js') }}"></script>
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
    #passwordMatch {
        font-weight: 500;
        font-size: 0.85rem;
    }
    .rule-item.valid {
        color: #10B981 !important;
    }
    .rule-item.invalid {
        color: #ef4444 !important;
    }
</style>
@endsection

@section('content')
<div class="registration-container">
    <div class="registration-card">
        <div class="card-header">
            <i class="fas fa-user-plus"></i>
            <h2>Register New Farmer</h2>
            <p class="subtitle">Add a new farmer to your group</p>
        </div>

        <form id="farmerRegistrationForm" action="{{ route('lf.storeFarmer') }}" method="POST" enctype="multipart/form-data" class="registration-form">
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
                                   id="name" name="name" value="{{ old('name') }}" 
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
                                       id="nic_no" name="nic_no" value="{{ old('nic_no') }}"
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
                        <label for="username" class="form-label required-field">
                            <i class="fas fa-at"></i> Username
                        </label>
                        <div class="input-with-icon">
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username') }}" 
                                   placeholder="Choose a username" required>
                        </div>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label required-field">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="password-container">
                            <div class="input-with-icon">
                                <i class="fas fa-key"></i>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Enter password" required>
                            </div>
                            <i class="fa-regular fa-eye password-toggle" id="password-toggle-icon" onclick="togglePasswordVisibility()"></i>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="password-strength-container">
                            <div class="strength-meta">
                                <span>Strength: <span id="strength-text">None</span></span>
                                <span id="strength-percent">0%</span>
                            </div>
                            <div class="strength-bar">
                                <div id="strength-fill" class="strength-fill"></div>
                            </div>
                        </div>

                        <div class="password-requirements">
                            <div class="requirements-title">
                                <i class="fas fa-shield-alt"></i> Security Requirements
                            </div>
                            <ul class="requirements-grid">
                                <li id="rule-length" class="rule-item invalid"><i class="fas fa-times-circle"></i> 8+ Characters</li>
                                <li id="rule-number" class="rule-item invalid"><i class="fas fa-times-circle"></i> One Number</li>
                                <li id="rule-capital" class="rule-item invalid"><i class="fas fa-times-circle"></i> Capital Letter</li>
                                <li id="rule-lowercase" class="rule-item invalid"><i class="fas fa-times-circle"></i> Lowercase Letter</li>
                                <li id="rule-special" class="rule-item invalid"><i class="fas fa-times-circle"></i> Special Character</li>
                                <li id="rule-no-space" class="rule-item invalid"><i class="fas fa-times-circle"></i> No Spaces</li>
                                <li id="rule-no-repeat" class="rule-item invalid"><i class="fas fa-times-circle"></i> No 3x Repeats</li>
                                <li id="rule-no-sequence" class="rule-item invalid"><i class="fas fa-times-circle"></i> No Sequences</li>
                                <li id="rule-not-common" class="rule-item invalid"><i class="fas fa-times-circle"></i> Not Common</li>
                                <li id="rule-no-links" class="rule-item invalid"><i class="fas fa-times-circle"></i> No Links</li>
                                <li id="rule-no-personal" class="rule-item invalid"><i class="fas fa-times-circle"></i> No Personal Info</li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label required-field">
                            <i class="fas fa-lock"></i> Confirm Password
                        </label>
                        <div class="password-container">
                            <div class="input-with-icon">
                                <i class="fas fa-key"></i>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="Confirm password" required>
                            </div>
                            <i class="fa-regular fa-eye password-toggle" id="confirm-password-toggle-icon" onclick="toggleConfirmPasswordVisibility()"></i>
                        </div>
                        <div id="passwordMatch" class="mt-3"></div>
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
                                   id="primary_mobile" name="primary_mobile" value="{{ old('primary_mobile') }}" 
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
                                   id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}" 
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
                                   id="email" name="email" value="{{ old('email') }}" 
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
                            <img src="{{ asset('assets/images/default-avatar.png') }}" alt="Profile Preview" id="profilePreviewImg">
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
                                  rows="3" placeholder="Enter complete residential address" required>{{ old('residential_address') }}</textarea>
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
                                id="divisional_secretariat" name="divisional_secretariat" required disabled>
                            <option value="">Select District First</option>
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
                                id="grama_niladhari_division" name="grama_niladhari_division" required disabled>
                            <option value="">Select DS First</option>
                        </select>
                        @error('grama_niladhari_division')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="gn_division_code" class="form-label">
                            <i class="fas fa-barcode"></i> GN Division Code
                        </label>
                        <div class="input-with-icon">
                            <input type="text" class="form-control @error('gn_division_code') is-invalid @enderror" 
                                   id="gn_division_code" name="gn_division_code" 
                                   value="{{ old('gn_division_code') }}" 
                                   placeholder="GN Division Code" readonly>
                        </div>
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
                                   value="{{ old('address_map_link') }}" 
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
                            <option value="bank" {{ old('preferred_payment') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="ezcash" {{ old('preferred_payment') == 'ezcash' ? 'selected' : '' }}>EzCash</option>
                            <option value="mcash" {{ old('preferred_payment') == 'mcash' ? 'selected' : '' }}>mCash</option>
                            <option value="all" {{ old('preferred_payment') == 'all' ? 'selected' : '' }}>All Methods</option>
                        </select>
                        @error('preferred_payment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bank Details Section (Initially hidden) -->
                    <div id="bankDetails" class="payment-details-section" style="display: none;">
                        <h4 class="payment-section-title">
                            <i class="fas fa-university"></i> Bank Account Details
                        </h4>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="bank_name" class="form-label required-field">Bank Name</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                       id="bank_name" name="bank_name" value="{{ old('bank_name') }}" 
                                       placeholder="e.g., Commercial Bank">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="bank_branch" class="form-label required-field">Bank Branch</label>
                                <input type="text" class="form-control @error('bank_branch') is-invalid @enderror" 
                                       id="bank_branch" name="bank_branch" value="{{ old('bank_branch') }}" 
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
                                       value="{{ old('account_holder_name') }}" 
                                       placeholder="Account holder's name as in bank">
                                @error('account_holder_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="account_number" class="form-label required-field">Account Number</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                       id="account_number" name="account_number" 
                                       value="{{ old('account_number') }}" 
                                       placeholder="e.g., 1234567890">
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- EzCash Details Section (Initially hidden) -->
                    <div id="ezcashDetails" class="payment-details-section" style="display: none;">
                        <h4 class="payment-section-title">
                            <i class="fas fa-mobile-alt"></i> EzCash Details
                        </h4>
                        <div class="form-group">
                            <label for="ezcash_mobile" class="form-label required-field">EzCash Mobile Number</label>
                            <div class="input-with-icon">
                                <input type="tel" class="form-control @error('ezcash_mobile') is-invalid @enderror" 
                                       id="ezcash_mobile" name="ezcash_mobile" 
                                       value="{{ old('ezcash_mobile') }}" 
                                       placeholder="e.g., 0771234567" pattern="[0-9]{10}"
                                       maxlength="10" minlength="10" inputmode="numeric">
                            </div>
                            <div id="ezcash_error" class="error-text" style="display: none;">EzCash number must start with 074, 076 or 077</div>
                            @error('ezcash_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- mCash Details Section (Initially hidden) -->
                    <div id="mcashDetails" class="payment-details-section" style="display: none;">
                        <h4 class="payment-section-title">
                            <i class="fas fa-mobile-alt"></i> mCash Details
                        </h4>
                        <div class="form-group">
                            <label for="mcash_mobile" class="form-label required-field">mCash Mobile Number</label>
                            <div class="input-with-icon">
                                <input type="tel" class="form-control @error('mcash_mobile') is-invalid @enderror" 
                                       id="mcash_mobile" name="mcash_mobile" 
                                       value="{{ old('mcash_mobile') }}" 
                                       placeholder="e.g., 0771234567" pattern="[0-9]{10}"
                                       maxlength="10" minlength="10" inputmode="numeric">
                            </div>
                            <div id="mcash_error" class="error-text" style="display: none;">mCash number must start with 070 or 071</div>
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
                    <i class="fas fa-user-plus"></i> Register Farmer
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

    // Populate Districts
    if (typeof gnData !== 'undefined') {
        Object.keys(gnData).forEach(dist => {
            districtSelect.append(`<option value="${dist}">${dist}</option>`);
        });
    }

    districtSelect.on('change', function() {
        const dist = $(this).val();
        dsSelect.empty().append('<option value="" disabled selected>Select DS</option>').prop('disabled', false);
        gndSelect.empty().append('<option value="" disabled selected>Select DS First</option>').prop('disabled', true);
        codeInput.val('');
        
        if (gnData[dist]) {
            Object.keys(gnData[dist]).forEach(ds => {
                dsSelect.append(`<option value="${ds}">${ds}</option>`);
            });
        }
    });

    dsSelect.on('change', function() {
        const dist = districtSelect.val();
        const ds = $(this).val();
        gndSelect.empty().append('<option value="" disabled selected>Select GN Division</option>').prop('disabled', false);
        codeInput.val('');

        if (gnData[dist] && gnData[dist][ds]) {
            gnData[dist][ds].forEach(gn => {
                gndSelect.append(`<option value="${gn.name}" data-code="${gn.code}">${gn.name}</option>`);
            });
        }
    });

    gndSelect.on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const code = selectedOption.data('code');
        codeInput.val(code || '');
    });

    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    const nicInput = document.getElementById('nic_no');
    const nicStatus = document.getElementById('nicStatus');
    const preferredPayment = document.getElementById('preferred_payment');
    const profilePhotoInput = document.getElementById('profile_photo');
    const profilePreviewImg = document.getElementById('profilePreviewImg');
    const form = document.getElementById('farmerRegistrationForm');

    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('password-toggle-icon');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    function toggleConfirmPasswordVisibility() {
        const confirmPasswordField = document.getElementById('password_confirmation');
        const toggleIcon = document.getElementById('confirm-password-toggle-icon');
        if (confirmPasswordField.type === 'password') {
            confirmPasswordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            confirmPasswordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    window.togglePasswordVisibility = togglePasswordVisibility;
    window.toggleConfirmPasswordVisibility = toggleConfirmPasswordVisibility;

    function validatePasswordStrength() {
        const passwordValue = password.value;
        if (!passwordValue) return false;
        
        const result = validateAdvancedPassword(passwordValue, {
            username: document.getElementById('username').value,
            email: document.getElementById('email').value
        });

        updatePasswordRuleFeedback(result);

        const strengthText = document.getElementById('strength-text');
        const strengthPercent = document.getElementById('strength-percent');
        const strengthFill = document.getElementById('strength-fill');

        if (strengthText) strengthText.innerText = result.strengthText;
        if (strengthPercent) strengthPercent.innerText = result.percent + '%';
        if (strengthFill) {
            strengthFill.style.width = result.percent + '%';
            strengthFill.style.backgroundColor = result.color;
        }

        return result.isValid;
    }

    function validatePasswordMatch() {
        return password.value === confirmPassword.value;
    }

    function validateNIC(nic) {
        if (!nic) return false;
        nic = nic.trim().toUpperCase();
        const oldNicPattern = /^[0-9]{9}[VX]$/;
        const newNicPattern = /^[0-9]{12}$/;
        if (oldNicPattern.test(nic)) {
            const year = parseInt(nic.substr(0, 2));
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
        if (nic.length === 10 && /^[0-9]{9}[VX]$/.test(nic)) {
            return nic;
        }
        if (nic.length === 12 && /^[0-9]{12}$/.test(nic)) {
            return nic;
        }
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
                    @if(file_exists(public_path('assets/icons/Gif/File Too Large1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/File Too Large1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
                @if(file_exists(public_path('assets/icons/Gif/Invalid NIC1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Invalid NIC1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
                Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Missing EzCash', text: 'Please enter EzCash mobile number' });
                return false;
            }
            if (!/^(074|076|077)[0-9]{7}$/.test(ezcash)) {
                Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Invalid EzCash', text: 'EzCash number must be exactly 10 digits and start with 074, 076, or 077' });
                return false;
            }
        }
        if (paymentMethod === 'mcash' || paymentMethod === 'all') {
            if (!mcash) {
                Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Missing mCash', text: 'Please enter mCash mobile number' });
                return false;
            }
            if (!/^(070|071)[0-9]{7}$/.test(mcash)) {
                Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Invalid mCash', text: 'mCash number must be exactly 10 digits and start with 070 or 071' });
                return false;
            }
        }

        if (!validatePasswordStrength()) {
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/error4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                title: 'Password Does Not Meet Requirements',
                html: 'Your password must satisfy all 11 security rules:<br><ul style="text-align:left;margin-top:8px;"><li>At least 8 characters</li><li>One number, one capital, one lowercase, one special character</li><li>No spaces, no 3× repeated chars, no sequences</li><li>Not a common password, no URLs, no personal info</li></ul>',
                confirmButtonColor: '#10B981'
            });
            return false;
        }

        if (!validatePasswordMatch()) {
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/error4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                title: 'Passwords Mismatch',
                text: 'Password and confirmation do not match.',
                confirmButtonColor: '#10B981'
            });
            return false;
        }

        if (paymentMethod === 'bank') {
            const bankFields = ['bank_name', 'bank_branch', 'account_holder_name', 'account_number'];
            for (const field of bankFields) {
                if (!document.getElementById(field).value.trim()) {
                    Swal.fire({
                        @if(file_exists(public_path('assets/icons/Gif/Search File1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Search File1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
                    @if(file_exists(public_path('assets/icons/Gif/Search File1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Search File1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                    title: 'Missing Information',
                    text: 'Please enter EzCash mobile number.',
                    confirmButtonColor: '#10B981'
                });
                return false;
            }
        } else if (paymentMethod === 'mcash') {
            if (!mcash) {
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/Search File1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Search File1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
                        @if(file_exists(public_path('assets/icons/Gif/Search File1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Search File1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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

    nicInput.addEventListener('input', updateNICStatus);
    password.addEventListener('input', validatePasswordStrength);
    preferredPayment.addEventListener('change', togglePaymentDetails);

    confirmPassword.addEventListener('input', function() {
        const matchDiv = document.getElementById('passwordMatch');
        if (!matchDiv) return;
        if (!confirmPassword.value) {
            matchDiv.innerHTML = '';
            return;
        }
        if (password.value === confirmPassword.value) {
            matchDiv.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Passwords match</span>';
        } else {
            matchDiv.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Passwords do not match</span>';
        }
    });

    password.addEventListener('input', function() {
        if (confirmPassword.value) {
            confirmPassword.dispatchEvent(new Event('input'));
        }
    });
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Auto-fill WhatsApp number if empty
        const primaryMobile = document.getElementById('primary_mobile').value;
        const whatsappNumberField = document.getElementById('whatsapp_number');
        if (!whatsappNumberField.value.trim()) {
            whatsappNumberField.value = primaryMobile;
        }
        
        if (!validateForm()) {
            return false;
        }

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';

        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                if (response.status === 422) {
                    let errorMessage = 'Validation failed:<br><ul style="text-align: left;">';
                    Object.values(data.errors).forEach(err => {
                        errorMessage += `<li>${err[0]}</li>`;
                    });
                    errorMessage += '</ul>';
                    throw new Error(errorMessage);
                }
                throw new Error(data.message || 'Registration failed');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/success4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                    title: 'Farmer Registered Successfully!',
                    html: `
                        <div style="text-align: left; padding: 10px;">
                            <p><strong>Name:</strong> ${data.farmer.name}</p>
                            <p><strong>Username:</strong> ${data.username}</p>
                            <p><strong>Mobile:</strong> ${data.farmer.primary_mobile}</p>
                            <p>SMS and email sent with login details.</p>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Add New Farmer',
                    cancelButtonText: 'View Farmer List',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    } else {
                        window.location.href = '{{ route("lf.manageFarmers") }}';
                    }
                });
            } else {
                throw new Error(data.message || 'Registration failed');
            }
        })
        .catch(error => {
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/error6.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error6.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                title: 'Registration Failed',
                text: error.message || 'An error occurred while registering the farmer.',
                confirmButtonColor: '#10B981'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Register Farmer';
        });
    });

    togglePaymentDetails();
    updateNICStatus();
});
</script>
@endsection
@extends('farmer.layouts.farmer_master')

@section('title', 'Payment Settings')
@section('page-title', 'Payment Settings')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/farmer/payment.css') }}">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="payment-container">
    <div class="payment-header">
        <div class="header-icon">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="header-text">
            <h1>Payment Preferences</h1>
            <p>Set your preferred payment method for receiving payments</p>
        </div>
    </div>

    <div class="payment-content">
        <div class="payment-info">
            <i class="fas fa-info-circle"></i>
            <div>
                <h3>Payment Setup</h3>
                <p>Configure your payment methods to receive payments from buyers. Choose your preferred method and provide the necessary details.</p>
            </div>
        </div>

        <form id="paymentForm" class="payment-form">
            @csrf
            <input type="hidden" name="action" value="update_payment">

            <div class="method-section">
                <h2><i class="fas fa-credit-card"></i> Select Payment Method</h2>

                <div class="method-grid">
                    <div class="method-option @if(($farmer->preferred_payment ?? 'bank') == 'bank') selected @endif" data-value="bank">
                        <div class="method-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="method-details">
                            <h3>Bank Transfer</h3>
                            <p>Receive payments directly to your bank account</p>
                        </div>
                        <div class="method-check">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>

                    <div class="method-option @if(($farmer->preferred_payment ?? 'bank') == 'ezcash') selected @endif" data-value="ezcash">
                        <div class="method-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="method-details">
                            <h3>eZ Cash</h3>
                            <p>Mobile wallet payments via Dialog eZ Cash</p>
                        </div>
                        <div class="method-check">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>

                    <div class="method-option @if(($farmer->preferred_payment ?? 'bank') == 'mcash') selected @endif" data-value="mcash">
                        <div class="method-icon">
                            <i class="fas fa-sim-card"></i>
                        </div>
                        <div class="method-details">
                            <h3>mCash</h3>
                            <p>Mobile payments via Mobitel mCash</p>
                        </div>
                        <div class="method-check">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>

                    <div class="method-option @if(($farmer->preferred_payment ?? 'bank') == 'all') selected @endif" data-value="all">
                        <div class="method-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="method-details">
                            <h3>All Methods</h3>
                            <p>Accept payments through all available methods</p>
                        </div>
                        <div class="method-check">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="preferred_payment" name="preferred_payment" value="{{ $farmer->preferred_payment ?? 'bank' }}">
            </div>

            <div class="details-section">
                <div class="bank-details payment-details @if(($farmer->preferred_payment ?? 'bank') == 'bank') active @endif" id="bank-details">
                    <div class="details-header">
                        <i class="fas fa-university"></i>
                        <h3>Bank Account Information</h3>
                    </div>
                    <p class="details-subtitle">Provide your bank account details for direct transfers</p>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="account_holder_name">
                                <i class="fas fa-user"></i>
                                Account Holder Name
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="account_holder_name" name="account_holder_name"
                                   value="{{ $farmer->account_holder_name ?? '' }}"
                                   placeholder="Enter account holder name as in bank records">
                        </div>

                        <div class="form-group">
                            <label for="account_number">
                                <i class="fas fa-hashtag"></i>
                                Account Number
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="account_number" name="account_number"
                                   value="{{ $farmer->account_number ?? '' }}"
                                   placeholder="Enter your bank account number">
                            <div class="form-note">Usually 10-12 digits</div>
                        </div>

                        <div class="form-group">
                            <label for="bank_name">
                                <i class="fas fa-landmark"></i>
                                Bank Name
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="bank_name" name="bank_name"
                                   value="{{ $farmer->bank_name ?? '' }}"
                                   placeholder="e.g., Bank of Ceylon, People's Bank">
                        </div>

                        <div class="form-group">
                            <label for="bank_branch">
                                <i class="fas fa-map-marker-alt"></i>
                                Bank Branch
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="bank_branch" name="bank_branch"
                                   value="{{ $farmer->bank_branch ?? '' }}"
                                   placeholder="e.g., Colombo Main, Kandy City">
                        </div>
                    </div>
                </div>

                <div class="ezcash-details payment-details @if(($farmer->preferred_payment ?? 'bank') == 'ezcash') active @endif" id="ezcash-details">
                    <div class="details-header">
                        <i class="fas fa-mobile-alt"></i>
                        <h3>eZ Cash Details</h3>
                    </div>
                    <p class="details-subtitle">Provide your Dialog eZ Cash mobile number</p>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="ezcash_mobile">
                                <i class="fas fa-mobile-alt"></i>
                                eZ Cash Mobile Number
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="ezcash_mobile" name="ezcash_mobile"
                                   value="{{ $farmer->ezcash_mobile ?? '' }}"
                                   placeholder="Enter your Dialog mobile number">
                            <div class="form-note">Format: 07XXXXXXXX</div>
                        </div>
                    </div>
                </div>

                <div class="mcash-details payment-details @if(($farmer->preferred_payment ?? 'bank') == 'mcash') active @endif" id="mcash-details">
                    <div class="details-header">
                        <i class="fas fa-sim-card"></i>
                        <h3>mCash Details</h3>
                    </div>
                    <p class="details-subtitle">Provide your Mobitel mCash mobile number</p>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="mcash_mobile">
                                <i class="fas fa-sim-card"></i>
                                mCash Mobile Number
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="mcash_mobile" name="mcash_mobile"
                                   value="{{ $farmer->mcash_mobile ?? '' }}"
                                   placeholder="Enter your Mobitel mobile number">
                            <div class="form-note">Format: 07XXXXXXXX</div>
                        </div>
                    </div>
                </div>

                <div class="all-details payment-details @if(($farmer->preferred_payment ?? 'bank') == 'all') active @endif" id="all-details">
                    <div class="details-header">
                        <i class="fas fa-exchange-alt"></i>
                        <h3>All Payment Methods</h3>
                    </div>
                    <p class="details-subtitle">Provide details for all payment methods</p>

                    <div class="method-group">
                        <h4><i class="fas fa-university"></i> Bank Account Information</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="all_account_holder_name">Account Holder Name <span class="required">*</span></label>
                                <input type="text" id="all_account_holder_name" name="all_account_holder_name"
                                       value="{{ $farmer->account_holder_name ?? '' }}"
                                       placeholder="Enter account holder name">
                            </div>
                            <div class="form-group">
                                <label for="all_account_number">Account Number <span class="required">*</span></label>
                                <input type="text" id="all_account_number" name="all_account_number"
                                       value="{{ $farmer->account_number ?? '' }}"
                                       placeholder="Enter account number">
                            </div>
                            <div class="form-group">
                                <label for="all_bank_name">Bank Name <span class="required">*</span></label>
                                <input type="text" id="all_bank_name" name="all_bank_name"
                                       value="{{ $farmer->bank_name ?? '' }}"
                                       placeholder="Enter bank name">
                            </div>
                            <div class="form-group">
                                <label for="all_bank_branch">Bank Branch <span class="required">*</span></label>
                                <input type="text" id="all_bank_branch" name="all_bank_branch"
                                       value="{{ $farmer->bank_branch ?? '' }}"
                                       placeholder="Enter bank branch">
                            </div>
                        </div>
                    </div>

                    <div class="method-group">
                        <h4><i class="fas fa-mobile-alt"></i> Mobile Payment Details</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="all_ezcash_mobile">eZ Cash Mobile Number <span class="required">*</span></label>
                                <input type="text" id="all_ezcash_mobile" name="all_ezcash_mobile"
                                       value="{{ $farmer->ezcash_mobile ?? '' }}"
                                       placeholder="Enter Dialog mobile number">
                            </div>
                            <div class="form-group">
                                <label for="all_mcash_mobile">mCash Mobile Number <span class="required">*</span></label>
                                <input type="text" id="all_mcash_mobile" name="all_mcash_mobile"
                                       value="{{ $farmer->mcash_mobile ?? '' }}"
                                       placeholder="Enter Mobitel mobile number">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="additional-section">
                <h3><i class="fas fa-edit"></i> Additional Information</h3>
                <div class="form-group">
                    <label for="payment_details">
                        <i class="fas fa-info-circle"></i>
                        Additional Notes
                    </label>
                    <textarea id="payment_details" name="payment_details" rows="3"
                              placeholder="Any additional payment instructions or notes">{{ old('payment_details', $farmer->payment_details ?? '') }}</textarea>
                    <div class="form-note">Optional: Add any special instructions or additional payment information</div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button type="button" class="btn-save" id="savePaymentBtn">
                    <i class="fas fa-save"></i>
                    Save Payment Settings
                    <div class="save-effect"></div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const methodOptions = document.querySelectorAll('.method-option');
        const paymentInput = document.getElementById('preferred_payment');
        const paymentDetails = document.querySelectorAll('.payment-details');

        function showPaymentDetails(method) {
            paymentDetails.forEach(section => {
                section.classList.remove('active');
            });

            const selectedSection = document.getElementById(`${method}-details`);
            if (selectedSection) {
                selectedSection.classList.add('active');
            }
        }

        const initialMethod = paymentInput.value;
        showPaymentDetails(initialMethod);

        methodOptions.forEach(option => {
            const value = option.getAttribute('data-value');
            option.addEventListener('click', () => {
                methodOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                paymentInput.value = value;
                showPaymentDetails(value);
            });
        });

        const savePaymentBtn = document.getElementById('savePaymentBtn');
        if (savePaymentBtn) {
            savePaymentBtn.addEventListener('click', async function() {
                const { value: currentPassword } = await Swal.fire({
                    title: 'Verify Your Identity',
                    text: 'Please enter your current password to update payment settings',
                    input: 'password',
                    inputAttributes: {
                        autocapitalize: 'off',
                        autocorrect: 'off',
                        placeholder: 'Enter current password',
                        required: 'required'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Verify',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6c757d',
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),
                    preConfirm: (password) => {
                        if (!password) {
                            Swal.showValidationMessage('Please enter your password');
                            return false;
                        }
                        return password;
                    }
                });

                if (currentPassword === undefined) {
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'update_payment');
                formData.append('preferred_payment', paymentInput.value);
                formData.append('payment_details', document.getElementById('payment_details').value);
                formData.append('current_password', currentPassword);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                const selectedMethod = paymentInput.value;
                let validationPassed = true;
                let errorFields = [];

                if (selectedMethod === 'bank') {
                    const fields = [
                        { id: 'account_holder_name', name: 'account_holder_name' },
                        { id: 'account_number', name: 'account_number' },
                        { id: 'bank_name', name: 'bank_name' },
                        { id: 'bank_branch', name: 'bank_branch' }
                    ];

                    fields.forEach(field => {
                        const element = document.getElementById(field.id);
                        if (element) {
                            formData.append(field.name, element.value);
                            if (!element.value.trim()) {
                                validationPassed = false;
                                element.classList.add('is-invalid');
                                errorFields.push(field.name.replace('_', ' '));
                            } else {
                                element.classList.remove('is-invalid');
                            }
                        }
                    });
                }
                else if (selectedMethod === 'ezcash') {
                    const element = document.getElementById('ezcash_mobile');
                    formData.append('ezcash_mobile', element.value);
                    if (!element.value.trim()) {
                        validationPassed = false;
                        element.classList.add('is-invalid');
                        errorFields.push('ezcash mobile number');
                    } else {
                        element.classList.remove('is-invalid');
                    }
                }
                else if (selectedMethod === 'mcash') {
                    const element = document.getElementById('mcash_mobile');
                    formData.append('mcash_mobile', element.value);
                    if (!element.value.trim()) {
                        validationPassed = false;
                        element.classList.add('is-invalid');
                        errorFields.push('mcash mobile number');
                    } else {
                        element.classList.remove('is-invalid');
                    }
                }
                else if (selectedMethod === 'all') {
                    const fields = [
                        { id: 'all_account_holder_name', name: 'account_holder_name' },
                        { id: 'all_account_number', name: 'account_number' },
                        { id: 'all_bank_name', name: 'bank_name' },
                        { id: 'all_bank_branch', name: 'bank_branch' },
                        { id: 'all_ezcash_mobile', name: 'ezcash_mobile' },
                        { id: 'all_mcash_mobile', name: 'mcash_mobile' }
                    ];

                    fields.forEach(field => {
                        const element = document.getElementById(field.id);
                        if (element) {
                            formData.append(field.name, element.value);
                            if (!element.value.trim()) {
                                validationPassed = false;
                                element.classList.add('is-invalid');
                                errorFields.push(field.name.replace('_', ' '));
                            } else {
                                element.classList.remove('is-invalid');
                            }
                        }
                    });
                }

                if (!validationPassed) {
                    showErrorToast('Please fill in all required fields: ' + errorFields.join(', '));
                    return;
                }

                const submitBtn = this;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Saving...</span>';
                submitBtn.disabled = true;

                try {
                    const response = await fetch('{{ route("farmer.profile.settings.update-payment") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        const text = await response.text();
                        console.error('Non-JSON response:', text.substring(0, 200));
                        throw new Error('Server returned an unexpected response. Please try again.');
                    }

                    const data = await response.json();

                    if (!response.ok) {
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat().join(', ');
                            throw new Error(errorMessages);
                        }
                        throw new Error(data.message || `Server error: ${response.status}`);
                    }

                    if (data.success) {
                        showSuccessToast(data.message || 'Payment settings updated successfully!');
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Failed to update settings');
                    }
                } catch (error) {
                    console.error('Update error:', error);
                    showErrorToast(error.message || 'Failed to update payment settings. Please try again.');
                } finally {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        }

        const btnCancel = document.querySelector('.btn-cancel');
        if (btnCancel) {
            btnCancel.addEventListener('click', () => {
                window.history.back();
            });
        }

        const inputs = document.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = 'var(--primary-green)';
                this.style.boxShadow = '0 0 0 3px var(--focus-shadow)';
            });

            input.addEventListener('blur', function() {
                this.style.borderColor = '';
                this.style.boxShadow = '';
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                }
            });
        });
    });

    function showSuccessToast(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: 'linear-gradient(135deg, #10B981 0%, #059669 100%)',
            color: 'white',
            iconColor: 'white',
            customClass: {
                popup: 'sweetalert-success'
            }
        });
    }

    function showErrorToast(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            background: '#ef4444',
            color: 'white',
            iconColor: 'white',
            customClass: {
                popup: 'sweetalert-error'
            }
        });
    }
</script>
@endsection

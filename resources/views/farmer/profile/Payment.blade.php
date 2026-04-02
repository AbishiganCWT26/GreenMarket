@extends('farmer.layouts.farmer_master')

@section('title', 'Payment Settings')
@section('page-title', 'Payment Settings')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/farmer/Payment.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="payment-wrap">
        <div class="payment-container">
            <div class="payment-card">
                <div class="payment-head">
                    <div class="payment-head-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="payment-head-text">
                        <h1>Payment Settings</h1>
                        <p>Manage your payment preferences</p>
                    </div>
                </div>

                <form id="paymentForm" class="payment-form">
                    @csrf
                    <input type="hidden" name="action" value="update_payment">
                    <input type="hidden" id="preferred_payment" name="preferred_payment"
                        value="{{ $farmer->preferred_payment ?? 'bank' }}">

                    <div class="method-wrap">
                        <div class="method-title">
                            <i class="fas fa-credit-card"></i>
                            <span>Select Payment Method</span>
                        </div>
                        <div class="method-list">
                            <div class="method-item @if(($farmer->preferred_payment ?? 'bank') == 'bank') active @endif"
                                data-method="bank">
                                <div class="method-item-icon bank">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="method-item-info">
                                    <h4>Bank Transfer</h4>
                                    <p>Direct bank deposit</p>
                                </div>
                                <div class="method-item-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="method-item @if(($farmer->preferred_payment ?? 'bank') == 'ezcash') active @endif"
                                data-method="ezcash">
                                <div class="method-item-icon ezcash">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="method-item-info">
                                    <h4>eZ Cash</h4>
                                    <p>Dialog mobile wallet</p>
                                </div>
                                <div class="method-item-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="method-item @if(($farmer->preferred_payment ?? 'bank') == 'mcash') active @endif"
                                data-method="mcash">
                                <div class="method-item-icon mcash">
                                    <i class="fas fa-sim-card"></i>
                                </div>
                                <div class="method-item-info">
                                    <h4>mCash</h4>
                                    <p>Mobitel mobile wallet</p>
                                </div>
                                <div class="method-item-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="method-item @if(($farmer->preferred_payment ?? 'bank') == 'all') active @endif"
                                data-method="all">
                                <div class="method-item-icon all">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <div class="method-item-info">
                                    <h4>All Methods</h4>
                                    <p>Accept all payments</p>
                                </div>
                                <div class="method-item-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-wrap">
                        <div class="detail-panel bank-panel" id="bankPanel">
                            <div class="detail-head">
                                <i class="fas fa-university"></i>
                                <h3>Bank Details</h3>
                            </div>
                            <div class="detail-body">
                                <div class="form-field">
                                    <label><i class="fas fa-user"></i> Account Holder Name <span
                                            class="required">*</span></label>
                                    <input type="text" id="account_holder_name" name="account_holder_name"
                                        value="{{ $farmer->account_holder_name ?? '' }}" placeholder="Full name as in bank">
                                </div>
                                <div class="form-field">
                                    <label><i class="fas fa-hashtag"></i> Account Number <span
                                            class="required">*</span></label>
                                    <input type="text" id="account_number" name="account_number"
                                        value="{{ $farmer->account_number ?? '' }}" placeholder="Bank account number">
                                    <small>Usually 10-12 digits</small>
                                </div>
                                <div class="form-field">
                                    <label><i class="fas fa-landmark"></i> Bank Name <span class="required">*</span></label>
                                    <input type="text" id="bank_name" name="bank_name"
                                        value="{{ $farmer->bank_name ?? '' }}" placeholder="e.g., Bank of Ceylon">
                                </div>
                                <div class="form-field">
                                    <label><i class="fas fa-map-marker-alt"></i> Bank Branch <span
                                            class="required">*</span></label>
                                    <input type="text" id="bank_branch" name="bank_branch"
                                        value="{{ $farmer->bank_branch ?? '' }}" placeholder="Branch location">
                                </div>
                            </div>
                        </div>

                        <div class="detail-panel ezcash-panel" id="ezcashPanel">
                            <div class="detail-head">
                                <i class="fas fa-mobile-alt"></i>
                                <h3>eZ Cash Details</h3>
                            </div>
                            <div class="detail-body">
                                <div class="form-field">
                                    <label><i class="fas fa-mobile-alt"></i> eZ Cash Number <span
                                            class="required">*</span></label>
                                    <input type="text" id="ezcash_mobile" name="ezcash_mobile"
                                        value="{{ $farmer->ezcash_mobile ?? '' }}" placeholder="07XXXXXXXX" maxlength="10">
                                    <small>Dialog number (074, 076, 077)</small>
                                </div>
                            </div>
                        </div>

                        <div class="detail-panel mcash-panel" id="mcashPanel">
                            <div class="detail-head">
                                <i class="fas fa-sim-card"></i>
                                <h3>mCash Details</h3>
                            </div>
                            <div class="detail-body">
                                <div class="form-field">
                                    <label><i class="fas fa-sim-card"></i> mCash Number <span
                                            class="required">*</span></label>
                                    <input type="text" id="mcash_mobile" name="mcash_mobile"
                                        value="{{ $farmer->mcash_mobile ?? '' }}" placeholder="07XXXXXXXX" maxlength="10">
                                    <small>Mobitel number (070, 071)</small>
                                </div>
                            </div>
                        </div>

                        <div class="detail-panel all-panel" id="allPanel">
                            <div class="detail-head">
                                <i class="fas fa-exchange-alt"></i>
                                <h3>All Payment Methods</h3>
                            </div>
                            <div class="detail-body">
                                <div class="sub-section">
                                    <h4><i class="fas fa-university"></i> Bank Details</h4>
                                    <div class="form-row">
                                        <div class="form-field">
                                            <label>Account Holder Name <span class="required">*</span></label>
                                            <input type="text" id="all_account_holder_name" name="all_account_holder_name"
                                                value="{{ $farmer->account_holder_name ?? '' }}">
                                        </div>
                                        <div class="form-field">
                                            <label>Account Number <span class="required">*</span></label>
                                            <input type="text" id="all_account_number" name="all_account_number"
                                                value="{{ $farmer->account_number ?? '' }}">
                                        </div>
                                        <div class="form-field">
                                            <label>Bank Name <span class="required">*</span></label>
                                            <input type="text" id="all_bank_name" name="all_bank_name"
                                                value="{{ $farmer->bank_name ?? '' }}">
                                        </div>
                                        <div class="form-field">
                                            <label>Bank Branch <span class="required">*</span></label>
                                            <input type="text" id="all_bank_branch" name="all_bank_branch"
                                                value="{{ $farmer->bank_branch ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="sub-section">
                                    <h4><i class="fas fa-mobile-alt"></i> Mobile Wallet Details</h4>
                                    <div class="form-row">
                                        <div class="form-field">
                                            <label>eZ Cash Number <span class="required">*</span></label>
                                            <input type="text" id="all_ezcash_mobile" name="all_ezcash_mobile"
                                                value="{{ $farmer->ezcash_mobile ?? '' }}" placeholder="07XXXXXXXX"
                                                maxlength="10">
                                            <small>Dialog number (074, 076, 077)</small>
                                        </div>
                                        <div class="form-field">
                                            <label>mCash Number <span class="required">*</span></label>
                                            <input type="text" id="all_mcash_mobile" name="all_mcash_mobile"
                                                value="{{ $farmer->mcash_mobile ?? '' }}" placeholder="07XXXXXXXX"
                                                maxlength="10">
                                            <small>Mobitel number (070, 071)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="extra-wrap">
                        <div class="extra-head">
                            <i class="fas fa-edit"></i>
                            <span>Additional Notes</span>
                        </div>
                        <div class="extra-body">
                            <textarea id="payment_details" name="payment_details" rows="3"
                                placeholder="Optional payment instructions or notes...">{{ old('payment_details', $farmer->payment_details ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="action-wrap">
                        <button type="button" class="btn-cancel" onclick="window.history.back()">
                            <i class="fas fa-times"></i>
                            <span>Cancel</span>
                        </button>
                        <button type="button" class="btn-save" id="savePaymentBtn">
                            <i class="fas fa-save"></i>
                            <span>Save Settings</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const methodItems = document.querySelectorAll('.method-item');
            const preferredPayment = document.getElementById('preferred_payment');
            const panels = document.querySelectorAll('.detail-panel');

            function showPanel(method) {
                panels.forEach(panel => {
                    panel.classList.remove('active');
                });
                const activePanel = document.getElementById(`${method}Panel`);
                if (activePanel) {
                    activePanel.classList.add('active');
                }
            }

            function updateMethodSelection(method) {
                methodItems.forEach(item => {
                    if (item.dataset.method === method) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
                preferredPayment.value = method;
                showPanel(method);
            }

            methodItems.forEach(item => {
                item.addEventListener('click', function () {
                    const method = this.dataset.method;
                    updateMethodSelection(method);
                });
            });

            const initialMethod = preferredPayment.value;
            updateMethodSelection(initialMethod);

            function validateMobile(value, type) {
                const digits = value.replace(/\D/g, '');
                if (digits.length !== 10) return false;
                if (type === 'ezcash') {
                    return /^07[467]/.test(digits);
                }
                if (type === 'mcash') {
                    return /^07[01]/.test(digits);
                }
                return true;
            }

            ['ezcash_mobile', 'mcash_mobile', 'all_ezcash_mobile', 'all_mcash_mobile'].forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('input', function () {
                        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
                        if (this.classList.contains('error')) {
                            this.classList.remove('error');
                        }
                    });
                }
            });

            document.querySelectorAll('input, textarea').forEach(input => {
                input.addEventListener('focus', function () {
                    this.style.borderColor = 'var(--primary-green)';
                });
                input.addEventListener('blur', function () {
                    this.style.borderColor = '';
                });
            });

            const saveBtn = document.getElementById('savePaymentBtn');

            saveBtn.addEventListener('click', async function () {
                const selectedMethod = preferredPayment.value;
                let isValid = true;
                let errorMsg = '';

                // 1. Validation Logic First
                if (selectedMethod === 'bank') {
                    const fields = ['account_holder_name', 'account_number', 'bank_name', 'bank_branch'];
                    fields.forEach(field => {
                        const input = document.getElementById(field);
                        if (input) {
                            if (!input.value.trim()) {
                                isValid = false;
                                input.classList.add('error');
                                errorMsg = 'Please fill all bank details';
                            } else {
                                input.classList.remove('error');
                            }
                        }
                    });
                } else if (selectedMethod === 'ezcash') {
                    const input = document.getElementById('ezcash_mobile');
                    const val = input.value.trim();
                    if (!val) {
                        isValid = false;
                        input.classList.add('error');
                        errorMsg = 'eZ Cash number required';
                    } else if (!validateMobile(val, 'ezcash')) {
                        isValid = false;
                        input.classList.add('error');
                        errorMsg = 'Invalid eZ Cash number (10 digits, 074/076/077)';
                    } else {
                        input.classList.remove('error');
                    }
                } else if (selectedMethod === 'mcash') {
                    const input = document.getElementById('mcash_mobile');
                    const val = input.value.trim();
                    if (!val) {
                        isValid = false;
                        input.classList.add('error');
                        errorMsg = 'mCash number required';
                    } else if (!validateMobile(val, 'mcash')) {
                        isValid = false;
                        input.classList.add('error');
                        errorMsg = 'Invalid mCash number (10 digits, 070/071)';
                    } else {
                        input.classList.remove('error');
                    }
                } else if (selectedMethod === 'all') {
                    const bankFields = ['all_account_holder_name', 'all_account_number', 'all_bank_name', 'all_bank_branch'];
                    bankFields.forEach(id => {
                        const input = document.getElementById(id);
                        const val = input.value.trim();
                        if (!val) {
                            isValid = false;
                            input.classList.add('error');
                            errorMsg = 'Please fill all bank details';
                        } else {
                            input.classList.remove('error');
                        }
                    });

                    const ezcashInput = document.getElementById('all_ezcash_mobile');
                    const ezcashVal = ezcashInput.value.trim();
                    if (!ezcashVal) {
                        isValid = false;
                        ezcashInput.classList.add('error');
                        errorMsg = 'eZ Cash number required';
                    } else if (!validateMobile(ezcashVal, 'ezcash')) {
                        isValid = false;
                        ezcashInput.classList.add('error');
                        errorMsg = 'Invalid eZ Cash number';
                    } else {
                        ezcashInput.classList.remove('error');
                    }

                    const mcashInput = document.getElementById('all_mcash_mobile');
                    const mcashVal = mcashInput.value.trim();
                    if (!mcashVal) {
                        isValid = false;
                        mcashInput.classList.add('error');
                        errorMsg = 'mCash number required';
                    } else if (!validateMobile(mcashVal, 'mcash')) {
                        isValid = false;
                        mcashInput.classList.add('error');
                        errorMsg = 'Invalid mCash number';
                    } else {
                        mcashInput.classList.remove('error');
                    }
                }

                if (!isValid) {
                    Swal.fire({
                        @if(file_exists(public_path('assets/icons/Gif/Validation Error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Validation Error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                        title: 'Validation Error',
                        text: errorMsg,
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                // 2. Identity Verification Second
                const { value: password } = await Swal.fire({
                    title: 'Verify Identity',
                    text: 'Enter your password to confirm changes',
                    html: `
                            <div class="password-container">
                                <input type="password" id="password-field" placeholder="Current password">
                                <i class="fa-regular fa-eye password-toggle" id="password-toggle-icon" onclick="togglePasswordVisibility()"></i>
                            </div>
                        `,
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    confirmButtonColor: '#10B981',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const pass = Swal.getPopup().querySelector('#password-field').value;
                        if (!pass) {
                            Swal.showValidationMessage('Password required');
                            return false;
                        }
                        return pass;
                    }
                });

                if (!password) return;

                // 3. Prepare Form Data and Submit
                const formData = new FormData();
                formData.append('action', 'update_payment');
                formData.append('preferred_payment', preferredPayment.value);
                formData.append('payment_details', document.getElementById('payment_details').value);
                formData.append('current_password', password);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                if (selectedMethod === 'bank') {
                    ['account_holder_name', 'account_number', 'bank_name', 'bank_branch'].forEach(field => {
                        formData.append(field, document.getElementById(field).value);
                    });
                } else if (selectedMethod === 'ezcash') {
                    formData.append('ezcash_mobile', document.getElementById('ezcash_mobile').value.trim());
                } else if (selectedMethod === 'mcash') {
                    formData.append('mcash_mobile', document.getElementById('mcash_mobile').value.trim());
                } else if (selectedMethod === 'all') {
                    const bankFields = [
                        { id: 'all_account_holder_name', name: 'account_holder_name' },
                        { id: 'all_account_number', name: 'account_number' },
                        { id: 'all_bank_name', name: 'bank_name' },
                        { id: 'all_bank_branch', name: 'bank_branch' }
                    ];
                    bankFields.forEach(field => {
                        formData.append(field.name, document.getElementById(field.id).value.trim());
                    });
                    formData.append('ezcash_mobile', document.getElementById('all_ezcash_mobile').value.trim());
                    formData.append('mcash_mobile', document.getElementById('all_mcash_mobile').value.trim());
                }


                const btn = this;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Saving...</span>';
                btn.disabled = true;

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

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            @if(file_exists(public_path('assets/icons/Gif/success4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                            title: 'Success!',
                            text: data.message || 'Payment settings updated',
                            confirmButtonColor: '#10B981',
                            timer: 2000,
                            showConfirmButton: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Update failed');
                    }
                } catch (error) {
                    Swal.fire({
                        @if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                        title: 'Error!',
                        text: error.message,
                        confirmButtonColor: '#ef4444'
                    });
                } finally {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            });
        });
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password-field');
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

    </script>

@endsection

@extends('buyer.layouts.buyer_master')

@section('title', 'Request Product')

@section('page-title', 'Request Product')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
:root {
    --primary-green: #10B981;
    --dark-green: #059669;
    --body-bg: #f6f8fa;
    --card-bg: #ffffff;
    --text-color: #0f1724;
    --muted: #6b7280;
    --accent-amber: #f59e0b;
    --blue: #3b82f6;
    --purple: #8b5cf6;
    --yellow: #f59e0b;
    --shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
    --shadow-md: 0 7px 15px rgba(15,23,36,0.08);
}

.product-request-wrapper {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.request-header {
    margin-bottom: 30px;
    text-align: center;
}

.request-header h2 {
    color: var(--text-color);
    font-size: 2.5rem;
    margin-bottom: 10px;
    font-weight: 700;
}

.request-header p {
    color: var(--muted);
    font-size: 1.1rem;
}

.request-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 40px;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(15,23,36,0.05);
    transition: all 0.3s ease;
}

.request-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(15,23,36,0.12);
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--text-color);
    font-weight: 600;
    font-size: 1rem;
}

.form-control {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-control-file {
    padding: 12px;
    border: 2px dashed #d1d5db;
    border-radius: 10px;
    width: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-control-file:hover {
    border-color: var(--primary-green);
    background: rgba(16, 185, 129, 0.02);
}

.form-control-file:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.file-input-wrapper {
    position: relative;
}

.file-preview {
    margin-top: 15px;
    display: none;
}

.file-preview img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid #e5e7eb;
}

.btn-submit {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
    border: none;
    padding: 16px 32px;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
}

.btn-submit:active {
    transform: translateY(0);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.required {
    color: #ef4444;
}

.form-hint {
    font-size: 0.875rem;
    color: var(--muted);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.quantity-unit {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 15px;
}

.price-info {
    background: rgba(16, 185, 129, 0.05);
    padding: 15px;
    border-radius: 10px;
    border-left: 4px solid var(--primary-green);
}

.price-info p {
    margin: 0;
    color: var(--muted);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.loading {
    display: none;
    text-align: center;
    margin: 20px 0;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e7eb;
    border-top: 4px solid var(--primary-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.unit-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
}

.unit-value {
    font-weight: 600;
    color: var(--text-color);
}

.unit-description {
    font-size: 0.875rem;
    color: var(--muted);
}

@media (max-width: 992px) {
    .product-request-wrapper {
        padding: 15px;
    }

    .request-card {
        padding: 30px;
    }

    .request-header h2 {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .request-card {
        padding: 20px;
    }

    .quantity-unit {
        grid-template-columns: 1fr;
    }

    .btn-submit {
        padding: 14px 24px;
    }
}

@media (max-width: 480px) {
    .product-request-wrapper {
        padding: 10px;
    }

    .request-header h2 {
        font-size: 1.75rem;
    }

    .request-card {
        padding: 15px;
    }

    .form-control {
        padding: 12px;
    }
}
</style>
@endsection

@section('content')
<div class="product-request-wrapper">
    <div class="request-header">
        <h2>Request a Product</h2>
        <p>Tell us what product you need and we'll notify farmers</p>
    </div>

    <div class="request-card">
        <form id="productRequestForm" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="product_name">Product Name <span class="required">*</span></label>
                <input type="text" id="product_name" name="product_name" class="form-control"
                       placeholder="e.g., Organic Tomatoes, Fresh Carrots" required>
                <div class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Enter the specific product name you're looking for
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control"
                          rows="3" placeholder="Describe the product specifications, quality requirements, etc."></textarea>
            </div>

            <div class="form-group">
                <label for="product_image">Product Image (Optional)</label>
                <div class="file-input-wrapper">
                    <input type="file" id="product_image" name="product_image"
                           class="form-control-file" accept="image/*">
                </div>
                <div class="file-preview" id="imagePreview">
                    <img id="previewImage" src="" alt="Preview">
                </div>
            </div>

            <div class="form-group quantity-unit">
                <div>
                    <label for="needed_quantity">Quantity Needed <span class="required">*</span></label>
                    <input type="number" id="needed_quantity" name="needed_quantity"
                           class="form-control" min="0.01" step="0.01" required
                           placeholder="e.g., 10.5">
                </div>
                <div>
                    <label for="unit_of_measure">Unit <span class="required">*</span></label>
                    <select id="unit_of_measure" name="unit_of_measure" class="form-control" required>
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->standard_value }}">
                            {{ ucfirst($unit->standard_value) }} - {{ $unit->description }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="needed_date">Needed By Date <span class="required">*</span></label>
                <input type="date" id="needed_date" name="needed_date"
                       class="form-control" min="{{ date('Y-m-d') }}" required>
                <div class="form-hint">
                    <i class="fas fa-calendar-alt"></i>
                    Select when you need this product
                </div>
            </div>

            <div class="form-group">
                <label for="unit_price">Expected Price Per Unit (Optional)</label>
                <div class="price-info">
                    <p><i class="fas fa-tag"></i> This helps farmers understand your budget</p>
                </div>
                <input type="number" id="unit_price" name="unit_price"
                       class="form-control" min="0" step="0.01"
                       placeholder="e.g., 150.00">
            </div>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p style="margin-top: 10px;">Submitting your request...</p>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-paper-plane"></i>
                Submit Request
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productRequestForm');
    const submitBtn = document.getElementById('submitBtn');
    const loading = document.getElementById('loading');
    const imageInput = document.getElementById('product_image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        loading.style.display = 'block';
        submitBtn.style.display = 'none';

        const formData = new FormData(form);

        try {
            const response = await fetch('{{ route("buyer.productRequest.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: result.message,
                    confirmButtonColor: '#10B981',
                    confirmButtonText: 'View My Requests'
                });

                window.location.href = '{{ route("buyer.productRequests.my") }}';
            } else {
                throw new Error(result.message || 'Submission failed');
            }

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message,
                confirmButtonColor: '#ef4444'
            });
        } finally {
            submitBtn.disabled = false;
            loading.style.display = 'none';
            submitBtn.style.display = 'flex';
        }
    });

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('needed_date').setAttribute('min', today);
});
</script>
@endsection

@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Manage Farmers')

@section('page-title', 'Manage Farmers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i> Manage Farmers
                    </h5>
                    <a href="{{ route('lf.registerFarmer') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-user-plus me-1"></i> Register New Farmer
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <form method="GET" action="" class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search by name or NIC..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="district" class="form-control">
                                <option value="">All Districts</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district }}" {{ request('district') == $district ? 'selected' : '' }}>
                                        {{ $district }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                        </div>
                    </form>

                    <!-- Farmers Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Farmer</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th>Payment Method</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($farmers as $farmer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                @php
                                                    $profilePhotoPath = 'uploads/profile_pictures/' . ($farmer->user->profile_photo ?? 'default-avatar.png');
                                                    $defaultIconPath = 'assets/icons/farmer-icon.svg';

                                                    // Check if the profile photo exists
                                                    if ($farmer->user && $farmer->user->profile_photo && file_exists(public_path($profilePhotoPath))) {
                                                        $imageSrc = asset($profilePhotoPath);
                                                        $isDefaultIcon = false;
                                                    } else {
                                                        // Use the farmer icon as fallback
                                                        $imageSrc = asset($defaultIconPath);
                                                        $isDefaultIcon = true;
                                                    }
                                                @endphp

                                                <img src="{{ $imageSrc }}"
                                                    alt="{{ $farmer->name }}"
                                                    class="rounded-circle {{ $isDefaultIcon ? 'p-1 bg-light' : '' }}"
                                                    style="width: 40px; height: 40px; object-fit: {{ $isDefaultIcon ? 'contain' : 'cover' }};">
                                            </div>
                                            <div>
                                                <strong>{{ $farmer->name }}</strong><br>
                                                <small class="text-muted">{{ $farmer->nic_no }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="fas fa-phone text-primary me-1"></i>
                                            <small>{{ $farmer->primary_mobile }}</small><br>
                                            @if($farmer->whatsapp_number)
                                                <i class="fab fa-whatsapp text-success me-1"></i>
                                                <small>{{ $farmer->whatsapp_number }}</small><br>
                                            @endif
                                            @if($farmer->email)
                                                <i class="fas fa-envelope text-info me-1"></i>
                                                <small>{{ $farmer->email }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            {{ Str::limit($farmer->residential_address, 50) }}<br>
                                            <span class="text-muted">
                                                {{ $farmer->district }} - {{ $farmer->grama_niladhari_division }}
                                            </span>
                                        </small>
                                    </td>
                                    <td>
                                        @php
                                            $paymentMethod = $farmer->preferred_payment;
                                            $paymentColors = [
                                                'bank' => 'primary',
                                                'ezcash' => 'success',
                                                'mcash' => 'info',
                                                'all' => 'warning'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $paymentColors[$paymentMethod] ?? 'secondary' }}">
                                            {{ ucfirst($paymentMethod) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $productCount = $farmer->products()->count();
                                            $activeProducts = $farmer->products()->where('is_available', true)->count();
                                        @endphp
                                        <div class="text-center">
                                            <span class="badge bg-info">{{ $productCount }}</span><br>
                                            <small class="text-muted">{{ $activeProducts }} active</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($farmer->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info"
                                                    data-bs-toggle="tooltip"
                                                    title="View Details"
                                                    onclick="viewFarmerDetails({{ $farmer->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('lf.editFarmer', $farmer->id) }}"
                                               class="btn btn-sm btn-warning"
                                               data-bs-toggle="tooltip"
                                               title="Edit Farmer">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="tooltip"
                                                    title="Delete Farmer"
                                                    onclick="deleteFarmer({{ $farmer->id }}, '{{ $farmer->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Farmers Found</h5>
                                        <p class="text-muted">Register your first farmer to get started.</p>
                                        <a href="{{ route('lf.registerFarmer') }}" class="btn btn-primary">
                                            <i class="fas fa-user-plus me-1"></i> Register Farmer
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Stats -->
                    <div class="row mt-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="card border-left-primary shadow-sm h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Farmers</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $farmers->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="card border-left-success shadow-sm h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Active Farmers</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $farmers->where('is_active', true)->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="card border-left-info shadow-sm h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Products</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $farmers->sum(function($f) { return $f->products()->count(); }) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="card border-left-warning shadow-sm h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Avg Products/Farmer</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                @if($farmers->count() > 0)
                                                    {{ number_format($farmers->sum(function($f) { return $f->products()->count(); }) / $farmers->count(), 1) }}
                                                @else
                                                    0.0
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Farmer Details Modal -->
<div class="modal fade" id="farmerDetailsModal" tabindex="-1" aria-labelledby="farmerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="farmerDetailsModalLabel">
                    <i class="fas fa-user me-2"></i> Farmer Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="farmerDetailsContent">
                <!-- Details will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="editFarmerBtn" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Farmer
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    function viewFarmerDetails(farmerId) {
        // Show loading state
        document.getElementById('farmerDetailsContent').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading farmer details...</p>
            </div>
        `;

        // Fetch farmer details via AJAX
        fetch(`/lead-farmer/farmers/${farmerId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const farmer = data.farmer;

                    // Format payment details
                    let paymentDetails = '';
                    try {
                        const paymentInfo = JSON.parse(farmer.payment_details || '{}');
                        if (paymentInfo.bank) {
                            paymentDetails += `
                                <div class="mb-2">
                                    <strong>Bank:</strong> ${paymentInfo.bank.bank_name} (${paymentInfo.bank.bank_branch})<br>
                                    <strong>Account:</strong> ${paymentInfo.bank.account_number}<br>
                                    <strong>Holder:</strong> ${paymentInfo.bank.account_holder_name}
                                </div>
                            `;
                        }
                        if (paymentInfo.ezcash) {
                            paymentDetails += `<div class="mb-2"><strong>EzCash:</strong> ${paymentInfo.ezcash.mobile}</div>`;
                        }
                        if (paymentInfo.mcash) {
                            paymentDetails += `<div class="mb-2"><strong>mCash:</strong> ${paymentInfo.mcash.mobile}</div>`;
                        }
                    } catch (e) {
                        paymentDetails = '<div class="text-muted">No payment details available</div>';
                    }

                    // Build modal content
                    const content = `
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <img src="${farmer.profile_photo_url}"
                                     alt="${farmer.name}"
                                     class="img-fluid rounded-circle mb-3"
                                     style="max-width: 200px; height: 200px; object-fit: cover;">
                                <h4>${farmer.name}</h4>
                                <p class="text-muted">${farmer.nic_no}</p>
                                <span class="badge ${farmer.is_active ? 'bg-success' : 'bg-danger'}">
                                    ${farmer.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </div>

                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <h6 class="text-primary">
                                            <i class="fas fa-phone me-1"></i> Contact Information
                                        </h6>
                                        <div class="ps-3">
                                            <p class="mb-1">
                                                <strong>Primary Mobile:</strong> ${farmer.primary_mobile}
                                            </p>
                                            ${farmer.whatsapp_number ? `<p class="mb-1"><strong>WhatsApp:</strong> ${farmer.whatsapp_number}</p>` : ''}
                                            ${farmer.email ? `<p class="mb-1"><strong>Email:</strong> ${farmer.email}</p>` : ''}
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <h6 class="text-primary">
                                            <i class="fas fa-home me-1"></i> Address Information
                                        </h6>
                                        <div class="ps-3">
                                            <p class="mb-1">
                                                <strong>Address:</strong> ${farmer.residential_address}
                                            </p>
                                            <p class="mb-1">
                                                <strong>District:</strong> ${farmer.district}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Grama Niladhari Division:</strong> ${farmer.grama_niladhari_division}
                                            </p>
                                            ${farmer.address_map_link ? `<p class="mb-1"><strong>Map Link:</strong> <a href="${farmer.address_map_link}" target="_blank">View on Google Maps</a></p>` : ''}
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <h6 class="text-primary">
                                            <i class="fas fa-money-bill-wave me-1"></i> Payment Information
                                        </h6>
                                        <div class="ps-3">
                                            <p class="mb-2">
                                                <strong>Preferred Method:</strong>
                                                <span class="badge bg-${getPaymentMethodColor(farmer.preferred_payment)}">
                                                    ${farmer.preferred_payment.toUpperCase()}
                                                </span>
                                            </p>
                                            ${paymentDetails}
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <h6 class="text-primary">
                                            <i class="fas fa-box me-1"></i> Products Summary
                                        </h6>
                                        <div class="ps-3">
                                            <p class="mb-1">
                                                <strong>Total Products:</strong> ${farmer.products_count}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Active Products:</strong> ${farmer.active_products_count}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Last Updated:</strong> ${farmer.updated_at_formatted}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    document.getElementById('farmerDetailsContent').innerHTML = content;
                    document.getElementById('editFarmerBtn').href = `/lead-farmer/edit-farmer/${farmerId}`;

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('farmerDetailsModal'));
                    modal.show();
                } else {
                    toastr.error('Failed to load farmer details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Error loading farmer details');
            });
    }

    function deleteFarmer(farmerId, farmerName) {
        Swal.fire({
            title: 'Delete Farmer?',
            html: `<p>Are you sure you want to delete farmer <strong>${farmerName}</strong>?</p>
                   <p class="text-danger">This action cannot be undone. All associated products will also be deleted.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Send delete request
                fetch(`/lead-farmer/farmers/${farmerId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Deleted!',
                            `Farmer ${farmerName} has been deleted.`,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'Failed to delete farmer.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'Something went wrong. Please try again.',
                        'error'
                    );
                });
            }
        });
    }

    function getPaymentMethodColor(method) {
        const colors = {
            'bank': 'primary',
            'ezcash': 'success',
            'mcash': 'info',
            'all': 'warning'
        };
        return colors[method] || 'secondary';
    }
</script>
@endpush

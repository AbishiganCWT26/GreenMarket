@extends('admin.layouts.admin_master')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
@endsection

@section('content')
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon-box icon-blue">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-number" id="totalUsers">{{ $totalUsers }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box icon-green">
            <span class="custom-icon">
                {!! file_get_contents(public_path('assets/icons/farmer-icon-white.svg')) !!}
            </span>
        </div>
        <div class="stat-info">
            <div class="stat-number" id="farmersValue">{{ $farmers }}</div>
            <div class="stat-label">Farmers</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box icon-purple">
            <i class="fa-solid fa-box"></i>
        </div>
        <div class="stat-info">
            <div class="stat-number" id="productsValue">{{ $products }}</div>
            <div class="stat-label">Products</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box icon-yellow">
            <i class="fa-solid fa-chart-simple"></i>
        </div>
        <div class="stat-info">
            <div class="stat-number" id="salesValue">LKR {{ number_format($sales, 2) }}</div>
            <div class="stat-label">Total Sales</div>
        </div>
    </div>
</div>

<div class="content-grid">
    <div class="content-card">
        <div class="card-header">
            <i class="fa-solid fa-users-gear"></i>
            <span>User Management</span>
        </div>
        <div class="user-list">
            <div class="user-list-item"><span>Admins:</span><span>{{ $admins }}</span></div>
            <div class="user-list-item"><span>Lead Farmers:</span><span>{{ $leadFarmers }}</span></div>
            <div class="user-list-item"><span>Farmers:</span><span>{{ $farmers }}</span></div>
            <div class="user-list-item"><span>Buyers:</span><span>{{ $buyers }}</span></div>
            <div class="user-list-item"><span>Facilitators:</span><span>{{ $facilitators }}</span></div>
        </div>
    </div>

    <div class="quick-actions">
        <div class="widget-title">
            <i class="fa-solid fa-bolt"></i>
            <span>Quick Actions</span>
        </div>
        <ul class="action-list">
            <li><a href="{{ url('/admin/users') }}"><i class="fa-solid fa-users-gear action-icon"></i> Manage users</a></li>
            <li><a href="{{ url('/admin/sales') }}"><i class="fa-solid fa-receipt action-icon"></i> Sales Recorders</a></li>
            <li><a href="{{ url('/admin/taxonomy') }}"><i class="fa-solid fa-seedling action-icon"></i> Add Product Category</a></li>
            <li><a href="{{ url('/admin/reports/generate') }}"><i class="fa-solid fa-file-invoice action-icon"></i> Generate Report</a></li>
            <li><a href="{{ url('/admin/config') }}"><i class="fa-solid fa-gear action-icon"></i> System Configuration</a></li>
        </ul>
    </div>
</div>

<div class="table-container">
    <div class="widget-title">
        <i class="fa-solid fa-table-list"></i>
        <span>Recent Lead Farmer Groups</span>
    </div>
    <div class="overflow-x">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Group Name</th>
                    <th>Total Sales (LKR)</th>
                    <th>Active Farmers</th>
                    <th>Success Rate</th>
                </tr>
            </thead>
            <tbody>
                @if(count($groups) == 0)
                    <tr><td colspan="5" class="text-center">No data available</td></tr>
                @else
                    @foreach($groups as $g)
                    <tr>
                        <td data-label="Rank">{{ $g->rank }}</td>
                        <td data-label="Group Name">{{ $g->group_name }}</td>
                        <td data-label="Total Sales">LKR {{ number_format($g->total_sales, 2) }}</td>
                        <td data-label="Active Farmers">{{ $g->active_farmers }}</td>
                        <td data-label="Success Rate">{{ $g->success_rate }}%</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    
    <div class="pagination-wrapper mt-3">
        {{ $groups->appends(['complaints_page' => request('complaints_page')])->links('vendor.pagination.compact') }}
    </div>
</div>

<div class="table-container">
    <div class="widget-title">
        <i class="fa-solid fa-comments"></i>
        <span>Recent Complaints</span>
    </div>
    <div class="overflow-x">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Complainant</th>
                    <th>Against</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(count($complaints) == 0)
                    <tr><td colspan="8" class="text-center">No complaints</td></tr>
                @else
                    @foreach($complaints as $c)
                    <tr>
                        <td data-label="ID">{{ $c->id }}</td>
                        <td data-label="Complainant">{{ $c->complainant_name ?? '—' }}</td>
                        <td data-label="Against">
                            @php
                                $against = $c->against_user_id ? \DB::table('users')->where('id', $c->against_user_id)->value('username') : null;
                            @endphp
                            {{ $against ?? '—' }}
                        </td>
                        <td data-label="Type">{{ ucfirst(str_replace('_',' ', $c->complaint_type)) }}</td>
                        <td data-label="Description">{{ Str::limit($c->description, 80) }}</td>
                        <td data-label="Status">
                            <span class="status {{ $c->status }}">{{ ucfirst(str_replace('_',' ', $c->status)) }}</span>
                        </td>
                        <td data-label="Created">{{ \Carbon\Carbon::parse($c->created_at)->format('Y-m-d H:i') }}</td>
                        <td data-label="Actions">
                            <a href="{{ url('/admin/complaints/'.$c->id) }}" class="btn btn-sm">View</a>
                            @if($c->status != 'resolved')
                                <button class="btn btn-sm alert-facilitator" data-id="{{ $c->id }}">Alert</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    
    <div class="pagination-wrapper mt-3">
        {{ $complaints->appends(['groups_page' => request('groups_page')])->links('vendor.pagination.compact') }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Pass the facilitators list from PHP to JS
        const facilitators = @json($facilitatorsList);

        document.querySelectorAll('.alert-facilitator').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const complaintId = this.dataset.id;
                
                // Create options for the select dropdown
                let options = {};
                facilitators.forEach(f => {
                    options[f.user_id] = `${f.name} (${f.assigned_division || 'No Division'})`;
                });

                Swal.fire({
                    title: 'Select Facilitator',
                    text: 'Choose a facilitator to alert regarding this complaint:',
                    input: 'select',
                    inputOptions: options,
                    inputPlaceholder: 'Select a facilitator',
                    showCancelButton: true,
                    confirmButtonText: 'Send Alert',
                    confirmButtonColor: '#10B981',
                    showLoaderOnConfirm: true,
                    preConfirm: (facilitatorId) => {
                        if (!facilitatorId) {
                            Swal.showValidationMessage('Please select a facilitator');
                            return false;
                        }
                        
                        return fetch('{{ url("/admin/complaints/alert") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ 
                                id: complaintId,
                                facilitator_id: facilitatorId
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                `Request failed: ${error}`
                            );
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.value.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Alert Sent',
                                text: result.value.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.value.message || 'Unable to alert facilitator',
                                confirmButtonColor: '#10B981'
                            });
                        }
                    }
                });
            });
        });
    });
</script>
@endsection
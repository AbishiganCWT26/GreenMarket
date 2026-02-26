@extends('admin.layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Complaint Details #{{ $complaint->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.complaints.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Complaint Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>ID:</th>
                                    <td>{{ $complaint->id }}</td>
                                </tr>
                                <tr>
                                    <th>Complainant:</th>
                                    <td>{{ $complaint->complainant->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Against User:</th>
                                    <td>{{ $complaint->againstUser->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Order ID:</th>
                                    <td>{{ $complaint->order_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Reason:</th>
                                    <td>{{ $complaint->reason }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $complaint->description }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $complaint->status == 'pending' ? 'warning' : ($complaint->status == 'resolved' ? 'success' : 'info') }}">
                                            {{ ucfirst($complaint->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $complaint->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Resolved By:</th>
                                    <td>{{ $complaint->resolvedBy->name ?? 'Not resolved yet' }}</td>
                                </tr>
                                <tr>
                                    <th>Resolved At:</th>
                                    <td>{{ $complaint->resolved_at ? $complaint->resolved_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
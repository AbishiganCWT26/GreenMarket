@extends('admin.layouts.admin_master')

@section('title', 'Admin Audit Log')

@section('content')

<h2><i class="fas fa-user-secret"></i> Admin Activity Log</h2>

<div class="card-panel">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Admin</th>
                <th>Action</th>
                <th>IP</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>
            @foreach($audits as $a)
            <tr>
                <td>{{ $a->admin_name }}</td>
                <td>{{ $a->action }}</td>
                <td>{{ $a->ip_address }}</td>
                <td>{{ $a->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

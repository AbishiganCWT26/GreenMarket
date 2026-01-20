@extends('admin.layouts.admin_master')

@section('title', 'All Users')

@section('content')

<h2><i class="fas fa-users"></i> User Accounts</h2>

<div class="card-panel">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Email</th>
                <th>Joined</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($users as $u)
            <tr>
                <td>{{ $u->username }}</td>
                <td>{{ ucfirst($u->role) }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->created_at->format('Y-m-d') }}</td>
                <td>{{ $u->active ? 'Active' : 'Suspended' }}</td>
                <td>
                    <a href="{{ route('admin.users.view', $u->id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

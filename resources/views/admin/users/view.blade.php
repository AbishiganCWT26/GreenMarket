@extends('admin.layouts.admin_master')

@section('title', 'User Details')

@section('content')

<h2><i class="fas fa-user-circle"></i> User Details</h2>

<div class="card-panel">
    <p><strong>Name:</strong> {{ $user->username }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
    <p><strong>Status:</strong> {{ $user->active ? 'Active' : 'Suspended' }}</p>
    <p><strong>Joined:</strong> {{ $user->created_at->format('Y-m-d') }}</p>

    <a href="{{ route('admin.users.list') }}" class="btn btn-secondary">Back to List</a>
</div>

@endsection

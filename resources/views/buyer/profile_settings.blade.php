@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')

<h2><i class="fas fa-cog"></i> Account Settings</h2>

<div class="card-panel">

    <form action="{{ route('buyer.profile.password') }}" method="POST">
        @csrf

        <h4>Change Password</h4>
        <hr>

        <label>Current Password</label>
        <input type="password" name="current_password" class="form-control" required>

        <label>New Password</label>
        <input type="password" name="new_password" class="form-control" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>

        <button class="btn btn-primary mt-3">
            <i class="fas fa-key"></i> Update Password
        </button>
    </form>

</div>

@endsection

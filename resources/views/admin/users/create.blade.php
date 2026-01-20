@extends('admin.layouts.admin_master')

@section('content')
<div class="content-card">
    <h4 class="card-header">Register Admin / Facilitator</h4>

    <form action="{{ url('/admin/users/store') }}" method="POST">
        @csrf

        <label>Username</label>
        <input type="text" name="username" class="input" required>

        <label>Email</label>
        <input type="email" name="email" class="input" required>

        <label>Password</label>
        <input type="password" name="password" class="input" required>

        <label>Role</label>
        <select name="role" class="input">
            <option value="admin">Admin</option>
            <option value="facilitator">Facilitator</option>
        </select>

        <button class="btn">Create User</button>
    </form>
</div>
@endsection

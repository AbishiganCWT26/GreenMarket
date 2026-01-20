@extends('admin.layouts.admin_master')

@section('title', 'Manage User Roles')

@section('content')

<h2>User Role Manager</h2>

<div class="card">
    <div class="card-body">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Current Role</th>
                    <th>Change To</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>{{ $u->username }}</td>
                    <td>{{ $u->role }}</td>
                    <td>
                        <form action="{{ route('admin.users.updateRole', $u->id) }}" method="POST">
                            @csrf
                            <select name="role" class="form-control">
                                <option value="farmer">Farmer</option>
                                <option value="lead_farmer">Lead Farmer</option>
                                <option value="buyer">Buyer</option>
                                <option value="facilitator">Facilitator</option>
                                <option value="admin">Admin</option>
                            </select>

                            <button class="btn btn-primary btn-sm mt-1">Save</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>
</div>

@endsection

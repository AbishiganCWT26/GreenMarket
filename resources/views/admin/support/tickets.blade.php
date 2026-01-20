@extends('admin.layouts.admin_master')

@section('title', 'Support Tickets')

@section('content')

<h2><i class="fas fa-headset"></i> User Support Tickets</h2>

<div class="card-panel">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>User</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>

        <tbody>
            @foreach($tickets as $t)
            <tr>
                <td>{{ $t->id }}</td>
                <td>{{ $t->user->username }}</td>
                <td>{{ $t->subject }}</td>
                <td>{{ ucfirst($t->status) }}</td>
                <td>{{ $t->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

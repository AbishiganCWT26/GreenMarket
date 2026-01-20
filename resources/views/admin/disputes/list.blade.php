@extends('admin.layouts.admin_master')

@section('title', 'Order Disputes')

@section('content')

<h2><i class="fas fa-exclamation-circle"></i> Order Disputes</h2>

<div class="card-panel">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Dispute ID</th>
                <th>Order</th>
                <th>User</th>
                <th>Message</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($disputes as $d)
            <tr>
                <td>{{ $d->id }}</td>
                <td>{{ $d->order_id }}</td>
                <td>{{ $d->user->username }}</td>
                <td>{{ $d->message }}</td>
                <td>{{ ucfirst($d->status) }}</td>
            </tr>
            @endforeach
        </tbody>

    </table>

</div>

@endsection

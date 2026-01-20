@extends('admin.layouts.admin_master')

@section('title', 'System Standards')

@section('content')

<h2>Unit Measures & Quality Grades</h2>

<div class="card">
    <div class="card-body">

        <a href="{{ route('admin.taxonomy.create') }}" class="btn btn-primary">Add New Standard</a>

        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Description</th>
                    <th>Order</th>
                </tr>
            </thead>

            <tbody>
                @foreach($standards as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->standard_type }}</td>
                    <td>{{ $s->standard_value }}</td>
                    <td>{{ $s->description }}</td>
                    <td>{{ $s->display_order }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection

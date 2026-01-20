@extends('admin.layouts.admin_master')

@section('title', 'Content Management')

@section('content')
<h2>Content Management</h2>

<div class="card">
    <div class="card-body">

        <h4>Edit About Us</h4>

        <form method="POST" action="{{ route('admin.content.save') }}">
            @csrf
            <textarea name="about" class="form-control" rows="6">{{ $about }}</textarea>

            <br>
            <button class="btn btn-primary">Save</button>
        </form>

    </div>
</div>
@endsection

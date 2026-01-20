@extends('admin.layouts.admin_master')

@section('title', 'System Configuration')

@section('content')
<h2>System Configuration</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.config.update') }}">
            @csrf

            <div class="form-group">
                <label>Footer Text</label>
                <textarea name="footer_text" class="form-control" rows="3">{{ $footer }}</textarea>
            </div>

            <div class="form-group">
                <label>About Us</label>
                <textarea name="about_us" class="form-control" rows="5">{{ $about }}</textarea>
            </div>

            <button class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>
@endsection

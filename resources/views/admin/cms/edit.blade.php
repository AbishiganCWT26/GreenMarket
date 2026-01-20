@extends('admin.layouts.admin_master')

@section('title', 'Edit Page')

@section('content')

<h2>Edit Page: {{ ucfirst($page) }}</h2>

<div class="card-panel">
    <form method="POST" action="{{ route('admin.cms.save', $page) }}">
        @csrf

        <textarea name="content" rows="12" class="form-control">{{ $content }}</textarea>

        <button class="btn btn-primary mt-2">Save Page</button>
    </form>
</div>

@endsection

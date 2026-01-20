@extends('admin.layouts.admin_master')

@section('title', 'Content Index')

@section('content')
<h2>Content Overview</h2>

<ul>
    <li><a href="{{ route('admin.content.manage') }}">Manage Content</a></li>
    <li><a href="{{ route('admin.config.index') }}">System Config</a></li>
</ul>

@endsection

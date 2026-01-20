@extends('admin.layouts.admin_master')

@section('title', 'CMS Pages')

@section('content')

<h2><i class="fas fa-file-alt"></i> CMS Pages</h2>

<div class="card-panel">
    <ul>
        <li><a href="{{ route('admin.cms.edit', 'about') }}">About Us</a></li>
        <li><a href="{{ route('admin.cms.edit', 'privacy') }}">Privacy Policy</a></li>
        <li><a href="{{ route('admin.cms.edit', 'terms') }}">Terms & Conditions</a></li>
    </ul>
</div>

@endsection

@extends('admin.layouts.admin_master')

@section('title', 'System Settings')

@section('content')

<h2>Global System Settings</h2>

<div class="card-panel">
    <form method="POST" action="{{ route('admin.settings.system.save') }}">
        @csrf

        <div class="form-group">
            <label>Site Title</label>
            <input class="form-control" name="site_title" value="{{ $settings->site_title }}">
        </div>

        <div class="form-group">
            <label>Maintenance Mode</label>
            <select name="maintenance" class="form-control">
                <option value="0">Disabled</option>
                <option value="1">Enabled</option>
            </select>
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>

@endsection

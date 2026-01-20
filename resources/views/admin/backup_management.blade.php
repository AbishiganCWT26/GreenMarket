@extends('admin.layouts.admin_master')

@section('title', 'Backup Manager')

@section('content')
<h2>Database Backup</h2>

<div class="card">
    <div class="card-body">

        <p>You can download full database backups below.</p>

        <a href="{{ route('admin.config.backup') }}" class="btn btn-primary">Generate Backup</a>

        @if(isset($backupFiles) && count($backupFiles))
        <h4 class="mt-4">Available Backups</h4>
        <ul>
            @foreach($backupFiles as $file)
                <li><a href="{{ asset('backups/'.$file) }}">{{ $file }}</a></li>
            @endforeach
        </ul>
        @endif

    </div>
</div>
@endsection

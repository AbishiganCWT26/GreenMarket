@extends('admin.layouts.admin_master')

@section('title', 'System Logs')

@section('content')

<h2><i class="fas fa-terminal"></i> System Error Logs</h2>

<div class="card-panel log-box">

<pre>
{{ $logs ?? 'No logs available.' }}
</pre>

</div>

@endsection

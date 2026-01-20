@extends('admin.layouts.admin_master')

@section('title', 'Reports')

@section('content')
<div class="page-header">
    <h2>Reports</h2>
</div>

<div class="card">
    <div class="card-body">

        <h4>Select Report Type</h4>

        <ul class="report-list">
            <li><a href="{{ route('admin.reports.financial') }}">Financial Report</a></li>
            <li><a href="#">Sales Summary</a></li>
            <li><a href="#">Product Performance</a></li>
            <li><a href="#">Buyer Activity</a></li>
            <li><a href="#">Lead Farmer Reports</a></li>
        </ul>

    </div>
</div>
@endsection

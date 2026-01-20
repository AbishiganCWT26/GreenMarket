@extends('admin.layouts.admin_master')

@section('title', 'Payment Gateway Settings')

@section('content')

<h2>Payment Settings</h2>

<div class="card-panel">
    <form method="POST" action="{{ route('admin.settings.payment.save') }}">
        @csrf

        <div class="form-group">
            <label>Commission Percentage</label>
            <input class="form-control" name="commission" value="{{ $settings->commission }}">
        </div>

        <div class="form-group">
            <label>Bank Account Number</label>
            <input class="form-control" name="bank_account" value="{{ $settings->bank_account }}">
        </div>

        <button class="btn btn-primary">Save Settings</button>
    </form>
</div>

@endsection

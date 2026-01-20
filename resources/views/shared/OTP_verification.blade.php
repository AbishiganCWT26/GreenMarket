@extends('layouts.public_master')

@section('title','OTP Verification')

@section('content')
<div style="max-width:480px;margin:24px auto;">
    <h2>Enter OTP</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ url('/auth/verify-otp') }}">
        @csrf
        <div>
            <label>OTP Code</label><br>
            <input type="text" name="otp" required maxlength="8" style="width:100%;padding:8px">
        </div>

        <div style="margin-top:10px;">
            <button class="btn" type="submit">Verify</button>
        </div>
    </form>
</div>
@endsection

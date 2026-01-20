@extends('layouts.public_master')

@section('title','Reset Password')

@section('content')
<div style="max-width:480px;margin:24px auto;">
    <h2>Reset Password</h2>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ url('/password/reset') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token ?? '' }}">

        <div>
            <label>Email</label><br>
            <input type="email" name="email" required value="{{ $email ?? old('email') }}" style="width:100%;padding:8px">
        </div>

        <div>
            <label>New password</label><br>
            <input type="password" name="password" required style="width:100%;padding:8px">
        </div>

        <div>
            <label>Confirm password</label><br>
            <input type="password" name="password_confirmation" required style="width:100%;padding:8px">
        </div>

        <div style="margin-top:10px;">
            <button class="btn" type="submit">Reset password</button>
        </div>
    </form>
</div>
@endsection

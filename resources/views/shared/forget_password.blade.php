@extends('layouts.public_master')

@section('title','Forgot Password')

@section('content')
<div style="max-width:480px;margin:24px auto;">
    <h2>Forgot Password</h2>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ url('/password/email') }}">
        @csrf
        <div>
            <label>Email</label><br>
            <input type="email" name="email" required value="{{ old('email') }}" style="width:100%;padding:8px">
        </div>

        <div style="margin-top:10px;">
            <button class="btn" type="submit">Send reset link</button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ - Red Potion')

@section('content')
    <div class="login-wrapper">
        <div class="login-container">
            <h1 class="login-title">Verify Your Email</h1>
            <p class="login-subtitle">
                A verification link has been sent to your email address.
                Please check your inbox and verify your account.
            </p>

            <!-- Resend Verification Link -->
            <form action="{{ route('verification.send') }}" method="POST" class="login-form">
                @csrf
                <button type="submit" class="btn btn-primary">Resend Verification Email</button>
            </form>

            <!-- Logout Link -->
            <p class="register-link">
                Logged in with the wrong account? <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log out</a>
            </p>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
@endsection

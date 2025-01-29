@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ - Red Potion')

@section('content')
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-logo">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="RedPotion Logo">
                </a>
            </div>
            <p class="login-subtitle">กรุณาเลือกวิธีล็อกอิน</p>
            <form action="{{ route('login') }}" method="POST" class="login-form">
                <div class="social-login">
                    <a href="{{ route('auth.google') }}" class="btn btn-google">
                        <img src="{{ asset('images/google-icon.png') }}" alt="Google Icon" class="google-icon">
                        เข้าสู่ระบบด้วย Google
                    </a>
                </div>
                <div class="separator">
                    <span>หรือ</span>
                </div>                
                @csrf
                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required autofocus>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">พาสเวิร์ด</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                <!-- Remember Me & Forgot Password -->
                <div class="form-options">
                    <label class="form-remember">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">ลืมพาสเวิร์ด?</a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Log in</button>

                <!-- Register Link -->
                <p class="register-link">
                    <a href="{{ route('register') }}">สมัครสมาชิก</a>
                </p>
            </form>
        </div>
    </div>
@endsection

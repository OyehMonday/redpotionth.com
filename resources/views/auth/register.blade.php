@extends('layouts.app')

@section('title', 'สมัครสมาชิก - Red Potion')

@section('content')
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-logo">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="RedPotion Logo">
                </a>
            </div>
            <p class="login-subtitle">สมัครสมาชิก</p>
            <form action="{{ route('register') }}" method="POST" class="login-form">
                @csrf

                <!-- Name Field -->
                <div class="form-group">
                    <label for="name" class="form-label">Username</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Enter your username" required>
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>

                <!-- Confirm Password Field -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Confirm your password" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Sign up</button>

                <!-- Already Registered -->
                <p class="register-link">
                    Already have an account? <a href="{{ route('login') }}">Log in</a>
                </p>
            </form>
        </div>
    </div>
@endsection

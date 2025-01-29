@extends('layouts.app')

@section('title', 'สร้างพาสเวิร์ดใหม่ - Red Potion')

@section('content')
    <div class="login-wrapper">
        <div class="login-container">
            <h1 class="login-title">Reset Your Password</h1>
            <p class="login-subtitle">Enter your new password below</p>
            
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('password.update') }}" method="POST" class="login-form">
                @csrf
                @method('PUT')

                <!-- Token Field (Hidden) -->
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your new password" required>
                </div>

                <!-- Confirm Password Field -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Confirm your new password" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Reset Password</button>

                <!-- Back to Login -->
                <p class="register-link">
                    Remember your password? <a href="{{ route('login') }}">Log in</a>
                </p>
            </form>
        </div>
    </div>
@endsection

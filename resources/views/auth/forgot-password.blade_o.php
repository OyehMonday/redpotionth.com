@extends('layouts.app')

@section('title', 'ลืมรหัสผ่าน - Red Potion')

@section('content')
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-logo">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="RedPotion Logo">
                </a>
            </div>
            <h1 class="login-title">ลืมรหัสผ่าน</h1>
            <p class="login-subtitle">
                กรุณากรอกอีเมลของคุณเพื่อรับลิงก์สำหรับรีเซ็ตรหัสผ่าน
            </p>

            {{-- Success Message --}}
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Forgot Password Form --}}
            <form action="{{ route('password.email') }}" method="POST" class="login-form">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" id="email" name="email" 
                           class="form-input @error('email') is-invalid @enderror" 
                           placeholder="กรอกอีเมลของคุณ" 
                           value="{{ old('email') }}" required autofocus>
                    
                    @error('email')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    ส่งลิงก์รีเซ็ตรหัสผ่าน
                </button>
            </form>
        </div>
    </div>
@endsection

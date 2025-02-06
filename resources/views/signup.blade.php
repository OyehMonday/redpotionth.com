<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    <div class="main-wrapper">
        <div class="login-container">
            <div class="login-logo">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-image">
                </a>
            </div> 
            <h1 class="login-title">สมัครสมาชิก</h1>
            <p class="login-subtitle">กรุณากรอกข้อมูลเพื่อสมัครสมาชิก</p>
            
            @if ($errors->any())
                <div class="signupalert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif            

            <form action="{{ route('custom.signup') }}" method="POST" class="login-form">
                @csrf
                <div class="form-group">
                    <label for="username" class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" id="username" name="username" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>
                <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
            </form>

            <div class="register-link">
                <p>มีสมาชิกอยู่แล้ว? <a href="{{ route('custom.login.form') }}">เข้าสู่ระบบ</a></p>
            </div>
        </div>
    </div>
</body>
</html>

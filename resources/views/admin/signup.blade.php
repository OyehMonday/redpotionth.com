<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup</title>
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
            <h1 class="signup-title">สมัครสมาชิกผู้ดูแลระบบ</h1><br>
            
            <form action="{{ route('admin.signup') }}" method="POST" class="signup-form">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" id="name" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
                </div>

                <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
            </form>

            <div class="register-link">
                <p>มีบัญชีอยู่แล้ว? <a href="{{ route('admin.login') }}">เข้าสู่ระบบ</a></p>
            </div>
        </div>
    </div>
</body>
</html>

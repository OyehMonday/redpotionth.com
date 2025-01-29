<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
            <h1 class="login-title">เข้าสู่ระบบผู้ดูแล</h1><br>
            
            <form action="{{ route('admin.login') }}" method="POST" class="login-form">                
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>

                <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
            </form>

            <div class="register-link">
                <p>ยังไม่มีบัญชี? <a href="{{ route('admin.signup') }}">สร้างบัญชีใหม่</a></p>
            </div>
        </div>
    </div>
</body>
</html>

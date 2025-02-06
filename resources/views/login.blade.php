<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            <h1 class="login-title">เข้าสู่ระบบ</h1><br>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('signupsuccess'))
                <div class="alert alert-success">
                    {!! nl2br(e(session('signupsuccess'))) !!}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            @if(!session('signupsuccess'))
                <form action="{{ route('custom.login') }}" method="POST" class="login-form">            
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">อีเมล</label>
                        <input type="email" id="email" name="email" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">รหัสผ่าน</label>
                        <input type="password" id="password" name="password" class="form-input" required>
                    </div>

                    <div style="text-align: right;">
                        <a href="{{ route('password.request') }}" class="forgot-password">ลืมรหัสผ่าน?</a>
                    </div>

                    <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
                    
                    <div class="register-link">
                        <p>ยังไม่มีบัญชี? <a href="{{ route('custom.signup.form') }}">สมัครสมาชิก</a></p>
                    </div>
                    <div class="separator">
                        <span>หรือ</span>
                    </div>  
                    <div class="social-login">
                        <a href="{{ route('auth.google') }}" class="btn btn-google">
                            <img src="{{ asset('images/google-icon.png') }}" alt="Google Icon" class="google-icon">
                            เข้าสู่ระบบด้วย Google
                        </a>
                    </div>    
                </form>
            @endif
        </div>
    </div>
</body>
</html>

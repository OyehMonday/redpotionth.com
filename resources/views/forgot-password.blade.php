<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีเซ็ตรหัสผ่าน</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?compat=recaptcha" async defer></script>
</head>
<body>
    <div class="main-wrapper">
        <div class="login-container">
            <div class="login-logo">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-image">
                </a>
            </div> 
            <h1 class="login-title">ลืมรหัสผ่าน</h1>

            @if(session('status'))
                <p class="success-message">{{ session('status') }}</p>
                <p class="login-subtitle">กรุณาตรวจสอบอีเมลของคุณเพื่อทำการรีเซ็ตรหัสผ่าน</p>
                <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('custom.login.form') }}'">กลับไปยังหน้าล็อกอิน</button>
            @else
                <p class="login-subtitle">กรุณากรอกอีเมลของคุณเพื่อรับลิงก์สำหรับรีเซ็ตรหัสผ่าน</p>
                
                <form action="{{ route('password.email') }}" method="POST" class="login-form">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">อีเมล</label>
                        <input type="email" id="email" name="email" class="form-input" required>
                    </div>

                    <!-- Cloudflare Turnstile -->
                    <div class="form-group turnstile-container">
                        <div class="cf-turnstile" data-sitekey="0x4AAAAAAA_UyLit626y-h40"></div>
                    </div>
                    @if ($errors->has('cf-turnstile-response'))
                    <div class="turnstile-container">
                        <span class="text-danger">{{ $errors->first('cf-turnstile-response') }}</span>
                    </div>
                    @endif  

                    <button type="submit" class="btn btn-primary">ส่งลิงก์รีเซ็ตรหัสผ่าน</button>
                </form>
            @endif
        </div>
    </div>
</body>
</html>

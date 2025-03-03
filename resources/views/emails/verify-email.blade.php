<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันอีเมลของคุณ</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #1a1f36;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .email-container {
            max-width: 500px;
            margin: 0 auto;
            background: #242b48;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .logo {
            width: 150px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 22px;
            color: #fff;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
        }
        .reset-button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            transition: background 0.3s ease-in-out;
        }
        .reset-button:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>    
</head>
<body>
    <div class="email-container">
        <img src="{{ asset('images/logo.png') }}" alt="Website Logo" class="logo">

        <h1>ยืนยันอีเมลของคุณ</h1>

        <p>สวัสดี {{ $user->username }},</p>
        <p>กรุณาคลิกที่ลิงก์ด้านล่างเพื่อยืนยันอีเมลของคุณ:</p>
        <p>
            <a href="{{ route('verify.email', ['token' => $user->verification_token]) }}" class="reset-button">ยืนยันอีเมล</a>
        </p>
        
        <p>หากคุณไม่ได้ทำการสมัครสมาชิก โปรดเพิกเฉยต่ออีเมลนี้</p>

        <div class="footer">
            <p><strong>ทีมงาน Red Potion</strong><br>www.redpotionth.com</p>
        </div>
    </div>
</body>
</html>

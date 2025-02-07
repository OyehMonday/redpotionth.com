<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันอีเมลของคุณ</title>
</head>
<body>
    <h2>ยืนยันอีเมลของคุณ</h2>
    <p>สวัสดี {{ $user->username }},</p>
    <p>กรุณาคลิกที่ลิงก์ด้านล่างเพื่อยืนยันอีเมลของคุณ:</p>
    
    <p>
        <a href="{{ route('verify.email', ['token' => $user->verification_token]) }}"
           style="display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none;">
            ยืนยันอีเมล
        </a>
    </p>

    <p>หากคุณไม่ได้ลงทะเบียนในเว็บไซต์ของเรา กรุณาเพิกเฉยอีเมลนี้</p>
</body>
</html>

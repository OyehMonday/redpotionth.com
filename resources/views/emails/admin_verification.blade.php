<html>
<body>
    <h1>Welcome to RedPotion Admin</h1>
    <p>Please click the link below to verify your admin account:</p>
    <a href="{{ url('/admin/verify/' . $verificationToken) }}">Verify Email</a>
</body>
</html>

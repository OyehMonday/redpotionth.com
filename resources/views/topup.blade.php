<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Up - {{ $game->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body class="dark-theme">
    @include('navbar')

    <div class="container">
        <div class="section topup-section">
            <h1>เติมเงิน {{ $game->title }}</h1>
            <p>เลือกจำนวนเงินที่ต้องการเติมสำหรับเกมนี้</p>

            <div class="topup-options">
                <button class="topup-button">50 บาท</button>
                <button class="topup-button">100 บาท</button>
                <button class="topup-button">500 บาท</button>
            </div>

            <a href="{{ route('home') }}" class="back-button">กลับหน้าแรก</a>
        </div>
    </div>

</body>
</html>

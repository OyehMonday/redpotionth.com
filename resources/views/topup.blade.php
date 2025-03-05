<?php
use Illuminate\Support\Facades\Http;

// Function to get IP
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Get user IP
$userIP = getUserIP();

// Get IP details
$ipResponse = Http::get("https://ipinfo.io/{$userIP}/json");
$ipData = $ipResponse->json();
$country = $ipData['country'] ?? 'UNKNOWN'; 
$asn = $ipData['org'] ?? 'UNKNOWN'; 

// Game ID 5: Allow only Thailand IPs
// if ($game->id == 5 && $country !== 'TH') {
//     echo "<script>alert('Your IP is not in Thailand. Redirecting to Game ID 10...'); window.location.href = '".route('topup', ['game_id' => 10])."';</script>";
//     exit;
// }

// // Game ID 10: Allow only non-Thai IPs
// if ($gameId == 10 && $country === 'TH' && !$vpnDetected) {
//     echo "<script>alert('Your IP is in Thailand. Redirecting to Game ID 5...'); window.location.href = '".route('topup', ['game_id' => 5])."';</script>";
//     exit;
// }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เติม {{ $game->title }} {{ $game->description }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    <meta property="og:title" content="เติมเกม {{ $game->title }}">
    <meta property="og:description" content="{{ $game->description }}">
    <meta property="og:image" content="{{ url('images/' . $game->full_cover_image) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="เติมเกม {{ $game->title }}">
    <meta name="twitter:description" content="{{ $game->description }}">
    <meta name="twitter:image" content="{{ url('images/' . $game->full_cover_image) }}">  
</head>
<body class="dark-theme">
    @include('navbar')

    <div class="container">
        <div class="section topup-section">
            
            <div class="topup-header">
                <h1 class="topup-title">เติมเกม {{ $game->title }}</h1>
                <div class="topup-search">
                    <form action="{{ route('games.search') }}" method="GET" class="topup-search">
                        <input type="text" name="query" id="gameSearch" placeholder="ค้นหาเกม..." class="search-input">
                        <button type="submit" class="search-button">ค้นหา</button>
                    </form>
                </div>
            </div>
            <div style="margin-left: 0px; margin-bottom: 5px;">{{ $game->description }}</div>
          
            <div>
                <img src="{{ url('images/' . $game->full_cover_image) }}" class="full-cover-image" alt="{{ $game->name }}">
            </div>
            @if ($game->id == 5 && $country !== 'TH')
                <div class="topup-alert">
                    ⚠️ ระบบตรวจสอบพบว่าคุณอาจจะต้องเติม 
                    <a href="{{ route('games.topup', ['id' => 32]) }}" class="topup-link">PUBG Mobile (ต่างประเทศ)</a> ⚠️
                    <br><span class="topup-alertsub">ส่งไอดีมาตรวจสอบ ได้ก่อนที่ Line <a href="https://lin.ee/tHJwLONc" class="topup-link">@redpotionth</a></span>
                </div>
            @endif

            @if ($game->id == 32 && $country === 'TH')
                <div class="topup-alert">
                    ⚠️ ระบบตรวจสอบพบว่าคุณอาจจะสามารถเติม
                    <a href="{{ route('games.topup', ['id' => 5]) }}" class="topup-link">PUBG Mobile (ไอดีไทย)</a> ได้ เพื่อราคาที่ดีขึ้น ⚠️
                    <br><span class="topup-alertsub">ส่งไอดีมาตรวจสอบ ได้ก่อนที่ Line <a href="https://lin.ee/tHJwLONc" class="topup-link">@redpotionth</a></span>
                </div>
            @endif

            @if($game->category->name == 'Payment Link')
                <div class="paymentlink">
                    กรุณาติดต่อเรา เพื่อดำเนินการเติม<br>
                    <a href="https://lin.ee/tHJwLONc" target="_blank" class="user-link">
                        <img src="{{ asset('images/line-qr.png') }}" alt="Line" class="line-qr"><br>@redpotion
                    </a>
                </div>
            @else
                <h2 style="margin:10px 0 0 0;">เลือกแพ็กเกจที่ต้องการ เติมเงิน {{ $game->title }}</h2>

                <div class="topup-grid">
                    @foreach($packages as $package)
                        <div class="topupcard">
                        @if(!empty($package->cover_image)) 
                            <img src="{{ url('images/' . $package->cover_image) }}" class="topupcard-img" alt="{{ $package->name }}">
                        @else
                            <div class="topupcard-img">
                                <span style="font-size: 12px; color: #666;">No Image</span>
                            </div>
                        @endif
                            <div class="topupcard-body">
                                <h5 class="topupcard-title">{{ $package->name }}</h5>
                                <p class="topupcard-text">{{ $package->detail }}</p>
                                <p class="topupcard-price">
                                    <s class="old-price">{{ number_format($package->full_price, 0) }} บาท</s> <br>
                                    <strong class="new-price">{{ number_format($package->selling_price, 0) }} บาท</strong>
                                </p>
                                <form action="{{ route('game.cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                                    <button type="submit" class="topupcard-btn">
                                        <img src="{{ asset('images/cart.png') }}" class="cart-icon" alt="Cart"> เพิมใส่ตะกร้า
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <script>
        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }
    </script>    
    @include('footer')
</body>
</html>

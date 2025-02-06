<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">
                <h1>สรุปคำสั่งซื้อ</h1>
                <p class="placeholder">รายละเอียดคำสั่งซื้อของคุณ</p>
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @foreach($cart as $game_id => $game)
                    <div>
                        <div class="cart-container">
                            <div class="cart-left">
                                <div class="cart-gametitle">{{ $game['game_name'] }}</div>

                                @php
                                    $gameModel = \App\Models\Game::find($game_id);
                                @endphp

                                @if($gameModel && !empty($gameModel->cover_image))
                                    <a href="{{ url('/games/' . $game_id . '/topup') }}">
                                        <img src="{{ asset('storage/' . $gameModel->cover_image) }}" class="cart-gamecover" alt="{{ $game['game_name'] }}">
                                    </a>
                                @endif

                                <div id="lightbox" class="lightbox" onclick="closeLightbox(event)">
                                    <span class="lightbox-close" onclick="closeLightbox(event)">&times;</span>
                                    <img id="lightbox-img" class="lightbox-content" onclick="event.stopPropagation();">
                                </div>

                            </div>

                            <div class="cart-right">
                                @foreach($game['packages'] as $uniqueId => $package)
                                    <div class="cart-item">
                                        <div class="cart-details">
                                            <div class="topupcard-title">แพค : {{ $package['name'] }}</div>
                                            <div class="topupcard-text">{{ $package['detail'] ?? '' }}</div>
                                            <div class="cart-price">
                                                <s class="old-price">{{ number_format($package['full_price'], 0) }} บาท</s><br>
                                                <strong class="new-price">ราคา {{ number_format($package['price'], 0) }} บาท</strong>
                                            </div>
                                        </div>

                                        <div class="chout-actions">ID ผู้เล่น : {{ $package['player_id'] ?? 'ไม่ระบุ' }}</div>

                                        <div class="cart-remove"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach             
            </div>

            <div class="section topup-section">
                @php
                    $receiver = "0904450446"; 
                    $amount = 150.00;
                @endphp

                <img src="{{ url('/payment/qr/' . $receiver . '/' . $amount) }}" alt="PromptPay QR Code">
                
                <p>Amount to Pay: <strong>{{ number_format($amount, 2) }} THB</strong></p>                
            </div>
        </div>
    </div>

    <div class="container">
        <form action="" method="POST">
            @csrf
            <button type="submit" class="cart-btn">ยืนยันคำสั่งซื้อ</button>
        </form>
    </div>

    @include('footer')
</body>
</html>

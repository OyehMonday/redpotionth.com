<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมาชิก</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">
                <h1>คำสั่งซื้อของคุณ</h1>

                @if(session('success'))
                    <div class="alert alert-success" style="text-align: center;">{{ session('success') }}</div>
                @endif

                @if($orders->isEmpty())
                    <p>ยังไม่มีคำสั่งซื้อ</p>
                @else
                    <div class="orders-container">
                        @foreach($orders as $order)
                            <div class="order-card">
                                <div class="order-header">
                                    <div>
                                        <span class="order-title">หมายเลขคำสั่งซื้อ: #{{ $order->id }}</span><br>
                                        <span class="order-subheader">วันที่สั่งซื้อ: {{ $order->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="order-status">
                                        @if($order->status == '1')
                                            <span class="status pending">รอชำระเงิน</span>
                                        @elseif($order->status == '2')
                                            <span class="status review">รอชำระเงิน</span>
                                        @elseif($order->status == '3')
                                            <span class="status completed">รอตรวจสอบการชำระเงิน</span>
                                        @else
                                            <span class="status cancelled">ยกเลิก</span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                @if($order)
                                    @if(session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif

                                    @php
                                        $cartDetails = json_decode($order->cart_details, true);
                                    @endphp

                                    <div class="order-summary">
                                        @foreach($cartDetails as $game_id => $game)
                                            <div class="dash-container">
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
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach  
                                    </div>

                                @else
                                    <p>คำสั่งซื้อนี้ไม่พบ</p>
                                @endif              
                                </div>
                                
                                <div class="order-coins">
                                    <div class="coin-section">
                                        <div class="coin-item">
                                            ใช้ไป {{ number_format($order->used_coins ?? 0) }}<img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon">
                                        </div>
                                        <div class="coin-item">
                                            ได้รับ {{ number_format($order->coin_earned ?? 0) }}<img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon">
                                        </div>
                                    </div>
                                </div>

                                <div class="order-footer">
                                @php
                                    $finalAmount = max(0, $order->total_price - ($order->used_coins ?? 0));
                                @endphp
                                @if($order->status == '2')
                                    <div class="order-body">
                                        <p class="payamount">ยอดที่ต้องชำระ {{ number_format($finalAmount, 2) }} บาท</p>
                                    </div>
                                    <span><a href="{{ route('game.checkout.view', ['order_id' => $order->id]) }}" class="cart-btn" style="text-decoration: none;">ดำเนินการชำระเงิน</a></span>
                                @else
                                    <div class="order-body">
                                        <p class="payamount">ยอดชำระ {{ number_format($finalAmount, 2) }} บาท</p>
                                    </div>
                                @endif                                    
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
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

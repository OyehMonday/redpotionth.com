<div class="order-card">
    <div class="order-header">
        <div>
            <span class="order-title">หมายเลขคำสั่งซื้อ: #{{ $order->id }}</span><br>
            <span class="order-subheader">วันที่สั่งซื้อ: {{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="order-status">
            @if($order->status == '1')
                <span class="status pending">สถานะ : รอชำระเงิน</span>
            @elseif($order->status == '2')
                <span class="status review">สถานะ : รอชำระเงิน</span>
            @elseif($order->status == '3')
                <span class="status review">สถานะ : รอตรวจสอบการชำระเงิน</span><div style="text-align: right; margin-top:5px; font-size: 0.9em;">หากรอเกิน 20 นาที <a href="{{ route('contactus') }}" class="user-link">ติดต่อเรา</a></div>
            @elseif($order->status == '4')
                <span class="status completed">สถานะ : ทำรายการสำเร็จ</span>
            @elseif($order->status == '11')
                <span class="status review">สถานะ : อยู่ระหว่างดำเนินการ</span><div style="text-align: right; margin-top:5px; font-size: 0.9em;">หากรอเกิน 20 นาที <a href="{{ route('contactus') }}" class="user-link">ติดต่อเรา</a></div>
            @elseif($order->status == '99')
                <span class="status cancelled">สถานะ : ยกเลิก</span>
            @else
                <span class="status cancelled">สถานะ : ระหว่างตรวจสอบ</span>
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
                                    <img src="{{ asset('images/' . $gameModel->cover_image) }}" class="cart-gamecover" alt="{{ $game['game_name'] }}">
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า - Shopping Cart</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    <script>
        function openLightbox(imageSrc) {
            let lightbox = document.getElementById("lightbox");
            document.getElementById("lightbox-img").src = imageSrc;
            lightbox.style.display = "flex";
            setTimeout(() => {
                lightbox.classList.add("show");
            }, 10);
        }

        function closeLightbox(event) {
            let lightbox = document.getElementById("lightbox");

            if (event.target === lightbox || event.target.classList.contains("lightbox-close")) {
                lightbox.classList.remove("show");
                setTimeout(() => {
                    lightbox.style.display = "none";
                }, 300);
            }
        }
    </script>

</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">

                <h1>ตะกร้าสินค้า</h1>
                @if(empty($cart) || count($cart) == 0)
                    <p class="placeholder"></p>
                @else
                    <p class="placeholder">กรุณากรอก ID ผู้เล่นของคุณให้ถูกต้อง</p>
                @endif

                @if(session('success'))
                    <div class="alert alert-success" style="text-align: center;">{{ session('success') }}</div>
                @endif

                @php
                    $cart = session('cart', []);
                    $user = null;

                    if (Session::has('user')) {
                        $user = Session::get('user');
                        $existingOrder = \App\Models\Order::where('user_id', $user->id)
                                                        ->whereIn('status', ['1', '2'])
                                                        ->first();

                        if ($existingOrder) {
                            $cart = json_decode($existingOrder->cart_details, true);
                        }
                    }
                @endphp


                @if(empty($cart) || count($cart) == 0)
                    <p style="padding:50px 0;">ยังไม่มีสินค้าในตะกร้า</p>
                @else
                    <form action="{{ route('game.cart.update') }}" method="POST" onsubmit="return validateCart()">
                        <input type="hidden" name="use_coins" id="use-coins-input" value="0">
                        @csrf

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
                                                <img src="{{ asset('images/' . $gameModel->cover_image) }}" class="cart-gamecover" alt="{{ $game['game_name'] }}">
                                            </a>
                                        @endif

                                        @if($gameModel && !empty($gameModel->uid_image))
                                            <p style="margin-top:0px;">
                                                <a href="javascript:void(0);" onclick="openLightbox('{{ asset('images/' . $gameModel->uid_image) }}')" class="btn-info  uid-button">
                                                    วิธีดู UID
                                                </a>
                                            </p>
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

                                                <div class="cart-actions">
                                                    <div class="cart-uid">ID ผู้เล่น :
                                                        <input type="text"
                                                            placeholder="{{ $game['uid_detail'] ?? 'กรอก ID ผู้เล่นของคุณ' }}"
                                                            name="player_ids[{{ $game_id }}][{{ $uniqueId }}]"
                                                            value="{{ $package['player_id'] ?? '' }}"
                                                            required
                                                            class="form-control player-id">
                                                    </div>
                                                </div>

                                                <div class="cart-remove">
                                                    <a href="{{ route('game.cart.remove', ['game_id' => $game_id, 'package_id' => $uniqueId]) }}" class="btn btn-danger btn-sm">
                                                        <img src="{{ asset('images/remove.png') }}" class="remove-icon" alt="Remove">
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="cart-back">
                            <a href="javascript:history.back();" class="btn-back">← กลับไปก่อนหน้า</a>
                        </div>

                        <div class="cart-summary">
                            @php
                                $totalFullPrice = collect($cart)->pluck('packages')->flatten(1)->sum('full_price');
                                $totalSellingPrice = collect($cart)->pluck('packages')->flatten(1)->sum('price');
                                $totalDiscount = $totalFullPrice - $totalSellingPrice;
                            @endphp

                            @if($totalDiscount > 0)
                                <div class="cart-discount">
                                    ประหยัดไป <strong>{{ number_format($totalDiscount, 0) }}</strong> บาท
                                </div>
                            @endif   

                            @php
                                $coinsAvailable = $user ? \App\Models\User::where('id', $user['id'])->value('coins') : 0;
                                $totalAmount = collect($cart)->pluck('packages')->flatten(1)->sum('price');
                                $maxDiscount = floor($totalAmount * (env('COIN_DISCOUNT_LIMIT', 50) / 100)); 
                                $coinsToUse = min($coinsAvailable, $maxDiscount);
                                $coinConversionRate = env('COIN_CONVERSION_RATE', 100); 
                                $earnedCoins = floor(($totalAmount - $coinsToUse) / $coinConversionRate); 
                            @endphp

                            <div>
                                ยอดรวม {{ number_format($totalAmount, 2) }} บาท<br>

                                @if(Session::has('user')) 
                                    คุณมี <span id="coin-balance">{{ $coinsAvailable }}</span><img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon">
                                    
                                    @if($coinsAvailable > 0)
                                        <div class="coin-toggle">
                                            <label class="switch">
                                                <input type="checkbox" id="use-coins-toggle">
                                                <span class="slider round"></span>
                                            </label>
                                            <label for="use-coins-toggle">
                                                ใช้ <span id="coins-used">{{ $coinsToUse }}</span><img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon">
                                            </label>
                                        </div>
                                    @endif
                                @endif
                                <div class="cart-divider"></div>
                                <p class="payamount">ยอดที่ต้องชำระ <span id="final-amount">{{ number_format($totalAmount - $coinsToUse, 2) }}</span> บาท<br></p>
                                <p class="cart-coin">คุณจะได้รับ <span id="earned-coins">{{ $earnedCoins }}</span><img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon"></p>
                                <p><button type="submit" class="cart-btn">ชำระเงิน</button></p>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @include('footer')

    <script>
        function validateCart() {
            let playerInputs = document.querySelectorAll(".player-id");
            for (let input of playerInputs) {
                if (input.value.trim() === "") {
                    alert("กรุณากรอก ID ผู้เล่น ก่อนดำเนินการชำระเงิน!");
                    input.focus();
                    return false; 
                }
            }
            return true; 
        }
        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }

        document.addEventListener("DOMContentLoaded", function() {
            const toggle = document.getElementById("use-coins-toggle");
            const useCoinsInput = document.getElementById("use-coins-input");
            const coinsUsedEl = document.getElementById("coins-used");
            const finalAmountEl = document.getElementById("final-amount");
            const earnedCoinsEl = document.getElementById("earned-coins");

            const totalAmount = {{ $totalAmount ?? 0 }};
            const maxCoins = {{ $coinsToUse ?? 0 }};
            const coinConversionRate = {{ env('COIN_CONVERSION_RATE', 100) }};

            let usedCoins = maxCoins; 

            function updateCoinsUsage() {
                useCoinsInput.value = toggle.checked ? 1 : 0;
            }

            function updateAmount() {
                let newTotal = totalAmount;
                if (toggle.checked) {
                    newTotal -= usedCoins;
                }

                let earnedCoins = Math.floor(newTotal / coinConversionRate);

                finalAmountEl.textContent = newTotal.toFixed(2);
                earnedCoinsEl.textContent = earnedCoins;
            }

            document.querySelector("form").addEventListener("submit", function() {
                updateCoinsUsage();
            });

            toggle.addEventListener("change", function() {
                updateCoinsUsage();
                updateAmount();
            });

            updateCoinsUsage();
            updateAmount();
        });


    </script>
    
</body>
</html>

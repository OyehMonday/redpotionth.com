@php
    use App\Models\BusinessHour;

    $today = now()->format('l'); 
    $currentTime = now()->format('H:i');

    // Get today's business hours
    $businessHour = BusinessHour::where('day', $today)->first();

    $isOpen = false;

    if ($businessHour && $businessHour->open_time && $businessHour->close_time) {
        // Check if closing time is past midnight (next day)
        if ($businessHour->close_time < $businessHour->open_time) {
            // Store is open if current time is greater than open_time OR it's before close_time (past midnight case)
            $isOpen = ($currentTime >= $businessHour->open_time || $currentTime < $businessHour->close_time);
        } else {
            // Normal open-close logic (same day)
            $isOpen = ($currentTime >= $businessHour->open_time && $currentTime < $businessHour->close_time);
        }
    }

    // If store is closed, redirect to "closed" page
    if (!$isOpen) {
        header("Location: " . url('/closed'));
        exit();
    }
@endphp


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">

                @if($order)
                    @php
                        $cartDetails = json_decode($order->cart_details, true);
                        $totalAmount = 0;
                        $usedCoins = $order->used_coins ?? 0;

                        foreach ($cartDetails as $game) {
                            foreach ($game['packages'] as $package) {
                                $totalAmount += $package['price']; 
                            }
                        }

                        $finalAmount = $totalAmount - $usedCoins; 
                    @endphp                
                    <h1>หมายเลขคำสั่งซื้อ: #{{ $order->id }}</h1>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @foreach($cartDetails as $game_id => $game)
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
                @else
                <p>คำสั่งซื้อนี้ไม่พบ</p>
                @endif                              
            </div>
            <!-- qr section -->
            @if($order->status == "2") 
                <div class="section topup-section" style="text-align:center;">
                    กรุณาตรวจสอบ ID ผู้เล่น ให้ถูกต้อง ก่อนทำการชำระเงิน
                    <br><br>
                    @php
                        $receiver = "0105566013162"; 
                        $amount = collect($cartDetails)->pluck('packages')->flatten(1)->sum('price');

                        $checkoutUrl = route('game.checkout.view', ['order_id' => $order->id]);
                        if (!Session::has('user')) {
                            $checkoutUrl = route('custom.login.form') . '?redirect_to=' . urlencode($checkoutUrl);
                        }

                    @endphp

                    <img src="{{ url('/payment/qr/' . $receiver . '/' . number_format($finalAmount, 0, '', '')) }}" alt="PromptPay QR Code" class="qrcode">

                    @if($usedCoins > 0)
                        <p class="cart-coin">ใช้คอยน์ <strong>{{ $usedCoins }}</strong><img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon"></p>
                    @endif
                    <p class="payamount">ยอดที่ต้องชำระ {{ number_format($finalAmount, 2) }} บาท</p>  
                    @if($order->coin_earned > 0)
                        <p class="cart-coin">คุณจะได้รับ <strong>{{ $order->coin_earned }}</strong> <img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon"></p>
                    @endif
                    <form action="{{ route('game.payment.confirm', ['order_id' => $order->id]) }}" method="POST" enctype="multipart/form-data" id="payment-form">
                        @csrf

                        <div class="file-input-container">
                            <label for="payment_slip" class="paymentslip-btn">แนบสลิปการชำระเงิน</label>
                            <input type="file" name="payment_slip" id="payment_slip" accept="image/jpeg, image/png, application/pdf" class="hidden-file-input">
                            <span id="file-name">ยังไม่ได้เลือกไฟล์</span>
                        </div>

                        <p id="file-error" style="color: red; display: none;">กรุณาแนบสลิป ก่อนดำเนินการต่อ</p>


                        <button type="submit" class="cart-btn" id="submit-button">ดำเนินการต่อ</button>
                    </form>

                    <a href="{{ $checkoutUrl }}" class="conmobile">คลิกที่นี่ หากต้องการแนบสลิปจากมือถือ</a>

                    <div id="qrCodeModal" class="qr-code-modal">
                        <span class="close">&times;</span>
                        <div class="qr-code-modal-content">
                            <div id="qrCodeContainer"></div>
                        </div>
                    </div>
                </div>
            @endif   
            <!-- qr section -->

        </div>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }
                  
        var modal = document.getElementById("qrCodeModal");
        var closeButton = document.getElementsByClassName("close")[0];
        var qrLink = document.querySelector(".conmobile");

        qrLink.addEventListener('click', function(e) {
            e.preventDefault(); 

            modal.classList.add("show");

            document.getElementById("qrCodeContainer").innerHTML = "";

            var currentPageURL = window.location.href; 

            var qrCode = new QRCode(document.getElementById("qrCodeContainer"), {
            text: currentPageURL,
            width: 300,
            height: 300,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
            });
        });

        closeButton.onclick = function() {
            modal.classList.remove("show");
        }

        window.onclick = function(event) {
            if (event.target == modal) {
            modal.classList.remove("show");
            }
        }

        document.getElementById("payment_slip").addEventListener("change", function() {
            var fileInput = this.files[0];
            var fileNameDisplay = document.getElementById("file-name");
            var errorMessage = document.getElementById("file-error");

            if (fileInput) {
                fileNameDisplay.textContent = fileInput.name;
                errorMessage.style.display = "none"; 
            } else {
                fileNameDisplay.textContent = "ยังไม่ได้เลือกไฟล์";
                errorMessage.style.display = "block"; 
            }
        });

        document.getElementById("payment-form").addEventListener("submit", function(event) {
            var fileInput = document.getElementById("payment_slip").files[0];
            var errorMessage = document.getElementById("file-error");

            if (!fileInput) {
                errorMessage.style.display = "block"; 
                event.preventDefault();
            }
        });

    </script>
    @include('footer')
</body>
</html>

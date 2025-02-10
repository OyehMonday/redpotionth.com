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
                    <h1>เลขที่คำสั่งซื้อ: #{{ $order->id }}</h1>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @php
                        $cartDetails = json_decode($order->cart_details, true);
                    @endphp

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

                <img src="{{ url('/payment/qr/' . $receiver . '/' . number_format($amount, 0, '', '')) }}" alt="PromptPay QR Code" class="qrcode">
                
                <p style="color:red;">ยอดที่ต้องชำระ : <strong>{{ number_format($amount, 2) }} บาท</strong></p>  
                <form action="{{ route('game.payment.upload', ['order_id' => $order->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <label for="payment_slip" class="file-label"></label>
                <div class="file-input-container">
                    <input type="file" name="payment_slip" id="payment_slip" accept="image/jpeg, image/png, application/pdf" required>
                    <div><label for="payment_slip" class="cart-btn">แนบสลิปการชำระเงิน</label></div>
                    <div><span id="file-name">&nbsp;</span></div>
                </div>

                <button type="submit" class="cart-btn">ดำเนินการต่อ</button>
            </form>


                <a href="{{ $checkoutUrl }}" class="conmobile">คลิกที่นี่ หากต้องการ ดำเนินการแนบสลิปต่อ ในมือถือ</a>

                <div id="qrCodeModal" class="qr-code-modal">
                    <span class="close">&times;</span>
                    <div class="qr-code-modal-content">
                        <div id="qrCodeContainer"></div>
                    </div>
                </div>

            </div>

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
            var fileName = this.files[0] ? this.files[0].name : "ยังไม่ได้เลือกไฟล์";
            document.getElementById("file-name").textContent = fileName;
        });

    </script>
    @include('footer')
</body>
</html>

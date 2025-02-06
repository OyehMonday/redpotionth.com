<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    @include('navbar')

    <div class="container">
        <h1>ตะกร้าสินค้า</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(empty($cart) || count($cart) == 0)
            <p>Your cart is empty.</p>
        @else
            <form action="{{ route('game.cart.update') }}" method="POST" onsubmit="return validateCart()">
                @csrf

                @foreach($cart as $game_id => $game)
                    <div>
                        <div class="cart-container">
                            <!-- ✅ First Column: Game Title & Cover Image (Left Side) -->
                            <div class="cart-left">
                                <div class="cart-gametitle">{{ $game['game_name'] }}</div>

                                @php
                                    $gameModel = \App\Models\Game::find($game_id);
                                @endphp

                                @if($gameModel && !empty($gameModel->cover_image))
                                    <img src="{{ asset('storage/' . $gameModel->cover_image) }}" class="cart-gamecover" alt="{{ $game['game_name'] }}">
                                @endif

                                @if($gameModel && !empty($gameModel->uid_image))
                                    <p>
                                        <a href="{{ asset('storage/' . $gameModel->uid_image) }}" target="_blank" class="btn-info">
                                            วิธีหา UID
                                        </a>
                                    </p>
                                @endif
                            </div>

                            <!-- ✅ Second Column: Package Details, Player ID, Remove Link (Right Side) -->
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
                                                <input type="text" placeholder="{{ $game['uid_detail'] ?? 'กรอก ID ผู้เล่นของคุณ' }}" 
                                                       name="player_ids[{{ $game_id }}]" 
                                                       value="{{ $game['player_id'] }}" 
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

                <!-- ✅ Back Link -->
                <div class="cart-back">
                    <a href="javascript:history.back();" class="btn-back">← กลับไปก่อนหน้า</a>
                </div>

                <!-- ✅ Cart Summary -->
                <div class="cart-summary">
                    <div>
                        <strong>ยอดรวม : </strong>
                        <span class="cart-total">{{ number_format(collect($cart)->pluck('packages')->flatten(1)->sum('price'), 0) }} บาท</span>
                    </div>
                    <button type="submit" class="btn btn-sm">ชำระเงิน</button>
                </div>
            </form>
        @endif
    </div>

    @include('footer')

    <!-- ✅ JavaScript for Validation -->
    <script>
        function validateCart() {
            let playerInputs = document.querySelectorAll(".player-id");
            for (let input of playerInputs) {
                if (input.value.trim() === "") {
                    alert("⚠️ กรุณากรอก ID ผู้เล่น ก่อนดำเนินการชำระเงิน!");
                    input.focus();
                    return false; 
                }
            }
            return true; 
        }
    </script>
</body>
</html>

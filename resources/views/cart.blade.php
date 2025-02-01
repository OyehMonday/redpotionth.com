<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Potion</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    <!-- Navigation Menu -->
    @include('navbar')

    <div class="container">
        <div class="section topup-section">
            <h1 style="margin: 0px 0px 20px 0px;">ตะกร้า</h1>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(count($cart) == 0)
                <p>Your cart is empty.</p>
            @else
            <form action="{{ route('game.cart.update') }}" method="POST">
                @csrf

                @foreach($cart as $game_id => $game)
                    @php
                        $gameModel = \App\Models\Game::find($game_id);
                    @endphp

                    <div class="cart-game">
                        <!-- Game Title -->
                        <div class="cart-header">{{ $game['game_name'] }}</div>

                        <!-- Game Cover -->
                        @if($gameModel && !empty($gameModel->cover_image))
                            <div class="cart-game-cover">
                                <img src="{{ asset('storage/' . $gameModel->cover_image) }}" class="cart-gamecover" alt="{{ $game['game_name'] }}">
                            </div>
                        @endif

                        @foreach($game['packages'] as $package_id => $package)
                            <div class="cart-item">
                                <!-- Package Details -->
                                <div class="cart-details">
                                    <div>แพคที่เลือก</div>
                                    <div><strong>{{ $package['name'] }}</strong></div>
                                    <div><strong>{{ $package['detail'] }}</strong></div>
                                </div>

                                <!-- Price Details -->
                                <div class="cart-details">
                                    <p>
                                        <s class="old-price">{{ number_format($package['full_price'], 0) }} THB</s> <br>
                                        <strong class="new-price">{{ number_format($package['price'], 0) }} THB</strong>
                                    </p>
                                </div>

                                <!-- Player ID Input -->
                                <div class="cart-actions">
                                    <label>ID ผู้เล่น (Player ID):</label>
                                    <input type="text" name="player_ids[{{ $game_id }}]" value="{{ $game['player_id'] }}" required>
                                </div>

                                <!-- Remove Button -->
                                <div class="cart-actions">
                                    <form action="{{ route('game.cart.remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="game_id" value="{{ $game_id }}">
                                        <input type="hidden" name="package_id" value="{{ $package_id }}">
                                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary">Update Cart</button>
            </form>
            <form action="{{ route('game.cart.clear') }}" method="POST" style="margin-top: 15px;">
                @csrf
                <button type="submit" class="btn btn-danger">Clear Cart</button>
            </form>
            @endif
        </div>
    </div>

    @include('footer')
</body>
</html>

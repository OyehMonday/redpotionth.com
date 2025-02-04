<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Up - {{ $game->title }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body class="dark-theme">
    @include('navbar')

    <div class="container">
        <div class="section topup-section">
            <h1 style="margin-left: 0px;">เติมเกม {{ $game->title }}</h1>

            <div>
                <img src="{{ url('storage/' . $game->full_cover_image) }}" class="full-cover-image" alt="{{ $game->name }}">
            </div>
            <h2>เลือกแพ็กเกจที่ต้องการ เติมเงิน {{ $game->title }}</h2>

            <!-- Package Selection -->
            <div class="topup-grid">
                @foreach($packages as $package)
                    <div class="topupcard">
                    @if(!empty($package->cover_image)) 
                        <img src="{{ url('storage/' . $package->cover_image) }}" class="topupcard-img" alt="{{ $package->name }}">
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

        </div>
    </div>
    @include('footer')
</body>
</html>

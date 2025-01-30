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

    <div class="main-wrapper">
        <div class="container">

            <!-- Section 1: Game Selection -->
            <div class="section topup-section">
                <h1>Red Potion รับเติมเกม</h1>
                <p class="placeholder">บริการเติมเกม ผ่านทางระบบ UID เติมง่าย เข้าไว มีหลายเกมให้เลือก PUBG, Arena Breakout, Free Fire, ROV และเกมอื่นๆอีกมากมาย</p>
                <div class="game-grid">
                    @foreach($games as $game)
                        <div class="game-card">
                            <img src="{{ asset('storage/' . $game->cover_image) }}" alt="{{ $game->title }}" class="game-cover">
                            <h3 class="game-title">{{ $game->title }}</h3>
                            <a href="{{ route('games.topup', $game->id) }}" class="topup-button">เติมเงิน</a>
                        </div>
                    @endforeach
                </div>
            </div>
            <hr class="divider">

            <!-- Section 2: How It Works -->

        </div>
    </div>


    <script>
        // Toggle menu for mobile view
        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }
    </script>
</body>
</html>

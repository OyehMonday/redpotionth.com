<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Potion - บริการเติมเกม เติมง่าย เข้าไว มีหลายเกมให้เลือก PUBG, Arena Breakout, Free Fire, ROV</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">
                <div class="topup-header">
                    <h1 class="topup-title">Red Potion รับเติมเกม</h1>
                    
                    <div class="topup-search">
                        <form action="{{ route('games.search') }}" method="GET" class="topup-search">
                            <input type="text" name="query" id="gameSearch" placeholder="ค้นหาเกม..." class="search-input">
                            <button type="submit" class="search-button">ค้นหา</button>
                        </form>
                    </div>
                </div>
                <p class="placeholder">บริการเติมเกม ผ่านทางระบบ UID เติมง่าย เข้าไว มีหลายเกมให้เลือก PUBG, Arena Breakout, Free Fire, ROV และเกมอื่นๆอีกมากมาย</p>
                
                <div class="carousel-wrapper" style="margin-bottom:15px;">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            @foreach($carouselGames as $game)
                                <div class="swiper-slide">
                                    <a href="{{ route('games.topup', $game->id) }}">
                                        <img src="{{ asset('images/' . $game->full_cover_image) }}" alt="{{ $game->title }}" class="carousel-image">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>

                <div class="allgame-grid">
                    @foreach($games as $game)
                        <div class="game-card">
                            <a href="{{ route('games.topup', $game->id) }}" class="topup-card">
                                <img src="{{ asset('images/' . $game->cover_image) }}" alt="{{ $game->title }}" class="game-cover">
                                <h3 class="game-title">{{ $game->title }}</h3>
                                <div class="topup-button">เติมเงิน</div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }

        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });        
    </script>
    @include('footer')
</body>
</html>

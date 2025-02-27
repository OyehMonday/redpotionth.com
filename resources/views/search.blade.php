<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหาเกม "{{ $query }}" - Red Potion</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">
                
                <div class="topup-header">
                    <h1 class="topup-title">ผลการค้นหา: "{{ $query }}"</h1>
                    
                    <div class="topup-search">
                        <form action="{{ route('games.search') }}" method="GET" class="topup-search">
                            <input type="text" name="query" id="gameSearch" 
                                placeholder="ค้นหาเกม... ({{ $query }})" 
                                value="{{ $query }}" 
                                class="search-input">
                            <button type="submit" class="search-button">ค้นหา</button>
                        </form>
                    </div>
                </div>

                @if($games->isEmpty())
                    <p class="text-center">ไม่พบเกมที่ค้นหา</p>
                @else
                    <div class="game-grid">
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

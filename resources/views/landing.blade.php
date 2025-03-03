<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Potion - บริการเติมเกม เติมง่าย เข้าไว มีหลายเกมให้เลือก PUBG, Arena Breakout, Free Fire, ROV</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">

            <!-- Section 1: Top Up -->
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
                <div class="game-grid">
                    @foreach($games as $game)
                        <div class="game-card">
                            <a href="{{ route('games.topup', $game->id) }}" class="topup-card">
                                <img src="{{ asset('images/' . $game->cover_image) }}" alt="{{ $game->title }}" class="game-cover">
                                <h3 class="game-title">{{ $game->title }}</h3>
                                <div class="topup-button">เลือกแพค</div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="view-all-container">
                    <a href="{{ route('games.all') }}" class="view-all-link">เกมทั้งหมด</a>
                </div>
            </div>
            
            <hr class="divider">

            <!-- Section 2: Review -->
            <div class="section review-section">
                <h2>รีวิวจากลูกค้า</h2>
                <div id="comment-container" class="comment-container">
                    <p class="loading-text">กำลังโหลดรีวิว...</p>
                </div>
                <a id="view-all-reviews" href="https://www.facebook.com/share/p/14pZYS8HrF/" target="_blank" class="view-all-btn" style="display: none;">ดูรีวิวทั้งหมด</a>                
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {
                    $.ajax({
                        url: "{{ route('fetch.facebook.comments') }}",
                        method: "GET",
                        success: function(response) {
                            $("#comment-container").empty();

                            if (response.comments.length > 0) {
                                response.comments.forEach(comment => {
                                    let commentHtml = `
                                        <div class="comment-box">
                                            <div class="comment-header">
                                                <img src="${comment.profile_image}" class="profile-img" alt="User">
                                                <div class="comment-info">
                                                    <span class="comment-user">${comment.user_name}</span>
                                                    <span class="comment-time">${comment.formatted_time}</span>
                                                </div>
                                            </div>
                                            <p class="comment-text">${comment.message}</p>
                                        </div>`;
                                    $("#comment-container").append(commentHtml);
                                });

                                $("#view-all-reviews").show();
                            } else {
                                $("#comment-container").html('<p class="no-comments">ยังไม่มีรีวิวจากลูกค้า</p>');
                                $("#view-all-reviews").hide(); 
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching comments:", xhr.responseText);
                            $("#comment-container").html('<p class="no-comments">ไม่สามารถโหลดรีวิวได้</p>');
                            $("#view-all-reviews").hide();
                        }
                    });
                });
            </script>
            
            <!-- Section 3: Highlight -->
            <div class="section topup-section">
                <h1>แพคแนะนำ</h1>

                <div class="highlighted-carousel">
                    <button class="carousel-btn left" onclick="scrollCarousel('left')">&#10094;</button>
                    <div class="carousel-wrapper">
                        <div class="carousel-track">
                            @foreach ($highlightedPackages as $package)
                                <div class="carouselcard">
                                    <h3 class="game-title">{{ $package->game->title ?? 'Unknown Game' }}</h3>
                                    @if(!empty($package->game->cover_image)) 
                                        <img src="{{ asset('images/' . $package->game->cover_image) }}" class="carousel-img" alt="{{ $package->game->title }}">
                                    @else
                                        <div class="topupcard-img">
                                            <span style="font-size: 12px; color: #666;">No Image</span>
                                        </div>
                                    @endif
                                    <div class="topupcard-body">
                                        
                                        <p class="topupcard-title">{{ $package->name }}</p>
                                        <p class="topupcard-text">{{ $package->detail }}</p>
                                        <p class="topupcard-price">
                                            <s class="old-price">{{ number_format($package->full_price, 0) }} บาท</s> <br>
                                            <strong class="new-price">{{ number_format($package->selling_price, 0) }} บาท</strong>
                                        </p>
                                        <form action="{{ route('game.cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                                            <button type="submit" class="topupcard-btn">
                                                <img src="{{ asset('images/cart.png') }}" class="cart-icon" alt="Cart"> เพิ่มใส่ตะกร้า
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button class="carousel-btn right" onclick="scrollCarousel('right')">&#10095;</button>
                </div>
            </div>


        </div>
    </div>

    <script>
        function scrollCarousel(direction) {
            const track = document.querySelector('.carousel-track');
            const scrollAmount = document.querySelector('.carouselcard').offsetWidth * 3; 

            if (direction === 'left') {
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }

        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }
    </script>
    @include('footer')
</body>
</html>

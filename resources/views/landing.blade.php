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
                            <a href="{{ route('games.topup', $game->id) }}" class="topup-button">เลือกแพ็ก</a>
                        </div>
                    @endforeach
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

                                // Show "ดูรีวิวทั้งหมด" button after comments load
                                $("#view-all-reviews").show();
                            } else {
                                $("#comment-container").html('<p class="no-comments">ยังไม่มีรีวิวจากลูกค้า</p>');
                                $("#view-all-reviews").hide(); // Hide if no comments
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
        </div>
    </div>


    <script>
        // Toggle menu for mobile view
        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }
    </script>
    @include('footer')
</body>
</html>

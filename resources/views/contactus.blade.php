<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Red Potion</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">
                <h1 class="topup-title">ติดต่อเรา</h1>
                
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

                <div class="contact-channels">
                    <div class="contact-item">
                        ขอบคุณที่เข้ามาเลือกซื้อสินค้าดิจิตอลจากเว็บไซต์ของเรา (redpotionth.com) หากต้องการความช่วยเหลือ หรือสอบถามสินค้า/บริการ เพิ่มเติม สามารถติดต่อได้ตามช่วงทางต่อไปนี้
                    </div>
                    <div class="contact-item">
                        <div class="social-buttons">
                            <a href="http://m.me/redpotiontopup" class="contact-button">Facebook Inbox</a>
                            <a href="https://lin.ee/tHJwLONc" class="contact-button">Line : @redpotionth</a>
                            <a href="mailto:redpotionth@gmail.com" class="contact-button">Email : redpotionth@gmail.com</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        เพื่อความพึงพอใจของลูกค้า เว็บไซต์ของเราจะมีเจ้าหน้าที่ให้บริการตลอดเวลาให้บริการ โดยมีเวลาให้บริการดังต่อไปนี้
                    </div>
                    @php
                        use App\Models\BusinessHour;

                        // Map English days to Thai days
                        $thaiDays = [
                            'Monday'    => 'วันจันทร์',
                            'Tuesday'   => 'วันอังคาร',
                            'Wednesday' => 'วันพุธ',
                            'Thursday'  => 'วันพฤหัสบดี',
                            'Friday'    => 'วันศุกร์',
                            'Saturday'  => 'วันเสาร์',
                            'Sunday'    => 'วันอาทิตย์'
                        ];

                        // Fetch business hours for all 7 days
                        $businessHours = BusinessHour::orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();

                        // Prepare a formatted list of open-close times
                        $businessHoursList = [];

                        foreach ($businessHours as $day) {
                            $thaiDay = $thaiDays[$day->day] ?? $day->day; // Convert to Thai

                            if ($day->open_time == '00:00:00' && $day->close_time == '23:59:00') {
                                $businessHoursList[$thaiDay] = "เปิด 24 ชั่วโมง";
                            } elseif ($day->open_time && $day->close_time) {
                                // Check if closing time is past midnight
                                if ($day->close_time < $day->open_time) {
                                    $businessHoursList[$thaiDay] = date('H:i a', strtotime($day->open_time)) . " - " . date('H:i a', strtotime($day->close_time));
                                } else {
                                    $businessHoursList[$thaiDay] = date('H:i a', strtotime($day->open_time)) . " - " . date('H:i a', strtotime($day->close_time));
                                }
                            } else {
                                $businessHoursList[$thaiDay] = "เวลาทำการไม่ระบุ";
                            }
                        }
                    @endphp

                    <div class="contact-item">
                        <ul class="business-hours-list">
                            @foreach($businessHoursList as $day => $time)
                                <li><strong>{{ $day }}:</strong> {{ $time }}</li>
                            @endforeach
                        </ul>
                    </div>            
                </div>
            </div>
        </div>
    </div>

    <script>
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
        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }            
    </script>
    @include('footer')
</body>
</html>

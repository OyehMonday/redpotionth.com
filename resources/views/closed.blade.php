@php
    use App\Models\BusinessHour;

    $today = now()->format('l'); 
    $currentTime = now()->format('H:i');

    $daysInThai = [
        'Sunday' => 'อาทิตย์',
        'Monday' => 'จันทร์',
        'Tuesday' => 'อังคาร',
        'Wednesday' => 'พุธ',
        'Thursday' => 'พฤหัสบดี',
        'Friday' => 'ศุกร์',
        'Saturday' => 'เสาร์'
    ];

    $businessHours = BusinessHour::orderByRaw("
        FIELD(day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')
    ")->get();

    $nextOpenDay = null;
    $nextOpenTime = null;
    $foundNextOpen = false;
    $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    $currentDayIndex = array_search($today, $daysOfWeek);

    for ($i = 0; $i < count($daysOfWeek); $i++) {
        $checkDayIndex = ($currentDayIndex + $i) % count($daysOfWeek); 
        $checkDay = $daysOfWeek[$checkDayIndex];

        $hour = $businessHours->where('day', $checkDay)->first();

        if ($hour && $hour->open_time) {
            if ($checkDay == $today && $currentTime >= $hour->close_time) {
                continue;
            }

            $nextOpenDay = $daysInThai[$checkDay]; // Convert to Thai
            $nextOpenTime = date('H:i', strtotime($hour->open_time));
            break;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ร้านอยู่นอกเวลาทำการ</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">
                <h1>ร้านอยู่นอกเวลาทำการ</h1><br>
                <p>ขออภัย ขณะนี้ร้านอยู่นอกเวลาทำการ</p>

                @if($nextOpenDay && $nextOpenTime)
                    <p>กรุณากลับมาใหม่อีกครั้งใน วัน{{ __($nextOpenDay) }} เวลา {{ $nextOpenTime }}</p>
                @else
                    <p>กรุณากลับมาใหม่อีกครั้งในวันพรุ่งนี้</p>
                @endif
                <br><br>
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

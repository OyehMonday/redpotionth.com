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

            <!-- First Part: Red Potion Top Up -->
            <div class="section topup-section" id="topup">
                <h1>รับเติมเกม</h1>
                <p class="placeholder">บริการเติมเกม ผ่านทางระบบ UID เติมง่าย เข้าไว มีหลายเกมให้เลือก PUBG, Arena Breakout, Free Fire, ROV และเกมอื่นๆอีกมากมาย</p>
            </div>
            <hr class="divider">
            
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

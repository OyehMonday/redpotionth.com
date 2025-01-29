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
    <nav class="navbar">
        <div class="navbar-container">
            <!-- Brand Logo -->
            <a href="/" class="navbar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="RedPotion" class="navbar-logo">
            </a>
            <!-- Mobile Toggle -->
            <button class="navbar-toggle" onclick="toggleMenu()">☰</button>
            <ul class="navbar-menu" id="navbarMenu">
                <li><a href="#topup">เติมเกม</a></li>
                <li><a href="#market">ตลาดกลาง</a></li>
                <li>
                    @if(session()->has('user'))
                        <!-- User's Name -->
                        <a href="/dashboard" class="navbar-user">{{ session('user')->username }}</a>
                        <!-- Logout Link -->
                        <!-- <a href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a> -->
                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <!-- Login Link -->
                        <a href="/login">สมาชิก</a>
                    @endif
                </li>
            </ul>
        </div>
    </nav>




<!-- Hidden Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>


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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RedPotionTH - Layout Update</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    <!-- Navigation Menu -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="/" class="navbar-brand"><img src="{{ asset('images/logo.png') }}" alt="RedPotion" class="navbar-logo"></a>
            <button class="navbar-toggle" onclick="toggleMenu()">
                ☰
            </button>
            <ul class="navbar-menu" id="navbarMenu">
                <li><a href="#topup">เติมเกม</a></li>
                <li><a href="#market">ตลาดกลาง</a></li>
                <li><a href="#contact">สมาชิก</a></li>
            </ul>
        </div>
    </nav>

    <div class="main-wrapper">
        <div class="container">

            <!-- First Part: Red Potion Top Up -->
            <div class="section topup-section" id="topup">
                <h1>Red Potion รับเติมเกม</h1>
                <p class="placeholder">This section will have content added later.</p>
            </div>
            <hr class="divider"> <!-- Horizontal divider between the two sections -->
            
            <!-- Second Part: Red Potion Market -->
            <div class="section market-section" id="market">
                <h1>Red Potion ตลาดกลาง</h1>
                <h2 class="subheader with-line">ตลาดกลางสำหรับ การซื้อ-ขายไอเทมและบริการในเกม</h2>
                <p>คุณเป็นผู้ซื้อ หรือ ผู้ขาย</p>
                <a href="/seller">ผู้ขาย</a>
                <a href="/buyer">ผู้ซื้อ</a>
            </div>
        </div>
        <p class="description-text">
            Red Potion Market ช่วยให้การซื้อขายไอเทมและบริการในเกมปลอดภัยยิ่งขึ้น โดยทำหน้าที่เป็นตัวกลางที่คุณไว้ใจได้
        </p>
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

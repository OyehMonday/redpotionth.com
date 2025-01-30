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
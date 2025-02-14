<nav class="navbar">
    <div class="navbar-container">
        <a href="/" class="navbar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="RedPotion" class="navbar-logo">
        </a>
        <button class="navbar-toggle" onclick="toggleMenu()">☰</button>
        <ul class="navbar-menu" id="navbarMenu">
            <li><a href="{{ route('games.index') }}">จัดการเกม</a></li>
            <li><a href="{{ route('admin.orders.index') }}">จัดการคำสั่งซื้อ</a></li>
            <li>
                @if(auth()->guard('admin')->check())  
                    
                    <a href="{{ route('admin.dashboard') }}" class="navbar-user">{{ auth()->guard('admin')->user()->name }}</a>
                    <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('admin.login') }}">Admin Login</a>
                @endif
            </li>
        </ul>
    </div>
</nav>

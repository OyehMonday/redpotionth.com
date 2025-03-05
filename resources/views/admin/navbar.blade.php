<nav class="navbar">
    <div class="navbar-container">
        <a href="/" class="navbar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="RedPotion" class="navbar-logo">
        </a>
        <button class="navbar-toggle" onclick="toggleMenu()">☰</button>
        <ul class="navbar-menu" id="navbarMenu">
            @php
                $superAdminIds = explode(',', env('SUPER_ADMIN_IDS', ''));
                $superAdminIds = array_map('trim', $superAdminIds); 
            @endphp

            @if(in_array(auth()->guard('admin')->user()->id ?? 0, $superAdminIds))
                <li><a href="{{ route('games.index') }}">จัดการเกม</a></li>
                <li><a href="{{ route('admin.game-packages.index') }}">แพคไฮไลท์</a></li>
                <li><a href="{{ route('admin.business-hours.index') }}">เวลาเปิด-ปิด</a></li>
                <li><a href="{{ route('admin.orders.index') }}">คำสั่งซื้อ</a></li>
            @else
            <li><a href="{{ route('admin.orders.index') }}">คำสั่งซื้อ</a></li>
            @endif
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

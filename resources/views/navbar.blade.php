<nav class="navbar">
    <div class="navbar-container">
        
        <a href="{{ url('/') }}" class="navbar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="RedPotion" class="navbar-logo">
        </a>

        <div class="navbar-right">
            <div class="nav-cart">
                <a href="{{ route('game.cart.view') }}" class="cart-link">
                <img src="{{ asset('images/cart.png') }}" alt="Cart" class="cart-icon">
                @php
                    $cart = session('cart', []);
                    if (empty($cart) && Session::has('user')) {
                        $user = Session::get('user');
                        $existingOrder = \App\Models\Order::where('user_id', $user->id)->where('status', '1')->latest()->first();
                        if ($existingOrder) {
                            $cart = json_decode($existingOrder->cart_details, true);
                            session()->put('cart', $cart);
                        }
                    }
                    $cartItemCount = collect($cart)->pluck('packages')->flatten(1)->count();
                @endphp
                @if($cartItemCount > 0)
                    <span class="cart-badge">{{ $cartItemCount }}</span>
                @endif
                </a>
            </div>
            <button class="navbar-toggle" onclick="toggleMenu()">☰</button>
        </div>

        <ul class="navbar-menu" id="navbarMenu">
            <li><a href="/">หน้าแรก</a></li>
            <li><a href="{{ route('games.all') }}">เติมเกม</a></li>

            <li>
                @if(Session::has('user'))
                    <a href="{{ route('dashboard') }}">{{ Session::get('user')->username }}</a>
                @else
                    <a href="{{ route('custom.login.form') }}">สมาชิก</a>
                @endif
            </li>
            
            <li class="nav-cart-desktop">
                <a href="{{ route('game.cart.view') }}" class="cart-link">
                    <img src="{{ asset('images/cart.png') }}" alt="Cart" class="cart-icon">
                    @if($cartItemCount > 0)
                        <span class="cart-badge">{{ $cartItemCount }}</span>
                    @endif
                </a>
            </li>

        </ul>
    </div>
</nav>

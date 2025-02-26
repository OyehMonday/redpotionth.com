<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders by {{ $user->username }}</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <script>
        window.Laravel = { csrfToken: '{{ csrf_token() }}' };
      
        window.onload = function() {
            if (!sessionStorage.getItem("reloaded")) {
                sessionStorage.setItem("reloaded", "true");
                location.reload();
            } else {
                sessionStorage.removeItem("reloaded"); 
            }
        };
    </script>
</head>
<body>
    @include('admin.navbar')

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">
                <h1 style="margin-bottom:0px;">คำสั่งซื้อของ {{ $user->username }}</h1> อีเมล {{ $user->email ?? 'N/A' }}

                <div class="orders-container" style="margin-top:15px;">
                    @foreach ($orders as $order)
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <a href="{{ url('/admin/orders/' . $order->id . '/details') }}" target="_blank" class="order-titlelink">
                                        หมายเลขคำสั่งซื้อ: #{{ $order->id }}
                                    </a><br>
                                    <span class="order-subheader">
                                        วันที่สั่งซื้อ: {{ $order->created_at->format('n/j/Y, g:i:s A') }}
                                    </span>
                                </div>
                                <div class="order-status">
                                    {!! getOrderStatus($order->status) !!} 
                                    {!! getAdminActionPHP($order) !!}
                                </div>
                            </div>

                            <div class="order-summary">
                                @foreach (json_decode($order->cart_details, true) as $gameId => $game)
                                    <div class="dash-container">
                                        <div class="cart-left">
                                            <div class="cart-gametitle">{{ $game['game_name'] }}</div>
                                        </div>
                                        <div class="cart-right">
                                            @foreach ($game['packages'] as $package)
                                                <div class="cart-item">
                                                    <div class="cart-details">
                                                        <div class="topupcard-title">
                                                            แพค : {{ $package['name'] }}
                                                            <span class="topupcard-text">{{ $package['detail'] ?? '' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="cart-price">
                                                        <strong class="new-price">ราคา {{ number_format($package['price'], 2) }} บาท</strong>
                                                    </div>
                                                    <div class="chout-actions">
                                                        ID ผู้เล่น : {{ $package['player_id'] ?? 'ไม่ระบุ' }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="order-coins">
                                <div class="coin-section">
                                    <div class="coin-item">
                                        ใช้ไป {{ number_format($order->used_coins ?? 0) }}
                                        <img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon">
                                    </div>
                                    <div class="coin-item">
                                        ได้รับ {{ number_format($order->coin_earned ?? 0) }}
                                        <img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon">
                                    </div>
                                </div>
                            </div> 
                            <div class="order-footer">
                                @php
                                    $finalAmount = max(0, $order->total_price - ($order->used_coins ?? 0));
                                @endphp

                                <div class="order-body">
                                    <p class="payamount">ยอดโอน {{ number_format($finalAmount, 2) }} บาท</p>
                                    @if($order->payment_slip)
                                        <a href="{{ asset('storage/' . $order->payment_slip) }}" target="_blank" class="btn-info">ดูสลิป</a>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @php
    function getAdminActionPHP($order) {
    $actionHtml = '';

    if ($order->in_process_by) {
        $actionHtml = '<span class="inprocessed">รับออเดอร์โดย ' . ($order->admin_name ?? 'ไม่ระบุ') . '</span>';
        
        if ($order->approved_by) {
            $actionHtml .= ' <span class="inprocessed">เติมโดย ' . ($order->approved_by_name ?? 'ไม่ระบุ') . '</span>';
        } else {
            $actionHtml .= ' <button class="btn inprocess" onclick="markOrderCompleted(' . $order->id . ', this)">เติมแล้ว</button>';
        }
    } else {
        if ($order->status == 3 || $order->status == 2) {
            $actionHtml .= ' <button class="btn inprocess" onclick="markOrderInProcess(' . $order->id . ', this)">รับออเดอร์</button>';
        }
    }

    if ($order->status == 99) {
        $actionHtml .= '<span class="bcancelled" style="margin-left:3px;">ยกเลิกโดย ' . ($order->canceled_by_name ?? 'ไม่ระบุ') . '</span>';
    }
    
    if ($order->status == 3 || $order->status == 4 || $order->status == 11) {
        $actionHtml .= ' <button class="btn bcancel" style="margin-left:3px;" onclick="cancelOrder(' . $order->id . ', this)">ยกเลิก</button>';
    }

    return $actionHtml;
}

    function getOrderStatus($status) {
        switch ($status) {
            case 1: return '<span class="status pending">รอชำระเงิน</span>';
            case 2: return '<span class="status review">รอชำระเงิน</span>';
            case 3: return '<span class="status pending">แนบสลิปแล้ว</span>';
            case 4: return '<span class="status inprocessed">ทำรายการสำเร็จ</span>';
            case 11: return '<span class="status">กำลังดำเนินการ</span>';
            case 99: return '<span class="inprocessed">ยกเลิกแล้ว</span>';
            default: return '<span class="status cancelled">กรุณาติดต่อแอดมิน</span>';
        }
    }
    @endphp

    <script>
        function markOrderInProcess(orderId, buttonElement) {
            fetch(`/admin/orders/${orderId}/mark-in-process`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("ออเดอร์ถูกยืนยันแล้ว");
                    location.reload();
                } else {
                    alert("เกิดข้อผิดพลาด: " + data.message);
                }
            })
            .catch(error => console.error('Error updating order:', error));
        }

        function cancelOrder(orderId, buttonElement) {
            if (!confirm("ต้องการยกเลิกคำสั่งซื้อนี้?")) return;

            fetch(`/admin/orders/${orderId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert("เกิดข้อผิดพลาด: " + data.message);
                }
            })
            .catch(error => console.error('Error canceling order:', error));
        }


        function markOrderCompleted(orderId, buttonElement) {
            fetch(`/admin/orders/${orderId}/markCompleted`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("ออเดอร์สำเร็จ ตรวจสลิปแล้ว");

                    buttonElement.innerText = "";
                    buttonElement.disabled = true;

                    location.reload();
                } else {
                    alert("เกิดข้อผิดพลาด: " + data.message);
                }
            })
            .catch(error => console.error('Error completing order:', error));
        }

    </script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Panel</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <script>
        function openLightbox(imageSrc) {
            let lightbox = document.getElementById("lightbox");
            document.getElementById("lightbox-img").src = imageSrc;
            lightbox.style.display = "flex";
            setTimeout(() => {
                lightbox.classList.add("show");
            }, 10);
        }

        function closeLightbox(event) {
            let lightbox = document.getElementById("lightbox");

            if (event.target === lightbox || event.target.classList.contains("lightbox-close")) {
                lightbox.classList.remove("show");
                setTimeout(() => {
                    lightbox.style.display = "none";
                }, 300);
            }
        }
        
        window.Laravel = { csrfToken: '{{ csrf_token() }}' };
      
    </script>
</head>
<body>
    @include('admin.navbar')
  
    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">
                <h1>คำสั่งซื้อ</h1>
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @elseif(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="orders-container" id="order-list">
                    @foreach($orders as $order)
                        <div class="order-card" id="order-{{ $order->id }}">
                            <div class="order-header">
                                <div>
                                    <span class="order-title">หมายเลขคำสั่งซื้อ: #{{ $order->id }}</span><br>
                                    <span class="order-subheader">วันที่สั่งซื้อ: {{ $order->created_at->format('d/m/Y H:i') }}</span><br>
                                    <span class="order-subheader">โดย {{ $order->user->username }} อีเมล {{ $order->user->email }}</span>
                                </div>
                                <div class="order-status">
                                
                                    @if($order->status == '1')
                                        <span class="status pending">รอชำระเงิน</span>
                                    @elseif($order->status == '2')
                                        <span class="status review">รอชำระเงิน</span>
                                    @elseif($order->status == '3')
                                        <span class="status pending">แนบสลิปแล้ว</span>
                                    @elseif($order->status == '4')
                                        <span class="status inprocessed">ทำรายการสำเร็จ</span>
                                    @elseif($order->status == '11')
                                        <span class="status in-process">กำลังดำเนินการ</span>
                                    @else   
                                        <span class="status cancelled">ยกเลิก</span>
                                    @endif
                                    
                                    @if($order->in_process_by && $order->inProcessBy)
                                        <span class="inprocessed">รับออเดอร์โดย {{ $order->inProcessBy->name }}</span>
                                        @if($order->status == '4')
                                            <span class="inprocessed">เติมโดย {{ $order->approvedBy->name }}</span>
                                        @else
                                            <form action="{{ route('admin.orders.markCompleted', $order->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn inprocess" onclick="setTimeout(function(){ location.reload(); }, 500);">เติมแล้ว</button>
                                            </form>
                                        @endif
                                    @else
                                        @if($order->status == 3 OR $order->status == 2)
                                        <form action="{{ route('admin.orders.markInProcess', $order->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn inprocess" onclick="setTimeout(function(){ location.reload(); }, 500);">รับออเดอร์</button>
                                        </form>
                                        @else
                                            <span>IN PROCESS</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div>
                            @if($order)
                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                @php
                                    $cartDetails = json_decode($order->cart_details, true);
                                @endphp

                                <div class="order-summary">
                                    @foreach($cartDetails as $game_id => $game)
                                        <div class="dash-container">
                                            <div class="cart-left">
                                                <div class="cart-gametitle">{{ $game['game_name'] }}</div>
                                            </div>

                                            <div class="cart-right">
                                                @foreach($game['packages'] as $uniqueId => $package)
                                                    <div class="cart-item">
                                                        <div class="cart-details">
                                                            <div class="topupcard-title">แพค : {{ $package['name'] }} <span class="topupcard-text">{{ $package['detail'] ?? '' }}</span></div>                                                            
                                                        </div>
                                                        
                                                        <div class="cart-price">
                                                            <strong class="new-price">ราคา {{ number_format($package['price'], 0) }} บาท</strong>
                                                        </div>

                                                        <div class="chout-actions">ID ผู้เล่น : {{ $package['player_id'] ?? 'ไม่ระบุ' }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach  
                                </div>

                            @else
                                <p>คำสั่งซื้อนี้ไม่พบ</p>
                            @endif              
                            </div>
                            
                            <div class="order-coins">
                                <div class="coin-section">
                                    <div class="coin-item">
                                        ใช้ไป {{ number_format($order->used_coins ?? 0) }}<img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon">
                                    </div>
                                    <div class="coin-item">
                                        ได้รับ {{ number_format($order->coin_earned ?? 0) }}<img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon">
                                    </div>
                                </div>
                            </div>

                            <div class="order-footer">
                            @php
                                $finalAmount = max(0, $order->total_price - ($order->used_coins ?? 0));
                            @endphp
                                <div class="order-body">
                                    <p class="payamount" style="margin:0;">ยอดโอน {{ number_format($finalAmount, 2) }} บาท</p>
                                    @if($order->payment_slip)
                                    
                                    <a href="javascript:void(0);" onclick="openLightbox('{{ asset('storage/' . $order->payment_slip) }}')" class="btn-info">ดูสลิป</a>
                                    @else
                                        -
                                    @endif
                                </div>   
                                <div id="lightbox" class="lightbox" onclick="closeLightbox(event)">
                                    <span class="lightbox-close" onclick="closeLightbox(event)">&times;</span>
                                    <img id="lightbox-img" class="lightbox-content" onclick="event.stopPropagation();">
                                </div>                              
                            </div>
                        </div>
                    @endforeach
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order ID</th>
                            <th>Status</th>
                            <th>Payment Slip</th>
                            <th>Approval Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order->id }}</td>
                                <td>
                                    @switch($order->status)
                                        @case(2)
                                            รอโอนเงิน
                                            @break
                                        @case(3)
                                            รอตรวจสอบสลิป
                                            @break
                                        @case(4)
                                            ตรวจสอบสลิปแล้ว
                                            @break
                                        @default
                                            Unknown Status
                                    @endswitch
                                </td>
                                <td>
                                    @if($order->payment_slip)
                                        <a href="{{ asset('storage/' . $order->payment_slip) }}" target="_blank">ดูสลิป</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                @if($order->payment_approved_at)
                                    {{ $order->payment_approved_at->format('Y-m-d H:i:s') }}
                                @else
                                    -
                                @endif
                                </td>
                                <td>
                                    @if($order->status == 2)
                                        <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-success">Approve Payment</button>
                                        </form>
                                    @elseif($order->status == 3)
                                        <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-success">Approve Payment</button>
                                        </form>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
let lastOrderId = 0;  // Initially set to 0 to fetch the first batch of orders

// Function to fetch the latest orders
function fetchNewOrders() {
    fetch(`/admin/orders/new?last_order_id=${lastOrderId}`)  // Send the last order ID to the backend
        .then(response => response.json())  // Parse the response as JSON
        .then(orders => {
            if (orders.length) {
                const orderList = document.querySelector('#order-list');
                
                // Clear the current order list to reload new orders
                orderList.innerHTML = '';

                // Loop through each order and dynamically add it to the list
                orders.forEach(order => {
                    const orderElement = document.createElement('div');
                    orderElement.classList.add('order-card');

                    // Replace the innerHTML with the full order structure
                    orderElement.innerHTML = `
                        <div class="order-header">
                            <div>
                                <span class="order-title">หมายเลขคำสั่งซื้อ: #${order.id}</span><br>
                                <span class="order-subheader">วันที่สั่งซื้อ: ${new Date(order.created_at).toLocaleString()}</span><br>
                                <span class="order-subheader">โดย ${order.user ? order.user.username : 'N/A'} อีเมล ${order.user ? order.user.email : 'N/A'}</span>
                            </div>
                            <div class="order-status">
                                ${getOrderStatus(order)}  <!-- Dynamically set order status -->
                                ${getAdminAction(order)}  <!-- Dynamically handle admin actions -->
                            </div>
                        </div>
                    `;

                    // Append the new order to the list
                    orderList.appendChild(orderElement);

                    // Update lastOrderId to the latest order ID
                    lastOrderId = order.id;
                });
            }
        })
        .catch(error => console.error('Error fetching new orders:', error));  // Log any errors
}

// Poll every 5 seconds for new orders and reload the list
setInterval(fetchNewOrders, 5000);  // Adjust polling interval as needed (e.g., 5000ms = 5 seconds)

// Fetch the latest orders on initial page load
window.onload = fetchNewOrders;

// Function to handle order status display logic
function getOrderStatus(order) {
    let statusHtml = '';
    
    switch (order.status) {
        case '1':
            statusHtml = '<span class="status pending">รอชำระเงิน</span>';
            break;
        case '2':
            statusHtml = '<span class="status review">รอชำระเงิน</span>';
            break;
        case '3':
            statusHtml = '<span class="status pending">แนบสลิปแล้ว</span>';
            break;
        case '4':
            statusHtml = '<span class="status inprocessed">ทำรายการสำเร็จ</span>';
            break;
        case '11':
            statusHtml = '<span class="status in-process">กำลังดำเนินการ</span>';
            break;
        default:
            statusHtml = '<span class="status cancelled">ยกเลิก</span>';
            break;
    }

    return statusHtml;
}

var orders = @json($orders);
    console.log(orders);
// Function to handle admin actions for orders
function getAdminAction(order) {
    let actionHtml = '';

    // If order is being processed, show admin name
    if (order.in_process_by) {
        actionHtml = `<span class="inprocessed">รับออเดอร์โดย ${order.in_process_by}</span>`;
        if (order.status == '4') {
            actionHtml += `<span class="inprocessed">เติมโดย ${order.approved_by} </span>`;
        }
    } else {
        console.log('No Admin Processing This Order');
        if (order.status == '3' || order.status == '2') {
            // Show form to mark order as "In Process"
            actionHtml = `
                <form action="/admin/orders/${order.id}/mark-in-process" method="POST" style="display:inline;">
                    <input type="hidden" name="_token" value="${window.Laravel.csrfToken}">
                    <button type="submit" class="btn btn-warning" onclick="setTimeout(function(){ location.reload(); }, 500);">รับออเดอร์</button>
                </form>
            `;
        } else {
            actionHtml = '<span>IN PROCESS</span>';
        }
    }

    return actionHtml;
}

</script>

</body>
</html>

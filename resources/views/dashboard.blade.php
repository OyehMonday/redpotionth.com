<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">

    <h1>แดชบอร์ดของลูกค้า</h1>

    <!-- ✅ Display Success Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- ✅ Show List of Orders -->
    <h2>คำสั่งซื้อของคุณ</h2>

    @if($orders->isEmpty())
        <p>ยังไม่มีคำสั่งซื้อ</p>
    @else
        <div class="orders-container">
            @foreach($orders as $order)
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">หมายเลขคำสั่งซื้อ: #{{ $order->id }}</span>
                        <span class="order-status">
                            @if($order->status == '1')
                                <span class="status pending">รอดำเนินการ</span>
                            @elseif($order->status == '2')
                                <span class="status review">รอตรวจสอบการชำระเงิน</span>
                            @elseif($order->status == '3')
                                <span class="status completed">ชำระเงินสำเร็จ</span>
                            @else
                                <span class="status cancelled">ยกเลิก</span>
                            @endif
                        </span>
                    </div>

                    <div class="order-body">
                        <p><strong>จำนวนเงิน:</strong> {{ number_format($order->total_price, 2) }} บาท</p>
                        <p><strong>วันที่สั่งซื้อ:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="order-footer">
                        <a href="{{ route('game.checkout.view', ['order_id' => $order->id]) }}" class="btn btn-primary">ดูรายละเอียด</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>


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

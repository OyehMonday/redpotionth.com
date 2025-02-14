<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Panel</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body>
    @include('admin.navbar')
  
    <div class="container">
        <h1>Order List</h1>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

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
                            @if($order->status == 3)
                                <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-success">Approve Payment</button>
                                </form>
                            @elseif($order->status == 4)
                                <span class="badge badge-success">ตรวจสลิปโดย {{ $order->approvedBy->name }} </span>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>

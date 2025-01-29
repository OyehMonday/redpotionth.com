<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    <div class="main-wrapper">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <div class="dashboard-logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-image">
                    </a>
                </div> 
                <h1 class="dashboard-title">แดชบอร์ดผู้ดูแลระบบ</h1>
                <p class="dashboard-welcome">ยินดีต้อนรับ, {{ Auth::guard('admin')->user()->name }}</p>
            </div>
            
            <div class="dashboard-content">
                <p>นี่คือแดชบอร์ดของคุณ คุณสามารถจัดการระบบหลังบ้านจากหน้านี้</p>
                <div class="dashboard-actions">
                    <a href="{{ url('/admin/manage-users') }}" class="btn btn-secondary">จัดการผู้ใช้งาน</a>
                    <a href="{{ url('/admin/manage-items') }}" class="btn btn-secondary">จัดการสินค้า</a>
                </div>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="btn btn-primary">ออกจากระบบ</button>
            </form>
        </div>
    </div>
</body>
</html>

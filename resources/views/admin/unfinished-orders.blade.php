<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Panel</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <script>
        window.Laravel = { csrfToken: '{{ csrf_token() }}' };
      
        document.getElementById("gotoPageBtn").addEventListener("click", () => {
            let requestedPage = parseInt(document.getElementById("gotoPageInput").value);
            if (!isNaN(requestedPage) && requestedPage >= 1 && requestedPage <= totalPages) {
                fetchOrders(requestedPage);
            } else {
                alert("หน้าที่คุณป้อนไม่ถูกต้อง กำลังนำคุณกลับไปที่หน้า 1");
                fetchOrders(1);
            }
        });

    </script>
</head>
<body>
    @include('admin.navbar')
  
    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">
            @foreach ($orders as $order)
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <span class="order-title">หมายเลขคำสั่งซื้อ: #{{ $order->id }}</span><br>
                        <span class="order-subheader">วันที่สั่งซื้อ: {{ $order->created_at->format('Y-m-d H:i:s') }}</span><br>
                        <span class="order-subheader">โดย {{ $order->user->username ?? 'N/A' }} อีเมล {{ $order->user->email ?? 'N/A' }}</span>
                    </div>
                    <div class="order-status">
                        {!! getOrderStatus($order) !!} 
                        {!! getAdminAction($order) !!} 
                    </div>
                </div>

                <div class="order-summary">
                    {!! getCartDetails($order) !!}
                </div>   

                <div class="order-coins">
                    {!! getCoins($order) !!}
                </div>  

                <div class="order-footer">
                    {!! getPaymentSlip($order) !!}
                </div>          
            </div>
        @endforeach

            </div>
        </div>
    </div>

    <script>
        let currentPage = localStorage.getItem('currentPage') ? parseInt(localStorage.getItem('currentPage')) : 1;
        let totalPages = 1;

        function fetchOrders(page = 1) {
            if (page < 1 || page > totalPages) {
                alert("หมายเลขหน้านี้ไม่มีอยู่ กำลังนำคุณกลับไปที่หน้า 1");
                page = 1; 
            }

            fetch(`/admin/orders/new?page=${page}`)
            .then(response => response.json())
            .then(data => {
                const orderList = document.querySelector('#order-list');
                orderList.innerHTML = '';
                
                document.getElementById("unfinishedOrders").innerHTML = `มีคำสั่งซื้อรอดำเนินการ ${data.unfinished_orders} ออเดอร์`;

                if (data.orders.length === 0 && page > 1) {
                    alert("ไม่มีข้อมูลในหน้านี้ กำลังนำคุณกลับไปที่หน้า 1");
                    fetchOrders(1);
                    return;
                }

                data.orders.forEach(order => {
                    const orderElement = document.createElement('div');
                    orderElement.classList.add('order-card');

                    orderElement.innerHTML = `
                        <div class="order-header">
                            <div>
                                <span class="order-title">หมายเลขคำสั่งซื้อ: #${order.id}</span><br>
                                <span class="order-subheader">วันที่สั่งซื้อ: ${new Date(order.created_at).toLocaleString()}</span><br>
                                <span class="order-subheader">โดย ${order.user ? order.user.username : 'N/A'} อีเมล ${order.user ? order.user.email : 'N/A'}</span>
                            </div>
                            <div class="order-status">
                                ${getOrderStatus(order)} 
                                ${getAdminAction(order)} 
                            </div>
                        </div>

                        <div class="order-summary">
                            ${getCartDetails(order)}
                        </div>   

                        <div class="order-coins">
                            ${getCoins(order)}
                        </div>  

                        <div class="order-footer">
                            ${getPaymentSlip(order)}
                        </div>          
                    `;

                    orderList.appendChild(orderElement);
                });

                currentPage = data.current_page; 
                totalPages = data.total_pages;

                localStorage.setItem('currentPage', currentPage);

                updatePaginationControls();  
            })
            .catch(error => {
                alert("เกิดข้อผิดพลาดในการโหลดข้อมูล กำลังนำคุณกลับไปที่หน้า 1");
                fetchOrders(1);
            });
        }


        function updatePaginationControls() {
            document.getElementById("prevBtn").disabled = (currentPage === 1);
            document.getElementById("nextBtn").disabled = (currentPage >= totalPages);

            document.getElementById("currentPage").innerText = currentPage;
            document.getElementById("totalPages").innerText = totalPages;
        }

        document.getElementById("prevBtn").addEventListener("click", () => {
            if (currentPage > 1) {
                fetchOrders(--currentPage);
            }
        });

        document.getElementById("nextBtn").addEventListener("click", () => {
            if (currentPage < totalPages) {
                fetchOrders(++currentPage);
            }
        });

        document.getElementById("gotoPageBtn").addEventListener("click", () => {
            let requestedPage = parseInt(document.getElementById("gotoPageInput").value);
            if (!isNaN(requestedPage) && requestedPage >= 1 && requestedPage <= totalPages) {
                fetchOrders(requestedPage);
            } else {
                alert("กรุณากรอกหมายเลขหน้าที่ถูกต้อง");
            }
        });

        window.onload = () => {
            const savedPage = localStorage.getItem('currentPage');
            fetchOrders(savedPage ? parseInt(savedPage) : 1);
        };

        setInterval(() => {
            fetchOrders(currentPage);
        }, 5000);


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
                    statusHtml = '<span class="status">กำลังดำเนินการ</span>';
                    break;
                default:
                    statusHtml = '<span class="status cancelled">ยกเลิก</span>';
                    break;
            }

            return statusHtml;
        }

        function getAdminAction(order) {
            let actionHtml = '';

            if (order.in_process_by) {
                actionHtml = `<span class="inprocessed">รับออเดอร์โดย ${order.admin_name}</span>`;
                if (order.status == '4') {
                    actionHtml += ` <span class="inprocessed">เติมโดย ${order.approved_by_name} </span>`;
                } else {
                    actionHtml += `
                        <button class="btn inprocess" onclick="markOrderCompleted(${order.id}, this)">เติมแล้ว</button>
                    `;
                }
            } else {
                if (order.status == '3' || order.status == '2') {
                    actionHtml = `
                        <button class="btn inprocess" onclick="markOrderInProcess(${order.id}, this)">รับออเดอร์</button>
                    `;
                } else {
                    actionHtml = '<span>IN PROCESS</span>';
                }
            }

            return actionHtml;
        }

        function getCartDetails(order) {
            let cartHtml = '';
            const cartDetails = JSON.parse(order.cart_details);
            for (let gameId in cartDetails) {
                const game = cartDetails[gameId];
                cartHtml += `
                    <div class="dash-container">
                        <div class="cart-left">
                            <div class="cart-gametitle">${game.game_name}</div>
                        </div>
                        <div class="cart-right">
                            ${Object.values(game.packages).map(package => {
                                return `
                                    <div class="cart-item">
                                        <div class="cart-details">
                                            <div class="topupcard-title">แพค : ${package.name} <span class="topupcard-text">${package.detail || ''}</span></div>
                                        </div>
                                        <div class="cart-price">
                                            <strong class="new-price">ราคา ${new Intl.NumberFormat().format(package.price)} บาท</strong>
                                        </div>
                                        <div class="chout-actions">ID ผู้เล่น : ${package.player_id || 'ไม่ระบุ'}</div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }
            return cartHtml;
        }

        function getCoins(order) {
            return `
                <div class="coin-section">
                    <div class="coin-item">
                        ใช้ไป ${new Intl.NumberFormat().format(order.used_coins || 0)}<img src="../images/coin.png" alt="Coin" class="coin-icon">
                    </div>
                    <div class="coin-item">
                        ได้รับ ${new Intl.NumberFormat().format(order.coin_earned || 0)}<img src="../images/coin.png" alt="Coin" class="coin-icon">
                    </div>
                </div>
            `;
        }

        function getPaymentSlip(order) {
            let finalAmount = Math.max(0, order.total_price - (order.used_coins || 0));
            return `
                <div class="order-body">
                    <p class="payamount">ยอดโอน ${new Intl.NumberFormat().format(finalAmount)} บาท</p>
                    ${order.payment_slip ? `<a href="../storage/${order.payment_slip}" target="_blank" class="btn-info">ดูสลิป</a>` : '-'}
                </div>
            `;
        }

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
                    fetchOrders(currentPage, showUnfinishedOnly); 
                } else {
                    alert("เกิดข้อผิดพลาด: " + data.message);
                }
            })
            .catch(error => console.error('Error updating order:', error));
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
                    alert(data.message);

                    const orderElement = buttonElement.closest('.order-card');
                    if (orderElement) {
                        orderElement.remove();
                    }
                } else {
                    alert("เกิดข้อผิดพลาด: " + data.message);
                }
            })
            .catch(error => console.error('Error updating order:', error));
        }

       
    </script>
</body>
</html>

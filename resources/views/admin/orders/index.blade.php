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
                <h2 id="unfinishedOrders" class="order-title" style="cursor: pointer; text-decoration: none; margin:0px;">
                    มีคำสั่งซื้อรอดำเนินการ 0 ออเดอร์
                </h2>
            </div>
            <div class="section topup-section">
                <h1>คำสั่งซื้อ</h1>
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @elseif(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="orders-container" id="order-list">
                    <!-- Orders will be dynamically loaded here -->
                </div>

                <div class="pagination-controls">
                    <button id="prevBtn" class="btn" disabled>ก่อนหน้า</button>
                    
                    <span id="page-info">
                        หน้า <span id="currentPage">1</span> จาก <span id="totalPages">?</span>
                    </span>

                    <button id="nextBtn" class="btn" disabled>ถัดไป</button>

                </div>
                <div class="pagination-controls" style="margin-top:5px;">
                    <input type="number" id="gotoPageInput" class="goto-input" min="1" placeholder="ไปหน้า">
                    <button id="gotoPageBtn" class="btn">ไป</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        let currentPage = localStorage.getItem('currentPage') ? parseInt(localStorage.getItem('currentPage')) : 1;
        let totalPages = 1;
        let showUnfinishedOnly = false; 

        function fetchOrders(page = 1) {
            let url = `/admin/orders/new?page=${page}`;
            if (showUnfinishedOnly) {
                url += `&unfinished_only=true`; 
            }

            fetch(url)
            .then(response => response.json())
            .then(data => {
                const orderList = document.querySelector('#order-list');
                orderList.innerHTML = '';

                const unfinishedOrdersText = document.getElementById("unfinishedOrders");
                unfinishedOrdersText.innerHTML = `มีคำสั่งซื้อรอดำเนินการ ${data.unfinished_orders} ออเดอร์`;

                unfinishedOrdersText.onclick = () => {
                    showUnfinishedOnly = !showUnfinishedOnly;
                    fetchOrders(1); 
                };

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
                                <a href="/admin/orders/${order.id}/details" target="_blank" class="order-titlelink">
                                    หมายเลขคำสั่งซื้อ: #${order.id}
                                </a><br>
                                <span class="order-subheader">วันที่สั่งซื้อ: ${new Date(order.created_at).toLocaleString()}</span><br>
                                <span class="order-subheader">
                                    โดย <a href="/admin/users/${order.user.id}/orders" target="_blank" class="user-link">${order.user ? order.user.username : 'N/A'}</a> 
                                    อีเมล ${order.user ? order.user.email : 'N/A'}</span>
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
            .catch(error => console.error('Error fetching orders:', error));
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
                case '99':
                    statusHtml = '<span class="inprocessed">ยกเลิกแล้ว</span>';
                    break;
                default:
                    statusHtml = '<span class="status cancelled">กรุณาติดต่อแอดมิน</span>';
                    break;
            }

            return statusHtml;
        }

        function formatDateTime(datetime) {
            if (!datetime) return "N/A"; 

            const date = new Date(datetime);
            return date.toLocaleString("th-TH", {
                year: "numeric",
                month: "long",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
                hour12: false
            });
        }

        function getAdminAction(order) {
            let actionHtml = '';
            
            if (order.in_process_by) {
                actionHtml = `<span class="inprocessed">รับออเดอร์โดย ${order.admin_name}</span>`;
                if (order.approved_by) {
                    actionHtml += ` <span class="inprocessed" title="เติมเมื่อ: ${formatDateTime(order.payment_approved_at)}">เติมโดย ${order.approved_by_name} </span>`;
                } else {
                    actionHtml += ` <button class="btn inprocess" onclick="markOrderCompleted(${order.id}, this)">เติมแล้ว</button>`;
                }
            } else {
                if (order.status == '3' || order.status == '2') {
                    actionHtml = ` <button class="btn inprocess" onclick="handleOrderAcknowledgement(${order.id}, this)">รับออเดอร์</button>`;
                } else {
                }
            }

            if (order.status == '99') {
                actionHtml += `<span class="bcancelled" style="margin-left:3px;">ยกเลิกโดย ${order.canceled_by_name} </span>`;
            }if (order.status == '2') {
                actionHtml += ``;
            }if (order.status == '3' || order.status == '4' || order.status == '11') {
                actionHtml += `<button class="btn bcancel" style="margin-left:3px;" onclick="cancelOrder(${order.id}, this)">ยกเลิก</button>`;
            }

            return actionHtml;
        }

        function handleOrderAcknowledgement(orderId, buttonElement) {
            markOrderInProcess(orderId, buttonElement);
            window.open(`/admin/orders/${orderId}/details`, '_blank');
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
                        ใช้ไป ${new Intl.NumberFormat().format(order.used_coins || 0)}<img src="../../images/coin.png" alt="Coin" class="coin-icon">
                    </div>
                    <div class="coin-item">
                        ได้รับ ${new Intl.NumberFormat().format(order.coin_earned || 0)}<img src="../../images/coin.png" alt="Coin" class="coin-icon">
                    </div>
                </div>
            `;
        }

        function getPaymentSlip(order) {
            let finalAmount = Math.max(0, order.total_price - (order.used_coins || 0));

            let warningMessage = '';
                if (order.referror == 1) {
                    warningMessage = `<p class="warning-message" style="color: red; font-weight: bold;">สลิปอาจซ้ำ</p>`;
                } else if (order.referror == 2) {
                    warningMessage = `<p class="warning-message" style="color: red; font-weight: bold;">สลิปไม่มี QR ตรวจสอบไม่ได้</p>`;
                }

            return `
                <div class="order-body">
                    <p class="payamount">ยอดโอน ${new Intl.NumberFormat().format(finalAmount)} บาท</p>
                    ${order.payment_slip ? `<a href="../images/${order.payment_slip}" target="_blank" class="btn-info">ดูสลิป</a>` : '-'}
                    ${warningMessage}
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

                    const orderElement = buttonElement.closest('.order-card');
                    if (orderElement) {
                        orderElement.innerHTML = `
                            <div class="order-header">
                                <div>
                                    <span class="order-title">หมายเลขคำสั่งซื้อ: #${data.order_id}</span><br>
                                    <span class="order-subheader">ยกเลิก โดย ${data.canceled_by_name}</span>
                                </div>
                            </div>
                        `;
                    }
                } else {
                    alert("เกิดข้อผิดพลาด: " + data.message);
                }
            })
            .catch(error => console.error('Error canceling order:', error));
        }

        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }
    </script>    
</body>
</html>

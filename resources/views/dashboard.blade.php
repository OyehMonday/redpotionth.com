<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมาชิก</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">

            <div class="user-info-container">
                <div class="coin-info">
                    <span class="order-title">รายละเอียด Coin</span>
                    <div class="order-title" style="padding-top:20px;">
                        คุณมี: {{ number_format($user->coins ?? 0) }}
                        <img src="{{ asset('images/coin.png') }}" alt="Coin" class="coin-icon">
                    </div>
                    <p class="order-subheader">(Coin จะถูกเพิ่มเมื่อคำสั่งซื้อสำเร็จ)</p>
                </div>
                
                <div class="user-info">
                    <span class="order-title">ข้อมูลสมาชิก</span>
                    <div class="user-detail"><strong>Username :</strong> {{ $user->username }}</div>
                    <div class="user-detail"><strong>อีเมล :</strong> {{ $user->email }}</div>
                    <div class="user-logout"><a href="{{ route('logout') }}">ออกจากระบบ</a></div>
                </div>
            </div>

            <div class="section topup-section">
                <h1>คำสั่งซื้อของคุณ</h1>

                @if(session('success'))
                    <div class="alert alert-success" style="text-align: center;">{{ session('success') }}</div>
                @endif

                @if($orders->isEmpty())
                    <p>ยังไม่มีคำสั่งซื้อ</p>
                @else
                    <div class="orders-container" id="ordersContainer">
                        @foreach($orders as $order)
                            @include('partials.order_card', ['order' => $order])
                        @endforeach
                    </div>

                    <div id="loading" style="display: none; text-align: center; margin-top: 10px;">
                        <p>กำลังโหลด...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        let offset = 5; 
        let isLoading = false;
        
        function loadMoreOrders() {
            if (isLoading) return;
            isLoading = true;
            
            document.getElementById("loading").style.display = "block"; 

            fetch(`/load-more-orders?offset=${offset}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        offset += data.length; 
                        let ordersContainer = document.getElementById("ordersContainer");

                        data.forEach(order => {
                            let finalAmount = Math.max(0, parseFloat(order.total_price || 0) - parseFloat(order.used_coins || 0));
                            let orderHTML = `
                                <div class="order-card">
                                    <div class="order-header">
                                        <div>
                                            <span class="order-title">หมายเลขคำสั่งซื้อ: #${order.id}</span><br>
                                            <span class="order-subheader">วันที่สั่งซื้อ: ${formatDate(order.created_at)}</span>
                                        </div>
                                        <div class="order-status">
                                            <span class="status">${getStatusText(order.status)}</span>
                                        </div>
                                    </div>
                                    <div class="order-summary">
                                        ${generateCartDetails(order.cart_details)}
                                    </div>  
                                    
                                    <div class="order-coins">
                                        <div class="coin-section">
                                            <div class="coin-item">
                                                ใช้ไป ${numberFormat(order.used_coins)} 
                                                <img src="/images/coin.png" alt="Coin" class="coin-icon">
                                            </div>
                                            <div class="coin-item">
                                                ได้รับ ${numberFormat(order.coin_earned)} 
                                                <img src="/images/coin.png" alt="Coin" class="coin-icon">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="order-footer">
                                        <div class="order-body">
                                            <p class="payamount">ยอด${order.status == 2 ? 'ที่ต้องชำระ' : 'ชำระ'} ${numberFormat(finalAmount, 2)} บาท</p>
                                        </div>
                                        ${order.status == 2 ? `<span><a href="/game/checkout/${order.id}" class="cart-btn" style="text-decoration: none;">ดำเนินการชำระเงิน</a></span>` : ''}
                                    </div>                                                                      
                                </div>
                            `;
                            ordersContainer.insertAdjacentHTML("beforeend", orderHTML);
                        });
                    } else {
                        window.removeEventListener("scroll", handleScroll);
                    }
                    isLoading = false;
                    document.getElementById("loading").style.display = "none"; 
                })
                .catch(error => {
                    console.error("Error loading more orders:", error);
                    isLoading = false;
                });
        }

        function generateCartDetails(cartDetails) {
            let cartHTML = "";
            Object.keys(cartDetails).forEach(gameId => {
                let game = cartDetails[gameId];
                cartHTML += `<div class="dash-container">
                    <div class="cart-left">
                        <div class="cart-gametitle">${game.game_name}</div>
                        <a href="/games/${game.game_id}/topup">
                            <img src="/images/${game.cover_image}" class="cart-gamecover" alt="${game.game_name}">
                        </a>
                    </div>
                    <div class="cart-right">`;
                    if (Array.isArray(game.packages) && game.packages.length > 0) {
                        game.packages.forEach(pack => {
                            cartHTML += `<div class="cart-item">
                                <div class="cart-details">
                                    <div class="topupcard-title">แพค : ${pack.name || 'ไม่ระบุ'}</div>
                                    <div class="topupcard-text">${pack.detail || ''}</div>
                                    <div class="cart-price">
                                        <s class="old-price">${numberFormat(parseFloat(pack.full_price || 0))} บาท</s><br>
                                        <strong class="new-price">ราคา ${numberFormat(parseFloat(pack.price || 0))} บาท</strong>
                                    </div>
                                </div>
                                <div class="chout-actions">ID ผู้เล่น : ${pack.player_id || 'ไม่ระบุ'}</div>
                            </div>`;
                        });
                    } else {
                        cartHTML += `<p>ไม่มีแพคเกจ</p>`;
                    }
                cartHTML += `</div></div>`;
            });
            return cartHTML;
        }

        function getStatusText(status) {
            let statusClass = "cancelled";
            let statusText = "สถานะ : ระหว่างตรวจสอบ"; 

            if (status == '1') {
                statusClass = "pending";
                statusText = "สถานะ : รอชำระเงิน";
            } else if (status == '2') {
                statusClass = "review";
                statusText = "สถานะ : รอชำระเงิน";
            } else if (status == '3') {
                statusClass = "review";
                statusText = "สถานะ : รอตรวจสอบการชำระเงิน";
            } else if (status == '4') {
                statusClass = "completed";
                statusText = "สถานะ : ทำรายการสำเร็จ";
            } else if (status == '11') {
                statusClass = "review";
                statusText = "สถานะ : อยู่ระหว่างดำเนินการ";
            } else if (status == '99') {
                statusClass = "cancelled";
                statusText = "สถานะ : ยกเลิก";
            }

            return `<span class="status ${statusClass}">${statusText}</span>`;
        }


        function formatDate(dateString) {
            let formattedDateString = dateString.replace(" ", "T");

            let date = new Date(formattedDateString);
            if (isNaN(date.getTime())) {
                console.warn("Invalid Date:", dateString);
                return "ไม่สามารถระบุวันที่";
            }

            let day = String(date.getDate()).padStart(2, '0'); 
            let month = String(date.getMonth() + 1).padStart(2, '0'); 
            let year = date.getFullYear();
            let hours = String(date.getHours()).padStart(2, '0'); 
            let minutes = String(date.getMinutes()).padStart(2, '0');

            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }

        function numberFormat(num) {
            return new Intl.NumberFormat('th-TH', { minimumFractionDigits: 0 }).format(num);
        }

        function handleScroll() {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 50) {
                loadMoreOrders();
            }
        }

        window.addEventListener("scroll", handleScroll);

        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }        
    </script>
    @include('footer')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Orders</title>
    <script src="{{ asset('js/app.js') }}"></script> <!-- Include your JS file -->
</head>
<body>
    <h1>Test Orders</h1>

    <!-- Display Orders Here -->
    <div id="order-list">
        <!-- Initial orders will be loaded here -->
        @foreach($orders as $order)
            <div class="order-card" id="order-{{ $order->id }}">
                <p>Order ID: #{{ $order->id }}</p>
            </div>
        @endforeach
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
                        
                        // Clear the current order list
                        orderList.innerHTML = '';

                        // Loop through each order and dynamically add it to the list
                        orders.forEach(order => {
                            const orderElement = document.createElement('div');
                            orderElement.classList.add('order-card');
                            orderElement.innerHTML = `
                                <p>Order ID: #${order.id}</p>
                            `;
                            orderList.appendChild(orderElement);
                        });

                        // After updating the list, set the last order ID to the latest order ID
                        lastOrderId = orders[orders.length - 1].id;  // Update the last order ID
                    }
                })
                .catch(error => console.error('Error fetching new orders:', error));
        }

        // Poll every 5 seconds for new orders and reload the list
        setInterval(fetchNewOrders, 5000);  // Adjust polling interval as needed (e.g., 5000ms = 5 seconds)

        // Fetch the latest orders on initial page load
        window.onload = fetchNewOrders;
    </script>
</body>
</html>

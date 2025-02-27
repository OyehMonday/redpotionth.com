<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">

</head>
<body>
    
    @include('admin.navbar')

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="border p-5">
                    <h2>Welcome to the Admin Dashboard</h2>
                    <p>This is where you can manage users, view data, and perform administrative tasks.</p>
                    <!-- You can add dynamic content here later -->
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
</body>
</html>

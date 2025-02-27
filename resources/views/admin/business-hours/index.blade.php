<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Games & Categories - Admin Panel</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body>

    @include('admin.navbar') 

    <div class="main-wrapper">
        <div class="container">
            <div class="section topup-section">
                <h1>Manage Business Hours</h1>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.business-hours.update') }}">
                    @csrf
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Open Time</th>
                                <th>Close Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($businessHours as $hour)
                            <tr>
                                <td>{{ $hour->day }}</td>
                                <td><input type="time" name="hours[{{ $hour->day }}][open_time]" value="{{ $hour->open_time }}"></td>
                                <td><input type="time" name="hours[{{ $hour->day }}][close_time]" value="{{ $hour->close_time }}"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
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
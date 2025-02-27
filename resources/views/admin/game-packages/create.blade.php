<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Package - Admin Panel</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body>

    @include('admin.navbar') <!-- Include navbar for consistency -->

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="border p-4 rounded">
                    <h2 class="mb-4">Add New Package for {{ $game->title }}</h2>

                    <!-- Error Handling -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Package Creation Form -->
                    <form method="POST" action="{{ route('game-packages.store', $game) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="name">Package Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="detail">Package Detail</label>
                            <input type="text" name="detail" id="detail" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="full_price">Full Price</label>
                            <input type="number" name="full_price" id="full_price" class="form-control" required step="0.01">
                        </div>

                        <div class="form-group">
                            <label for="selling_price">Selling Price</label>
                            <input type="number" name="selling_price" id="selling_price" class="form-control" required step="0.01">
                        </div>

                        <div class="form-group">
                            <label for="cover_image">Package Image</label>
                            <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*">
                        </div>

                        <button type="submit" class="btn btn-success mt-3">Add Package</button>
                        <a href="{{ route('game-packages.index', $game) }}" class="btn btn-secondary mt-3">Cancel</a>
                    </form>
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

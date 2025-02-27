<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package - Admin Panel</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body>

    @include('admin.navbar') 

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="border p-4 rounded">
                    <h2 class="mb-4">Edit Package for {{ $game->title }}</h2>

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

                    <!-- Package Edit Form -->
                    <form method="POST" action="{{ route('game-packages.update', [$game, $package]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Package Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $package->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="full_price">Full Price (THB)</label>
                            <input type="number" name="full_price" id="full_price" class="form-control" value="{{ $package->full_price }}" required step="0.01">
                        </div>

                        <div class="form-group">
                            <label for="selling_price">Selling Price (THB)</label>
                            <input type="number" name="selling_price" id="selling_price" class="form-control" value="{{ $package->selling_price }}" required step="0.01">
                        </div>

                        <div class="form-group">
                            <label for="detail">Package Detail</label>
                            <input type="text" name="detail" id="detail" class="form-control" value="{{ $package->detail }}">
                        </div>

                        <div class="form-group">
                            <label for="cover_image">Package Cover Image</label>
                            @if ($package->cover_image)
                                <div class="mb-2">
                                    <img src="{{ asset('images/' . $package->cover_image) }}" width="100">
                                </div>
                            @endif
                            <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*">
                            <small class="text-muted">Leave blank if you don't want to change the image.</small>
                        </div>

                        <button type="submit" class="btn btn-success mt-3">Update Package</button>
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

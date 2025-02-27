<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Game Category - Admin Panel</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
</head>
<body>

    @include('admin.navbar') <!-- Include navbar for consistency -->

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="border p-4 rounded">
                    <h2 class="mb-4">Edit Game Category</h2>

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

                    <!-- Category Edit Form -->
                    <form method="POST" action="{{ route('game-categories.update', $gameCategory->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $gameCategory->name) }}" required>
                        </div>

                        <button type="submit" class="btn btn-success mt-3">Update Category</button>
                        <a href="{{ route('game-categories.index') }}" class="btn btn-secondary mt-3">Cancel</a>
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

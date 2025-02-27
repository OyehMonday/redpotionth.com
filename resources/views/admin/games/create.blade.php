<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Game</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
</head>
<body>

    @include('admin.navbar')

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="border p-5">
                    <h2>Add New Game</h2>

                    <form method="POST" action="{{ route('games.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Game Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Game Description:</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Game Category</label>
                            <select name="game_category_id" class="form-control" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Cover Image</label>
                            <input type="file" name="cover_image" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Full Cover Image</label>
                            <input type="file" name="full_cover_image" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="uid_detail">Player ID Placeholder</label>
                            <input type="text" class="form-control" name="uid_detail" placeholder="ตัวอย่าง : กรอก UID ของคุณ">
                        </div>

                        <div class="form-group">
                            <label for="uid_image">Player ID Guide Image</label>
                            <input type="file" class="form-control" name="uid_image">
                        </div>

                        <button type="submit" class="btn btn-success">Add Game</button>
                        <a href="{{ route('games.index') }}" class="btn btn-secondary">Back</a>
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

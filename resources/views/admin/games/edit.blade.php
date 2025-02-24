<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Game - Admin Panel</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
</head>
<body>

    @include('admin.navbar') <!-- Include navbar for consistency -->

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="border p-4 rounded">
                    <h2 class="mb-4">Edit Game</h2>

                    <!-- Back Button -->
                    <a href="{{ route('games.index') }}" class="btn btn-secondary mb-3">Back to Games</a>

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

                    <!-- Game Edit Form -->
                    <form method="POST" action="{{ route('games.update', $game->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="title">Game Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $game->title) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Game Description:</label>
                            <textarea name="description" id="description" class="form-control">{{ old('description', $game->description ?? '') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="game_category_id">Game Category</label>
                            <select name="game_category_id" id="game_category_id" class="form-control" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $game->game_category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cover_image">Cover Image</label>
                            <input type="file" name="cover_image" id="cover_image" class="form-control">
                            @if ($game->cover_image)
                                <img src="{{ url('storage/' . $game->cover_image) }}" width="100" class="mt-2">
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="full_cover_image">Full Cover Image</label>
                            <input type="file" name="full_cover_image" id="full_cover_image" class="form-control">
                            @if ($game->full_cover_image)
                                <img src="{{ url('storage/' . $game->full_cover_image) }}" width="100" class="mt-2">
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="uid_detail">Player ID Placeholder</label>
                            <input type="text" class="form-control" id="uid_detail" name="uid_detail" value="{{ $game->uid_detail }}">
                        </div>

                        <div class="form-group">
                            <label for="uid_image">Player ID Guide Image</label>
                            <input type="file" class="form-control" id="uid_image" name="uid_image">
                            @if($game->uid_image)
                                <img src="{{ url('storage/' . $game->uid_image) }}" class="img-preview" width="100">
                            @endif
                        </div>                        

                        <button type="submit" class="btn btn-success mt-3">Update Game</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

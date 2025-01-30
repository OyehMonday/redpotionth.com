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

    @include('admin.navbar') <!-- Include the same navbar as /admin/dashboard -->

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
   
                <!-- Manage Games Section -->
                <div class="border p-5">
                    <h2>Manage Games</h2>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($games as $game)
                                <tr>
                                    <td>
                                        @if ($game->cover_image)
                                            <img src="{{ asset('storage/' . $game->cover_image) }}" width="50">
                                        @else
                                            <span>No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $game->title }}</td>
                                    <td>{{ $game->category->name ?? 'Uncategorized' }}</td>
                                    <td>
                                        <a href="{{ route('games.edit', $game) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('games.destroy', $game) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this game?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($games->isEmpty())
                        <p class="text-center">No games found. <a href="{{ route('games.create') }}">Add a game</a>.</p>
                    @endif                
                    <a href="{{ route('games.create') }}" class="btn btn-primary mb-3">Add Game</a>
                </div>
                <hr class="section-divider">
                <!-- Manage Game Categories Section -->
                <div class="border p-5 mb-4">
                    <h2>Manage Game Categories</h2>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Category name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        <a href="{{ route('game-categories.edit', $category) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('game-categories.destroy', $category) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($categories->isEmpty())
                        <p class="text-center">No categories found. <a href="{{ route('game-categories.create') }}">Add a category</a>.</p>
                    @endif
                    <br><a href="{{ route('game-categories.create') }}" class="btn btn-primary mb-3">Add Category</a>
                </div>

            </div>
        </div>
    </div>

</body>
</html>

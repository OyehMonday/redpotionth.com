<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Games & Categories - Admin Panel</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <!-- jQuery & jQuery UI for Drag & Drop Sorting -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <style>
        /* Style for the drag handle */
        .handle {
            cursor: grab;
            font-size: 20px;
        }

        /* Highlight row when dragging */
        #sortable tr.ui-sortable-helper {
            background-color: #f8f9fa;
            border: 2px dashed #007bff;
        }
    </style>
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
                                <th>Sort</th> <!-- New column for drag handle -->
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortable">
                            @foreach ($games as $game)
                                <tr data-id="{{ $game->id }}">
                                    <td class="handle">☰</td> <!-- Drag handle -->
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
                                        <a href="{{ route('game-packages.index', $game) }}" class="btn btn-sm btn-info">Packages</a>
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

    <!-- JavaScript for Drag & Drop Sorting -->
    <script>
        $(document).ready(function() {
            $("#sortable").sortable({
                handle: '.handle',
                update: function(event, ui) {
                    let order = [];
                    $("#sortable tr").each(function(index) {
                        order.push({ id: $(this).data("id"), sort_order: index + 1 });
                    });

                    $.ajax({
                        url: "{{ route('games.sort') }}",
                        type: "POST",
                        data: {
                            order: order,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            console.log(response);
                        }
                    });
                }
            });
        });
    </script>

</body>
</html>

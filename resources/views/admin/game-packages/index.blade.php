@php
    $superAdminIds = explode(',', env('SUPER_ADMIN_IDS', '')); 
    $superAdminIds = array_map('trim', $superAdminIds); 
@endphp

@if(!in_array(auth()->user()->id ?? 0, $superAdminIds))
    <script>window.location.href = "/admin/orders";</script>
@endif
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages - Admin Panel</title>
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <style>
        .handle {
            cursor: grab;
            font-size: 20px;
        }

        #sortable tr.ui-sortable-helper {
            background-color: #f8f9fa;
            border: 2px dashed #007bff;
        }
    </style>
</head>
<body>

    @include('admin.navbar') 

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">

                <div class="border p-5">
                    <h2>Manage Packages for {{ $game->title }}</h2>


                    <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sort</th>
                            <th>Package Cover</th>
                            <th>Package Name</th>
                            <th>Details</th>
                            <th>Full Price</th>
                            <th>Selling Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable">
                        @foreach ($packages as $package)
                            <tr data-id="{{ $package->id }}">
                                <td class="handle">☰</td>
                                <td>
                                    @if ($package->cover_image)
                                        <img src="{{ asset('images/' . $package->cover_image) }}" width="50">
                                    @else
                                        <span>No Image</span>
                                    @endif
                                </td>
                                <td>{{ $package->name }}</td>
                                <td>{{ $package->detail ?? '-' }}</td>
                                <td>{{ number_format($package->full_price, 2) }} THB</td>
                                <td>{{ number_format($package->selling_price, 2) }} THB</td>
                                <td>
                                    <a href="{{ route('game-packages.edit', [$game, $package]) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('game-packages.destroy', [$game, $package]) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this package?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    </table>

                    @if($packages->isEmpty())
                        <p class="text-center">No packages found. <a href="{{ route('game-packages.create', $game) }}">Add a package</a>.</p>
                    @endif
                </div>
                <br>
                <a href="{{ route('game-packages.create', $game) }}" class="btn btn-primary mb-3">Add Package</a>

            </div>
        </div>
    </div>

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
                        url: "{{ route('game-packages.sort', $game) }}",
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
        
        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }
    </script>
</body>
</html>

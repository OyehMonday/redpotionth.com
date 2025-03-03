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

    <!-- jQuery & jQuery UI for Drag & Drop Sorting -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF Token for AJAX -->

    <style>
        .container-flex {
            display: flex;
            gap: 20px;
        }
        .selected-packages, .all-packages {
            flex: 1;
            border: 1px solid #ddd;
            padding: 15px;
            height: 500px;
            overflow-y: auto;
        }
        .selected-packages {
        }
        .sortable {
    min-height: 100px !important; /* Ensure enough space for dropping */
    border: 2px dashed #ddd;
}

        .handle {
            cursor: grab;
            font-size: 20px;
        }
        .sortable tr.ui-sortable-helper {
            background-color: #f8f9fa;
            border: 2px dashed #007bff;
        }
    </style>
</head>
<body>
    @include('admin.navbar')

    <div class="container mt-5">
        <h2>Manage Highlight Packages</h2>
        <div class="container-flex">
            <!-- Left Column: Selected Packages -->
            <div class="selected-packages">
                <h3>Selected Packages</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sort</th>
                            <th>Package Cover</th>
                            <th>Package Name</th>
                            <th>Game Name</th>
                        </tr>
                    </thead>
                    <tbody id="selected-packages" class="sortable">
                        @foreach ($selectedPackages as $package)
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
                                <td>{{ $package->game->title ?? 'Unknown Game' }}</td> <!-- Display Game Name -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Right Column: All Packages -->
            <div class="all-packages">
                <h3>All Packages</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sort</th>
                            <th>Package Cover</th>
                            <th>Package Name</th>
                            <th>Game Name</th> <!-- Show Game Name -->
                        </tr>
                    </thead>
                    <tbody id="all-packages" class="sortable">
                        @foreach ($allPackages as $package)
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
                                <td>{{ $package->game->title ?? 'Unknown Game' }}</td> <!-- Display Game Name -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript for Drag & Drop Selection -->
    <script>
        $(document).ready(function() {
            console.log("Sortable script loaded");

            $(".sortable").sortable({
                connectWith: ".sortable", 
                items: "tr",
                handle: ".handle",
                placeholder: "ui-state-highlight",

                receive: function(event, ui) {
                    let packageId = $(ui.item).data("id");
                    let targetList = $(this).attr("id");

                    if (targetList === "selected-packages") {
                        $.ajax({
                            url: "{{ route('admin.game-packages.sort') }}",
                            type: "POST",
                            data: {
                                id: packageId,
                                highlight: $("#selected-packages tr").length, 
                                _token: "{{ csrf_token() }}"
                            }
                        });
                    } else {
                        console.log("⬅ Moving back to all packages...");
                        $.ajax({
                            url: "{{ route('admin.game-packages.removeHighlight') }}",
                            type: "POST",
                            data: {
                                id: packageId,
                                _token: "{{ csrf_token() }}"
                            }
                        });
                    }
                },

                update: function(event, ui) {
                    let sortedOrder = [];
                    $("#selected-packages tr").each(function(index) {
                        sortedOrder.push({ id: $(this).data("id"), highlight: index + 1 });
                    });

                    $.ajax({
                        url: "{{ route('admin.game-packages.sort') }}",
                        type: "POST",
                        data: {
                            order: sortedOrder, 
                            _token: "{{ csrf_token() }}"
                        },
                    });
                }

            }).disableSelection();
        });
        
        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }
    </script>
</body>
</html>
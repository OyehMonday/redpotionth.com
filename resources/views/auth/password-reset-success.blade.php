@extends('layouts.app')

@section('title', 'Password Reset Successful')

@section('content')
    <div class="login-wrapper">
        <div class="login-container">
            <h1 class="login-title">Password Reset Successful</h1>
            <p class="login-subtitle">Redirecting to login page...</p>
        </div>
    </div>

    <script>
        // ✅ Force Page Refresh and Redirect to Login
        setTimeout(function () {
            location.reload(true);  // Full browser refresh
            window.location.href = "{{ route('login') }}";
        }, 2000); // Redirect after 2 seconds
    </script>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Panel')</title>

    <!-- Bootstrap & Other CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/support/support.css') }}">

    @stack('styles') <!-- Optional for additional styles -->
</head>
<body>

    @include('user.user_navbar') <!-- Include support navbar -->

    <div class="container mt-4">
        @yield('content') <!-- Main page content -->
    </div>

    <!-- Bootstrap & Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts') <!-- Optional for additional scripts -->

</body>
</html>

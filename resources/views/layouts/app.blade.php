
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    <!-- Bootstrap & Other CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" src="{{ asset('images/gecologo.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('storage/images/gecologo.png') }}" sizes="512x512">


    @stack('styles') <!-- Optional for additional styles -->
</head>
<body>

    @include('admin.admin_navbar') <!-- Include admin navbar -->

    <div class="container mt-4">
        @yield('content') <!-- Main page content -->
    </div>

    <!-- Bootstrap & Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts') <!-- Optional for additional scripts -->

</body>
</html>

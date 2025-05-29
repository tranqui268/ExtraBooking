<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Login')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 4 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column align-items-center justify-content-center min-vh-100">
    <div class="text-center mb-4">
        <img class="logo-image" src="https://res.cloudinary.com/dhis8yzem/image/upload/v1747887624/logo_2_o7zkws.png" alt="Logo">
        <div class="logo">Motorbike Repair</div>
    </div>
    @yield('content')
    @include('layouts.scripts')
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
</body>
</html>

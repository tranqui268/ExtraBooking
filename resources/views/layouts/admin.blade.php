<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('head')
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <div class="logo mb-4">
            <img src="https://res.cloudinary.com/dhis8yzem/image/upload/v1747887624/logo_2_o7zkws.png" alt="Logo"
                style="width: 40px;" class="mb-2">
            <div>Motorbike<span>Repair</span></div>
        </div>

        <nav class="nav flex-column">
            <a id="admin-dashboard" href="{{ url('/admin') }}"
                class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                <i class="icon bi bi-grid-fill"></i> Dashboard
            </a>
            <a id="user-menu" href="{{ url('/admin/inventory') }}" class="nav-link">
                <i class="icon bi bi-box"></i> User Management
            </a>
            <a id="service-menu" href="{{ url('/admin/services') }}" class="nav-link">
                <i class="icon bi bi-tools"></i> Service
            </a>
            <a id="customer-menu" href="{{ url('/admin/customers') }}" class="nav-link">
                <i class="icon bi bi-people"></i> Customers
            </a>
            <a id="booking-menu" href="{{ url('/admin/bookings') }}" class="nav-link">
                <i class="icon bi bi-calendar-event"></i> Bookings
            </a>
            <a id="employee-menu" href="{{ url('/admin/staff') }}" class="nav-link">
                <i class="icon bi bi-person-badge"></i> Employee Management
            </a>

            <a id="schedule-menu" href="{{ url('/admin/staff') }}" class="nav-link">
                <i class="icon bi bi-person-badge"></i> Schedule
            </a>

        </nav>

        <div class="mt-auto pt-4">
            <button onclick="logout()" type="button" class="btn btn-outline-danger w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
            </button>
        </div>
    </div>

    <div class="main">
        @yield('content')
    </div>
    @include('layouts.scripts')
    @include('scripts.auth-scripts')

</body>

</html>
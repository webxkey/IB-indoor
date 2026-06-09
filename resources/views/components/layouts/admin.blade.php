<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sportynix Hub Admin - Manage sports venues, bookings, users, and analytics with a professional dashboard for indoor facility operations.">
    <title>{{ $title ?? 'Sportynix Hub' }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#198754">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="IndoorB">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">


    @livewireStyles
    <style>
        :root {
            --primary-color: #198754;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: white;
            width: var(--sidebar-width);
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1rem;
            font-size: 1.25rem;
            border-bottom: 1px solid #dee2e6;
        }

        .sidebar-section {
            padding: 0 1rem;
        }

        .sidebar-heading {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            margin-top: 1rem;
        }

        .sidebar .nav-link {
            color: #333;
            padding: 0.75rem 1rem;
            margin: 0.125rem 0;
            border-radius: 0.375rem;
            transition: all 0.15s ease-in-out;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .mobile-app-promo {
            padding: 1rem;
        }

        /* Main Content */
        .admin-main-content {
            margin-left: var(--sidebar-width);
            padding: 0 2rem;
            min-width: 0;
            width: 100%;
        }

        /* Header */
        header {
            background-color: white;
            border-bottom: 1px solid #dee2e6;
            margin: 0 -2rem 2rem -2rem;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .search-box .form-control {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            width: 300px;
        }

        .search-box .input-group-text {
            border: 1px solid #dee2e6;
        }

        /* Stats Cards */
        .stat-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.15s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.7;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #dee2e6;
            padding: 1.5rem 1.5rem 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Ground Cards */
        .ground-card {
            transition: transform 0.15s ease-in-out;
        }

        .ground-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Upcoming Bookings */
        .upcoming-booking {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
            transition: background-color 0.15s ease-in-out;
        }

        .upcoming-booking:hover {
            background-color: #e9ecef;
        }

        .booking-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-radius: 50%;
            font-size: 1.25rem;
        }

        /* Ground Items */
        .ground-item {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
            transition: background-color 0.15s ease-in-out;
        }

        .ground-item:hover {
            background-color: #e9ecef;
        }

        .ground-avatar img {
            object-fit: cover;
        }

        /* Schedule Items */
        .schedule-item {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary-color);
        }

        .schedule-time {
            min-width: 80px;
        }

        /* Sport Items */
        .sport-item {
            padding: 0.5rem 0;
        }

        /* Ground Performance */
        .ground-performance-item {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
            margin-bottom: 1rem;
        }

        /* Report Metrics */
        .report-metric {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
        }

        /* Support Items */
        .support-item {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
        }

        .support-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-radius: 50%;
            font-size: 1.25rem;
        }

        /* Status Items */
        .status-item {
            padding: 0.75rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
        }

        /* Tutorial Items */
        .tutorial-item {
            cursor: pointer;
            transition: transform 0.15s ease-in-out;
        }

        .tutorial-item:hover {
            transform: translateY(-2px);
        }

        .tutorial-thumbnail {
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        /* Progress Circle */
        .progress-circle {
            position: relative;
            display: inline-block;
        }

        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Time Tracker */
        .time-display {
            padding: 2rem 0;
        }

        #sessionTimer {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #00ff88;
            text-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
        }

        /* Tables */
        .table-responsive {
            border-radius: 0.5rem;
        }

        .table th {
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            vertical-align: middle;
        }

        /* Responsive */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.2s ease-in-out;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1040;
                height: 100vh;
                width: var(--sidebar-width);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .admin-main-content {
                margin-left: 0 !important;
                padding: 0 1rem;
            }

            header {
                margin: 0 -1rem 1.5rem -1rem;
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .search-box .form-control {
                width: 180px;
            }
        }

        @media (max-width: 575.98px) {
            .search-box .form-control {
                width: 150px;
            }

            .stat-card .card-body {
                padding: 1rem;
            }

            .stat-card h2 {
                font-size: 1.5rem;
            }

            .btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-content>* {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Badge Styles */
        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        /* Button Styles */
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
        }

        /* Page Transitions */
        #main-content {
            animation: fadeInUp 0.3s ease-out;
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Hover Effects */
        .btn-group .btn:hover {
            z-index: 1;
        }

        /* Form Enhancements */
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        /* Modal Enhancements */
        .modal-content {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 1.5rem;
        }

        /* Accordion Enhancements */
        .accordion-button {
            font-weight: 500;
        }

        .accordion-button:not(.collapsed) {
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--primary-color);
        }

        /* List Group Enhancements */
        .list-group-item.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Progress Bar Enhancements */
        .progress {
            border-radius: 0.5rem;
        }

        .progress-bar {
            border-radius: 0.5rem;
        }

        /* Notification Enhancements */
        .alert {
            border-radius: 0.75rem;
            border: none;
        }

        /* Print Styles */
        @media print {

            .sidebar,
            .btn,
            .dropdown {
                display: none !important;
            }

            main {
                margin-left: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }
        }
    </style>
    @stack('styles')

</head>

<body>
    <div class="container-fluid px-0">
        <div class="d-flex">
            <!-- Sidebar -->
            <nav class="sidebar d-md-block collapse" id="adminSidebar">
                <div class="position-sticky pt-1">
                    <div class="sidebar-brand mb-2">
                        <div class="d-flex align-items-center gap-0" style="font-size: 1.8rem;">
                            <span class="fw-bold">Sportynix Hub</span>
                            <i class="fas fa-futbol text-success"></i>
                        </div>

                    </div>

                    <div class="sidebar-section">
                        <h6 class="sidebar-heading text-muted text-uppercase">Super Admin</h6>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                    href="{{ route('admin.dashboard') }}" data-section="dashboard">
                                    <i class="fas fa-th-large me-2"></i>
                                    Dashboard
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.indoors') ? 'active' : '' }}"
                                    href="{{ route('admin.indoors') }}" data-section="grounds">
                                    <i class="fas fa-map-marked-alt me-2"></i>
                                    Indoors
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.bookings') ? 'active' : '' }}"
                                    href="{{ route('admin.bookings') }}" data-section="bookings">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    Bookings
                                    <span class="badge bg-success ms-auto">12</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.customers') ? 'active' : '' }}"
                                    href="{{ route('admin.customers') }}" data-section="customers">
                                    <i class="fas fa-users me-2"></i>
                                    App Users
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.indoor-admins') ? 'active' : '' }}"
                                    href="{{ route('admin.indoor-admins') }}" data-section="indoor-admins">
                                    <i class="fas fa-users-cog me-2"></i>
                                    Indoor Admins
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.blogs-management') ? 'active' : '' }}"
                                    href="{{ route('admin.blogs-management') }}" data-section="indoor-admins">
                                    <i class="fas fa-newspaper me-2"></i>
                                    Blogs Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.landing-page') ? 'active' : '' }}"
                                    href="{{ route('admin.landing-page') }}" data-section="indoor-admins">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Landing Page
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.tournaments') ? 'active' : '' }}"
                                    href="{{ route('admin.tournaments') }}">
                                    <i class="fas fa-trophy me-2"></i>
                                    Tournaments
                                    @php
                                        $pendingTournaments = \App\Models\LeagueLeague::where('status','published')->count();
                                    @endphp
                                    @if($pendingTournaments > 0)
                                    <span class="badge bg-warning text-dark ms-auto">{{ $pendingTournaments }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.announcements') ? 'active' : '' }}"
                                    href="{{ route('admin.announcements') }}">
                                    <i class="fas fa-bullhorn me-2"></i>
                                    Announcements
                                </a>
                            </li>

                        </ul>
                    </div>

                    <div class="sidebar-section mt-4">
                        <h6 class="sidebar-heading text-muted text-uppercase">GENERAL</h6>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                                    <i class="fas fa-cog me-2"></i>
                                    Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-section="help">
                                    <i class="fas fa-question-circle me-2"></i>
                                    Help
                                </a>
                            </li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-link btn btn-link text-start w-100" style="text-decoration:none;">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    <!-- Mobile App Promotion -->
                    <div class="mobile-app-promo mt-4">
                        <div class="card bg-dark text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-mobile-alt fa-2x mb-2"></i>
                                <h6>Download our Mobile App</h6>
                                <p class="small">Manage bookings on the go</p>
                                <button class="btn btn-success btn-sm">Download</button>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main content -->
            <div class="admin-main-content flex-grow-1">
                <!-- Header -->
                <header class="d-flex justify-content-between align-items-center py-3 mb-4">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary d-md-none me-2" type="button" id="adminSidebarToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary me-2" title="Bookings">
                            <i class="fas fa-envelope"></i>
                        </a>
                        @php
                            $unreadCount = 0;
                            $recentNotifications = [];
                            if (auth()->check()) {
                                $unreadCount = \App\Models\BookingNotification::where('user_id', auth()->id())->where('is_read', false)->count();
                                $recentNotifications = \App\Models\BookingNotification::where('user_id', auth()->id())->orderByDesc('created_at')->limit(5)->get();
                            }
                        @endphp
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-secondary position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                @if($unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:320px;max-height:400px;overflow-y:auto;">
                                <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Notifications</span>
                                    @if($unreadCount > 0)
                                    <a href="{{ route('admin.bookings') }}" class="small text-success">Mark all read</a>
                                    @endif
                                </li>
                                @forelse($recentNotifications as $notif)
                                <li>
                                    <div class="dropdown-item d-block py-2 {{ !$notif->is_read ? 'bg-light' : '' }}">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2 mt-1">
                                                <i class="fas fa-{{ $notif->type === 'booking_created' ? 'calendar-plus text-success' : ($notif->type === 'booking_cancelled' ? 'calendar-times text-danger' : 'bell text-info') }}"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold small">{{ $notif->title }}</div>
                                                <div class="text-muted small">{{ Str::limit($notif->message, 60) }}</div>
                                                <div class="text-muted" style="font-size:0.7rem;">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="px-3 py-3 text-center text-muted small">No notifications yet</li>
                                @endforelse
                                <li class="border-top px-3 py-2 text-center">
                                    <a href="{{ route('admin.bookings') }}" class="small text-success">View all notifications</a>
                                </li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle me-2" width="40" height="40" style="object-fit:cover;">
                                <div class="text-start">
                                    <div class="fw-semibold">{{ auth()->user()->name ?? 'Admin' }}</div>
                                    <div class="small text-muted">{{ auth()->user()->email ?? '' }}</div>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text small text-muted">Signed in as <strong>{{ ucfirst(auth()->user()->role ?? 'admin') }}</strong></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/user/profile"><i class="fas fa-user me-2"></i>My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>

                <main id="main-content">
                    {{ $slot ?? '' }}
                </main>
            </div>
        </div>
    </div>
</body>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@livewireScripts

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('adminSidebar');
        const toggle  = document.getElementById('adminSidebarToggle');

        if (toggle && sidebar) {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 768 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            });
        }

        document.querySelectorAll('.dropdown-menu form').forEach(form => {
            form.addEventListener('click', e => e.stopPropagation());
        });
    });
</script>

@stack('scripts')

</body>

</html>
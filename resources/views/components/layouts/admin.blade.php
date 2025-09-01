<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Page Title' }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

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
        main {
            margin-left: var(--sidebar-width);
            padding: 0 2rem;
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
                transition: transform 0.15s ease-in-out;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            main {
                margin-left: 0;
                padding: 0 1rem;
            }

            header {
                margin: 0 -1rem 2rem -1rem;
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .search-box .form-control {
                width: 200px;
            }

            .d-flex.justify-content-between.align-items-center {
                flex-direction: column;
                align-items: stretch !important;
                gap: 1rem;
            }

            .d-flex.justify-content-between.align-items-center > div:last-child {
                text-align: center;
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

        .dashboard-content > * {
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
    
</head>
<body>

<div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-1">
                    <div class="sidebar-brand mb-2">
                        <div class="d-flex align-items-center gap-0" style="font-size: 1.8rem;">
                        <span class="fw-bold">IndoorB</span>
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
                                <i class="fas fa-chart-bar me-2"></i>
                                Indoor Admins
                            </a>
                        </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#" data-section="analytics">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="sidebar-section mt-4">
                        <h6 class="sidebar-heading text-muted text-uppercase">GENERAL</h6>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-section="settings">
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
                                <a class="nav-link" href="#">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Logout
                                </a>
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
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Header -->
                <header class="d-flex justify-content-between align-items-center py-3 mb-4">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="search-box">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" placeholder="Search..." style="background: transparent;">
                                <span class="input-group-text bg-transparent border-start-0">
                                    <kbd>⌘F</kbd>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary me-2">
                            <i class="fas fa-envelope"></i>
                        </button>
                        <button class="btn btn-outline-secondary me-3">
                            <i class="fas fa-bell"></i>
                        </button>
                        <div class="dropdown">
                            <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                                <div class="text-start">
                                    <div class="fw-semibold">John Manager</div>
                                    <div class="small text-muted">admin@bookingpro.com</div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><a class="dropdown-item" href="#">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </ul>
                        </div>
                    </div>
                </header>

            </main>
        </div>


   <!-- Main Content -->
            <main class="main-content">
                {{ $slot ?? '' }}
            </main>
</div>
</body>



           

    @livewireScripts

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('main-content');
        
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 991.98) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Prevent dropdown from closing when clicking inside forms
        document.querySelectorAll('.dropdown-menu form').forEach(form => {
            form.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        // Responsive adjustments
        function handleResize() {
            if (window.innerWidth > 991.98) {
                sidebar.classList.remove('show');
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize();
    });
</script>

@stack('scripts')

</body>
</html>

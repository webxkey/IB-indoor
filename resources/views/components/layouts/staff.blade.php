<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Staff Dashboard' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @stack('styles')
    @livewireStyles

    <style>
        :root {
            --primary-color: #19722d;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            background-color: white;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            overflow-y: auto;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0 2rem;
            width: 100%;
        }

        .header {
            background-color: white;
            border-bottom: 1px solid #dee2e6;
            margin: 0 -2rem 2rem -2rem;
            padding: 1rem 2rem;
        }

        .sidebar .nav-link {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            color: #333;
            display: flex;
            align-items: center;
            transition: all 0.15s ease-in-out;
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.2s ease-in-out;
                width: 100%;
                height: 100%;
                position: fixed;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
                padding: 1rem;
            }

            .header {
                margin: 0 -1rem 1rem -1rem;
                padding: 1rem;
            }

            .text-start.d-md-block {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar d-none d-md-block">
            <div class="position-sticky pt-1">
                <div class="sidebar-brand p-3 border-bottom">
                    <div class="d-flex align-items-center gap-0" style="font-size: 1.8rem;">
                        <span class="fw-bold">IndoorB</span>
                        <i class="fas fa-futbol text-success"></i>
                    </div>
                </div>

                <div class="sidebar-section p-3">
                    <h6 class="text-muted text-uppercase">Admin</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}">
                                <i class="fas fa-th-large me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('staff.bookings') ? 'active' : '' }}" href="{{ route('staff.bookings') }}">
                                <i class="fas fa-calendar-check me-2"></i> Bookings
                                <span class="badge bg-success ms-auto">12</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('staff.sports') ? 'active' : '' }}" href="{{ route('staff.sports') }}">
                                <i class="fas fa-map-marked-alt me-2"></i> Sports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('staff.reports') ? 'active' : '' }}" href="{{ route('staff.reports') }}">
                                <i class="fas fa-chart-bar me-2"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('staff.feedbacks') ? 'active' : '' }}" href="{{ route('staff.feedbacks') }}">
                                <i class="fa-solid fa-rss me-2"></i> Feedback
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section p-3 mt-3">
                    <h6 class="text-muted text-uppercase">General</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('staff.setting') ? 'active' : '' }}" href="{{ route('staff.setting') }}"><i class="fas fa-cog me-2"></i> Settings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('staff.help') ? 'active' : '' }}" href="{{ route('staff.help') }}"><i class="fas fa-question-circle me-2"></i> Help</a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link w-100 text-start">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <header class="header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button id="sidebarToggle" class="btn btn-outline-secondary d-md-none me-2">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="search-box">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." style="background: transparent;">
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary me-3"><i class="fas fa-bell"></i></button>

                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                            <div class="text-start d-none d-md-block">
                                <div class="fw-semibold">{{ Auth::user()->name ?? 'Staff Member' }}</div>
                                <div class="small text-muted">{{ Auth::user()->email ?? 'staff@example.com' }}</div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- Shown only on small screens -->
                            <li class="d-md-none px-3 py-2 border-bottom text-center">
                                <div class="fw-semibold">{{ Auth::user()->name ?? 'Staff Member' }}</div>
                                <div class="small text-muted">{{ Auth::user()->email ?? 'staff@example.com' }}</div>
                            </li>
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('staff.setting') }}">Settings</a></li>
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

@livewireScripts

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('show');
            });
        }

        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 991.98) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
                if (!isClickInsideSidebar && !isClickOnToggle) {
                    sidebar.classList.remove('show');
                }
            }
        });
    });
</script>
@stack('scripts')
</body>
</html>

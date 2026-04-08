<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Staff Dashboard' }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                padding: 0.25rem;
            }

            .header {
                margin: 0 -0.25rem 0.5rem -0.25rem;
                padding: 0.6rem 1rem;
            }

            .text-start.d-md-block {
                display: none !important;
            }

            .main-content {
                padding-bottom: 70px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row">
            <!-- Sidebar -->
            <nav id="sidebar" class="sidebar">
                <div class="position-sticky pt-1">
                    <div class="sidebar-brand p-3 border-bottom">
                        <div class="d-flex align-items-center gap-0" style="font-size: 1.8rem;">
                            <span class="fw-bold">IndoorB</span>
                            <i class="fas fa-futbol text-success"></i>
                        </div>
                    </div>

                    <div class="sidebar-section p-3">
                        <h6 class="text-muted text-uppercase mb-2">Admin</h6>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}">
                                    <i class="fas fa-th-large me-2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('staff.bookings') ? 'active' : '' }}" href="{{ route('staff.bookings') }}">
                                    <i class="fas fa-calendar-check me-2"></i> Bookings
                                    @php
                                        $todayBookings = 0;
                                        if (auth()->check()) {
                                            $complexId = auth()->user()->complex_id ?? null;
                                            if ($complexId) {
                                                $todayBookings = \App\Models\BookingBooking::where('complex_id_id', $complexId)
                                                    ->whereDate('booking_date', now()->toDateString())
                                                    ->count();
                                            }
                                        }
                                    @endphp
                                    <span class="badge bg-success ms-auto">{{ $todayBookings }}</span>
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

            <!-- Mobile Bottom Navigation Bar -->
            <nav class="d-md-none fixed-bottom bg-white border-top py-1" style="z-index:1050;">
                <div class="d-flex justify-content-around align-items-center">
                    <a href="{{ route('staff.dashboard') }}" class="d-flex flex-column align-items-center text-decoration-none {{ request()->routeIs('staff.dashboard') ? 'text-success' : 'text-muted' }}" style="font-size:0.65rem;">
                        <i class="fas fa-th-large mb-1" style="font-size:1.1rem;"></i>Home
                    </a>
                    <a href="{{ route('staff.bookings') }}" class="d-flex flex-column align-items-center text-decoration-none {{ request()->routeIs('staff.bookings') ? 'text-success' : 'text-muted' }}" style="font-size:0.65rem;">
                        <i class="fas fa-calendar-check mb-1" style="font-size:1.1rem;"></i>Bookings
                    </a>
                    <a href="{{ route('staff.sports') }}" class="d-flex flex-column align-items-center text-decoration-none {{ request()->routeIs('staff.sports') ? 'text-success' : 'text-muted' }}" style="font-size:0.65rem;">
                        <i class="fas fa-map-marked-alt mb-1" style="font-size:1.1rem;"></i>Sports
                    </a>
                    <a href="{{ route('staff.reports') }}" class="d-flex flex-column align-items-center text-decoration-none {{ request()->routeIs('staff.reports') ? 'text-success' : 'text-muted' }}" style="font-size:0.65rem;">
                        <i class="fas fa-chart-bar mb-1" style="font-size:1.1rem;"></i>Reports
                    </a>
                    <a href="{{ route('staff.setting') }}" class="d-flex flex-column align-items-center text-decoration-none {{ request()->routeIs('staff.setting') ? 'text-success' : 'text-muted' }}" style="font-size:0.65rem;">
                        <i class="fas fa-cog mb-1" style="font-size:1.1rem;"></i>Settings
                    </a>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="main-content flex-grow-1">
                <header class="header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <button id="sidebarToggle" class="btn btn-outline-secondary d-md-none me-2">
                            <i class="fas fa-bars"></i>
                        </button>
                       
                    </div>
                    <div class="d-flex align-items-center">
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
                                    <a href="{{ route('staff.setting') }}?section=notifications" class="small text-success">Mark all read</a>
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
                                    <a href="{{ route('staff.setting') }}?section=notifications" class="small text-success">View all notifications</a>
                                </li>
                            </ul>
                        </div>

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
                                <li><a class="dropdown-item" href="{{ route('staff.setting') }}"><i class="fas fa-user me-2"></i>My Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('staff.setting') }}">Settings</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
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
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });

                document.addEventListener('click', function(event) {
                    if (window.innerWidth <= 991.98) {
                        const isClickInsideSidebar = sidebar.contains(event.target);
                        const isClickOnToggle = sidebarToggle.contains(event.target);
                        if (!isClickInsideSidebar && !isClickOnToggle) {
                            sidebar.classList.remove('show');
                        }
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
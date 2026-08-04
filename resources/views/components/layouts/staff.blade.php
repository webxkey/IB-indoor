<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Staff Dashboard' }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#19722d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Sportynix Hub">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/icons/icon-152.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('images/icons/icon-144.png') }}">
    <link rel="apple-touch-icon" sizes="128x128" href="{{ asset('images/icons/icon-128.png') }}">
    <meta name="msapplication-TileImage" content="{{ asset('images/icons/icon-144.png') }}">
    <meta name="msapplication-TileColor" content="#19722d">

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

        .sidebar .cafeteria-main-toggle {
            color: #0f172a !important;
            font-weight: 700 !important;
            font-size: 0.95rem !important;
            background: transparent !important;
            padding: 0.75rem 1rem !important;
        }

        .sidebar .cafeteria-main-toggle:hover {
            color: #19722d !important;
            background-color: #f8f9fa !important;
        }

        .sidebar .cafeteria-main-toggle i.fa-utensils {
            color: #0f172a !important;
        }

        .sidebar .nav-link .submenu-arrow {
            transition: transform 0.2s ease-in-out;
            font-size: 0.75rem;
            color: #475569 !important;
        }

        .sidebar .nav-link.collapsed .submenu-arrow {
            transform: rotate(-90deg);
        }

        .sidebar .nav-link:not(.collapsed) .submenu-arrow {
            transform: rotate(0deg);
        }

        .cafeteria-submenu-container {
            display: none;
        }

        .cafeteria-submenu-container.open {
            display: block !important;
        }

        .sidebar .submenu-nav {
            border-left: 2px solid #cbd5e1;
            margin-left: 1.25rem !important;
            padding-left: 0.25rem;
        }

        .sidebar .submenu-nav .nav-link {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.88rem !important;
            color: #334155 !important;
            display: flex !important;
            align-items: center !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .sidebar .submenu-nav .nav-link.active {
            background-color: rgba(25, 114, 45, 0.12) !important;
            color: #19722d !important;
            font-weight: 600 !important;
        }

        .sidebar .submenu-nav .nav-link:hover {
            background-color: #f1f5f9 !important;
            color: #19722d !important;
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
                padding-bottom: 90px !important;
            }

            .header {
                margin: 0 -0.25rem 0.5rem -0.25rem;
                padding: 0.5rem 0.75rem;
            }

            .main-content {
                padding-bottom: 90px;
            }

            /* Notification dropdown: full-width-ish on mobile */
            .notif-dropdown {
                width: calc(100vw - 1.5rem);
                min-width: unset !important;
            }
        }
    </style>
</head>

<body>

    {{-- PWA Install Banner --}}
    <div id="pwa-install-banner" style="display:none;position:fixed;bottom:80px;left:12px;right:12px;z-index:9999;
        background:#19722d;color:#fff;border-radius:12px;padding:12px 16px;
        align-items:center;gap:12px;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
        <img src="{{ asset('images/icons/icon-72.png') }}" width="40" height="40" style="border-radius:8px;flex-shrink:0;">
        <div style="flex:1;min-width:0;">
            <div style="font-weight:700;font-size:0.9rem;">Install Sportynix Hub</div>
            <div style="font-size:0.75rem;opacity:0.85;">Add to home screen for quick access</div>
        </div>
        <button onclick="installPWA()" style="background:#fff;color:#19722d;border:none;border-radius:8px;
            padding:8px 14px;font-weight:700;font-size:0.82rem;cursor:pointer;flex-shrink:0;">Install</button>
        <button onclick="dismissPWA()" style="background:transparent;border:none;color:#fff;
            font-size:1.2rem;cursor:pointer;padding:0 4px;flex-shrink:0;">✕</button>
    </div>

    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row">
            <!-- Sidebar -->
            <nav id="sidebar" class="sidebar">
                <div class="position-sticky pt-1">
                    <div class="sidebar-brand p-3 border-bottom">
                        <div class="d-flex align-items-center gap-0" style="font-size: 1.8rem;">
                            <span class="fw-bold">Sportynix Hub</span>
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
                                <a class="nav-link {{ request()->routeIs('staff.finance') ? 'active' : '' }}" href="{{ route('staff.finance') }}">
                                    <i class="fas fa-file-invoice-dollar me-2"></i> Finance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('staff.permanent-bookings') ? 'active' : '' }}" href="{{ route('staff.permanent-bookings') }}">
                                    <i class="fas fa-calendar-alt me-2"></i> Permanent Bookings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('staff.sports') ? 'active' : '' }}" href="{{ route('staff.sports') }}">
                                    <i class="fas fa-map-marked-alt me-2"></i> Sports
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('staff.tournament') ? 'active' : '' }}" href="{{ route('staff.tournament') }}">
                                    <i class="fas fa-trophy me-2"></i> Tournament
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('staff.announcements') ? 'active' : '' }}" href="{{ route('staff.announcements') }}">
                                    <i class="fas fa-bullhorn me-2"></i> Announcements
                                </a>
                            </li>
                        </ul>
                    </div>

                    @php
                        $isCafeteriaActive = request()->routeIs('staff.cafeteria.*');
                    @endphp
                    <div class="sidebar-section p-3 mt-2">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <button type="button" 
                                        id="cafeteriaToggleBtn"
                                        class="nav-link cafeteria-main-toggle w-100 text-start border-0 bg-transparent d-flex align-items-center justify-content-between {{ !$isCafeteriaActive ? 'collapsed' : '' }}" 
                                        aria-expanded="{{ $isCafeteriaActive ? 'true' : 'false' }}" 
                                        aria-controls="cafeteriaSubmenu"
                                        style="cursor: pointer;">
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-utensils me-2"></i>
                                        <span class="fw-semibold">Cafeteria</span>
                                    </span>
                                    <i class="fas fa-chevron-down submenu-arrow"></i>
                                </button>
                                <div class="cafeteria-submenu-container {{ $isCafeteriaActive ? 'open' : '' }}" id="cafeteriaSubmenu">
                                    <ul class="nav flex-column submenu-nav mt-1">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('staff.cafeteria.billing') ? 'active' : '' }}" href="{{ route('staff.cafeteria.billing') }}">
                                                <i class="fas fa-cash-register me-2"></i> Billing Terminal
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('staff.cafeteria.products') ? 'active' : '' }}" href="{{ route('staff.cafeteria.products') }}">
                                                <i class="fas fa-store me-2"></i> Products & Categories
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('staff.cafeteria.sales') ? 'active' : '' }}" href="{{ route('staff.cafeteria.sales') }}">
                                                <i class="fas fa-history me-2"></i> Sales History
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('staff.cafeteria.reports') ? 'active' : '' }}" href="{{ route('staff.cafeteria.reports') }}">
                                                <i class="fas fa-chart-line me-2"></i> Sales Reports
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="sidebar-section p-3 mt-3">
                        <h6 class="text-muted text-uppercase">General</h6>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('staff.banners') ? 'active' : '' }}" href="{{ route('staff.banners') }}"><i class="fas fa-images me-2"></i> Banner Management</a>
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
            <nav class="d-md-none fixed-bottom bg-white border-top" style="z-index:1050;padding:6px 0 env(safe-area-inset-bottom, 6px);">
                <div class="d-flex justify-content-around align-items-center">
                    @php
                    $navItems = [
                        ['route' => 'staff.dashboard',   'icon' => 'fa-th-large',       'label' => 'Home'],
                        ['route' => 'staff.bookings',    'icon' => 'fa-calendar-check', 'label' => 'Bookings'],
                        ['route' => 'staff.sports',      'icon' => 'fa-futbol',         'label' => 'Sports'],
                        ['route' => 'staff.tournament',  'icon' => 'fa-trophy',         'label' => 'Tournament'],
                        ['route' => 'staff.setting',     'icon' => 'fa-cog',            'label' => 'Settings'],
                    ];
                    @endphp
                    @foreach($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="d-flex flex-column align-items-center text-decoration-none position-relative"
                       style="flex:1;padding:6px 4px;color:{{ $active ? '#19722d' : '#94a3b8' }};">
                        @if($active)
                        <span style="position:absolute;top:0;left:50%;transform:translateX(-50%);
                            width:32px;height:3px;background:#19722d;border-radius:0 0 4px 4px;"></span>
                        @endif
                        <i class="fas {{ $item['icon'] }}" style="font-size:1.4rem;margin-bottom:3px;"></i>
                        <span style="font-size:0.7rem;font-weight:{{ $active ? '700' : '500' }};">{{ $item['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </nav>

            <!-- Main Content -->
            <div class="main-content flex-grow-1">
                <header class="header d-flex justify-content-between align-items-center">
                    <!-- Left: hamburger + logo on mobile -->
                    <div class="d-flex align-items-center">
                        <button id="sidebarToggle" class="btn btn-outline-secondary d-md-none me-2" style="padding:0.35rem 0.6rem;">
                            <i class="fas fa-bars"></i>
                        </button>
                        <!-- App logo — visible on mobile only -->
                        <div class="d-md-none d-flex align-items-center gap-1" style="font-size:1.3rem;font-weight:700;line-height:1;">
                            <span>Sportynix Hub</span><i class="fas fa-futbol text-success" style="font-size:1rem;"></i>
                        </div>
                    </div>

                    <!-- Right: bell + avatar -->
                    <div class="d-flex align-items-center gap-2">
                        @php
                            $unreadCount = 0;
                            $recentNotifications = [];
                            if (auth()->check()) {
                                $unreadCount = \App\Models\BookingNotification::where('user_id', auth()->id())->where('is_read', false)->count();
                                $recentNotifications = \App\Models\BookingNotification::where('user_id', auth()->id())->orderByDesc('created_at')->limit(5)->get();
                            }
                        @endphp

                        <!-- Notification Bell -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding:0.35rem 0.6rem;">
                                <i class="fas fa-bell"></i>
                                @if($unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.55rem;">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow notif-dropdown" style="max-height:380px;overflow-y:auto;">
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
                                                <div class="text-muted small">{{ Str::limit($notif->message, 55) }}</div>
                                                <div class="text-muted" style="font-size:0.7rem;">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="px-3 py-3 text-center text-muted small">No notifications yet</li>
                                @endforelse
                                <li class="border-top px-3 py-2 text-center">
                                    <a href="{{ route('staff.setting') }}?section=notifications" class="small text-success">View all</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="dropdown">
                            <button type="button" class="btn p-0 border-0 bg-transparent d-flex align-items-center dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle" width="36" height="36" style="object-fit:cover;">
                                <div class="text-start d-none d-md-block ms-2">
                                    <div class="fw-semibold" style="font-size:0.9rem;line-height:1.2;">{{ Auth::user()->name ?? 'Staff Member' }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ Auth::user()->email ?? '' }}</div>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width:200px;z-index:1060;">
                                <li class="px-3 py-2 border-bottom text-center">
                                    <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle mb-1" width="44" height="44" style="object-fit:cover;">
                                    <div class="fw-semibold small">{{ Auth::user()->name ?? 'Staff Member' }}</div>
                                    <div class="text-muted" style="font-size:0.72rem;">{{ Auth::user()->email ?? '' }}</div>
                                </li>
                                <li><a class="dropdown-item" href="/user/profile"><i class="fas fa-user me-2"></i>My Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('staff.setting') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

            const cafeteriaToggle = document.getElementById('cafeteriaToggleBtn');
            const cafeteriaSubmenu = document.getElementById('cafeteriaSubmenu');
            if (cafeteriaToggle && cafeteriaSubmenu) {
                cafeteriaToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isOpen = cafeteriaSubmenu.classList.contains('open');
                    if (isOpen) {
                        cafeteriaSubmenu.classList.remove('open');
                        cafeteriaToggle.classList.add('collapsed');
                        cafeteriaToggle.setAttribute('aria-expanded', 'false');
                    } else {
                        cafeteriaSubmenu.classList.add('open');
                        cafeteriaToggle.classList.remove('collapsed');
                        cafeteriaToggle.setAttribute('aria-expanded', 'true');
                    }
                });
            }
        });
    </script>
    @stack('scripts')

    {{-- Global Booking Notification Poller --}}
    <div id="global-notif-container" style="position:fixed;top:70px;right:16px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none;max-width:360px;width:calc(100vw - 32px);"></div>

    <script>
    (function () {
        var pollUrl   = '{{ route("staff.notifications.poll") }}';
        var markUrl   = '{{ route("staff.notifications.mark-read") }}';
        var csrfToken = '{{ csrf_token() }}';
        var lastSeen  = new Date().toISOString();
        var bellBadge = document.querySelector('.badge.bg-danger.rounded-pill');

        function iconFor(type) {
            if (type === 'booking_created')   return 'fa-calendar-plus';
            if (type === 'booking_cancelled') return 'fa-calendar-times';
            if (type === 'booking_updated')   return 'fa-calendar-check';
            return 'fa-bell';
        }
        function colorFor(type) {
            if (type === 'booking_created')   return '#19722d';
            if (type === 'booking_cancelled') return '#dc3545';
            return '#0ea5e9';
        }

        function showToast(notif) {
            var container = document.getElementById('global-notif-container');
            if (!container) return;

            var toast = document.createElement('div');
            toast.style.cssText = [
                'background:#fff',
                'border-radius:12px',
                'box-shadow:0 4px 20px rgba(0,0,0,0.15)',
                'padding:14px 16px',
                'display:flex',
                'align-items:flex-start',
                'gap:12px',
                'pointer-events:auto',
                'border-left:4px solid ' + colorFor(notif.type),
                'animation:slideInRight 0.3s ease',
                'max-width:360px',
                'width:100%',
            ].join(';');

            toast.innerHTML =
                '<div style="flex-shrink:0;width:36px;height:36px;border-radius:50%;background:' + colorFor(notif.type) + '22;display:flex;align-items:center;justify-content:center;">' +
                    '<i class="fas ' + iconFor(notif.type) + '" style="color:' + colorFor(notif.type) + ';font-size:0.95rem;"></i>' +
                '</div>' +
                '<div style="flex:1;min-width:0;">' +
                    '<div style="font-weight:700;font-size:0.88rem;color:#1e293b;margin-bottom:2px;">' + notif.title + '</div>' +
                    '<div style="font-size:0.8rem;color:#64748b;word-break:break-word;">' + notif.message + '</div>' +
                    '<div style="font-size:0.72rem;color:#94a3b8;margin-top:4px;">Just now</div>' +
                '</div>' +
                '<button onclick="this.closest(\'div[style]\').remove()" style="background:none;border:none;cursor:pointer;padding:0;color:#94a3b8;font-size:1rem;line-height:1;flex-shrink:0;">&#x2715;</button>';

            container.prepend(toast);

            // Auto-remove after 8 seconds
            setTimeout(function () {
                if (toast.parentNode) {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.4s';
                    setTimeout(function () { if (toast.parentNode) toast.remove(); }, 400);
                }
            }, 8000);
        }

        function updateBadge(count) {
            // Try to find or create badge on the bell button
            var bell = document.querySelector('#global-notif-container ~ * .fa-bell, .fa-bell')
                    || document.querySelector('[data-bs-toggle="dropdown"] .fa-bell');
            if (!bell) return;
            var btn = bell.closest('button');
            if (!btn) return;

            var badge = btn.querySelector('.badge.bg-danger');
            if (count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                    badge.style.fontSize = '0.55rem';
                    btn.style.position = 'relative';
                    btn.appendChild(badge);
                }
                badge.textContent = count > 9 ? '9+' : count;
            } else if (badge) {
                badge.remove();
            }
        }

        function poll() {
            fetch(pollUrl + '?since=' + encodeURIComponent(lastSeen), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
            })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data) return;
                lastSeen = data.server_time || new Date().toISOString();
                updateBadge(data.unread_count);
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(function (n) { showToast(n); });
                }
            })
            .catch(function () {});
        }

        // Add animation keyframes
        var style = document.createElement('style');
        style.textContent = '@keyframes slideInRight{from{transform:translateX(110%);opacity:0}to{transform:translateX(0);opacity:1}}';
        document.head.appendChild(style);

        // Start polling after 10 s, then every 20 s
        setTimeout(function () {
            poll();
            setInterval(poll, 20000);
        }, 10000);

        // Also mark notifications as read when bell dropdown is opened
        document.addEventListener('shown.bs.dropdown', function (e) {
            var toggle = e.relatedTarget;
            if (toggle && toggle.querySelector && toggle.querySelector('.fa-bell')) {
                fetch(markUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                }).then(function () { updateBadge(0); }).catch(function () {});
            }
        });
    })();
    </script>

    {{-- PWA Service Worker + Install Prompt --}}
    <script>
        // Register service worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW registered:', reg.scope))
                    .catch(err => console.log('SW error:', err));
            });
        }

        // Install prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', e => {
            e.preventDefault();
            deferredPrompt = e;
            const banner = document.getElementById('pwa-install-banner');
            if (banner) banner.style.display = 'flex';
        });

        function installPWA() {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(result => {
                deferredPrompt = null;
                document.getElementById('pwa-install-banner').style.display = 'none';
            });
        }

        function dismissPWA() {
            document.getElementById('pwa-install-banner').style.display = 'none';
            localStorage.setItem('pwa-dismissed', '1');
        }

        // Hide banner if already dismissed
        window.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('pwa-dismissed')) {
                const banner = document.getElementById('pwa-install-banner');
                if (banner) banner.remove();
            }
        });

        // Hide banner if already installed
        window.addEventListener('appinstalled', () => {
            const banner = document.getElementById('pwa-install-banner');
            if (banner) banner.style.display = 'none';
        });
    </script>
</body>

</html>
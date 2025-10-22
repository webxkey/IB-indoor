<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Indoor Booking' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Navbar */
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        /* Payoneer Mock Logo Styling */
        .navbar-brand .mock-logo-text {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Navbar link adjustments for Payoneer style */
        .navbar .nav-link {
            color: #333 !important; /* Slightly darker links */
            font-weight: 500;
            /* Reduced padding/margin to match the image spacing */
            padding-right: 0.5rem !important; 
            padding-left: 0.5rem !important;
        }
        
        .navbar-light .navbar-nav .nav-link:focus, .navbar-light .navbar-nav .nav-link:hover {
            color: #000 !important; /* Hover effect */
        }

        /* Payoneer Register Button Gradient */
        .register-btn {
            background: linear-gradient(90deg, #7b5aff 0%, #a453ff 100%); /* The purple-to-blue gradient */
            color: #fff !important;
            border: none;
            padding: 8px 20px;
            border-radius: 6px; /* Slightly rounded corners */
            font-weight: 600;
            transition: opacity 0.3s ease;
            margin-left: 10px; /* Space from 'Sign In' */
        }

        .register-btn:hover {
            opacity: 0.9;
            color: #fff !important;
        }

        /* Adjusting dropdown arrows to be subtle like in the image */
        .dropdown-toggle::after {
            vertical-align: 0.15em;
            margin-left: 0.255em;
        }
        
        /* Hero Section for Landing Page */
        .hero {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: url('../images/bgwatch.jpg') no-repeat center center/cover;
            color: #fff;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top:0; left:0; width:100%; height:100%;
            background-color: rgba(0,0,0,0.4);
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.2rem;
            margin: 20px 0;
        }

        .hero .btn {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
        }

        /* Footer */
        footer {
            background-color: #304b8a;
            color: #fff;
            padding: 40px 0;
        }
        footer a {
            color: #fff;
        }
        footer a:hover {
            color: #f0f0f0;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Login page styling */
        .login-container {
            height: 100vh;
            width: 100vw;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            z-index: 0;
        }

        .login-form-overlay {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            z-index: 1;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .user-icon-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .user-icon-container i {
            font-size: 3rem;
            color: #304b8a;
            background: #f0f0f0;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-control {
            border-radius: 25px;
            padding: 12px 20px;
            border: 1px solid #ddd;
        }

        .login-btn {
            width: 100%;
            border-radius: 25px;
            padding: 10px;
            background-color: #304b8a;
            border: none;
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    @php
        $currentRoute = Route::currentRouteName();
    @endphp

    @if(!in_array($currentRoute, ['login', 'register']))
    <nav class="navbar navbar-expand-lg navbar-light py-3">
        <div class="container">
            <a class="navbar-brand me-auto" href="{{ route('landing') }}">
                <span class="mock-logo-text">
                    <span style="color: #FF7043;">P</span>
                    <span style="color: #4CAF50;">a</span>
                    <span style="color: #2196F3;">y</span>
                    <span style="color: #FFC107;">o</span>
                    <span style="color: #9C27B0;">n</span>
                    <span style="color: #304b8a;">e</span>
                    <span style="color: #FF5252;">e</span>
                    <span style="color: #7B1FA2;">r</span>
                </span>
            </a>

            <div class="d-flex align-items-center">
                <ul class="navbar-nav flex-row">
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link dropdown-toggle" href="#" id="freelancersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Freelancers
                        </a>
                    </li>
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link dropdown-toggle" href="#" id="businessDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Business
                        </a>
                    </li>
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link dropdown-toggle" href="#" id="marketplaceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Marketplace
                        </a>
                    </li>
                    
                    <li class="nav-item me-3">
                        <a class="nav-link" href="{{ route('login') }}">Sign In</a>
                    </li>
                </ul>

                <a class="btn register-btn" href="{{ route('register') }}">
                    Register &rightarrow;
                </a>
            </div>
        </div>
    </nav>
    @endif

    <main>
        {{ $slot }}
    </main>

    @if(!in_array($currentRoute, ['login', 'register']))
    <footer class="mt-5">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>About Us</h5>
                    <p>IndoorBooking is a modern solution for managing indoor complexes and bookings seamlessly.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('login') }}">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Contact</h5>
                    <p>Email: support@indoorbooking.com</p>
                    <p>Phone: +1 234 567 890</p>
                </div>
            </div>
            <p class="mt-3">&copy; {{ date('Y') }} IndoorBooking. All rights reserved.</p>
        </div>
    </footer>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>
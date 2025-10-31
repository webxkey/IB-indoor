<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Indoor Booking - Professional platform for managing sports venues, bookings, and events. Streamline your indoor facility operations with ease and efficiency.">
    <title>Indoor Booking</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"

        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
        <!-- Custom Mobile Sidebar Menu CSS -->
        <style>
            /* Mobile Sidebar Menu Styles */
            @media (max-width: 991.98px) {
                .mobile-sidebar {
                    position: fixed;
                    top: 0;
                    right: -100vw;
                    width: 80vw;
                    max-width: 350px;
                    height: 100vh;
                    background: #fff;
                    box-shadow: -2px 0 32px 0 rgba(25,135,84,0.18), -2px 0 8px 0 rgba(0,0,0,0.10);
                    z-index: 2000;
                    transition: right 0.45s cubic-bezier(0.22, 1, 0.36, 1);
                    padding: 2rem 2.5rem 1.5rem 1.5rem; /* Increased right padding */
                    overflow-y: auto;
                    display: flex;
                    flex-direction: column;
                    border-top-left-radius: 32px;
                    border-bottom-left-radius: 32px;
                    -webkit-overflow-scrolling: touch;
                    will-change: right;
                }
                .mobile-sidebar.open {
                    right: 0;
                }
                .mobile-sidebar .close-btn {
                    position: absolute;
                    top: 18px;
                    right: 18px;
                    font-size: 2rem;
                    color: #0A7C4D;
                    background: none;
                    border: none;
                    z-index: 2100;
                }
                .mobile-sidebar .navbar-nav {
                    flex-direction: column;
                    gap: 1.2rem;
                }
                .mobile-sidebar .nav-link {
                    font-size: 1.2rem;
                    padding: 0.5rem 0;
                }
                .mobile-sidebar .btn-primary {
                    margin-top: 1.5rem;
                    width: 100%;
                    background-color: rgb(25, 135, 84) !important;
                    color: #fff !important;
                    border: none;
                    font-weight: 600;
                    border-radius: 50px;
                    box-shadow: none;
                    transition: background 0.3s ease;
                }
                .mobile-sidebar .btn-primary:hover {
                    background-color: #146c43 !important;
                    color: #fff !important;
                    transform: translateY(-2px);
                }
                .mobile-sidebar .navbar-brand {
                    margin-bottom: 2rem;
                }
                .mobile-sidebar-backdrop {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(25,135,84,0.10);
                    z-index: 1999;
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity 0.45s cubic-bezier(0.22, 1, 0.36, 1);
                }
                .mobile-sidebar-backdrop.open {
                    opacity: 1;
                    pointer-events: auto;
                }
                .navbar-collapse {
                    display: none !important;
                }
            }
        </style>
        



    <style>
        /* Body full height flex layout */
        body {
            --primary: #0A7C4D;
            --primary-dark: #08663d;
            --accent: #4CAF50;
            --bg-light: #E8F5EB;
            --text-dark: #1A1A1A;
            --text-muted: #6c757d;
            --white: #fff;
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
            scroll-behavior: smooth;
            line-height: 1.6;
        }


        /* Custom Navbar Styling */

        .navbar {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 90%;
            max-width: 1200px;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 50px;
            padding: 12px 30px 24px 30px; /* Added bottom padding */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .navbar.scrolled {
            top: 10px;
            width: 95%;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #0A7C4D !important;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .navbar-brand:hover i {
            transform: rotate(-15deg);
        }

        .nav-link {
            color: #1A1A1A !important;
            font-weight: 500;
            margin: 0 8px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateX(-50%);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #0A7C4D !important;
        }

        /* Navbar Buttons - Themed */
        .navbar .btn-primary {
            background-color: rgb(25, 135, 84);
            border: none;
            color: #ffffff;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .navbar .btn-primary:hover {
            background-color: #ffffff;
            color: rgb(25, 135, 84);
            border: 1px solid rgb(25, 135, 84);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }


        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #E8F5EB 0%, #FFFFFF 100%);
            display: flex;
            align-items: center;
            position: relative;
            padding-top: 100px;
            overflow: hidden;
        }

        /* Footer Styling - Green Theme */
        /* 🌿 Indoor Booking Footer - Updated Professional Green Theme */

        /* Primary Theme Color */
        :root {
            --indoor-b-primary: rgb(25, 135, 84);
            /* Green */
            --indoor-b-primary-dark: rgb(18, 97, 60);
            /* Darker Green for hover */
            --indoor-b-background: #f8f9fa;
            /* Light Grey background for contrast */
            --indoor-b-text-secondary: #6c757d;
            /* Soft dark grey for secondary info */
        }

        /* IndoorB text next to the logo */
        .indoorb-logo-text {
            color: rgb(25, 135, 84);
            /* Green color */
        }


        /* 1. Footer Container - Added light background for contrast */
        .indoor-booking-footer {
            background-color: var(--indoor-b-background);
            /* Light Grey background */
            border-top: 1px solid #e0e0e0;
            /* Primary green color is removed from the container for better contrast */
            color: #212529;
            /* Standard dark text color */
        }

        /* 2. All Text - Reset default text color, use a darker one for titles and links */
        /* This rule is simplified to only target link/title-like elements for the green color */
        .indoor-booking-footer h4,
        .indoor-booking-footer h6 {
            color: #212529 !important;
            /* Make titles dark/black for contrast */
            font-weight: 700 !important;
            /* Make titles bolder */
        }

        /* 3. Primary Text (Logo/Titles) - Now dark for better contrast */
        .indoor-booking-text-primary {
            color: #212529 !important;
        }

        /* 4. Links - Use primary green color */
        .indoor-booking-link {
            color: var(--indoor-b-primary);
            /* Green for links */
            text-decoration: none;
            line-height: 2.2;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .indoor-booking-link:hover {
            color: var(--indoor-b-primary-dark);
            /* Darker green on hover */
            text-decoration: underline;
        }

        /* 🌿 Register Now Button - Retained original look, ensure full pill shape on all devices */
        .indoor-booking-btn {
            background-color: var(--indoor-b-primary);
            color: #ffffff;
            border: none;
            /* Reduced padding for slightly cleaner look */
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: none;
            text-decoration: none;
            /* Ensure no underline on anchor */
        }

        .indoor-booking-btn:hover {
            background-color: var(--indoor-b-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
            color: #ffffff;
        }

        /* 6. Divider */
        .indoor-booking-hr {
            border-top: 1px solid #dcdcdc;
            /* Slightly lighter divider */
            opacity: 1;
            margin-top: 0;
        }

        /* 7. Secondary Text - Changed to a softer grey for a professional distinction */
        .indoor-booking-secondary-text {
            color: var(--indoor-b-text-secondary) !important;
            /* Soft dark grey */
            font-size: 0.85rem;
        }

        /* 8. Social Media Icons - Filled circle for a bolder look */
        .indoor-booking-social-icon {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 36px;
            /* Slightly smaller */
            height: 36px;
            border-radius: 50%;
            background-color: var(--indoor-b-primary);
            /* Filled with green */
            color: #ffffff;
            /* White icon */
            border: 1px solid var(--indoor-b-primary);
            /* Green border */
            font-size: 1.1rem;
            /* Slightly smaller icon */
            transition: all 0.3s ease;
            text-decoration: none;
            /* Remove underline */
        }

        .indoor-booking-social-icon:hover {
            background-color: var(--indoor-b-primary-dark);
            /* Darken fill on hover */
            border-color: var(--indoor-b-primary-dark);
            transform: translateY(-2px);
            /* Slight lift */
        }

        /* 9. Language Selector - Keep border but remove default background for a cleaner look */
        .indoor-booking-select {
            width: 150px;
            border: 1px solid var(--indoor-b-text-secondary);
            /* Grey border */
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            color: #212529;
            /* Dark text */
            background-color: #ffffff;
            /* White background */
            transition: all 0.3s ease;
            /* Ensure no green border/box-shadow on focus for cleaner look */
            box-shadow: none !important;
        }

        .indoor-booking-select:focus {
            border-color: var(--indoor-b-primary);
            /* Primary color on focus */
        }

        /*---------------------------------
  General Section Padding
---------------------------------*/
        .section-padding {
            padding: 80px 0;
        }

        /*---------------------------------
  Hero Section
---------------------------------*/
        .hero-section {
            position: relative;
            background-color: #f8f9fa;
            padding: 100px 0;
            overflow: hidden;
        }

        .hero-content h1 {
            font-weight: 700;
            color: #212529;
        }

        .hero-content .lead {
            color: #495057;
        }

        .btn-gradient {
            background-image: linear-gradient(90deg, rgb(25 135 84), rgb(25 135 84));
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-gradient:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 11, 201, 0.3);
        }

        /*---------------------------------
  Hero Floating Cards
---------------------------------*/
        .hero-image-wrapper {
            position: relative;
        }

        .floating-card.card-sport {
            background: white;
            padding: 10px 15px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .floating-card.card-sport img {
            display: block;
            margin: 0 auto 5px auto;
        }

        /*---------------------------------
  Decorative Circles
---------------------------------*/
        .deco-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            position: absolute;
            opacity: 0.3;
            z-index: 0;
        }

        .circle-green {
            background: #198754;
            top: 10%;
            left: -50px;
        }

        .circle-blue {
            background: #198754;
            bottom: 10%;
            right: -50px;
        }

        /*---------------------------------
  Stats Section
---------------------------------*/
        .display-4.text-primary {
            color: #198754 !important;
        }

        /*---------------------------------
  Team Section
---------------------------------*/
        .team-card img {
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .team-card img:hover {
            transform: translateY(-5px);
        }

        /*---------------------------------
  Testimonials Carousel
---------------------------------*/
        .carousel .blockquote {
            font-size: 1.1rem;
            font-style: italic;
            color: #495057;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: black;
            border-radius: 50%;
            padding: 10px;
        }

        /*---------------------------------
/* CTA Section */
        .cta-section {
            background-color: white;
            /* Background is now white */
            color: ffff;
            /* Text color matching button */
            padding: 80px 0;
        }

        .cta-section .btn-gradient {
            background-color: rgb(25, 135, 84);
            /* Green background */
            color: white;
            /* White text */
            font-weight: 600;
            border: none;
            transition: background 0.3s ease;
        }

        .cta-section .btn-gradient:hover {
            background-color: #146c43;
            /* Darker green on hover */
            color: white;
        }

        /*---------------------------------
  Blur Dots
---------------------------------*/
        .blur-dot {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.4;
            z-index: 0;
        }

        .dot-1 {
            width: 200px;
            height: 200px;
            background-color: #FFFFFF;
            /* Pink */
            bottom: 10%;
            left: 5%;
        }

        .dot-2 {
            width: 150px;
            height: 150px;
            background-color: #add8e6;
            /* Light Blue */
            top: 15%;
            right: 10%;
        }



        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-section {
            background-image: url('http://127.0.0.1:8000/images/bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 120px 0;
            color: white;
        }

        .section-padding {
            padding: 80px 0;
        }

        .team-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-item h2 {
            font-weight: 700;
        }

        .testimonial-carousel {
            background-color: #f8f9fa;
            padding: 50px 0;
        }

        .testimonial-carousel blockquote {
            font-size: 1.25rem;
        }

        .cta-section {
            background: #FFFFFF;
            color: black;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-section {
            padding: 6rem 0;
            background: linear-gradient(105deg, hsl(200, 100%, 97%), hsl(0, 0%, 100%) 50%);
            overflow: hidden;
        }

        .hero-title {
            font-size: 3.8rem;
            font-weight: 700;
            line-height: 1.2;
            color: #212529;
        }

        .hero-text {
            font-size: 1.15rem;
            color: #555;
            max-width: 500px;
        }

        .hero-text a {
            color: #0d6efd;
            font-weight: 500;
            text-decoration: none;
        }

        .hero-image-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 500px;
            /* Provides space for absolutely positioned items */
        }

        .main-hero-image {
            max-width: 600px;
            max-height: 500px;
            border-radius: 1rem;
            position: relative;
            z-index: 2;
        }

        .floating-card {
            position: absolute;
            background-color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 1rem;
            z-index: 3;
            animation: float 4s ease-in-out infinite;
        }

        /* Positions */
        .card-workforce {
            top: 15%;
            left: 5%;
        }

        .card-paid {
            top: 45%;
            left: -10%;
        }

        .floating-logo-wrapper {
            position: absolute;
            top: 5%;
            right: 10%;
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 3;
            animation: float 5s ease-in-out infinite;
        }

        /* Floating Cards Content Styling */
        .card-paid .icon-wrapper {
            background-color: #e7f1ff;
            color: #0d6efd;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
        }

        .card-paid .paid-text {
            font-weight: 500;
            color: #212529;
            margin: 0;
        }

        .card-paid .paid-status {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }

        .card-paid .check-icon {
            color: #0d6efd;
            background-color: #e7f1ff;
            border-radius: 50%;
            padding: 4px;
            font-size: 1.5rem;
            margin-left: auto;
        }

        /* Decorative Circles */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            z-index: 1;
        }

        .circle-green {
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, #20c997, #198754);
            bottom: 5%;
            left: 15%;
        }

        .circle-blue {
            width: 30px;
            height: 30px;
            background: linear-gradient(45deg, #0dcaf0, #0d6efd);
            bottom: 10%;
            right: 10%;
        }

        /* Keyframes for floating animation */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .hero-section {
                padding: 4rem 0;
                text-align: center;
            }

            .hero-text {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-image-wrapper {
                margin-top: 3rem;
                min-height: 400px;
                /* Adjust height for mobile */
            }

            .main-hero-image {
                max-width: 100%;
            }

            .card-workforce {
                top: -30px;
                /* Reposition for stacking */
                left: 50%;
                transform: translateX(-50%);
                animation: none;
            }

            .card-paid {
                bottom: -30px;
                /* Reposition for stacking */
                top: auto;
                left: 50%;
                transform: translateX(-50%);
                animation: none;
            }

            .floating-logo-wrapper {
                top: 0;
                right: 0;
            }
        }


        .card-img-top {
            height: 220px;
            /* You can adjust this height */
            object-fit: cover;
            /* Ensures the image fills the box without stretching */
        }

        /* Consistent image sizing */
        .card-img-top {
            height: 220px;
            object-fit: cover;
            border-radius: 0.5rem 0.5rem 0 0;
            /* Rounded top corners */
            border-bottom: 1px solid #dee2e6;
            /* Subtle border under image */
            transition: transform 0.4s ease;
        }

        /* Card base styling */
        .card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            /* Makes sure image and content stay inside rounded edges */
        }

        /* Full card hover effect */
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        /* Image zoom on card hover */
        .card:hover .card-img-top {
            transform: scale(1.06);
        }

        .card-img-top {
            height: 220px;
            object-fit: cover;
            border-radius: 0.5rem 0.5rem 0 0;
            border-bottom: 1px solid #dee2e6;
            transition: transform 0.4s ease;
        }

        .card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .card:hover .card-img-top {
            transform: scale(1.06);
        }

        .btn-green {
            background-color: rgb(25, 135, 84);
            color: white;
        }

        .btn-green:hover {
            background-color: #1e9d6d;
            color: white;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }


        /* banner section */

        .sports-banner {
            background: linear-gradient(90deg, rgb(25, 135, 84), rgb(40, 167, 69));
            padding: 2rem;
            /* keeps top, right, bottom padding */
            padding-left: 2rem;
            /* increases left padding */
            border-radius: 3rem;
            margin-left: 5px;
            margin-right: 5px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .banner-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .banner-image-container {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .banner-image-container img {
            width: 400px;
            height: 400px;
            object-fit: cover;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .promo-content {
            flex: 1;
            background-color: #ffffff;
            color: #212529;
            padding: 2rem;
            border-radius: 1rem;
            text-align: left;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .pass-logo {
            display: inline-flex;
            align-items: center;
            border: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 1.5rem;
            background-color: #fff;
        }

        .pass-logo .brand {
            background-color: rgb(25, 135, 84);
            color: #fff;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            margin-right: 0.5rem;
        }

        .promo-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
            color: #343a40;
        }

        .promo-content .highlight {
            color: #fd7e14;
        }

        .promo-content p {
            font-size: 1.1rem;
            margin-top: 0.5rem;
            color: #6c757d;
        }

        /* Carousel Controls */
        #sportsBannerCarousel .carousel-control-prev,
        #sportsBannerCarousel .carousel-control-next {
            top: 50%;
            transform: translateY(-50%);
            width: 5%;
            opacity: 0.9;
            z-index: 10;
        }

        #sportsBannerCarousel .carousel-control-prev-icon,
        #sportsBannerCarousel .carousel-control-next-icon {
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            padding: 15px;
            width: 45px;
            height: 45px;
            background-size: 60%;
            transition: all 0.3s ease;
        }

        #sportsBannerCarousel .carousel-control-prev-icon:hover,
        #sportsBannerCarousel .carousel-control-next-icon:hover {
            transform: scale(1.1);
        }

        /* Responsive Adjustments */
        @media (max-width: 767px) {
            .sports-banner {
                padding: 1rem;
            }

            .banner-content {
                flex-direction: column;
                text-align: center;
            }

            .promo-content {
                margin-bottom: 1.5rem;
            }

            .banner-image-container img {
                width: 100%;
                height: auto;
            }

            .pass-logo {
                margin-left: auto;
                margin-right: auto;
            }
        }

        /* About us */

        /* Center About Us content and button */
        .about-text-box {
            background-color: #f8f9fa;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            max-width: 600px;
        }

        .about-text-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .about-text-box h2 {
            font-weight: 700;
            color: #212529;
        }

        .about-text-box p {
            font-size: 1.1rem;
            color: #495057;
            line-height: 1.7;
        }

        /* Centered button with matching brand color */
        .btn-green {
            background-color: rgb(25, 135, 84);
            color: white;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 24px;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-green:hover {
            background-color: #146c43;
            color: white;
            transform: translateY(-2px);
        }


        /* social impact */


        /* General page background */
        body {
            background-color: #f8f9fa;
        }

        /* Section layout */
        .impact-section {
            padding: 4rem 0;
        }

        /* Info Box Styling */
        .info-box {
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-radius: 1rem;
            max-width: 600px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .info-box h2 {
            font-weight: 700;
            color: #212529;
            text-align: center;
        }

        .info-box p {
            font-size: 1.1rem;
            color: #495057;
            line-height: 1.7;
        }

        /* Centered Green Button */
        .btn-green {
            background-color: rgb(25, 135, 84);
            color: white;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 24px;
            border: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-green:hover {
            background-color: #146c43;
            color: white;
            transform: translateY(-2px);
        }

        /* latest news */
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }

        .news-section {
            padding: 4rem 0;
        }

        /* Styling for a single news card */
        .news-card {
            position: relative;
            border-radius: 1rem;
            /* Rounded corners for the card */
            overflow: hidden;
            /* Ensures the image respects the border-radius */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 350px;
            /* Giving a fixed height for a uniform look */
        }

        .news-card:hover {
            transform: translateY(-10px);
            /* Lifts the card on hover */
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .news-card .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Ensures the image covers the card area without distortion */
        }

        /* The dark overlay to make text more readable */
        .news-card .card-img-overlay {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0) 50%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            /* Aligns content to the bottom */
            padding: 1.5rem;
        }

        .news-card .date-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background-color: rgba(255, 255, 255, 0.9);
            color: #333;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            /* Pill shape */
            font-size: 0.8rem;
            font-weight: 600;
        }

        .news-card .card-title {
            color: #ffffff;
            font-weight: bold;
            font-size: 1.25rem;
            margin: 0;
        }

        .news-header a {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.2s;
        }

        .news-header a:hover {
            color: #000;
        }


        .features-section {
            padding: 4rem 0;
        }

        .feature-item {
            position: relative;
            padding-left: 20px;
            /* Make space for the decorative line */
            margin-bottom: 2rem;
        }

        /* The decorative vertical line */
        .feature-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            /* Adjust vertical alignment of the line */
            width: 3px;
            height: 28px;
            border-radius: 2px;
        }

        /* Different gradient colors for each line */
        .feature-item.style-1::before {
            background: linear-gradient(180deg, #f87171, #f0abfc);
            /* Red to Purple */
        }

        .feature-item.style-2::before {
            background: linear-gradient(180deg, #4ade80, #38bdf8);
            /* Green to Blue */
        }

        .feature-item.style-3::before {
            background: linear-gradient(180deg, #facc15, #fb923c);
            /* Yellow to Orange */
        }

        .feature-item .icon {
            font-size: 1.5rem;
            /* Icon size */
            margin-bottom: 1rem;
            display: inline-block;
        }

        .feature-item h5 {
            font-weight: bold;
        }

        .feature-item p {
            color: #6c757d;
        }


        /* contact */
        /* Custom submit button style */
        .btn-submit {
            background-color: rgb(25, 135, 84);
            border: none;
            color: white;
            font-weight: 500;
            padding: 10px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background-color: rgb(21, 115, 72);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
        }


        /* About us page */

        /* Shared section padding */
        .section-padding {
            padding: 80px 0;
        }

        /* Feature Boxes */
        .feature-box {
            transition: all 0.3s ease;
        }

        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        /* Stats Section */
        .stats-section {
            background: #fff;
        }




        /* Testimonials */
        .testimonial-carousel {
            background-color: #f8f9fa;
        }

        .blockquote {
            font-size: 1.25rem;
            color: #212529;
            font-style: italic;
        }

        /* CTA Section */
        .cta-section {
            background: #ffffff;
            /* White background */
            color: #000000;
            /* Black text */
            padding: 100px 0;
        }

        /* indoor */
        .card {
            border: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-success {
            background-color: #198754;
            border: none;
        }

        .btn-success:hover {
            background-color: #157347;
        }

        /* Hover effect for social icons */
        .social-icon {
            color: #6c757d;
            /* default gray */
            transition: color 0.3s;
        }

        .social-icon:hover {
            color: #0d6efd;
            /* blue on hover */
        }
    </style>
</head>

<body>

    <!-- Navbar / Header -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 40px; width: auto; margin-right: 10px;">IndoorB</a>
            <!-- Desktop Navbar (visible only on lg and up) -->
            <div class="collapse navbar-collapse justify-content-end d-none d-lg-flex" id="nav">
                <ul class="navbar-nav align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('indoor') ? 'active' : '' }}" href="{{ route('indoor') }}">Indoor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('Sign in') ? 'active' : '' }}" href="{{ route('login') }}">Sign in</a>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-primary ms-lg-3 mt-3 mt-lg-0">Register</a>
            </div>
            <!-- Mobile Navbar Toggler (visible only on mobile) -->
            <button class="navbar-toggler d-lg-none" type="button" id="mobileMenuBtn" aria-label="Open menu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

        <!-- Mobile Sidebar Menu (strictly hidden on desktop) -->
        <div class="mobile-sidebar d-lg-none" id="mobileSidebar">
            <button class="close-btn" id="closeSidebarBtn" aria-label="Close menu"><i class="bi bi-x-lg"></i></button>
            <a class="navbar-brand" href="#"><img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 40px; width: auto; margin-right: 10px;">IndoorB</a>
            <ul class="navbar-nav align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('indoor') ? 'active' : '' }}" href="{{ route('indoor') }}">Indoor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('Sign in') ? 'active' : '' }}" href="{{ route('login') }}">Sign in</a>
                </li>
            </ul>
            <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
        </div>
        <div class="mobile-sidebar-backdrop d-lg-none" id="mobileSidebarBackdrop"></div>
    <!-- Main Content -->

    <main style="margin-top: 100px;">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="indoor-booking-footer mt-5 pt-4 pb-4">
        <div class="container">
            <div class="row mb-5">

                <div class="col-12 col-lg-3 mb-4 mb-lg-0 d-flex flex-column align-items-center align-items-lg-start">
                    <h4 class="mb-3 indoor-booking-text-primary d-flex align-items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 60px; width: auto; margin-right: 10px;">
                        <span class="indoorb-logo-text fw-bold">IndoorB</span>
                    </h4>
                    <a href="{{ route('register') }}" class="btn btn-lg indoor-booking-btn">
                        Register Now
                    </a>
                </div>

                <div class="col-6 col-md-3 col-lg-2 mb-3 mb-lg-0">
                    <h6 class="text-uppercase indoor-booking-text-primary mb-3">What we do</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('features') }}" class="indoor-booking-link">Features</a></li>
                        <li><a href="{{ route('blog') }}" class="indoor-booking-link">Blog</a></li>
                        <li><a href="{{ route('security') }}" class="indoor-booking-link">Security</a></li>
                        <li><a href="{{ route('for-business') }}" class="indoor-booking-link">For Business</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2 mb-3 mb-lg-0">
                    <h6 class="text-uppercase indoor-booking-text-primary mb-3">Who we are</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('about') }}" class="indoor-booking-link">About us</a></li>
                        <li><a href="#" class="indoor-booking-link">Careers</a></li>
                        <li><a href="#" class="indoor-booking-link">Brand Center</a></li>
                        <li><a href="{{ route('privacy') }}" class="indoor-booking-link">Privacy</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2 mb-3 mb-lg-0">
                    <h6 class="text-uppercase indoor-booking-text-primary mb-3">Use IndoorB</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="indoor-booking-link">Web App</a></li>
                        <li><a href="#" class="indoor-booking-link">iPhone</a></li>
                        <li><a href="#" class="indoor-booking-link">Android</a></li>
                        <li><a href="{{ route('login') }}" class="indoor-booking-link">Admin PC</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-3">
                    <h6 class="text-uppercase indoor-booking-text-primary mb-3">Need help?</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('contact') }}" class="indoor-booking-link">Contact Us</a></li>
                        <li><a href="#" class="indoor-booking-link">Help Center</a></li>
                        <li><a href="#" class="indoor-booking-link">Apps</a></li>
                        <li><a href="#" class="indoor-booking-link">Security Advisories</a></li>
                    </ul>
                </div>
            </div>

            <hr class="indoor-booking-hr">

            <div class="row pt-3 align-items-center">

                <div class="col-12 col-md-6 col-lg-4 mb-3 mb-lg-0 indoor-booking-secondary-text d-flex flex-wrap align-items-center">
                    <span class="me-3">&copy; 2024 IndoorB LLC</span>
                    <a href="{{ route('terms-of-service') }}" class="indoor-booking-link me-3">Terms of service</a>
                </div>

                <div class="col-12 col-md-6 col-lg-4 mb-3 mb-lg-0 d-flex justify-content-center">
                    <a href="https://twitter.com/webxkey" class="indoor-booking-social-icon mx-2"><i class="bi bi-twitter"></i></a>
                    <a href="https://linkedin.com/company/webxkey" class="indoor-booking-social-icon mx-2"><i class="bi bi-linkedin"></i></a>
                    <a href="https://www.instagram.com/webxkey/#" class="indoor-booking-social-icon mx-2"><i class="bi bi-instagram"></i></a>
                    <a href="https://facebook.com/webxkey" class="indoor-booking-social-icon mx-2"><i class="bi bi-facebook"></i></a>
                </div>

                <div class="col-12 col-lg-4 d-flex justify-content-lg-end justify-content-center">
                    <select class="form-select indoor-booking-select" aria-label="Language selector">
                        <option selected>English</option>
                        <option value="1">Spanish</option>
                        <option value="2">French</option>
                    </select>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Mobile Sidebar Menu JS
            document.addEventListener('DOMContentLoaded', function() {
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                const mobileSidebar = document.getElementById('mobileSidebar');
                const closeSidebarBtn = document.getElementById('closeSidebarBtn');
                const mobileSidebarBackdrop = document.getElementById('mobileSidebarBackdrop');

                function openSidebar() {
                    mobileSidebar.classList.add('open');
                    mobileSidebarBackdrop.classList.add('open');
                    document.body.style.overflow = 'hidden';
                }
                function closeSidebar() {
                    mobileSidebar.classList.remove('open');
                    mobileSidebarBackdrop.classList.remove('open');
                    document.body.style.overflow = '';
                }
                mobileMenuBtn.addEventListener('click', openSidebar);
                closeSidebarBtn.addEventListener('click', closeSidebar);
                mobileSidebarBackdrop.addEventListener('click', closeSidebar);

                // Close sidebar on ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') closeSidebar();
                });
            });

            // Existing scroll and nav logic
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                navbar.classList.toggle('scrolled', window.scrollY > 50);
                updateActiveNavLink();
                checkScroll();
            });
            function updateActiveNavLink() {
                const sections = document.querySelectorAll('section[id]');
                const navLinks = document.querySelectorAll('.nav-link');
                let currentSection = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - 120;
                    const sectionHeight = section.offsetHeight;
                    if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                        currentSection = section.getAttribute('id');
                    }
                });
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${currentSection}`) {
                        link.classList.add('active');
                    }
                });
            }
            function animateCounter(id, end, duration) {
                const el = document.getElementById(id);
                if (!el) return;
                let start = 0;
                const increment = end / (duration / 16);
                const timer = setInterval(() => {
                    start += increment;
                    if (start >= end) {
                        el.textContent = end.toLocaleString();
                        clearInterval(timer);
                    } else {
                        el.textContent = Math.floor(start).toLocaleString();
                    }
                }, 16);
            }
            function checkScroll() {
                const elements = document.querySelectorAll('.fade-in');
                elements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 150;
                    if (elementTop < window.innerHeight - elementVisible) {
                        element.classList.add('visible');
                    }
                });
            }
            window.addEventListener('load', () => {
                updateActiveNavLink();
                checkScroll();
            });
        </script>
</body>


</html>
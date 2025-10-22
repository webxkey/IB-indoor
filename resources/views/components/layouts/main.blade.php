<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indoor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"

        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


    <style>
        /* Body full height flex layout */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        /* Custom Navbar Styling */
        .custom-navbar {
            background-color: rgb(25, 135, 84);
            /* Green background */
            border-bottom: none;
        }

        .navbar-brand strong {
            font-size: 1.5rem;
            color: #fff;
            /* White text */
        }

        .nav-link {
            font-weight: 500;
            color: white !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #e2e2e2 !important;
        }

        /* Register Button */
        .btn-register {
            background-color: #fff;
            border: none;
            color: rgb(25, 135, 84);
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Footer Styling - Green Theme */
        /* Compact Footer Styling - Green Theme */
        .footer-green {
            background-color: rgb(25, 135, 84);
            padding-top: 1.5rem;
            /* Reduced from 4rem */
            padding-bottom: 1.5rem;
            /* Reduced from 4rem */
            color: #ffffff;
            font-size: 0.9rem;
            /* Slightly smaller overall font */
        }

        .footer-heading {
            color: #e6e6e6;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            font-size: 0.8rem;
            /* Smaller heading */
            margin-bottom: 0.5rem;
        }

        .footer-links li {
            margin-bottom: 0.4rem;
            /* Tighter spacing */
        }

        .footer-links a {
            color: #ffffff;
            text-decoration: none;
            transition: opacity 0.3s ease, text-decoration 0.3s ease;
            font-size: 0.85rem;
        }

        .footer-links a:hover {
            text-decoration: underline;
            opacity: 0.85;
        }

        .social-icons a {
            color: #ffffff;
            font-size: 1rem;
            /* Smaller icons */
            margin-right: 0.9rem;
            transition: opacity 0.3s ease;
        }

        .social-icons a:hover {
            opacity: 0.8;
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
            border-radius: 1.5rem;
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
    </style>
</head>

<body>

    <!-- Navbar / Header -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3 custom-navbar">
        <div class="container">
            <a class="navbar-brand" href="#"><strong>IndoorB</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'fw-bold text-white' : 'text-white-50' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('indoor') ? 'fw-bold text-white' : 'text-white-50' }}" href="{{ route('indoor') }}">Indoor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'fw-bold text-white' : 'text-white-50' }}" href="{{ route('about') }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'fw-bold text-white' : 'text-white-50' }}" href="{{ route('contact') }}">Contact</a>
                    </li>

                </ul>

                <div class="d-flex align-items-center ms-3">
                    <a class="nav-link text-white-50 me-3" href="{{ route('login') }}">Sign In</a>
                    <a class="btn btn-register" href="{{ route('register') }}" role="button">Register &rarr;</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->

    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="footer-green text-white mt-auto">
        <div class="container">
            <div class="row">

                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <h6 class="footer-heading mb-2">Solutions</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Business</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4 col-6 mb-3">
                    <h6 class="footer-heading mb-2">Partners</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Partner affiliate program</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4 col-6 mb-3">
                    <h6 class="footer-heading mb-2">About</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Press Center</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 col-6 mb-3">
                    <h6 class="footer-heading mb-2">Help</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Security Center</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-3">
                    <h6 class="footer-heading mb-2">Follow us</h6>
                    <div class="d-flex social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>
<div>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->

    <style>
        /* Light Theme Custom Variables */
        :root {
            --main-bg: #ffffff;
            --main-text: #000000;
            --accent-color: rgb(25, 135, 84);
            --card-bg: #f9fafb;
            --secondary-bg: #f3f4f6;
            --muted-text: #6b7280;
        }

        body {
            background-color: var(--main-bg);
            color: var(--main-text);
            font-family: 'Inter', sans-serif;
        }

        /* Primary Buttons */
        .btn-primary-green {
            background-color: var(--accent-color);
            color: #ffffff;
            border: none;
            font-weight: 600;
            padding: 10px 32px;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
            transition: all 0.3s ease;
        }

        .btn-primary-green:hover {
            background-color: rgb(20, 108, 67);
            color: #ffffff;
            transform: scale(1.02);
            box-shadow: 0 6px 15px rgba(25, 135, 84, 0.4);
        }

        /* Bordered Secondary Button */
        .btn-border-green {
            border: 2px solid var(--accent-color) !important;
            color: var(--accent-color) !important;
            background-color: transparent;
            font-weight: 500;
            padding: 10px 32px;
            border-radius: 0.75rem;
            transition: background-color 0.3s ease;
        }

        .btn-border-green:hover {
            background-color: rgba(25, 135, 84, 0.05);
            color: var(--accent-color) !important;
        }

        /* Feature Card */
        .card-feature {
            background-color: var(--card-bg);
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        }

        /* Icon Wrapper */
        .icon-wrapper {
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--accent-color);
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 1rem;
        }

        /* Typography */
        h1 {
            font-size: 3rem;
            font-weight: 800;
        }

        h2 {
            font-size: 2rem;
            font-weight: 700;
        }

        h3 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        p {
            color: var(--muted-text);
        }

        .text-accent-green {
            color: var(--accent-color) !important;
        }

        .bg-secondary-light {
            background-color: var(--secondary-bg) !important;
        }
    </style>
    <main>
        <!-- Hero Section -->
        <section class="py-5 bg-secondary-light text-center">
            <div class="container">
                <h1 class="mb-3">Book Your Game. <span class="text-accent-green">Instantly.</span></h1>
                <p class="mx-auto mb-4" style="max-width:700px;">Say goodbye to phone calls and availability spreadsheets. Our platform brings all your indoor sports booking needs into one seamless, powerful app.</p>
                <a href="#features" class="btn btn-primary-green">Explore All Features</a>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-5">
            <div class="container text-center">
                <h2 class="text-accent-green fw-semibold text-uppercase mb-2">Core Capabilities</h2>
                <p class="h2 fw-bolder text-dark mb-5">Everything You Need for Effortless Indoor Sports</p>

                <div class="row g-4">
                    <!-- Feature Card 1 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card-feature h-100 text-center">
                            <div class="icon-wrapper mx-auto">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="mb-3">Real-Time Availability</h3>
                            <p>See exactly which courts and slots are open, right now. Our live calendar updates instantly so you never double-book or miss a spot.</p>
                        </div>
                    </div>

                    <!-- Feature Card 2 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card-feature h-100 text-center">
                            <div class="icon-wrapper mx-auto">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18v-3m0-6V7"></path>
                                </svg>
                            </div>
                            <h3 class="mb-3">Multi-Sport Venue Support</h3>
                            <p>From basketball and tennis to futsal and volleyball, our flexible system handles bookings for any indoor sport configuration.</p>
                        </div>
                    </div>

                    <!-- Feature Card 3 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card-feature h-100 text-center">
                            <div class="icon-wrapper mx-auto">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <h3 class="mb-3">Instant Secure Payments</h3>
                            <p>Integrate digital payments effortlessly. Accept deposits or full payment right at the time of booking with bank-grade security.</p>
                        </div>
                    </div>

                    <!-- Add more feature cards similarly -->
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section id="demo" class="py-5 bg-secondary-light text-center border-top">
            <div class="container" style="max-width:900px;">
                <h2 class="mb-4">Ready to Transform Your Bookings?</h2>
                <p class="mb-4">See how our intuitive platform can save time and boost revenue for your sports facility.</p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="#" class="btn btn-primary-green">Request a Free Demo</a>
                    <a href="#" class="btn btn-border-green">Contact Sales</a>
                </div>
            </div>
        </section>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>




</div>
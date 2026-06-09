<div>

    <style>
        /* ====== Theme ====== */
        :root {
            --indoor-green: #198754;
            --indoor-light: #f8f9fa;
            --indoor-dark: #212529;
        }

        body {
            background-color: var(--indoor-light);
            color: var(--indoor-dark);
            font-family: 'Poppins', sans-serif;
        }

        /* ====== Hero Section ====== */
        .business-hero {
            background: linear-gradient(rgba(25, 135, 84, 0.75), rgba(25, 135, 84, 0.75)),
                url('https://images.unsplash.com/photo-1599058917212-d750089bc07e?auto=format&fit=crop&w=1600&q=80') center/cover;
            color: #fff;
            padding: 100px 0;
            text-align: center;
        }

        .business-hero h1 {
            font-weight: 700;
            font-size: 2.8rem;
        }

        .business-hero p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* ====== Content Sections ====== */
        .section {
            padding: 70px 0;
        }

        .section-title {
            font-weight: 700;
            color: var(--indoor-dark);
            text-align: center;
            margin-bottom: 2rem;
        }

        .section-subtitle {
            text-align: center;
            color: #555;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ====== Cards ====== */
        .business-card {
            background: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
            height: 100%;
            transition: all 0.3s ease;
            text-align: center;
        }

        .business-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .business-card h4 {
            color: var(--indoor-green);
            font-weight: 600;
            margin-top: 1rem;
        }

        .business-card p {
            color: #555;
            font-size: 0.95rem;
        }

        /* ====== CTA Section ====== */
        .cta-section {
            background-color: var(--indoor-green);
            color: #fff;
            text-align: center;
            padding: 70px 20px;
            border-radius: 0;
        }

        .cta-section h2 {
            font-weight: 700;
        }

        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 25px;
        }

        .cta-section .btn-light {
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 30px;
        }

        /* ====== Footer ====== */
    </style>
    <!-- Hero Section -->
    <section class="business-hero">
        <div class="container">
            <h1>Sportynix Hub for Business</h1>
            <p>Partner with us to power your indoor sports venue, corporate events, or brand promotions.</p>
        </div>
    </section>

    <!-- Why Partner With Us -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Why Partner with Sportynix Hub?</h2>
            <p class="section-subtitle">We help indoor sports businesses grow by providing seamless technology, exposure, and support to reach more customers.</p>

            <div class="row row-cols-1 row-cols-md-3 g-4">

                <div class="col">
                    <div class="business-card">
                        <img src="https://cdn-icons-png.flaticon.com/512/5973/5973659.png" width="70" alt="Growth Icon">
                        <h4>Increased Bookings</h4>
                        <p>Boost your venue’s visibility and fill up time slots faster with our online booking system and active user base.</p>
                    </div>
                </div>

                <div class="col">
                    <div class="business-card">
                        <img src="https://cdn-icons-png.flaticon.com/512/1187/1187541.png" width="70" alt="Analytics Icon">
                        <h4>Smart Analytics</h4>
                        <p>Access real-time booking and revenue insights to make smarter business decisions and manage operations efficiently.</p>
                    </div>
                </div>

                <div class="col">
                    <div class="business-card">
                        <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" width="70" alt="Support Icon">
                        <h4>Dedicated Support</h4>
                        <p>Our team works closely with you to set up, manage, and optimize your venue listing for maximum performance.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Corporate Solutions -->
    <section class="section bg-white">
        <div class="container">
            <h2 class="section-title">Corporate & Group Solutions</h2>
            <p class="section-subtitle">Looking to organize a team-building event or corporate sports league? We’ve got you covered.</p>

            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1593095948071-4747b6f8dcd3?auto=format&fit=crop&w=900&q=80" class="img-fluid rounded-3 shadow-sm" alt="Corporate Sports">
                </div>
                <div class="col-md-6 mt-4 mt-md-0">
                    <ul class="list-unstyled">
                        <li class="mb-3">✅ Customized event packages for companies</li>
                        <li class="mb-3">✅ Exclusive access to top-rated indoor venues</li>
                        <li class="mb-3">✅ Team-building and tournament management</li>
                        <li class="mb-3">✅ Flexible schedules and discounted group rates</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Partner With Sportynix Hub Today</h2>
            <p>Join hundreds of venues and organizations already using our platform to streamline operations and attract new customers.</p>
            <a href="#" class="btn btn-light">Get Started →</a>
        </div>
    </section>
</div>
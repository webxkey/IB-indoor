<div>
    <style>
        /* ===== Theme ===== */
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

        /* ===== Hero Section ===== */
        .careers-hero {
            background: linear-gradient(rgba(25, 135, 84, 0.75), rgba(25, 135, 84, 0.75)),
                url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1600&q=80') center/cover;
            color: #fff;
            text-align: center;
            padding: 100px 0;
        }

        .careers-hero h1 {
            font-weight: 700;
            font-size: 2.8rem;
        }

        .careers-hero p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* ===== Section Titles ===== */
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

        /* ===== Job Cards ===== */
        .job-card {
            background: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 25px;
            transition: all 0.3s ease;
            height: 100%;
        }

        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
        }

        .job-card h5 {
            color: var(--indoor-green);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .job-card p {
            color: #555;
            font-size: 0.95rem;
        }

        .apply-btn {
            background-color: var(--indoor-green);
            color: #fff;
            border-radius: 50px;
            padding: 8px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .apply-btn:hover {
            background-color: #145c3d;
            color: #fff;
            text-decoration: none;
        }

        /* ===== Culture Section ===== */
        .culture-section {
            padding: 60px 0;
        }

        .culture-section img {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* ===== Footer ===== */
    </style>
    <!-- Hero Section -->
    <section class="careers-hero">
        <div class="container">
            <h1>Join Our Team</h1>
            <p>Be part of Indoor Booking and help us revolutionize the indoor sports experience.</p>
        </div>
    </section>

    <!-- Careers Section -->
    <section class="container my-5">
        <h2 class="section-title">Current Openings</h2>
        <p class="section-subtitle">We are always looking for talented individuals to join our growing team. Explore the positions below and apply today!</p>

        <div class="row row-cols-1 row-cols-md-3 g-4">

            <!-- Job 1 -->
            <div class="col">
                <div class="job-card">
                    <h5>Frontend Developer</h5>
                    <p>Location: Colombo, Sri Lanka</p>
                    <p>We’re looking for a creative Frontend Developer skilled in HTML, CSS, JavaScript, and Bootstrap to build user-friendly interfaces.</p>
                    <a href="#" class="apply-btn">Apply Now</a>
                </div>
            </div>

            <!-- Job 2 -->
            <div class="col">
                <div class="job-card">
                    <h5>Marketing Specialist</h5>
                    <p>Location: Remote / Colombo</p>
                    <p>Join our marketing team to drive campaigns, social media strategy, and community engagement to grow our platform.</p>
                    <a href="#" class="apply-btn">Apply Now</a>
                </div>
            </div>

            <!-- Job 3 -->
            <div class="col">
                <div class="job-card">
                    <h5>Operations Manager</h5>
                    <p>Location: Colombo, Sri Lanka</p>
                    <p>Responsible for overseeing venue partnerships, user support, and smooth day-to-day platform operations.</p>
                    <a href="#" class="apply-btn">Apply Now</a>
                </div>
            </div>

        </div>
    </section>

    <!-- Company Culture Section -->
    <section class="culture-section bg-white">
        <div class="container">
            <h2 class="section-title">Our Culture</h2>
            <p class="section-subtitle">Indoor Booking values innovation, teamwork, and a passion for sports. We believe in a collaborative environment where everyone can grow and make an impact.</p>

            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1531058020387-3be344556be6?auto=format&fit=crop&w=900&q=80" alt="Teamwork" class="img-fluid">
                </div>
                <div class="col-md-6 mt-4 mt-md-0">
                    <ul class="list-unstyled">
                        <li>✅ Collaborative and inclusive work environment</li>
                        <li>✅ Opportunities for personal and professional growth</li>
                        <li>✅ Flexible work options</li>
                        <li>✅ Team events and sports activities</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="text-center my-5">
        <a href="#" class="apply-btn">See All Openings & Apply →</a>
    </section>
</div>
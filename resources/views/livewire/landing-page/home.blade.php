<div>
    <!-- Hero Section -->
    <section class="hero-section position-relative">
        <div class="blur-dot dot-1"></div>
        <div class="blur-dot dot-2"></div>

        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-6 col-md-10 text-center text-lg-start hero-content mb-5 mb-lg-0">
                    <h1 class="display-4 mb-4">{{$sectionTitle}}</h1>
                    <p class="lead mb-5">{{$sectionDescription}}</p>
                    <a href="{{ route('indoor') }}" class="btn btn-gradient btn-lg">Explore Venues</a>
                </div>

                <div class="col-lg-6 text-center text-lg-end">
                    <div class="hero-image-wrapper position-relative">
                        <img src="{{$heroImageUrl}}"
                            class="main-hero-image img-fluid rounded"
                            alt="Indoor Sports Venue">
                        <div class="deco-circle circle-green position-absolute"></div>
                        <div class="deco-circle circle-blue position-absolute"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ✅ FIXED: Bootstrap Carousel properly closed -->
    <div id="sportsBannerCarousel" class="carousel slide my-5" data-bs-ride="carousel">
        <div class="carousel-inner">

            <!-- Slide 1 -->
            <div class="carousel-item active">
                <div class="sports-banner">
                    <div class="banner-content">
                        <!-- Left Content -->
                        <div class="promo-content">
                            <div class="pass-logo"><span class="brand">INDOOR</span> PASS</div>
                            <h1>Unlimited <span class="highlight">Court Access</span></h1>
                            <p>For just LKR 1,999 a month</p>
                        </div>

                        <!-- Right Image -->
                        <div class="banner-image-container">
                            <img src="{{$bannerImageUrl}}" alt="Indoor Sports Courts" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-item">
                <div class="sports-banner">
                    <div class="banner-content">
                        <!-- Left Content -->
                        <div class="promo-content">
                            <div class="pass-logo"><span class="brand">INDOOR</span> PASS</div>
                            <h1>Play <span class="highlight">Anytime</span></h1>
                            <p>Access courts 24/7 with one simple pass</p>
                        </div>

                        <!-- Right Image -->
                        <div class="banner-image-container">
                            <img src="{{$bannerImageUrl1}}" alt="Indoor Sport Player" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="carousel-item">
                <div class="sports-banner">
                    <div class="banner-content">
                        <!-- Left Content -->
                        <div class="promo-content">
                            <div class="pass-logo"><span class="brand">INDOOR</span> PASS</div>
                            <h1><span class="highlight">Join Today</span> & Save</h1>
                            <p>Get exclusive offers on court bookings</p>
                        </div>

                        <!-- Right Image -->
                        <div class="banner-image-container">
                            <img src="{{$bannerImageUrl2}}" alt="Sports Offer" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#sportsBannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#sportsBannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- ✅ "About Us" section now correctly outside the carousel -->
    <div class="container about-section my-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="{{$aboutImageUrl}}"
                    alt="Team working at an office"
                    class="img-fluid rounded-4">
            </div>
            <div class="col-lg-6 d-flex justify-content-center align-items-center">
                <div class="p-4 p-md-5 rounded-4 about-text-box text-center">
                    <h2 class="fw-bold mb-3">{{$aboutTitle}}</h2>
                    <p class="text-secondary">
                        {{$aboutDescription}}
                    </p>
                    <a href="{{ route('about') }}" class="btn btn-green mt-3">Learn More &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container impact-section">
        <div class="row align-items-center g-5">

            <!-- Left Column: Centered Text Box -->
            <div class="col-lg-6 d-flex justify-content-center align-items-center">
                <div class="p-4 p-md-5 rounded-4 info-box text-center">
                    <h2 class="fw-bold mb-3">{{$socialTitle}}</h2>
                    <p class="text-secondary">
                        {{$socialDescription}}
                    </p>

                    <a href="{{ route('about') }}" class="btn btn-green mt-3">Learn More &rarr;</a>
                </div>
            </div>

            <!-- Right Column: Image -->
            <div class="col-lg-6">
                <img src="{{$socialImageurl}}"
                    alt="Community member using a service"
                    class="img-fluid rounded-4">
            </div>

        </div>
    </div>


    <div class="container features-section">
        <div class="row text-center text-lg-start">

            <div class="col-lg-4">
                <div class="feature-item style-1">
                    <i class="bi bi-calendar-check icon"></i>
                    <h5>Easy Court Booking</h5>
                    <p>Find and book your favorite courts in seconds. Our real-time schedule makes it simple to see availability and secure your spot.</p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="feature-item style-2">
                    <i class="bi bi-person-badge icon"></i>
                    <h5>Flexible Memberships</h5>
                    <p>Choose from a variety of membership plans that fit your lifestyle and save you money on every booking you make.</p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="feature-item style-3">
                    <i class="bi bi-trophy icon"></i>
                    <h5>Track Your Progress</h5>
                    <p>Join leagues, track your game stats, and compete with other players in the community to climb the leaderboards.</p>
                </div>
            </div>

        </div>

    </div>
    <div class="container news-section">
        <div class="d-flex justify-content-between align-items-center mb-4 news-header">
            <h2 class="fw-bold">Latest News</h2>
            <a href="{{ route('indoor') }}">View All &rarr;</a>
        </div>



        <div class="row">
            @foreach ($VenuesDetails as $venue)
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="" class="text-decoration-none">
                    <div class="card news-card">
                        <img
                            src="{{ $venue->image_url }}"
                            class="card-img"
                            alt="{{ $venue->name ?? 'Venue Image' }}">

                        <div class="date-badge">
                            {{ $venue->created_at ? $venue->created_at->format('F d, Y') : 'No Date' }}
                        </div>

                        <div class="card-img-overlay">
                            <h5 class="card-title">
                                {{ $venue->venue_name ?? 'Unnamed Venue' }}
                            </h5>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

    </div>

</div>
<div>

    <!-- Our Journey Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="{{$aboutImageUrl}}" 
                         alt="Indoor Sports Facilities" class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-3 text-dark" style="text-align: center;">{{$aboutTitle}}</h2>
                    <p class="text-muted" style="justify-content: space-between; text-align:center">{{$aboutDescription}}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Why Choose Sportynix Hub?</h2>
                <p class="text-muted">We make indoor sports bookings seamless and enjoyable.</p>
            </div>
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="p-4 feature-box shadow-sm bg-white rounded-4">
                        <i class="bi bi-geo-alt-fill fs-1 mb-3 text-success"></i>
                        <h4 class="fw-semibold">Wide Range of Venues</h4>
                        <p class="text-muted">Book from top-rated futsal, cricket, basketball, football, and volleyball courts near you.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 feature-box shadow-sm bg-white rounded-4">
                        <i class="bi bi-calendar-check-fill fs-1 mb-3 text-success"></i>
                        <h4 class="fw-semibold">Easy Online Booking</h4>
                        <p class="text-muted">Check live availability, get instant confirmation, and play without delays.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 feature-box shadow-sm bg-white rounded-4">
                        <i class="bi bi-people-fill fs-1 mb-3 text-success"></i>
                        <h4 class="fw-semibold">Trusted by Athletes</h4>
                        <p class="text-muted">Thousands of players and teams rely on Sportynix Hub for hassle-free indoor bookings.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


  <!-- Stats Section -->
  <section class="section-padding stats-section text-center bg-light">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-3 col-6">
          <h2 class="display-5 fw-bold text-success">{{ !empty($venueCount) ? $venueCount : 0 }}</h2>
          <p class="text-muted mb-0">Indoor Venues</p>
        </div>
        <div class="col-lg-3 col-6">
          <h2 class="display-5 fw-bold text-success">{{ number_format($bookingCount) }}</h2>
          <p class="text-muted mb-0">Successful Bookings</p>
        </div>
        <div class="col-lg-3 col-6">
          <h2 class="display-5 fw-bold text-success">
            {{ $reviewCount > 0 ? number_format($reviewCount / max(1, $venueCount), 1) : '4.9' }}/5
          </h2>
          <p class="text-muted mb-0">User Ratings</p>
        </div>
        <div class="col-lg-3 col-6">
          <h2 class="display-5 fw-bold text-success">30+</h2>
          <p class="text-muted mb-0">Cities Served</p>
        </div>
      </div>
    </div>
  </section>

    <!-- Testimonials -->
  <!-- Testimonials -->
<section class="testimonial-carousel py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark">What Players Say</h2>
        </div>

        @if($feedbacks->isNotEmpty())
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($feedbacks as $index => $feedback)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="text-center p-4">
                                <blockquote class="blockquote">
                                    “{{ $feedback->comment }}”
                                </blockquote>
                                <footer class="blockquote-footer mt-2 text-success fw-semibold">
                                    {{ $feedback->user->first_name ?? 'Anonymous' }}
                                </footer>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        @else
            <p class="text-center text-muted">No testimonials available yet.</p>
        @endif
    </div>
</section>

    <!-- Call to Action -->
<section class="section-padding cta-section text-center text-black">
    <div class="container">
        <h2 class="fw-bold mb-3">Ready to Book Your Game?</h2>
        <p class="lead mb-4">Find futsal, cricket, basketball, football, and volleyball venues near you.</p>
        <a href="{{ route('indoor') }}" class="btn btn-dark btn-lg fw-semibold px-5 rounded-pill" style="background-color: rgb(25, 135, 84);">Explore Indoor Venues</a>
    </div>
</section>

</div>
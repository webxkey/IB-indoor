<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
            <i class="fas fa-question-circle text-info me-2"></i>
            Help & Contact
        </h5>

        <!-- Success message -->
        @if (session()->has('contact_message'))
            <div class="alert alert-success">
                {{ session('contact_message') }}
            </div>
        @endif

        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-12 mb-4">
                <div class="border rounded-2 p-3 h-100">
                    <h6 class="fw-semibold mb-3 d-flex align-items-center">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        Contact Us
                    </h6>
                    <form wire:submit.prevent="submitContactForm">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Subject</label>
                            <input type="text" class="form-control" wire:model="contact_subject" placeholder="Enter subject">
                            @error('contact_subject') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">Message</label>
                            <textarea class="form-control" rows="4" wire:model="contact_message" placeholder="Enter your message"></textarea>
                            @error('contact_message') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <button class="btn btn-primary px-4">
                            <i class="fas fa-paper-plane me-2"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Company Details -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="border rounded-2 p-3 h-100">
                    <h6 class="fw-semibold mb-3 d-flex align-items-center">
                        <i class="fas fa-building me-2 text-secondary"></i>
                        Company Details
                    </h6>
                    <p><strong>Company:</strong> WebXKey (Pvt) Ltd</p>
                    <p><strong>Address:</strong><br>273/1D, Warana Road,<br>Central Place, Thihariya, Sri Lanka</p>
                    <p><strong>Phone:</strong> +94 76 123 4567<br><strong>Email:</strong> support@webxkey.com</p>
                    <p><strong>Business Hours:</strong><br>Mon–Fri: 9:00 AM – 6:00 PM<br>Sat: 9:00 AM – 1:00 PM<br>Sun: Closed</p>

                    <div class="mt-4">
                        <h6 class="fw-semibold mb-2">Follow Us</h6>
                        <div class="d-flex gap-2">
                            <a href="https://facebook.com/webxkey" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com/webxkey" target="_blank" class="btn btn-sm btn-outline-info rounded-circle"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com/webxkey" target="_blank" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com/company/webxkey" target="_blank" class="btn btn-sm btn-outline-dark rounded-circle"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Map -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="border rounded-2 p-3 h-100">
                    <h6 class="fw-semibold mb-3 d-flex align-items-center">
                        <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                        Our Location
                    </h6>

                    <div class="bg-light rounded-1 mb-3" style="height: 180px;">
                        <iframe
                            width="100%"
                            height="180"
                            frameborder="0"
                            style="border:0"
                            src="https://www.google.com/maps?q=WebXKey%20Pvt%20Ltd,273/1D%20Warana%20Road%20Thihariya%20Sri%20Lanka&hl=en&z=16&output=embed"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>

                    <div class="d-flex justify-content-between small">
                        <div>
                            <label class="small text-muted mb-1">GPS Coordinates</label>
                            <p class="mb-0 fw-medium">7.1204° N, 80.0752° E</p>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary rounded-2" onclick="openDirections()">
                                <i class="fas fa-directions me-1"></i> Directions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-4 pt-3 border-top">
            <h6 class="fw-semibold mb-3 d-flex align-items-center">
                <i class="fas fa-life-ring me-2 text-warning"></i>
                Frequently Asked Questions
            </h6>

            <div class="accordion" id="faqAccordion">
                <!-- Password Reset -->
                <div class="accordion-item border-0 mb-2 rounded-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed rounded-2" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne">
                            How do I reset my password?
                        </button>
                    </h2>
                    <div id="faqOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body small">
                            Go to the login page and click “Forgot Password.” You’ll receive a link via email to reset it.
                        </div>
                    </div>
                </div>

                <!-- Business Hours -->
                <div class="accordion-item border-0 mb-2 rounded-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed rounded-2" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo">
                            What are your business hours?
                        </button>
                    </h2>
                    <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body small">
                            Our office is open Monday–Friday (9:00 AM to 6:00 PM) and Saturday (9:00 AM to 1:00 PM). Closed on Sundays and public holidays.
                        </div>
                    </div>
                </div>

                <!-- Contact -->
                <div class="accordion-item border-0 mb-2 rounded-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed rounded-2" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree">
                            How can I contact support?
                        </button>
                    </h2>
                    <div id="faqThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body small">
                            You can contact our support team via this form or directly email us at <strong>support@webxkey.com</strong>.
                        </div>
                    </div>
                </div>

                <!-- Tracking -->
                <div class="accordion-item border-0 rounded-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed rounded-2" type="button" data-bs-toggle="collapse" data-bs-target="#faqFour">
                            Where is WebXKey located?
                        </button>
                    </h2>
                    <div id="faqFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body small">
                            We’re located at 273/1D, Warana Road, Thihariya, Sri Lanka — just 5 minutes from Kandy Road.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openDirections() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            const destinationLat = 7.1204; // Thihariya coordinates
            const destinationLng = 80.0752;

            window.open(`https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${destinationLat},${destinationLng}`, '_blank');
        });
    } else {
        alert('Geolocation is not supported by your browser.');
    }
}
</script>

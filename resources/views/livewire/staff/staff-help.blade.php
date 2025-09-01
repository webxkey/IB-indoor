<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
            <i class="fas fa-question-circle text-info me-2"></i>
            Help & Contact
        </h5>
        
        <div class="row">
            <!-- Contact Form (Large Grid) -->
            <div class="col-lg-12 mb-4">
                <div class="border rounded-2 p-3 h-100">
                    <h6 class="fw-semibold mb-3 d-flex align-items-center">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        Contact Us
                    </h6>
                    <form>
                        <div class="mb-3">
                            <label class="form-label small text-muted">Your Name</label>
                            <input type="text" class="form-control" placeholder="Enter your name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">Email Address</label>
                            <input type="email" class="form-control" placeholder="Enter your email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">Subject</label>
                            <select class="form-select">
                                <option selected>Select a subject</option>
                                <option>General Inquiry</option>
                                <option>Technical Support</option>
                                <option>Billing Question</option>
                                <option>Feedback</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">Message</label>
                            <textarea class="form-control" rows="3" placeholder="Enter your message"></textarea>
                        </div>
                        <button class="btn btn-primary px-4">
                            <i class="fas fa-paper-plane me-2"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Company Details (Medium Grid) -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="border rounded-2 p-3 h-100">
                    <h6 class="fw-semibold mb-3 d-flex align-items-center">
                        <i class="fas fa-building me-2 text-secondary"></i>
                        Company Details
                    </h6>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Company Name</label>
                        <p class="mb-2 fw-medium">Ouighty and Sons</p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Address</label>
                        <p class="mb-2">US$8 Broome $8<br>US$9 Boom: C.A. 90564</p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Contact</label>
                        <p class="mb-2">Phone: US$-555-555<br>Email: contact@sjgbyrgccompany.com</p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">Business Hours</label>
                        <p class="mb-2">Monday - Friday: 9:00 AM - 5:00 PM<br>Saturday: 10:00 AM - 2:00 PM</p>
                    </div>
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-2">Follow Us</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-circle p-0" style="width: 32px; height: 32px; line-height: 32px;">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-info rounded-circle p-0" style="width: 32px; height: 32px; line-height: 32px;">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-danger rounded-circle p-0" style="width: 32px; height: 32px; line-height: 32px;">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-dark rounded-circle p-0" style="width: 32px; height: 32px; line-height: 32px;">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Location Map (Small Grid) -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="border rounded-2 p-3 h-100">
                    <h6 class="fw-semibold mb-3 d-flex align-items-center">
                        <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                        Our Location
                    </h6>
                    <div class="bg-light rounded-1 mb-3" style="height: 180px; position: relative;">
                        <div class="position-absolute top-50 start-50 translate-middle text-muted">
                            <i class="fas fa-map-marked-alt fa-2x"></i>
                        </div>
                        <!-- In a real implementation, you would embed a Google Map here -->
                    </div>
                    <div class="d-flex justify-content-between small">
                        <div>
                            <label class="small text-muted mb-1">GPS Coordinates</label>
                            <p class="mb-0 fw-medium">34.0522° N, 118.2437° W</p>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary rounded-2">
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
                <div class="accordion-item border-0 mb-2 rounded-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed rounded-2" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne">
                            How do I reset my password?
                        </button>
                    </h2>
                    <div id="faqOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body small">
                            You can reset your password by clicking on the "Forgot Password" link on the login page. 
                            You'll receive an email with instructions to create a new password.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 mb-2 rounded-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed rounded-2" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo">
                            What are your business hours?
                        </button>
                    </h2>
                    <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body small">
                            Our business hours are Monday to Friday from 9:00 AM to 5:00 PM, and Saturdays from 10:00 AM to 2:00 PM. 
                            We are closed on Sundays and public holidays.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 rounded-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed rounded-2" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree">
                            How can I track my shipment?
                        </button>
                    </h2>
                    <div id="faqThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body small">
                            You can track your shipment by logging into your account and navigating to the "Shipments" section. 
                            Alternatively, use the tracking number provided in your confirmation email on our website's tracking page.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
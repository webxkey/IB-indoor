<div class="container">

    @if(session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="container-fluid bg-light min-vh-100 p-4 d-flex gap-4">

        <!-- Sidebar Navigation -->
        <div class="bg-white rounded-3 shadow-sm p-4 w-25">
            <nav class="nav flex-column">
                <a href="#" class="nav-link {{ $activeSection === 'profile' ? 'active bg-success text-white' : 'text-dark' }} rounded-2 mb-1" wire:click.prevent="showSection('profile')">
                    <i class="fas fa-user-circle me-2"></i>My Profile
                </a>
                <a href="#" class="nav-link {{ $activeSection === 'security' ? 'active bg-success text-white' : 'text-dark' }} rounded-2 mb-1" wire:click.prevent="showSection('security')">
                    <i class="fas fa-shield-alt me-2"></i>Security
                </a>
                <a href="#" class="nav-link {{ $activeSection === 'notifications' ? 'active bg-success text-white' : 'text-dark' }} rounded-2 mb-1" wire:click.prevent="showSection('notifications')">
                    <i class="fas fa-bell me-2"></i>Notifications
                </a>
                <a href="#" class="nav-link {{ $activeSection === 'team' ? 'active bg-success text-white' : 'text-dark' }} rounded-2 mb-1" wire:click.prevent="showSection('team')">
                    <i class="fas fa-users me-2"></i>Team Member
                </a>

            </nav>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-3 shadow-sm p-4 w-75">

            @if($activeSection === 'profile')
            <!-- Profile Header -->
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div class="d-flex align-items-center">
                        @if($complexes->cover_image)
                        <img src="{{ asset('storage/' . $complexes->cover_image) }}"
                            alt="Complex Image"
                            class="rounded-2 me-3"
                            width="100"
                            height="100"
                            style="object-fit: cover;">
                        @else
                        <!-- Default/placeholder image if no cover image exists -->
                        <div class="rounded-2 me-3 bg-light d-flex align-items-center justify-content-center"
                            style="width: 100px; height: 100px;">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                        @endif
                        <div>
                            <h3 class="h4 fw-bold text-dark mb-1">{{ $complexes->complex_name }}</h3>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success bg-opacity-10 text-success me-2">{{ $complexes->complex_type }}</span>
                                <span class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i>{{ $complexes->county }}</span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-outline-danger px-4 rounded-2 d-flex align-items-center" wire:click="openEditModal">
                        <i class="fas fa-edit me-2"></i>Edit
                    </button>
                </div>

                <!-- Contact Information Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                            <i class="fas fa-address-card text-primary me-2"></i>
                            Contact Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="small text-muted mb-1">Email Address</label>
                                <p class="mb-3">{{ $complexes->email_address }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small text-muted mb-1">Website</label>
                                <p class="mb-3">
                                    <a href="{{ $complexes->website }}" target="_blank" class="text-decoration-none">
                                        {{ $complexes->website }}
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small text-muted mb-1">Contact Number</label>
                                <p class="mb-3">{{ $complexes->contact_number }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small text-muted mb-1">Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $complexes->status == 'Active' ? 'success' : 'danger' }}">
                                        {{ $complexes->status }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                            <i class="fas fa-map-marked-alt text-primary me-2"></i>
                            Address
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="small text-muted mb-1">County</label>
                                <p class="mb-3">{{ $complexes->county }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small text-muted mb-1">Location</label>
                                <p class="mb-3">{{ $complexes->location }}</p>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="small text-muted mb-1">Full Address</label>
                                <p class="mb-3">{{ $complexes->address }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="small text-muted mb-1">Postal Code</label>
                                <p class="mb-0">{{ $complexes->postal_code }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operating Details Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                            <i class="fas fa-clock text-primary me-2"></i>
                            Operating Details
                        </h5>

                        <!-- Opening Hours -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">Opening Hours</h6>
                            <div class="table-responsive">
                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        @foreach($complexes->opening_hours as $day => $time)
                                        <tr>
                                            <td class="w-25 fw-medium text-muted">{{ ucfirst($day) }}</td>
                                            <td>
                                                @if($time)
                                                <span class="badge bg-success bg-opacity-10 text-success">{{ $time }}</span>
                                                @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger">Closed</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div>
                            <h6 class="fw-semibold mb-3">Amenities</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($complexes->amenities as $amenity)
                                <span class="badge bg-light text-dark border small fw-normal py-2 px-3 d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    {{ $amenity }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                            <i class="fas fa-photo-video text-primary me-2"></i>
                            Media
                        </h5>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Video Tour URL</label>
                            <p>
                                <a href="{{ $complexes->video_tour_url }}" target="_blank" class="text-decoration-none">
                                    {{ $complexes->video_tour_url ?: 'Not available' }}
                                </a>
                            </p>
                        </div>
                        <div class="mb-4">
                            <label class="small text-muted mb-2">Gallery Images</label>
                            @if(count($complexes->gallery_images ?? []) > 0)
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($complexes->gallery_images as $image)
                                <div class="position-relative" style="width: 120px; height: 120px;">
                                    <img src="{{ asset($image) }}"
                                        alt="Gallery Image"
                                        class="rounded-2 w-100 h-100"
                                        style="object-fit: cover;">

                                    <!-- View Full Image Button -->
                                    <a href="{{ asset($image) }}"
                                        target="_blank"
                                        class="position-absolute top-0 end-0 m-1 bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                        style="width: 28px; height: 28px;"
                                        title="View full size">
                                        <i class="fas fa-expand text-muted" style="font-size: 0.7rem;"></i>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-muted small">No gallery images available</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Details Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Additional Details
                        </h5>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Description</label>
                            <p class="mb-0">{{ $complexes->description }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Terms & Conditions</label>
                            <p class="mb-0">{{ $complexes->terms }}</p>
                        </div>
                        <div>
                            <label class="small text-muted mb-1">Social Links</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($complexes->social_links as $platform => $url)
                                <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill d-flex align-items-center">
                                    <i class="fab fa-{{ strtolower($platform) }} me-2"></i>
                                    {{ ucfirst($platform) }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @elseif($activeSection === 'security')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                        <i class="fas fa-lock text-primary me-2"></i>
                        Security Settings
                    </h5>

                    <!-- Two-Factor Authentication Section -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <label class="small text-muted mb-1">Two-Factor Authentication</label>
                                <p class="mb-0">Add an extra layer of security to your account</p>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary rounded-2 px-3">
                                    <i class="fas fa-edit me-1"></i> Configure
                                </button>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="twoFactorSwitch">
                            <label class="form-check-label small" for="twoFactorSwitch">Enable 2FA</label>
                        </div>
                    </div>

                    <!-- Password Change Section -->
                    <div class="mb-4 pb-3 border-bottom">
                        <h6 class="fw-semibold mb-3 d-flex align-items-center">
                            <i class="fas fa-key text-warning me-2"></i>
                            Change Password
                        </h6>

                        @if (session()->has('password_message'))
                        <div class="alert alert-success alert-dismissible fade show mb-3">
                            {{ session('password_message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label small text-muted">Current Password</label>
                            <div class="input-group">
                                <input
                                    wire:model="current_password"
                                    type="password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    placeholder="Enter current password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">New Password</label>
                            <div class="input-group">
                                <input
                                    wire:model="new_password"
                                    type="password"
                                    class="form-control @error('new_password') is-invalid @enderror"
                                    placeholder="Enter new password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">8+ characters with uppercase, lowercase, number & symbol</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted">Confirm New Password</label>
                            <div class="input-group">
                                <input
                                    wire:model="new_password_confirmation"
                                    type="password"
                                    class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                    placeholder="Confirm new password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button
                            wire:click="changePassword"
                            wire:loading.attr="disabled"
                            class="btn btn-primary px-4">
                            <span wire:loading.remove wire:target="changePassword">
                                <i class="fas fa-save me-2"></i> Update Password
                            </span>
                            <span wire:loading wire:target="changePassword">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Updating...
                            </span>
                        </button>
                    </div>



                    <!-- CCTV Maintenance Section -->
                    <div class="mb-3">
                        <h6 class="fw-semibold mb-3 d-flex align-items-center">
                            <i class="fas fa-video text-danger me-2"></i>
                            CCTV Maintenance
                        </h6>
                        <div class="mb-3">
                            <label class="small text-muted mb-2">Camera Status</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="border rounded-2 p-2 text-center" style="width: 120px;">
                                    <div class="bg-light rounded-1 mb-2" style="height: 80px; position: relative;">
                                        <div class="position-absolute top-50 start-50 translate-middle text-muted">
                                            <i class="fas fa-video-slash"></i>
                                        </div>
                                    </div>
                                    <span class="badge bg-success small">Main Entrance</span>
                                    <div class="text-danger small mt-1">Offline</div>
                                </div>
                                <div class="border rounded-2 p-2 text-center" style="width: 120px;">
                                    <div class="bg-light rounded-1 mb-2" style="height: 80px; position: relative;">
                                        <div class="position-absolute top-50 start-50 translate-middle text-muted">
                                            <i class="fas fa-video"></i>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary small">Lobby</span>
                                    <div class="text-success small mt-1">Online</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Maintenance Schedule</label>
                            <div class="d-flex align-items-center">
                                <select class="form-select form-select-sm w-auto me-2">
                                    <option>Daily</option>
                                    <option selected>Weekly</option>
                                    <option>Monthly</option>
                                </select>
                                <button class="btn btn-sm btn-outline-secondary rounded-2">
                                    <i class="fas fa-calendar-alt me-1"></i> Set Schedule
                                </button>
                            </div>
                        </div>
                        <button class="btn btn-outline-danger rounded-2 px-3">
                            <i class="fas fa-sync-alt me-1"></i> Restart All Cameras
                        </button>
                    </div>

                    <!-- Last Login Information -->
                    <div class="mt-4 pt-3 border-top">
                        <label class="small text-muted mb-1">Last Login</label>
                        <p class="mb-0">
                            <i class="fas fa-clock me-1 text-muted"></i>
                            <span class="fw-medium">2023-11-15 14:30:22</span> from
                            <span class="fw-medium">192.168.1.100</span> (Chrome, Windows)
                        </p>
                    </div>
                </div>
            </div>
            @elseif($activeSection === 'notifications')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                        <i class="fas fa-bell text-primary me-2"></i>
                        Notification Settings
                    </h5>

                    <!-- Notification Preferences -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <label class="small text-muted mb-1">Email Notifications</label>
                                <p class="mb-0 small">Receive notifications via email</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                <label class="form-check-label small" for="emailNotifications"></label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <label class="small text-muted mb-1">SMS Notifications</label>
                                <p class="mb-0 small">Receive text message alerts</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="smsNotifications">
                                <label class="form-check-label small" for="smsNotifications"></label>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Bookings -->
                    <div class="mb-4 pb-3 border-bottom">
                        <h6 class="fw-semibold mb-3 d-flex align-items-center">
                            <i class="fas fa-calendar-check text-success me-2"></i>
                            Upcoming Bookings
                        </h6>

                    </div>


                </div>
            </div>
            @elseif($activeSection === 'team')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-semibold mb-0 d-flex align-items-center">
                            <i class="fas fa-users text-primary me-2"></i>
                            Facility Staff
                        </h5>
                        <button class="btn btn-sm btn-primary rounded-2 px-3" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                            <i class="fas fa-plus me-1"></i> Add Staff
                        </button>
                    </div>

                    <!-- Staff Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search staff...">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <select class="form-select">
                                <option selected>All Roles</option>
                                <option>Reception</option>
                                <option>Game Supervisor</option>
                                <option>Maintenance</option>
                                <option>Manager</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <select class="form-select">
                                <option selected>All Shifts</option>
                                <option>Morning (8AM-4PM)</option>
                                <option>Evening (4PM-12AM)</option>
                                <option>Night (12AM-8AM)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Staff Grid -->
                    <div class="row">
                        <!-- Staff Member 1 - Reception -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="position-relative mb-3">
                                        <img src="https://randomuser.me/api/portraits/women/44.jpg"
                                            class="rounded-circle shadow"
                                            width="100" height="100"
                                            alt="Sarah Johnson">
                                        <span class="badge bg-success position-absolute bottom-0 end-0 rounded-circle p-2">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    </div>
                                    <h6 class="fw-semibold mb-1">Sarah Johnson</h6>
                                    <p class="small text-muted mb-2">Reception Staff</p>
                                    <div class="d-flex justify-content-center gap-2 mb-3">
                                        <span class="badge bg-primary">Shift: Morning</span>
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary rounded-circle p-0" style="width: 32px; height: 32px;">
                                            <i class="fas fa-calendar"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info rounded-circle p-0" style="width: 32px; height: 32px;">
                                            <i class="fas fa-phone"></i>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary rounded-circle p-0"
                                                style="width: 32px; height: 32px;"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-trash me-2"></i>Remove</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-clock me-2"></i>View Schedule</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 pt-0">
                                    <small class="text-muted">Currently: On Duty</small>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Member 2 - Game Supervisor -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="position-relative mb-3">
                                        <img src="https://randomuser.me/api/portraits/men/32.jpg"
                                            class="rounded-circle shadow"
                                            width="100" height="100"
                                            alt="Michael Chen">
                                        <span class="badge bg-danger position-absolute bottom-0 end-0 rounded-circle p-2">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    </div>
                                    <h6 class="fw-semibold mb-1">Michael Chen</h6>
                                    <p class="small text-muted mb-2">Bowling Alley Supervisor</p>
                                    <div class="d-flex justify-content-center gap-2 mb-3">
                                        <span class="badge bg-warning text-dark">Shift: Evening</span>
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary rounded-circle p-0" style="width: 32px; height: 32px;">
                                            <i class="fas fa-calendar"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info rounded-circle p-0" style="width: 32px; height: 32px;">
                                            <i class="fas fa-phone"></i>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary rounded-circle p-0"
                                                style="width: 32px; height: 32px;"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-trash me-2"></i>Remove</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-clock me-2"></i>View Schedule</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 pt-0">
                                    <small class="text-muted">Currently: Off Duty (Starts at 4PM)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Member 3 - Maintenance -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="position-relative mb-3">
                                        <img src="https://randomuser.me/api/portraits/men/75.jpg"
                                            class="rounded-circle shadow"
                                            width="100" height="100"
                                            alt="David Wilson">
                                        <span class="badge bg-success position-absolute bottom-0 end-0 rounded-circle p-2">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    </div>
                                    <h6 class="fw-semibold mb-1">David Wilson</h6>
                                    <p class="small text-muted mb-2">Facility Maintenance</p>
                                    <div class="d-flex justify-content-center gap-2 mb-3">
                                        <span class="badge bg-info text-dark">Shift: Flexible</span>
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary rounded-circle p-0" style="width: 32px; height: 32px;">
                                            <i class="fas fa-calendar"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info rounded-circle p-0" style="width: 32px; height: 32px;">
                                            <i class="fas fa-phone"></i>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary rounded-circle p-0"
                                                style="width: 32px; height: 32px;"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-trash me-2"></i>Remove</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-clock me-2"></i>View Schedule</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 pt-0">
                                    <small class="text-muted">Currently: On Duty (Equipment Check)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Member 4 - Manager -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="position-relative mb-3">
                                        <img src="https://randomuser.me/api/portraits/women/68.jpg"
                                            class="rounded-circle shadow"
                                            width="100" height="100"
                                            alt="Emma Rodriguez">
                                        <span class="badge bg-success position-absolute bottom-0 end-0 rounded-circle p-2">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    </div>
                                    <h6 class="fw-semibold mb-1">Emma Rodriguez</h6>
                                    <p class="small text-muted mb-2">Facility Manager</p>
                                    <div class="d-flex justify-content-center gap-2 mb-3">
                                        <span class="badge bg-dark">Shift: Full-time</span>
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary rounded-circle p-0" style="width: 32px; height: 32px;">
                                            <i class="fas fa-calendar"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info rounded-circle p-0" style="width: 32px; height: 32px;">
                                            <i class="fas fa-phone"></i>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary rounded-circle p-0"
                                                style="width: 32px; height: 32px;"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-trash me-2"></i>Remove</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-clock me-2"></i>View Schedule</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 pt-0">
                                    <small class="text-muted">Currently: In Meeting</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Staff pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Add Staff Modal -->
            <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addStaffModalLabel">Add New Staff Member</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Role</label>
                                        <select class="form-select" required>
                                            <option value="">Select Role</option>
                                            <option>Reception</option>
                                            <option>Game Supervisor</option>
                                            <option>Maintenance</option>
                                            <option>Manager</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Primary Shift</label>
                                        <select class="form-select" required>
                                            <option value="">Select Shift</option>
                                            <option>Morning (8AM-4PM)</option>
                                            <option>Evening (4PM-12AM)</option>
                                            <option>Night (12AM-8AM)</option>
                                            <option>Flexible</option>
                                            <option>Full-time</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Profile Photo</label>
                                    <input type="file" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Assigned Games/Facilities</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bowlingCheck">
                                        <label class="form-check-label" for="bowlingCheck">Bowling Alley</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="poolCheck">
                                        <label class="form-check-label" for="poolCheck">Pool Tables</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="arcadeCheck">
                                        <label class="form-check-label" for="arcadeCheck">Arcade Games</label>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary">Add Staff Member</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <!-- Edit Modal -->
        @if($isEditModalOpen)
        <div class="modal fade show d-block" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Edit Complex Details</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeEditModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="saveChanges">
                            <!-- Basic Information -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">Basic Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="complex_name" class="form-label">Complex Name</label>
                                        <input type="text" class="form-control" id="complex_name"
                                            wire:model="complex_name"
                                            value="{{ $complex_name }}">
                                        @error('complex_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="complex_type" class="form-label">Complex Type</label>
                                        <input type="text" class="form-control" id="complex_type"
                                            wire:model="complex_type"
                                            value="{{ $complex_type }}">
                                        @error('complex_type') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" wire:model="status">
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cover_image" class="form-label">Cover Image</label>
                                        <input type="file" class="form-control" id="cover_image" wire:model="cover_image">

                                        <!-- Show current image -->
                                        @if($existing_cover_image)
                                        <div class="mt-2">
                                            <p>Current Image:</p>
                                            <img src="{{ asset('storage/' . $existing_cover_image) }}"
                                                alt="Current Cover Image"
                                                style="max-width: 200px; max-height: 200px;"
                                                class="img-thumbnail">
                                        </div>
                                        @endif

                                        @error('cover_image') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Contact Information -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">Contact Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="email_address" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" wire:model="email_address">
                                        @error('email_address') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="tel" class="form-control" wire:model="contact_number">
                                        @error('contact_number') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="url" class="form-control" wire:model="website">
                                        @error('website') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Address -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">Address</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="county" class="form-label">County</label>
                                        <input type="text" class="form-control" wire:model="county">
                                        @error('county') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control" wire:model="location">
                                        @error('location') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="address" class="form-label">Full Address</label>
                                        <textarea class="form-control" wire:model="address" rows="2"></textarea>
                                        @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="postal_code" class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" wire:model="postal_code">
                                        @error('postal_code') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Operating Hours -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">Operating Hours</h6>
                                <div class="row g-3">
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="{{ strtolower($day) }}_hours" class="form-label">
                                                {{ $day }}
                                            </label>
                                            <input type="text"
                                                class="form-control @error('opening_hours.'.$day) is-invalid @enderror"
                                                wire:model="opening_hours.{{ $day }}"
                                                id="{{ strtolower($day) }}_hours"
                                                placeholder="e.g. 8:00 AM - 10:00 PM"
                                                value="{{ $opening_hours[$day] ?? '' }}">
                                            @error('opening_hours.'.$day)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Amenities -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">Amenities</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-control" style="min-height: 100px;">
                                            <!-- Current Amenities -->
                                            <div class="mb-3">
                                                <label class="form-label">Amenities</label>

                                                @if(count($amenities) > 0)
                                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
                                                    @foreach($amenities as $index => $amenity)
                                                    <div class="col">
                                                        <div class="bg-light text-dark border small fw-normal py-2 px-3 d-flex align-items-center justify-content-between rounded">
                                                            <span>{{ $amenity }}</span>
                                                            <button type="button"
                                                                class="btn btn-sm btn-link text-danger p-0"
                                                                wire:click="removeAmenity({{ $index }})"
                                                                title="Remove amenity">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @else
                                                <div class="text-muted">No amenities added yet</div>
                                                @endif
                                            </div>

                                            <!-- Add New Amenity -->
                                            <div class="mt-3 d-flex align-items-center">
                                                <input type="text" class="form-control form-control-sm w-auto"
                                                    wire:model="new_amenity"
                                                    placeholder="Add new amenity"
                                                    wire:keydown.enter="addAmenity">
                                                <button type="button" class="btn btn-sm btn-success ms-2"
                                                    wire:click="addAmenity"
                                                    wire:loading.attr="disabled">
                                                    <span wire:loading.remove>Add</span>
                                                    <span wire:loading>
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        Adding...
                                                    </span>
                                                </button>
                                            </div>
                                            @error('new_amenity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Media -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">Media</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="video_tour_url" class="form-label">Video Tour URL</label>
                                        <input type="url" class="form-control" wire:model="video_tour_url">
                                        @error('video_tour_url') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="gallery_images" class="form-label">Gallery Images (Upload 4 at a time)</label>

                                        <div class="input-group mb-3">
                                            <input type="file"
                                                class="form-control @error('gallery_images.*') is-invalid @enderror"
                                                id="gallery_images"
                                                wire:model="gallery_images"
                                                multiple
                                                accept="image/*">
                                            <button class="btn btn-primary"
                                                type="button"
                                                wire:click="uploadGallery"
                                                wire:loading.attr="disabled"
                                                wire:target="gallery_images">
                                                <span wire:loading.remove wire:target="gallery_images">Upload</span>
                                                <span wire:loading wire:target="gallery_images">
                                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                                    Uploading...
                                                </span>
                                            </button>
                                        </div>

                                        @error('gallery_images')
                                        <div class="text-danger small mb-2">{{ $message }}</div>
                                        @enderror
                                        @error('gallery_images.*')
                                        <div class="text-danger small mb-2">{{ $message }}</div>
                                        @enderror

                                        <div class="d-flex flex-wrap gap-3 mt-3">
                                            <!-- Existing Images -->
                                            @foreach($existing_gallery_images as $index => $image)
                                            <div class="card position-relative" style="width: 120px;">
                                                <img src="{{ asset($image) }}"
                                                    class="card-img-top"
                                                    alt="Gallery image {{ $index + 1 }}"
                                                    height="100">
                                                <div class="card-body p-2">
                                                    <div class="d-flex justify-content-between">
                                                        <a href="{{ asset($image) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-link text-primary"
                                                            title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-link text-danger"
                                                            wire:click="removeImage({{ $index }})"
                                                            wire:confirm="Are you sure you want to delete this image?"
                                                            title="Delete">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach

                                            <!-- New Upload Previews -->
                                            @foreach($gallery_images as $index => $image)
                                            <div class="card position-relative" style="width: 120px;">
                                                <img src="{{ $image->temporaryUrl() }}"
                                                    class="card-img-top"
                                                    alt="New upload {{ $index + 1 }}">
                                                <span class="badge bg-success position-absolute top-0 start-0">
                                                    New
                                                </span>
                                            </div>
                                            @endforeach

                                            @if(count($existing_gallery_images) === 0 && count($gallery_images) === 0)
                                            <div class="text-center w-100 py-4 text-muted">
                                                <i class="fas fa-images fa-3x mb-2"></i>
                                                <p>No images uploaded yet</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Details -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">Additional Details</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" wire:model="description" rows="3"></textarea>
                                        @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="terms" class="form-label">Terms & Conditions</label>
                                        <textarea class="form-control" wire:model="terms" rows="3"></textarea>
                                        @error('terms') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="small text-muted mb-1">Social Links</label>

                                        <!-- Display Current Links -->
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @foreach($social_links as $platform => $url)
                                            <div class="position-relative">
                                                <a href="{{ $url }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary rounded-pill d-flex align-items-center pe-4">
                                                    <i class="fab fa-{{ strtolower($platform) }} me-1"></i>
                                                    {{ ucfirst($platform) }}
                                                </a>
                                                <button class="btn btn-sm btn-link text-danger position-absolute end-0 top-50 translate-middle-y"
                                                    wire:click="removeSocialLink('{{ $platform }}')"
                                                    title="Remove {{ $platform }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            @endforeach
                                        </div>

                                        <!-- Add New Link Form -->
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <select class="form-select form-select-sm" wire:model="new_social_platform">
                                                    <option value="">Select Platform</option>
                                                    <option value="facebook">Facebook</option>
                                                    <option value="twitter">Twitter</option>
                                                    <option value="instagram">Instagram</option>
                                                    <option value="linkedin">LinkedIn</option>
                                                    <option value="youtube">YouTube</option>
                                                    <option value="tiktok">TikTok</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="url" class="form-control form-control-sm"
                                                    wire:model="new_social_url"
                                                    placeholder="https://...">
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-sm btn-success w-100"
                                                    wire:click="addSocialLink"
                                                    wire:loading.attr="disabled">
                                                    <span wire:loading.remove>Add</span>
                                                    <span wire:loading>
                                                        <span class="spinner-border spinner-border-sm"></span>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>

                                        @error('new_social_platform') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        @error('new_social_url') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Cancel</button>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>

        <style>
            .gallery-thumbnail {
                width: 120px;
                height: 120px;
                transition: all 0.2s ease;
            }

            .gallery-thumbnail:hover {
                transform: scale(1.05);
                z-index: 1;
            }

            .thumbnail-actions {
                position: absolute;
                top: 0;
                right: 0;
                opacity: 0;
                transition: opacity 0.2s ease;
            }

            .gallery-thumbnail:hover .thumbnail-actions {
                opacity: 1;
            }

            .action-btn {
                width: 28px;
                height: 28px;
                padding: 0;
                margin: 4px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        </style>
        @endif
    </div>
</div>

<!-- Include Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function togglePassword(button) {
        const input = button.parentNode.querySelector('input');
        const icon = button.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
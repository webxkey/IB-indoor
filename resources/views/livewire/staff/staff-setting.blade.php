<div>
<style>
    /* Settings page mobile fixes */
    .settings-nav-mobile {
        display: flex;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        gap: 6px;
        padding: 10px 12px;
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        scrollbar-width: none;
    }
    .settings-nav-mobile::-webkit-scrollbar { display: none; }
    .settings-nav-mobile .nav-pill {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        padding: 7px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 500;
        text-decoration: none;
        border: 1.5px solid #e2e8f0;
        color: #374151;
        background: #f9fafb;
        flex-shrink: 0;
        cursor: pointer;
        transition: all 0.2s;
    }
    .settings-nav-mobile .nav-pill.active {
        background: #19722d;
        border-color: #19722d;
        color: #fff;
    }
    .settings-nav-mobile .nav-pill i { margin-right: 5px; }
    .settings-nav-mobile button.nav-pill {
        background: #f9fafb;
        border: 1.5px solid #e2e8f0;
        cursor: pointer;
        touch-action: manipulation;
    }
    .settings-nav-mobile button.nav-pill.active {
        background: #19722d;
        border-color: #19722d;
        color: #fff;
    }

    @media (max-width: 767px) {
        .settings-sidebar-desktop { display: none !important; }
        .settings-nav-mobile { display: flex !important; }
        .settings-content { padding: 14px 12px !important; border-radius: 0 !important; box-shadow: none !important; }
        .settings-wrapper { padding: 0 !important; gap: 0 !important; background: #f8f9fa; min-height: 100vh; }

        /* Profile image smaller */
        .settings-content img.rounded-2 { width: 64px !important; height: 64px !important; }

        /* Row → stack on mobile */
        .settings-content .row.g-3 > [class*="col-md"] { margin-bottom: 0; }

        /* Reduce card body padding */
        .settings-content .card-body { padding: 14px; }

        /* Fix flex header wrapping */
        .settings-content .d-flex.justify-content-between.align-items-center {
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Form fields full width */
        .settings-content .form-control,
        .settings-content .form-select { font-size: 0.9rem; }

        /* Modals full screen on mobile */
        .modal-dialog { margin: 0 !important; max-width: 100% !important; }
        .modal-content { border-radius: 0 !important; min-height: 100vh; }
    }
    @media (min-width: 768px) {
        .settings-nav-mobile { display: none !important; }
        .settings-sidebar-desktop { display: block !important; }
    }
</style>

<div class="container-fluid px-0 px-md-3">

    @if(session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show mx-2 mx-md-0 mt-2" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mx-2 mx-md-0 mt-2" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @php
    $navSections = [
        'profile'       => ['fa-user-circle', 'Profile',      'My Profile'],
        'security'      => ['fa-shield-alt',  'Security',     'Security'],
        'notifications' => ['fa-bell',        'Notifications','Notifications'],
        'team'          => ['fa-users',       'Team',         'Team Member'],
        'opening_time'  => ['fa-clock',       'Hours',        'Opening Time'],
        'sports'        => ['fa-futbol',      'Sports',       'Sports Settings'],
        'cctv'          => ['fa-video',       'CCTV',         'CCTV Cameras'],
    ];
    @endphp

    {{-- Mobile horizontal tab bar (visible only on mobile) --}}
    <div class="settings-nav-mobile">
        @foreach($navSections as $key => [$icon, $shortLabel, $fullLabel])
        <a href="{{ route('staff.setting') }}?section={{ $key }}"
           class="nav-pill {{ $activeSection === $key ? 'active' : '' }}">
            <i class="fas {{ $icon }}"></i>{{ $shortLabel }}
        </a>
        @endforeach
    </div>

    <div class="d-flex gap-4 p-3 p-md-4 bg-light min-vh-100 settings-wrapper align-items-start">

        {{-- Desktop sidebar (hidden on mobile) --}}
        <div class="bg-white rounded-3 shadow-sm p-3 settings-sidebar-desktop" style="min-width:200px;width:220px;flex-shrink:0;">
            <nav class="nav flex-column">
                @foreach($navSections as $key => [$icon, $shortLabel, $fullLabel])
                <a href="{{ route('staff.setting') }}?section={{ $key }}"
                   class="nav-link {{ $activeSection === $key ? 'active bg-success text-white' : 'text-dark' }} rounded-2 mb-1"
                   style="text-decoration:none;">
                    <i class="fas {{ $icon }} me-2"></i>{{ $fullLabel }}
                </a>
                @endforeach
            </nav>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-3 shadow-sm p-3 p-md-4 flex-grow-1 settings-content" style="min-width:0;width:100%;">

            @if($activeSection === 'profile')
            @if($complexes)
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
                            <img src="{{ $complexes->image_url }}" alt="Default Complex Image" class="rounded-2" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        @endif
                        <div>
                            <h3 class="h4 fw-bold text-dark mb-1">{{ $complexes->name }}</h3>
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

                  

                        <!-- Amenities -->
                        <div>
                            <h6 class="fw-semibold mb-3">Amenities</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @if(isset($complexes->amenities) && is_array($complexes->amenities) && count($complexes->amenities) > 0)
                                    @foreach($complexes->amenities as $amenity)
                                    <span class="badge bg-light text-dark border small fw-normal py-2 px-3 d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ $amenity }}
                                    </span>
                                    @endforeach
                                @else
                                    <span class="text-muted small">No amenities listed</span>
                                @endif
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
                            @if(isset($existing_gallery_images) && is_array($existing_gallery_images) && count($existing_gallery_images) > 0)
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($existing_gallery_images as $index => $image)
                                <div class="position-relative" style="width: 120px; height: 120px;">
                                    <img src="{{ asset($image) }}"
                                        alt="Gallery Image"
                                        class="rounded-2 w-100 h-100"
                                        style="object-fit: cover;">
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
                                @if(isset($complexes->social_links) && is_array($complexes->social_links) && count($complexes->social_links) > 0)
                                    @foreach($complexes->social_links as $platform => $url)
                                    <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill d-flex align-items-center">
                                        <i class="fab fa-{{ strtolower($platform) }} me-2"></i>
                                        {{ ucfirst($platform) }}
                                    </a>
                                    @endforeach
                                @else
                                    <span class="text-muted small">No social links available</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Complex data not found. Please contact administrator.
            </div>
            @endif
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
                            <span class="fw-medium">{{ auth()->user()->updated_at ? auth()->user()->updated_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s') }}</span>
                            &mdash; Logged in as <span class="fw-medium">{{ ucfirst(auth()->user()->role) }}</span>
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
                        @php
                            $upcomingNotifBookings = \App\Models\BookingBooking::where('complex_id_id', $complex_id)
                                ->where('booking_date', '>=', \Carbon\Carbon::today()->toDateString())
                                ->whereNotIn('status', ['Cancelled', 'Completed'])
                                ->orderBy('booking_date')->orderBy('start_time')
                                ->limit(5)->get();
                        @endphp
                        @if($upcomingNotifBookings->count() > 0)
                            @foreach($upcomingNotifBookings as $bk)
                            <div class="d-flex align-items-center mb-2 p-2 bg-light rounded-2">
                                <div class="me-3">
                                    <i class="fas fa-calendar-day text-success fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small">{{ $bk->game_name }} — {{ $bk->user_name ?? 'Guest' }}</div>
                                    <div class="text-muted" style="font-size:0.8rem;">
                                        {{ \Carbon\Carbon::parse($bk->booking_date)->format('D, M d') }}
                                        &bull; {{ \Carbon\Carbon::parse($bk->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($bk->end_time)->format('h:i A') }}
                                        &bull; Court {{ $bk->court_number ?? 'N/A' }}
                                    </div>
                                </div>
                                <span class="badge bg-{{ $bk->status === 'Confirmed' ? 'success' : 'warning' }} bg-opacity-75">{{ $bk->status }}</span>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted small">No upcoming bookings at this time.</p>
                        @endif
                    </div>


                </div>
            </div>
            @elseif($activeSection === 'team')
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold">Team Members</h4>
                    <button class="btn btn-success" wire:click="openStaffForm()">
                        <i class="fas fa-plus me-1"></i> Add Member
                    </button>
                </div>

                @if($showStaffForm)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">{{ $editingStaffId ? 'Edit' : 'Add' }} Staff Member</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" wire:model="staffForm.name">
                                @error('staffForm.name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" wire:model="staffForm.role" placeholder="e.g. Receptionist">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" wire:model="staffForm.phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" wire:model="staffForm.email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select class="form-select" wire:model="staffForm.shift">
                                    <option value="morning">Morning</option>
                                    <option value="evening">Evening</option>
                                    <option value="night">Night</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" wire:model="staffForm.status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-success" wire:click="saveStaff" wire:loading.attr="disabled">
                                <span wire:loading wire:target="saveStaff" class="spinner-border spinner-border-sm me-1"></span>
                                Save
                            </button>
                            <button class="btn btn-outline-secondary" wire:click="closeStaffForm">Cancel</button>
                        </div>
                    </div>
                </div>
                @endif

                @if(count($staffMembers) > 0)
                <div class="row g-3">
                    @foreach($staffMembers as $member)
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-semibold mb-1">{{ $member['name'] }}</h6>
                                        <span class="badge bg-primary mb-1">{{ $member['role'] ?? 'Staff' }}</span>
                                        <p class="small text-muted mb-1"><i class="fas fa-phone me-1"></i>{{ $member['phone'] ?? 'N/A' }}</p>
                                        <p class="small text-muted mb-1"><i class="fas fa-clock me-1"></i>{{ ucfirst($member['shift'] ?? '') }} shift</p>
                                        <span class="badge bg-{{ $member['status'] === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($member['status']) }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" wire:click="openStaffForm({{ $member['id'] }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" wire:click="deleteStaff({{ $member['id'] }})" onclick="return confirm('Remove this staff member?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                    <p>No staff members yet. Click "Add Member" to get started.</p>
                </div>
                @endif
            </div>

            @elseif($activeSection === 'opening_time')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                            <i class="fas fa-clock text-primary me-2"></i>
                            Opening Hours
                        </h5>
                        <button class="btn btn-sm btn-outline-primary rounded-2 px-3" wire:click.prevent="toggleOpeningHoursEditor">
                            <i class="fas fa-edit me-1"></i> {{ $showOpeningHoursEditor ? 'Close' : 'Edit Opening Hours' }}
                        </button>
                    </div>
                        <!-- Opening Hours -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">Opening Hours</h6>
                            <div class="table-responsive">
                                @if($showOpeningHoursEditor)
                                    <form wire:submit.prevent="updateOpeningHours">
                                        <table class="table table-borderless table-sm">
                                            <tbody>
                                                @if(isset($days) && is_array($days))
                                                    @foreach($days as $day)
                                                    <tr>
                                                    <td class="w-25 fw-medium text-muted">{{ ucfirst($day) }}</td>
                                                    <td class="w-50">
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <input type="time" class="form-control form-control-sm" wire:model.live="opening_hours.{{ $day }}.open">
                                                            <span class="text-muted">to</span>
                                                            <input type="time" class="form-control form-control-sm" wire:model.live="opening_hours.{{ $day }}.close">
                                                            <div class="form-check ms-3">
                                                                <input class="form-check-input" type="checkbox" id="closed_{{ $day }}" wire:model.live="opening_hours.{{ $day }}.closed">
                                                                <label class="form-check-label small" for="closed_{{ $day }}">Closed</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted">Unable to load days</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-primary" type="submit">Save Opening Hours</button>
                                            <button class="btn btn-sm btn-outline-secondary" type="button" wire:click.prevent="toggleOpeningHoursEditor">Cancel</button>
                                        </div>
                                    </form>
                                @else
                                    <table class="table table-borderless table-sm">
                                        <tbody>
                                            @if(is_array($opening_hours) && count($opening_hours) > 0)
                                                @foreach($opening_hours as $day => $time)
                                                <tr>
                                                    <td class="w-25 fw-medium text-muted">{{ ucfirst($day) }}</td>
                                                    <td>
                                                        @if(isset($time['closed']) && $time['closed'])
                                                            <span class="badge bg-danger bg-opacity-10 text-danger">Closed</span>
                                                        @else
                                                            @php
                                                                $open = $time['open'] ?? null;
                                                                $close = $time['close'] ?? null;
                                                                $label = $open && $close ? $open . ' - ' . $close : ($open ?? '-');
                                                            @endphp
                                                            <span class="text-muted">{{ $label }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">No opening hours set</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                </div>

            </div>
            @elseif($activeSection === 'sports')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                        <i class="fas fa-futbol text-primary me-2"></i>
                        Sports Settings
                    </h5>
                    <p class="text-muted small mb-4">Per-sport opening hours (override venue hours) and blocked time slots.</p>

                    @if(count($sports) === 0)
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-futbol fa-3x mb-3 opacity-25"></i>
                            <p>No sports configured for this venue yet.</p>
                        </div>
                    @else
                        @foreach($sports as $sport)
                        <div class="card border mb-3">
                            <div class="card-body">
                                {{-- Sport header --}}
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">
                                            {{ $sport->name }}
                                            <span class="badge bg-{{ strtolower($sport->status) === 'active' ? 'success' : 'secondary' }} ms-2">
                                                {{ ucfirst($sport->status) }}
                                            </span>
                                        </h6>
                                        <div class="small text-muted">
                                            <i class="fas fa-tag me-1"></i> Rs. {{ number_format((float)$sport->price, 2) }} / {{ $sport->rate_type ?? 'hour' }}
                                            &nbsp;•&nbsp;
                                            <i class="fas fa-th me-1"></i> {{ $sport->maximum_court ?? 1 }} court(s)
                                            &nbsp;•&nbsp;
                                            <i class="fas fa-clock me-1"></i>
                                            @if(!empty($sport->opening_hours))
                                                <span class="text-warning">Custom hours</span>
                                            @else
                                                <span>Venue hours</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary rounded-2"
                                            wire:click="editSportHours({{ $sport->id }})">
                                        <i class="fas fa-clock me-1"></i> Edit Hours
                                    </button>
                                </div>

                                {{-- Inline hours editor (only shown for the sport being edited) --}}
                                @if($editingSportId === (int) $sport->id)
                                <div class="border-top pt-3 mt-2">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox"
                                               id="override_{{ $sport->id }}"
                                               wire:model.live="sportOverrideHours">
                                        <label class="form-check-label small" for="override_{{ $sport->id }}">
                                            <strong>Override venue hours for this sport</strong>
                                            <span class="text-muted">— when off, the venue's opening hours apply.</span>
                                        </label>
                                    </div>

                                    @if($sportOverrideHours)
                                        <table class="table table-borderless table-sm mb-3">
                                            <tbody>
                                                @foreach($days as $day)
                                                <tr>
                                                    <td class="w-25 fw-medium text-muted">{{ ucfirst($day) }}</td>
                                                    <td>
                                                        <div class="d-flex gap-2 align-items-center flex-wrap">
                                                            <input type="time"
                                                                   class="form-control form-control-sm"
                                                                   style="max-width:140px"
                                                                   wire:model.live="sportOpeningHours.{{ $day }}.open"
                                                                   @if($sportOpeningHours[$day]['closed'] ?? false) disabled @endif>
                                                            <span class="text-muted">to</span>
                                                            <input type="time"
                                                                   class="form-control form-control-sm"
                                                                   style="max-width:140px"
                                                                   wire:model.live="sportOpeningHours.{{ $day }}.close"
                                                                   @if($sportOpeningHours[$day]['closed'] ?? false) disabled @endif>
                                                            <div class="form-check ms-2">
                                                                <input class="form-check-input" type="checkbox"
                                                                       id="closed_{{ $sport->id }}_{{ $day }}"
                                                                       wire:model.live="sportOpeningHours.{{ $day }}.closed">
                                                                <label class="form-check-label small" for="closed_{{ $sport->id }}_{{ $day }}">Closed</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif

                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-primary" type="button" wire:click="saveSportHours">
                                            <i class="fas fa-save me-1"></i> Save Hours
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" type="button" wire:click="cancelSportHoursEdit">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                                @endif

                                {{-- Read-only view of opening hours when not editing --}}
                                @if($editingSportId !== (int) $sport->id && !empty($sport->opening_hours))
                                <div class="border-top pt-3 mt-2">
                                    <h6 class="small fw-semibold text-muted mb-2">Custom Opening Hours</h6>
                                    <div class="row g-2 small">
                                        @foreach($days as $day)
                                            @php
                                                $h = is_array($sport->opening_hours) ? ($sport->opening_hours[$day] ?? null) : null;
                                            @endphp
                                            <div class="col-md-3 col-6">
                                                <span class="text-muted">{{ ucfirst($day) }}:</span>
                                                @if($h && ($h['closed'] ?? false))
                                                    <span class="badge bg-danger bg-opacity-10 text-danger">Closed</span>
                                                @elseif($h)
                                                    <span>{{ $h['open'] ?? '—' }} - {{ $h['close'] ?? '—' }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                {{-- Blocked slots --}}
                                <div class="border-top pt-3 mt-3">
                                    <h6 class="small fw-semibold text-muted mb-2">
                                        <i class="fas fa-ban me-1"></i> Blocked Time Slots
                                        @php
                                            $blocks = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
                                            $totalBlocks = 0;
                                            foreach ($blocks as $date => $times) {
                                                foreach ($times as $time => $courts) {
                                                    $totalBlocks += count($courts);
                                                }
                                            }
                                        @endphp
                                        <span class="badge bg-secondary ms-1">{{ $totalBlocks }}</span>
                                    </h6>

                                    @if($totalBlocks === 0)
                                        <p class="text-muted small mb-0">No slots blocked. Block individual slots from the <a href="{{ route('staff.bookings') }}">Bookings page</a>.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless small mb-0">
                                                <thead>
                                                    <tr class="text-muted">
                                                        <th>Date</th>
                                                        <th>Time</th>
                                                        <th>Court</th>
                                                        <th>Reason</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($blocks as $date => $times)
                                                        @foreach($times as $time => $courts)
                                                            @foreach($courts as $court => $reason)
                                                            <tr>
                                                                <td>{{ $date }}</td>
                                                                <td>{{ $time }}</td>
                                                                <td>Court {{ $court }}</td>
                                                                <td>{{ $reason }}</td>
                                                                <td class="text-end">
                                                                    <button type="button"
                                                                            class="btn btn-sm btn-link text-danger p-0"
                                                                            title="Unblock"
                                                                            wire:click="unblockSportSlot({{ $sport->id }}, '{{ $date }}', '{{ $time }}', '{{ $court }}')"
                                                                            wire:confirm="Remove this block?">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
            @elseif($activeSection === 'cctv')
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold">CCTV Cameras</h4>
                    <button class="btn btn-success" wire:click="openCameraForm()">
                        <i class="fas fa-plus me-1"></i> Add Camera
                    </button>
                </div>

                @if($showCameraForm)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">{{ $editingCameraId ? 'Edit' : 'Add' }} Camera</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Camera Name *</label>
                                <input type="text" class="form-control" wire:model="cameraForm.name" placeholder="e.g. Main Entrance">
                                @error('cameraForm.name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" wire:model="cameraForm.location" placeholder="e.g. Front Gate">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Stream URL (HLS/RTSP)</label>
                                <input type="url" class="form-control" wire:model="cameraForm.stream_url" placeholder="https://...">
                                @error('cameraForm.stream_url') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" wire:model="cameraForm.status">
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-success" wire:click="saveCamera" wire:loading.attr="disabled">Save</button>
                            <button class="btn btn-outline-secondary" wire:click="closeCameraForm">Cancel</button>
                        </div>
                    </div>
                </div>
                @endif

                @if(count($cameras) > 0)
                <div class="row g-3">
                    @foreach($cameras as $camera)
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">{{ $camera['name'] }}</h6>
                                        <p class="small text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>{{ $camera['location'] ?? 'No location' }}</p>
                                        <span class="badge bg-{{ $camera['status'] === 'online' ? 'success' : ($camera['status'] === 'offline' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($camera['status']) }}
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" wire:click="openCameraForm({{ $camera['id'] }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" wire:click="deleteCamera({{ $camera['id'] }})" onclick="return confirm('Remove this camera?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @if(!empty($camera['stream_url']))
                                <div class="mt-2">
                                    <small class="text-muted">Stream: <a href="{{ $camera['stream_url'] }}" target="_blank" class="text-truncate d-inline-block" style="max-width:200px">{{ $camera['stream_url'] }}</a></small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-video fa-3x mb-3 opacity-25"></i>
                    <p>No cameras configured yet. Click "Add Camera" to get started.</p>
                </div>
                @endif
            </div>
            @endif {{-- end activeSection block --}}

            <!-- Add Staff Modal (moved outside conditional - see below) -->
            @php /* placeholder removed */ @endphp
            @if(false) {{-- old modal placeholder --}}
                <div class="modal fade" id="addStaffModalOLD" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Staff Member</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

        <!-- Add Staff Modal (outside conditionals so it works from any tab) -->
        <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStaffModalLabel">Add New Staff Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i>
                            To add a staff member, go to <strong>Admin Panel → Indoor Admins</strong> and create a new staff account, then assign it to this complex.
                        </div>
                        <p class="text-muted">Staff account management is handled through the central admin panel to ensure proper role assignment and security.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
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
                                                <button type="button" class="btn btn-sm btn-link text-danger position-absolute end-0 top-50 translate-middle-y"
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
        </div>{{-- /settings-content --}}
    </div>{{-- /settings-wrapper --}}
</div>{{-- /container-fluid --}}

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

    // Livewire validation error event listener
    document.addEventListener('livewire:initialized', function () {
        window.Livewire.on('validationErrors', function (data) {
            console.error('Validation Errors:', data.errors);
        });
    });

</script>
</div>{{-- /livewire root --}}
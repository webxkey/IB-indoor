<div>

<style>
.settings-sidebar .nav-link {
    color: #495057;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    transition: all .15s;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    margin-bottom: 2px;
}
.settings-sidebar .nav-link:hover { background: #f1f5f2; color: #19722d; }
.settings-sidebar .nav-link.active { background: #19722d; color: #fff; }
.settings-sidebar .nav-link.active i { color: #fff; }
.settings-sidebar .nav-link i { color: #6c757d; font-size: .95rem; }
.tab-pane { display: none; }
.tab-pane.active { display: block; }
.save-row { position: sticky; bottom: 0; background: #fff; border-top: 1px solid #e9ecef; padding: 14px 0; z-index: 10; margin-top: 24px; }
.setting-card { border: none; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.07); }
.setting-card .card-header { background: none; border-bottom: 1px solid #f0f0f0; padding: 16px 20px 12px; }
.toggle-switch { position: relative; display: inline-block; width: 46px; height: 24px; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #ccc; border-radius: 24px; transition: .2s; }
.toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .2s; }
input:checked + .toggle-slider { background: #19722d; }
input:checked + .toggle-slider:before { transform: translateX(22px); }
.flash-toast { position: fixed; top: 72px; right: 20px; z-index: 9999; min-width: 280px; border-radius: 10px; animation: slideIn .3s ease; }
@keyframes slideIn { from { transform: translateX(110%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>

{{-- Flash toasts --}}
@foreach(['success_general'=>'General','success_appearance'=>'Appearance','success_booking'=>'Booking','success_notifications'=>'Notifications','success_social'=>'Social links','success_security'=>'Security','success_profile'=>'Profile','success_password'=>'Password'] as $key => $label)
@if(session($key))
<div class="flash-toast alert alert-success shadow d-flex align-items-center gap-2 py-2">
    <i class="fas fa-check-circle text-success"></i>
    <span><strong>{{ $label }}</strong> settings saved successfully.</span>
</div>
@endif
@endforeach

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-cog text-success me-2"></i>Settings</h4>
        <small class="text-muted">Manage platform configuration</small>
    </div>
    {{-- Quick stats --}}
    <div class="d-flex gap-3">
        <div class="text-center">
            <div class="fw-bold text-success">{{ $totalUsers }}</div>
            <small class="text-muted">Users</small>
        </div>
        <div class="text-center">
            <div class="fw-bold text-primary">{{ $totalAdmins }}</div>
            <small class="text-muted">Admins</small>
        </div>
        <div class="text-center">
            <div class="fw-bold text-warning">{{ $totalStaff }}</div>
            <small class="text-muted">Staff</small>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- ── LEFT SIDEBAR ──────────────────────────────────────────────── --}}
    <div class="col-md-3">
        <div class="card setting-card">
            <div class="card-body p-2 settings-sidebar">
                @php
                $tabs = [
                    ['id'=>'general',       'icon'=>'fa-sliders-h',        'label'=>'General'],
                    ['id'=>'appearance',    'icon'=>'fa-palette',          'label'=>'Appearance'],
                    ['id'=>'booking',       'icon'=>'fa-calendar-check',   'label'=>'Booking'],
                    ['id'=>'notifications', 'icon'=>'fa-bell',             'label'=>'Notifications'],
                    ['id'=>'social',        'icon'=>'fa-share-alt',        'label'=>'Social Media'],
                    ['id'=>'security',      'icon'=>'fa-shield-alt',       'label'=>'Security'],
                    ['id'=>'profile',       'icon'=>'fa-user-circle',      'label'=>'My Profile'],
                ];
                @endphp
                @foreach($tabs as $tab)
                <button wire:click="$set('activeTab','{{ $tab['id'] }}')"
                    class="nav-link {{ $activeTab===$tab['id'] ? 'active' : '' }}">
                    <i class="fas {{ $tab['icon'] }}"></i>
                    {{ $tab['label'] }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Maintenance warning --}}
        @if($maintenance_mode)
        <div class="alert alert-danger mt-3 py-2 px-3" style="font-size:.82rem;">
            <i class="fas fa-tools me-1"></i> <strong>Maintenance mode is ON.</strong> Site is inaccessible to users.
        </div>
        @endif
    </div>

    {{-- ── RIGHT PANEL ───────────────────────────────────────────────── --}}
    <div class="col-md-9">

        {{-- ════ GENERAL ════ --}}
        @if($activeTab === 'general')
        <div class="card setting-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h text-success"></i>
                <span class="fw-bold">General Settings</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Site Name <span class="text-danger">*</span></label>
                        <input wire:model="site_name" type="text" class="form-control @error('site_name') is-invalid @enderror" placeholder="IndoorB">
                        @error('site_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tagline</label>
                        <input wire:model="site_tagline" type="text" class="form-control" placeholder="Book sports venues easily">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Site Description</label>
                        <textarea wire:model="site_description" rows="2" class="form-control" placeholder="Short description for SEO & meta tags..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input wire:model="contact_email" type="email" class="form-control @error('contact_email') is-invalid @enderror" placeholder="admin@indoorb.com">
                            @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Phone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input wire:model="contact_phone" type="text" class="form-control" placeholder="+92 300 0000000">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Address</label>
                        <input wire:model="address" type="text" class="form-control" placeholder="Lahore, Pakistan">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Currency Symbol</label>
                        <input wire:model="currency" type="text" class="form-control" placeholder="Rs.">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Timezone</label>
                        <select wire:model="timezone" class="form-select">
                            <option value="Asia/Karachi">Asia/Karachi (PKT +5:00)</option>
                            <option value="Asia/Kolkata">Asia/Kolkata (IST +5:30)</option>
                            <option value="Asia/Dubai">Asia/Dubai (GST +4:00)</option>
                            <option value="UTC">UTC</option>
                            <option value="Europe/London">Europe/London</option>
                            <option value="America/New_York">America/New_York</option>
                        </select>
                    </div>
                </div>
                <div class="save-row d-flex gap-2">
                    <button wire:click="saveGeneral" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i>Save General Settings
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ════ APPEARANCE ════ --}}
        @if($activeTab === 'appearance')
        <div class="card setting-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-palette text-success"></i>
                <span class="fw-bold">Appearance</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Logo URL</label>
                        <input wire:model="logo_url" type="text" class="form-control" placeholder="/images/logo.png">
                        @if($logo_url)
                        <div class="mt-2">
                            <img src="{{ $logo_url }}" alt="Logo preview" style="height:48px;object-fit:contain;border:1px solid #dee2e6;border-radius:6px;padding:4px;background:#f8f9fa;">
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Favicon URL</label>
                        <input wire:model="favicon_url" type="text" class="form-control" placeholder="/images/logo.png">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Primary Color <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2">
                            <input wire:model="primary_color" type="color" class="form-control form-control-color" style="width:52px;height:42px;">
                            <input wire:model="primary_color" type="text" class="form-control @error('primary_color') is-invalid @enderror" placeholder="#19722d" maxlength="7">
                            @error('primary_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">Used across buttons, badges, and nav active states.</small>
                    </div>
                    <div class="col-md-8">
                        {{-- Color preview --}}
                        <label class="form-label fw-semibold">Preview</label>
                        <div class="p-3 rounded d-flex gap-2 align-items-center" style="background:#f8f9fa;border:1px solid #dee2e6;">
                            <button class="btn btn-sm text-white" style="background:{{ $primary_color }};">Primary Button</button>
                            <span class="badge text-white" style="background:{{ $primary_color }};">Badge</span>
                            <span style="color:{{ $primary_color }};font-weight:600;">Active Link</span>
                            <div style="width:20px;height:20px;border-radius:50%;background:{{ $primary_color }};"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Footer Text</label>
                        <input wire:model="footer_text" type="text" class="form-control" placeholder="© 2026 IndoorB. All rights reserved.">
                    </div>
                </div>
                <div class="save-row d-flex gap-2">
                    <button wire:click="saveAppearance" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i>Save Appearance
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ════ BOOKING ════ --}}
        @if($activeTab === 'booking')
        <div class="card setting-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-calendar-check text-success"></i>
                <span class="fw-bold">Booking Settings</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Default Slot Duration (minutes)</label>
                        <div class="input-group">
                            <input wire:model="default_slot_duration" type="number" min="15" max="480" class="form-control @error('default_slot_duration') is-invalid @enderror">
                            <span class="input-group-text">min</span>
                            @error('default_slot_duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">Platform default; venues can override.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Max Advance Booking (days)</label>
                        <div class="input-group">
                            <input wire:model="max_advance_days" type="number" min="1" max="365" class="form-control @error('max_advance_days') is-invalid @enderror">
                            <span class="input-group-text">days</span>
                            @error('max_advance_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">How far ahead users can book.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cancellation Cutoff (hours)</label>
                        <div class="input-group">
                            <input wire:model="cancellation_hours" type="number" min="0" max="168" class="form-control @error('cancellation_hours') is-invalid @enderror">
                            <span class="input-group-text">hrs before</span>
                            @error('cancellation_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">Set to 0 to allow any-time cancellation.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tax Rate (%)</label>
                        <div class="input-group">
                            <input wire:model="tax_rate" type="number" min="0" max="100" step="0.1" class="form-control @error('tax_rate') is-invalid @enderror">
                            <span class="input-group-text">%</span>
                            @error('tax_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background:#f8f9fa;">
                            <div>
                                <div class="fw-semibold">Allow Guest Booking</div>
                                <small class="text-muted">Let unregistered users book without an account.</small>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" wire:model="allow_guest_booking">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Info box --}}
                <div class="alert alert-info mt-3 py-2 px-3 d-flex gap-2 align-items-start" style="font-size:.83rem;">
                    <i class="fas fa-info-circle mt-1"></i>
                    <span>These are platform-wide defaults. Individual venues can override slot duration and hours in their Sports settings.</span>
                </div>

                <div class="save-row d-flex gap-2">
                    <button wire:click="saveBooking" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i>Save Booking Settings
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ════ NOTIFICATIONS ════ --}}
        @if($activeTab === 'notifications')
        <div class="card setting-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-bell text-success"></i>
                <span class="fw-bold">Notification Settings</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Admin Notification Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input wire:model="notify_admin_email" type="email" class="form-control @error('notify_admin_email') is-invalid @enderror" placeholder="admin@indoorb.com">
                            @error('notify_admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">All admin alerts are sent to this email.</small>
                    </div>
                </div>

                <h6 class="fw-semibold mt-4 mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.06em;">Email Triggers</h6>
                <div class="d-flex flex-column gap-2">
                    @php
                    $notifs = [
                        ['key'=>'notify_new_booking',   'label'=>'New Booking Created',     'desc'=>'Send email when a booking is confirmed.'],
                        ['key'=>'notify_cancellation',  'label'=>'Booking Cancelled',        'desc'=>'Send email when a booking is cancelled.'],
                        ['key'=>'whatsapp_notify',      'label'=>'WhatsApp Notifications',   'desc'=>'Send WhatsApp messages for booking events.'],
                    ];
                    @endphp
                    @foreach($notifs as $n)
                    <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background:#f8f9fa;">
                        <div>
                            <div class="fw-semibold small">{{ $n['label'] }}</div>
                            <small class="text-muted">{{ $n['desc'] }}</small>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" wire:model="{{ $n['key'] }}">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    @endforeach
                </div>

                <div class="save-row d-flex gap-2">
                    <button wire:click="saveNotifications" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i>Save Notifications
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ════ SOCIAL ════ --}}
        @if($activeTab === 'social')
        <div class="card setting-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-share-alt text-success"></i>
                <span class="fw-bold">Social Media Links</span>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">These links appear in the website footer and contact sections.</p>
                @php
                $socials = [
                    ['key'=>'social_facebook',  'icon'=>'fab fa-facebook',  'color'=>'#1877f2', 'label'=>'Facebook',   'placeholder'=>'https://facebook.com/yourpage'],
                    ['key'=>'social_instagram', 'icon'=>'fab fa-instagram', 'color'=>'#e4405f', 'label'=>'Instagram',  'placeholder'=>'https://instagram.com/yourpage'],
                    ['key'=>'social_twitter',   'icon'=>'fab fa-x-twitter', 'color'=>'#000',    'label'=>'X / Twitter','placeholder'=>'https://twitter.com/yourpage'],
                    ['key'=>'social_youtube',   'icon'=>'fab fa-youtube',   'color'=>'#ff0000', 'label'=>'YouTube',    'placeholder'=>'https://youtube.com/@yourchannel'],
                    ['key'=>'social_whatsapp',  'icon'=>'fab fa-whatsapp',  'color'=>'#25d366', 'label'=>'WhatsApp',   'placeholder'=>'+92 300 0000000'],
                ];
                @endphp
                <div class="d-flex flex-column gap-3">
                    @foreach($socials as $s)
                    <div>
                        <label class="form-label fw-semibold d-flex align-items-center gap-2">
                            <span style="width:28px;height:28px;border-radius:6px;background:{{ $s['color'] }};display:flex;align-items:center;justify-content:center;">
                                <i class="{{ $s['icon'] }} text-white" style="font-size:.85rem;"></i>
                            </span>
                            {{ $s['label'] }}
                        </label>
                        <input wire:model="{{ $s['key'] }}" type="{{ str_starts_with($s['key'],'social_whatsapp') ? 'text' : 'url' }}" class="form-control" placeholder="{{ $s['placeholder'] }}">
                    </div>
                    @endforeach
                </div>
                <div class="save-row d-flex gap-2">
                    <button wire:click="saveSocial" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i>Save Social Links
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ════ SECURITY ════ --}}
        @if($activeTab === 'security')
        <div class="card setting-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-shield-alt text-success"></i>
                <span class="fw-bold">Security & Access</span>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3 mb-4">
                    {{-- Maintenance Mode --}}
                    <div class="d-flex align-items-center justify-content-between p-3 rounded border {{ $maintenance_mode ? 'border-danger bg-danger bg-opacity-10' : '' }}" style="{{ !$maintenance_mode ? 'background:#f8f9fa;' : '' }}">
                        <div>
                            <div class="fw-semibold d-flex align-items-center gap-2">
                                <i class="fas fa-tools {{ $maintenance_mode ? 'text-danger' : 'text-muted' }}"></i>
                                Maintenance Mode
                                @if($maintenance_mode)<span class="badge bg-danger ms-1">ACTIVE</span>@endif
                            </div>
                            <small class="text-muted">When ON, the site shows a maintenance page to all non-admin visitors.</small>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" wire:model="maintenance_mode">
                            <span class="toggle-slider" style="{{ $maintenance_mode ? 'background:#dc3545;' : '' }}"></span>
                        </label>
                    </div>

                    {{-- Registration --}}
                    <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background:#f8f9fa;">
                        <div>
                            <div class="fw-semibold"><i class="fas fa-user-plus text-muted me-2"></i>New Registrations</div>
                            <small class="text-muted">Allow new users to register on the platform.</small>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" wire:model="registration_open">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Session Lifetime (minutes)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input wire:model="session_lifetime" type="number" min="15" max="1440" class="form-control @error('session_lifetime') is-invalid @enderror">
                            <span class="input-group-text">min</span>
                            @error('session_lifetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">Requires a server restart / config:clear to apply.</small>
                    </div>
                </div>

                <div class="alert alert-warning mt-4 py-2 px-3 d-flex gap-2 align-items-start" style="font-size:.83rem;">
                    <i class="fas fa-exclamation-triangle mt-1"></i>
                    <div>
                        <strong>Note:</strong> Enabling Maintenance Mode will lock out all users including venue staff.
                        You (admin) will still have access via the <code>/admin</code> routes.
                    </div>
                </div>

                <div class="save-row d-flex gap-2">
                    <button wire:click="saveSecurity" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i>Save Security Settings
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- ════ PROFILE ════ --}}
        @if($activeTab === 'profile')
        <div class="d-flex flex-column gap-4">

            {{-- Profile info --}}
            <div class="card setting-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-user-circle text-success"></i>
                    <span class="fw-bold">Admin Profile</span>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded" style="background:#f8f9fa;">
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="Profile" class="rounded-circle" width="64" height="64" style="object-fit:cover;border:3px solid #19722d;">
                        <div>
                            <div class="fw-bold fs-5">{{ Auth::user()->name }}</div>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                            <div><span class="badge bg-success mt-1">{{ ucfirst(Auth::user()->role ?? 'admin') }}</span></div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input wire:model="profile_name" type="text" class="form-control @error('profile_name') is-invalid @enderror">
                            @error('profile_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input wire:model="profile_email" type="email" class="form-control @error('profile_email') is-invalid @enderror">
                            @error('profile_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="save-row d-flex gap-2">
                        <button wire:click="saveProfile" class="btn btn-success px-4">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </div>
            </div>

            {{-- Change password --}}
            <div class="card setting-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-key text-success"></i>
                    <span class="fw-bold">Change Password</span>
                </div>
                <div class="card-body p-4">
                    @if(session('success_password'))
                    <div class="alert alert-success py-2">{{ session('success_password') }}</div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Current Password</label>
                            <input wire:model="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">New Password</label>
                            <input wire:model="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" autocomplete="new-password">
                            @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input wire:model="new_password_confirmation" type="password" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="save-row d-flex gap-2">
                        <button wire:click="changePassword" class="btn btn-warning px-4 text-dark">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </div>
                </div>
            </div>

            {{-- All settings overview --}}
            <div class="card setting-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-database text-success"></i>
                    <span class="fw-bold">All Settings Overview</span>
                </div>
                <div class="card-body p-0">
                    @foreach($allSettings as $group => $rows)
                    <div class="px-4 py-2 border-bottom" style="background:#f8f9fa;">
                        <span class="text-uppercase fw-bold text-muted" style="font-size:.7rem;letter-spacing:.08em;">{{ $group }}</span>
                    </div>
                    @foreach($rows as $row)
                    <div class="d-flex align-items-center justify-content-between px-4 py-2 border-bottom">
                        <div>
                            <span class="fw-semibold small">{{ $row->label }}</span>
                            <small class="text-muted ms-2 d-none d-md-inline">{{ $row->key }}</small>
                        </div>
                        <div style="max-width:260px;text-align:right;">
                            @if($row->type === 'boolean')
                                <span class="badge {{ $row->value ? 'bg-success' : 'bg-secondary' }}">{{ $row->value ? 'Enabled' : 'Disabled' }}</span>
                            @elseif($row->type === 'color')
                                <span class="d-inline-flex align-items-center gap-1">
                                    <span style="width:14px;height:14px;border-radius:3px;background:{{ $row->value }};border:1px solid #ddd;display:inline-block;"></span>
                                    <code style="font-size:.78rem;">{{ $row->value }}</code>
                                </span>
                            @else
                                <span class="text-muted small" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;display:block;">
                                    {{ $row->value ?: '—' }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @endforeach
                </div>
            </div>

        </div>
        @endif

    </div>{{-- /col-md-9 --}}
</div>{{-- /row --}}

<script>
// Auto-dismiss flash toasts after 4s
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        document.querySelectorAll('.flash-toast').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
});
document.addEventListener('livewire:navigated', function() {
    setTimeout(() => {
        document.querySelectorAll('.flash-toast').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
});
</script>

</div>

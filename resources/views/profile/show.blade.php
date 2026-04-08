<x-layouts.staff>
    <x-slot name="title">My Profile</x-slot>

    <style>
        .profile-section .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,.08);
            margin-bottom: 1.5rem;
        }
        .profile-section .card-header {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            border-radius: 1rem 1rem 0 0 !important;
            padding: 1.25rem 1.5rem 0.75rem;
        }
        .profile-section .card-header h5 {
            color: #19722d;
            font-weight: 600;
            margin: 0;
        }
        .profile-section .card-header p {
            color: #6c757d;
            font-size: 0.875rem;
            margin: 0.25rem 0 0;
        }
        .profile-section .card-body {
            padding: 1.5rem;
        }
        /* Override Jetstream Tailwind classes inside these forms */
        .profile-section label {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.375rem;
            display: block;
        }
        .profile-section input[type=text],
        .profile-section input[type=email],
        .profile-section input[type=password] {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            width: 100%;
            font-size: 0.95rem;
            transition: border-color .15s;
        }
        .profile-section input[type=text]:focus,
        .profile-section input[type=email]:focus,
        .profile-section input[type=password]:focus {
            outline: none;
            border-color: #19722d;
            box-shadow: 0 0 0 0.2rem rgba(25,114,45,.2);
        }
        .profile-section button[type=submit],
        .profile-section .btn-primary-app {
            background-color: #19722d;
            border-color: #19722d;
            color: #fff;
            border-radius: 0.5rem;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background .15s;
        }
        .profile-section button[type=submit]:hover,
        .profile-section .btn-primary-app:hover {
            background-color: #145c24;
        }
        .profile-section .text-red-600,
        .profile-section .text-red-500 { color: #dc3545 !important; font-size: 0.8rem; }
        .profile-section .text-green-600 { color: #19722d !important; font-size: 0.85rem; }
        /* Photo preview */
        .profile-section img.rounded-full { border-radius: 50% !important; }
        /* Tailwind utility resets that break Bootstrap layout */
        .profile-section .mt-1  { margin-top: 0.25rem !important; }
        .profile-section .mt-2  { margin-top: 0.5rem !important; }
        .profile-section .mt-4  { margin-top: 1rem !important; }
        .profile-section .mt-5  { margin-top: 1.25rem !important; }
        .profile-section .mb-1  { margin-bottom: 0.25rem !important; }
        .profile-section .space-y-6 > * + * { margin-top: 1.25rem; }
        .profile-section .flex   { display: flex; }
        .profile-section .items-center { align-items: center; }
        .profile-section .gap-4 { gap: 1rem; }
        .profile-section .col-span-6 { width: 100%; }
        .profile-section .grid   { display: grid; }
        .profile-section .grid-cols-6 { grid-template-columns: repeat(6,1fr); gap:1rem; }
        .profile-section .sm\:col-span-4 { grid-column: span 4; }
        @media(max-width:640px){
            .profile-section .sm\:col-span-4 { grid-column: span 6; }
        }
    </style>

    <div class="profile-section" style="max-width:860px; margin: 0 auto;">

        <div class="d-flex align-items-center mb-4 gap-3">
            <img src="{{ Auth::user()->profile_photo_url }}" class="rounded-circle" width="56" height="56" style="object-fit:cover;border:3px solid #19722d;">
            <div>
                <h4 class="mb-0 fw-bold">{{ Auth::user()->name }}</h4>
                <span class="text-muted small">{{ ucfirst(Auth::user()->role ?? 'Staff') }} &bull; {{ Auth::user()->email }}</span>
            </div>
        </div>

        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user me-2"></i>Profile Information</h5>
                <p>Update your name, email, and profile photo.</p>
            </div>
            <div class="card-body">
                @livewire('profile.update-profile-information-form')
            </div>
        </div>
        @endif

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-lock me-2"></i>Update Password</h5>
                <p>Ensure your account is using a strong password.</p>
            </div>
            <div class="card-body">
                @livewire('profile.update-password-form')
            </div>
        </div>
        @endif

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-shield-alt me-2"></i>Two-Factor Authentication</h5>
                <p>Add extra security to your account.</p>
            </div>
            <div class="card-body">
                @livewire('profile.two-factor-authentication-form')
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-desktop me-2"></i>Browser Sessions</h5>
                <p>Manage and log out active sessions on other devices.</p>
            </div>
            <div class="card-body">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>
        </div>

        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        <div class="card border-danger" style="border:1px solid #f5c6cb !important;">
            <div class="card-header" style="border-bottom-color:#f5c6cb;">
                <h5 style="color:#dc3545;"><i class="fas fa-trash-alt me-2"></i>Delete Account</h5>
                <p>Permanently delete your account.</p>
            </div>
            <div class="card-body">
                @livewire('profile.delete-user-form')
            </div>
        </div>
        @endif

    </div>
</x-layouts.staff>

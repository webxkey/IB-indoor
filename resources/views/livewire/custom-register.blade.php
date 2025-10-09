<x-layouts.auth>
    <!-- User icon -->
    <div class="user-icon-container">
        <i class="bi bi-person-circle"></i>
    </div>

    <!-- Error messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Name -->
    <div class="form-group">
        <input type="text" class="form-control" wire:model="name" placeholder="Enter Name" required>
    </div>

    <!-- Email -->
    <div class="form-group">
        <input type="email" class="form-control" wire:model="email" placeholder="Enter Email" required>
    </div>

    <!-- Password -->
    <div class="form-group">
        <input type="password" class="form-control" wire:model="password" placeholder="Enter Password" required>
    </div>

    <!-- Confirm Password -->
    <div class="form-group">
        <input type="password" class="form-control" wire:model="password_confirmation" placeholder="Confirm Password" required>
    </div>

    <!-- Register Button -->
    <button type="button" wire:click="register" class="btn btn-primary">Register</button>

    <!-- Divider -->
    <div class="divider">
        <span>Or Register With</span>
    </div>

    <!-- Social icons -->
    <div class="social-login">
        <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
        <a href="#" class="social-icon"><i class="bi bi-twitter"></i></a>
        <a href="#" class="social-icon"><i class="bi bi-google"></i></a>
        <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
        <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
    </div>

    <p class="mt-3 text-center">
        Already have an account? <a href="{{ route('login') }}" class="forgot-link">Login</a>
    </p>
</x-layouts.auth>

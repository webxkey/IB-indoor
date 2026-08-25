<x-layouts.app>
    <div class="login-container">
        <!-- Full-screen background image -->
        <div class="background-image"></div>
        
        <!-- Centered form overlay -->
        <div class="login-form-overlay">
            <!-- User icon -->
            <div class="user-icon-container">
                <i class="bi bi-person-circle"></i>
            </div>
            
            <h5 class="text-center mb-3" style="color: #304b8a; font-weight: 600;">Reset Password</h5>
            
            <p class="text-sm text-center text-secondary mb-4" style="font-size: 0.9rem;">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </p>

            @if (session('status'))
                <div class="alert alert-success py-2 small text-center mb-4">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any() && !$errors->has('email'))
                <div class="alert alert-danger py-2 small mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email field -->
                <div class="form-group mb-4">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter Email" required autofocus autocomplete="username">
                    @error('email') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary login-btn">
                    {{ __('Email Password Reset Link') }}
                </button>
                
                <!-- Back to login link -->
                <div class="text-center mt-3 form-options">
                    <a href="{{ route('login') }}" class="forgot-link">
                        {{ __('Back to login') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

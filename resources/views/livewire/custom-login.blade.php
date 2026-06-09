<div class="login-container">
    <!-- Full-screen background image -->
    <div class="background-image"></div>
    
    <!-- Centered login form overlay -->
    <div class="login-form-overlay">
        <!-- User icon -->
        <div class="user-icon-container">
            <i class="bi bi-person-circle"></i>
        </div>
        
        <form wire:submit.prevent="login">
            <!-- Error messages -->
            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Email field -->
            <div class="form-group mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="Enter Email" required autofocus>
                @error('email') <div class="invalid-feedback small">{{ $message }}</div> @enderror
            </div>
            
            <!-- Password field -->
            <div class="form-group mb-3">
                <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="Enter Password" required id="loginPassword" style="border-radius: 25px 0 0 25px;">
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleLoginPassword()" style="border-radius: 0 25px 25px 0; border-left: none;">
                        <i class="bi bi-eye" id="loginPasswordIcon"></i>
                    </button>
                </div>
                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            
            <!-- Remember & Forgot options -->
            <div class="d-flex justify-content-between form-options mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password</a>
            </div>
            
            <!-- Login button -->
            <button type="submit" class="btn btn-primary login-btn" wire:loading.attr="disabled">
                <span wire:loading.remove>Login</span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Logging in...
                </span>
            </button>
            
            <!-- Divider with text -->
            <div class="divider">
                <span>Or Login With</span>
            </div>
            
            <!-- Social media login options -->
            <div class="social-login">
                <a href="https://www.facebook.com/webxkey" class="social-icon"><i class="bi bi-facebook"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-twitter"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-google"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
            </div>
        </form>
    </div>

    <script>
        function toggleLoginPassword() {
            const input = document.getElementById('loginPassword');
            const icon = document.getElementById('loginPasswordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</div>

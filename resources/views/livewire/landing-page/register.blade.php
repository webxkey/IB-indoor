<div class="fullscreen-wrapper">
    <style>
        :root {
            --primary: #196b54;
            --primary-dark: #125a44;
            --primary-light: #e8f5f1;
            --primary-gradient: linear-gradient(135deg, #196b54 0%, #22a47f 100%);
            --secondary: #304b8a;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --border-radius: 14px;
            --transition: all 0.3s ease;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #d5d8d7ff 0%, #fafafaff 100%);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .fullscreen-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .fullscreen-container {
            width: 100%;
            max-width: 1500px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .registration-card {
            padding: 2.5rem;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .header-icon {
            background: var(--primary-gradient);
            color: white;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.2rem;
            box-shadow: 0 10px 20px rgba(25, 107, 84, 0.3);
        }

        .header-content h1 {
            font-size: 2.3rem;
            font-weight: 700;
            margin: 0;
            color: var(--primary);
        }

        .header-content p {
            font-size: 1.1rem;
            color: var(--gray);
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 3rem 0;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 5%;
            right: 5%;
            height: 3px;
            background: var(--gray-light);
            transform: translateY(-50%);
            border-radius: 2px;
            z-index: 1;
        }

        .step {
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .step-number {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--gray-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin: 0 auto 0.5rem;
            color: var(--gray);
            transition: var(--transition);
        }

        .step.active .step-number {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: scale(1.1);
        }

        .step-label {
            font-size: 0.9rem;
            color: var(--gray);
        }

        .step.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }

        .current-step-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .current-step-header h2 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .step-counter {
            color: var(--gray);
            font-size: 1rem;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            border: 1px solid var(--gray-light);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header h3 {
            font-size: 1.3rem;
            color: var(--dark);
            margin: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-input {
            padding: 14px 16px;
            border: 2px solid var(--gray-light);
            border-radius: var(--border-radius);
            font-size: 1rem;
            background: white;
            transition: var(--transition);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(25, 107, 84, 0.1);
        }

        .form-input.is-invalid {
            border-color: #dc3545;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 100px;
        }

        .alert {
            border-radius: var(--border-radius);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .opening-hours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .day-schedule {
            display: flex;
            flex-direction: column;
            padding: 1rem;
            border: 1px solid var(--gray-light);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .day-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .day-label {
            margin-left: 0.5rem;
            font-weight: 600;
        }

        .time-inputs {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .time-input {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid var(--gray-light);
            border-radius: 6px;
        }

        .time-input:disabled {
            background-color: var(--gray-light);
            cursor: not-allowed;
        }

        .time-separator {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .amenity-label {
            margin-left: 0.5rem;
        }

        .selected-count {
            background: var(--primary);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .day-checkbox,
        .amenity-checkbox {
            display: block;
            position: relative;
            padding-left: 35px;
            cursor: pointer;
            font-size: 1rem;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .day-checkbox input,
        .amenity-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark,
        .amenity-checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 24px;
            width: 24px;
            background-color: var(--gray-light);
            border-radius: 6px;
            transition: var(--transition);
            border: 1px solid #ccc;
        }

        .day-checkbox:hover input~.checkmark,
        .amenity-checkbox:hover input~.amenity-checkmark {
            background-color: #ccc;
        }

        .day-checkbox input:checked~.checkmark,
        .amenity-checkbox input:checked~.amenity-checkmark {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .checkmark:after,
        .amenity-checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .day-checkbox input:checked~.checkmark:after,
        .amenity-checkbox input:checked~.amenity-checkmark:after {
            display: block;
        }

        .day-checkbox .checkmark:after,
        .amenity-checkbox .amenity-checkmark:after {
            left: 8px;
            top: 4px;
            width: 7px;
            height: 12px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }

        .terms-section {
            margin-top: 2rem;
            padding: 1.5rem;
            background: var(--primary-light);
            border-radius: var(--border-radius);
        }

        .terms-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .terms-link:hover {
            text-decoration: underline;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 6px 18px rgba(25, 107, 84, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(25, 107, 84, 0.4);
        }

        .btn-secondary {
            background: var(--gray);
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-outline-primary {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-light);
        }

        .login-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-partition {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .partition-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            border: 1px solid var(--gray-light);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }

        .partition-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--gray-light);
        }

        .partition-header h4 {
            font-size: 1.1rem;
            color: var(--primary);
            margin: 0;
            margin-left: 0.5rem;
        }

        .partition-icon {
            background: var(--primary-light);
            color: var(--primary);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .partition-form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem;
        }

        .file-preview {
            margin-top: 0.5rem;
        }

        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            border: 2px solid var(--gray-light);
        }

        .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 0.8rem;
            cursor: pointer;
        }

        .image-container {
            position: relative;
            display: inline-block;
            margin-right: 1rem;
            margin-bottom: 1rem;
        }

        /* Success Modal Styles */
        .success-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .success-modal.show {
            opacity: 1;
            visibility: visible;
        }

        .success-modal-content {
            background: white;
            border-radius: var(--border-radius);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 90%;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .success-modal.show .success-modal-content {
            transform: translateY(0);
        }

        .success-icon {
            background: var(--primary-gradient);
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
        }

        .success-modal h3 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .success-modal p {
            color: var(--gray);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .success-modal .btn {
            margin: 0 auto;
        }

        @media (max-width: 992px) {
            .form-partition {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .fullscreen-wrapper {
                padding: 1rem;
            }

            .registration-card {
                padding: 1.5rem;
            }

            .header-content h1 {
                font-size: 1.8rem;
            }

            .step-number {
                width: 45px;
                height: 45px;
            }

            .form-actions {
                flex-direction: column;
                gap: 1rem;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .selected-count {
                align-self: flex-start;
            }

            .success-modal-content {
                padding: 2rem;
            }
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- Success Modal -->
    @if ($showSuccessModal)
    <div class="modal show d-block">
        <div class="modal-dialog">
            <div class="modal-content text-center p-4">
                <h3 class="text-success mb-3">Registration Successful!</h3>
                <p>Your complex has been created successfully.</p>

                @if (Auth::user() && Auth::user()->complex)
                <div class="mt-3 border-top pt-3 text-start">
                    <h5>Complex Details</h5>
                    <p><strong>Name:</strong> {{ Auth::user()->complex->name }}</p>
                    <p><strong>Owner:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                </div>
                @endif

                <button class="btn btn-primary mt-3" wire:click="$set('showSuccessModal', false)">Close</button>
            </div>
        </div>
    </div>
    @endif

    <div class="fullscreen-container">
        <div class="registration-card">
            <div class="form-header">
                <div class="header-icon"><i class="fas fa-building"></i></div>
                <div class="header-content">
                    <h1>Register Your Sports Complex</h1>
                    <p>Join our platform to manage your sports facility with ease</p>
                </div>
            </div>

            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center"><i class="fas fa-check-circle me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center"><i class="fas fa-exclamation-triangle me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center"><i class="fas fa-exclamation-triangle me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="progress-steps">
                <div class="step {{ $currentStep >= 1 ? 'active' : '' }}">
                    <div class="step-number">1</div><span class="step-label">Personal</span>
                </div>
                <div class="step {{ $currentStep >= 2 ? 'active' : '' }}">
                    <div class="step-number">2</div><span class="step-label">Complex</span>
                </div>
                <div class="step {{ $currentStep >= 3 ? 'active' : '' }}">
                    <div class="step-number">3</div><span class="step-label">Facility Details</span>
                </div>
            </div>

            <div class="current-step-header">
                <h2>
                    @if($currentStep == 1) Personal Information
                    @elseif($currentStep == 2) Complex Information
                    @else Facility Details
                    @endif
                </h2>
                <p class="step-counter">Step {{ $currentStep }} of 3</p>
            </div>

            <form wire:submit.prevent="register" enctype="multipart/form-data">
                {{-- STEP 1 --}}
                <div class="form-step @if($currentStep == 1) active @endif">
                    <div class="form-partition">
                        <div class="partition-card">
                            <div class="partition-header">
                                <div class="partition-icon"><i class="fas fa-user"></i></div>
                                <h4>Personal Details</h4>
                            </div>
                            <div class="partition-form-group">
                                <label for="name" class="form-label"><i class="fas fa-user me-2"></i>Full Name </label>
                                <input type="text" class="form-input @error('name') is-invalid @enderror" id="name" wire:model.lazy="name" required placeholder="Enter your full name">
                                @error('name') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                            <div class="partition-form-group">
                                <label for="email" class="form-label"><i class="fas fa-envelope me-2"></i>Email Address </label>
                                <input type="email" class="form-input @error('email') is-invalid @enderror" id="email" wire:model.lazy="email" required placeholder="Enter your email">
                                @error('email') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                            <div class="partition-form-group">
                                <label for="contact" class="form-label"><i class="fas fa-phone me-2"></i>Contact Number </label>
                                <input type="text" class="form-input @error('contact') is-invalid @enderror" id="contact" wire:model.lazy="contact" required placeholder="+1 234 567 890">
                                @error('contact') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="partition-card">
                            <div class="partition-header">
                                <div class="partition-icon"><i class="fas fa-lock"></i></div>
                                <h4>Account Security</h4>
                            </div>
                            <div class="partition-form-group">
                                <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>Password </label>
                                <input type="password" class="form-input @error('password') is-invalid @enderror" id="password" wire:model="password" required placeholder="Create a password">
                                @error('password') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                            <div class="partition-form-group">
                                <label for="password_confirmation" class="form-label"><i class="fas fa-lock me-2"></i>Confirm Password </label>
                                <input type="password" class="form-input" id="password_confirmation" wire:model="password_confirmation" required placeholder="Confirm your password">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 2 --}}
                <div class="form-step @if($currentStep == 2) active @endif">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="complex_name" class="form-label"><i class="fas fa-building me-2"></i>Complex Name </label>
                            <input type="text" class="form-input @error('complex_name') is-invalid @enderror" id="complex_name" wire:model="complex_name" required placeholder="Enter your complex name">
                            @error('complex_name') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="complex_type" class="form-label"><i class="fas fa-warehouse me-2"></i>Complex Type </label>
                            <select class="form-input @error('complex_type') is-invalid @enderror" id="complex_type" wire:model="complex_type" required>
                                <option value="Indoor">Indoor</option>
                                <option value="Outdoor">Outdoor</option>
                                <option value="Both">Both</option>
                            </select>
                            @error('complex_type') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="status" class="form-label"><i class="fas fa-info-circle me-2"></i>Status </label>
                            <select class="form-input @error('status') is-invalid @enderror" id="status" wire:model="status" required>
                                <option value="Active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="maintenance">Under Maintenance</option>
                                <option value="new">New</option>
                            </select>
                            @error('status') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="county" class="form-label"><i class="fas fa-map me-2"></i>County </label>
                            <select class="form-input @error('county') is-invalid @enderror" id="county" wire:model="county" required>
                                <option value="">Select County</option>
                                @foreach($availableCity as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                            </select>
                            @error('county') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="location" class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Location </label>
                            <input type="text" class="form-input @error('location') is-invalid @enderror" id="location" wire:model="location" required placeholder="Enter location area">
                            @error('location') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="postal_code" class="form-label"><i class="fas fa-mail-bulk me-2"></i>Postal Code </label>
                                <input type="text" class="form-input @error('postal_code') is-invalid @enderror" id="postal_code" wire:model="postal_code" placeholder="Postal code">
                            @error('postal_code') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group full-width">
                            <label for="address" class="form-label"><i class="fas fa-home me-2"></i>Full Address </label>
                            <textarea class="form-input @error('address') is-invalid @enderror" id="address" wire:model="address" rows="3" required placeholder="Enter complete facility address"></textarea>
                            @error('address') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="contact_number" class="form-label"><i class="fas fa-phone me-2"></i>Complex Contact </label>
                            <input type="text" class="form-input @error('contact_number') is-invalid @enderror" id="contact_number" wire:model="contact_number" required placeholder="Complex contact number">
                            @error('contact_number') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="email_address" class="form-label"><i class="fas fa-envelope me-2"></i>Complex Email </label>
                                <input type="email" class="form-input @error('email_address') is-invalid @enderror" id="email_address" wire:model="email_address" placeholder="Complex email address">
                            @error('email_address') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="website" class="form-label"><i class="fas fa-globe me-2"></i>Website</label>
                            <input type="text" class="form-input @error('website') is-invalid @enderror" id="website" wire:model="website" placeholder="https://example.com">
                            @error('website') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group full-width">
                            <label for="description" class="form-label"><i class="fas fa-file-alt me-2"></i>Description </label>
                            <textarea class="form-input @error('description') is-invalid @enderror" id="description" wire:model="description" rows="4" required placeholder="Describe your sports complex facilities and services"></textarea>
                            @error('description') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- STEP 3 --}}
                <div class="form-step @if($currentStep == 3) active @endif">
                    <!-- Sport & Facility Details Section -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3><i class="fas fa-running me-2"></i>Sport & Facility Details</h3>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="sport_type" class="form-label">Primary Sport Type </label>
                                <select class="form-input @error('sport_type') is-invalid @enderror" id="sport_type" wire:model="sport_type" required>
                                    <option value="">Select sport</option>
                                    @foreach($availableSportTypes as $sport)
                                    <option value="{{ $sport }}">{{ ucfirst($sport) }}</option>
                                    @endforeach
                                </select>
                                @error('sport_type') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="capacity" class="form-label">Capacity </label>
                                <input type="number" class="form-input @error('capacity') is-invalid @enderror" id="capacity" wire:model="capacity" min="1" required placeholder="Enter capacity">
                                @error('capacity') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="hourly_rate" class="form-label">Hourly Rate ($) </label>
                                <input type="number" step="0.01" class="form-input @error('hourly_rate') is-invalid @enderror" id="hourly_rate" wire:model="hourly_rate" min="0" required placeholder="0.00">
                                @error('hourly_rate') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="length" class="form-label">Length (meters) </label>
                                <input type="number" class="form-input @error('length') is-invalid @enderror" id="length" wire:model="length" placeholder="Length" step="0.01" required>
                                @error('length') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="width" class="form-label">Width (meters) </label>
                                <input type="number" class="form-input @error('width') is-invalid @enderror" id="width" wire:model="width" placeholder="Width" step="0.01" required>
                                @error('width') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Facility Images Section -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3><i class="fas fa-images me-2"></i>Facility Images</h3>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="cover_image" class="form-label">Cover Image</label>
                                <input type="file" class="form-input @error('cover_image') is-invalid @enderror" id="cover_image" wire:model="cover_image" accept="image/*">
                                <small class="text-muted">Main display image for the facility (max 2MB)</small>
                                @error('cover_image') <div class="error-message">{{ $message }}</div> @enderror
                                @if ($cover_image)
                                <div class="file-preview">
                                    <div class="image-container">
                                        <img src="{{ $cover_image->temporaryUrl() }}" class="preview-image">
                                        <button type="button" class="remove-image" wire:click="resetCoverImage">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="gallery_images" class="form-label">Gallery Images</label>
                                <input type="file" class="form-input @error('gallery_images.') is-invalid @enderror" id="gallery_images" wire:model="gallery_images" multiple accept="image/">
                                <small class="text-muted">Upload multiple images (max 5, 2MB each)</small>
                                @error('gallery_images.*') <div class="error-message">{{ $message }}</div> @enderror
                                @if ($gallery_images)
                                <div class="file-preview">
                                    @foreach ($gallery_images as $index => $image)
                                    <div class="image-container">
                                        <img src="{{ $image->temporaryUrl() }}" class="preview-image">
                                        <button type="button" class="remove-image" wire:click="removeGalleryImage({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Opening Hours Section -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3><i class="fas fa-clock me-2"></i>Opening Hours</h3>
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="setAllHours">
                                <i class="fas fa-copy me-1"></i>Copy Monday to All
                            </button>
                        </div>
                        <div class="opening-hours-grid">
                            @foreach(['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday'] as $dayKey => $dayLabel)
                            <div class="day-schedule">
                                <div class="day-header">
                                    <label class="day-checkbox">
                                        <input type="checkbox" wire:model="opening_hours.{{ $dayKey }}.closed">
                                        <span class="checkmark"></span>
                                        <span class="day-label">{{ $dayLabel }} (Closed)</span>
                                    </label>
                                </div>
                                <div class="time-inputs">
                                    <input type="time" class="time-input" wire:model="opening_hours.{{ $dayKey }}.open"
                                        @if($opening_hours[$dayKey]['closed'] ?? false) disabled @endif>
                                    <span class="time-separator">to</span>
                                    <input type="time" class="time-input" wire:model="opening_hours.{{ $dayKey }}.close"
                                        @if($opening_hours[$dayKey]['closed'] ?? false) disabled @endif>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Amenities Section -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3><i class="fas fa-list me-2"></i>Amenities</h3>
                            <span class="selected-count">{{ count(array_filter($amenities)) }} selected</span>
                        </div>
                        <div class="amenities-grid">
                            @foreach($availableAmenities as $amenity)
                            <label class="amenity-checkbox">
                                <input type="checkbox" value="{{ $amenity }}" wire:model="amenities">
                                <span class="amenity-checkmark"></span>
                                <span class="amenity-label">{{ ucfirst(str_replace('_', ' ', $amenity)) }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Social Media Section -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3><i class="fas fa-share-alt me-2"></i>Social Media & Links</h3>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="video_tour_url" class="form-label">Video Tour URL</label>
                                <input type="url" class="form-input @error('video_tour_url') is-invalid @enderror" id="video_tour_url" wire:model="video_tour_url" placeholder="https://youtube.com/...">
                                @error('video_tour_url') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="facebook_url" class="form-label">Facebook URL</label>
                                <input type="url" class="form-input @error('facebook_url') is-invalid @enderror" id="facebook_url" wire:model="facebook_url" placeholder="https://facebook.com/...">
                                @error('facebook_url') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="twitter_url" class="form-label">Twitter URL</label>
                                <input type="url" class="form-input @error('twitter_url') is-invalid @enderror" id="twitter_url" wire:model="twitter_url" placeholder="https://twitter.com/...">
                                @error('twitter_url') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="instagram_url" class="form-label">Instagram URL</label>
                                <input type="url" class="form-input @error('instagram_url') is-invalid @enderror" id="instagram_url" wire:model="instagram_url" placeholder="https://instagram.com/...">
                                @error('instagram_url') <div class="error-message">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Terms Section -->
                    <div class="terms-section">
                        <div class="form-check">
                            <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" wire:model="terms" required>
                            <label class="form-check-label" for="terms">
                                I confirm that all information is accurate and agree to the
                                <a href="#" class="terms-link">Terms and Conditions</a>
                            </label>
                            @error('terms') <div class="error-message">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    @if($currentStep > 1)<button type="button" class="btn btn-secondary" wire:click="previousStep" wire:loading.attr="disabled"><i class="fas fa-arrow-left me-2"></i>Previous</button>@else<div></div>@endif
                    @if($currentStep < 3)<button type="button" class="btn btn-primary" wire:click="nextStep" wire:loading.attr="disabled">Next <i class="fas fa-arrow-right ms-2"></i></button>@else<button type="submit" class="btn btn-success" wire:loading.attr="disabled"><span wire:loading.remove><i class="fas fa-check me-2"></i>Complete Registration</span><span wire:loading>
                                <div class="loading-spinner"></div>Processing...
                            </span></button>@endif
                </div>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="{{ route('login') }}" class="login-link">Sign in here</a></p>
            </div>
        </div>
    </div>
</div>
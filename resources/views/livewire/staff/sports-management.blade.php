@push('styles')
<style>
    container-fluid {
        background: var(--background-color);
        min-height: 100vh;
        padding: 0;
    }

    .btn {
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.95rem;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-label {
        font-weight: 500;
        color: #374151;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        padding: 12px 16px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
</style>
@endpush
<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Sports Management</h1>
            <p class="text-muted">Manage all sports activities and their booking status</p>
        </div>
        <div>
            <button id="addSportButton" class="btn btn-success me-2" wire:click="openModal">
                <i class="fas fa-plus me-1"></i> Add New Sport
            </button>
        </div>
    </div>

    @if(session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        @foreach($sports as $sport)
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="{{ $sport->image ?? asset('images/sports_images/default.jpg') }}"
                        alt="Sport Image" class="img-fluid rounded" style="max-height: 245px; object-fit: cover;">
                    <span class="position-absolute top-0 start-0 text-white px-2 py-1 rounded-end 
                    {{ $sport->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                        {{ $sport->status }}
                    </span>

                </div>

                <div class="card-body">
                    <h5 class="card-title">{{ $sport->name }}</h5>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Hourly Base Rate:</span>
                        <span class="fw-bold text-dark">Rs. {{ number_format($sport->price) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Available Courts:</span>
                        <span class="fw-bold text-dark">{{ $sport->maximum_court }}</span>
                    </div>

                    @php
                        $rules = is_array($sport->pricing_rules) ? $sport->pricing_rules : (is_string($sport->pricing_rules) ? (json_decode($sport->pricing_rules, true) ?? []) : []);
                        $rawCharges = $sport->additional_charges;
                        if (is_string($rawCharges)) {
                            $decoded = json_decode($rawCharges, true);
                            while (is_string($decoded)) {
                                $decoded = json_decode($decoded, true);
                            }
                            $charges = is_array($decoded) ? $decoded : [];
                        } else {
                            $charges = is_array($rawCharges) ? $rawCharges : [];
                        }
                        $maxPersons = $charges['max_persons_per_hour'] ?? null;
                    @endphp

                    @if(!empty($rules['peak_price']))
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Peak Rate ({{ \Carbon\Carbon::parse($rules['peak_start_time'] ?? '17:00')->format('g:i A') }} - {{ \Carbon\Carbon::parse($rules['peak_end_time'] ?? '22:00')->format('g:i A') }}):</span>
                        <span class="fw-bold text-primary">Rs. {{ number_format($rules['peak_price']) }}</span>
                    </div>
                    @endif

                    @if($maxPersons)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Max Swimmers/Limit:</span>
                        <span class="badge bg-info text-dark fw-bold">{{ $maxPersons }} Persons/Hr</span>
                    </div>
                    @endif

                    @if(!empty($sport->private_booking_price))
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Private Rental:</span>
                        <span class="badge bg-success fw-bold">Rs. {{ number_format($sport->private_booking_price) }}</span>
                    </div>
                    @endif

                    <div class="mt-2 pt-2 border-top">
                        <small class="text-muted d-block mb-1">Payment Policy:</small>
                        @php
                            $policyBadge = $this->getPaymentPolicyBadge($sport);
                        @endphp
                        <span class="badge {{ $policyBadge['class'] }}">{{ $policyBadge['label'] }}</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-sm btn-primary flex-fill"
                            wire:click="editSport('{{ $sport->id }}')">
                            <i class="fas fa-edit me-1"></i> Manage
                        </button>
                        <button class="btn btn-sm btn-outline-info" wire:click="openPricingModal('{{ $sport->id }}')" title="Set Pricing Rules">
                            <i class="fas fa-tags"></i> Pricing
                        </button>
                        @if($this->isPoolSport($sport->name))
                        <button class="btn btn-sm btn-outline-primary" wire:click="openPoolModal('{{ $sport->id }}')" title="Manage Pool Admission Tickets">
                            <i class="fas fa-swimming-pool me-1"></i> Tickets
                        </button>
                        @endif
                        <button class="btn btn-sm btn-outline-warning" wire:click="openSlotBlockModal('{{ $sport->id }}')" title="Manage blocked slots">
                            <i class="fas fa-ban"></i> Blocked Slots
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Sport Modal -->
    <div class="modal fade" id="addSportModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New Sport</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        wire:click="closeModal"></button>
                </div>

                <form wire:submit.prevent="saveSport">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="game_name" class="form-label fw-semibold">Game Name*</label>
                                <select class="form-select" id="game_name" wire:model.live="game_name" required>
                                    <option value="">Select Sport</option>
                                    <option value="Football">Football</option>
                                    <option value="Cricket">Cricket</option>
                                    <option value="Badminton">Badminton</option>
                                    <option value="Basketball">Basketball</option>
                                    <option value="Pools">Pools (Swimming Pool)</option>
                                    <option value="Pooltable">Pool Table (Snooker / Billiards)</option>
                                    <option value="Cricket & Football">Cricket & Football</option>
                                </select>
                                @error('game_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="game_type" class="form-label fw-semibold">Game Type*</label>
                                <select class="form-select" id="game_type" wire:model="game_type" required>
                                    <option value="" disabled>Select game type</option>
                                    <option value="Indoor">Indoor</option>
                                    <option value="Outdoor">Outdoor</option>
                                </select>
                                @error('game_type') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="rate_type" class="form-label fw-semibold">Rate Type*</label>
                                <select class="form-select" id="rate_type" wire:model="rate_type" required>
                                    <option value="" disabled>Select rate type</option>
                                    <option value="Per hour">Per hour</option>
                                    <option value="Per person">Per person</option>
                                    <option value="Per session">Per session</option>
                                    <option value="Per day">Per day</option>
                                </select>
                                @error('rate_type') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="price" class="form-label fw-semibold">Price*</label>
                                <input type="number" class="form-control" id="price" wire:model="price" step="0.01"
                                    required>
                                @error('price') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="maximum_court" class="form-label fw-semibold">Maximum Court*</label>
                                <input type="number" class="form-control" id="maximum_court" wire:model="maximum_court"
                                    min="1" required>
                                @error('maximum_court') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">Status*</label>
                                <select class="form-select" id="status" wire:model="status" required>
                                    <option value="" disabled>Select status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                                @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            

                            <div class="col-md-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" id="description" wire:model="description" rows="2"
                                    placeholder="Optional"></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            {{-- Payment Policy Customization --}}
                            <div class="col-md-12">
                                <div class="card border-primary border-opacity-25 bg-light shadow-sm">
                                    <div class="card-header bg-primary bg-opacity-10 fw-bold small text-primary text-uppercase d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-credit-card me-1"></i> Payment Policy Customization
                                        </div>
                                        @if(!$this->isOnlinePaymentEnabled)
                                        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Online Payment Disabled at Venue Level</span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        @if(!$this->isOnlinePaymentEnabled)
                                        <div class="alert alert-warning py-2 mb-3 small d-flex align-items-center">
                                            <i class="fas fa-info-circle me-2 fa-lg text-warning"></i>
                                            <div>
                                                Online payment is currently <strong>disabled</strong> in your venue settings (<a href="{{ route('staff.setting') }}?section=payments" class="fw-bold text-dark text-decoration-underline" target="_blank">Venue Settings -&gt; Payment Options</a>). Online payment options are locked unless online payment is enabled for the venue.
                                            </div>
                                        </div>
                                        @endif

                                        <div class="row g-3">
                                            <div class="col-md-{{ in_array($booking_payment_mode_override, ['advance_only', 'advance_or_full', 'partial']) ? '6' : '12' }}">
                                                <label class="form-label fw-semibold small">Payment Requirement Mode *</label>
                                                <select class="form-select form-select-sm" wire:model.live="booking_payment_mode_override" {{ !$this->isOnlinePaymentEnabled ? 'disabled' : '' }}>
                                                    <option value="">Use venue default</option>
                                                    <option value="no_payment">No online payment required</option>
                                                    <option value="advance_only">Pay advance to book</option>
                                                    <option value="full_only">Pay full amount to book</option>
                                                    <option value="advance_or_full">Allow advance or full payment</option>
                                                </select>
                                            </div>

                                            @if(in_array($booking_payment_mode_override, ['advance_only', 'advance_or_full', 'partial']))
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold small">Deposit Type *</label>
                                                <select class="form-select form-select-sm" wire:model="advance_payment_type_override" {{ !$this->isOnlinePaymentEnabled ? 'disabled' : '' }}>
                                                    <option value="percentage">Percentage (%)</option>
                                                    <option value="fixed">Fixed Amount (LKR)</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold small">Deposit Value *</label>
                                                <input type="number" class="form-control form-control-sm" wire:model="advance_payment_value_override" min="0" step="0.01" placeholder="e.g. 20" {{ !$this->isOnlinePaymentEnabled ? 'disabled' : '' }}>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Capacity / Pool Hourly Limit Customization --}}
                            <div class="col-md-12">
                                <div class="card border-info border-opacity-25 bg-light shadow-sm">
                                    <div class="card-header bg-info bg-opacity-10 fw-bold small text-info text-uppercase d-flex justify-content-between align-items-center py-2">
                                        <div>
                                            <i class="fas fa-users me-1"></i> Hourly Swimmer / Person Access Limit (Pools & Sports)
                                        </div>
                                        <div class="form-check form-switch m-0 text-lowercase">
                                            <input class="form-check-input" type="checkbox" id="capacity_limit_enabled_add" wire:model.live="capacity_limit_enabled">
                                            <label class="form-check-label fw-bold text-dark text-capitalize" for="capacity_limit_enabled_add">
                                                Enable Limit
                                            </label>
                                        </div>
                                    </div>
                                    @if($capacity_limit_enabled)
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Max Persons / Swimmers Allowed Per Hour *</label>
                                                <input type="number" class="form-control form-control-sm" wire:model="max_persons_per_hour" min="1" placeholder="e.g. 10">
                                                <small class="text-muted">Set capacity limit for pool entries and court slots</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Private Booking Options Customization --}}
                            <div class="col-md-12">
                                <div class="card border-success border-opacity-25 bg-light">
                                    <div class="card-header bg-success bg-opacity-10 fw-bold small text-success text-uppercase">
                                        <i class="fas fa-user-lock me-1"></i> Private Booking & Full Rental Customization
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="private_booking_enabled_add" wire:model.live="private_booking_enabled">
                                                    <label class="form-check-label fw-semibold" for="private_booking_enabled_add">
                                                        Enable Private Booking Rental
                                                    </label>
                                                </div>
                                            </div>

                                            @if($private_booking_enabled)
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Private Pricing Mode</label>
                                                <select class="form-select form-select-sm" wire:model.live="private_booking_pricing_mode">
                                                    <option value="flat_total">Fixed private total for the whole selected range</option>
                                                    <option value="per_hour">Private hourly rate multiplied by selected duration</option>
                                                    <option value="normal_total">Use normal calculated slot total</option>
                                                    <option value="normal_multiplier">Normal calculated slot total multiplied by private multiplier</option>
                                                </select>
                                            </div>

                                            @if(in_array($private_booking_pricing_mode, ['flat_total', 'per_hour', 'hourly_flat']))
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">
                                                    {{ in_array($private_booking_pricing_mode, ['per_hour', 'hourly_flat']) ? 'Private Hourly Rate (LKR/hr)' : 'Private Booking Price (LKR)' }}
                                                </label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">LKR</span>
                                                    <input type="number" class="form-control" wire:model="private_booking_price" min="0" step="0.01" placeholder="{{ in_array($private_booking_pricing_mode, ['per_hour', 'hourly_flat']) ? 'e.g. 5000' : 'e.g. 10000' }}">
                                                </div>
                                                <small class="text-muted">{{ in_array($private_booking_pricing_mode, ['per_hour', 'hourly_flat']) ? 'Private rate charged per hour' : 'Total flat rate for entire private rental' }}</small>
                                            </div>
                                            @endif

                                            @if($private_booking_pricing_mode === 'normal_multiplier')
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Price Multiplier</label>
                                                <input type="number" class="form-control form-control-sm" wire:model="private_booking_price_multiplier" step="0.1" min="1.0" placeholder="1.5">
                                                <small class="text-muted">Multiplied against normal calculated slot total (e.g. 1.5x)</small>
                                            </div>
                                            @endif

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Minimum Duration (Minutes)</label>
                                                <input type="number" class="form-control form-control-sm" wire:model="private_booking_min_duration_minutes" placeholder="180">
                                                <small class="text-muted">Default 180 min (3 Hours)</small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Sport</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pricing Rules Modal -->
    @if($showPricingModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5); z-index: 1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-tags me-2"></i>Customize Dynamic Peak Times & Pricing</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closePricingModal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Customize both peak time windows and peak rates. Leave empty to use standard base pricing.</p>
                    
                    {{-- Peak Schedule Global Enable Switch --}}
                    <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 mb-3 shadow-sm" style="border-radius: 8px;">
                        <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="far fa-calendar-alt text-primary fs-5 me-2"></i>
                                <div>
                                    <h6 class="fw-bold text-primary mb-0 small text-uppercase">Enable Per-Day Peak Time Schedule</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">Configure custom peak time start & end hours per day</small>
                                </div>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="peak_schedule_global_toggle"
                                       wire:model.live="pricingRules.peak_schedule_enabled" style="width: 2.2rem; height: 1.1rem; cursor: pointer;">
                            </div>
                        </div>
                    </div>

                    {{-- White Background Per-Day Peak Schedule Table --}}
                    @if(filter_var(data_get($pricingRules, 'peak_schedule_enabled'), FILTER_VALIDATE_BOOLEAN))
                    <div class="card border border-secondary border-opacity-25 bg-white mb-4 shadow-sm" style="border-radius: 8px; overflow: hidden;">
                        <div class="card-header bg-light border-bottom py-2 px-3">
                            <h6 class="fw-bold mb-0 text-dark text-uppercase small" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                                <i class="far fa-clock me-1 text-primary"></i> Day-by-Day Peak Time Configuration
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light text-uppercase text-muted" style="font-size: 0.72rem;">
                                        <tr>
                                            <th class="ps-3 py-2">Day</th>
                                            <th class="text-center py-2">Peak Enabled</th>
                                            <th class="py-2">Starts</th>
                                            <th class="text-center py-2">to</th>
                                            <th class="pe-3 py-2">Ends</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $weekDaysList = [
                                                'monday'    => 'Monday',
                                                'tuesday'   => 'Tuesday',
                                                'wednesday' => 'Wednesday',
                                                'thursday'  => 'Thursday',
                                                'friday'    => 'Friday',
                                                'saturday'  => 'Saturday',
                                                'sunday'    => 'Sunday'
                                            ];
                                        @endphp
                                        @foreach($weekDaysList as $dayKey => $dayLabel)
                                        <tr>
                                            <td class="ps-3 fw-bold text-dark py-2">{{ $dayLabel }}</td>
                                            <td class="text-center py-2">
                                                <input class="form-check-input" type="checkbox" style="width: 1.15rem; height: 1.15rem; cursor: pointer;"
                                                       wire:model.live="pricingRules.peak_schedule.{{ $dayKey }}.enabled">
                                            </td>
                                            <td class="py-2">
                                                @php $isDayPeak = !empty(data_get($pricingRules, "peak_schedule.{$dayKey}.enabled")); @endphp
                                                <select class="form-select form-select-sm border-gray-300 shadow-sm"
                                                        style="max-width: 125px; font-size: 0.82rem;"
                                                        wire:model="pricingRules.peak_schedule.{{ $dayKey }}.start_time"
                                                        {{ !$isDayPeak ? 'disabled' : '' }}>
                                                    @for($h = 5; $h <= 23; $h++)
                                                        <option value="{{ sprintf('%02d', $h) }}:00">{{ \Carbon\Carbon::parse(sprintf('%02d', $h) . ':00')->format('g:i A') }}</option>
                                                    @endfor
                                                </select>
                                            </td>
                                            <td class="text-center text-muted small py-2">to</td>
                                            <td class="pe-3 py-2">
                                                <select class="form-select form-select-sm border-gray-300 shadow-sm"
                                                        style="max-width: 125px; font-size: 0.82rem;"
                                                        wire:model="pricingRules.peak_schedule.{{ $dayKey }}.end_time"
                                                        {{ !$isDayPeak ? 'disabled' : '' }}>
                                                    @for($h = 6; $h <= 23; $h++)
                                                        <option value="{{ sprintf('%02d', $h) }}:00">{{ \Carbon\Carbon::parse(sprintf('%02d', $h) . ':00')->format('g:i A') }}</option>
                                                    @endfor
                                                </select>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Peak Hour Price (LKR/hr)</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input type="number" class="form-control" wire:model="pricingRules.peak_price" placeholder="e.g. 3500">
                            </div>
                            <small class="text-muted">Applied during Peak Time Window</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Off-Peak Hour Price (LKR/hr)</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input type="number" class="form-control" wire:model="pricingRules.offpeak_price" placeholder="e.g. 2000">
                            </div>
                            <small class="text-muted">Applied outside Peak Time Window</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Weekend Price (LKR/hr)</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input type="number" class="form-control" wire:model="pricingRules.weekend_price" placeholder="e.g. 4000">
                            </div>
                            <small class="text-muted">Applied on Saturday & Sunday</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Advance Booking Discount (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" wire:model="pricingRules.advance_discount" placeholder="e.g. 10" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Book X Days in Advance</label>
                            <input type="number" class="form-control" wire:model="pricingRules.advance_days" placeholder="e.g. 3">
                            <small class="text-muted">Discount applies if booked this many days early</small>
                        </div>

                        {{-- Private Booking & Full Rental Options inside Pricing Modal --}}
                        <div class="col-md-12 mt-3">
                            <div class="card border-success border-opacity-25 bg-light shadow-sm">
                                <div class="card-header bg-success bg-opacity-10 fw-bold small text-success text-uppercase d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <i class="fas fa-user-lock me-1"></i> Private Booking & Full Rental Options
                                    </div>
                                    <div class="form-check form-switch m-0 text-lowercase">
                                        <input class="form-check-input" type="checkbox" id="private_booking_enabled_modal" wire:model.live="pricingRules.private_booking_enabled">
                                        <label class="form-check-label fw-bold text-dark text-capitalize" for="private_booking_enabled_modal">
                                            Enable Private Booking
                                        </label>
                                    </div>
                                </div>
                                @if($pricingRules['private_booking_enabled'] ?? false)
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Private Pricing Type *</label>
                                            <select class="form-select form-select-sm" wire:model.live="pricingRules.private_booking_pricing_mode">
                                                <option value="flat_total">Fixed private total for the whole selected range</option>
                                                                                                <option value="per_hour">Private hourly rate multiplied by selected duration</option>
                                                <option value="normal_total">Use normal calculated slot total</option>
                                                <option value="normal_multiplier">Normal calculated slot total multiplied by private multiplier</option>
                                            </select>
                                        </div>

                                        @php $pMode = $pricingRules['private_booking_pricing_mode'] ?? 'flat_total'; @endphp

                                        @if(in_array($pMode, ['flat_total', 'per_hour', 'hourly_flat']))
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">
                                                {{ in_array($pMode, ['per_hour', 'hourly_flat']) ? 'Private Hourly Rate (LKR/hr) *' : 'Private Flat Rate Price (LKR) *' }}
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white">LKR</span>
                                                <input type="number" class="form-control form-control-sm" wire:model="pricingRules.private_booking_price" min="0" step="0.01" placeholder="{{ in_array($pMode, ['per_hour', 'hourly_flat']) ? 'e.g. 5000' : 'e.g. 15000' }}">
                                            </div>
                                            <small class="text-muted">{{ in_array($pMode, ['per_hour', 'hourly_flat']) ? 'Private rate charged per hour' : 'Total flat rate for entire private rental' }}</small>
                                        </div>
                                        @endif

                                        @if($pMode === 'normal_multiplier')
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Private Price Multiplier *</label>
                                            <input type="number" class="form-control form-control-sm" wire:model="pricingRules.private_booking_price_multiplier" step="0.1" min="1.0" placeholder="1.5">
                                            <small class="text-muted">Multiplied against normal calculated slot total (e.g. 1.5x)</small>
                                        </div>
                                        @endif

                                         <div class="col-md-6">
                                             <label class="form-label fw-semibold small">Minimum Booking Slots (Hours) *</label>
                                             <div class="input-group input-group-sm">
                                                 <input type="number" class="form-control form-control-sm" wire:model="pricingRules.private_booking_min_slots" min="1" max="24" placeholder="3">
                                                 <span class="input-group-text bg-light text-muted">Slots / Hrs</span>
                                             </div>
                                             <small class="text-info"><i class="fas fa-info-circle me-1"></i>Saved as {{ ((int)($pricingRules['private_booking_min_slots'] ?? 3)) * 60 }} min in table (`private_booking_min_duration_minutes`).</small>
                                         </div>

                                         <div class="col-md-3">
                                             <label class="form-label fw-semibold small">Request Limit Type *</label>
                                             <select class="form-select form-select-sm" wire:model="private_request_limit_type">
                                                 <option value="percentage">Percentage (%)</option>
                                                 <option value="fixed">Fixed Amount (LKR)</option>
                                                 <option value="unlimited">Unlimited</option>
                                             </select>
                                         </div>

                                         <div class="col-md-3">
                                             <label class="form-label fw-semibold small">Request Limit Value *</label>
                                             <div class="input-group input-group-sm">
                                                 <input type="number" class="form-control form-control-sm" wire:model="private_request_limit_value" min="0" step="0.01" placeholder="50.00">
                                                 <span class="input-group-text bg-white">{{ $private_request_limit_type === 'percentage' ? '%' : 'LKR' }}</span>
                                             </div>
                                         </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @error('pricingRules.peak_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    @error('pricingRules.advance_discount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" wire:click="closePricingModal">Cancel</button>
                    <button type="button" class="btn btn-success" wire:click="savePricingRules" wire:loading.attr="disabled">
                        <span wire:loading wire:target="savePricingRules" class="spinner-border spinner-border-sm me-1"></span>
                        Save Pricing Rules
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Slot Blocking Modal --}}
    @if($showSlotBlockModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-ban me-2"></i>Blocked Slots — {{ $slotBlockSportName }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeSlotBlockModal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Blocked slots appear as <strong>Unavailable</strong> in the booking calendar and cannot be booked from the mobile app.</p>

                    {{-- Add New Block --}}
                    <div class="card border-warning mb-4">
                        <div class="card-header bg-warning bg-opacity-10 fw-semibold small text-uppercase">
                            <i class="fas fa-plus me-1"></i> Add Blocked Slot
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small">Date *</label>
                                    <input type="date" class="form-control form-control-sm" wire:model="slotBlockDate">
                                    @error('slotBlockDate') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Start Time *</label>
                                    <select class="form-select form-select-sm" wire:model="slotBlockTime">
                                        @for($h = 6; $h <= 22; $h++)
                                        <option value="{{ sprintf('%02d', $h) }}:00:00">{{ sprintf('%02d', $h) }}:00</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">End Time *</label>
                                    <select class="form-select form-select-sm" wire:model="slotBlockEndTime">
                                        @for($h = 7; $h <= 23; $h++)
                                        <option value="{{ sprintf('%02d', $h) }}:00:00">{{ sprintf('%02d', $h) }}:00</option>
                                        @endfor
                                    </select>
                                    @error('slotBlockEndTime') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Court *</label>
                                    <input type="text" class="form-control form-control-sm" wire:model="slotBlockCourt" placeholder="1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Reason *</label>
                                    <select class="form-select form-select-sm" wire:model="slotBlockReason">
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Private Event">Private Event</option>
                                        <option value="Tournament">Tournament</option>
                                        <option value="Staff Training">Staff Training</option>
                                        <option value="Unavailable">Unavailable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-warning text-white" wire:click="addSlotBlock" wire:loading.attr="disabled">
                                    <span wire:loading wire:target="addSlotBlock" class="spinner-border spinner-border-sm me-1"></span>
                                    <i class="fas fa-plus me-1"></i>Block This Slot
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Existing Blocks --}}
                    <h6 class="fw-semibold small text-uppercase text-muted mb-2">Current Blocked Slots</h6>
                    @if(count($existingBlocks) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Time Range</th>
                                    <th>Court</th>
                                    <th>Reason</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($existingBlocks as $block)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($block['date'])->format('D, M d Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse('2000-01-01 ' . $block['time'])->format('g:i A') }}
                                        @if(!empty($block['end_time']))
                                        – {{ \Carbon\Carbon::parse('2000-01-01 ' . $block['end_time'])->format('g:i A') }}
                                        @endif
                                    </td>
                                    <td>Court {{ $block['court'] }}</td>
                                    <td><span class="badge bg-warning text-dark">{{ $block['reason'] }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger py-0 px-1"
                                            wire:click="removeSlotBlock('{{ $block['date'] }}', '{{ $block['time'] }}', '{{ $block['court'] }}')"
                                            title="Remove block">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-3 small">
                        <i class="fas fa-check-circle me-1 text-success"></i>No slots blocked for this sport.
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="closeSlotBlockModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Sport Modal -->
    <div class="modal fade" id="editSportModal" tabindex="-1" aria-labelledby="editSportModalLabel" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editSportModalLabel">Edit Sport</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form wire:submit.prevent="updateSport">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Game Name*</label>
                                <input type="text" class="form-control" wire:model="game_name" disabled>
                                @error('game_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Game Type*</label>
                                <select class="form-select" wire:model="game_type" required>
                                    <option value="" disabled>Select game type</option>
                                    <option value="Indoor">Indoor</option>
                                    <option value="Outdoor">Outdoor</option>
                                </select>
                                @error('game_type') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Rate Type*</label>
                                <select class="form-select" wire:model="rate_type" required>
                                    <option value="" disabled>Select rate type</option>
                                    <option value="Per hour">Per hour</option>
                                    <option value="Per person">Per person</option>
                                    <option value="Per session">Per session</option>
                                    <option value="Per day">Per day</option>
                                </select>
                                @error('rate_type') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price (LKR)*</label>
                                <input type="number" class="form-control" wire:model="price" min="0" step="0.01"
                                    required>
                                @error('price') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Maximum Courts*</label>
                                <input type="number" class="form-control" wire:model="maximum_court" min="1" required>
                                @error('maximum_court') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status*</label>
                                <select class="form-select" wire:model="status" required>
                                    <option value="" disabled>Select status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                                @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>


                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" wire:model="description" rows="2"></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            {{-- Payment Policy Customization --}}
                            <div class="col-md-12">
                                <div class="card border-primary border-opacity-25 bg-light shadow-sm">
                                    <div class="card-header bg-primary bg-opacity-10 fw-bold small text-primary text-uppercase d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-credit-card me-1"></i> Payment Policy Customization
                                        </div>
                                        @if(!$this->isOnlinePaymentEnabled)
                                        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Online Payment Disabled at Venue Level</span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        @if(!$this->isOnlinePaymentEnabled)
                                        <div class="alert alert-warning py-2 mb-3 small d-flex align-items-center">
                                            <i class="fas fa-info-circle me-2 fa-lg text-warning"></i>
                                            <div>
                                                Online payment is currently <strong>disabled</strong> in your venue settings (<a href="{{ route('staff.setting') }}?section=payments" class="fw-bold text-dark text-decoration-underline" target="_blank">Venue Settings -&gt; Payment Options</a>). Online payment options are locked unless online payment is enabled for the venue.
                                            </div>
                                        </div>
                                        @endif

                                        <div class="row g-3">
                                            <div class="col-md-{{ in_array($booking_payment_mode_override, ['advance_only', 'advance_or_full', 'partial']) ? '6' : '12' }}">
                                                <label class="form-label fw-semibold small">Payment Requirement Mode *</label>
                                                <select class="form-select form-select-sm" wire:model.live="booking_payment_mode_override" {{ !$this->isOnlinePaymentEnabled ? 'disabled' : '' }}>
                                                    <option value="">Use venue default</option>
                                                    <option value="no_payment">No online payment required</option>
                                                    <option value="advance_only">Pay advance to book</option>
                                                    <option value="full_only">Pay full amount to book</option>
                                                    <option value="advance_or_full">Allow advance or full payment</option>
                                                </select>
                                            </div>

                                            @if(in_array($booking_payment_mode_override, ['advance_only', 'advance_or_full', 'partial']))
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold small">Deposit Type *</label>
                                                <select class="form-select form-select-sm" wire:model="advance_payment_type_override" {{ !$this->isOnlinePaymentEnabled ? 'disabled' : '' }}>
                                                    <option value="percentage">Percentage (%)</option>
                                                    <option value="fixed">Fixed Amount (LKR)</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold small">Deposit Value *</label>
                                                <input type="number" class="form-control form-control-sm" wire:model="advance_payment_value_override" min="0" step="0.01" placeholder="e.g. 20" {{ !$this->isOnlinePaymentEnabled ? 'disabled' : '' }}>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Capacity / Pool Hourly Limit Customization --}}
                            <div class="col-md-12">
                                <div class="card border-info border-opacity-25 bg-light shadow-sm">
                                    <div class="card-header bg-info bg-opacity-10 fw-bold small text-info text-uppercase d-flex justify-content-between align-items-center py-2">
                                        <div>
                                            <i class="fas fa-users me-1"></i> Hourly Swimmer / Person Access Limit (Pools & Sports)
                                        </div>
                                        <div class="form-check form-switch m-0 text-lowercase">
                                            <input class="form-check-input" type="checkbox" id="capacity_limit_enabled_edit" wire:model.live="capacity_limit_enabled">
                                            <label class="form-check-label fw-bold text-dark text-capitalize" for="capacity_limit_enabled_edit">
                                                Enable Limit
                                            </label>
                                        </div>
                                    </div>
                                    @if($capacity_limit_enabled)
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Max Persons / Swimmers Allowed Per Hour *</label>
                                                <input type="number" class="form-control form-control-sm" wire:model="max_persons_per_hour" min="1" placeholder="e.g. 10">
                                                <small class="text-muted">Set capacity limit for pool entries and court slots</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Private Booking Options Customization --}}
                            <div class="col-md-12">
                                <div class="card border-success border-opacity-25 bg-light">
                                    <div class="card-header bg-success bg-opacity-10 fw-bold small text-success text-uppercase">
                                        <i class="fas fa-user-lock me-1"></i> Private Booking & Full Rental Customization
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="private_booking_enabled_edit" wire:model.live="private_booking_enabled">
                                                    <label class="form-check-label fw-semibold" for="private_booking_enabled_edit">
                                                        Enable Private Booking Rental
                                                    </label>
                                                </div>
                                            </div>

                                            @if($private_booking_enabled)
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Private Pricing Mode</label>
                                                <select class="form-select form-select-sm" wire:model.live="private_booking_pricing_mode">
                                                    <option value="flat_total">Fixed private total for the whole selected range</option>
                                                    <option value="per_hour">Private hourly rate multiplied by selected duration</option>
                                                    <option value="normal_total">Use normal calculated slot total</option>
                                                    <option value="normal_multiplier">Normal calculated slot total multiplied by private multiplier</option>
                                                </select>
                                            </div>

                                            @if(in_array($private_booking_pricing_mode, ['flat_total', 'per_hour', 'hourly_flat']))
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">
                                                    {{ in_array($private_booking_pricing_mode, ['per_hour', 'hourly_flat']) ? 'Private Hourly Rate (LKR/hr)' : 'Private Booking Price (LKR)' }}
                                                </label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">LKR</span>
                                                    <input type="number" class="form-control" wire:model="private_booking_price" min="0" step="0.01" placeholder="{{ in_array($private_booking_pricing_mode, ['per_hour', 'hourly_flat']) ? 'e.g. 5000' : 'e.g. 10000' }}">
                                                </div>
                                                <small class="text-muted">{{ in_array($private_booking_pricing_mode, ['per_hour', 'hourly_flat']) ? 'Private rate charged per hour' : 'Total flat rate for entire private rental' }}</small>
                                            </div>
                                            @endif

                                            @if($private_booking_pricing_mode === 'normal_multiplier')
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Price Multiplier</label>
                                                <input type="number" class="form-control form-control-sm" wire:model="private_booking_price_multiplier" step="0.1" min="1.0" placeholder="1.5">
                                                <small class="text-muted">Multiplied against normal calculated slot total (e.g. 1.5x)</small>
                                            </div>
                                            @endif

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Minimum Duration (Minutes)</label>
                                                <input type="number" class="form-control form-control-sm" wire:model="private_booking_min_duration_minutes" placeholder="180">
                                                <small class="text-muted">Default 180 min (3 Hours)</small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="deleteSportBtn" wire:click="confirmDelete('{{ $editSportId }}')">
                            Delete
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading wire:target="updateSport" class="spinner-border spinner-border-sm"
                                role="status" aria-hidden="true"></span>
                            Update Sport
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pool Admission Tickets Modal -->
    @if($showPoolModal)
    <div class="fixed inset-0 z-[1055] flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in overflow-y-auto" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.65); display: flex; align-items: center; justify-content: center; z-index: 1055;">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full my-auto overflow-hidden border border-slate-100 flex flex-col max-h-[88vh]" style="background: white; border-radius: 1.5rem; max-width: 42rem; width: 100%; overflow: hidden;">
            <div class="bg-primary text-white p-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-swimming-pool fa-lg"></i>
                    <h5 class="m-0 fw-bold">Pool Ticket Tiers & Admission Types</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" wire:click="closePoolModal"></button>
            </div>

            <div class="p-4 overflow-y-auto" style="max-height: 75vh;">
                @if(session()->has('pool_modal_message'))
                    <div class="alert alert-success py-2 small fw-semibold mb-3">
                        <i class="fas fa-check-circle me-1"></i> {{ session('pool_modal_message') }}
                    </div>
                @endif

                <!-- Pool Private Request & Session Settings -->
                <div class="card border-0 bg-light p-3 rounded-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold small text-primary uppercase m-0"><i class="fas fa-sliders-h me-1"></i> Pool Booking Limits & Private Settings</h6>
                        <button type="button" wire:click="updatePoolSettings" class="btn btn-sm btn-success fw-bold py-1 px-3">
                            <i class="fas fa-save me-1"></i> Save Settings
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold mb-1">Limit Type *</label>
                            <select class="form-select form-select-sm" wire:model="private_request_limit_type">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (LKR)</option>
                                <option value="unlimited">Unlimited</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold mb-1">Limit Value *</label>
                            <div class="input-group input-group-sm">
                                <input type="number" wire:model="private_request_limit_value" class="form-control form-control-sm" min="0" step="0.01" placeholder="50.00">
                                <span class="input-group-text bg-white">{{ $private_request_limit_type === 'percentage' ? '%' : 'LKR' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 bg-light p-3 rounded-3 mb-4">
                    <h6 class="fw-bold small text-primary uppercase mb-2"><i class="fas fa-plus-circle me-1"></i> Add New Admission Ticket Tier</h6>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold mb-1">Ticket Name *</label>
                            <input type="text" wire:model="newAdmissionName" class="form-control form-control-sm" placeholder="e.g. Adult Pass, Child Pass">
                            @error('newAdmissionName') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">Price (LKR) *</label>
                            <input type="number" wire:model="newAdmissionPrice" class="form-control form-control-sm" placeholder="e.g. 2500" min="0" step="0.01">
                            @error('newAdmissionPrice') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <button type="button" wire:click="addPoolAdmissionType" class="btn btn-sm btn-primary w-100 fw-bold">
                                <i class="fas fa-plus me-1"></i> Add Tier
                            </button>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold small text-dark mb-2"><i class="fas fa-ticket-alt me-1"></i> Existing Ticket Categories ({{ count($poolAdmissionTypes) }})</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle border mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Category Name</th>
                                <th>Price (LKR)</th>
                                <th>Capacity Units</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($poolAdmissionTypes as $ticket)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $ticket['name'] }}</td>
                                    <td class="fw-bold text-success">Rs. {{ number_format($ticket['price'], 2) }}</td>
                                    <td><span class="badge bg-secondary">{{ $ticket['capacity_units'] }} Swimmer(s)</span></td>
                                    <td>
                                        <span class="badge {{ $ticket['is_active'] ? 'bg-success' : 'bg-danger' }}">
                                            {{ $ticket['is_active'] ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" wire:click="deletePoolAdmissionType({{ $ticket['id'] }})" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete Tier">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No custom ticket categories added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-3 bg-light border-top text-end">
                <button type="button" wire:click="closePoolModal" class="btn btn-sm btn-secondary fw-bold px-4">Close</button>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modals
        const addSportModalEl = document.getElementById('addSportModal');
        const editSportModalEl = document.getElementById('editSportModal');
        const addSportModal = new bootstrap.Modal(addSportModalEl);
        const editSportModal = new bootstrap.Modal(editSportModalEl);
        
        // Helper function to safely move focus out of modal
        function moveFocusOutOfModal() {
            const addBtn = document.getElementById('addSportButton');
            if (addBtn) {
                addBtn.focus();
            } else {
                document.body.focus();
            }
        }
        
        // Helper function to clean up modal artifacts
        function cleanupModalArtifacts() {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }

        // Add Sport Modal events
        window.addEventListener('showModal', () => {
            addSportModal.show();
        });

        window.addEventListener('hideModal', () => {
            moveFocusOutOfModal();
            addSportModal.hide();
        });

        addSportModalEl.addEventListener('hidden.bs.modal', () => {
            Livewire.dispatch('resetForm');
            cleanupModalArtifacts();
        });

        // Edit Sport Modal events
        window.addEventListener('showEditSportModal', () => {
            editSportModal.show();
        });

        window.addEventListener('hideEditSportModal', () => {
            moveFocusOutOfModal();
            editSportModal.hide();
        });

        editSportModalEl.addEventListener('hidden.bs.modal', () => {
            Livewire.dispatch('resetForm');
            cleanupModalArtifacts();
        });

        // Track pending delete confirmation
        let pendingDeleteId = null;

        // Delete confirmation with SweetAlert2
        window.addEventListener('showConfirmation', event => {
            pendingDeleteId = event.detail.id;
            
            // First move focus out of modal to prevent aria-hidden conflict
            moveFocusOutOfModal();
            
            // Then hide the modal
            editSportModal.hide();
        });
        
        // Wait for modal to be fully hidden before showing SweetAlert
        editSportModalEl.addEventListener('hidden.bs.modal', function onHiddenForDelete() {
            if (pendingDeleteId === null) return;
            
            const deleteId = pendingDeleteId;
            pendingDeleteId = null;
            
            // Clean up any remaining modal artifacts
            cleanupModalArtifacts();
            
            // Now show SweetAlert
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'animated shake',
                    confirmButton: 'btn btn-danger mx-1',
                    cancelButton: 'btn btn-secondary mx-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteConfirmed', { id: deleteId });
                } else {
                    // Reopen modal if user cancels
                    editSportModal.show();
                }
            });
        });

        // Handle successful deletion
        window.addEventListener('sportDeleted', () => {
            cleanupModalArtifacts();
            
            Swal.fire({
                title: 'Deleted!',
                text: 'The sport has been deleted.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    popup: 'animated fadeIn'
                }
            }).then(() => {
                moveFocusOutOfModal();
            });
        });

        // Handle deletion error
        window.addEventListener('deleteError', event => {
            Swal.fire({
                title: 'Error!',
                text: event.detail.message,
                icon: 'error',
                confirmButtonColor: '#3085d6',
                customClass: {
                    popup: 'animated shake',
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        });
    });
</script>
@endpush
@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #15803d 0%, #166534 100%);
        --available-color: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        --available-border: #bae6fd;
        --border-radius: 16px;
        --shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        --small-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
    }

    .card.booking-card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin-bottom: 24px;
        background-color: #ffffff;
        overflow: hidden;
    }

    .card-header.booking-header {
        background: var(--primary-gradient);
        color: white;
        padding: 20px 10px;
        font-size: 1.6rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .game-tabs {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 0 10px;
    }

    .game-tab {
        width: 100%;
        text-align: center;
        padding: 12px 30px;
        font-weight: 600;
        color: #166534;
        background: white;
        border-bottom: 3px solid #15803d;
    }

    .date-navigation {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        background: white;
        border-bottom: 1px solid #e2e8f0;
    }

    .date-btn {
        background: #f1f5f9;
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: #475569;
        transition: all 0.2s ease;
    }

    .date-btn:hover {
        background: #15803d;
        color: white;
    }

    .current-date {
        margin: 0 25px;
        font-size: 1.2rem;
        font-weight: 700;
        color: #1e293b;
    }

    .booking-calendar {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        table-layout: fixed;
    }

    .booking-calendar th {
        background: var(--primary-gradient);
        color: white;
        padding: 16px 15px;
        font-weight: 700;
        text-align: center;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .booking-calendar th.time-column {
        width: 180px;
    }

    .booking-calendar td {
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
        padding: 6px;
    }

    .pool-slot-container {
        background: #e0f2fe;
        border: 1.5px solid #38bdf8;
        border-radius: 12px;
        padding: 14px 18px;
        min-height: 72px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .booked-status-text {
        color: #1e3a8a;
        font-weight: 800;
        font-size: 0.88rem;
        letter-spacing: 0.5px;
    }

    .left-badge {
        background-color: #dcfce7 !important;
        color: #15803d !important;
        font-weight: 800;
        font-size: 0.78rem;
        padding: 4px 10px;
        border-radius: 12px;
    }

    .booking-pill-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 50px;
        padding: 4px 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .booking-pill-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border-color: #0284c7;
    }

    .start-pill-badge {
        background: #16a34a;
        color: white;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-pool-action {
        background-color: #0284c7 !important;
        border-color: #0284c7 !important;
        color: white !important;
        font-weight: 700;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.82rem;
    }

    .btn-pool-action:hover {
        background-color: #0369a1 !important;
        border-color: #0369a1 !important;
    }

    .past-slot-box {
        background: #f1f5f9;
        border: 1.5px dashed #cbd5e1;
        color: #94a3b8;
        font-weight: 700;
        text-align: center;
        border-radius: 8px;
        padding: 10px;
        cursor: not-allowed;
    }

    /* Modal styling matching Book Slot modal */
    .book-slot-modal .modal-header {
        background: linear-gradient(135deg, #15803d 0%, #166534 100%);
        color: white;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    .modal-body-content {
        background-color: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
    }

    .booking-info-pill {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
    }

    .info-val {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin-top: 2px;
    }

    .end-time-selection-box {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        margin-top: 12px;
    }

    .end-time-label {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        display: block;
    }

    .duration-select {
        border: 2px solid #22c55e !important;
        border-radius: 8px;
        font-weight: 700;
        color: #15803d;
        padding: 10px 14px;
        font-size: 0.98rem;
    }

    .end-time-help {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 8px;
        line-height: 1.35;
    }

    .btn-book-slot {
        background: #15803d !important;
        color: white !important;
        font-weight: 700;
        border-radius: 8px;
        padding: 10px 24px;
    }

    .btn-book-slot:hover {
        background: #166534 !important;
    }

    .ticket-counter-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .timer-display {
        font-size: 2.5rem;
        font-weight: 800;
        font-family: monospace;
        color: #15803d;
        background: #f0fdf4;
        padding: 10px 20px;
        border-radius: 12px;
        border: 2px solid #bbf7d0;
        display: inline-block;
    }
</style>
@endpush

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0 text-dark">
                <i class="fas fa-swimming-pool text-success me-2"></i>Swimming Pool Bookings
            </h2>
            <p class="text-muted mb-0">Manage pool session slots, ticket admissions, and bookings</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-success text-white fw-bold shadow-sm rounded-3 px-4 py-2" wire:click="openCreateModal()">
                <i class="fas fa-plus me-1"></i> New Pool Booking
            </button>
        </div>
    </div>

    @if(session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(!$pool)
    <div class="alert alert-warning rounded-3 shadow-sm p-4 text-center">
        <i class="fas fa-info-circle fa-2x mb-2 text-warning"></i>
        <h4>No Active Swimming Pool Found</h4>
        <p class="mb-0">Please create or enable a Swimming Pool in <a href="{{ route('staff.sports') }}" class="fw-bold">Sports Management</a> first.</p>
    </div>
    @else

    <!-- Timetable Booking System Card -->
    <div class="card booking-card">
        <div class="card-header booking-header">
            <h5 class="mb-0 text-white fw-bold">
                <i class="fas fa-trophy me-2"></i>Sports Booking System
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="game-tabs">
                <div class="game-tab active">
                    <i class="fas fa-swimming-pool me-2"></i> {{ $pool->name }}
                </div>
            </div>

            <!-- Date Navigator Bar -->
            <div class="date-navigation">
                <button class="btn date-btn" wire:click="prevDay">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="current-date position-relative" style="cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;" onclick="document.getElementById('datePickerInput').showPicker()">
                    <span>{{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}</span>
                    <i class="fas fa-calendar-alt text-muted" style="font-size: 1.1rem;"></i>
                    <input type="date" id="datePickerInput" wire:model.live="selectedDate" style="position: absolute; opacity: 0; top: 0; left: 0; height: 100%; width: 100%; cursor: pointer;">
                </div>
                <button class="btn date-btn" wire:click="nextDay">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- Timetable Matrix Grid -->
            <div class="table-responsive">
                <table class="booking-calendar">
                    <thead>
                        <tr>
                            <th class="time-column">TIME</th>
                            <th>MAIN POOL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $todayStr = \Carbon\Carbon::today()->format('Y-m-d');
                            $currentTimeStr = \Carbon\Carbon::now()->format('H:i:s');
                            $maxCapacity = $pool->capacity ?: 25;
                        @endphp
                        @foreach($this->timeSlots as $slot)
                        @php
                            $slotStart = $slot['start_time'];
                            $slotEnd = $slot['end_time'];
                            $isPast = ($selectedDate < $todayStr) || ($selectedDate === $todayStr && $slotEnd <= $currentTimeStr);

                            $slotBookings = $bookings->filter(function($b) use ($slotStart, $slotEnd) {
                                if (strtolower($b->status) !== 'confirmed') {
                                    return false;
                                }
                                if ($b->occurrence) {
                                    $occStart = \Carbon\Carbon::parse($b->occurrence->start_time)->format('H:i:s');
                                    $occEnd = \Carbon\Carbon::parse($b->occurrence->end_time)->format('H:i:s');
                                    return ($occStart <= $slotStart && $occEnd > $slotStart);
                                }
                                $createdTime = \Carbon\Carbon::parse($b->created_at)->format('H:i:s');
                                return ($createdTime >= $slotStart && $createdTime < $slotEnd);
                            });

                            $totalBookedTickets = $slotBookings->sum('total_admissions');
                            $remainingCapacity = max(0, $maxCapacity - $totalBookedTickets);
                            $isFull = ($remainingCapacity <= 0);
                        @endphp


                        <tr>
                            <td class="text-center fw-bold text-muted align-middle" style="font-size: 0.92rem; background: #fafafa;">
                                {{ $slot['label'] }}
                            </td>
                            <td>
                                @if($isPast && $slotBookings->isEmpty())
                                    <!-- Past Slot -->
                                    <div class="past-slot-box">
                                        <i class="fas fa-lock me-1"></i> Time Past
                                    </div>
                                @else
                                     <!-- Pool Slot Row Container -->
                                     <div class="pool-slot-container" style="cursor: pointer;" @if(!$isPast && !$isFull) wire:click="openCreateModalWithSlot('{{ $slotStart }}', '{{ $slotEnd }}')" @endif>
                                         <!-- Capacity count indicator -->
                                         <span class="booked-status-text">
                                             {{ $totalBookedTickets }}/{{ $maxCapacity }} BOOKED
                                         </span>
                                         <span class="left-badge">
                                             {{ $remainingCapacity }} LEFT
                                         </span>

                                         <!-- Guest Booking Pills -->
                                         @foreach($slotBookings as $b)
                                         <div class="booking-pill-btn" onclick="event.stopPropagation();" wire:click="openDetailModal({{ $b->id }})">
                                             <span>{{ $b->user_name }} ({{ $b->total_admissions }}p)</span>
                                             <span class="start-pill-badge" onclick="event.stopPropagation(); startPoolTimerPill('{{ addslashes($b->user_name) }}', '{{ $b->total_admissions }}', '{{ $b->booking_reference }}', '{{ $b->occurrence->start_time ?? '' }}', '{{ $b->id }}')">
                                                 <i class="fas fa-play" style="font-size: 0.65rem;"></i> Start
                                             </span>
                                         </div>
                                         @endforeach

                                         @if($isFull)
                                         <span class="badge bg-danger ms-auto px-3 py-2 fw-bold fs-6">Fully Booked</span>
                                         @endif
                                     </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @endif

    <!-- Cancellation Confirmation / Continuous Booking Choice Modal -->
    @if($showCancelModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header {{ $isMultiSlotBooking ? 'bg-warning text-dark' : 'bg-danger text-white' }} py-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center mb-0">
                        @if($isMultiSlotBooking)
                            <i class="fas fa-exclamation-triangle me-2"></i>Cancel Continuous Booking
                        @else
                            <i class="fas fa-trash-alt me-2"></i>Confirm Booking Cancellation
                        @endif
                    </h5>
                    <button type="button" class="btn-close {{ $isMultiSlotBooking ? '' : 'btn-close-white' }}" wire:click="closeCancelModal()"></button>
                </div>
                <div class="modal-body p-4">
                    @if($isMultiSlotBooking)
                    <div class="alert alert-warning border-warning mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        This booking spans across multiple continuous time slots. Would you like to cancel the entire overall booking or cancel only this specific slot?
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="cancelTypeRadio" id="cancelOverallRadio" value="overall" wire:model.live="cancelType">
                            <label class="form-check-label fw-bold text-dark" for="cancelOverallRadio">
                                <i class="fas fa-layer-group text-danger me-1"></i> Cancel Overall Booking
                            </label>
                            <div class="text-muted small ms-4">Cancels all continuous slots associated with this booking reference.</div>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="cancelTypeRadio" id="cancelIndividualRadio" value="individual" wire:model.live="cancelType">
                            <label class="form-check-label fw-bold text-dark" for="cancelIndividualRadio">
                                <i class="fas fa-clock text-primary me-1"></i> Cancel This Slot Only (Individually)
                            </label>
                            <div class="text-muted small ms-4">Cancels only the slot for {{ $cancelSlotStart }}, preserving the remaining session.</div>
                        </div>
                    </div>
                    @else
                    <p class="text-dark fs-6 mb-3">
                        Are you sure you want to cancel this booking? This action will release the ticket capacity back to available.
                    </p>
                    @endif
                </div>
                <div class="modal-footer bg-light border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light border px-4 py-2 fw-bold rounded-3" wire:click="closeCancelModal()">Keep Booking</button>
                    <button type="button" class="btn btn-danger px-4 py-2 fw-bold shadow-sm rounded-3" wire:click="confirmCancelBooking()">
                        <i class="fas fa-check me-1"></i> Confirm Cancellation
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Collect Payment Modal (Matching BookingsManagement.php) -->
    @if($showPaymentCollectModal && $paymentCollectBooking)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.65); z-index: 1080;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <h6 class="modal-title fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="fas fa-money-bill-wave me-2"></i>Collect Payment
                    </h6>
                    <button type="button" class="btn-close btn-close-white" wire:click="closePaymentCollectModal()"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="bg-light rounded-3 p-3 mb-3" style="font-size: 0.85rem;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Booking Ref:</span>
                            <span class="fw-bold text-dark">{{ $paymentCollectBooking->booking_reference }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Player Name:</span>
                            <span class="fw-bold text-dark">{{ $paymentCollectBooking->user_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Phone Number:</span>
                            <span class="fw-bold text-dark">{{ $paymentCollectBooking->user_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Admissions:</span>
                            <span class="badge bg-secondary">{{ $paymentCollectBooking->total_admissions }} Tickets</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Payment Status:</span>
                            <span class="badge bg-warning text-dark">
                                {{ ucfirst($paymentCollectBooking->payment_status ?: 'Pending') }}
                            </span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-dark fw-bold">Total Amount Due:</span>
                            <span class="fw-bold text-success fs-5">Rs. {{ number_format($paymentCollectBooking->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 0.5px;">Select Payment Method</label>
                        <div class="d-flex gap-2">
                            <div class="flex-fill text-center p-3 rounded-3 border {{ $paymentCollectMethod === 'cash' ? 'border-warning bg-warning bg-opacity-10 fw-bold' : 'bg-light' }}" 
                                 style="cursor: pointer; font-size: 0.85rem;" wire:click="$set('paymentCollectMethod', 'cash')">
                                <i class="fas fa-money-bill-wave d-block mb-1 text-success" style="font-size: 1.3rem;"></i>
                                Cash
                            </div>
                            <div class="flex-fill text-center p-3 rounded-3 border {{ $paymentCollectMethod === 'card' ? 'border-warning bg-warning bg-opacity-10 fw-bold' : 'bg-light' }}" 
                                 style="cursor: pointer; font-size: 0.85rem;" wire:click="$set('paymentCollectMethod', 'card')">
                                <i class="fas fa-credit-card d-block mb-1 text-primary" style="font-size: 1.3rem;"></i>
                                Card
                            </div>
                            <div class="flex-fill text-center p-3 rounded-3 border {{ $paymentCollectMethod === 'transfer' ? 'border-warning bg-warning bg-opacity-10 fw-bold' : 'bg-light' }}" 
                                 style="cursor: pointer; font-size: 0.85rem;" wire:click="$set('paymentCollectMethod', 'transfer')">
                                <i class="fas fa-exchange-alt d-block mb-1 text-info" style="font-size: 1.3rem;"></i>
                                Transfer
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light border px-3 py-2 fw-bold rounded-3" wire:click="closePaymentCollectModal()">Cancel</button>
                    <button type="button" class="btn btn-warning text-dark fw-bold px-4 py-2 shadow-sm rounded-3" wire:click="collectBookingPayment()">
                        <i class="fas fa-check-circle me-1"></i> Collect Payment (Rs. {{ number_format($paymentCollectBooking->total_amount, 2) }})
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Booking Detail & Live Playing Timer Modal -->
    @if($showDetailModal && $selectedBooking)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header bg-gradient bg-success text-white py-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="fas fa-stopwatch me-2"></i>Pool Booking Details & Timer
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeDetailModal()"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="alert alert-light border rounded-3 p-3 text-start mb-3" style="font-size: 0.9rem;">
                        <div class="d-flex justify-content-between mb-1">
                            <strong class="text-muted">Reference:</strong>
                            <span class="fw-bold text-success">{{ $selectedBooking->booking_reference ?: 'POOL-' . $selectedBooking->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <strong class="text-muted">Player Name:</strong>
                            <span class="fw-bold text-dark">{{ $selectedBooking->user_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <strong class="text-muted">Phone Number:</strong>
                            <span class="fw-bold text-dark">{{ $selectedBooking->user_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <strong class="text-muted">Total Tickets:</strong>
                            <span class="badge bg-secondary">{{ $selectedBooking->total_admissions }} Tickets</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <strong class="text-muted">Total Price:</strong>
                            <strong class="text-dark">Rs. {{ number_format($selectedBooking->total_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong class="text-muted">Payment Status:</strong>
                            <span class="badge {{ strtolower($selectedBooking->payment_status) === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($selectedBooking->payment_status ?: 'Pending') }}
                            </span>
                        </div>
                    </div>

                    <!-- Live Session Timer Display -->
                    <div class="mb-3">
                        <div class="text-muted small fw-bold mb-1"><i class="far fa-clock me-1"></i>POOL SESSION TIMER</div>
                        <div class="timer-display" id="poolSessionTimerDisplay">01:00:00</div>
                    </div>

                    <!-- Timer Controls -->
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <button type="button" class="btn btn-success fw-bold px-3" onclick="startPoolTimer()">
                            <i class="fas fa-play me-1"></i> Start Timer Window
                        </button>
                        <button type="button" class="btn btn-warning fw-bold px-3 text-dark" onclick="pausePoolTimer()">
                            <i class="fas fa-pause me-1"></i> Pause
                        </button>
                        <button type="button" class="btn btn-secondary fw-bold px-3" onclick="resetPoolTimer()">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center gap-2 pt-3 border-top">
                        @if(strtolower($selectedBooking->payment_status) !== 'paid')
                        <button type="button" class="btn btn-warning text-dark fw-bold px-3 py-2 rounded-3" wire:click="markAsPaid({{ $selectedBooking->id }})">
                            <i class="fas fa-money-bill-wave me-1"></i> Mark as Paid (Rs. {{ number_format($selectedBooking->total_amount, 2) }})
                        </button>
                        @else
                        <span class="badge bg-success py-2 px-3 fs-6"><i class="fas fa-check-circle me-1"></i> Fully Paid</span>
                        @endif

                        <button type="button" class="btn btn-danger fw-bold px-3 py-2 rounded-3" wire:click="initiateCancelBooking({{ $selectedBooking->id }})">
                            <i class="fas fa-times me-1"></i> Cancel Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Create Pool Slot Booking Modal (Matching Image Design) -->
    @if($showCreateModal)
    <div class="modal fade show d-block book-slot-modal" tabindex="-1" style="background: rgba(0,0,0,0.6);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-white d-flex align-items-center">
                        <i class="fas fa-plus-circle me-2"></i>Book Slot
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeCreateModal()"></button>
                </div>
                <form wire:submit.prevent="saveBooking">
                    <div class="modal-body p-4">
                        <div class="modal-body-content p-3 mb-3">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="booking-info-pill">
                                        <span class="info-label">GAME</span>
                                        <span class="info-val text-truncate">{{ $pool->name ?? 'Swimming Pool' }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="booking-info-pill">
                                        <span class="info-label">COURT / POOL</span>
                                        <span class="info-val">Main Pool</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="booking-info-pill">
                                        <span class="info-label">DATE</span>
                                        <span class="info-val">{{ $selectedDate }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="booking-info-pill">
                                        <span class="info-label">START TIME</span>
                                        <span class="info-val text-success">{{ $slotStartTime }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- End Time Duration Dropdown -->
                            <div class="end-time-selection-box">
                                <label class="end-time-label">
                                    <i class="far fa-clock text-success me-1"></i> End Time (Select Duration)
                                </label>
                                <select class="form-select duration-select" wire:model="selectedEndTime">
                                    @forelse($endTimeOptions as $opt)
                                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                    @empty
                                        <option value="17:00:00">5:00 PM (1 hr)</option>
                                    @endforelse
                                </select>
                                <div class="end-time-help">
                                    Select end time to book multiple consecutive slots at once. Already booked or blocked slots in between will be skipped automatically.
                                </div>
                            </div>
                        </div>

                        <!-- Player Name & Phone Number -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Player Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-2" wire:model="userName" placeholder="Enter player name" required>
                                @error('userName') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control border-2" wire:model="userNumber" placeholder="Enter phone number" required>
                                @error('userNumber') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Permanent & Private Switches -->
                        <div class="d-flex align-items-center gap-4 mb-3 p-3 bg-light rounded-3 border">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="permanentCheck" wire:model="permanent">
                                <label class="form-check-label fw-bold text-dark" for="permanentCheck">Permanent Booking</label>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="isPrivateCheck" wire:model="isPrivate">
                                <label class="form-check-label fw-bold text-dark" for="isPrivateCheck">
                                    <i class="fas fa-lock text-warning me-1"></i> Private Booking
                                </label>
                            </div>
                        </div>

                        <!-- Ticket Tiers Counter -->
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <h6 class="fw-bold text-dark mb-2"><i class="fas fa-ticket-alt text-success me-1"></i>Admission Tickets</h6>
                            @forelse($admissionTypes as $adm)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom last-border-0">
                                <div>
                                    <span class="fw-bold text-dark">{{ $adm->name }}</span>
                                    <small class="text-muted d-block">Rs. {{ number_format($adm->price, 2) }}</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-danger ticket-counter-btn" wire:click="decrementTicket({{ $adm->id }})">-</button>
                                    <span class="fw-bold fs-6 px-2">{{ $ticketQuantities[$adm->id] ?? 0 }}</span>
                                    <button type="button" class="btn btn-outline-success ticket-counter-btn" wire:click="incrementTicket({{ $adm->id }})">+</button>
                                </div>
                            </div>
                            @empty
                            <div class="text-muted small">General Entry</div>
                            @endforelse
                        </div>

                        <!-- Price Summary -->
                        <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-dark fw-bold">Total Admissions:</span>
                                    <strong class="fs-6 text-success ms-2">{{ $this->totalAdmissions }} Tickets</strong>
                                </div>
                                <div>
                                    <span class="text-dark fw-bold">Total Amount:</span>
                                    <strong class="fs-5 text-success ms-2">Rs. {{ number_format($this->totalAmount, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light border px-4 py-2 fw-bold text-secondary rounded-3" wire:click="closeCreateModal()">Cancel</button>
                        <button type="submit" class="btn btn-book-slot px-4 py-2 fw-bold shadow-sm">
                            <i class="fas fa-check me-2"></i>Book Slot
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    let timerInterval = null;
    let secondsLeft = 3600;

    // Called on every tick by popup window timer_window.html
    window.updateMainTimerDisplay = function(remainingSecs, timerId, isRunning) {
        secondsLeft = remainingSecs;
        const el = document.getElementById('poolSessionTimerDisplay');
        if (el) {
            el.textContent = formatTimerText(remainingSecs);
        }
    };

    // Called when Force Complete button is clicked inside popup window
    window.forceCompleteFromPopup = function(timerId, bookingId) {
        const el = document.getElementById('poolSessionTimerDisplay');
        if (el) {
            el.textContent = '00:00:00';
        }
        if (window.Livewire) {
            @this.completeBooking(bookingId);
        }
    };

    function openTimerWindow(playerName, admissions, bookingRef, startTime, numericId) {
        const popup = window.open(
            '/staff/timer_window.html',
            'timer_' + (numericId || bookingRef || 'POOL'),
            'width=500,height=520,resizable=yes,scrollbars=no'
        );

        if (popup) {
            const payload = {
                timerId: 'timer_' + (numericId || bookingRef || 'POOL'),
                totalDuration: 3600,
                remaining: secondsLeft,
                isRunning: true,
                player: (playerName || 'Pool Swimmer') + ' (' + (admissions || 1) + 'p)',
                game: 'Swimming Pool',
                startTimeDisplay: startTime || '16:00:00',
                bookingId: numericId || bookingRef || 'POOL',
                court: 'Main Pool'
            };

            popup.onload = () => {
                if (popup.receiveTimerData) {
                    popup.receiveTimerData(payload);
                }
            };

            setTimeout(() => {
                if (popup.receiveTimerData) {
                    popup.receiveTimerData(payload);
                }
            }, 400);
        }
    }

    function startPoolTimerPill(playerName, admissions, bookingRef, startTime, numericId) {
        openTimerWindow(playerName, admissions, bookingRef, startTime, numericId);
    }

    function formatTimerText(totalSecs) {
        const hrs = String(Math.floor(totalSecs / 3600)).padStart(2, '0');
        const mins = String(Math.floor((totalSecs % 3600) / 60)).padStart(2, '0');
        const secs = String(totalSecs % 60).padStart(2, '0');
        return `${hrs}:${mins}:${secs}`;
    }

    function startPoolTimer() {
        const playerName = "{{ $selectedBooking->user_name ?? 'Pool Swimmer' }}";
        const admissions = "{{ $selectedBooking->total_admissions ?? '1' }}";
        const bookingRef = "{{ $selectedBooking->booking_reference ?? 'POOL' }}";
        const startTime = "{{ $selectedBooking->occurrence->start_time ?? '16:00:00' }}";
        const numericId = "{{ $selectedBooking->id ?? 0 }}";

        if (!timerInterval) {
            timerInterval = setInterval(() => {
                if (secondsLeft > 0) {
                    secondsLeft--;
                    const el = document.getElementById('poolSessionTimerDisplay');
                    if (el) el.textContent = formatTimerText(secondsLeft);
                } else {
                    clearInterval(timerInterval);
                    timerInterval = null;
                }
            }, 1000);
        }

        openTimerWindow(playerName, admissions, bookingRef, startTime, numericId);
    }

    function pausePoolTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }

    function resetPoolTimer() {
        pausePoolTimer();
        secondsLeft = 3600;
        const el = document.getElementById('poolSessionTimerDisplay');
        if (el) el.textContent = formatTimerText(secondsLeft);
    }
</script>
@endpush

@push('styles')
<style>
    :root {
        --primary-color: #6aea66;
        --primary-gradient: linear-gradient(135deg, #195f0b 0%, #198754 100%);
        --secondary-color: #6c757d;
        --cricket-color: #4CAF50;
        --badminton-color: #2196F3;
        --tennis-color: #FF9800;
        --squash-color: #9C27B0;
        --basketball-color: #F44336;
        --available-color: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        --available-border: #bae6fd;
        --available-hover: linear-gradient(135deg, #0ea5e9 0%, #02c733 100%);
        --booked-color: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
        --booked-border: #fca5a5;
        --pending-color: linear-gradient(135deg, #fffbeb 0%, #fed7aa 100%);
        --pending-border: #fdba74;
        --background-color: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        --card-bg: #ffffff;
        --border-radius: 16px;
        --small-radius: 12px;
        --shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        --small-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
    }

    body {
        font-family: 'Inter', sans-serif;
    }

    .container-fluid {
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

    /* Stat Cards Styles */
    .stat-card {
        background: var(--card-bg);
        border-radius: var(--border-radius);
        border: none;
        box-shadow: var(--small-shadow);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow);
    }

    .stat-card .card-title {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-card h2 {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: all 0.3s ease;
    }

    .stat-icon.icon-sports { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #16a34a; }
    .stat-icon.icon-bookings { background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); color: #0284c7; }
    .stat-icon.icon-revenue { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
    .stat-icon.icon-canceled { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }

    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
    }

    /* Slot Availability Styles */
    .slot-availability-section {
        background: var(--card-bg);
        border-radius: var(--border-radius);
        box-shadow: var(--small-shadow);
    }

    .venue-card {
        background: var(--card-bg);
        border-radius: var(--border-radius);
        border: 1px solid #f3f4f6;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .venue-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateY(-4px);
        border-color: #e5e7eb;
    }

    .venue-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .venue-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .venue-icon.football { background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); color: #2e7d32; }
    .venue-icon.badminton { background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); color: #ef6c00; }
    .venue-icon.cricket { background: linear-gradient(135deg, #fce4ec 0%, #f8bbd9 100%); color: #c2185b; }
    .venue-icon.tennis { background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); color: #1565c0; }
    .venue-icon.pools { background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%); color: #00838f; }
    .venue-icon.basketball { background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; }

    .venue-badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
    }

    .venue-badge.success { background: #dcfce7; color: #16a34a; }
    .venue-badge.warning { background: #fef3c7; color: #d97706; }
    .venue-badge.info { background: #e0f2fe; color: #0284c7; }
    .venue-badge.danger { background: #fee2e2; color: #dc2626; }
    .venue-badge.secondary { background: #f3f4f6; color: #6b7280; }

    .maintenance-notice {
        font-size: 12px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .slots-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .time-slot {
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        min-width: 85px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .time-slot.available {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        color: #0284c7;
        border-color: #bae6fd;
    }

    .time-slot.available:hover {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        transform: scale(1.05);
    }

    .time-slot.booked {
        background: linear-gradient(135deg, #f5f5f5 0%, #e5e5e5 100%);
        color: #737373;
        border-color: #d4d4d4;
        cursor: not-allowed;
    }

    .time-slot.reserved {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #b45309;
        border-color: #fcd34d;
    }

    .time-slot.maintenance {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #6b7280;
        border-color: #d1d5db;
        cursor: not-allowed;
    }

    .time-slot-label {
        display: block;
        font-size: 11px;
        opacity: 0.8;
        margin-top: 2px;
    }

    /* Calendar Styles */
    .calendar-widget {
        background: var(--card-bg);
        border-radius: var(--border-radius);
        box-shadow: var(--small-shadow);
        padding: 20px;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .calendar-nav-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        background: #f3f4f6;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .calendar-nav-btn:hover {
        background: #e5e7eb;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }

    .calendar-day-header {
        text-align: center;
        font-size: 11px;
        font-weight: 600;
        color: #9ca3af;
        padding: 8px 0;
    }

    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #374151;
    }

    .calendar-day:hover {
        background: #f3f4f6;
    }

    .calendar-day.other-month {
        color: #d1d5db;
    }

    .calendar-day.today {
        background: #16a34a;
        color: white;
    }

    .calendar-day.selected {
        background: #0ea5e9;
        color: white;
    }

    /* Upcoming Sidebar */
    .upcoming-sidebar {
        background: var(--card-bg);
        border-radius: var(--border-radius);
        box-shadow: var(--small-shadow);
    }

    .upcoming-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .upcoming-item:last-child {
        border-bottom: none;
    }

    .upcoming-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
    }

    .upcoming-avatar.blue { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1d4ed8; }
    .upcoming-avatar.orange { background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%); color: #c2410c; }
    .upcoming-avatar.green { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #15803d; }
    .upcoming-avatar.purple { background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); color: #7c3aed; }

    .upcoming-details h6 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 2px;
        color: #1f2937;
    }

    .upcoming-details span {
        font-size: 12px;
        color: #6b7280;
    }
</style>

@endpush
<div class="container-fluid">
    <!-- Dashboard Content (Default) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Dashboard</h1>
            <p class="text-muted">Manage, track, and optimize your indoor ground bookings with ease.</p>
        </div>
        <div>
            <a class="btn btn-success me-2" href="{{ route('staff.bookings') }}">
                <i class="fas fa-plus me-1"></i>
                Add Booking
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Total Sports</h6>
                            <h2 class="mb-0">{{ $sportsCount }}</h2>
                            <small class="text-success fw-medium">
                                <i class="fas fa-arrow-up me-1"></i>
                                Increased from last month
                            </small>
                        </div>
                        <div class="stat-icon icon-sports">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Total Bookings</h6>
                            <h2 class="mb-0">{{ $bookingsCount }}</h2>
                            <small class="text-success fw-medium">
                                <i class="fas fa-arrow-up me-1"></i>
                                Increased from last month
                            </small>
                        </div>
                        <div class="stat-icon icon-bookings">
                            <i class="fas fa-play-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Total Revenue</h6>
                            <h2 class="mb-0">{{$todaybookingRevenue}}</h2>
                            <small class="text-danger fw-medium">
                                <i class="fas fa-arrow-down me-1"></i>
                                Decreased from last month
                            </small>
                        </div>
                        <div class="stat-icon icon-revenue">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Canceled Bookings</h6>
                            <h2 class="mb-0">{{$cancelledBookingsCount}}</h2>
                            <small class="text-warning fw-medium">
                                On Discussion
                            </small>
                        </div>
                        <div class="stat-icon icon-canceled">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Upcoming booking View modal -->
    <div wire:ignore.self class="modal fade" id="bookingDetailsModal" tabindex="-1"
        aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="bookingDetailsModalLabel">Booking Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if($selectedBooking)
                    <div class="card border-0">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-4 fw-semibold text-secondary">Game</div>
                                <div class="col-8">{{ $selectedBooking->game_name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4 fw-semibold text-secondary">User</div>
                                <div class="col-8">{{ $selectedBooking->user_name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4 fw-semibold text-secondary">Court</div>
                                <div class="col-8">{{ $selectedBooking->court_number }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4 fw-semibold text-secondary">Status</div>
                                <div class="col-8">
                                    <span
                                        class="badge {{ $selectedBooking->status === 'Confirmed' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $selectedBooking->status }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4 fw-semibold text-secondary">Time</div>
                                <div class="col-8">{{
                                    \Carbon\Carbon::parse($selectedBooking->start_time)->format('M-d-Y') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4 fw-semibold text-secondary">Time</div>
                                <div class="col-8">
                                    {{ \Carbon\Carbon::parse($selectedBooking->start_time)->format(' h:i A') }} -
                                    {{ \Carbon\Carbon::parse($selectedBooking->end_time)->format('h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center text-muted py-3">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Loading details...
                    </div>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Slot Availability Section -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="slot-availability-section p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Slot Availability</h5>
                        <p class="text-muted mb-0 small">
                            Showing status for {{ \Carbon\Carbon::parse($slotAvailabilityDate)->format('l, M d') }}
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <select wire:model.live="selectedSportFilter" class="form-select form-select-sm" style="width: auto; min-width: 150px;">
                            <option value="all">All Sports</option>
                            @foreach($this->sportsList as $sport)
                                <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('staff.bookings') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-1"></i> New
                        </a>
                    </div>
                </div>

                <!-- Venue/Sport Cards -->
                <div class="venue-list" wire:key="slots-{{ $slotAvailabilityDate }}">
                    @forelse($sportsWithSlots as $index => $sportData)
                        <div class="venue-card p-3 mb-3" wire:key="sport-{{ $sportData['id'] }}-{{ $slotAvailabilityDate }}">
                            <div class="venue-header">
                                <div class="venue-icon {{ strtolower(str_replace(' ', '', $sportData['name'])) }}">
                                    @if(str_contains(strtolower($sportData['name']), 'football'))
                                        <i class="fas fa-futbol"></i>
                                    @elseif(str_contains(strtolower($sportData['name']), 'badminton'))
                                        <i class="fas fa-table-tennis"></i>
                                    @elseif(str_contains(strtolower($sportData['name']), 'cricket'))
                                        <i class="fas fa-baseball-ball"></i>
                                    @elseif(str_contains(strtolower($sportData['name']), 'tennis'))
                                        <i class="fas fa-table-tennis"></i>
                                    @elseif(str_contains(strtolower($sportData['name']), 'basketball'))
                                        <i class="fas fa-basketball-ball"></i>
                                    @elseif(str_contains(strtolower($sportData['name']), 'pool'))
                                        <i class="fas fa-swimming-pool"></i>
                                    @else
                                        <i class="fas fa-running"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-semibold">{{ $sportData['name'] }}</h6>
                                    @if(isset($sportData['is_pool']) && $sportData['is_pool'])
                                        <small class="text-muted">{{ $sportData['maxCourts'] }} Max Swimmers/Hr</small>
                                    @elseif($sportData['maxCourts'] > 1)
                                        <small class="text-muted">{{ $sportData['maxCourts'] }} Courts</small>
                                    @endif
                                </div>
                                @if($sportData['badge'])
                                    <span class="venue-badge {{ $sportData['badgeType'] }}">
                                        {{ $sportData['badge'] }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="slots-container">
                                @if(isset($sportData['isClosed']) && $sportData['isClosed'])
                                    <div class="text-center py-3 w-100">
                                        <i class="fas fa-store-slash text-muted me-2"></i>
                                        <span class="text-muted">Venue is closed on this day</span>
                                    </div>
                                @elseif(count($sportData['slots']) === 0)
                                    <div class="text-center py-3 w-100">
                                        <i class="fas fa-clock text-muted me-2"></i>
                                        <span class="text-muted">No available slots remaining for today</span>
                                    </div>
                                @else
                                    @foreach($sportData['slots'] as $slot)
                                        <div class="time-slot {{ $slot['status'] }}" 
                                             @if($slot['status'] === 'available')
                                                wire:click="openAddBookingModal('{{ $sportData['id'] }}', '{{ $sportData['name'] }}', {{ $slot['hour'] }})"
                                                style="cursor: pointer;"
                                             @endif
                                             title="{{ ucfirst($slot['status']) }}">
                                            <span>{{ $slot['time'] }}</span>
                                            <span class="time-slot-label">{{ ucfirst($slot['status']) }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No sports available for this venue.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Legend -->
                <div class="d-flex justify-content-center gap-4 mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center gap-2">
                        <div class="time-slot available" style="min-width: auto; padding: 6px 10px; cursor: default;">
                            <small>Available</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="time-slot booked" style="min-width: auto; padding: 6px 10px; cursor: default;">
                            <small>Booked</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar & Upcoming Sidebar -->
        <div class="col-lg-4">
            <!-- Calendar Widget -->
            <div class="calendar-widget mb-4">
                <div class="calendar-header">
                    <button wire:click="previousMonth" class="calendar-nav-btn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h6 class="fw-bold mb-0">
                        {{ \Carbon\Carbon::createFromDate($calendarYear, $calendarMonth, 1)->format('F Y') }}
                    </h6>
                    <button wire:click="nextMonth" class="calendar-nav-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <div class="calendar-grid">
                    <!-- Day Headers -->
                    @foreach(['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $dayName)
                        <div class="calendar-day-header">{{ $dayName }}</div>
                    @endforeach

                    <!-- Calendar Days -->
                    @foreach($calendarDays as $day)
                        <div class="calendar-day {{ !$day['current'] ? 'other-month' : '' }} {{ $day['today'] ? 'today' : '' }} {{ $day['selected'] ? 'selected' : '' }}"
                             @if($day['current'])
                                wire:click="selectCalendarDate({{ $day['day'] }})"
                             @endif>
                            {{ $day['day'] }}
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Upcoming Bookings Sidebar -->
            <div class="upcoming-sidebar p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Upcoming</h6>
                    <a href="{{ route('staff.bookings') }}" class="text-primary small text-decoration-none">View All</a>
                </div>

                @forelse($todayUpcomingBookings as $index => $booking)
                    @php
                        $colors = ['blue', 'orange', 'green', 'purple'];
                        $color = $colors[$index % count($colors)];
                        $initials = collect(explode(' ', $booking->user_name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('');
                    @endphp
                    <div class="upcoming-item">
                        <div class="upcoming-avatar {{ $color }}">
                            {{ $initials }}
                        </div>
                        <div class="upcoming-details flex-grow-1">
                            <h6>{{ $booking->user_name }}</h6>
                            <span>{{ $booking->game_name }} • {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} {{ \Carbon\Carbon::parse($booking->booking_date)->isToday() ? 'Today' : \Carbon\Carbon::parse($booking->booking_date)->format('M d') }}</span>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                        <p class="text-muted small mb-0">No upcoming bookings today</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Add Booking Modal -->
    @if($showAddBookingModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header bg-success text-white" style="border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-calendar-plus me-2"></i>Add Booking
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeAddBookingModal"></button>
                </div>
                <div class="modal-body p-4">
                    @if(session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Booking Info Summary -->
                    <div class="bg-light rounded-3 p-3 mb-4">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Sport</small>
                                <strong>{{ $bookingFormSportName }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Date</small>
                                <strong>{{ \Carbon\Carbon::parse($bookingFormDate)->format('M d, Y') }}</strong>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <small class="text-muted d-block">Time Slot</small>
                                <strong class="text-success">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::createFromTime($bookingFormHour, 0)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::createFromTime($bookingFormHour + 1, 0)->format('h:i A') }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <form wire:submit.prevent="saveQuickBooking">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user me-1 text-muted"></i>Customer Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('bookingFormUserName') is-invalid @enderror" 
                                   wire:model="bookingFormUserName" 
                                   placeholder="Enter customer name">
                            @error('bookingFormUserName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-phone me-1 text-muted"></i>Phone Number <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control @error('bookingFormUserNumber') is-invalid @enderror" 
                                   wire:model="bookingFormUserNumber" 
                                   placeholder="Enter phone number">
                            @error('bookingFormUserNumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                @if(isset($bookingFormSportId) && str_starts_with((string)$bookingFormSportId, 'pool_'))
                                    <i class="fas fa-users me-1 text-muted"></i>Number of Swimmers / Admissions <span class="text-danger">*</span>
                                @else
                                    <i class="fas fa-hashtag me-1 text-muted"></i>Court Number <span class="text-danger">*</span>
                                @endif
                            </label>
                            <input type="number" class="form-control @error('bookingFormCourtNumber') is-invalid @enderror" 
                                   wire:model="bookingFormCourtNumber" 
                                   min="1" value="1">
                            @error('bookingFormCourtNumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-sticky-note me-1 text-muted"></i>Notes (Optional)
                            </label>
                            <textarea class="form-control" wire:model="bookingFormNotes" 
                                      rows="2" placeholder="Any special requests or notes..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light flex-grow-1" wire:click="closeAddBookingModal">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success flex-grow-1">
                                <i class="fas fa-check me-1"></i>Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Booking Notifications Modal -->
    <div class="modal fade h-50" id="bookingNotificationsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-end"
            style="position: fixed; top: 80px; right: 20px; margin: 0; width: 420px; ">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <!-- Header -->
                <div class="modal-header py-3"
                    style="background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%); border-radius: 12px 12px 0 0;">
                    <h6 class="modal-title mb-0 text-white fw-semibold">
                        <i class="fas fa-bell me-2"></i>
                        @if($markedAsRead)
                        <span class="animate-fade-in">Notifications Cleared</span>
                        @else
                        Recent Bookings ({{ $recentBookings->count() }})
                        @endif
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-0" wire:poll.5s>
                    @if($markedAsRead)
                    <div class="text-center py-4 animate-fade-in">
                        <div class="d-inline-block p-3 rounded-circle bg-success bg-opacity-10 mb-3">
                            <i class="fas fa-check-circle text-success fa-3x"></i>
                        </div>
                        <h5 class="fw-semibold">All notifications marked as read</h5>
                        <p class="text-muted small">You can close this window</p>
                    </div>
                    @else
                    <div style="max-height: 60vh; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            @foreach($recentBookings as $booking)
                            <li class="list-group-item border-0 py-3"
                                style="border-bottom: 1px solid #f0f0f0 !important;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="me-3">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="avatar-sm me-2">
                                                <div
                                                    class="avatar-title bg-light text-primary rounded-circle fw-semibold">
                                                    {{ substr($booking->user_name, 0, 1) }}
                                                </div>
                                            </div>
                                            <strong>{{ $booking->user_name }}</strong>
                                        </div>
                                        <div class="text-muted small ms-4 ps-2">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="far fa-phone me-2"></i>
                                                {{ $booking->user_number }}
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="far fa-clock me-2"></i>
                                                {{ $booking->created_at->format('h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary py-2 px-2 fw-normal">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $booking->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="modal-footer py-3" style="background-color: #f8f9fa; border-radius: 0 0 12px 12px;">
                    @if(!$markedAsRead)
                    <small class="text-muted me-auto">
                        <i class="fas fa-info-circle me-1"></i>
                        Showing {{ $recentBookings->count() }} of {{ $totalBookingsCount }}
                    </small>
                    @endif
                    <button wire:click="markAllAsRead"
                        class="btn btn-sm {{ $markedAsRead ? 'btn-success' : 'btn-primary' }} px-3 py-2 fw-semibold"
                        style="border-radius: 8px;">
                        <i class="fas fa-check-circle me-1"></i>
                        {{ $markedAsRead ? 'Done' : 'Mark All Read' }}
                    </button>
                </div>
            </div>
        </div>
    </div>


<script>
    // Calendar keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            Livewire.dispatch('previousMonth');
        } else if (e.key === 'ArrowRight') {
            Livewire.dispatch('nextMonth');
        }
    });
</script>
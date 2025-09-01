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
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title opacity-75">Total Sports</h6>
                            <h2 class="mb-0">{{ $bookingsCount }}</h2>
                            <small class="opacity-75">
                                <i class="fas fa-arrow-up me-1"></i>
                                Increased from last month
                            </small>
                        </div>
                        <div class="stat-icon">
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
                            <h2 class="mb-0">{{ $sportsCount }}</h2>
                            <small class="text-muted">
                                <i class="fas fa-arrow-up text-success me-1"></i>
                                Increased from last month
                            </small>
                        </div>
                        <div class="stat-icon">
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
                            <small class="text-muted">
                                <i class="fas fa-arrow-down text-warning me-1"></i>
                                Decreased from last month
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-map-marked-alt"></i>
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
                            <h6 class="card-title text-muted">Canceled Booking</h6>
                            <h2 class="mb-0">{{$cancelledBookingsCount}}</h2>
                            <small class="text-muted">
                                On Discussion
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Analytics -->
    <div class="row">
        <!-- Upcoming Bookings -->
        <div class="col-lg-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Upcoming Bookings</h5>
                    <a href="{{ route('staff.bookings') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> New
                    </a>
                </div>
                <div class="card-body">
                    @if($upcomingBookings && $upcomingBookings->count())
                    @foreach($upcomingBookings as $booking)
                    <div class="upcoming-booking mb-3">
                        <div class="d-flex align-items-center border rounded p-3 shadow-sm">
                            <div class="booking-icon me-3">
                                <i class="fas fa-futbol text-success fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $booking->game_name }} - {{ $booking->court_number }}</h6>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y,') }}
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                </small>
                                <br>
                                <small class="text-muted">Player: {{ $booking->user_name }}</small>
                            </div>
                            <div>
                                <button wire:click="showBookingDetails({{ $booking->booking_id }})"
                                    class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#bookingDetailsModal">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted">No upcoming bookings.</p>
                    @endif
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

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const dummyData = {
    "2025-07-24": {
      "Cricket": ["booked", "available", "available", "booked", "booked", "available"],
      "Football": ["available", "available", "booked", "booked", "available", "booked"],
      "Badminton": ["booked", "booked", "available", "available", "available", "booked"]
    },
    "2025-07-25": {
      "Cricket": ["available", "booked", "booked", "available", "available", "available"],
      "Football": ["booked", "booked", "available", "booked", "available", "booked"],
      "Badminton": ["available", "available", "available", "booked", "booked", "available"]
    }
  };

  const timeSlots = ["12AM - 1AM", "1AM - 2AM", "2AM - 3AM", "3AM - 4AM", "4AM - 5AM", "5AM - 6AM"];

  function renderTable(date) {
    const tableBody = document.getElementById("booking-table-body");
    const dateData = dummyData[date];
    tableBody.innerHTML = "";

    if (!dateData) {
      tableBody.innerHTML = `<tr><td colspan="${timeSlots.length + 1}" class="text-center">No bookings for selected date.</td></tr>`;
      return;
    }

    for (const [game, slots] of Object.entries(dateData)) {
      const row = document.createElement("tr");
      row.innerHTML += `<td class="game-name">${game}</td>`;
      slots.forEach(slot => {
        const className = slot === "booked" ? "booked" : "available";
        row.innerHTML += `<td class="${className}">${slot.charAt(0).toUpperCase() + slot.slice(1)}</td>`;
      });
      tableBody.appendChild(row);
    }
  }

  document.getElementById("date-picker").addEventListener("change", (e) => {
    const selectedDate = e.target.value;
    document.getElementById("selected-date").innerText = selectedDate;
    renderTable(selectedDate);
  });

  // Default load: today's date
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("date-picker").value = today;
  renderTable(today);
</script>
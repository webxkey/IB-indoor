<x-guest-layout>
    <div class="pt-4 bg-gray-100">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div>
                <x-authentication-card-logo />
            </div>

            <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">
                {!! $terms !!}
            </div>
        </div>
    </div>
</x-guest-layout>

<style>
    :root {
        --primary-color: #198754;
        --secondary-color: #6c757d;
        --cricket-color: #4CAF50;
        --badminton-color: #2196F3;
        --tennis-color: #FF9800;
        --squash-color: #9C27B0;
        --basketball-color: #F44336;
        --available-color: #e8f5e9;
        --booked-color: #ffebee;
    }

    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .card-header {
        background-color: #358f50;
        color: white;
        border-bottom: none;
        padding: 1.5rem;
        border-radius: 1rem 1rem 0 0 !important;
    }

    .game-tabs {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .game-tab {
        padding: 12px 20px;
        cursor: pointer;
        font-weight: 500;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
        color: #495057;
        flex-grow: 1;
        text-align: center;
    }

    .game-tab:hover {
        background-color: #f8f9fa;
    }

    .game-tab.active {
        border-bottom: 3px solid var(--primary-color);
        color: var(--primary-color);
        font-weight: 600;
    }

    .booking-calendar {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        table-layout: fixed;
    }

    .booking-calendar th,
    .booking-calendar td {
        border: 1px solid #dee2e6;
        text-align: center;
        vertical-align: middle;
        padding: 12px;
        font-size: 0.95rem;
        transition: background-color 0.2s ease-in-out;
    }

    .booking-calendar th {
        background-color: #e9ecef;
        font-weight: 700;
        color: #343a40;
        border-bottom: 2px solid #dee2e6;
        padding: 15px 10px;
        text-align: center;
        white-space: nowrap;
    }

    .booking-calendar .time-column {
        font-weight: 600;
        background-color: #f8f9fa;
        width: 20%;
        text-align: center;
    }

    .time-slot {
        position: relative;
        height: 100%;
        padding: 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .available {
        background-color: var(--available-color);
        border-left: 4px solid #4CAF50;
    }

    .available:hover {
        background-color: #d4edda;
    }

    .booked {
        background-color: var(--booked-color);
        border-left: 4px solid #F44336;
    }

    .booked:hover {
        background-color: #f8d7da;
    }

    .booking-info {
        margin-top: 2px;
        font-size: 0.75rem;
        line-height: 1.3;
    }

    .date-navigation {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
    }

    .current-date {
        font-weight: 600;
        font-size: 1.2rem;
        color: #495057;
    }

    .date-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .timer-display {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s;
    }

    .timer-running {
        color: #dc3545;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }

    .modal-timer {
        font-size: 3.5rem;
        font-family: 'Courier New', monospace;
        font-weight: bold;
        margin: 20px 0;
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    @media (max-width: 768px) {
        .game-tabs {
            justify-content: center;
            overflow-x: auto;
            white-space: nowrap;
            display: block;
            padding-bottom: 10px;
        }

        .game-tab {
            display: inline-block;
            min-width: 120px;
        }

        .booking-calendar th,
        .booking-calendar td {
            padding: 8px 5px;
            font-size: 0.8rem;
        }

        .modal-timer {
            font-size: 2.5rem;
        }
    }
</style>

<div>
    <div class="container-fluid py-4">
        <!-- Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-calendar-alt me-2"></i>Game Booking System
                </h5>
            </div>
            <div class="card-body">
                <!-- Game Tabs -->
                <div class="game-tabs">
                    @forelse ($games as $game)
                        @php
                            $icon = match(strtolower($game['name'])) {
                                'cricket' => 'fas fa-baseball-ball',
                                'badminton' => 'fas fa-table-tennis',
                                'tennis' => 'fas fa-table-tennis',
                                'squash' => 'fas fa-running',
                                'basketball' => 'fas fa-basketball-ball',
                                default => 'fas fa-gamepad',
                            };
                        @endphp
                        <div class="game-tab" data-game="{{ strtolower($game['name']) }}">
                            <i class="{{ $icon }} me-2"></i>{{ ucfirst($game['name']) }}
                        </div>
                    @empty
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No games available.
                        </div>
                    @endforelse
                </div>

                <!-- Date Navigation -->
                <div class="date-navigation">
                    <button class="btn btn-outline-secondary date-btn" id="prevDay">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="current-date" id="currentDate">{{ now()->format('F j, Y') }}</div>
                    <button class="btn btn-outline-secondary date-btn" id="nextDay">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <!-- Calendar -->
                <div class="table-responsive">
                    <table class="booking-calendar">
                        <thead>
                            <tr></tr>
                        </thead>
                        <tbody id="calendarBody">
                            <tr>
                                <td colspan="100%" class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle me-2"></i>Book Slot
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form wire:submit.prevent="addBooking">
                        <div class="modal-body">
                            <!-- Booking Info -->
                            <div class="card bg-light mb-3">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Game:</strong> <span id="modalGame">-</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Court:</strong> <span id="modalCourt">-</span>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <strong>Date:</strong> <span id="modalDate">-</span>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <strong>Time:</strong> <span id="modalTime">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="mb-3">
                                <label class="form-label">Player Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('playerName') is-invalid @enderror" 
                                       wire:model="playerName" 
                                       placeholder="Enter player name">
                                @error('playerName') 
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('phoneNumber') is-invalid @enderror" 
                                       wire:model="phoneNumber" 
                                       placeholder="Enter phone number">
                                @error('phoneNumber') 
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                            <option value="Confirmed">Confirmed</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                            <option value="No-Show">No-Show</option>
                                        </select>
                                        @error('status') 
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" wire:model="permanent">
                                            <label class="form-check-label">Permanent Booking</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          wire:model="notes" 
                                          rows="3" 
                                          placeholder="Additional notes..."></textarea>
                                @error('notes') 
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @error('general')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-check me-2"></i>Book Slot
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-2"></i>Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Timer Modal -->
        <div class="modal fade" id="timerModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-stopwatch me-2"></i>Timer Controls
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="alert alert-info mb-3">
                            <strong>Player:</strong> <span id="modalTimerPlayerName">-</span><br>
                            <strong>Start Time:</strong> <span id="modalTimerGameStartTime">-</span>
                        </div>
                        <div class="modal-timer" id="modalTimer">00:00:00</div>
                        <div class="d-flex justify-content-center gap-2">
                            <button id="startButton" class="btn btn-success" onclick="startTimer(activeModalTimerId)">
                                <i class="fas fa-play me-1"></i>Start
                            </button>
                            <button id="pauseButton" class="btn btn-warning" onclick="pauseTimer(activeModalTimerId)" disabled>
                                <i class="fas fa-pause me-1"></i>Pause
                            </button>
                            <button id="resetButton" class="btn btn-danger" onclick="resetTimer(activeModalTimerId)">
                                <i class="fas fa-redo me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- In your layout file -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data from backend
    const sportData = @json($bookingdetails);
    const gamesConfig = @json($games);
    
    // Global variables
    let currentGame = Object.keys(sportData)[0]?.toLowerCase() || 'badminton';
    let currentDate = new Date();
    let activeTimers = {};
    let activeModalTimerId = null;

    // Initialize
    initializeSystem();

    function initializeSystem() {
        console.log('Initializing booking system...');
        
        // Set active game tab
        if (Object.keys(sportData).length > 0) {
            currentGame = Object.keys(sportData)[0].toLowerCase();
        }
        
        const activeTab = document.querySelector(`.game-tab[data-game="${currentGame}"]`);
        if (activeTab) {
            activeTab.classList.add('active');
        }
        
        updateCalendar();
        setupEventListeners();
    }

    function setupEventListeners() {
        // Game tabs
        document.querySelectorAll('.game-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.game-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentGame = this.dataset.game;
                updateCalendar();
            });
        });

        // Date navigation
        const prevBtn = document.getElementById('prevDay');
        const nextBtn = document.getElementById('nextDay');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                currentDate.setDate(currentDate.getDate() - 1);
                updateCalendar();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                currentDate.setDate(currentDate.getDate() + 1);
                updateCalendar();
            });
        }

        // Available slot clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.time-slot.available')) {
                const slot = e.target.closest('.time-slot.available');
                openBookingModal(slot);
            }
        });

        // Booked slot clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.time-slot.booked')) {
                const slot = e.target.closest('.time-slot.booked');
                const timerId = slot.dataset.timerId;
                if (timerId) {
                    openTimerModal(timerId);
                }
            }
        });

        // Livewire events
        window.addEventListener('bookingCreated', function() {
            console.log('Booking created successfully');
            closeModal('bookingModal');
            updateCalendar();
        });

        window.addEventListener('closeModal', function() {
            closeModal('bookingModal');
        });
    }

    function openBookingModal(slot) {
        const time = slot.dataset.time;
        const display = slot.dataset.display;
        const court = slot.dataset.court;
        const date = formatDate(currentDate);
        const dateKey = formatDateKey(currentDate);

        // Update modal content
        document.getElementById('modalGame').textContent = currentGame.charAt(0).toUpperCase() + currentGame.slice(1);
        document.getElementById('modalDate').textContent = date;
        document.getElementById('modalTime').textContent = display;
        document.getElementById('modalCourt').textContent = court;

        // Set Livewire data
        @this.setSelectedBookingData({
            game: currentGame,
            dateKey: dateKey,
            time: time,
            court: court
        }).then(() => {
            const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
            modal.show();
        }).catch(error => {
            console.error('Error setting booking data:', error);
            alert('Error opening booking form. Please try again.');
        });
    }

    function openTimerModal(timerId) {
        activeModalTimerId = timerId;
        const timerState = activeTimers[timerId];
        
        if (timerState) {
            document.getElementById('modalTimerPlayerName').textContent = timerState.player;
            document.getElementById('modalTimerGameStartTime').textContent = timerState.startTimeDisplay;
            document.getElementById('modalTimer').textContent = formatTime(timerState.remaining);
            
            updateTimerButtons(timerState.isRunning);
        }
        
        const modal = new bootstrap.Modal(document.getElementById('timerModal'));
        modal.show();
    }

    function closeModal(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }
    }

    function updateCalendar() {
        console.log('Updating calendar for:', currentGame, formatDateKey(currentDate));
        
        const dateKey = formatDateKey(currentDate);
        const gameData = sportData[currentGame]?.[dateKey] || {};
        
        // Get number of courts
        let numberOfCourts = 3;
        const currentGameObject = gamesConfig.find(game => game.name.toLowerCase() === currentGame);
        if (currentGameObject && currentGameObject.court) {
            numberOfCourts = currentGameObject.court;
        }
        
        const courts = Array.from({ length: numberOfCourts }, (_, i) => `Court ${i + 1}`);
        
        // Clear existing timers
        for (const timerId in activeTimers) {
            if (activeTimers[timerId].intervalId) {
                clearInterval(activeTimers[timerId].intervalId);
                activeTimers[timerId].intervalId = null;
            }
        }
        
        // Build header
        let headerHtml = '<th class="time-column">Time</th>';
        courts.forEach(court => {
            headerHtml += `<th>${court}</th>`;
        });
        document.querySelector('.booking-calendar thead tr').innerHTML = headerHtml;
        
        // Build body
        let bodyHtml = '';
        const timeSlots = generateTimeSlots();
        
        timeSlots.forEach(slot => {
            bodyHtml += `<tr><td class="time-column">${slot.display}</td>`;
            
            courts.forEach(court => {
                const bookings = gameData[court] || {};
                const bookingInfo = bookings[slot.time24];
                
                if (bookingInfo) {
                    // Booked slot
                    const timerId = `${currentGame}-${dateKey}-${court.replace(/\s/g, '')}-${slot.time24.replace(/:/g, '-')}`;
                    const totalDuration = calculateDurationInSeconds(slot.time24, bookingInfo.end);
                    
                    if (!activeTimers[timerId]) {
                        activeTimers[timerId] = {
                            totalDuration: totalDuration,
                            remaining: totalDuration,
                            intervalId: null,
                            isRunning: false,
                            player: bookingInfo.player,
                            game: currentGame.charAt(0).toUpperCase() + currentGame.slice(1),
                            startTimeDisplay: slot.display,
                            popupWindow: null
                        };
                    }
                    
                    const displayTime = formatTime(activeTimers[timerId].remaining);
                    const statusBadge = getStatusBadge(bookingInfo.status);
                    const permanentBadge = bookingInfo.permanent ? '<span class="badge bg-primary ms-1">Permanent</span>' : '';
                    
                    bodyHtml += `
                        <td>
                            <div class="time-slot booked" data-timer-id="${timerId}">
                                <div class="booking-info">
                                    <strong>${bookingInfo.player}</strong><br>
                                    <small>${bookingInfo.phone}</small><br>
                                    ${statusBadge}${permanentBadge}
                                    <div id="${timerId}" class="timer-display mt-2">
                                        ${displayTime}
                                    </div>
                                </div>
                            </div>
                        </td>`;
                } else {
                    // Available slot
                    bodyHtml += `
                        <td>
                            <div class="time-slot available" 
                                 data-time="${slot.time24}" 
                                 data-display="${slot.display}" 
                                 data-court="${court}">
                                <i class="fas fa-plus-circle me-2"></i>Available
                            </div>
                        </td>`;
                }
            });
            
            bodyHtml += '</tr>';
        });
        
        document.getElementById('calendarBody').innerHTML = bodyHtml;
        document.getElementById('currentDate').textContent = formatDate(currentDate);
        
        // Restart running timers
        for (const timerId in activeTimers) {
            const timerState = activeTimers[timerId];
            if (timerState.isRunning && timerState.remaining > 0) {
                startTimer(timerId);
            }
        }
    }

    function generateTimeSlots() {
        const slots = [];
        for (let hour = 0; hour < 24; hour++) {
            const startHour = hour % 12 || 12;
            const ampm = hour < 12 ? 'AM' : 'PM';
            const nextHour24 = (hour + 1) % 24;
            const nextHour12 = nextHour24 % 12 || 12;
            const nextAmpm = nextHour24 < 12 ? 'AM' : 'PM';

            slots.push({
                time24: `${hour.toString().padStart(2, '0')}:00:00`,
                display: `${startHour}:00 ${ampm} - ${nextHour12}:00 ${nextAmpm}`
            });
        }
        return slots;
    }

    function formatDate(date) {
        return date.toLocaleDateString('en-US', { 
            month: 'long', 
            day: 'numeric', 
            year: 'numeric' 
        });
    }

    function formatDateKey(date) {
        return date.toISOString().split('T')[0];
    }

    function calculateDurationInSeconds(startTimeStr, endTimeStr) {
        const [startHour, startMinute] = startTimeStr.split(':').map(Number);
        const [endHour, endMinute] = endTimeStr.split(':').map(Number);

        const startDate = new Date(0, 0, 0, startHour, startMinute, 0);
        let endDate = new Date(0, 0, 0, endHour, endMinute, 0);

        if (endDate < startDate) {
            endDate.setDate(endDate.getDate() + 1);
        }

        return (endDate.getTime() - startDate.getTime()) / 1000;
    }

    function formatTime(seconds) {
        const hrs = Math.floor(seconds / 3600).toString().padStart(2, '0');
        const mins = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
        const secs = (seconds % 60).toString().padStart(2, '0');
        return `${hrs}:${mins}:${secs}`;
    }

    function getStatusBadge(status) {
        const badges = {
            'Confirmed': '<span class="badge bg-success">Confirmed</span>',
            'Pending': '<span class="badge bg-warning">Pending</span>',
            'Completed': '<span class="badge bg-info">Completed</span>',
            'Cancelled': '<span class="badge bg-danger">Cancelled</span>',
            'No-Show': '<span class="badge bg-secondary">No-Show</span>'
        };
        return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
    }

    // Timer functions
    function startTimer(timerIdToControl) {
        const timerState = activeTimers[timerIdToControl];
        if (!timerState || timerState.intervalId) return;

        timerState.isRunning = true;
        updateTimerButtons(true);

        timerState.intervalId = setInterval(() => {
            if (timerState.remaining > 0) {
                timerState.remaining--;
                updateTimerDisplay(timerIdToControl);
            } else {
                clearInterval(timerState.intervalId);
                timerState.intervalId = null;
                timerState.isRunning = false;
                updateTimerButtons(false);
                updateTimerDisplay(timerIdToControl);
            }
        }, 1000);
    }

    function pauseTimer(timerIdToControl) {
        const timerState = activeTimers[timerIdToControl];
        if (!timerState || !timerState.intervalId) return;

        clearInterval(timerState.intervalId);
        timerState.intervalId = null;
        timerState.isRunning = false;
        updateTimerButtons(false);
        updateTimerDisplay(timerIdToControl);
    }

    function resetTimer(timerIdToControl) {
        const timerState = activeTimers[timerIdToControl];
        if (!timerState) return;

        pauseTimer(timerIdToControl);
        timerState.remaining = timerState.totalDuration;
        timerState.isRunning = false;
        updateTimerDisplay(timerIdToControl);
    }

    function updateTimerDisplay(timerId) {
        const timerState = activeTimers[timerId];
        if (!timerState) return;

        const formattedTime = formatTime(timerState.remaining);

        // Update modal timer if active
        if (activeModalTimerId === timerId) {
            const modalTimer = document.getElementById('modalTimer');
            if (modalTimer) {
                modalTimer.textContent = formattedTime;
            }
        }

        // Update table timer
        const tableTimer = document.getElementById(timerId);
        if (tableTimer) {
            tableTimer.textContent = formattedTime;
            if (timerState.isRunning) {
                tableTimer.classList.add('timer-running');
            } else {
                tableTimer.classList.remove('timer-running');
            }
        }
    }

    function updateTimerButtons(isRunning) {
        const startBtn = document.getElementById('startButton');
        const pauseBtn = document.getElementById('pauseButton');
        
        if (startBtn && pauseBtn) {
            startBtn.disabled = isRunning;
            pauseBtn.disabled = !isRunning;
        }
    }

    // Global timer functions for onclick handlers
    window.startTimer = startTimer;
    window.pauseTimer = pauseTimer; 
    window.resetTimer = resetTimer;
});
</script>
@endpush
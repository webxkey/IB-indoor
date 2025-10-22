@push('styles')
<style>
    /* Booked slot styling */
    .booked-slot {
        @apply flex items-center justify-between p-2 bg-red-50 border border-red-200 rounded-lg;
    }

    .booked-slot .avatar {
        @apply w-8 h-8 flex items-center justify-center bg-red-200 rounded-full text-white font-semibold text-xs;
    }

    .booked-slot .user-details {
        @apply flex-1 ml-2;
    }

    .booked-slot .user-details p {
        @apply text-sm text-gray-700;
    }

    .booked-slot .user-details .sub-info {
        @apply text-xs text-gray-500;
    }

    .booked-slot .timer-section {
        @apply flex items-center space-x-2;
    }

    .booked-slot .timer-section .timer {
        @apply text-lg font-bold text-gray-800;
    }

    .booked-slot .permanent-badge {
        @apply inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full;
    }

    .booked-slot .cancel-icon {
        @apply material-icons text-red-500 cursor-pointer text-base;
    }

    /* Ensure available slot styling remains intact */
    .book-slot-btn {
        @apply w-full h-16 flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg text-gray-400 hover:border-green-500 hover:text-green-600 transition-all duration-300 group;
    }

    .book-slot-btn .material-icons {
        @apply mr-2 opacity-0 group-hover:opacity-100 transition-opacity;
    }


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
        padding:0;
    }

    .card.booking-card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin-bottom: 24px;
        background-color: var(--card-bg);
        transition: transform 0.3s ease;
        overflow: hidden;
    }

    .card.booking-card:hover {
        transform: translateY(-4px);
    }

    .card-header.booking-header {
        background: var(--primary-gradient);
        color: white;
        border-bottom: none;
        padding: 20px 10px;
        border-radius: 0;
        font-size: 1.2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        text-align: center;
        justify-content: center;
    }

    .card-header.booking-header h5 {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 700;
    }

    .card-header.booking-header i {
        margin-right: 15px;
        font-size: 2rem;
    }

    .game-tabs {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        overflow-x: auto;
        padding: 0 10px;
        margin: 0;
    }

    .game-tab {
        width: 100%;
        text-align: center;
        padding: 10px 30px;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        white-space: nowrap;
        font-weight: 500;
        color: #64748b;
        position: relative;
        background: transparent;
        border: none;
        font-size: 1rem;
    }

    .game-tab:hover {
        background: rgba(102, 126, 234, 0.05);
        transform: translateY(-2px);
    }

    .game-tab.active {
        background: white;
        color: #195f0b;
        border-bottom: 3px solid #195f0b;
        font-weight: 600;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
    }

    .game-tab i {
        margin-right: 10px;
        font-size: 1.2rem;
    }

    .date-navigation {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        background: white;
        border-bottom: 1px solid #e2e8f0;
        margin: 0;
    }

    .date-btn {
        background: #f1f5f9;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: var(--small-radius);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        color: #64748b;
        font-size: 1.1rem;
    }

    .date-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
    }

    .current-date {
        margin: 0 30px;
        font-size: 1.2rem;
        font-weight: 600;
        color: #1e293b;
    }

    .table-responsive {
        padding: 20px;
        background: white;
    }

    .booking-calendar {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--small-shadow);
        table-layout: fixed;
    }

    .booking-calendar th {
        background: var(--primary-gradient);
        color: white;
        padding: 20px 15px;
        font-weight: 600;
        text-align: center;
        font-size: 0.95rem;
        position: sticky;
        top: 0;
        z-index: 10;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .booking-calendar th:first-child {
        border-top-left-radius: var(--border-radius);
    }

    .booking-calendar th:last-child {
        border-top-right-radius: var(--border-radius);
    }

    .booking-calendar td {
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        height: 80px;
        padding: 0;
    }

    .booking-calendar .time-column {
        width: 20%;
        background: #f8fafc !important;
        color: #64748b !important;
        font-size: 0.9rem;
        font-weight: 500;
        border-right: 2px solid #e2e8f0;
        padding: 15px;
        text-align: center;
    }

    .time-slot {
        margin: 8px;
        border-radius: var(--small-radius);
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
    }

    .time-slot.available {
        background: var(--available-color);
        border: 2px dashed var(--available-border);
        color: #0369a1;
        font-weight: 500;
    }

    .time-slot.available:hover {
        background: var(--available-hover);
        color: white;
        border-style: solid;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.25);
    }

    .time-slot.available .fas {
        font-size: 1.2rem;
        margin-right: 8px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .time-slot.available:hover .fas {
        opacity: 1;
    }

    .time-slot.booked {
        background: var(--booked-color);
        border: 2px solid var(--booked-border);
        color: #dc2626;
    }

    .time-slot.booked:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.15);
    }

    .booking-info {
        text-align: center;
        padding: 8px;
        width: 100%;
    }

    .booking-info strong {
        display: block;
        font-size: 0.9rem;
        margin-bottom: 4px;
        color: #1e293b;
    }

    .booking-info small {
        display: block;
        opacity: 0.8;
        font-size: 0.75rem;
        margin-bottom: 6px;
        color: #64748b;
    }

    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: var(--small-radius);
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 2px;
    }

    .bg-success {
        background: #dcfce7 !important;
        color: #166534 !important;
    }

    .bg-warning {
        background: #fef3c7 !important;
        color: #92400e !important;
    }

    .bg-info {
        background: #dbeafe !important;
        color: #1e40af !important;
    }

    .bg-danger {
        background: #fee2e2 !important;
        color: #dc2626 !important;
    }

    .bg-secondary {
        background: #f1f5f9 !important;
        color: #64748b !important;
    }

    .bg-primary {
        background: #e0e7ff !important;
        color: #3730a3 !important;
    }

    .timer-display {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        font-size: 0.85rem;
     
        color: #374151;
    }

    .timer-running {
        background: #fee2e2 !important;
        color: #dc2626 !important;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .modal-content {
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        border: none;
    }

    .modal-header {
        background: var(--primary-gradient);
        color: white;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        padding: 20px 24px;
        border-bottom: none;
    }

    .modal-header h5 {
        font-weight: 600;
        font-size: 1.3rem;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-body-content {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: var(--small-radius);
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }

    .modal-body-content .row > div > div {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        font-size: 0.9rem;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .modal-body-content strong {
        color: var(--primary-color);
        font-weight: 600;
        margin-right: 8px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        padding: 12px 16px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .modal-footer {
        padding: 20px 24px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .btn {
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.95rem;
    }

    .btn-success {
        background: var(--primary-gradient);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #64748b;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: #374151;
    }

    .btn-warning {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .btn-primary {
        background: var(--primary-gradient);
        color: white;
    }

    .alert {
        border-radius: var(--small-radius);
        box-shadow: var(--small-shadow);
        padding: 16px 20px;
        font-size: 0.95rem;
        border: none;
        margin-bottom: 20px;
    }

    .alert-success {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        color: #166534;
        border-left: 4px solid #10b981;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #dc2626;
        border-left: 4px solid #ef4444;
    }

    .alert-info {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #1e40af;
        border-left: 4px solid #3b82f6;
    }

    .loading-overlay {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
        backdrop-filter: blur(8px);
        z-index: 10000;
        border-radius: var(--border-radius);
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.4em;
        color: var(--primary-color);
    }

    .modal-timer {
        font-family: 'Courier New', monospace;
        font-size: 3.5rem;
        font-weight: 700;
        color: #1e293b;
        margin: 24px 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-label {
        font-weight: 500;
        color: #374151;
    }

    /* Enhanced hover effects */
    .booking-calendar tbody tr {
        transition: background-color 0.3s ease;
    }

    .booking-calendar tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.02);
    }

    /* Loading animation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .fa-spin {
        animation: spin 1s linear infinite;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 10px;
        }

        .card-header.booking-header {
            /* padding: 20px; */
        }

        .card-header.booking-header h5 {
            font-size: 1.5rem;
        }

        .game-tabs {
            padding: 0 10px;
            flex-wrap: nowrap;
            overflow-x: auto;
        }

        .game-tab {
            padding: 15px 20px;
            font-size: 0.9rem;
            min-width: 120px;
        }

        .date-navigation {
            padding: 20px 15px;
        }

        .current-date {
            font-size: 1.1rem;
            margin: 0 20px;
        }

        .date-btn {
            width: 45px;
            height: 45px;
        }

        .time-slot {
            height: 56px;
            margin: 4px;
        }

        .booking-info {
            padding: 4px;
        }

        .booking-info strong {
            font-size: 0.8rem;
        }

        .modal-timer {
            font-size: 2.5rem;
        }

        .table-responsive {
            padding: 10px;
        }

        .booking-calendar th,
        .booking-calendar td.time-column {
            padding: 10px 8px;
            font-size: 0.85rem;
        }
    }

    /* Enhanced focus states for accessibility */
    .game-tab:focus,
    .date-btn:focus,
    .time-slot:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }

    /* Better text contrast */
    .text-muted {
        color: #64748b !important;
    }

    /* Improved button states */
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Enhanced modal animations */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: scale(0.95);
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }
</style>
@endpush

<div>
    <div class="container-fluid">
        <div wire:loading.flex class="loading-overlay"
            style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.7); z-index: 9999; justify-content: center; align-items: center;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2">Processing...</div>
            </div>
        </div>

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

        <div class="card booking-card">
            <div class="card-header booking-header">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-trophy me-2"></i>Sports Booking System
                </h5>
            </div>
            <div class="card-body p-0">
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
                        <i class="{{ $icon }} me-2"></i>{{ $game['name'] }}
                    </div>
                    @empty
                    <div class="alert alert-info m-3">
                        <i class="fas fa-info-circle me-2"></i>No games available. Please add sports to your complex.
                    </div>
                    @endforelse
                </div>

                @if(!empty($games))
                <div class="date-navigation">
                    <button class="btn btn-outline-secondary date-btn" id="prevDay">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="current-date" id="currentDate">{{ now()->format('F j, Y') }}</div>
                    <button class="btn btn-outline-secondary date-btn" id="nextDay">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="booking-calendar">
                        <thead>
                            <tr id="calendarHeader">
                                <th class="time-column">Time</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody">
                            <tr>
                                <td colspan="100%" class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading calendar...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <div class="modal fade" id="bookingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle me-2"></i>Book Slot
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form wire:submit.prevent="addBooking">
                        <div class="modal-body">
                            <div class="modal-body-content">
                                <div class="row g-3 p-3">
                                    <div class="col-6">
                                        <div>
                                            <strong>Game:</strong> <span id="modalGame">{{ $selectedGame }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div>
                                            <strong>Court:</strong> <span id="modalCourt">{{ $selectedCourt }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div>
                                            <strong>Date:</strong> <span id="modalDate">{{ $selectedDate }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div>
                                            <strong>Time:</strong> <span id="modalTime">{{ $selectedTime }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Player Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('playerName') is-invalid @enderror"
                                    wire:model.debounce.500ms="playerName" placeholder="Enter player name">
                                @error('playerName')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phoneNumber') is-invalid @enderror"
                                    wire:model.debounce.500ms="phoneNumber" placeholder="Enter phone number">
                                @error('phoneNumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select @error('status') is-invalid @enderror"
                                            wire:model="status">
                                            <option value="Pending">Pending</option>
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
                                    wire:model.debounce.500ms="notes" rows="3"
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

        <div class="modal fade" id="timerModal" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
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
                        <div class="d-flex justify-content-center gap-2 mt-3" id="timerControls">
                           
                            <button class="btn btn-danger" 
                                    wire:click="cancelBooking(${timerId})"
                                  >
                                Cancel booking
                            </button>
                        </div>
                    </div>

                    <div class="modal-footer d-flex flex-column align-items-center">
                        <button type="button" class="btn btn-primary btn-sm mb-2" id="newWindowBtn">Start Time</button>
                        <small class="text-muted">Timer will automatically update in the booking view</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Safely handle data from backend
    let sportData = @json($bookingdetails ?? []);
    const gamesConfig = @json($games ?? []);

    // Global variables
    let currentGame = '';
    let currentDate = new Date();
    let activeTimers = {};
    let activeModalTimerId = null;

    // Initialize system
    initializeSystem();

    function initializeSystem() {
        console.log('Initializing booking system...', { sportData, gamesConfig });
        
        // Set active game tab
        if (gamesConfig && gamesConfig.length > 0) {
            currentGame = gamesConfig[0].name.toLowerCase();
            const activeTab = document.querySelector(`.game-tab[data-game="${currentGame}"]`);
            if (activeTab) {
                activeTab.classList.add('active');
            } else {
                console.warn(`No game-tab found for currentGame: ${currentGame}`);
                const firstTab = document.querySelector('.game-tab');
                if (firstTab) {
                    currentGame = firstTab.getAttribute('data-game');
                    firstTab.classList.add('active');
                }
            }
            updateCalendar();
        } else {
            console.warn('No games configured');
        }
        
        setupEventListeners();
    }

    function setupEventListeners() {
        // Game tabs
        document.querySelectorAll('.game-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.game-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentGame = this.dataset.game;
                console.log('Game tab clicked:', currentGame);
                updateCalendar();
            });
        });

        // Date navigation
        const prevBtn = document.getElementById('prevDay');
        const nextBtn = document.getElementById('nextDay');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                currentDate.setDate(currentDate.getDate() - 1);
                console.log('Previous day clicked:', formatDate(currentDate));
                updateCalendar();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                currentDate.setDate(currentDate.getDate() + 1);
                console.log('Next day clicked:', formatDate(currentDate));
                updateCalendar();
            });
        }

        // Available slot clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.time-slot.available')) {
                e.preventDefault();
                const slot = e.target.closest('.time-slot.available');
                openBookingModal(slot);
            }
        });

        // Booked slot clicks (for timer)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.time-slot.booked')) {
                e.preventDefault();
                const slot = e.target.closest('.time-slot.booked');
                const timerId = slot.dataset.timerId;
                if (timerId) {
                    openTimerModal(timerId);
                }
            }
        });

        // New window button for timer popup
        const newWindowBtn = document.getElementById('newWindowBtn');
        if (newWindowBtn) {
            newWindowBtn.addEventListener('click', function() {
                if (activeModalTimerId) {
                    const timerState = activeTimers[activeModalTimerId];
                    if (timerState) {
                        if (timerState.popupWindow && !timerState.popupWindow.closed) {
                            timerState.popupWindow.close();
                            timerState.popupWindow = null;
                        }

                        const newWindow = window.open(
                            '/storage/staff/timer_window.html',
                            activeModalTimerId,
                            'width=500,height=450,resizable=yes,scrollbars=no'
                        );

                        if (newWindow) {
                            timerState.popupWindow = newWindow;
                            newWindow.onload = () => {
                                if (newWindow.receiveTimerData) {
                                    newWindow.receiveTimerData({
                                        timerId: activeModalTimerId,
                                        totalDuration: timerState.totalDuration,
                                        remaining: timerState.remaining,
                                        isRunning: timerState.isRunning,
                                        player: timerState.player,
                                        game: timerState.game,
                                        startTimeDisplay: timerState.startTimeDisplay
                                    });
                                }
                            };
                        } else {
                            alert('Popup blocked! Please allow popups for this site.');
                        }
                    }
                }
            });
        }

        // Livewire events
        window.addEventListener('bookingCreated', function(event) {
            console.log('Booking created successfully', event.detail);
            closeModal('bookingModal');
            refreshBookingData().then(() => {
                updateCalendar();
            });
        });

        window.addEventListener('closeModal', function() {
            console.log('closeModal event received');
            closeModal('bookingModal');
            updateCalendar();
        });

        $('#bookingModal').on('hidden.bs.modal', function () {
            console.log('Booking modal hidden');
            updateCalendar();
        });
    }

    function refreshBookingData() {
        return new Promise((resolve, reject) => {
            @this.call('getBookingDetails').then(response => {
                sportData = response || {};
                console.log('Booking data refreshed:', sportData);
                resolve();
            }).catch(error => {
                console.error('Error refreshing booking data:', error);
                alert('Error refreshing booking data. Please try again.');
                reject(error);
            });
        });
    }

    function openBookingModal(slot) {
        const time = slot.dataset.time || '';
        const display = slot.dataset.display || '';
        const court = slot.dataset.court || '';
        const date = formatDate(currentDate);
        const dateKey = formatDateKey(currentDate);

        if (!currentGame || !time || !display || !court || !dateKey) {
            console.error('Missing required data for booking modal:', { currentGame, time, display, court, dateKey });
            alert('Error: Unable to open booking modal. Missing required information.');
            return;
        }

        console.log('Opening booking modal with data:', { game: currentGame, dateKey, time, court, display });

        const modalElement = document.getElementById('bookingModal');
        if (!modalElement) {
            console.error('Booking modal element not found');
            alert('Error: Booking modal not found.');
            return;
        }

        const modal = new bootstrap.Modal(modalElement);
        document.getElementById('modalGame').textContent = currentGame.charAt(0).toUpperCase() + currentGame.slice(1);
        document.getElementById('modalDate').textContent = date;
        document.getElementById('modalTime').textContent = display;
        document.getElementById('modalCourt').textContent = court;

        // Extract the time in H:i:s format from the time variable
        const timeParts = time.split(':');
        const formattedTime = `${timeParts[0]}:${timeParts[1]}:${timeParts[2]}`;

        @this.setSelectedBookingData({ game: currentGame, dateKey, time: formattedTime, court }).then(() => {
            console.log('setSelectedBookingData resolved');
            modal.show();
        }).catch(error => {
            console.error('Error setting booking data:', error);
            alert('Error opening booking form. Please try again.');
            updateCalendar();
        });
    }

    function openTimerModal(timerId) {
        activeModalTimerId = timerId;
        const timerState = activeTimers[timerId] || {
            totalDuration: 3600,
            remaining: 3600,
            intervalId: null,
            isRunning: false,
            player: 'N/A',
            game: currentGame.charAt(0).toUpperCase() + currentGame.slice(1),
            startTimeDisplay: 'N/A',
            popupWindow: null
        };

        const modalElement = document.getElementById('timerModal');
        if (!modalElement) {
            console.error('Timer modal element not found');
            alert('Error: Timer modal not found.');
            return;
        }

        const modal = new bootstrap.Modal(modalElement);
        document.getElementById('modalTimerPlayerName').textContent = timerState.player || 'N/A';
        document.getElementById('modalTimerGameStartTime').textContent = timerState.startTimeDisplay || 'N/A';
        document.getElementById('modalTimer').textContent = formatTime(timerState.remaining);

        if (!activeTimers[timerId]) {
            activeTimers[timerId] = timerState;
        }

        updateTimerButtons(timerState.isRunning, timerId);
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
        if (!currentGame) {
            console.warn('No current game selected, skipping calendar update.');
            document.getElementById('calendarBody').innerHTML = `
                <tr>
                    <td colspan="100%" class="text-center py-4">
                        <i class="fas fa-info-circle me-2"></i>Please select a game to view the calendar.
                    </td>
                </tr>
            `;
            document.getElementById('calendarHeader').innerHTML = '<th class="time-column">Time</th>';
            return;
        }
        
        console.log('Updating calendar for:', currentGame, formatDateKey(currentDate));
        
        const dateKey = formatDateKey(currentDate);
        const gameData = sportData[currentGame]?.[dateKey] || {};
        
        let numberOfCourts = 3;
        const currentGameObject = gamesConfig.find(game => game.name.toLowerCase() === currentGame);
        if (currentGameObject && currentGameObject.maximum_court) {
            numberOfCourts = currentGameObject.maximum_court;
        }
        
        const courts = Array.from({ length: numberOfCourts }, (_, i) => `${i + 1}`);
        
        for (const timerId in activeTimers) {
            if (activeTimers[timerId].intervalId) {
                clearInterval(activeTimers[timerId].intervalId);
                activeTimers[timerId].intervalId = null;
            }
        }
        
        let headerHtml = '<th class="time-column">Time</th>';
        courts.forEach(court => {
            headerHtml += `<th>Court ${court}</th>`;
        });        
         document.getElementById('calendarHeader').innerHTML = headerHtml;
         
         let bodyHtml = '';
        const timeSlots = generateTimeSlots();
        
        timeSlots.forEach(slot => {
            bodyHtml += `<tr><td class="time-column">${slot.display}</td>`;
            
            courts.forEach(court => {
                const bookings = gameData[court] || {};                
                const bookingInfo = bookings[slot.time24.substring(0, 5) + ':00'];
                 
                if (bookingInfo) {
                    const timerId = `${currentGame}-${dateKey}-${court.replace(/\s/g, '')}-${slot.time24.replace(/:/g, '-')}`;
                    const totalDuration = calculateDurationInSeconds(slot.time24, bookingInfo.end);
                    
                    if (!activeTimers[timerId] || activeTimers[timerId].totalDuration !== totalDuration) {
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
                    const isRunning = activeTimers[timerId].isRunning;
                    
                  bodyHtml += `
                        <td>
                            <div class="time-slot booked d-flex align-items-center justify-content-between p-2 rounded shadow-sm bg-light mb-0" data-timer-id="${timerId}">
                                
                                <!-- Left: Avatar + Booking Info -->
                                <div class="d-flex align-items-center flex-shrink-0" style="min-width: 0;">
                                    <img alt="User Avatar"
                                        class="rounded-circle shadow-sm me-2"
                                        src="${bookingInfo.avatar || '/storage/staff/user.png'}"
                                        width="48" height="48">
                                    <div class="booking-info text-truncate">
                                        <strong class="d-block text-dark text-truncate">${bookingInfo.player}</strong>
                                        <small class="d-block text-muted text-truncate">${bookingInfo.phone}</small>
                                        <small class="d-block text-muted text-truncate">${bookingInfo.email || ''}</small>
                                    </div>
                                </div>

                                <!-- Center: Timer -->
                                <div class="text-center flex-grow-1">
                                        ${statusBadge}
                                    <div id="${timerId}" class="timer-display ${isRunning ? 'timer-running' : ''} fw-bold text-primary">
                                        ${displayTime}
                                    </div>
                                </div>

                                <!-- Right: Badges -->
                                <div class="badges d-flex align-items-center gap-1 flex-shrink-0">
                            

                                    ${bookingInfo.permanent ? `<span class="badge bg-dark text-white">P</span>` : ''}
                                </div>
                                
                            </div>
                        </td>

                        `;

                } else {
                    bodyHtml += `
                        <td>
                            <div class="time-slot available" 
                                 data-time="${slot.time24.substring(0, 8)}" 
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
        
        for (const timerId in activeTimers) {
            const timerState = activeTimers[timerId];
            if (timerState.isRunning && timerState.remaining > 0) {
                startTimer(timerId);
            }
        }
    }
    function handleSlotClick(sportId, courtName, timeSlot) {
    // Open modal and set booking details, similar to sample
    const modal = document.getElementById('booking-modal');
    const modalDetailsEl = document.getElementById('modal-details');
    modalDetailsEl.innerHTML = `
        <strong>Sport:</strong> ${sportId}<br>
        <strong>Court:</strong> ${courtName}<br>
        <strong>Time:</strong> ${timeSlot}<br>
        <strong>Date:</strong> ${formatDate(currentDate)}
    `;
    modal.classList.add('flex');
}

    function handleCancelBooking(timerId) {
        // Remove booking from sportData and re-render
        // Example: delete sportData[currentGame][dateKey][court][time];
        updateCalendar();
    }

    function generateTimeSlots() {
        const slots = [];
        for (let hour = 6; hour < 23; hour++) {
            const startHour = hour % 12 || 12;
            const ampm = hour < 12 ? 'AM' : 'PM';
            const nextHour24 = (hour + 1) % 24;
            const nextHour12 = nextHour24 % 12 || 12;
            const nextAmpm = nextHour24 < 12 ? 'AM' : 'PM';

            slots.push({
                time24: `${hour.toString().padStart(2, '0')}:00:00.000000`, // Modified time format
                display: `${startHour}:00 ${ampm} - ${nextHour12}:00 ${nextAmpm}`
            });
        }
        return slots;
    }

    function formatDate(date) {
        return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
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
        let badgeClass = '';
        switch (status) {
            case 'Confirmed': badgeClass = 'bg-success'; break;
            case 'Pending': badgeClass = 'bg-warning text-dark'; break;
            case 'Completed': badgeClass = 'bg-info'; break;
            case 'Cancelled': badgeClass = 'bg-danger'; break;
            case 'No-Show': badgeClass = 'bg-secondary'; break;
            default: badgeClass = 'bg-secondary';
        }
        return `<span class="badge ${badgeClass}">${status}</span>`;
    }

    function startTimer(timerIdToControl) {
        const timerState = activeTimers[timerIdToControl];
        if (!timerState || timerState.intervalId) return;

        timerState.isRunning = true;
        updateTimerButtons(true, timerIdToControl);

        timerState.intervalId = setInterval(() => {
            if (timerState.remaining > 0) {
                timerState.remaining--;
                updateTimerDisplay(timerIdToControl);
            } else {
                clearInterval(timerState.intervalId);
                timerState.intervalId = null;
                timerState.isRunning = false;
                updateTimerButtons(false, timerIdToControl);
                updateTimerDisplay(timerIdToControl);
            }
        }, 1000);

        if (timerState.popupWindow && !timerState.popupWindow.closed && typeof timerState.popupWindow.startWindowTimer === 'function') {
            timerState.popupWindow.startWindowTimer();
        }
    }

    function pauseTimer(timerIdToControl) {
        const timerState = activeTimers[timerIdToControl];
        if (!timerState || !timerState.intervalId) return;

        clearInterval(timerState.intervalId);
        timerState.intervalId = null;
        timerState.isRunning = false;
        updateTimerButtons(false, timerIdToControl);
        updateTimerDisplay(timerIdToControl);

        if (timerState.popupWindow && !timerState.popupWindow.closed && typeof timerState.popupWindow.pauseWindowTimer === 'function') {
            timerState.popupWindow.pauseWindowTimer();
        }
    }

    function resetTimer(timerIdToControl) {
        const timerState = activeTimers[timerIdToControl];
        if (!timerState) return;

        pauseTimer(timerIdToControl);
        timerState.remaining = timerState.totalDuration;
        timerState.isRunning = false;
        updateTimerButtons(false, timerIdToControl);
        updateTimerDisplay(timerIdToControl);

        if (timerState.popupWindow && !timerState.popupWindow.closed && typeof timerState.popupWindow.resetWindowTimer === 'function') {
            timerState.popupWindow.resetWindowTimer();
        }
    }

    function updateTimerDisplay(timerId) {
        const timerState = activeTimers[timerId];
        if (!timerState) return;

        const formattedTime = formatTime(timerState.remaining);

        if (activeModalTimerId === timerId) {
            const modalTimer = document.getElementById('modalTimer');
            if (modalTimer) modalTimer.textContent = formattedTime;
        }

        const tableTimer = document.getElementById(timerId);
        if (tableTimer) {
            tableTimer.textContent = formattedTime;
            if (timerState.isRunning) tableTimer.classList.add('timer-running');
            else tableTimer.classList.remove('timer-running');
        }
    }

    function updateTimerButtons(isRunning, timerId) {
        const startBtn = document.getElementById('startButton');
        const pauseBtn = document.getElementById('pauseButton');
        const resetBtn = document.getElementById('resetButton');
        
        if (startBtn && pauseBtn && resetBtn && activeModalTimerId === timerId) {
            startBtn.disabled = isRunning;
            pauseBtn.disabled = !isRunning;
            resetBtn.disabled = isRunning;
        }
    }

    window.updateMainTimerDisplay = function(remaining, timerIdFromPopup, isRunningFromPopup) {
        const timerState = activeTimers[timerIdFromPopup];
        if (timerState) {
            timerState.remaining = remaining;
            timerState.isRunning = isRunningFromPopup;
            updateTimerDisplay(timerIdFromPopup);
            if (activeModalTimerId === timerIdFromPopup) {
                const modalTimer = document.getElementById('modalTimer');
                if (modalTimer) modalTimer.textContent = formatTime(remaining);
                updateTimerButtons(isRunningFromPopup, timerIdFromPopup);
            }
        }
    };

    window.startTimer = startTimer;
    window.pauseTimer = pauseTimer;    
    window.resetTimer = resetTimer;
});

document.addEventListener('livewire:initialized', () => {
    Livewire.on('bookingCancelled', () => {
        // Refresh your calendar or booking display here
        console.log('Booking cancelled - refresh UI');
    });
});
</script>
@endpush
@push('styles')
<style>
    /* Notification animation */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .booking-notification {
        animation: slideInRight 0.3s ease-out !important;
    }

    /* Past slot styling */
    .time-slot.past-slot {
        background: #e9ecef !important;
        border: 2px dashed #adb5bd !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
        opacity: 0.6 !important;
        pointer-events: none !important;
    }

    /* Blocked slot styling */
    .time-slot.blocked-slot {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
        border: 2px solid #ffc107 !important;
        color: #856404 !important;
        cursor: default !important;
    }

    .time-slot.blocked-slot:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    /* Held slot styling (mobile user in checkout) */
    .time-slot.held-slot {
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%) !important;
        border: 2px dashed #f97316 !important;
        color: #c2410c !important;
        cursor: default !important;
        animation: held-pulse 2s ease-in-out infinite;
    }
    .time-slot.held-slot:hover {
        transform: none !important;
    }
    @keyframes held-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.75; }
    }

    /* Slot action buttons */
    .slot-action-btn {
        padding: 3px 8px;
        font-size: 0.75rem;
        border-radius: 5px;
        border: 1.5px solid;
        background: transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        line-height: 1.4;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-weight: 500;
    }
    .slot-action-btn:hover {
        opacity: 0.85;
        transform: translateY(-1px);
    }

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
        padding: 0;
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
        margin: 2px;
        border-radius: var(--small-radius);
        height: 75px;
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

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
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

    .modal-body-content strong {
        color: var(--primary-color);
        font-weight: 600;
        margin-right: 8px;
    }

    .booking-info-pill {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.88rem;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .booking-info-pill .info-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .booking-info-pill .info-val {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .end-time-selection-box {
        background: white;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 12px 14px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .end-time-selection-box .end-time-label {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.88rem;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
    }

    .end-time-selection-box .duration-select {
        border: 2px solid #16a34a !important;
        font-weight: 600;
        color: #15803d;
        background-color: #f0fdf4;
        margin-bottom: 6px;
        padding: 8px 12px !important;
        font-size: 0.9rem !important;
        cursor: pointer;
    }

    .end-time-selection-box .duration-select:focus {
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2);
    }

    .end-time-selection-box .end-time-help {
        font-size: 0.76rem;
        line-height: 1.35;
        color: #64748b;
    }

    /* Spanned Continuous Booking Cell Styling */
    td[rowspan] {
        height: 1px !important;
        vertical-align: top !important;
        padding: 2px !important;
    }

    td[rowspan] > .time-slot.booked {
        height: calc(100% - 4px) !important;
        max-height: calc(100% - 4px) !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
        margin: 2px 0 !important;
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
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .fa-spin {
        animation: spin 1s linear infinite;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0 !important;
        }

        .card.booking-card {
            margin-bottom: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .card-header.booking-header {
            padding: 12px 10px;
        }

        .card-header.booking-header h5 {
            font-size: 1.1rem;
        }

        .game-tabs {
            padding: 0;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .game-tab {
            padding: 10px 14px;
            font-size: 0.8rem;
            min-width: 90px;
        }

        .game-tab i {
            font-size: 0.9rem;
            margin-right: 5px;
        }

        .date-navigation {
            padding: 8px 10px;
        }

        .current-date {
            font-size: 0.95rem;
            margin: 0 10px;
        }

        .date-btn {
            width: 36px;
            height: 36px;
            font-size: 0.85rem;
        }

        .table-responsive {
            padding: 0;
        }

        .booking-calendar {
            font-size: 0.75rem;
        }

        .booking-calendar th {
            padding: 8px 4px;
            font-size: 0.7rem;
            letter-spacing: 0;
        }

        .booking-calendar .time-column {
            width: 52px;
            min-width: 52px;
            padding: 6px 4px;
            font-size: 0.68rem;
            white-space: nowrap;
            word-break: keep-all;
        }

        .booking-calendar td {
            height: 60px;
            padding: 0;
        }

        .time-slot {
            height: 56px;
            margin: 2px;
            font-size: 0.7rem;
        }

        .booking-info {
            padding: 2px;
        }

        .booking-info strong {
            font-size: 0.72rem;
        }

        .booking-info small {
            font-size: 0.65rem;
        }

        .modal-timer {
            font-size: 2.5rem;
        }

        /* Hide avatar image on mobile to save space */
        .time-slot.booked img {
            width: 32px !important;
            height: 32px !important;
        }

        .slot-action-btn {
            padding: 1px 4px;
            font-size: 0.6rem;
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
        {{-- <div wire:loading.flex class="loading-overlay"
            style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.7); z-index: 9999; justify-content: center; align-items: center;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2">Processing...</div>
            </div>
        </div> --}}

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
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

        <div class="d-flex justify-content-end align-items-center mb-3">
            <a href="{{ route('staff.completed-bookings') }}" class="btn btn-success d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm rounded-3 fw-bold">
                <i class="fas fa-check-double"></i> Completed Bookings
            </a>
        </div>

        <div class="card booking-card">
            <div class="card-header booking-header">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-trophy me-2"></i>Sports Booking System
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="game-tabs" wire:ignore>
                    @forelse ($games as $index => $game)
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
                    <div class="game-tab {{ $index === 0 ? 'active' : '' }}" data-game="{{ strtolower($game['name']) }}">
                        <i class="{{ $icon }}"></i> {{ $game['name'] }}
                    </div>
                    @empty
                    <div class="alert alert-info m-3">
                        <i class="fas fa-info-circle me-2"></i>No games available. Please add sports to your complex.
                    </div>
                    @endforelse
                </div>

                @if(!empty($games))
                <div class="date-navigation" wire:ignore>
                    <button class="btn btn-outline-secondary date-btn" id="prevDay">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="current-date position-relative" style="cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;" onclick="document.getElementById('datePickerInput').showPicker()">
                        <span id="currentDate">{{ now()->format('F j, Y') }}</span>
                        <i class="fas fa-calendar-alt text-muted" style="font-size: 1.1rem;"></i>
                        <input type="date" id="datePickerInput" style="position: absolute; opacity: 0; top: 0; left: 0; height: 100%; width: 100%; cursor: pointer;">
                    </div>
                    <button class="btn btn-outline-secondary date-btn" id="nextDay">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="booking-calendar" wire:ignore>
                        <thead>
                            <tr id="calendarHeader">
                                <th class="time-column">Time</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody">
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
                            <div class="modal-body-content p-3 mb-3">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="booking-info-pill">
                                            <span class="info-label">Game</span>
                                            <span class="info-val text-truncate" id="modalGame">{{ $selectedGame }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="booking-info-pill">
                                            <span class="info-label">Court</span>
                                            <span class="info-val" id="modalCourt">{{ $selectedCourt }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="booking-info-pill">
                                            <span class="info-label">Date</span>
                                            <span class="info-val" id="modalDate">{{ $selectedDate }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="booking-info-pill">
                                            <span class="info-label">Start Time</span>
                                            <span class="info-val text-success" id="modalTime">{{ $selectedTime }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="end-time-selection-box">
                                    <label for="endTimeSelect" class="end-time-label">
                                        <i class="far fa-clock text-success me-1"></i> End Time (Select Duration)
                                    </label>
                                    <select id="endTimeSelect" class="form-select duration-select" wire:model="selectedEndTime">
                                        @if(empty($endTimeOptions))
                                            @php
                                                try {
                                                    $defaultEnd = \Carbon\Carbon::parse($selectedTime)->addHour();
                                                } catch (\Throwable $e) {
                                                    $defaultEnd = \Carbon\Carbon::now()->addHour();
                                                }
                                            @endphp
                                            <option value="{{ $defaultEnd->format('H:i:s') }}">
                                                {{ $defaultEnd->format('g:i A') }} (1 hr)
                                            </option>
                                        @else
                                            @foreach($endTimeOptions as $opt)
                                                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="end-time-help">
                                        Select end time to book multiple consecutive slots at once. Already booked or blocked slots in between will be skipped automatically.
                                    </div>
                                </div>
                            </div>

                            @php
                                $currentGameKey = strtolower($selectedGame ?? '');
                                $maxCap = $maxCapacityMap[$currentGameKey] ?? 1;
                            @endphp

                            @if($maxCap > 1 || $currentGameKey === 'pools' || $currentGameKey === 'pool')
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="fas fa-users me-1"></i> Number of Swimmers / Persons <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary"><i class="fas fa-user-plus"></i></span>
                                    <input type="number" class="form-control" wire:model="num_persons" min="1" max="{{ $maxCap }}" placeholder="1">
                                    <span class="input-group-text bg-light text-muted">Persons (Max: {{ $maxCap }})</span>
                                </div>
                                <small class="text-muted">Specify how many persons are included in this booking slot</small>
                            </div>
                            @endif

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
                                <input type="tel" class="form-control @error('phoneNumber') is-invalid @enderror"
                                    wire:model.debounce.500ms="phoneNumber" 
                                    placeholder="Enter phone number"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');">
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
                                            <option value="Confirmed">Confirmed</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                            <option value="No-Show">No-Show</option>
                                            <option value="Playing">Playing</option>
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
                                {{-- <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-2"></i>Processing...
                                </span> --}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Permanent Booking Preview & Confirmation Modal --}}
        @if($showPermanentConfirmModal)
        <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.6);z-index:1070;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow-lg border-0 rounded-3">
                    <div class="modal-header bg-success text-white py-3">
                        <h5 class="modal-title fw-bold text-white mb-0">
                            <i class="fas fa-calendar-check me-2"></i>Permanent Booking Confirmation (30-Day Window)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closePermanentConfirmModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-3 mb-4" style="background: #f0fdf4;">
                            <i class="fas fa-info-circle fa-2x text-success"></i>
                            <div>
                                <h6 class="fw-bold mb-1 text-success">Recurring Schedule: Every {{ $permanentDayName }} for 30 Days</h6>
                                <p class="small mb-0 text-slate-700">
                                    <strong>Game:</strong> {{ $selectedGame }} &bull; 
                                    <strong>Court:</strong> {{ $selectedCourt }} &bull; 
                                    <strong>Time:</strong> {{ $selectedTime }} &bull; 
                                    <strong>Customer:</strong> {{ $playerName }} ({{ $phoneNumber }})
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">
                            {{-- Available Slots --}}
                            <div class="col-md-6">
                                <div class="card h-100 border-success shadow-sm">
                                    <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center py-2">
                                        <span><i class="fas fa-check-circle me-1"></i> Available Slots (Will be booked)</span>
                                        <span class="badge bg-white text-success fw-bold">{{ count($permanentAvailableDates) }}</span>
                                    </div>
                                    <div class="card-body p-2" style="max-height: 250px; overflow-y: auto;">
                                        @forelse($permanentAvailableDates as $item)
                                        <div class="d-flex align-items-center justify-content-between p-2 mb-1 bg-light rounded border border-success-subtle">
                                            <span class="fw-semibold text-dark small"><i class="fas fa-calendar-day text-success me-2"></i>{{ $item['formatted'] }}</span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle small">Available</span>
                                        </div>
                                        @empty
                                        <p class="text-muted text-center small my-3">No available slots found for this time in the next 30 days.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- Ignored / Conflict Slots --}}
                            <div class="col-md-6">
                                <div class="card h-100 border-warning shadow-sm">
                                    <div class="card-header bg-warning text-dark fw-bold d-flex justify-content-between align-items-center py-2">
                                        <span><i class="fas fa-exclamation-triangle me-1"></i> Ignored Slots (Already Booked/Blocked)</span>
                                        <span class="badge bg-dark text-white fw-bold">{{ count($permanentIgnoredDates) }}</span>
                                    </div>
                                    <div class="card-body p-2" style="max-height: 250px; overflow-y: auto;">
                                        @forelse($permanentIgnoredDates as $item)
                                        <div class="d-flex align-items-center justify-content-between p-2 mb-1 bg-light rounded border border-warning-subtle">
                                            <span class="fw-semibold text-dark small"><i class="fas fa-calendar-times text-warning me-2"></i>{{ $item['formatted'] }}</span>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle small">{{ $item['reason'] }}</span>
                                        </div>
                                        @empty
                                        <p class="text-muted text-center small my-3">None! All slots are free and available to book.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3">
                        <button type="button" class="btn btn-secondary" wire:click="closePermanentConfirmModal">Cancel</button>
                        <button type="button" class="btn btn-success fw-bold px-4" 
                                wire:click="confirmPermanentBooking" 
                                @if(empty($permanentAvailableDates)) disabled @endif>
                            <i class="fas fa-check me-2"></i>Confirm Permanent Booking ({{ count($permanentAvailableDates) }} Slots)
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Feature #9: Block Slot Modal --}}
        @if($showBlockModal)
        <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1060;">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <h6 class="modal-title text-white"><i class="fas fa-ban me-2"></i>Block Time Slot</h6>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeBlockModal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted mb-2">Block this slot so it cannot be booked from the mobile app.</p>
                        @if($blockingSlot)
                        <div class="mb-2 p-2 bg-light rounded small">
                            <strong>Date:</strong> {{ $blockingSlot['date'] ?? '' }}&nbsp;
                            <strong>Time:</strong> {{ $blockingSlot['time'] ?? '' }}&nbsp;
                            <strong>Court:</strong> {{ $blockingSlot['court'] ?? '' }}
                        </div>
                        @endif
                        <label class="form-label small fw-semibold">Reason</label>
                        <select class="form-select form-select-sm" wire:model="blockReason">
                            <option value="Maintenance">Maintenance</option>
                            <option value="Private Event">Private Event</option>
                            <option value="Tournament">Tournament</option>
                            <option value="Staff Training">Staff Training</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div class="modal-footer" style="background: #f8fafc;">
                        <button class="btn btn-sm btn-secondary" wire:click="closeBlockModal">Cancel</button>
                        <button class="btn btn-sm btn-warning" wire:click="blockSlot">
                            <i class="fas fa-ban me-1"></i>Block Slot
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Feature #12: Waitlist Modal --}}
        @if($showWaitlistModal)
        <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1060;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                        <h6 class="modal-title text-white"><i class="fas fa-list-ul me-2"></i>Waitlist
                            @if($waitlistDate)
                            <small class="ms-2 opacity-75">{{ $waitlistDate }} &bull; {{ $waitlistTime }} &bull; Court {{ $waitlistCourt }}</small>
                            @endif
                        </h6>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeWaitlistModal"></button>
                    </div>
                    <div class="modal-body">
                        @if(count($waitlistEntries) > 0)
                        <div class="mb-3">
                            <h6 class="small fw-semibold mb-2 text-muted text-uppercase">Current Waitlist ({{ count($waitlistEntries) }})</h6>
                            @foreach($waitlistEntries as $entry)
                            <div class="d-flex justify-content-between align-items-center p-2 border rounded mb-2 bg-light">
                                <div>
                                    <div class="small fw-semibold">{{ $entry['customer_name'] }}</div>
                                    <div class="text-muted small">{{ $entry['customer_phone'] }}</div>
                                    <span class="badge bg-{{ $entry['status'] === 'notified' ? 'success' : ($entry['status'] === 'booked' ? 'primary' : 'secondary') }} small">
                                        {{ ucfirst($entry['status']) }}
                                    </span>
                                    @if(!empty($entry['notified_at']))
                                    <span class="text-muted small ms-1">Notified: {{ \Carbon\Carbon::parse($entry['notified_at'])->format('H:i') }}</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-1">
                                    @if($entry['status'] === 'waiting')
                                    <button class="btn btn-sm btn-outline-success slot-action-btn" wire:click="notifyWaitlistNext({{ $entry['id'] }})" title="Notify this person slot is available">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                    @endif
                                    <button class="btn btn-sm btn-outline-danger slot-action-btn" wire:click="removeFromWaitlist({{ $entry['id'] }})" title="Remove from waitlist">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <hr class="my-2">
                        @else
                        <div class="text-center text-muted py-2 small mb-3">
                            <i class="fas fa-inbox me-1"></i>No one on the waitlist yet.
                        </div>
                        @endif

                        <h6 class="small fw-semibold mb-2 text-muted text-uppercase">Add to Waitlist</h6>
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm @error('waitlistName') is-invalid @enderror"
                                wire:model="waitlistName" placeholder="Customer Name">
                            @error('waitlistName') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm @error('waitlistPhone') is-invalid @enderror"
                                wire:model="waitlistPhone" placeholder="Phone Number">
                            @error('waitlistPhone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer" style="background: #f8fafc;">
                        @if(count($waitlistEntries) > 0)
                        <button class="btn btn-sm btn-success me-auto" wire:click="notifyFirstInWaitlist" wire:loading.attr="disabled">
                            <i class="fas fa-paper-plane me-1"></i>Send SMS
                        </button>
                        @endif
                        <button class="btn btn-sm btn-secondary" wire:click="closeWaitlistModal">Close</button>
                        <button class="btn btn-sm btn-info text-white" wire:click="addToWaitlist" wire:loading.attr="disabled">
                            <i class="fas fa-plus me-1"></i>Add to Waitlist
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Multi-Slot / Continuous Booking Cancel Choice Modal (Pic 2) --}}
        @if($showCancelModal)
        <div class="modal fade show d-block" id="cancelBookingChoiceModal" tabindex="-1" style="background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 1085;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="border-radius: 20px;">
                    <!-- Modal Header -->
                    <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-rose-50 text-rose-600 rounded-3 d-flex align-items-center justify-content-center" style="background: #ffe4e6; color: #e11d48; width: 52px; height: 52px; border-radius: 14px;">
                                <i class="far fa-calendar-times fs-3"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="modal-title fw-bold text-dark mb-0 fs-4">Cancel Booking #{{ $cancelBookingId }}</h5>
                                <small class="text-muted" style="font-size: 0.88rem;">Choose fully booking cancel or selection to slot cancel</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" wire:click="closeCancelModal"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4">
                        @if($cancelType === 'choose')
                            <p class="text-secondary fw-semibold mb-4 text-start" style="font-size: 0.95rem;">Select how you would like to handle this booking cancellation:</p>

                            <div class="row g-4">
                                <!-- Option 1: Fully Booking Cancel -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-2 rounded-4 p-4 shadow-sm text-start" style="border: 2px solid #ef4444 !important; border-radius: 16px; background: #ffffff;">
                                        <div class="mb-3">
                                            <div class="d-inline-flex p-3 rounded-3" style="background: #ffe4e6; color: #e11d48; border-radius: 12px;">
                                                <i class="far fa-trash-alt fs-4"></i>
                                            </div>
                                        </div>
                                        <h6 class="fw-bold text-danger mb-2" style="font-size: 1.05rem;">Fully Booking Cancel</h6>
                                        <p class="text-muted small mb-4" style="line-height: 1.45; font-size: 0.86rem;">
                                            Cancel the entire continuous booking series and deactivate all remaining slot occurrences.
                                        </p>
                                        <div class="mt-auto pt-3 border-top border-danger border-opacity-10">
                                            <button type="button" class="btn btn-link text-danger text-decoration-none fw-bold p-0 d-flex align-items-center gap-2" wire:click="cancelFullSeries">
                                                <span>Cancel Entire Series</span>
                                                <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Option 2: Selection to Slot Cancel -->
                                <div class="col-md-6">
                                    <div class="card h-100 border rounded-4 p-4 shadow-sm text-start" style="border: 1px solid #cbd5e1 !important; border-radius: 16px; background: #ffffff;">
                                        <div class="mb-3">
                                            <div class="d-inline-flex p-3 rounded-3" style="background: #fef3c7; color: #d97706; border-radius: 12px;">
                                                <i class="fas fa-tasks fs-4"></i>
                                            </div>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-2" style="font-size: 1.05rem;">Selection to Slot Cancel</h6>
                                        <p class="text-muted small mb-4" style="line-height: 1.45; font-size: 0.86rem;">
                                            Select specific slot occurrences/dates to cancel while keeping other scheduled slots active.
                                        </p>
                                        <div class="mt-auto pt-3 border-top border-secondary border-opacity-10">
                                            <button type="button" class="btn btn-link text-decoration-none fw-bold p-0 d-flex align-items-center gap-2" style="color: #d97706 !important;" wire:click="setCancelType('specific')">
                                                <span>Select Specific Slots</span>
                                                <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Specific Slots Selection View -->
                            <div class="d-flex align-items-center justify-content-between mb-3 text-start">
                                <h6 class="fw-bold text-dark mb-0">Select specific slots to cancel:</h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" wire:click="setCancelType('choose')">
                                    <i class="fas fa-arrow-left me-1"></i> Back to options
                                </button>
                            </div>

                            <div class="form-check mb-3 p-3 bg-light rounded-3 border text-start">
                                <input class="form-check-input ms-0 me-2" type="checkbox" id="selectAllCancelSlots" wire:model.live="selectAllCancelSlots">
                                <label class="form-check-label fw-bold text-dark cursor-pointer" for="selectAllCancelSlots">
                                    Select All Continuous Slots ({{ count($cancelContinuousSlots) }})
                                </label>
                            </div>

                            <div class="list-group mb-4 text-start" style="max-height: 260px; overflow-y: auto;">
                                @foreach($cancelContinuousSlots as $slotRecord)
                                    @php
                                        $sStart = \Carbon\Carbon::parse($slotRecord['start_time'])->format('g:i A');
                                        $sEnd = \Carbon\Carbon::parse($slotRecord['end_time'])->format('g:i A');
                                    @endphp
                                    <label class="list-group-item d-flex align-items-center justify-content-between p-3 cursor-pointer">
                                        <div class="d-flex align-items-center gap-3">
                                            <input class="form-check-input me-2" type="checkbox" value="{{ $slotRecord['id'] }}" wire:model.live="selectedCancelSlotIds">
                                            <div>
                                                <strong class="d-block text-dark"><i class="far fa-clock text-primary me-1"></i>{{ $sStart }} – {{ $sEnd }}</strong>
                                                <small class="text-muted">Court {{ $slotRecord['court_number'] }} · {{ $slotRecord['game_name'] }}</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-secondary">Booking #{{ $slotRecord['id'] }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary rounded-3" wire:click="setCancelType('choose')">Cancel</button>
                                <button type="button" class="btn btn-danger px-4 rounded-3" wire:click="cancelSelectedSpecificSlots">
                                    <i class="fas fa-trash-alt me-2"></i>Cancel Selected Slots
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Footer (Shown in Choice View) -->
                    @if($cancelType === 'choose')
                        <div class="modal-footer border-top-0 pt-0 pb-4 px-4 justify-content-start">
                            <button type="button" class="btn btn-light border px-4 rounded-3" wire:click="closeCancelModal">Close</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="modal fade" id="timerModal" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-stopwatch me-2"></i>Booking Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="alert alert-info mb-3">
                            <strong>Player:</strong> <span id="modalTimerPlayerName">-</span><br>
                            <strong>Game:</strong> <span id="modalTimerGameName">-</span><br>
                            <strong>Court:</strong> <span id="modalTimerCourt">-</span><br>
                            <strong>Date:</strong> <span id="modalTimerDate">-</span><br>
                            <strong>Start Time:</strong> <span id="modalTimerGameStartTime">-</span>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary" id="modalStatusBadge" style="font-size: 0.85rem;">Confirmed</span>
                        </div>
                        <div class="modal-timer" id="modalTimer" style="font-size: 2.5rem; font-weight: 700; font-variant-numeric: tabular-nums;">00:00:00</div>
                        <div class="d-flex justify-content-center gap-2 mt-3" id="timerControls">
                            <button type="button" class="btn btn-danger" id="cancelBookingBtn">
                                <i class="fas fa-times me-2"></i>Cancel Booking
                            </button>
                        </div>
                    </div>

                    <div class="modal-footer d-flex flex-column align-items-center">
                        <!-- Start Timer button: shown only when NOT playing -->
                        <button type="button" class="btn btn-primary btn-sm mb-2" id="newWindowBtn">
                            <i class="fas fa-play-circle me-2"></i>Start Timer
                        </button>
                        
                        <!-- Running controls: shown when Playing -->
                        <div id="modalRunningControls" class="d-none w-100 text-center">
                            <div class="d-flex justify-content-center gap-2 mb-2">
                                <button type="button" class="btn btn-warning btn-sm" id="modalPauseBtn">
                                    <i class="fas fa-pause me-1"></i>Pause
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="modalForceCompleteBtn">
                                    <i class="fas fa-check-circle me-1"></i>Force Complete
                                </button>
                            </div>
                        </div>
                        <small class="text-muted mt-1">Timer will automatically update in the booking view</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Collection Modal for Completed/Played Bookings --}}
    @if($showPaymentCollectModal && $paymentCollectBooking)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1070;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <h6 class="modal-title fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Collect Payment</h6>
                    <button type="button" class="btn-close btn-close-white" wire:click="closePaymentCollectModal"></button>
                </div>
                <div class="modal-body p-3">
                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" style="font-size: 0.8rem;">
                            {{ session('success') }}
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="bg-light rounded-3 p-3 mb-3" style="font-size: 0.82rem;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Booking ID:</span>
                            <span class="fw-bold">#{{ $paymentCollectBooking->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Player:</span>
                            <span class="fw-bold">{{ $paymentCollectBooking->user_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Phone:</span>
                            <span class="fw-bold">{{ $paymentCollectBooking->user_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Sport:</span>
                            <span class="fw-bold">{{ $paymentCollectBooking->game_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Court:</span>
                            <span class="fw-bold">{{ $paymentCollectBooking->court_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Date:</span>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($paymentCollectBooking->booking_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Time:</span>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($paymentCollectBooking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($paymentCollectBooking->end_time)->format('h:i A') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Payment Status:</span>
                            <span class="badge {{ strtolower($paymentCollectBooking->payment_status) === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($paymentCollectBooking->payment_status ?: 'Unpaid') }}
                            </span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted fw-bold">Amount:</span>
                            <span class="fw-bold text-primary" style="font-size: 1.1rem;">LKR {{ number_format($paymentCollectBooking->price ?: 0, 2) }}</span>
                        </div>
                    </div>

                    @if(strtolower($paymentCollectBooking->payment_status) !== 'paid')
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">Select Payment Method</label>
                        <div class="d-flex gap-2">
                            <div class="flex-fill text-center p-2 rounded-3 border {{ $paymentCollectMethod === 'cash' ? 'border-warning bg-warning bg-opacity-10 fw-bold' : 'bg-light' }}" 
                                 style="cursor: pointer; font-size: 0.8rem;" wire:click="$set('paymentCollectMethod', 'cash')">
                                <i class="fas fa-money-bill-wave d-block mb-1" style="font-size: 1.2rem;"></i>
                                Cash
                            </div>
                            <div class="flex-fill text-center p-2 rounded-3 border {{ $paymentCollectMethod === 'card' ? 'border-warning bg-warning bg-opacity-10 fw-bold' : 'bg-light' }}" 
                                 style="cursor: pointer; font-size: 0.8rem;" wire:click="$set('paymentCollectMethod', 'card')">
                                <i class="fas fa-credit-card d-block mb-1" style="font-size: 1.2rem;"></i>
                                Card
                            </div>
                            <div class="flex-fill text-center p-2 rounded-3 border {{ $paymentCollectMethod === 'transfer' ? 'border-warning bg-warning bg-opacity-10 fw-bold' : 'bg-light' }}" 
                                 style="cursor: pointer; font-size: 0.8rem;" wire:click="$set('paymentCollectMethod', 'transfer')">
                                <i class="fas fa-exchange-alt d-block mb-1" style="font-size: 1.2rem;"></i>
                                Transfer
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer bg-light py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="printPaymentReceipt()">
                        <i class="fas fa-print me-1"></i>Print Receipt
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-secondary" wire:click="closePaymentCollectModal">Close</button>
                        @if(strtolower($paymentCollectBooking->payment_status) !== 'paid')
                        <button type="button" class="btn btn-sm btn-success fw-bold" wire:click="collectBookingPayment">
                            <i class="fas fa-check-circle me-1"></i>Confirm Payment
                        </button>
                        @else
                        <span class="badge bg-success py-2 px-3"><i class="fas fa-check me-1"></i>Already Paid</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Printable Receipt --}}
    <div id="printable-payment-receipt" style="display:none;">
        <div style="font-family: 'Courier New', monospace; max-width: 300px; margin: 0 auto; padding: 10px; font-size: 12px; line-height: 1.5;">
            <div style="text-align: center; margin-bottom: 10px;">
                <div style="font-size: 16px; font-weight: bold; text-transform: uppercase;">{{ $paymentCollectBooking->venue->name ?? 'Thansher’s Futsal' }}</div>
                <div style="font-size: 10px; color: #555; margin-top: 2px;">Booking Management Department</div>
                <div style="font-size: 9px; color: #666;">{{ $paymentCollectBooking->venue->address ?? 'Thihariya, Sri Lanka' }}</div>
                <div style="border-bottom: 1px dashed #000; margin: 8px 0;"></div>
            </div>

            <div style="margin-bottom: 6px;">
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Booking ID:</span>
                    <span style="font-weight: bold;">#{{ $paymentCollectBooking->id }}</span>
                </div>
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Issued Date:</span>
                    <span>{{ now()->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Status:</span>
                    <span style="font-weight: bold; text-transform: uppercase;">{{ $paymentCollectBooking->status }}</span>
                </div>
            </div>

            <div style="border-bottom: 1px dashed #000; margin: 8px 0;"></div>

            <div style="margin-bottom: 6px;">
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Player Name:</span>
                    <span style="font-weight: bold;">{{ $paymentCollectBooking->user_name }}</span>
                </div>
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Phone Number:</span>
                    <span style="font-weight: bold;">{{ $paymentCollectBooking->user_number }}</span>
                </div>
            </div>

            <div style="border-bottom: 1px dashed #000; margin: 8px 0;"></div>

            <div style="margin-bottom: 6px;">
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Sport Activity:</span>
                    <span style="font-weight: bold;">{{ $paymentCollectBooking->game_name }}</span>
                </div>
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Court / Slot:</span>
                    <span style="font-weight: bold;">Court {{ $paymentCollectBooking->court_number }}</span>
                </div>
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Play Date:</span>
                    <span style="font-weight: bold;">{{ \Carbon\Carbon::parse($paymentCollectBooking->booking_date)->format('l, M d, Y') }}</span>
                </div>
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Play Time:</span>
                    <span style="font-weight: bold;">{{ \Carbon\Carbon::parse($paymentCollectBooking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($paymentCollectBooking->end_time)->format('h:i A') }}</span>
                </div>
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Duration:</span>
                    <span style="font-weight: bold;">
                        @if($paymentCollectBooking->duration >= 60)
                            {{ number_format($paymentCollectBooking->duration / 60, 1) }} hour(s)
                        @else
                            {{ $paymentCollectBooking->duration ?: 60 }} mins
                        @endif
                    </span>
                </div>
            </div>

            <div style="border-bottom: 1px dashed #000; margin: 8px 0;"></div>

            <div style="margin-bottom: 6px;">
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Payment Mode:</span>
                    <span style="font-weight: bold; text-transform: uppercase;">{{ $paymentCollectBooking->payment_method ?: $paymentCollectMethod }}</span>
                </div>
                <div class="flex-between" style="display: flex; justify-content: space-between;">
                    <span>Payment Status:</span>
                    <span style="font-weight: bold; text-transform: uppercase;">{{ $paymentCollectBooking->payment_status ?: 'Paid' }}</span>
                </div>
            </div>

            <div style="border-top: 1px solid #000; border-bottom: 1px solid #000; margin: 8px 0; padding: 6px 0;">
                <div class="flex-between" style="display: flex; justify-content: space-between; font-size: 14px; font-weight: bold;">
                    <span>TOTAL PAID:</span>
                    <span>LKR {{ number_format($paymentCollectBooking->price ?: 0, 2) }}</span>
                </div>
            </div>

            <div style="text-align: center; margin-top: 15px; font-size: 10px; color: #555;">
                <div>Thank you for playing with us!</div>
                <div style="margin-top: 2px;">Please bring this receipt for admission checks.</div>
            </div>
        </div>
    </div>

    @endif
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function printPaymentReceipt() {
        var receiptEl = document.getElementById('printable-payment-receipt');
        if (!receiptEl) {
            alert('Receipt not ready. Please try again.');
            return;
        }
        var printContents = receiptEl.innerHTML;
        var popupWin = window.open('', '_blank', 'width=450,height=600');
        if (!popupWin || popupWin.closed || typeof popupWin.closed == 'undefined') {
            alert('Popup blocked. Please allow popups for this site.');
            return;
        }
        popupWin.document.open();
        popupWin.document.write(`
            <html>
                <head>
                    <title>Booking Receipt</title>
                    <style>
                        body {
                            font-family: 'Courier New', monospace;
                            padding: 20px;
                            color: #000;
                            background-color: #fff;
                        }
                        .flex-between {
                            display: flex;
                            justify-content: space-between;
                        }
                        @media print {
                            body {
                                margin: 0;
                                padding: 10px;
                            }
                            @page {
                                size: auto;
                                margin: 0mm;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${printContents}
                </body>
            </html>
        `);
        popupWin.document.close();
        popupWin.focus();
        popupWin.print();
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Safely handle data from backend
        let sportData = @json($bookingdetails ?? []);
        const gamesConfig = @json($games ?? []);
        const complexId = @json($complex_id ?? null);
        const openingHours = @json($opening_hours ?? []);

        // Feature #9: Blocked slots per sport { sport_id: { date: { time: { court: reason } } } }
        let blockedSlotsData = @json(
            ($sports ?? collect())->mapWithKeys(function($sport) {
                $bs = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
                return [$sport->id => $bs];
            })->toArray()
        );
        // Map sport name -> sport_id for quick lookup
        const gameNameToId = @json(
            ($sports ?? collect())->mapWithKeys(function($sport) {
                return [strtolower($sport->name) => $sport->id];
            })->toArray()
        );
        window.maxCapacityMap = @json($maxCapacityMap ?? []);

        // Debug - log what we received
        console.log('=== DATA RECEIVED FROM BACKEND ===');
        console.log('gamesConfig:', gamesConfig);
        console.log('gamesConfig length:', gamesConfig ? gamesConfig.length : 0);
        console.log('sportData:', sportData);
        console.log('complexId:', complexId);
        console.log('==================================');

        // Global variables
        let currentGame = '';
        let currentDate = new Date();
        let activeTimers = {};
        let activeModalTimerId = null;

        // Initialize system
        initializeSystem();

        function initializeSystem() {
            console.log('Initializing booking system...', {
                sportData,
                gamesConfig
            });

            // Hide any stuck loading overlays
            const loadingOverlay = document.querySelector('.loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }

            // Set active game tab - always set to first game on init
            if (gamesConfig && gamesConfig.length > 0) {
                currentGame = gamesConfig[0].name.toLowerCase();
                console.log('Current game set to:', currentGame);
            } else {
                console.error('No games configured!');
            }

            // If all today's slots have passed, auto-advance to tomorrow
            const todaySlots = generateTimeSlots(true);
            if (todaySlots.length > 0) {
                const now = new Date();
                // Advance after the last slot ENDS (last slot start + 1 hour)
                const lastSlotHour = parseInt(todaySlots[todaySlots.length - 1].time24.split(':')[0], 10);
                const lastSlotEnds = new Date(now.getFullYear(), now.getMonth(), now.getDate(), lastSlotHour + 1, 0);
                if (now >= lastSlotEnds) {
                    // All slots have ended — show tomorrow
                    currentDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
                }
            } else {
                // Venue is closed today — show tomorrow
                const now = new Date();
                currentDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
            }

            updateCalendar();

            setupEventListeners();
            setupLivewireListeners(); // Listen for Livewire events
        }

        // Setup Livewire event listeners for real-time updates
        function setupLivewireListeners() {
            // Refresh blocked slots after a block/unblock action (instant, no page reload needed)
            window.addEventListener('refreshBlockedSlots', function() {
                @this.call('getBlockedSlotsData').then(function(newData) {
                    blockedSlotsData = newData || {};
                    updateCalendar();
                });
            });

            // Listen for notification events dispatched from Livewire
            window.addEventListener('notify', (event) => {
                const data = event.detail[0] || event.detail;
                if (data && data.title && data.message) {
                    showNotification(data.title, data.message);
                }
            });

            console.log('✅ Livewire listeners initialized for real-time updates');
        }

        // Show notification for new bookings
        function showNotification(type, title, message, duration = 8000) {
            // Handle 2-argument calls: showNotification(title, message)
            if (arguments.length === 2) {
                message = title;
                title = type;
                type = 'success';
            }

            // Remove any existing notifications first to prevent stacking
            const existingNotifications = document.querySelectorAll('.booking-notification');
            existingNotifications.forEach(notif => notif.remove());

            // Map type to icon and alert class
            const iconClass = {
                'success': 'fas fa-check-circle',
                'info': 'fas fa-info-circle',
                'warning': 'fas fa-exclamation-triangle',
                'danger': 'fas fa-times-circle'
            }[type] || 'fas fa-bell';

            const alertClass = ['success', 'info', 'warning', 'danger'].includes(type) ? type : 'success';

            // Create a toast notification at the top
            const toastHtml = `
            <div class="booking-notification alert alert-${alertClass} alert-dismissible fade show position-fixed shadow-lg" 
                 style="top: 20px; right: 20px; z-index: 99999; min-width: 350px; max-width: 450px; animation: slideInRight 0.3s ease-out;" 
                 role="alert">
                <div class="d-flex align-items-start">
                    <i class="${iconClass} fa-2x me-3 ${type === 'success' ? 'text-success' : ''}"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1"><strong>${title}</strong></h5>
                        <p class="mb-0">${message}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        `;

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = toastHtml;
            const notification = tempDiv.firstElementChild;
            document.body.appendChild(notification);

            // Play notification sound
            playNotificationSound();

            // Auto-remove after duration
            setTimeout(() => {
                if (notification && notification.parentNode) {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }
            }, duration);
        } // Optional: Play notification sound
        function playNotificationSound() {
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiDcJGWi77eeaTRAKT6fj8LRgGwc4kdfy0HotBSd4yPDekj4KE12y6OynUxINR6Hh8rsrIQUsgs/y24c5CBpruuvm');
                audio.play().catch(e => console.log('Audio play failed:', e));
            } catch (error) {
                console.log('Notification sound unavailable');
            }
        }

        // Check for No-Shows periodically
        function checkNoShows() {
            @this.call('checkNoShows').then(count => {
                if (count > 0) {
                    console.log(`⚠️ ${count} booking(s) marked as No-Show`);
                    refreshBookingData().then(() => {
                        updateCalendar();
                    });
                }
            }).catch(error => {
                console.error('Error checking No-Shows:', error);
            });
        }

        // Check for No-Shows every 2 minutes
        setInterval(checkNoShows, 120000);
        // Also check on page load
        setTimeout(checkNoShows, 5000);

        function setupEventListeners() {
            // Game tabs
            document.querySelectorAll('.game-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.game-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentGame = this.dataset.game.toLowerCase();
                    console.log('Game tab clicked:', currentGame);
                    updateCalendar();
                });
            });

            // Date navigation
            const prevBtn = document.getElementById('prevDay');
            const nextBtn = document.getElementById('nextDay');
            const datePickerInput = document.getElementById('datePickerInput');

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

            if (datePickerInput) {
                datePickerInput.addEventListener('change', function(e) {
                    if (e.target.value) {
                        const parts = e.target.value.split('-');
                        currentDate = new Date(parts[0], parts[1] - 1, parts[2]);
                        console.log('Date selected from picker:', formatDate(currentDate));
                        updateCalendar();
                    }
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

            // Booked slot clicks (for timer) — skip completed/played slots
            document.addEventListener('click', function(e) {
                if (e.target.closest('.time-slot.booked')) {
                    const slot = e.target.closest('.time-slot.booked');
                    // Skip completed slots — they use their own onclick for payment modal
                    if (slot.dataset.completed === 'true') return;
                    e.preventDefault();
                    const timerId = slot.dataset.timerId;
                    if (timerId) {
                        openTimerModal(timerId);
                    }
                }
            });

            // New window button for timer popup — also starts the timer
            const setupNewWindowBtn = document.getElementById('newWindowBtn');
            if (setupNewWindowBtn) {
                setupNewWindowBtn.addEventListener('click', function() {
                    if (activeModalTimerId) {
                        const timerState = activeTimers[activeModalTimerId];
                        if (timerState) {
                            // Start the timer countdown if not already running
                            if (!timerState.isRunning) {
                                startTimer(activeModalTimerId);
                            }

                            // Update modal UI to show Running Controls
                            const startBtn = document.getElementById('newWindowBtn');
                            const runningCtrl = document.getElementById('modalRunningControls');
                            const cancelBtnEl = document.getElementById('cancelBookingBtn');
                            const statusBadge = document.getElementById('modalStatusBadge');
                            if (startBtn) startBtn.classList.add('d-none');
                            if (runningCtrl) runningCtrl.classList.remove('d-none');
                            if (cancelBtnEl) cancelBtnEl.classList.add('d-none');
                            if (statusBadge) {
                                statusBadge.textContent = '▶ Playing';
                                statusBadge.className = 'badge bg-primary';
                            }

                            // Close old popup if any
                            if (timerState.popupWindow && !timerState.popupWindow.closed) {
                                timerState.popupWindow.close();
                                timerState.popupWindow = null;
                            }

                            const newWindow = window.open(
                                '/staff/timer_window.html',
                                activeModalTimerId,
                                'width=500,height=500,resizable=yes,scrollbars=no'
                            );

                            if (newWindow) {
                                timerState.popupWindow = newWindow;
                                newWindow.onload = () => {
                                    if (newWindow.receiveTimerData) {
                                        newWindow.receiveTimerData({
                                            timerId: activeModalTimerId,
                                            totalDuration: timerState.totalDuration,
                                            remaining: timerState.remaining,
                                            isRunning: true,
                                            player: timerState.player,
                                            game: timerState.game,
                                            startTimeDisplay: timerState.startTimeDisplay,
                                            bookingId: timerState.bookingId,
                                            court: timerState.court
                                        });
                                    }
                                };
                            } else {
                                Swal.fire('Popup Blocked', 'Please allow popups for this site to use the timer window.', 'warning');
                            }

                            // Automatically close the Booking Details modal
                            closeModal('timerModal');
                        }
                    }
                });
            }

            // Livewire events
            window.addEventListener('bookingCreated', function(event) {
                console.log('Booking created successfully', event.detail);
                closeModal('bookingModal');
                showNotification('✅ Success', 'Booking created successfully!');
                refreshBookingData().then(() => {
                    updateCalendar();
                }).catch(error => {
                    console.error('Error refreshing after booking:', error);
                    // Force page refresh as fallback
                    location.reload();
                });
            });

            window.addEventListener('closeModal', function() {
                console.log('closeModal event received');
                closeModal('bookingModal');
                refreshBookingData().then(() => {
                    updateCalendar();
                });
            });

            $('#bookingModal').on('hidden.bs.modal', function() {
                console.log('Booking modal hidden');
                updateCalendar();
            });

            // Listen for new booking detected event (from polling)
            window.addEventListener('newBookingDetected', (event) => {
                console.log('New booking detected from database!', event.detail);

                const booking = event.detail[0]; // Get booking data

                // Show notification
                showNotification(
                    '🔔 New Booking!',
                    `${booking.user_name} booked ${booking.game_name} - Court ${booking.court_number}<br>
                Date: ${booking.booking_date} | Time: ${booking.start_time}`
                );

                // Refresh the booking data and update calendar
                refreshBookingData().then(() => {
                    updateCalendar();
                });
            });

            // Listen for booking cancelled event
            window.addEventListener('bookingCancelled', () => {
                console.log('Booking cancelled - refresh UI');
                refreshBookingData().then(() => {
                    updateCalendar();
                });
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
                    Swal.fire('Error', 'Error refreshing booking data. Please try again.', 'error');
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
                console.error('Missing required data for booking modal:', {
                    currentGame,
                    time,
                    display,
                    court,
                    dateKey
                });
                Swal.fire('Error', 'Unable to open booking modal. Missing required information.', 'error');
                return;
            }

            console.log('Opening booking modal with data:', {
                game: currentGame,
                dateKey,
                time,
                court,
                display
            });

            const modalElement = document.getElementById('bookingModal');
            if (!modalElement) {
                console.error('Booking modal element not found');
                Swal.fire('Error', 'Booking modal not found.', 'error');
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

            @this.setSelectedBookingData({
                game: currentGame,
                dateKey,
                time: formattedTime,
                court
            }).then(() => {
                console.log('setSelectedBookingData resolved');
                modal.show();
            }).catch(error => {
                console.error('Error setting booking data:', error);
                Swal.fire('Error', 'Error opening booking form. Please try again.', 'error');
                updateCalendar();
            });
        }

        window.triggerCancelBookingJS = function(bookingId, playerName) {
            Swal.fire({
                title: 'Cancel Booking?',
                text: `Are you sure you want to cancel booking for ${playerName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Cancel Booking'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('cancelBooking', bookingId);
                }
            });
        };

        window.getCalculatedRemainingSeconds = function(timerId, dateKey, rawTime, totalDurationSecs) {
            totalDurationSecs = totalDurationSecs || 3600;
            const savedStart = localStorage.getItem('timer_started_at_' + timerId);
            if (savedStart) {
                const elapsed = Math.floor((Date.now() - parseInt(savedStart, 10)) / 1000);
                return Math.max(0, totalDurationSecs - elapsed);
            }
            if (dateKey && rawTime) {
                const dateParts = dateKey.split('-');
                const timeParts = rawTime.split(':');
                if (dateParts.length >= 3 && timeParts.length >= 2) {
                    const now = new Date();
                    const yearPart = parseInt(dateParts[0], 10);
                    const monthPart = parseInt(dateParts[1], 10) - 1;
                    const dayPart = parseInt(dateParts[2], 10);
                    const hourPart = parseInt(timeParts[0], 10);
                    const minPart = parseInt(timeParts[1], 10);
                    const slotStart = new Date(yearPart, monthPart, dayPart, hourPart, minPart, 0);
                    const slotEnd = new Date(slotStart.getTime() + totalDurationSecs * 1000);

                    if (now >= slotStart && now < slotEnd) {
                        return Math.max(0, Math.floor((slotEnd.getTime() - now.getTime()) / 1000));
                    }
                }
            }
            return totalDurationSecs;
        };

        window.startTimerDirectJS = function(timerId, bookingId) {
            if (!localStorage.getItem('timer_started_at_' + timerId)) {
                localStorage.setItem('timer_started_at_' + timerId, Date.now().toString());
            }
            @this.call('startBooking', bookingId).then(() => {
                if (activeTimers[timerId]) {
                    activeTimers[timerId].isRunning = true;
                    activeTimers[timerId].status = 'Playing';
                }
                showNotification('▶ Match Started', 'Match timer has been started successfully.');
                refreshBookingData().then(() => {
                    updateCalendar();
                    if (activeTimers[timerId]) {
                        startTimer(timerId);
                    }
                });
            });
        };

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
                popupWindow: null,
                bookingId: null,
                court: 'N/A',
                date: formatDate(currentDate),
                status: 'Confirmed'
            };

            // Prevent opening modal for No-Show bookings
            if (timerState.status === 'No-Show') {
                console.log('Cannot open timer for No-Show booking');
                showNotification('No-Show Booking', 'This booking is marked as No-Show and cannot be started.');
                return;
            }

            const modalElement = document.getElementById('timerModal');
            if (!modalElement) {
                console.error('Timer modal element not found');
                Swal.fire('Error', 'Timer modal not found.', 'error');
                return;
            }

            // Calculate real remaining time from wall clock
            const now = new Date();
            let realRemaining = timerState.remaining;
            if (timerState.dateKey && timerState.rawTime) {
                const dateParts = timerState.dateKey.split('-');
                const timeParts = timerState.rawTime.split(':');
                if (dateParts.length >= 3 && timeParts.length >= 2) {
                    const yearPart = parseInt(dateParts[0], 10);
                    const monthPart = parseInt(dateParts[1], 10) - 1;
                    const dayPart = parseInt(dateParts[2], 10);
                    const hourPart = parseInt(timeParts[0], 10);
                    const minPart = parseInt(timeParts[1], 10);
                    const slotStart = new Date(yearPart, monthPart, dayPart, hourPart, minPart, 0);
                    const slotEnd = new Date(slotStart.getTime() + timerState.totalDuration * 1000);
                    realRemaining = Math.max(0, Math.floor((slotEnd.getTime() - now.getTime()) / 1000));
                }
            }
            
            // Only update remaining if the timer is Playing and we have a valid calculation
            if (timerState.isRunning || timerState.status === 'Playing') {
                timerState.remaining = realRemaining;
            }

            const modal = new bootstrap.Modal(modalElement);
            document.getElementById('modalTimerPlayerName').textContent = timerState.player || 'N/A';
            document.getElementById('modalTimerGameName').textContent = timerState.game || 'N/A';
            document.getElementById('modalTimerCourt').textContent = timerState.court || 'N/A';
            document.getElementById('modalTimerDate').textContent = timerState.date || 'N/A';
            document.getElementById('modalTimerGameStartTime').textContent = timerState.startTimeDisplay || 'N/A';
            document.getElementById('modalTimer').textContent = formatTime(timerState.remaining);

            // Update status badge
            const statusBadgeEl = document.getElementById('modalStatusBadge');
            if (statusBadgeEl) {
                const isPlaying = timerState.isRunning || timerState.status === 'Playing';
                if (isPlaying) {
                    statusBadgeEl.textContent = '▶ Playing';
                    statusBadgeEl.className = 'badge bg-primary';
                    statusBadgeEl.style.fontSize = '0.85rem';
                } else {
                    statusBadgeEl.textContent = timerState.status || 'Confirmed';
                    statusBadgeEl.className = 'badge bg-success';
                    statusBadgeEl.style.fontSize = '0.85rem';
                }
            }

            // Toggle Start Timer vs Running Controls visibility
            const newWindowBtn = document.getElementById('newWindowBtn');
            const runningControls = document.getElementById('modalRunningControls');
            const isPlaying = timerState.isRunning || timerState.status === 'Playing';
            
            if (isPlaying) {
                // Already playing — hide Start Timer, show Running Controls
                if (newWindowBtn) newWindowBtn.classList.add('d-none');
                if (runningControls) runningControls.classList.remove('d-none');
            } else {
                // Not playing — show Start Timer, hide Running Controls
                if (newWindowBtn) newWindowBtn.classList.remove('d-none');
                if (runningControls) runningControls.classList.add('d-none');
            }

            if (!activeTimers[timerId]) {
                activeTimers[timerId] = timerState;
            }

            // Setup cancel booking button
            const cancelBtn = document.getElementById('cancelBookingBtn');
            if (cancelBtn) {
                // Remove old event listeners
                const newCancelBtn = cancelBtn.cloneNode(true);
                cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

                // Hide cancel button if already Playing
                if (isPlaying) {
                    newCancelBtn.classList.add('d-none');
                } else {
                    newCancelBtn.classList.remove('d-none');
                }

                newCancelBtn.addEventListener('click', function() {
                    @this.set('selectedGame', timerState.game || '');
                    @this.set('selectedDate', timerState.dateKey || '');
                    @this.set('selectedCourt', timerState.court || '');
                    @this.set('selectedTime', timerState.rawTime || '');

                    @this.call('initiateCancelBooking', timerState.bookingId).then((res) => {
                        modal.hide();
                        if (res === true) {
                            showNotification('Cancelled', 'Booking cancelled successfully.');
                            refreshBookingData().then(() => updateCalendar());
                        }
                    }).catch(error => {
                        console.error('Error initiating cancel booking:', error);
                    });
                });
            }

            // Setup Pause/Resume button
            const pauseBtn = document.getElementById('modalPauseBtn');
            if (pauseBtn) {
                const newPauseBtn = pauseBtn.cloneNode(true);
                pauseBtn.parentNode.replaceChild(newPauseBtn, pauseBtn);
                
                // Set initial label
                if (timerState.isRunning) {
                    newPauseBtn.innerHTML = '<i class="fas fa-pause me-1"></i>Pause';
                } else {
                    newPauseBtn.innerHTML = '<i class="fas fa-play me-1"></i>Resume';
                }

                newPauseBtn.addEventListener('click', function() {
                    if (timerState.isRunning) {
                        pauseTimer(timerId);
                        this.innerHTML = '<i class="fas fa-play me-1"></i>Resume';
                    } else {
                        startTimer(timerId);
                        this.innerHTML = '<i class="fas fa-pause me-1"></i>Pause';
                    }
                });
            }

            // Setup Force Complete button
            const forceCompleteBtn = document.getElementById('modalForceCompleteBtn');
            if (forceCompleteBtn) {
                const newForceBtn = forceCompleteBtn.cloneNode(true);
                forceCompleteBtn.parentNode.replaceChild(newForceBtn, forceCompleteBtn);

                newForceBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Force Complete?',
                        text: "Mark this booking as completed now?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, complete it!',
                        cancelButtonText: 'No, keep playing'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Stop the timer
                            if (timerState.intervalId) {
                                clearInterval(timerState.intervalId);
                                timerState.intervalId = null;
                            }
                            timerState.isRunning = false;
                            timerState.remaining = 0;

                            // Mark as played in DB
                            @this.call('markAsPlayed', timerState.bookingId).then(success => {
                                if (success) {
                                    timerState.status = 'played';
                                    modal.hide();
                                    showNotification('✅ Session Complete', `${timerState.player}'s session has been force-completed.`);
                                    refreshBookingData().then(() => updateCalendar());

                                    // Notify popup window if open
                                    if (timerState.popupWindow && !timerState.popupWindow.closed) {
                                        try { timerState.popupWindow.onTimerComplete && timerState.popupWindow.onTimerComplete(); } catch(e) {}
                                    }
                                }
                            }).catch(error => {
                                console.error('Error force-completing booking:', error);
                                Swal.fire('Error!', 'Could not complete booking. Please try again.', 'error');
                            });
                        }
                    });
                });
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
            const calendarBodyEl  = document.getElementById('calendarBody');
            const calendarHeaderEl = document.getElementById('calendarHeader');
            if (!calendarBodyEl || !calendarHeaderEl) {
                console.warn('Calendar DOM not ready yet, skipping update.');
                return;
            }
            if (!currentGame) {
                console.warn('No current game selected, skipping calendar update.');
                calendarBodyEl.innerHTML = `
                <tr>
                    <td colspan="100%" class="text-center py-4">
                        <i class="fas fa-info-circle me-2"></i>Please select a game to view the calendar.
                    </td>
                </tr>
            `;
                calendarHeaderEl.innerHTML = '<th class="time-column">Time</th>';
                return;
            }

            console.log('Updating calendar for:', currentGame, formatDateKey(currentDate));

            const dateKey = formatDateKey(currentDate);
            
            // FIX: Try both lowercase and exact case for game name
            let gameData = (sportData[currentGame] ? sportData[currentGame][dateKey] : undefined);
            if (!gameData) {
                // Try lowercase version
                const lowerGame = currentGame.toLowerCase();
                gameData = (sportData[lowerGame] ? sportData[lowerGame][dateKey] : undefined) || {};
                console.log('Using lowercase game key:', lowerGame);
            } else {
                console.log('Using exact game key:', currentGame);
            }

            console.log('DEBUG - dateKey:', dateKey);
            console.log('DEBUG - gameData:', gameData);
            console.log('DEBUG - sportData keys:', Object.keys(sportData));
            console.log('DEBUG - currentGame:', currentGame);

            let numberOfCourts = 3;
            const currentGameObject = gamesConfig.find(game => {
                return game.name.toLowerCase() === currentGame.toLowerCase();
            });

            console.log('DEBUG - currentGameObject:', currentGameObject);

            if (currentGameObject && currentGameObject.maximum_court) {
                numberOfCourts = currentGameObject.maximum_court;
                console.log('DEBUG - numberOfCourts from game:', numberOfCourts);
            } else {
                console.log('DEBUG - Using default numberOfCourts:', numberOfCourts);
            }

            const courts = Array.from({
                length: numberOfCourts
            }, (_, i) => `${i + 1}`);

            console.log('DEBUG - courts array:', courts);

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

            console.log('DEBUG - headerHtml:', headerHtml);

            calendarHeaderEl.innerHTML = headerHtml;

            let bodyHtml = '';
            const timeSlots = generateTimeSlots();
            const skipMap = {};

            console.log('DEBUG - timeSlots generated:', timeSlots.length);

            if (!timeSlots || timeSlots.length === 0) {
                // Venue closed today or no available slots
                calendarBodyEl.innerHTML = `
                    <tr>
                        <td colspan="${numberOfCourts + 1}" class="text-center py-4">
                            <i class="fas fa-times-circle me-2"></i>
                            No available slots for ${formatDate(currentDate)} (venue may be closed)
                        </td>
                    </tr>`;
                const currentDateEl = document.getElementById('currentDate');
                if (currentDateEl) currentDateEl.textContent = formatDate(currentDate);
                return;
            }

            timeSlots.forEach((slot, slotIndex) => {
                // Use compact time on mobile (e.g. "9AM" instead of "9:00 AM - 10:00 AM")
                const isMobile = window.innerWidth <= 768;
                const displayTime = isMobile ? slot.compact : slot.display;
                bodyHtml += `<tr><td class="time-column">${displayTime}</td>`;

                courts.forEach(court => {
                    if (skipMap[court] > 0) {
                        skipMap[court]--;
                        return; // Skip rendering <td> for this court as it is spanned by a previous row's rowspan
                    }

                    const bookings = gameData[court] || {};
                    let bookingInfo = bookings[slot.time24.substring(0, 5) + ':00'];

                    // Ignore cancelled bookings so they render as Available slots
                    if (bookingInfo && bookingInfo.status && bookingInfo.status.toLowerCase() === 'cancelled') {
                        bookingInfo = null;
                    }

                    // Define sportId here so it's available in both booked and available branches
                    const sportId = gameNameToId[currentGame] || gameNameToId[currentGame.toLowerCase()] || null;

                    const currentGameKey = currentGame.toLowerCase();
                    const maxCap = (window.maxCapacityMap && window.maxCapacityMap[currentGameKey])
                        ? window.maxCapacityMap[currentGameKey]
                        : (currentGameKey === 'pools' || currentGameKey === 'pool' ? 10 : 1);

                    // Multi-person capacity handling for Pool / Capacity sports
                    if (bookingInfo && maxCap > 1) {
                        const totPersons = bookingInfo.total_persons || bookingInfo.num_persons || 1;
                        const bList = bookingInfo.bookings_list || [bookingInfo];
                        const remainingSeats = maxCap - totPersons;

                        // Register activeTimers for each booking in the capacity list
                        bList.forEach(b => {
                            const timerId = `${currentGame}-${dateKey}-${court.replace(/\s/g, '')}-${slot.time24.replace(/:/g, '-')}-${b.id}`;
                            const isPlayingNow = (b.status === 'Playing') || (activeTimers[timerId] && activeTimers[timerId].isRunning);

                            if (!activeTimers[timerId]) {
                                activeTimers[timerId] = {
                                    totalDuration: 3600,
                                    remaining: 3600,
                                    intervalId: null,
                                    isRunning: isPlayingNow,
                                    player: b.player,
                                    game: currentGame.charAt(0).toUpperCase() + currentGame.slice(1),
                                    startTimeDisplay: slot.display,
                                    popupWindow: null,
                                    bookingId: b.id,
                                    court: court,
                                    date: formatDate(currentDate),
                                    dateKey: dateKey,
                                    rawTime: slot.time24,
                                    status: b.status || 'Confirmed'
                                };
                            } else {
                                if (isPlayingNow) {
                                    activeTimers[timerId].isRunning = true;
                                    activeTimers[timerId].status = 'Playing';
                                }
                            }

                            // Auto start countdown if status is Playing
                            if (isPlayingNow && !activeTimers[timerId].intervalId) {
                                startTimer(timerId);
                            }
                        });

                        let customerPillsHtml = '';
                        bList.forEach(b => {
                            const timerId = `${currentGame}-${dateKey}-${court.replace(/\s/g, '')}-${slot.time24.replace(/:/g, '-')}-${b.id}`;
                            const isPlaying = b.status === 'Playing';
                            const isCancelled = b.status === 'Cancelled';
                            if (isCancelled) return;

                            const timerStatus = activeTimers[timerId] ? activeTimers[timerId].status : b.status;
                            const isPlayingNow = isPlaying || timerStatus === 'Playing' || (activeTimers[timerId] && activeTimers[timerId].isRunning);
                            const currentSecs = activeTimers[timerId] ? activeTimers[timerId].remaining : 3600;

                            customerPillsHtml += `
                                <div class="d-inline-flex align-items-center bg-white border border-secondary border-opacity-25 rounded px-2 py-1 me-1 mb-1 shadow-sm" style="font-size:0.75rem;">
                                    <span class="fw-bold text-dark me-1 cursor-pointer" onclick="event.stopPropagation(); openTimerModal('${timerId}')" title="Click to open Timer & Match Status Modal">
                                        <i class="fas fa-stopwatch ${isPlayingNow ? 'text-primary' : 'text-secondary'} me-1"></i>${b.player} (${b.num_persons}p)
                                    </span>
                                    ${!isPlayingNow ? `
                                    <button type="button" class="btn btn-sm btn-success p-0 ms-1 px-1 me-1 text-white border-0 shadow-sm" style="font-size:0.68rem; line-height:1.2;"
                                            onclick="event.stopPropagation(); startTimerDirectJS('${timerId}', ${b.id})" title="Start Timer for ${b.player}">
                                        <i class="fas fa-play" style="font-size:0.62rem;"></i> Start
                                    </button>` : `
                                    <span id="${timerId}" class="badge bg-primary text-white font-monospace ms-1 me-1 shadow-sm cursor-pointer" style="font-size:0.72rem; font-weight:700;" onclick="event.stopPropagation(); openTimerModal('${timerId}')" title="Click to open timer modal">
                                        <i class="fas fa-play me-1"></i>${formatTime(currentSecs)}
                                    </span>`}
                                    <button type="button" class="btn btn-sm btn-outline-danger p-0 ms-1 border-0" style="line-height:1; padding:0 3px !important;"
                                            onclick="event.stopPropagation(); triggerCancelBookingJS(${b.id}, '${b.player}')" title="Cancel ${b.player}'s booking">
                                        <i class="fas fa-times" style="font-size:0.7rem;"></i>
                                    </button>
                                </div>
                            `;
                        });

                        if (remainingSeats > 0) {
                            // Partially Booked Slot
                            bodyHtml += `
                            <td>
                                <div class="time-slot available border border-info bg-info bg-opacity-10 p-2 rounded shadow-sm text-start"
                                     data-time="${slot.time24.substring(0, 8)}"
                                     data-display="${slot.display}"
                                     data-court="${court}">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-info text-dark fw-bold" style="font-size:0.75rem;">${totPersons}/${maxCap} Booked</span>
                                        <span class="badge bg-success text-white fw-bold" style="font-size:0.75rem;">${remainingSeats} Left</span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center my-1">
                                        ${customerPillsHtml}
                                    </div>
                                    <div class="d-flex gap-1 mt-1 justify-content-between align-items-center" onclick="event.stopPropagation()">
                                        <button class="slot-action-btn shadow-sm"
                                                style="background:#0284c7;color:#fff;border-color:#0369a1;padding:3px 8px;font-size:0.72rem;"
                                                onclick="openBookingModalJS('${slot.time24.substring(0, 8)}', '${slot.display}', '${court}')">
                                            <i class="fas fa-plus-circle me-1"></i>Book (${remainingSeats} Left)
                                        </button>
                                        ${sportId ? `
                                        <button class="slot-action-btn" style="background:#0ea5e9;color:#fff;border-color:#0369a1;"
                                                onclick="openWaitlistModalJS(${sportId}, '${dateKey}', '${slot.time24.substring(0, 8)}', '${court}')">
                                            <i class="fas fa-list-ul"></i> Wait
                                        </button>` : ''}
                                    </div>
                                </div>
                            </td>`;
                            return;
                        } else {
                            // Fully Booked Capacity Slot
                            bodyHtml += `
                            <td>
                                <div class="time-slot booked bg-danger bg-opacity-10 border border-danger p-2 rounded shadow-sm text-start">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-danger text-white fw-bold" style="font-size:0.75rem;">FULL (${totPersons}/${maxCap})</span>
                                        <span class="badge bg-dark text-white" style="font-size:0.7rem;">${bList.length} Booking(s)</span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center my-1">
                                        ${customerPillsHtml}
                                    </div>
                                </div>
                            </td>`;
                            return;
                        }
                    }

                    if (bookingInfo) {
                        // Calculate rowspan for consecutive active non-cancelled slots belonging to the same player & status
                        let span = 1;
                        let lastEnd = bookingInfo.end;
                        for (let nextIdx = slotIndex + 1; nextIdx < timeSlots.length; nextIdx++) {
                            const nextSlot = timeSlots[nextIdx];
                            const nextBooking = bookings[nextSlot.time24.substring(0, 5) + ':00'];
                            if (!nextBooking) break;

                            // Stop span if next booking is cancelled
                            if (nextBooking.status && nextBooking.status.toLowerCase() === 'cancelled') break;

                            const isSameUser = (nextBooking.player === bookingInfo.player) &&
                                               (nextBooking.phone === bookingInfo.phone) &&
                                               (nextBooking.status === bookingInfo.status);

                            if (isSameUser) {
                                span++;
                                lastEnd = nextBooking.end;
                            } else {
                                break;
                            }
                        }

                        if (span > 1) {
                            skipMap[court] = span - 1;
                        }

                        const timerId = `${currentGame}-${dateKey}-${court.replace(/\s/g, '')}-${slot.time24.replace(/:/g, '-')}`;
                        const totalDuration = calculateDurationInSeconds(slot.time24, lastEnd);
                        const isNoShow = bookingInfo.status === 'No-Show';
                        const isPlaying = bookingInfo.status === 'Playing';
                        const isCompleted = bookingInfo.status === 'Completed' || bookingInfo.status === 'played' || bookingInfo.status === 'Played';

                        if (!activeTimers[timerId] || activeTimers[timerId].totalDuration !== totalDuration) {
                            activeTimers[timerId] = {
                                totalDuration: totalDuration,
                                remaining: totalDuration,
                                intervalId: null,
                                isRunning: isPlaying, // Auto-start if Playing
                                player: bookingInfo.player,
                                game: currentGame.charAt(0).toUpperCase() + currentGame.slice(1),
                                startTimeDisplay: slot.display,
                                popupWindow: null,
                                bookingId: bookingInfo.id,
                                court: court,
                                date: formatDate(currentDate),
                                status: bookingInfo.status
                            };
                        }

                        const displayTime = formatTime(activeTimers[timerId].remaining);
                        const statusBadge = getStatusBadge(bookingInfo.status);
                        const permanentBadge = bookingInfo.permanent ? '<span class="badge bg-primary ms-1">Permanent</span>' : '';
                        const isRunning = activeTimers[timerId].isRunning || isPlaying;

                        // Don't show timer for No-Show or Completed bookings
                        const hideTimer = isNoShow || isCompleted;
                        const timerDisplay = hideTimer ? '' : `<div id="${timerId}" class="timer-display ${isRunning ? 'timer-running' : ''} fw-bold text-primary">${displayTime}</div>`;
                        
                        // Completed/played slots open payment modal; active slots open timer modal; No-Show is disabled
                        let clickHandler = '';
                        let cursorStyle = 'cursor: not-allowed; opacity: 0.7;';
                        if (isCompleted) {
                            clickHandler = `onclick="openPaymentCollectModal(${bookingInfo.id})"`;
                            cursorStyle = 'cursor: pointer;';
                        } else if (!isNoShow) {
                            clickHandler = `onclick="openTimerModal('${timerId}')"`;
                            cursorStyle = 'cursor: pointer;';
                        }

                        // Different background colors based on status
                        let bgClass = 'bg-light';
                        if (isPlaying) bgClass = 'bg-primary bg-opacity-10 border-primary';
                        if (isNoShow) bgClass = 'bg-secondary bg-opacity-25';
                        if (isCompleted) bgClass = 'bg-info bg-opacity-10';

                        const rowspanAttr = span > 1 ? `rowspan="${span}"` : '';
                        const firstSlotDisplay = slot.display.split(' - ')[0];
                        const lastSlotDisplay = timeSlots[slotIndex + span - 1].display.split(' - ')[1];
                        const overallTimeText = span > 1 ? `${firstSlotDisplay} – ${lastSlotDisplay}` : '';

                        const safeAvatarSrc = (bookingInfo.avatar && bookingInfo.avatar !== '/' && !bookingInfo.avatar.endsWith('/'))
                            ? bookingInfo.avatar
                            : `https://ui-avatars.com/api/?name=${encodeURIComponent(bookingInfo.player)}&background=0ea5e9&color=fff`;

                        if (span > 1) {
                            // Multi-slot continuous container filling 100% height of all spanned slots
                            let subSlotsPillsHtml = '';
                            for (let k = 0; k < span; k++) {
                                const sObj = timeSlots[slotIndex + k];
                                subSlotsPillsHtml += `
                                    <span class="badge bg-white text-dark border border-secondary border-opacity-25 font-monospace shadow-sm" style="font-size: 0.74rem; font-weight: 600; padding: 3px 6px;">
                                        <i class="far fa-clock text-primary me-1"></i>${sObj.display}
                                    </span>
                                `;
                            }

                            bodyHtml += `
                            <td ${rowspanAttr} style="vertical-align: top; padding: 2px; height: 1px;">
                                <div class="time-slot booked d-flex flex-column justify-content-between p-2 rounded shadow-sm ${bgClass} mb-0" data-timer-id="${timerId}" ${isCompleted ? 'data-completed="true"' : ''} ${clickHandler} style="${cursorStyle}; height: calc(100% - 4px); max-height: calc(100% - 4px); overflow: hidden;">
                                    
                                    <!-- Top Header: Player Avatar + Info + Badges -->
                                    <div class="d-flex align-items-center justify-content-between w-100 pb-1 border-bottom border-dark border-opacity-10" style="flex-shrink: 0;">
                                        <div class="d-flex align-items-center flex-shrink-0" style="min-width: 0;">
                                            <img src="${safeAvatarSrc}"
                                                onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(bookingInfo.player)}&background=0ea5e9&color=fff';"
                                                alt=""
                                                class="rounded-circle shadow-sm me-2"
                                                width="28" height="28"
                                                style="object-fit: cover; flex-shrink: 0;">
                                            <div class="booking-info text-start text-truncate" style="padding: 0;">
                                                <strong class="d-inline text-dark text-truncate" style="font-size: 0.82rem;">${bookingInfo.player}</strong>
                                                ${!bookingInfo.admin_comments ? '<i class="fas fa-mobile-alt ms-1 text-primary" style="font-size:0.75rem;" title="Mobile App Booking"></i>' : ''}
                                                <small class="d-block text-muted text-truncate" style="font-size: 0.72rem;">${bookingInfo.phone}</small>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-dark text-white" style="font-size: 0.7rem;">${span}h (${span * 60}m)</span>
                                            ${statusBadge}
                                            ${bookingInfo.permanent_source_id ? `<span class="badge bg-dark text-white">P</span>` : ''}
                                        </div>
                                    </div>

                                    <!-- Middle Section: Time Range & Active Timer & Column-wise Sub-Slots -->
                                    <div class="d-flex flex-column align-items-center justify-content-center my-1 w-100 text-center" style="overflow: hidden;">
                                        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 0.82rem;">
                                                <i class="far fa-calendar-alt text-primary me-1"></i>${overallTimeText}
                                            </span>
                                            ${timerDisplay ? `<span class="fw-bold text-primary ms-1" style="font-size: 0.95rem;">${timerDisplay}</span>` : ''}
                                        </div>

                                        <div class="sub-slots-inline-row d-flex align-items-center justify-content-center flex-wrap gap-1">
                                            ${subSlotsPillsHtml}
                                        </div>
                                    </div>

                                    <!-- Bottom Footer: Action / Waitlist Button -->
                                    <div class="d-flex justify-content-between align-items-center w-100 pt-1 border-top border-dark border-opacity-10" onclick="event.stopPropagation()" style="flex-shrink: 0;">
                                        <small class="text-muted fst-italic" style="font-size: 0.7rem;">Continuous (${span} Slots)</small>
                                        ${sportId ? `
                                        <button class="slot-action-btn"
                                                style="background:#0ea5e9;color:#fff;border-color:#0369a1;padding: 2px 7px; font-size: 0.72rem;"
                                                onclick="openWaitlistModalJS(${sportId}, '${dateKey}', '${slot.time24.substring(0,8)}', '${court}')"
                                                title="View/Add to waitlist">
                                            <i class="fas fa-list-ul me-1"></i> Waitlist
                                        </button>` : ''}
                                    </div>

                                </div>
                            </td>
                            `;
                        } else {
                            // Single-slot standard layout
                            bodyHtml += `
                            <td ${rowspanAttr} style="vertical-align: middle;">
                                <div class="time-slot booked d-flex align-items-center justify-content-between p-2 rounded shadow-sm ${bgClass} mb-0" data-timer-id="${timerId}" ${isCompleted ? 'data-completed="true"' : ''} ${clickHandler} style="${cursorStyle}">
                                    
                                    <!-- Left: Avatar + Booking Info -->
                                    <div class="d-flex align-items-center flex-shrink-0" style="min-width: 0;">
                                        <img src="${safeAvatarSrc}"
                                            onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(bookingInfo.player)}&background=0ea5e9&color=fff';"
                                            alt=""
                                            class="rounded-circle shadow-sm me-2"
                                            width="36" height="36"
                                            style="object-fit: cover; flex-shrink: 0;">
                                        <div class="booking-info text-truncate">
                                            <strong class="d-block text-dark text-truncate">${bookingInfo.player} ${!bookingInfo.admin_comments ? '<i class="fas fa-mobile-alt ms-1 text-primary" title="Mobile App Booking"></i>' : ''}</strong>
                                            <small class="d-block text-muted text-truncate">${bookingInfo.phone}</small>
                                        </div>
                                    </div>

                                    <!-- Center: Status + Timer -->
                                    <div class="d-flex align-items-center justify-content-center gap-2 flex-grow-1 px-2">
                                        <div class="text-center flex-shrink-0">
                                            ${statusBadge}
                                            ${timerDisplay}
                                        </div>
                                    </div>

                                    <!-- Right: Badges + Waitlist -->
                                    <div class="badges d-flex flex-column align-items-center gap-1 flex-shrink-0" onclick="event.stopPropagation()">
                                        ${bookingInfo.permanent_source_id ? `<span class="badge bg-dark text-white">P</span>` : ''}
                                        ${sportId ? `
                                        <button class="slot-action-btn mt-1"
                                                style="background:#0ea5e9;color:#fff;border-color:#0369a1;"
                                                onclick="openWaitlistModalJS(${sportId}, '${dateKey}', '${slot.time24.substring(0,8)}', '${court}')"
                                                title="View/Add to waitlist">
                                            <i class="fas fa-list-ul"></i> Wait
                                        </button>` : ''}
                                    </div>

                                </div>
                            </td>
                            `;
                        }

                    } else {
                        // Check if this time slot has already passed
                        const now = new Date();
                        const slotDateTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(),
                            parseInt(slot.time24.split(':')[0]), parseInt(slot.time24.split(':')[1]));
                        const isPastSlot = slotDateTime < now;

                        // Feature #9: check if slot is blocked
                        const slotTimeKey = slot.time24.substring(0, 8); // HH:mm:ss
                        const rawBlockValue = sportId &&
                            blockedSlotsData[sportId] &&
                            blockedSlotsData[sportId][dateKey] &&
                            blockedSlotsData[sportId][dateKey][slotTimeKey] &&
                            blockedSlotsData[sportId][dateKey][slotTimeKey][court];

                        // Recurring (day-of-week) blocks set in Sport settings:
                        //   blockedSlotsData[sportId]["monday"] = ["09:00:00", ...]
                        const dayOfWeek = new Date(dateKey + 'T00:00:00')
                            .toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
                        const recurringList = sportId && blockedSlotsData[sportId] && Array.isArray(blockedSlotsData[sportId][dayOfWeek])
                            ? blockedSlotsData[sportId][dayOfWeek] : [];
                        const isRecurringBlocked = recurringList.indexOf(slotTimeKey) !== -1;

                        const isBlocked = !!rawBlockValue || isRecurringBlocked;
                        const blockReason = rawBlockValue
                            ? (typeof rawBlockValue === 'object' ? rawBlockValue.reason : rawBlockValue)
                            : (isRecurringBlocked ? 'Recurring block' : null);
                        const blockEndTime = rawBlockValue && typeof rawBlockValue === 'object'
                            ? rawBlockValue.end_time : null;
                        const canUnblockHere = true;

                        // Check if slot is on hold (mobile user in checkout via Django webhook)
                        const isHeld = !!(sportId &&
                            window.heldSlots &&
                            window.heldSlots[sportId] &&
                            window.heldSlots[sportId][dateKey] &&
                            window.heldSlots[sportId][dateKey][slotTimeKey] &&
                            window.heldSlots[sportId][dateKey][slotTimeKey][court]);

                        if (isPastSlot) {
                            // Show past slot as disabled/unavailable
                            bodyHtml += `
                            <td>
                                <div class="time-slot past-slot"
                                     style="background: #e9ecef; border: 2px dashed #adb5bd; color: #6c757d; cursor: not-allowed; opacity: 0.6;">
                                    <i class="fas fa-lock me-2"></i>Time Past
                                </div>
                            </td>`;
                        } else if (isBlocked) {
                            // Show blocked slot.  One-off (date) blocks get an Unblock button here.
                            // Recurring blocks are managed in Settings → Sports.
                            const blockTimeRange = blockEndTime
                                ? `${slot.display.split(' - ')[0]} – ${blockEndTime.substring(0,5)}`
                                : '';
                            const actionHtml = canUnblockHere
                                ? `<button class="slot-action-btn"
                                            style="color:#856404;border-color:#d97706;"
                                            onclick="unblockSlotJS(${sportId}, '${dateKey}', '${slotTimeKey}', '${court}')"
                                            title="Remove block">
                                        <i class="fas fa-unlock me-1"></i>Unblock
                                    </button>`
                                : `<span class="small text-muted fst-italic">Recurring — change in Settings</span>`;
                            bodyHtml += `
                            <td>
                                <div class="time-slot blocked-slot d-flex flex-column align-items-center justify-content-center gap-1">
                                    <div><i class="fas fa-ban me-1"></i><strong>Unavailable</strong></div>
                                    <div class="small text-muted">${blockReason}${blockTimeRange ? ' · ' + blockTimeRange : ''}</div>
                                    ${actionHtml}
                                </div>
                            </td>`;
                        } else if (isHeld) {
                            // Slot is temporarily held by a mobile user in checkout
                            bodyHtml += `
                            <td>
                                <div class="time-slot held-slot d-flex flex-column align-items-center justify-content-center gap-1">
                                    <div><i class="fas fa-hourglass-half me-1"></i><strong>On Hold</strong></div>
                                    <div class="small">Mobile checkout in progress</div>
                                </div>
                            </td>`;
                        } else {
                            // Show available slot as clickable with block + waitlist action buttons
                            bodyHtml += `
                            <td>
                                <div class="time-slot available d-flex flex-column align-items-center justify-content-center gap-1"
                                     data-time="${slotTimeKey}"
                                     data-display="${slot.display}"
                                     data-court="${court}">
                                    <div><i class="fas fa-plus-circle me-1"></i>Available</div>
                                    <div class="d-flex gap-1" onclick="event.stopPropagation()">
                                        ${sportId ? `
                                        <button class="slot-action-btn"
                                                style="background:#f59e0b;color:#fff;border-color:#d97706;"
                                                onclick="openBlockModalJS(${sportId}, '${dateKey}', '${slotTimeKey}', '${court}')"
                                                title="Block this slot">
                                            <i class="fas fa-ban"></i> Block
                                        </button>
                                        <button class="slot-action-btn"
                                                style="background:#0ea5e9;color:#fff;border-color:#0369a1;"
                                                onclick="openWaitlistModalJS(${sportId}, '${dateKey}', '${slotTimeKey}', '${court}')"
                                                title="Add to waitlist">
                                            <i class="fas fa-list-ul"></i> Wait
                                        </button>` : ''}
                                    </div>
                                </div>
                            </td>`;
                        }
                    }
                });

                bodyHtml += '</tr>';
            });

            console.log('DEBUG - Total rows generated:', timeSlots.length);
            console.log('DEBUG - bodyHtml length:', bodyHtml.length);
            console.log('DEBUG - First 200 chars of bodyHtml:', bodyHtml.substring(0, 200));

            calendarBodyEl.innerHTML = bodyHtml;
            const currentDateEl = document.getElementById('currentDate');
            if (currentDateEl) currentDateEl.textContent = formatDate(currentDate);
            const datePickerInput = document.getElementById('datePickerInput');
            if (datePickerInput) datePickerInput.value = formatDateKey(currentDate);

            console.log('DEBUG - Calendar body updated!');

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

        function generateTimeSlots(includePast = false) {
            // Generate slots based on venue opening hours for the currently selected date
            const slots = [];
            const dayName = currentDate.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
            const dayHours = openingHours[dayName] || null;

            if (!dayHours || dayHours.closed || !dayHours.open || !dayHours.close) {
                return slots; // empty -> venue closed or no hours
            }

            // Parse open/close HH:MM
            const [openHStr, openMStr] = (dayHours.open || '00:00').split(':');
            const [closeHStr, closeMStr] = (dayHours.close || '00:00').split(':');
            let openH = parseInt(openHStr, 10);
            const openM = parseInt(openMStr || '0', 10);
            let closeH = parseInt(closeHStr, 10);
            const closeM = parseInt(closeMStr || '0', 10);

            // Handle 00:00 midnight closing time as 24:00 (end of day)
            if (closeH === 0 || (dayHours.close && (dayHours.close === '00:00' || dayHours.close.startsWith('00:00')))) {
                closeH = 24;
            }

            // Round start up if minutes > 0
            if (openM > 0) openH += 1;

            // Last start hour is closeH - 1 if close minutes == 0, else closeH
            let lastStart = closeH - 1;
            if (closeM > 0) {
                // allow a slot starting at closeH if it doesn't exceed close time
                lastStart = closeH;
            }

            if (openH > lastStart) return slots;

            const now = new Date();
            const isToday = currentDate.getFullYear() === now.getFullYear() &&
                            currentDate.getMonth() === now.getMonth() &&
                            currentDate.getDate() === now.getDate();

            for (let hour = openH; hour <= lastStart; hour++) {
                // If viewing today and includePast is false, filter out past slots (where slot end time has passed)
                if (isToday && !includePast) {
                    const slotEndHour = hour + 1;
                    const slotEndDateTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), slotEndHour, 0, 0);
                    if (now >= slotEndDateTime) {
                        continue; // Skip past time slots for today
                    }
                }

                const startHour = hour % 12 || 12;
                const ampm = hour < 12 ? 'AM' : 'PM';
                const nextHour24 = (hour + 1) % 24;
                const nextHour12 = nextHour24 % 12 || 12;
                const nextAmpm = nextHour24 < 12 ? 'AM' : 'PM';

                slots.push({
                    time24: `${hour.toString().padStart(2, '0')}:00:00.000000`,
                    display: `${startHour}:00 ${ampm} - ${nextHour12}:00 ${nextAmpm}`,
                    compact: `${startHour}${ampm.toLowerCase()}`
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
            // Use local date parts to avoid UTC timezone shift (e.g. Asia/Colombo UTC+5:30)
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
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
            let displayText = status;

            switch (status) {
                case 'Confirmed':
                    badgeClass = 'bg-success';
                    break;
                case 'Pending':
                    badgeClass = 'bg-warning text-dark';
                    break;
                case 'Playing':
                    badgeClass = 'bg-primary';
                    displayText = '▶ Playing';
                    break;
                case 'Completed':
                case 'played':
                case 'Played':
                    badgeClass = 'bg-info';
                    displayText = '✓ Completed';
                    break;
                case 'Cancelled':
                    badgeClass = 'bg-danger';
                    break;
                case 'No-Show':
                    badgeClass = 'bg-secondary';
                    displayText = '⏰ Time Passed';
                    break;
                default:
                    badgeClass = 'bg-secondary';
            }
            return `<span class="badge ${badgeClass}">${displayText}</span>`;
        }

        function startTimer(timerIdToControl) {
            const timerState = activeTimers[timerIdToControl];
            if (!timerState || timerState.intervalId) return;

            if (!localStorage.getItem('timer_started_at_' + timerIdToControl)) {
                localStorage.setItem('timer_started_at_' + timerIdToControl, Date.now().toString());
            }

            // Update booking status to Playing in the database
            if (timerState.bookingId && timerState.status !== 'Playing') {
                @this.call('startBooking', timerState.bookingId).then(success => {
                    if (success) {
                        timerState.status = 'Playing';
                        console.log('Booking status updated to Playing');
                        refreshBookingData().then(() => updateCalendar());
                    }
                });
            }

            timerState.isRunning = true;
            updateTimerButtons(true, timerIdToControl);

            timerState.intervalId = setInterval(() => {
                const calcRem = getCalculatedRemainingSeconds(timerIdToControl, timerState.dateKey, timerState.rawTime, timerState.totalDuration);
                timerState.remaining = calcRem;
                updateTimerDisplay(timerIdToControl);

                if (calcRem <= 0) {
                    clearInterval(timerState.intervalId);
                    timerState.intervalId = null;
                    timerState.isRunning = false;
                    updateTimerButtons(false, timerIdToControl);
                    updateTimerDisplay(timerIdToControl);

                    // Auto-mark as played when timer ends
                    if (timerState.bookingId) {
                        @this.call('markAsPlayed', timerState.bookingId).then(success => {
                            if (success) {
                                timerState.status = 'played';
                                showNotification('✅ Session Complete', `${timerState.player}'s session is over — marked as Played.`);
                                refreshBookingData().then(() => updateCalendar());
                                // Notify popup window too
                                if (timerState.popupWindow && !timerState.popupWindow.closed) {
                                    try { timerState.popupWindow.onTimerComplete && timerState.popupWindow.onTimerComplete(); } catch(e) {}
                                }
                            }
                        });
                    }
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

        // Bridge: popup timer window calls this to force-complete a booking
        window.forceCompleteFromPopup = function(timerId, bookingId) {
            const timerState = activeTimers[timerId];
            if (timerState) {
                // Stop the local timer
                if (timerState.intervalId) {
                    clearInterval(timerState.intervalId);
                    timerState.intervalId = null;
                }
                timerState.isRunning = false;
                timerState.remaining = 0;
            }
            // Mark as played in DB
            @this.call('markAsPlayed', bookingId).then(success => {
                if (success) {
                    if (timerState) timerState.status = 'played';
                    showNotification('✅ Session Complete', 'Booking has been force-completed from timer window.');
                    refreshBookingData().then(() => updateCalendar());
                }
            }).catch(error => {
                console.error('Error force-completing from popup:', error);
            });
        };

        // =========================================================
        // Feature #9 + #12: Bridge functions for calendar buttons
        // =========================================================

        // Bridge: open payment collection modal for completed bookings
        window.openPaymentCollectModal = function(bookingId) {
            @this.call('openPaymentCollectModal', bookingId);
        };

        window.openBlockModalJS = function(sportId, date, time, court) {
            @this.call('openBlockModal', sportId, date, time, court).catch(e => console.error('openBlockModal error', e));
        };

        window.unblockSlotJS = function(sportId, date, time, court) {
            Swal.fire({
                title: 'Unblock Slot?',
                text: "Are you sure you want to remove the block from this slot?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, unblock it!',
                cancelButtonText: 'No, keep it blocked'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('unblockSlot', sportId, date, time, court).then(() => {
                        // Refresh blocked data and redraw calendar
                        @this.get('sports').then(sportsArr => {
                            if (sportsArr) {
                                sportsArr.forEach(s => {
                                    const bs = s.blocked_slots ? (typeof s.blocked_slots === 'string' ? JSON.parse(s.blocked_slots) : s.blocked_slots) : {};
                                    blockedSlotsData[s.id] = bs;
                                });
                            }
                            Swal.fire(
                                'Unblocked!',
                                'The slot has been unblocked.',
                                'success'
                            );
                            updateCalendar();
                        }).catch(() => updateCalendar());
                    }).catch(e => {
                        console.error('unblockSlot error', e);
                        Swal.fire(
                            'Error!',
                            'Could not unblock the slot. Please try again.',
                            'error'
                        );
                    });
                }
            });
        };

        window.openWaitlistModalJS = function(sportId, date, time, court) {
            @this.call('openWaitlistModal', sportId, date, time, court).catch(e => console.error('openWaitlistModal error', e));
        };

        // After a slot is blocked, refresh blocked data and redraw
        window.addEventListener('livewire:updated', function() {
            // Re-read blocked slots from Livewire's sports property
            @this.get('sports').then(sportsArr => {
                if (sportsArr && Array.isArray(sportsArr)) {
                    sportsArr.forEach(s => {
                        const bs = s.blocked_slots ? (typeof s.blocked_slots === 'string' ? JSON.parse(s.blocked_slots) : s.blocked_slots) : {};
                        blockedSlotsData[s.id] = bs;
                    });
                }
            }).catch(() => {});
        });

        // ========================================
        // REAL-TIME WEBSOCKET BOOKING UPDATES
        // ========================================
        const channelName = `bookings.complex.${complexId}`;

        // Subscribe to real-time booking updates via WebSocket (Public Channel)
        // Wrapped in try-catch so a connection failure doesn't crash the calendar
        let channel = null;
        try {
            if (window.Echo && complexId) {
                console.log('Subscribing to public channel:', channelName);
                channel = window.Echo.channel(channelName);
                
                // Debug listeners
                channel.on('pusher:subscription_succeeded', () => {
                    console.log('✅ Subscribed to public channel:', channelName);
                });
                
                channel.on('pusher:subscription_error', (status) => {
                    console.error('❌ Failed to subscribe to public channel:', channelName, status);
                });
            }
        } catch(e) {
            console.warn('Echo channel setup failed:', e);
        }

        if (!channel) {
            console.warn('Real-time updates unavailable (Reverb not connected). Calendar still works.');
        }
        // Fallback: if Echo unavailable, make channel a no-op so .listen() calls don't crash
        if (!channel) { channel = { listen: function() { return this; } }; }

        /**
         * Listen for new bookings created from mobile app
         */
        channel.listen('.booking.created', (data) => {
            console.log('📡 RECEIVED BOOKING EVENT:', data);
            // Show toast notification
            showNotification('success', 
                '✨ New Booking',
                `${data.user_name} booked ${data.game_name}`,
                4000
            );

            // Reload the Livewire component to display new booking
            Livewire.dispatch('refreshBookings');
            
            // Refresh calendar after data is loaded
            setTimeout(async () => {
                try {
                    await refreshBookingData();
                    if (typeof updateCalendar === 'function') {
                        updateCalendar();
                    }
                } catch (error) {
                    console.error('Error refreshing data:', error);
                }
            }, 500);
        });

        /**
         * Listen for booking updates (status changes, payment updates, etc.)
         */
        channel.listen('.booking.updated', (data) => {
            // Show toast notification
            showNotification('info',
                '📝 Booking Updated',
                `Booking #${data.id} status: ${data.status}`,
                3000
            );

            // Reload the Livewire component to show updates
            Livewire.dispatch('refreshBookings');
            
            // Refresh calendar after data is loaded
            setTimeout(async () => {
                try {
                    await refreshBookingData();
                    if (typeof updateCalendar === 'function') {
                        updateCalendar();
                    }
                } catch (error) {
                    console.error('Error refreshing data:', error);
                }
            }, 500);
        });

        /**
         * Listen for booking deletions (cancellations)
         */
        channel.listen('.booking.deleted', (data) => {
            // Show toast notification
            showNotification('warning',
                '🗑️  Booking Deleted',
                `Booking #${data.id} has been cancelled`,
                3000
            );

            // Reload the Livewire component to remove deleted booking
            Livewire.dispatch('refreshBookings');
            
            // Refresh calendar after data is loaded
            setTimeout(async () => {
                try {
                    await refreshBookingData();
                    if (typeof updateCalendar === 'function') {
                        updateCalendar();
                    }
                } catch (error) {
                    console.error('Error refreshing data:', error);
                }
            }, 500);
        });

        // ========================================
        // SLOT STATE CHANGES FROM DJANGO WEBHOOK
        // (slot holds, blocks, booking confirmed/cancelled from mobile)
        // ========================================
        window.heldSlots = window.heldSlots || {};
        
        console.log('DEBUG: Number of sports for WebSocket loop:', {{ count($sports ?? []) }});

        @foreach($sports ?? collect() as $sport)
        console.log('DEBUG: Generating Echo listener for sport ID:', {{ $sport->id }});
        // subscribe to slot state changes for sport {{ $sport->id }}
        try {
            if (window.Echo) {
                const sportChannel = `slots.venue.{{ $complex_id }}.sport.{{ $sport->id }}`;
                console.log('Subscribing to sport channel:', sportChannel);
                
                window.Echo.channel(sportChannel).listen('.slot_state_changed', function(data) {
                    console.log('📡 RECEIVED SLOT STATE EVENT:', data);
                    var sportId = data.sport_id;
                    var date    = data.date;
                    var time    = data.start_time;
                    var court   = (data.court !== null && data.court !== undefined) ? data.court : '1';

                    if (!sportId || !date || !time) return;

                    if (data.event_type === 'slot.hold.created') {
                        window.heldSlots[sportId] = window.heldSlots[sportId] || {};
                        window.heldSlots[sportId][date] = window.heldSlots[sportId][date] || {};
                        window.heldSlots[sportId][date][time] = window.heldSlots[sportId][date][time] || {};
                        window.heldSlots[sportId][date][time][court] = true;
                        showNotification('warning', 'Slot On Hold',
                            data.date + ' ' + data.start_time + ' Court ' + court + ' — mobile checkout in progress', 4000);
                        if (typeof updateCalendar === 'function') updateCalendar();

                    } else if (data.event_type === 'slot.hold.released') {
                        if (window.heldSlots[sportId] && window.heldSlots[sportId][date] && window.heldSlots[sportId][date][time]) {
                            delete window.heldSlots[sportId][date][time][court];
                        }
                        if (typeof updateCalendar === 'function') updateCalendar();

                    } else if (data.event_type === 'booking.created') {
                        if (window.heldSlots[sportId] && window.heldSlots[sportId][date] && window.heldSlots[sportId][date][time]) {
                            delete window.heldSlots[sportId][date][time][court];
                        }
                        showNotification('success', 'Booking Confirmed',
                            data.date + ' ' + data.start_time + ' Court ' + court + ' booked via mobile', 4000);
                        
                        refreshBookingData().then(function() {
                            if (typeof updateCalendar === 'function') updateCalendar();
                        });

                    } else if (data.event_type === 'booking.cancelled') {
                        showNotification('warning', 'Booking Cancelled',
                            data.date + ' ' + data.start_time + ' Court ' + court, 3500);
                        
                        refreshBookingData().then(function() {
                            if (typeof updateCalendar === 'function') updateCalendar();
                        });

                    } else if (data.event_type === 'slot.blocked') {
                        showNotification('info', 'Slot Blocked',
                            data.date + ' ' + data.start_time + ' — ' + (data.reason || ''), 3000);
                        Livewire.dispatch('refreshBookings');

                    } else if (data.event_type === 'slot.unblocked') {
                        showNotification('info', 'Slot Unblocked',
                            data.date + ' ' + data.start_time, 3000);
                        Livewire.dispatch('refreshBookings');
                    }
                });
            }
        } catch(e) { console.warn('Sport Echo subscription failed:', e); }
        @endforeach
    });

    // Also listen for Livewire update event
    document.addEventListener('livewire:updated', function() {
        console.log('✅ Livewire component updated - refreshing calendar');
        if (typeof updateCalendar === 'function') {
            updateCalendar();
        }
    });
</script>
@endpush
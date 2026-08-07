<div class="finance-management-container">
    @push('styles')
    <style>
        .finance-header {
            margin-bottom: 2rem;
        }

        .finance-header h4 {
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .finance-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .summary-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .summary-icon.revenue {
            background: rgba(25, 114, 45, 0.1);
            color: #19722d;
        }

        .summary-icon.advance {
            background: rgba(14, 165, 233, 0.1);
            color: #0ea5e9;
        }

        .summary-icon.balance {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .summary-info h6 {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .summary-info h3 {
            color: #0f172a;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .filter-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
            display: block;
        }

        .filter-input {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            width: 100%;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .filter-input:focus {
            outline: none;
            border-color: #19722d;
            box-shadow: 0 0 0 3px rgba(25, 114, 45, 0.1);
        }

        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .finance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .finance-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            white-space: nowrap;
        }

        .finance-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        .finance-table tbody tr:hover {
            background: #f8fafc;
        }

        .finance-table tbody tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            white-space: nowrap;
        }

        .status-badge.paid {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }

        .status-badge.pending {
            background: rgba(234, 179, 8, 0.1);
            color: #ca8a04;
        }

        .status-badge.failed {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .status-badge.partial {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }
        
        .status-badge.default {
            background: rgba(100, 116, 139, 0.1);
            color: #475569;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: #0f172a;
        }

        .user-phone {
            font-size: 0.8rem;
            color: #64748b;
        }

        .booking-meta {
            font-size: 0.85rem;
            color: #64748b;
        }

        .booking-id {
            font-family: monospace;
            background: #f1f5f9;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #475569;
            font-weight: 600;
        }
        
        .currency {
            font-weight: 600;
        }

    </style>
    @endpush

    <div class="finance-header d-flex justify-content-between align-items-center">
        <div>
            <h4>Finance Overview</h4>
            <p class="mb-0">Monitor and manage all booking payments and transactions.</p>
        </div>
        <div>
            <a href="{{ route('staff.online-payment') }}" class="btn btn-primary" style="background-color: #19722d; border-color: #19722d;">
                <i class="fas fa-globe me-2"></i>Online Payment
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-icon revenue">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="summary-info">
                    <h6>Total Revenue (Paid)</h6>
                    <h3>Rs.{{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-icon advance">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="summary-info">
                    <h6>Total Advance Collected</h6>
                    <h3>Rs.{{ number_format($totalAdvance, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-icon balance">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="summary-info">
                    <h6>Total Balance Due</h6>
                    <h3>Rs.{{ number_format($totalBalance, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="filter-label">Search</label>
                <div class="position-relative">
                    <i class="fas fa-search position-absolute text-muted" style="left: 1rem; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" class="filter-input" placeholder="User Name, ID, Phone..." style="padding-left: 2.5rem;">
                </div>
            </div>
            <div class="col-md-2">
                <label class="filter-label">Start Date</label>
                <input type="date" wire:model.live="startDate" class="filter-input">
            </div>
            <div class="col-md-2">
                <label class="filter-label">End Date</label>
                <input type="date" wire:model.live="endDate" class="filter-input">
            </div>
            <div class="col-md-2">
                <label class="filter-label">Payment Status</label>
                <select wire:model.live="paymentStatus" class="filter-input">
                    <option value="">All Statuses</option>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                    <option value="refunded">Refunded</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Payment Method</label>
                <select wire:model.live="paymentMethod" class="filter-input">
                    <option value="">All Methods</option>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="transfer">Transfer</option>
                    <option value="genie">Online</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="finance-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Booking Details</th>
                        <th>Payment Status</th>
                        <th>Method</th>
                        <th class="text-end">Total Price</th>
                        <th class="text-end">Advance</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <span class="booking-id">#{{ $booking->id }}</span>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($booking->user_name, 0, 1)) }}
                                    </div>
                                    <div class="user-details">
                                        <span class="user-name">{{ $booking->user_name }}</span>
                                        <span class="user-phone">{{ $booking->user_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><strong>{{ $booking->sport->name ?? $booking->game_name ?? 'N/A' }}</strong></div>
                                <div class="booking-meta">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}<br>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = 'default';
                                    $iconClass = 'fa-circle-info';
                                    $statusText = $booking->payment_status ?? 'Unknown';
                                    
                                    if(strtolower($statusText) == 'paid') {
                                        $statusClass = 'paid';
                                        $iconClass = 'fa-check-circle';
                                    } elseif(strtolower($statusText) == 'pending') {
                                        $statusClass = 'pending';
                                        $iconClass = 'fa-clock';
                                    } elseif(strtolower($statusText) == 'failed') {
                                        $statusClass = 'failed';
                                        $iconClass = 'fa-times-circle';
                                    } elseif(strtolower($statusText) == 'partially_paid') {
                                        $statusClass = 'partial';
                                        $iconClass = 'fa-adjust';
                                    }
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="fas {{ $iconClass }}"></i> {{ ucfirst(str_replace('_', ' ', $statusText)) }}
                                </span>
                            </td>
                            <td>
                                @if($booking->payment_method)
                                    <span class="text-capitalize"><i class="fas fa-{{ strtolower($booking->payment_method) == 'cash' ? 'money-bill' : 'credit-card' }} text-muted me-1"></i> {{ $booking->payment_method }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end currency text-success">
                                Rs.{{ number_format($booking->price, 2) }}
                            </td>
                            <td class="text-end currency text-info">
                                Rs.{{ number_format($booking->advance_amount, 2) }}
                            </td>
                            <td class="text-end currency {{ $booking->balance_due > 0 ? 'text-warning' : 'text-muted' }}">
                                Rs.{{ number_format($booking->balance_due, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-receipt mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <h5>No financial records found</h5>
                                    <p>Try adjusting your filters or search terms.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bookings->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>

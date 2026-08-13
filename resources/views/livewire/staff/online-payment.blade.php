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
            <h4>Online Payments</h4>
            <p class="mb-0">Monitor and manage all online transactions.</p>
        </div>
        <div>
            <a href="{{ route('staff.finance') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Finance
            </a>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon revenue">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="summary-info">
                    <h6>Total Online Paid</h6>
                    <h3>Rs.{{ number_format($totalOnlinePaid, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon balance" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="summary-info">
                    <h6>Pending Amount</h6>
                    <h3>Rs.{{ number_format($pendingSettlement, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon advance" style="background: rgba(34, 197, 94, 0.1); color: #16a34a;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="summary-info d-flex justify-content-between align-items-center w-100">
                    <div>
                        <h6>Withdrawable Amount</h6>
                        <h3>Rs.{{ number_format($withdrawableAmount, 2) }}</h3>
                    </div>
                    <div class="d-flex flex-column gap-2 align-items-end">
                        @if($withdrawableAmount > 0)
                            <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" wire:click="openWithdrawalModal()"><i class="fas fa-paper-plane me-1"></i> Request</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="summary-info d-flex justify-content-between align-items-center w-100">
                    <div>
                        <h6>Withdrawn Amount</h6>
                        <h3>Rs.{{ number_format($totalWithdrawn, 2) }}</h3>
                    </div>
                    <div class="d-flex flex-column gap-2 align-items-end">
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" wire:click="openWithdrawalHistoryModal()"><i class="fas fa-history me-1"></i> History</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="filter-label">Search</label>
                <div class="position-relative">
                    <i class="fas fa-search position-absolute text-muted" style="left: 1rem; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" class="filter-input" placeholder="User Name, ID, Phone..." style="padding-left: 2.5rem;">
                </div>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Start Date</label>
                <input type="date" wire:model.live="startDate" class="filter-input">
            </div>
            <div class="col-md-3">
                <label class="filter-label">End Date</label>
                <input type="date" wire:model.live="endDate" class="filter-input">
            </div>
            <div class="col-md-2">
                <label class="filter-label">Financial Status</label>
                <select wire:model.live="financialStatus" class="filter-input">
                    <option value="">All Statuses</option>
                    <option value="Unpaid">Unpaid</option>
                    <option value="FullyPaid">Fully Paid</option>
                    <option value="PartiallyPaid">Partially Paid</option>
                    <option value="RefundPending">Refund Pending</option>
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
                        <th>Financial Status</th>
                        <th>Method</th>
                        <th class="text-end">Total Price</th>
                        <th class="text-end">Advance</th>
                        <th class="text-end">Balance</th>
                        <th class="text-center">Action</th>
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
                                @if(isset($booking->item_type) && $booking->item_type === 'pool')
                                    <div><strong>{{ $booking->pool->name ?? 'Pool' }}</strong></div>
                                    <div class="booking-meta">
                                        @if($booking->occurrence)
                                            {{ \Carbon\Carbon::parse($booking->occurrence->date)->format('M d, Y') }}<br>
                                            {{ \Carbon\Carbon::parse($booking->occurrence->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->occurrence->end_time)->format('h:i A') }}
                                        @else
                                            {{ $booking->created_at->format('M d, Y h:i A') }}
                                        @endif
                                    </div>
                                @else
                                    <div><strong>{{ $booking->sport->name ?? $booking->game_name ?? 'N/A' }}</strong></div>
                                    <div class="booking-meta">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}<br>
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = 'default';
                                    $iconClass = 'fa-circle-info';
                                    $statusText = $booking->financial_status ?? 'Unknown';
                                    
                                    if(in_array(strtolower($statusText), ['paid', 'fullypaid'])) {
                                        $statusClass = 'paid';
                                        $iconClass = 'fa-check-circle';
                                    } elseif(in_array(strtolower($statusText), ['unpaid', 'pending'])) {
                                        $statusClass = 'pending';
                                        $iconClass = 'fa-clock';
                                    } elseif(strtolower($statusText) == 'refundpending') {
                                        $statusClass = 'failed';
                                        $iconClass = 'fa-undo';
                                    } elseif(strtolower($statusText) == 'partiallypaid') {
                                        $statusClass = 'partial';
                                        $iconClass = 'fa-adjust';
                                    }
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="fas {{ $iconClass }}"></i> {{ preg_replace('/(?<!^)([A-Z])/', ' $1', ucfirst($statusText)) }}
                                </span>
                            </td>
                            <td>
                                @if(isset($booking->item_type) && $booking->item_type === 'pool')
                                    <span class="text-capitalize"><i class="fas fa-money-bill text-muted me-1"></i> {{ $booking->payment_method ?? 'Online' }}</span>
                                @else
                                    @if($booking->payment_method)
                                        @php
                                            $displayMethod = strtolower($booking->payment_method) == 'genie' ? 'Online' : $booking->payment_method;
                                        @endphp
                                        <span class="text-capitalize"><i class="fas fa-{{ strtolower($booking->payment_method) == 'cash' ? 'money-bill' : 'credit-card' }} text-muted me-1"></i> {{ $displayMethod }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-end currency text-success">
                                Rs.{{ number_format($booking->price ?? $booking->booking_total, 2) }}
                            </td>
                            <td class="text-end currency text-info">
                                Rs.{{ number_format($booking->advance_amount, 2) }}
                            </td>
                            <td class="text-end currency {{ $booking->balance_due > 0 ? 'text-warning' : 'text-muted' }}">
                                Rs.{{ number_format($booking->balance_due, 2) }}
                            </td>
                            <td class="text-center">
                                @if($booking->balance_due > 0 || in_array(strtolower($booking->payment_status ?? $booking->financial_status), ['pending', 'partially_paid', 'unpaid']))
                                    <button class="btn btn-sm btn-outline-success fw-bold rounded-pill" wire:click="openPaymentModal({{ $booking->id }}, '{{ $booking->item_type ?? 'sport' }}')">
                                        <i class="fas fa-money-bill-wave"></i> Payment
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-info fw-bold rounded-pill" wire:click="openDetailsModal({{ $booking->id }}, '{{ $booking->item_type ?? 'sport' }}')">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-globe mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <h5>No online records found</h5>
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

    <!-- Withdrawal Requests History Modal -->
    @if($showWithdrawalHistoryModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title font-weight-bold d-flex align-items-center gap-2">
                        <i class="fas fa-hand-holding-usd text-success me-2"></i> Withdrawal Requests History
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeWithdrawalHistoryModal()"></button>
                </div>
                
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 custom-table">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Request ID</th>
                                    <th>Requested By</th>
                                    <th>Date</th>
                                    <th class="text-end">Request Amount</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th class="text-center pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawalRequests as $req)
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">#REQ-{{ $req->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-circle bg-light text-primary fw-bold" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                    {{ strtoupper(substr($req->requester->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $req->requester->name ?? 'Unknown User' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $req->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="text-end fw-bold text-dark">Rs.{{ number_format($req->request_amount, 2) }}</td>
                                        <td class="text-end fw-bold text-success">Rs.{{ number_format($req->paid_amount, 2) }}</td>
                                        <td class="text-center pe-4">
                                            @if(strtolower($req->status) === 'pending')
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Pending</span>
                                            @elseif(strtolower($req->status) === 'approved')
                                                <span class="badge bg-info"><i class="fas fa-check"></i> Approved</span>
                                            @elseif(strtolower($req->status) === 'completed')
                                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Completed</span>
                                            @elseif(strtolower($req->status) === 'rejected')
                                                <span class="badge bg-danger"><i class="fas fa-times"></i> Rejected</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($req->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-file-invoice mb-3 text-secondary opacity-50" style="font-size: 3rem;"></i>
                                            <h5>No withdrawal requests found.</h5>
                                            <p class="mb-0">You have not made any requests to withdraw funds yet.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($withdrawalRequests->hasPages())
                    <div class="px-4 py-3 border-top bg-light">
                        {{ $withdrawalRequests->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Collect Payment Modal -->
    @if($showPaymentModal && $paymentBooking)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered max-w-sm">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title font-weight-bold flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-success me-2"></i> Collect Payment
                        </h5>
                        <button type="button" class="btn-close" wire:click="closePaymentModal()"></button>
                    </div>
                    
                    <div class="modal-body p-4">
                        <div class="bg-light p-3 rounded mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Booking ID:</span>
                                <span class="fw-bold">#{{ $paymentBooking->id }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Customer:</span>
                                <span class="fw-bold">{{ $paymentBooking->user_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Amount Due:</span>
                                <span class="fw-bold text-success fs-5">LKR {{ number_format($paymentBooking->balance_due > 0 ? $paymentBooking->balance_due : max(0, $paymentBooking->price - $paymentBooking->advance_amount), 2) }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="form-label text-muted small text-uppercase fw-bold mb-2">Select Payment Method</label>
                            <div class="d-flex gap-2">
                                <div class="flex-fill border rounded p-2 text-center {{ $collectPaymentMethod === 'cash' ? 'border-success bg-success bg-opacity-10 text-success fw-bold' : 'bg-white text-secondary' }}"
                                     style="cursor:pointer;" wire:click="$set('collectPaymentMethod', 'cash')">
                                    <i class="fas fa-money-bill d-block mb-1 fs-5"></i>
                                    <small>Cash</small>
                                </div>
                                <div class="flex-fill border rounded p-2 text-center {{ $collectPaymentMethod === 'card' ? 'border-success bg-success bg-opacity-10 text-success fw-bold' : 'bg-white text-secondary' }}"
                                     style="cursor:pointer;" wire:click="$set('collectPaymentMethod', 'card')">
                                    <i class="fas fa-credit-card d-block mb-1 fs-5"></i>
                                    <small>Card</small>
                                </div>
                                <div class="flex-fill border rounded p-2 text-center {{ $collectPaymentMethod === 'transfer' ? 'border-success bg-success bg-opacity-10 text-success fw-bold' : 'bg-white text-secondary' }}"
                                     style="cursor:pointer;" wire:click="$set('collectPaymentMethod', 'transfer')">
                                    <i class="fas fa-exchange-alt d-block mb-1 fs-5"></i>
                                    <small>Transfer</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" wire:click="closePaymentModal()">Cancel</button>
                        <button type="button" class="btn btn-success rounded-pill px-4" wire:click="collectPayment()">Confirm Payment</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- View Payment Details Modal -->
    @if($showDetailsModal && $detailsBooking)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered max-w-sm">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title font-weight-bold flex items-center gap-2">
                            <i class="fas fa-receipt text-info me-2"></i> Payment Details
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDetailsModal()"></button>
                    </div>
                    
                    <div class="modal-body p-4">
                        <div class="bg-light p-3 rounded mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Booking ID:</span>
                                <span class="fw-bold">#{{ $detailsBooking->id }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Customer:</span>
                                <span class="fw-bold">{{ $detailsBooking->user_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Price:</span>
                                <span class="fw-bold">Rs.{{ number_format($detailsBooking->price, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Final Status:</span>
                                <span class="badge bg-success">{{ preg_replace('/(?<!^)([A-Z])/', ' $1', ucfirst($detailsBooking->financial_status)) }}</span>
                            </div>
                        </div>

                        <div class="border rounded p-3 bg-white">
                            <h6 class="fw-bold mb-3 text-uppercase small text-muted">Payment Breakdown</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary"><i class="fas fa-globe me-2"></i>Online Paid</span>
                                <span class="fw-bold text-dark">Rs.{{ number_format($detailsBooking->online_paid_amount ?: 0, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-secondary"><i class="fas fa-money-bill-wave me-2"></i>Offline Paid</span>
                                <span class="fw-bold text-dark">Rs.{{ number_format($detailsBooking->offline_paid_amount ?: 0, 2) }}</span>
                            </div>
                            <hr class="my-2 text-muted opacity-25">
                            <div class="d-flex justify-content-between mt-3">
                                <span class="fw-bold">Total Paid</span>
                                <span class="fw-bold text-success fs-5">Rs.{{ number_format($detailsBooking->amount_paid ?: 0, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 w-100" wire:click="closeDetailsModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Request Withdrawal Modal -->
    @if($showWithdrawalModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered max-w-sm">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title font-weight-bold flex items-center gap-2">
                            <i class="fas fa-paper-plane text-primary me-2"></i> Request Withdrawal
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeWithdrawalModal()"></button>
                    </div>
                    
                    <div class="modal-body p-4">
                        <div class="bg-success bg-opacity-10 p-3 rounded mb-4 text-center border border-success border-opacity-25">
                            <span class="text-success small fw-bold text-uppercase d-block mb-1">Max Withdrawable Amount</span>
                            <span class="fw-bold text-success fs-3">Rs.{{ number_format($withdrawableAmount, 2) }}</span>
                        </div>
                        
                        @if(session()->has('error'))
                            <div class="alert alert-danger py-2 px-3 small rounded-3">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Request Amount (Rs.)</label>
                            <input type="number" step="0.01" class="form-control form-control-lg fw-bold" wire:model="withdrawalAmount" placeholder="0.00" max="{{ $withdrawableAmount }}">
                            <small class="text-muted mt-2 d-block">Type the exact amount you wish to withdraw.</small>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" wire:click="closeWithdrawalModal()">Cancel</button>
                        <button type="button" class="btn btn-primary rounded-pill px-4" wire:click="submitWithdrawalRequest()">Send Request</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

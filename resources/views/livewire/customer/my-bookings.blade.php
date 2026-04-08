<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">My Bookings</h2>
            <p class="text-muted mb-0">View your booking history and download receipts</p>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="h3 fw-bold text-primary mb-1">{{ $stats['total'] }}</div>
                <div class="small text-muted">Total Bookings</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="h3 fw-bold text-success mb-1">{{ $stats['upcoming'] }}</div>
                <div class="small text-muted">Upcoming</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="h3 fw-bold text-info mb-1">{{ $stats['completed'] }}</div>
                <div class="small text-muted">Completed</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="h3 fw-bold text-warning mb-1">LKR {{ number_format($stats['spent']) }}</div>
                <div class="small text-muted">Total Spent</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                        placeholder="Search by sport or name...">
                </div>
                <div class="col-md-4">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">All Statuses</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Pending">Pending</option>
                        <option value="Playing">Playing</option>
                        <option value="No-Show">No-Show</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Bookings List --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @forelse($bookings as $booking)
            <div class="p-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center"
                        style="width:48px;height:48px;min-width:48px;">
                        <i class="fas fa-futbol text-success"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $booking->game_name }}</div>
                        <div class="small text-muted">
                            {{ $booking->venue->name ?? 'Venue' }} &bull;
                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('D, M d Y') }}
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}–{{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                        </div>
                        <div class="small text-muted">Court {{ $booking->court_number ?? '1' }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end">
                        <div class="fw-semibold">LKR {{ number_format($booking->price, 2) }}</div>
                        <span class="badge bg-{{
                            $booking->status === 'Completed' ? 'success' :
                            ($booking->status === 'Cancelled' ? 'danger' :
                            ($booking->status === 'Playing' ? 'primary' :
                            ($booking->status === 'No-Show' ? 'secondary' : 'warning'))) }}">
                            {{ $booking->status }}
                        </span>
                    </div>
                    <a href="{{ route('customer.receipt', $booking->id) }}" target="_blank"
                        class="btn btn-sm btn-outline-success" title="Download Receipt">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-calendar-times fa-3x mb-3 opacity-25"></i>
                <p>No bookings found.</p>
            </div>
            @endforelse
        </div>
        @if($bookings->hasPages())
        <div class="card-footer bg-white">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>

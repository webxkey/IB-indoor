<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\BookingBooking;
use App\Models\PoolsPoolbooking;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\Auth;

#[Title("Online Payments")]
#[Layout("components.layouts.staff")]
class OnlinePayment extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $startDate = '';
    public $financialStatus = '';

    public $showPaymentModal = false;
    public $paymentBooking = null;
    public $collectPaymentMethod = 'cash';

    public $showDetailsModal = false;
    public $detailsBooking = null;

    public $showWithdrawalModal = false;
    public $withdrawalAmount = '';

    public $showWithdrawalHistoryModal = false;

    public function openWithdrawalModal()
    {
        $this->showWithdrawalModal = true;
        $this->withdrawalAmount = '';
    }

    public function closeWithdrawalModal()
    {
        $this->showWithdrawalModal = false;
        $this->withdrawalAmount = '';
    }

    public function openWithdrawalHistoryModal()
    {
        $this->showWithdrawalHistoryModal = true;
    }

    public function closeWithdrawalHistoryModal()
    {
        $this->showWithdrawalHistoryModal = false;
    }

    public function submitWithdrawalRequest()
    {
        $amount = (float) $this->withdrawalAmount;
        if ($amount <= 0) {
            session()->flash('error', 'Please enter a valid amount.');
            return;
        }

        $user = Auth::user();
        
        WithdrawalRequest::create([
            'complex_id' => $user->complex_id,
            'requested_by' => $user->id,
            'request_amount' => $amount,
            'status' => 'pending',
        ]);

        session()->flash('success', "Withdrawal request for Rs." . number_format($amount, 2) . " submitted successfully.");
        $this->closeWithdrawalModal();
    }

    public $detailsBookingId = null;

    public function openDetailsModal($id, $type = 'sport')
    {
        $this->detailsBookingId = $id;
        if ($type === 'pool') {
            $this->detailsBooking = PoolsPoolbooking::find($id);
        } else {
            $this->detailsBooking = BookingBooking::find($id);
        }
        if ($this->detailsBooking) {
            $this->showDetailsModal = true;
        }
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->detailsBooking = null;
        $this->detailsBookingId = null;
    }

    public function openPaymentModal($id, $type = 'sport')
    {
        $this->paymentBookingId = $id;
        $this->bookingTypeForPayment = $type;
        if ($type === 'pool') {
            $this->paymentBooking = PoolsPoolbooking::find($id);
        } else {
            $this->paymentBooking = BookingBooking::find($id);
        }
        
        if ($this->paymentBooking) {
            $this->collectPaymentMethod = $this->paymentBooking->payment_method ?: 'cash';
            if ($this->collectPaymentMethod === 'genie') {
                $this->collectPaymentMethod = 'cash';
            }
            $this->showPaymentModal = true;
        }
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->paymentBooking = null;
        $this->paymentBookingId = null;
    }

    public $bookingTypeForPayment = 'sport';
    public $paymentBookingId = null;

    public function collectPayment()
    {
        $booking = null;
        if ($this->bookingTypeForPayment === 'pool') {
            $booking = PoolsPoolbooking::find($this->paymentBookingId);
        } else {
            $booking = BookingBooking::find($this->paymentBookingId);
        }

        if (!$booking && $this->paymentBooking) {
            $booking = $this->paymentBooking;
        }

        if ($booking) {
            $booking->payment_status = 'Paid';
            if ($this->bookingTypeForPayment !== 'pool') {
                $booking->payment_method = $this->collectPaymentMethod;
            }
            
            $amountDue = $booking->balance_due > 0 
                ? (float) $booking->balance_due 
                : max(0, (float) ($booking->price ?? $booking->booking_total ?? 0) - (float) ($booking->advance_amount ?? 0));
            
            $booking->amount_paid = ((float) ($booking->amount_paid ?? 0)) + $amountDue;

            if ($this->bookingTypeForPayment !== 'pool') {
                if (strtolower($this->collectPaymentMethod) === 'cash') {
                    $booking->offline_paid_amount = ((float) ($booking->offline_paid_amount ?? 0)) + $amountDue;
                } else {
                    $booking->online_paid_amount = ((float) ($booking->online_paid_amount ?? 0)) + $amountDue;
                }
            }

            $booking->balance_due = 0;
            $booking->financial_status = 'FullyPaid';

            $booking->save();

            session()->flash('success', "Payment of Rs." . number_format($amountDue, 2) . " collected successfully via " . ucfirst($this->collectPaymentMethod) . ".");
            $this->closePaymentModal();
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->financialStatus = '';
        $this->resetPage('bookingsPage');
    }

    public function updatingSearch()
    {
        $this->resetPage('bookingsPage');
    }
    
    public function updatingStartDate()
    {
        $this->resetPage();
    }
    
    public function updatingEndDate()
    {
        $this->resetPage();
    }
    
    public function updatingFinancialStatus()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $user = Auth::user();
        $complexId = $user->complex_id ?? null;

        // Fetch bookings that have an online payment OR were initiated as genie
        $sportsQuery = BookingBooking::with(['user', 'sport'])
            ->where(function ($q) {
                $q->whereRaw('LOWER(payment_method) = ?', ['genie'])
                  ->orWhere('online_paid_amount', '>', 0);
            });
            
        if ($complexId) {
            $sportsQuery->where('complex_id_id', $complexId);
        }

        $poolsQuery = PoolsPoolbooking::with(['user', 'pool', 'occurrence'])
            ->where('online_paid_amount', '>', 0);
            
        if ($complexId) {
            $poolsQuery->whereHas('pool', function($q) use ($complexId) {
                $q->where('venue_id', $complexId);
            });
        }

        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $sportsQuery->where(function ($q) use ($search) {
                $q->where('user_name', 'ilike', $search)
                  ->orWhereRaw('CAST(id AS TEXT) LIKE ?', [$search])
                  ->orWhere('user_number', 'like', $search);
            });
            $poolsQuery->where(function ($q) use ($search) {
                $q->where('user_name', 'ilike', $search)
                  ->orWhereRaw('CAST(id AS TEXT) LIKE ?', [$search])
                  ->orWhere('user_number', 'like', $search);
            });
        }

        if (!empty($this->startDate)) {
            $sportsQuery->whereDate('booking_date', '>=', $this->startDate);
            $poolsQuery->whereDate('created_at', '>=', $this->startDate);
        }

        if (!empty($this->endDate)) {
            $sportsQuery->whereDate('booking_date', '<=', $this->endDate);
            $poolsQuery->whereDate('created_at', '<=', $this->endDate);
        }

        if (!empty($this->financialStatus)) {
            $sportsQuery->where('financial_status', $this->financialStatus);
            $poolsQuery->where('financial_status', $this->financialStatus);
        }

        // Summary Statistics
        $activeSportsSummaryQuery = (clone $sportsQuery)
            ->whereNotIn('financial_status', ['RefundPending', 'Refunded'])
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'refunded')->orWhereNull('payment_status');
            });
            
        $activePoolsSummaryQuery = (clone $poolsQuery)
            ->whereNotIn('financial_status', ['RefundPending', 'Refunded']);

        $totalOnlinePaid = $activeSportsSummaryQuery->sum('online_paid_amount') + $activePoolsSummaryQuery->sum('online_paid_amount');
        
        $withdrawableAmountSports = (clone $activeSportsSummaryQuery)
            ->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['completed', 'played', 'no-show'])
            ->sum('online_paid_amount');
        $withdrawableAmountPools = (clone $activePoolsSummaryQuery)
            ->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['completed', 'played', 'no-show'])
            ->sum('online_paid_amount');
        $withdrawableAmount = $withdrawableAmountSports + $withdrawableAmountPools;

        $pendingSettlementSports = (clone $activeSportsSummaryQuery)
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['completed', 'played', 'no-show', 'cancelled'])
            ->sum('online_paid_amount');
        $pendingSettlementPools = (clone $activePoolsSummaryQuery)
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['completed', 'played', 'no-show', 'cancelled'])
            ->sum('online_paid_amount');
        $pendingSettlement = $pendingSettlementSports + $pendingSettlementPools;

        // Calculate Withdrawn Amount (Pending + Completed)
        $withdrawalsBaseQuery = WithdrawalRequest::query()->where('requested_by', $user->id);
        if ($complexId) {
            $withdrawalsBaseQuery->where('complex_id', $complexId);
        }

        $pendingWithdrawals = (clone $withdrawalsBaseQuery)
            ->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['pending', 'approved'])
            ->sum('request_amount');
            
        $completedWithdrawals = (clone $withdrawalsBaseQuery)
            ->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['completed', 'paid'])
            ->sum('paid_amount');
            
        $totalWithdrawn = $pendingWithdrawals + $completedWithdrawals;

        // Adjust Withdrawable Amount by subtracting already withdrawn/requested amounts
        $withdrawableAmount = max(0, $withdrawableAmount - $totalWithdrawn);

        // Fetch withdrawal requests for the complex
        $withdrawalsQuery = WithdrawalRequest::with('requester')->where('requested_by', $user->id);
        if ($complexId) {
            $withdrawalsQuery->where('complex_id', $complexId);
        }
        $withdrawalRequests = $withdrawalsQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'withdrawalsPage');

        $sportsList = $sportsQuery->get();
        $sportsList->each(function($item) { $item->item_type = 'sport'; });

        $poolsList = $poolsQuery->get();
        $poolsList->each(function($item) { $item->item_type = 'pool'; });

        $combined = $sportsList->toBase()->concat($poolsList->toBase())->sortByDesc('created_at')->values();

        $page = $this->getPage('bookingsPage');
        $perPage = 15;
        $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $combined->forPage($page, $perPage),
            $combined->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'bookingsPage']
        );

        return view('livewire.staff.online-payment', [
            'bookings' => $bookings,
            'withdrawalRequests' => $withdrawalRequests,
            'totalOnlinePaid' => $totalOnlinePaid,
            'withdrawableAmount' => $withdrawableAmount,
            'pendingSettlement' => $pendingSettlement,
            'totalWithdrawn' => $totalWithdrawn,
        ]);
    }
}

<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\BookingBooking;
use App\Models\PoolsPoolbooking;
use Illuminate\Support\Facades\Auth;

#[Title("Finance Management")]
#[Layout("components.layouts.staff")]
class Finance extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $paymentStatus = '';
    public $paymentMethod = '';

    public $showPaymentModal = false;
    public $paymentBooking = null;
    public $bookingTypeForPayment = 'sport';
    public $collectPaymentMethod = 'cash';

    public $showDetailsModal = false;
    public $detailsBooking = null;

    public function openDetailsModal($id, $type = 'sport')
    {
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
    }

    public function openPaymentModal($id, $type = 'sport')
    {
        $this->bookingTypeForPayment = $type; // we need to remember this for collectPayment
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
    }

    public function collectPayment()
    {
        if ($this->paymentBooking) {
            $amountDue = $this->paymentBooking->balance_due > 0 
                ? $this->paymentBooking->balance_due 
                : max(0, ($this->paymentBooking->price ?? $this->paymentBooking->booking_total) - $this->paymentBooking->advance_amount);

            $this->paymentBooking->payment_status = 'Paid';
            if ($this->bookingTypeForPayment !== 'pool') {
                $this->paymentBooking->payment_method = $this->collectPaymentMethod;
                $this->paymentBooking->offline_paid_amount = ($this->paymentBooking->offline_paid_amount ?? 0) + $amountDue;
            }
            
            $this->paymentBooking->amount_paid = ($this->paymentBooking->amount_paid ?? 0) + $amountDue;
            $this->paymentBooking->balance_due = 0;
            $this->paymentBooking->financial_status = 'FullyPaid';

            $this->paymentBooking->save();

            session()->flash('success', "Payment of Rs." . number_format($amountDue, 2) . " collected successfully.");
            $this->closePaymentModal();
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStartDate()
    {
        $this->resetPage();
    }
    
    public function updatingEndDate()
    {
        $this->resetPage();
    }
    
    public function updatingPaymentStatus()
    {
        $this->resetPage();
    }
    
    public function updatingPaymentMethod()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $complexId = $user->complex_id ?? null;

        $sportsQuery = BookingBooking::with(['user', 'sport']);
        if ($complexId) {
            $sportsQuery->where('complex_id_id', $complexId);
        }

        $poolsQuery = PoolsPoolbooking::with(['user', 'pool', 'occurrence']);
        if ($complexId) {
            $poolsQuery->whereHas('pool', function($q) use ($complexId) {
                $q->where('venue_id', $complexId); // Pools_pool has venue_id
            });
        }

        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $sportsQuery->where(function ($q) use ($search) {
                $q->where('user_name', 'like', $search)
                  ->orWhere('id', 'like', $search)
                  ->orWhere('user_number', 'like', $search);
            });
            $poolsQuery->where(function ($q) use ($search) {
                $q->where('user_name', 'like', $search)
                  ->orWhere('id', 'like', $search)
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

        if (!empty($this->paymentStatus)) {
            $sportsQuery->where('payment_status', $this->paymentStatus);
            $poolsQuery->where('financial_status', $this->paymentStatus); // Pool uses financial_status
        }

        if (!empty($this->paymentMethod)) {
            $sportsQuery->whereRaw('LOWER(payment_method) = ?', [strtolower($this->paymentMethod)]);
            $method = strtolower($this->paymentMethod);
            if (in_array($method, ['genie', 'online', 'card'])) {
                $poolsQuery->where('online_paid_amount', '>', 0);
            } elseif ($method === 'cash') {
                $poolsQuery->where('amount_paid', '>', 0)
                           ->where(function($q) {
                               $q->whereNull('online_paid_amount')->orWhere('online_paid_amount', 0);
                           });
            } else {
                $poolsQuery->whereRaw('1 = 0');
            }
        }

        // Summary Statistics
        $activeSportsSummaryQuery = (clone $sportsQuery)
            ->whereNotIn('financial_status', ['RefundPending', 'Refunded'])
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'refunded')->orWhereNull('payment_status');
            });
            
        $activePoolsSummaryQuery = (clone $poolsQuery)
            ->whereNotIn('financial_status', ['RefundPending', 'Refunded']);

        $totalRevenue = $activeSportsSummaryQuery->sum('amount_paid') + $activePoolsSummaryQuery->sum('amount_paid');
        $totalAdvance = $activeSportsSummaryQuery->sum('advance_amount') + $activePoolsSummaryQuery->sum('advance_amount');
        $totalBalance = (clone $sportsQuery)->sum('balance_due') + (clone $poolsQuery)->sum('balance_due');

        $sportsList = $sportsQuery->get();
        // Append a dynamic property to distinguish them in the view
        $sportsList->each(function($item) { $item->item_type = 'sport'; });

        $poolsList = $poolsQuery->get();
        $poolsList->each(function($item) { $item->item_type = 'pool'; });

        $combined = $sportsList->toBase()->concat($poolsList->toBase())->sortByDesc('created_at')->values();

        $page = $this->getPage();
        $perPage = 15;
        $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $combined->forPage($page, $perPage),
            $combined->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.staff.finance', [
            'bookings' => $bookings,
            'totalRevenue' => $totalRevenue,
            'totalAdvance' => $totalAdvance,
            'totalBalance' => $totalBalance,
        ]);
    }
}

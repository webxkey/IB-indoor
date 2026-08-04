<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\BookingBooking;
use Illuminate\Support\Facades\Auth;

#[Title("Online Payments")]
#[Layout("components.layouts.staff")]
class OnlinePayment extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $financialStatus = '';

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
    
    public function updatingFinancialStatus()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $user = Auth::user();
        $complexId = $user->complex_id ?? null;

        // Force 'genie' as the payment method for online payments
        $query = BookingBooking::with(['user', 'sport'])
            ->whereRaw('LOWER(payment_method) = ?', ['genie']);
            
        if ($complexId) {
            $query->where('complex_id_id', $complexId);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('user_name', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . $this->search . '%')
                  ->orWhere('user_number', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->startDate)) {
            $query->whereDate('booking_date', '>=', $this->startDate);
        }

        if (!empty($this->endDate)) {
            $query->whereDate('booking_date', '<=', $this->endDate);
        }

        if (!empty($this->financialStatus)) {
            $query->where('financial_status', $this->financialStatus);
        }

        // Summary Statistics
        $summaryQuery = clone $query;
        $activeSummaryQuery = (clone $summaryQuery)
            ->whereNotIn('financial_status', ['RefundPending', 'Refunded'])
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'refunded')->orWhereNull('payment_status');
            });

        $totalRevenue = (clone $activeSummaryQuery)->sum('amount_paid');
        $totalAdvance = (clone $activeSummaryQuery)->sum('advance_amount');
        $totalBalance = (clone $summaryQuery)->sum('balance_due');

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.staff.online-payment', [
            'bookings' => $bookings,
            'totalRevenue' => $totalRevenue,
            'totalAdvance' => $totalAdvance,
            'totalBalance' => $totalBalance,
        ]);
    }
}

<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\BookingBooking;
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

        $query = BookingBooking::with(['user', 'sport']);
            
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

        if (!empty($this->paymentStatus)) {
            $query->where('payment_status', $this->paymentStatus);
        }

        if (!empty($this->paymentMethod)) {
            $query->whereRaw('LOWER(payment_method) = ?', [strtolower($this->paymentMethod)]);
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

        return view('livewire.staff.finance', [
            'bookings' => $bookings,
            'totalRevenue' => $totalRevenue,
            'totalAdvance' => $totalAdvance,
            'totalBalance' => $totalBalance,
        ]);
    }
}

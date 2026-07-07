<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\BookingBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.staff')]
#[Title('Completed Bookings')]
class CompletedBookings extends Component
{
    use WithPagination;

    public $search = '';
    public $filterDate = '';
    public $perPage = 15;
    
    public $complex_id;
    
    // Receipt Modal State
    public $showReceiptModal = false;
    public $selectedBooking = null;

    // Payment Modal State
    public $showPaymentModal = false;
    public $paymentBooking = null;
    public $collectPaymentMethod = 'cash';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterDate' => ['except' => ''],
    ];

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        // Optionally set default filter date to today or leave empty to view all
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDate()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterDate = '';
        $this->resetPage();
    }

    public function viewReceipt($id)
    {
        $this->selectedBooking = BookingBooking::with('user', 'venue', 'sport')
            ->where('complex_id_id', $this->complex_id)
            ->findOrFail($id);
            
        $this->showReceiptModal = true;
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->selectedBooking = null;
    }

    public function openPaymentModal($id)
    {
        $this->paymentBooking = BookingBooking::where('complex_id_id', $this->complex_id)->findOrFail($id);
        $this->collectPaymentMethod = $this->paymentBooking->payment_method ?: 'cash';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->paymentBooking = null;
    }

    public function collectPayment()
    {
        if ($this->paymentBooking) {
            $this->paymentBooking->payment_status = 'Paid';
            $this->paymentBooking->payment_method = $this->collectPaymentMethod;
            $this->paymentBooking->save();

            session()->flash('success', "Payment of LKR " . number_format($this->paymentBooking->price ?: 0, 2) . " collected successfully.");
            $this->closePaymentModal();
        }
    }

    public function render()
    {
        $query = BookingBooking::where('complex_id_id', $this->complex_id)
            ->whereIn('status', ['Completed', 'played', 'completed', 'Played']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('user_name', 'like', '%' . $this->search . '%')
                  ->orWhere('user_number', 'like', '%' . $this->search . '%')
                  ->orWhere('game_name', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterDate) {
            $query->whereDate('booking_date', Carbon::parse($this->filterDate)->toDateString());
        }

        $bookings = $query->latest('booking_date')
            ->latest('start_time')
            ->paginate($this->perPage);

        return view('livewire.staff.completed-bookings', [
            'bookings' => $bookings
        ]);
    }
}

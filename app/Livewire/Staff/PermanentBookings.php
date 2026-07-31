<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\BookingPermanentbooking;
use App\Models\BookingBooking;

#[Layout("components.layouts.staff")]
#[Title("Permanent Bookings")]
class PermanentBookings extends Component
{
    use WithPagination;

    public $search = '';

    // Modal cancellation state
    public $showCancelModal = false;
    public $cancelBookingId = null;
    public $cancelType = 'choose'; // 'choose', 'full', 'slots'
    public $selectedSlots = [];
    public $selectAllSlots = false;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function openCancelModal($id)
    {
        $complexId = auth()->user()->complex_id ?? null;
        $query = BookingPermanentbooking::query();
        if ($complexId) {
            $query->where('complex_id', $complexId);
        }

        $booking = $query->find($id);
        if (!$booking) {
            session()->flash('error', 'Permanent booking not found.');
            return;
        }

        $this->cancelBookingId = $id;
        $this->cancelType = 'choose';
        $this->selectedSlots = [];
        $this->selectAllSlots = false;
        $this->showCancelModal = true;
    }

    public function setCancelType($type)
    {
        $this->cancelType = $type;
    }

    public function updatedSelectAllSlots($value)
    {
        if ($value && $this->cancelBookingId) {
            $booking = BookingPermanentbooking::with(['bookings' => function($q) {
                $q->orderBy('booking_date', 'asc')->orderBy('start_time', 'asc');
            }])->find($this->cancelBookingId);

            if ($booking) {
                $this->selectedSlots = $booking->bookings
                    ->where('status', '!=', 'Cancelled')
                    ->pluck('id')
                    ->map(fn($id) => (string)$id)
                    ->toArray();
            }
        } else {
            $this->selectedSlots = [];
        }
    }

    public function cancelFullBooking()
    {
        if (!$this->cancelBookingId) {
            return;
        }

        $complexId = auth()->user()->complex_id ?? null;
        $query = BookingPermanentbooking::query();
        if ($complexId) {
            $query->where('complex_id', $complexId);
        }

        $booking = $query->find($this->cancelBookingId);
        if ($booking) {
            $booking->is_active = false;
            $booking->status = 'Cancelled';
            $booking->save();

            // Cancel all associated child bookings
            BookingBooking::where('permanent_source_id', $booking->id)
                ->where('status', '!=', 'Cancelled')
                ->update(['status' => 'Cancelled']);

            session()->flash('message', "Permanent booking #{$booking->id} and all associated slots have been cancelled successfully.");
        } else {
            session()->flash('error', 'Permanent booking not found.');
        }

        $this->closeCancelModal();
    }

    public function cancelSelectedSlots()
    {
        if (!$this->cancelBookingId) {
            return;
        }

        if (empty($this->selectedSlots)) {
            session()->flash('error', 'Please select at least one slot to cancel.');
            return;
        }

        $count = BookingBooking::whereIn('id', $this->selectedSlots)
            ->where('permanent_source_id', $this->cancelBookingId)
            ->update(['status' => 'Cancelled']);

        // Check if there are any remaining non-cancelled slots for this permanent booking
        $remainingCount = BookingBooking::where('permanent_source_id', $this->cancelBookingId)
            ->where('status', '!=', 'Cancelled')
            ->count();

        if ($remainingCount === 0) {
            $booking = BookingPermanentbooking::find($this->cancelBookingId);
            if ($booking) {
                $booking->is_active = false;
                $booking->status = 'Cancelled';
                $booking->save();
            }
        }

        session()->flash('message', "{$count} slot booking(s) have been cancelled successfully.");
        $this->closeCancelModal();
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancelBookingId = null;
        $this->cancelType = 'choose';
        $this->selectedSlots = [];
        $this->selectAllSlots = false;
    }

    public function render()
    {
        $complexId = auth()->user()->complex_id ?? null;
        
        $query = BookingPermanentbooking::query();
        
        if ($complexId) {
            $query->where('complex_id', $complexId);
        }

        if ($this->search) {
            $query->where('id', 'like', '%' . $this->search . '%');
        }

        $bookings = $query->with(['user', 'bookings' => function($q) {
            $q->orderBy('booking_date', 'asc')->orderBy('start_time', 'asc');
        }])->orderBy('created_at', 'desc')->paginate(15);

        $selectedPermanentBooking = null;
        if ($this->showCancelModal && $this->cancelBookingId) {
            $selectedPermanentBooking = BookingPermanentbooking::with(['user', 'bookings' => function($q) {
                $q->orderBy('booking_date', 'asc')->orderBy('start_time', 'asc');
            }])->find($this->cancelBookingId);
        }

        return view('livewire.staff.permanent-bookings', [
            'bookings' => $bookings,
            'selectedPermanentBooking' => $selectedPermanentBooking,
        ]);
    }
}

<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\BookingBooking;
use Illuminate\Support\Facades\Auth;

#[Title('My Bookings')]
class MyBookings extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        $query = BookingBooking::with(['sport', 'venue'])
            ->where(function($q) use ($user) {
                $q->where('user_id_id', $user->id)
                  ->orWhere('user_number', $user->phone ?? '')
                  ->orWhere('user_name', $user->name ?? '');
            })
            ->orderByDesc('booking_date');

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('game_name', 'like', '%' . $this->search . '%')
                  ->orWhere('user_name', 'like', '%' . $this->search . '%');
            });
        }

        $bookings = $query->paginate(10);

        $stats = [
            'total'     => BookingBooking::where('user_id_id', $user->id)->count(),
            'upcoming'  => BookingBooking::where('user_id_id', $user->id)
                                ->where('booking_date', '>=', now()->toDateString())
                                ->whereNotIn('status', ['Cancelled'])
                                ->count(),
            'completed' => BookingBooking::where('user_id_id', $user->id)->where('status', 'Completed')->count(),
            'spent'     => BookingBooking::where('user_id_id', $user->id)->where('payment_status', 'Paid')->sum('price'),
        ];

        return view('livewire.customer.my-bookings', [
            'bookings' => $bookings,
            'stats'    => $stats,
        ])->layout('components.layouts.customer');
    }
}

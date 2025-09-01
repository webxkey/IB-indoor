<?php

namespace App\Livewire\Staff;

use App\Models\Booking;
use App\Models\BookingVenue;
use App\Models\Sport;
use App\Models\User;
use App\Models\BookingFacility;
use App\Models\BookingGalleryImage;
use App\Models\BookingVenueReview;
use App\Models\BookingDiscount;
use App\Models\BookingSport;
use App\Models\BookingBooking;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
;

#[Title("Staff Dashboard")]
#[Layout("components.layouts.staff")]
class StaffDashboard extends Component
{
    public $bookingsCount;
    public $sportsCount;
    // public $usersCount;
    public $complex_id;
    public $todaybookingRevenue = 0;
    public $cancelledBookingsCount = 0;
    public $upcomingBookings;
    public $selectedBooking;
    public $selectedDate;
    public $analyticsSlots = [];
    public $showNotifications = false;
    public $recentBookings ;
    public $unreadBookingsCount;
    public $totalBookingsCount;
    public $markedAsRead = false;

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        // Initialize counts with default values or fetch from the database
        $this->bookingsCount = BookingBooking::where('complex_id_id', $this->complex_id)->count(); // Fetch the total number of bookings for this complex
        // dd($this->complex_id);
        $this->sportsCount = BookingSport::where('venue_id', $this->complex_id)->count(); // Fetch the total number of sports\
        $this->todaybookingRevenue = BookingBooking::whereDate('created_at', today())
            ->where('complex_id_id', $this->complex_id)
            ->where('status', 'completed')
            ->sum('price');
        // Calculate today's booking revenue
        $this->cancelledBookingsCount = BookingBooking::where('status', 'Cancelled')
            ->where('complex_id_id', $this->complex_id)
            ->count(); // Count the number of cancelled bookings for today

        $this->selectedDate = Carbon::today()->toDateString();
        $this->complex_id = Auth::user()->complex_id;
        $this->loadDashboardStats(); // Move your count logic here
        $this->generateTimeSlots();
        $this->loadNotifications();
    }

     public function showBookingDetails($booking_id)
    {
        $this->selectedBooking = BookingBooking::find($booking_id);
    }

    public function closeModal()
    {
        $this->selectedBooking = null;
        $this->dispatchBrowserEvent('closeModal');
    }

    public function updatedSelectedDate()
    {
        $this->generateTimeSlots();
    }

    public function generateTimeSlots()
    {
        $date = Carbon::parse($this->selectedDate)->toDateString();

        if (!$date) {
            $this->analyticsSlots = [];
            return;
        }
        

        $games = BookingSport::all();
        $startHour = 6;
        $endHour = 23;

        $analytics = [];

        foreach ($games as $game) {
            $slots = [];

            for ($i = $startHour; $i < $endHour; $i++) {
                $slotStart = Carbon::parse($date)->setHour($i)->setMinute(0)->setSecond(0);
                $slotEnd = $slotStart->copy()->addHour();

                // Check if a booking overlaps with this slot
                $bookingExists = BookingBooking::where('sport_id', $game->game_id)
                    ->whereDate('booking_date', $date)
                    ->where(function ($query) use ($slotStart, $slotEnd) {
                        $query->where(function ($q) use ($slotStart, $slotEnd) {
                            $q->where('start_time', '<', $slotEnd)
                            ->where('end_time', '>', $slotStart);
                        });
                    })
                    ->exists();

                $slots[] = $bookingExists ? 'Booked' : 'Available';
            }

            $analytics[] = [
                'game' => $game->game_name,
                'slots' => $slots,
            ];
        }

        $this->analyticsSlots = $analytics;
    }

    public function loadDashboardStats()
    {
        $this->bookingsCount = BookingBooking::count();
        $this->sportsCount = BookingSport::where('venue_id', $this->complex_id)->count();
        $this->todaybookingRevenue = BookingBooking::whereDate('created_at', today())
            ->where('complex_id_id', $this->complex_id)
            ->where('status', 'completed')
            ->sum('price');
        $this->cancelledBookingsCount = BookingBooking::where('status', 'Cancelled')
            ->where('complex_id_id', $this->complex_id)
            ->count();
    }
    public function loadNotifications()
    {
        $this->recentBookings = BookingBooking::where('created_at', '>', now()
        ->subDays(1))
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        $this->unreadBookingsCount = BookingBooking::where('created_at', '>', now()->subDays(3))
            ->count();

        $this->totalBookingsCount = BookingBooking::count();
    }

    public function markAllAsRead()
    {
        BookingBooking::whereNull('viewed_at')
            ->update(['viewed_at' => now()]);

        $this->markedAsRead = true;
        $this->dispatchBrowserEvent('notifications-marked-read');
        
        // Optional: Automatically close modal after 2 seconds
        $this->dispatchBrowserEvent('close-modal-after-delay', [
            'delay' => 2000,
            'modalId' => 'bookingNotificationsModal'
        ]);
    }


    public function render()
    {
        return view('livewire.staff.staff-dashboard')
            ->with([
                'bookingsCount' => $this->bookingsCount,
                'sportsCount' => $this->sportsCount,
                'sportsCount' => $this->sportsCount,
                'cancelledBookingsCount' => $this->cancelledBookingsCount,
                'todaybookingRevenue' => $this->todaybookingRevenue,
            ]);
    }
}

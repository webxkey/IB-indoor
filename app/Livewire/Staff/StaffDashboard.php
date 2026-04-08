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
use Illuminate\Support\Facades\Auth;;

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
    public $recentBookings;
    public $unreadBookingsCount;
    public $totalBookingsCount;
    public $markedAsRead = false;
    
    // Slot Availability properties
    public $slotAvailabilityDate;
    public $selectedSportFilter = 'all';
    public $sportsWithSlots = [];
    public $calendarMonth;
    public $calendarYear;
    public $calendarDays = [];
    public $todayUpcomingBookings = [];
    
    // Add Booking Modal properties
    public $showAddBookingModal = false;
    public $bookingFormSportId;
    public $bookingFormSportName;
    public $bookingFormHour;
    public $bookingFormDate;
    public $bookingFormUserName;
    public $bookingFormUserNumber;
    public $bookingFormCourtNumber = 1;
    public $bookingFormNotes;

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        // Initialize counts with default values or fetch from the database
        $this->bookingsCount = BookingBooking::where('complex_id_id', $this->complex_id)->count(); // Fetch the total number of bookings for this complex
        // dd($this->complex_id);
        $this->sportsCount = BookingSport::where('venue_id', $this->complex_id)->count(); // Fetch the total number of sports\
        $this->todaybookingRevenue = BookingBooking::whereDate('created_at', today())
            ->where('complex_id_id', $this->complex_id)
            ->where('status', 'Completed')
            ->sum('price');
        // Calculate today's booking revenue
        $this->cancelledBookingsCount = BookingBooking::where('status', 'Cancelled')
            ->where('complex_id_id', $this->complex_id)
            ->count(); // Count the number of cancelled bookings for today

        $this->selectedDate = Carbon::today()->toDateString();
        $this->complex_id = Auth::user()->complex_id;
        $this->loadDashboardStats(); // Move your count logic here
        $this->loadUpcomingBookings(); // Load upcoming bookings for this venue
        $this->generateTimeSlots();
        $this->loadNotifications();
        
        // Initialize slot availability
        $this->slotAvailabilityDate = Carbon::today()->toDateString();
        $this->calendarMonth = Carbon::today()->month;
        $this->calendarYear = Carbon::today()->year;
        $this->loadSlotAvailability();
        $this->generateCalendarDays();
        $this->loadTodayUpcomingBookings();
    }

    public function showBookingDetails($booking_id)
    {
        // Only show booking details if it belongs to the current venue
        $this->selectedBooking = BookingBooking::where('id', $booking_id)
            ->where('complex_id_id', $this->complex_id)
            ->first();
    }

    public function closeModal()
    {
        $this->selectedBooking = null;
        $this->dispatch('closeModal');
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

        // Only get games from the current venue
        $games = BookingSport::where('venue_id', $this->complex_id)->get();
        $startHour = 6;
        $endHour = 23;

        $analytics = [];

        foreach ($games as $game) {
            $slots = [];

            for ($i = $startHour; $i < $endHour; $i++) {
                $slotStart = Carbon::parse($date)->setHour($i)->setMinute(0)->setSecond(0);
                $slotEnd = $slotStart->copy()->addHour();

                // Check if a booking overlaps with this slot (filtered by complex)
                $bookingExists = BookingBooking::where('game_id_id', $game->id)
                    ->where('complex_id_id', $this->complex_id)
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
                'game' => $game->name,
                'slots' => $slots,
            ];
        }

        $this->analyticsSlots = $analytics;
    }

    public function loadUpcomingBookings()
    {
        // Load only upcoming bookings for the current venue
        $this->upcomingBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('booking_date', '>=', Carbon::today()->toDateString())
            ->where('status', '!=', 'Cancelled')
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    public function loadDashboardStats()
    {
        // Filter all stats by current venue only
        $this->bookingsCount = BookingBooking::where('complex_id_id', $this->complex_id)->count();
        $this->sportsCount = BookingSport::where('venue_id', $this->complex_id)->count();
        $this->todaybookingRevenue = BookingBooking::whereDate('created_at', today())
            ->where('complex_id_id', $this->complex_id)
            ->where('status', 'Completed')
            ->sum('price');
        $this->cancelledBookingsCount = BookingBooking::where('status', 'Cancelled')
            ->where('complex_id_id', $this->complex_id)
            ->count();
    }
    public function loadNotifications()
    {
        // Only show notifications for the current venue
        $this->recentBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('created_at', '>', now()->subDays(1))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $this->unreadBookingsCount = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('created_at', '>', now()->subDays(3))
            ->count();

        $this->totalBookingsCount = BookingBooking::where('complex_id_id', $this->complex_id)->count();
    }

    public function markAllAsRead()
    {
        BookingBooking::where('complex_id_id', $this->complex_id)
            ->whereNull('viewed_at')
            ->update(['viewed_at' => now()]);

        $this->markedAsRead = true;
        $this->dispatch('notifications-marked-read');
    }

    // Slot Availability Methods
    public function loadSlotAvailability()
    {
        $date = Carbon::parse($this->slotAvailabilityDate)->toDateString();
        $selectedDate = Carbon::parse($this->slotAvailabilityDate);
        $isToday = $selectedDate->isToday();
        $currentHour = Carbon::now()->hour;
        
        // Get venue opening hours
        $venue = BookingVenue::find($this->complex_id);
        $dayName = strtolower($selectedDate->format('l')); // monday, tuesday, etc.
        
        // Default hours if no opening hours set
        $startHour = 6; // 6 AM default
        $endHour = 22; // 10 PM default
        $isClosed = false;
        
        if ($venue && $venue->opening_hours && isset($venue->opening_hours[$dayName])) {
            $dayHours = $venue->opening_hours[$dayName];
            
            // Check if closed for this day
            if (!empty($dayHours['closed'])) {
                $isClosed = true;
            } else {
                // Parse opening hours
                if (!empty($dayHours['open'])) {
                    $openTime = Carbon::parse($dayHours['open']);
                    $startHour = $openTime->hour;
                }
                if (!empty($dayHours['close'])) {
                    $closeTime = Carbon::parse($dayHours['close']);
                    $endHour = $closeTime->hour;
                }
            }
        }
        
        // Get sports for this venue (case-insensitive status check)
        $sports = BookingSport::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->get();

        $this->sportsWithSlots = [];
        
        // If venue is closed on this day
        if ($isClosed) {
            foreach ($sports as $sport) {
                if ($this->selectedSportFilter !== 'all' && $sport->id != $this->selectedSportFilter) {
                    continue;
                }
                
                $this->sportsWithSlots[] = [
                    'id' => $sport->id,
                    'name' => $sport->name,
                    'image' => $sport->image,
                    'slots' => [],
                    'availableCount' => 0,
                    'badge' => 'Closed',
                    'badgeType' => 'danger',
                    'maxCourts' => $sport->maximum_court ?? 1,
                    'isClosed' => true,
                ];
            }
            return;
        }

        foreach ($sports as $sport) {
            // Apply sport filter
            if ($this->selectedSportFilter !== 'all' && $sport->id != $this->selectedSportFilter) {
                continue;
            }

            $slots = [];
            $availableCount = 0;

            for ($i = $startHour; $i < $endHour; $i++) {
                $slotStart = Carbon::parse($date)->setHour($i)->setMinute(0)->setSecond(0);
                $slotEnd = $slotStart->copy()->addHour();
                
                // Skip past time slots for today
                if ($isToday && $i <= $currentHour) {
                    continue; // Don't show past slots
                }

                // Check booking status for this slot
                $booking = BookingBooking::where('game_id_id', $sport->id)
                    ->where('complex_id_id', $this->complex_id)
                    ->whereDate('booking_date', $date)
                    ->where(function ($query) use ($slotStart, $slotEnd) {
                        $query->where('start_time', '<', $slotEnd)
                            ->where('end_time', '>', $slotStart);
                    })
                    ->first();

                $status = 'available';
                if ($booking) {
                    $status = 'booked';
                }

                if ($status === 'available') {
                    $availableCount++;
                }

                $slots[] = [
                    'time' => $slotStart->format('h:i A'),
                    'status' => $status,
                    'hour' => $i,
                ];
            }

            // Determine badge type based on bookings
            $totalSlots = count($slots);
            $badge = null;
            $badgeType = null;

            if ($totalSlots > 0) {
                $bookedPercentage = (($totalSlots - $availableCount) / $totalSlots) * 100;
                
                if ($bookedPercentage >= 70) {
                    $badge = 'High Demand';
                    $badgeType = 'warning';
                } elseif ($availableCount > 0) {
                    $badge = $availableCount . ' Available';
                    $badgeType = 'success';
                } else {
                    $badge = 'Fully Booked';
                    $badgeType = 'danger';
                }
            } else {
                $badge = 'No Slots';
                $badgeType = 'secondary';
            }

            $this->sportsWithSlots[] = [
                'id' => $sport->id,
                'name' => $sport->name,
                'image' => $sport->image,
                'slots' => $slots,
                'availableCount' => $availableCount,
                'badge' => $badge,
                'badgeType' => $badgeType,
                'maxCourts' => $sport->maximum_court ?? 1,
                'isClosed' => false,
            ];
        }
    }

    public function updatedSlotAvailabilityDate()
    {
        $this->loadSlotAvailability();
    }

    public function updatedSelectedSportFilter()
    {
        $this->loadSlotAvailability();
    }

    public function selectCalendarDate($day)
    {
        $this->slotAvailabilityDate = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, $day)->toDateString();
        $this->generateCalendarDays(); // Regenerate to update selected state
        $this->loadSlotAvailability();
    }

    public function previousMonth()
    {
        $date = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarMonth = $date->month;
        $this->calendarYear = $date->year;
        $this->generateCalendarDays();
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarMonth = $date->month;
        $this->calendarYear = $date->year;
        $this->generateCalendarDays();
    }

    public function generateCalendarDays()
    {
        $firstDay = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1);
        $lastDay = $firstDay->copy()->endOfMonth();
        $startPadding = $firstDay->dayOfWeek; // 0 = Sunday
        
        $days = [];
        
        // Add padding for days before the month starts
        for ($i = 0; $i < $startPadding; $i++) {
            $prevDate = $firstDay->copy()->subDays($startPadding - $i);
            $days[] = [
                'day' => $prevDate->day,
                'current' => false,
                'today' => false,
                'selected' => false,
            ];
        }
        
        // Add days of the month
        for ($d = 1; $d <= $lastDay->day; $d++) {
            $currentDate = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, $d);
            $days[] = [
                'day' => $d,
                'current' => true,
                'today' => $currentDate->isToday(),
                'selected' => $currentDate->toDateString() === $this->slotAvailabilityDate,
            ];
        }
        
        $this->calendarDays = $days;
    }

    public function loadTodayUpcomingBookings()
    {
        $this->todayUpcomingBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->whereDate('booking_date', Carbon::today())
            ->where('status', '!=', 'Cancelled')
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();
    }

    public function getSportsListProperty()
    {
        return BookingSport::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->get();
    }

    // Add Booking Modal Methods
    public function openAddBookingModal($sportId, $sportName, $hour)
    {
        $this->bookingFormSportId = $sportId;
        $this->bookingFormSportName = $sportName;
        $this->bookingFormHour = $hour;
        $this->bookingFormDate = $this->slotAvailabilityDate;
        $this->bookingFormUserName = '';
        $this->bookingFormUserNumber = '';
        $this->bookingFormCourtNumber = 1;
        $this->bookingFormNotes = '';
        $this->showAddBookingModal = true;
    }

    public function closeAddBookingModal()
    {
        $this->showAddBookingModal = false;
        $this->resetBookingForm();
    }

    public function resetBookingForm()
    {
        $this->bookingFormSportId = null;
        $this->bookingFormSportName = null;
        $this->bookingFormHour = null;
        $this->bookingFormDate = null;
        $this->bookingFormUserName = '';
        $this->bookingFormUserNumber = '';
        $this->bookingFormCourtNumber = 1;
        $this->bookingFormNotes = '';
    }

    public function saveQuickBooking()
    {
        $this->validate([
            'bookingFormUserName' => 'required|min:2',
            'bookingFormUserNumber' => 'required|min:10',
            'bookingFormCourtNumber' => 'required|integer|min:1',
        ]);

        $sport = BookingSport::find($this->bookingFormSportId);
        $startTime = Carbon::parse($this->bookingFormDate)->setHour($this->bookingFormHour)->setMinute(0)->setSecond(0);
        $endTime = $startTime->copy()->addHour();

        BookingBooking::create([
            'game_name' => $sport->name,
            'game_id_id' => $this->bookingFormSportId,
            'complex_id_id' => $this->complex_id,
            'user_name' => $this->bookingFormUserName,
            'user_number' => $this->bookingFormUserNumber,
            'court_number' => $this->bookingFormCourtNumber,
            'booking_date' => $this->bookingFormDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => 1,
            'price' => $sport->price ?? 0,
            'status' => 'Confirmed',
            'payment_status' => 'Pending',
            'is_challenge_booking' => false,

            'notes' => $this->bookingFormNotes,
        ]);

        $this->closeAddBookingModal();
        $this->loadSlotAvailability();
        $this->loadTodayUpcomingBookings();
        $this->loadDashboardStats();
        
        session()->flash('message', 'Booking created successfully!');
    }


    public function render()
    {
        return view('livewire.staff.staff-dashboard')
            ->with([
                'bookingsCount' => $this->bookingsCount,
                'sportsCount' => $this->sportsCount,
                'cancelledBookingsCount' => $this->cancelledBookingsCount,
                'todaybookingRevenue' => $this->todaybookingRevenue,
            ]);
    }
}

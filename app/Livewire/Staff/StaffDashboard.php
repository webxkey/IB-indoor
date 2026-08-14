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
use App\Models\PoolsPool;
use App\Models\PoolsPoolbooking;
use App\Models\PoolsPoolsessionoccurrence;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        $this->selectedDate = Carbon::today()->toDateString();
        
        $this->loadDashboardStats(); // Load combined stats
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

    public function showBookingDetails($booking_id, $type = 'sport')
    {
        if ($type === 'pool') {
            $pb = PoolsPoolbooking::with(['pool', 'occurrence'])
                ->where('id', $booking_id)
                ->whereHas('pool', function ($q) {
                    $q->where('venue_id', $this->complex_id);
                })
                ->first();

            if ($pb) {
                $this->selectedBooking = (object) [
                    'id' => $pb->id,
                    'game_name' => $pb->pool ? ($pb->pool->name ?: 'Pools') : 'Pools',
                    'user_name' => $pb->user_name,
                    'court_number' => 'Pool',
                    'status' => $pb->status,
                    'start_time' => $pb->occurrence ? $pb->occurrence->start_time : $pb->created_at,
                    'end_time' => $pb->occurrence ? $pb->occurrence->end_time : null,
                    'booking_date' => $pb->occurrence ? $pb->occurrence->session_date : $pb->created_at,
                ];
            }
        } else {
            // Only show booking details if it belongs to the current venue
            $this->selectedBooking = BookingBooking::where('id', $booking_id)
                ->where('complex_id_id', $this->complex_id)
                ->first();
        }
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

        // Get games from the current venue
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
        // Load upcoming regular bookings for the current venue
        $this->upcomingBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('booking_date', '>=', Carbon::today()->toDateString())
            ->where('status', '!=', 'Cancelled')
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    public function loadDashboardStats()
    {
        // Total Sports (BookingSport + PoolsPool)
        $sportsCount = BookingSport::where('venue_id', $this->complex_id)->count();
        $poolsCount = PoolsPool::where('venue_id', $this->complex_id)->count();
        $this->sportsCount = $sportsCount + $poolsCount;

        // Total Bookings (BookingBooking + PoolsPoolbooking)
        $regularBookingsCount = BookingBooking::where('complex_id_id', $this->complex_id)->count();
        $poolBookingsCount = PoolsPoolbooking::whereHas('pool', function ($q) {
            $q->where('venue_id', $this->complex_id);
        })->count();
        $this->bookingsCount = $regularBookingsCount + $poolBookingsCount;

        // Revenue for today
        $regularRevenue = BookingBooking::whereDate('created_at', today())
            ->where('complex_id_id', $this->complex_id)
            ->whereIn('status', ['Completed', 'Confirmed', 'Played', 'played'])
            ->sum('amount_paid');
            
        if ($regularRevenue == 0) {
            $regularRevenue = BookingBooking::whereDate('created_at', today())
                ->where('complex_id_id', $this->complex_id)
                ->whereIn('status', ['Completed', 'Confirmed', 'Played', 'played'])
                ->sum('price');
        }

        $poolRevenue = PoolsPoolbooking::whereDate('created_at', today())
            ->whereHas('pool', function ($q) {
                $q->where('venue_id', $this->complex_id);
            })
            ->whereIn('status', ['Confirmed', 'Played', 'played', 'Completed'])
            ->sum('amount_paid');

        if ($poolRevenue == 0) {
            $poolRevenue = PoolsPoolbooking::whereDate('created_at', today())
                ->whereHas('pool', function ($q) {
                    $q->where('venue_id', $this->complex_id);
                })
                ->whereIn('status', ['Confirmed', 'Played', 'played', 'Completed'])
                ->sum('booking_total');
        }

        $this->todaybookingRevenue = $regularRevenue + $poolRevenue;

        // Cancelled bookings count
        $regularCancelled = BookingBooking::where('status', 'Cancelled')
            ->where('complex_id_id', $this->complex_id)
            ->count();
            
        $poolCancelled = PoolsPoolbooking::where('status', 'Cancelled')
            ->whereHas('pool', function ($q) {
                $q->where('venue_id', $this->complex_id);
            })
            ->count();

        $this->cancelledBookingsCount = $regularCancelled + $poolCancelled;
    }

    public function loadNotifications()
    {
        // Show notifications for current venue (BookingBooking + PoolsPoolbooking)
        $regularBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('created_at', '>', now()->subDays(1))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $poolBookings = PoolsPoolbooking::whereHas('pool', function ($q) {
                $q->where('venue_id', $this->complex_id);
            })
            ->where('created_at', '>', now()->subDays(1))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $this->recentBookings = $regularBookings->concat($poolBookings)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $regularUnread = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('created_at', '>', now()->subDays(3))
            ->count();

        $poolUnread = PoolsPoolbooking::whereHas('pool', function ($q) {
                $q->where('venue_id', $this->complex_id);
            })
            ->where('created_at', '>', now()->subDays(3))
            ->count();

        $this->unreadBookingsCount = $regularUnread + $poolUnread;

        $regularTotal = BookingBooking::where('complex_id_id', $this->complex_id)->count();
        $poolTotal = PoolsPoolbooking::whereHas('pool', function ($q) {
            $q->where('venue_id', $this->complex_id);
        })->count();

        $this->totalBookingsCount = $regularTotal + $poolTotal;
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
        
        // Get regular sports for this venue
        $sports = BookingSport::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->get();

        // Get pools from pools_pool table
        $pools = PoolsPool::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->get();

        $this->sportsWithSlots = [];

        // If venue is closed on this day
        if ($isClosed) {
            foreach ($sports as $sport) {
                if ($this->selectedSportFilter !== 'all' && (string)$sport->id !== (string)$this->selectedSportFilter) {
                    continue;
                }
                
                $this->sportsWithSlots[] = [
                    'id' => (string) $sport->id,
                    'name' => $sport->name,
                    'image' => $sport->image,
                    'slots' => [],
                    'availableCount' => 0,
                    'badge' => 'Closed',
                    'badgeType' => 'danger',
                    'maxCourts' => $sport->maximum_court ?? 1,
                    'is_pool' => false,
                    'isClosed' => true,
                ];
            }

            foreach ($pools as $pool) {
                $poolIdStr = 'pool_' . $pool->id;
                if ($this->selectedSportFilter !== 'all' && $poolIdStr !== (string)$this->selectedSportFilter && (string)$pool->id !== (string)$this->selectedSportFilter) {
                    continue;
                }

                $this->sportsWithSlots[] = [
                    'id' => $poolIdStr,
                    'name' => $pool->name ?: 'Pools',
                    'image' => $pool->image ?: asset('images/sports_images/pools.jpg'),
                    'slots' => [],
                    'availableCount' => 0,
                    'badge' => 'Closed',
                    'badgeType' => 'danger',
                    'maxCourts' => $pool->capacity ?: 20,
                    'is_pool' => true,
                    'isClosed' => true,
                ];
            }
            return;
        }

        // Process Regular Sports
        foreach ($sports as $sport) {
            if ($this->selectedSportFilter !== 'all' && (string)$sport->id !== (string)$this->selectedSportFilter) {
                continue;
            }

            $slots = [];
            $availableCount = 0;

            for ($i = $startHour; $i < $endHour; $i++) {
                $slotStart = Carbon::parse($date)->setHour($i)->setMinute(0)->setSecond(0);
                $slotEnd = $slotStart->copy()->addHour();
                
                // Skip past time slots for today
                if ($isToday && $i <= $currentHour) {
                    continue;
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

                $status = $booking ? 'booked' : 'available';

                if ($status === 'available') {
                    $availableCount++;
                }

                $slots[] = [
                    'time' => $slotStart->format('h:i A'),
                    'status' => $status,
                    'hour' => $i,
                ];
            }

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
                'id' => (string) $sport->id,
                'name' => $sport->name,
                'image' => $sport->image,
                'slots' => $slots,
                'availableCount' => $availableCount,
                'badge' => $badge,
                'badgeType' => $badgeType,
                'maxCourts' => $sport->maximum_court ?? 1,
                'is_pool' => false,
                'isClosed' => false,
            ];
        }

        // Process Pools from pools_pool
        foreach ($pools as $pool) {
            $poolIdStr = 'pool_' . $pool->id;
            if ($this->selectedSportFilter !== 'all' && $poolIdStr !== (string)$this->selectedSportFilter && (string)$pool->id !== (string)$this->selectedSportFilter) {
                continue;
            }

            $slots = [];
            $availableCount = 0;
            $capacity = $pool->capacity ?: 20;

            for ($i = $startHour; $i < $endHour; $i++) {
                $slotStart = Carbon::parse($date)->setHour($i)->setMinute(0)->setSecond(0);
                $slotEnd = $slotStart->copy()->addHour();
                
                if ($isToday && $i <= $currentHour) {
                    continue;
                }

                // Check pool bookings for this slot
                $poolBookings = PoolsPoolbooking::where('pool_id', $pool->id)
                    ->where('status', '!=', 'Cancelled')
                    ->where(function ($query) use ($date, $slotStart, $slotEnd) {
                        $query->whereHas('occurrence', function ($q) use ($date, $slotStart, $slotEnd) {
                            $q->whereDate('session_date', $date)
                                ->where('start_time', '<', $slotEnd->format('H:i:s'))
                                ->where('end_time', '>', $slotStart->format('H:i:s'));
                        })->orWhere(function ($q) use ($date) {
                            $q->whereNull('occurrence_id')->whereDate('created_at', $date);
                        });
                    })
                    ->get();

                $totalAdmissionsBooked = $poolBookings->sum('total_admissions');
                $status = ($totalAdmissionsBooked >= $capacity) ? 'booked' : 'available';

                if ($status === 'available') {
                    $availableCount++;
                }

                $slots[] = [
                    'time' => $slotStart->format('h:i A'),
                    'status' => $status,
                    'hour' => $i,
                ];
            }

            $totalSlots = count($slots);
            $badge = null;
            $badgeType = null;

            if ($totalSlots > 0) {
                if ($availableCount > 0) {
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
                'id' => $poolIdStr,
                'name' => $pool->name ?: 'Pools',
                'image' => $pool->image ?: asset('images/sports_images/pools.jpg'),
                'slots' => $slots,
                'availableCount' => $availableCount,
                'badge' => $badge,
                'badgeType' => $badgeType,
                'maxCourts' => $capacity,
                'is_pool' => true,
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
        $sportBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->whereDate('booking_date', Carbon::today())
            ->where('status', '!=', 'Cancelled')
            ->get()
            ->map(function ($b) {
                return (object) [
                    'id' => $b->id,
                    'type' => 'sport',
                    'user_name' => $b->user_name,
                    'game_name' => $b->game_name,
                    'booking_date' => $b->booking_date,
                    'start_time' => $b->start_time,
                    'end_time' => $b->end_time,
                    'status' => $b->status,
                    'court_number' => $b->court_number,
                ];
            });

        $poolBookings = PoolsPoolbooking::whereHas('pool', function ($q) {
                $q->where('venue_id', $this->complex_id);
            })
            ->where('status', '!=', 'Cancelled')
            ->where(function ($q) {
                $q->whereHas('occurrence', function ($occQ) {
                    $occQ->whereDate('session_date', Carbon::today());
                })->orWhere(function ($createdQ) {
                    $createdQ->whereNull('occurrence_id')->whereDate('created_at', Carbon::today());
                });
            })
            ->with(['occurrence', 'pool'])
            ->get()
            ->map(function ($pb) {
                $startTime = $pb->occurrence ? $pb->occurrence->start_time : $pb->created_at;
                $dateStr = $pb->occurrence ? $pb->occurrence->session_date : $pb->created_at;
                return (object) [
                    'id' => $pb->id,
                    'type' => 'pool',
                    'user_name' => $pb->user_name,
                    'game_name' => $pb->pool ? ($pb->pool->name ?: 'Pools') : 'Pools',
                    'booking_date' => $dateStr,
                    'start_time' => $startTime,
                    'end_time' => $pb->occurrence ? $pb->occurrence->end_time : null,
                    'status' => $pb->status,
                    'court_number' => 'Pool',
                ];
            });

        $this->todayUpcomingBookings = $sportBookings->concat($poolBookings)
            ->sortBy(function ($item) {
                return Carbon::parse($item->start_time)->format('H:i:s');
            })
            ->take(5)
            ->values();
    }

    public function getSportsListProperty()
    {
        $sports = BookingSport::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->get()
            ->map(function ($sport) {
                return (object) [
                    'id' => (string) $sport->id,
                    'name' => $sport->name,
                    'is_pool' => false,
                ];
            });

        $pools = PoolsPool::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->get()
            ->map(function ($pool) {
                return (object) [
                    'id' => 'pool_' . $pool->id,
                    'name' => $pool->name ?: 'Pools',
                    'is_pool' => true,
                ];
            });

        return $sports->concat($pools);
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

        if (is_string($this->bookingFormSportId) && str_starts_with($this->bookingFormSportId, 'pool_')) {
            $poolId = (int) str_replace('pool_', '', $this->bookingFormSportId);
            $pool = PoolsPool::find($poolId);

            if ($pool) {
                $startTime = Carbon::parse($this->bookingFormDate)->setHour($this->bookingFormHour)->setMinute(0)->setSecond(0);
                $endTime = $startTime->copy()->addHour();

                $occ = PoolsPoolsessionoccurrence::firstOrCreate(
                    [
                        'pool_id' => $pool->id,
                        'session_date' => $this->bookingFormDate,
                        'start_time' => $startTime->format('H:i:s'),
                        'end_time' => $endTime->format('H:i:s'),
                    ],
                    [
                        'name' => 'Pool Slot ' . $startTime->format('H:i:s'),
                        'capacity' => $pool->capacity ?: 50,
                        'status' => 'open',
                    ]
                );

                $ref = 'POOL-' . strtoupper(Str::random(6));

                PoolsPoolbooking::create([
                    'booking_reference' => $ref,
                    'pool_id' => $pool->id,
                    'user_id' => Auth::id(),
                    'occurrence_id' => $occ->id,
                    'user_name' => $this->bookingFormUserName,
                    'user_number' => $this->bookingFormUserNumber,
                    'status' => 'Confirmed',
                    'total_admissions' => $this->bookingFormCourtNumber ?: 1,
                    'booking_total' => 0.00,
                    'total_amount' => 0.00,
                    'amount_paid' => 0.00,
                    'balance_due' => 0.00,
                    'financial_status' => 'Pending',
                    'payment_status' => 'Pending',
                    'notes' => $this->bookingFormNotes,
                ]);
            }
        } else {
            $sport = BookingSport::find($this->bookingFormSportId);
            $startTime = Carbon::parse($this->bookingFormDate)->setHour($this->bookingFormHour)->setMinute(0)->setSecond(0);
            $endTime = $startTime->copy()->addHour();

            BookingBooking::create([
                'game_name' => $sport ? $sport->name : 'Sport',
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
                'is_private' => false,
                'notes' => $this->bookingFormNotes,
            ]);
        }

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


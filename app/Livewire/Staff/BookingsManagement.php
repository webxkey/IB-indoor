<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Carbon\Carbon;
use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\BookingWaitlist;
use App\Models\UserUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Storage;

#[Title("Staff Dashboard")]
#[Layout("components.layouts.staff")]
class BookingsManagement extends Component
{
    public $sports;
    public $bookings;
    public $complex_id;
    public $games;
    public $bookingdetails = [];
    public $opening_hours = [];

    public $selectedGame = '';
    public $selectedDate = '';
    public $selectedTime = '';
    public $selectedCourt = '';
    public $playerName = '';
    public $phoneNumber = '';
    public $status = 'Confirmed';
    public $permanent = false;
    public $notes = '';

    // Last known booking count and latest ID for change detection
    public $lastBookingCount = 0;
    public $lastBookingId = 0;

    // Slot blocking
    public $blockingSlot = null; // ['sportId'=>, 'date'=>, 'time'=>, 'court'=>]
    public $blockReason = 'Maintenance';
    public $showBlockModal = false;

    // Waitlist
    public $waitlistEntries = [];
    public $showWaitlistModal = false;
    public $waitlistSportId = null;
    public $waitlistDate = '';
    public $waitlistTime = '';
    public $waitlistCourt = '';
    public $waitlistName = '';
    public $waitlistPhone = '';

    protected $rules = [
        'selectedGame' => 'required|string',
        'selectedDate' => 'required|date_format:Y-m-d',
        'selectedTime' => 'required',
        'selectedCourt' => 'required|string',
        'playerName' => 'required|string|max:255',
        'phoneNumber' => 'required|string|max:20',
        'status' => 'required|in:Confirmed,Pending,Completed,Cancelled,No-Show,Playing',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $listeners = [
        'setSelectedBookingData',
        'refreshBookings',
        'echo:bookings.complex.{complex_id},booking.created' => 'handleNewBooking',
        'echo:bookings.complex.{complex_id},booking.updated' => 'handleUpdatedBooking',
        'echo:bookings.complex.{complex_id},booking.deleted' => 'handleDeletedBooking',
    ];

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        $this->checkAndUpdateBookingStatuses();
        $this->loadSports();
        $this->updateChangeTracking();
    }

    /**
     * Smart polling - checks for changes every 3 seconds
     * Only refreshes data when booking count or latest ID changes
     * This is very lightweight - just 2 quick COUNT/MAX queries
     */
    public function checkForChanges()
    {
        $currentCount = BookingBooking::where('complex_id_id', $this->complex_id)->count();
        $latestId = BookingBooking::where('complex_id_id', $this->complex_id)->max('id') ?? 0;

        // Check if anything changed
        if ($currentCount !== $this->lastBookingCount || $latestId !== $this->lastBookingId) {
            Log::info('Booking change detected', [
                'old_count' => $this->lastBookingCount,
                'new_count' => $currentCount,
                'old_id' => $this->lastBookingId,
                'new_id' => $latestId,
            ]);

            $this->lastBookingCount = $currentCount;
            $this->lastBookingId = $latestId;
            
            // Reload the full booking data
            $this->loadSports();
            
            // Dispatch browser event for notification
            $this->dispatch('bookingDataChanged');
            
            return true;
        }

        return false;
    }

    /**
     * Update change tracking variables
     */
    private function updateChangeTracking()
    {
        $this->lastBookingCount = BookingBooking::where('complex_id_id', $this->complex_id)->count();
        $this->lastBookingId = BookingBooking::where('complex_id_id', $this->complex_id)->max('id') ?? 0;
    }

    #[On('refreshBookings')]
    public function refreshBookings()
    {
        $this->loadSports();
        $this->updateChangeTracking();
    }

    public function loadSports()
    {
        try {
            $this->sports = BookingSport::where('venue_id', $this->complex_id)
                ->where('status', 'Active')
                ->get();

            // Load and normalize opening hours from venue
            $venue = \App\Models\BookingVenue::find($this->complex_id);
            $dbHours = [];
            if ($venue) {
                $dbHours = is_string($venue->opening_hours) ? json_decode($venue->opening_hours, true) : ($venue->opening_hours ?? []);
            }
            $this->opening_hours = $this->parseOpeningHoursFromDb($dbHours);

            // Only load non-cancelled bookings for display
            $this->bookings = BookingBooking::where('complex_id_id', $this->complex_id)
                ->whereNotIn('status', ['Cancelled'])
                ->with('user') // Eager load user relationship to avoid N+1 queries
                ->get();
            // $user_userIds = $this->bookings->pluck('user_id_id')->unique()->toArray();  
            // $userUsersProfile = UserUser::whereIn('id', $user_userIds)
            //     ->get(['id', 'profile_picture'])
            //     ->keyBy('id');
            // dd($userUsersProfile->toArray());

            $this->games = $this->sports->map(function ($sport) {
                return [
                    'name' => $sport->name ?? 'Unknown',
                    'maximum_court' => $sport->maximum_court ?? 1,
                    'game_id' => $sport->id,
                ];
            })->toArray();

            $this->bookingdetails = [];
            foreach ($this->bookings as $booking) {
                $game = strtolower($booking->game_name ?? '');
                if (empty($game)) continue;

                $date = Carbon::parse($booking->booking_date)->format('Y-m-d');
                $court = $booking->court_number ?? '1';
                $startTime = Carbon::parse($booking->start_time);
                $endTime = $booking->end_time
                    ? Carbon::parse($booking->end_time)
                    : $startTime->copy()->addHour();

                $hours = max(1, $startTime->diffInHours($endTime));

                for ($i = 0; $i < $hours; $i++) {
                    $slotKey = $startTime->copy()->addHours($i)->format('H:i:s');
                    $slotEnd = $startTime->copy()->addHours($i + 1)->format('H:i:s');

                    // Get user's profile picture or use default
                    $user = $booking->user;
                    $avatarUrl = '/storage/staff/user.png';
                    if ($user && $user->profile_picture) {
                        $avatarUrl = Storage::url($user->profile_picture);
                    }

                    $this->bookingdetails[$game][$date][$court][$slotKey] = [
                        'player' => $booking->user_name ?? 'Unknown',
                        'phone' => $booking->user_number ?? 'N/A',
                        'status' => $booking->status ?? 'Pending',
                        'permanent_source_id' => $booking->permanent_source_id,
                        'end' => $slotEnd,
                        'avatar' => $avatarUrl,
                        'id' => $booking->id,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in loadSports', ['error' => $e->getMessage()]);
            $this->addError('general', 'Failed to load data. Please refresh.');
        }
    }

    /**
     * Parse opening hours similar to StaffSetting
     */
    private function parseOpeningHoursFromDb($dbHours)
    {
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $result = [];
        foreach ($days as $day) {
            $result[$day] = ['open' => null, 'close' => null, 'closed' => false];
        }

        if (!is_array($dbHours)) {
            return $result;
        }

        foreach ($dbHours as $key => $value) {
            $k = strtolower($key);
            if (!in_array($k, $days)) continue;
            if (is_string($value)) {
                if (strpos($value, '-') !== false) {
                    [$open, $close] = array_map('trim', explode('-', $value, 2));
                    $result[$k] = ['open' => $open, 'close' => $close, 'closed' => false];
                } elseif (strtolower($value) === 'closed') {
                    $result[$k] = ['open' => null, 'close' => null, 'closed' => true];
                } else {
                    $result[$k] = ['open' => $value, 'close' => null, 'closed' => false];
                }
            } elseif (is_array($value)) {
                $open = $value['open'] ?? ($value[0] ?? null);
                $close = $value['close'] ?? ($value[1] ?? null);
                $closed = isset($value['closed']) ? (bool) $value['closed'] : false;
                $result[$k] = ['open' => $open, 'close' => $close, 'closed' => $closed];
            }
        }

        return $result;
    }

    public function getBookingDetails()
    {
        $this->loadSports();
        return $this->bookingdetails;
    }

    public function setSelectedBookingData($data)
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $this->selectedGame = $data['game'] ?? '';
        $this->selectedDate = $data['dateKey'] ?? '';
        $this->selectedTime = $data['time'] ?? '';
        $this->selectedCourt = $data['court'] ?? '';
    }

    public function addBooking()
    {
        $this->validate();

        // Find sport (case-insensitive to handle lowercase game names from calendar)
        $sport = BookingSport::whereRaw('LOWER(name) = ?', [strtolower($this->selectedGame)])
            ->where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->first();

        if (!$sport) {
            $this->addError('general', 'Selected game is not available.');
            return;
        }

        $staffUser = Auth::user();
        if (!$staffUser || !$staffUser->complex_id) {
            $this->addError('general', 'Your account is not properly configured.');
            return;
        }

        // Find corresponding users_user record (may not exist for admin-created accounts)
        $userUser = UserUser::where('email', $staffUser->email)->first();

        // Normalize time format
        $startTime = $this->selectedTime;
        if (strlen($startTime) === 5) {
            $startTime .= ':00';
        }

        $endTime = Carbon::parse($startTime)->addMinutes(60)->format('H:i:s');

        $bookingData = [
            'user_id_id' => $userUser?->id, // null if no users_user record
            'complex_id_id' => $this->complex_id,
            'game_id_id' => $sport->id,
            'game_name' => $this->selectedGame,
            'booking_date' => $this->selectedDate,
            'permanent_source_id' => null,
            'user_name' => $this->playerName,
            'user_number' => $this->phoneNumber,
            'court_number' => $this->selectedCourt,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => 60,
            'price' => $sport->price ?? 1800.00,
            'payment_status' => 'Pending',
            'payment_method' => null,
            'status' => $this->status,
            'notes' => $this->notes ?: '',
            'admin_comments' => '',
            'is_challenge_booking' => false,
            'opponent_team_id' => null,
            'team_id' => null,
        ];

        // Create booking(s)
        if ($this->permanent) {
            $sourceBookingId = null;
            for ($i = 0; $i < 7; $i++) {
                $bookingDate = Carbon::parse($this->selectedDate)->addDays($i)->format('Y-m-d');
                $newBooking = BookingBooking::create(array_merge($bookingData, [
                    'booking_date' => $bookingDate,
                    'permanent_source_id' => $sourceBookingId,
                    'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
                ]));
                if ($i === 0) {
                    $sourceBookingId = $newBooking->id;
                }
            }
        } else {
            BookingBooking::create(array_merge($bookingData, [
                'booking_date' => $this->selectedDate,
                'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
            ]));
        }

        // Write notification
        try {
            \App\Models\BookingNotification::create([
                'user_id' => auth()->id(),
                'type' => 'booking_created',
                'title' => 'New Booking Created',
                'message' => "Booking for {$this->selectedGame} on {$this->selectedDate} at {$this->selectedTime} by {$this->playerName}",
                'data' => ['game' => $this->selectedGame, 'date' => $this->selectedDate],
                'is_read' => false,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to write notification: ' . $e->getMessage());
        }

        // Refresh data and close modal
        $this->loadSports();
        $this->resetFields();

        // Dispatch events (don't let broadcast errors affect success)
        try {
            $this->dispatch('bookingCreated');
            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            Log::warning('Broadcast event failed', ['error' => $e->getMessage()]);
        }

        session()->flash('message', 'Booking created successfully!');
    }

    public function resetFields()
    {
        $this->reset([
            'selectedGame',
            'selectedDate',
            'selectedTime',
            'selectedCourt',
            'playerName',
            'phoneNumber',
            'status',
            'permanent',
            'notes',
        ]);
        $this->status = 'Confirmed';
        $this->resetErrorBag();
    }

    /**
     * Cancel a booking - slot becomes available again
     */
    public function cancelBooking($bookingId = null)
    {
        $booking = $bookingId ? BookingBooking::find($bookingId) : null;

        if (!$booking && $this->selectedGame && $this->selectedDate && $this->selectedCourt && $this->selectedTime) {
            $booking = BookingBooking::where('complex_id_id', $this->complex_id)
                ->where('game_name', $this->selectedGame)
                ->where('booking_date', $this->selectedDate)
                ->where('court_number', $this->selectedCourt)
                ->where('start_time', $this->selectedTime)
                ->first();
        }

        if ($booking) {
            $booking->status = 'Cancelled';
            $booking->save();

            // Notify waitlist on cancellation
            try {
                $waitlisted = BookingWaitlist::where('sport_id', $booking->game_id_id)
                    ->where('booking_date', \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d'))
                    ->where('time_slot', $booking->start_time)
                    ->where('status', 'waiting')
                    ->first();
                if ($waitlisted) {
                    $waitlisted->update(['status' => 'notified', 'notified_at' => now()]);
                }
            } catch (\Exception $e) {}

            try {
                \App\Models\BookingNotification::create([
                    'user_id' => auth()->id(),
                    'type' => 'booking_cancelled',
                    'title' => 'Booking Cancelled',
                    'message' => "Booking #{$booking->id} for {$booking->game_name} on {$booking->booking_date} was cancelled",
                    'data' => ['booking_id' => $booking->id],
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) { }

            $this->dispatch('bookingCancelled');
            $this->loadSports();
            session()->flash('message', 'Booking cancelled. Slot is now available.');
            return true;
        }

        session()->flash('error', 'Booking not found!');
        return false;
    }

    /**
     * Start a booking - changes status to Playing
     */
    public function startBooking($bookingId)
    {
        $booking = BookingBooking::find($bookingId);

        if ($booking && in_array($booking->status, ['Confirmed', 'Pending'])) {
            $booking->status = 'Playing';
            $booking->save();

            $this->loadSports();
            return true;
        }

        return false;
    }

    /**
     * Complete a booking - changes status to Completed
     */
    public function completeBooking($bookingId)
    {
        $booking = BookingBooking::find($bookingId);

        if ($booking && in_array($booking->status, ['Playing', 'confirmed', 'Confirmed'])) {
            $booking->status = 'played';
            $booking->save();

            $this->refreshBookings();
            return true;
        }

        return false;
    }

    public function markAsPlayed($bookingId)
    {
        $booking = BookingBooking::find($bookingId);
        if (!$booking) return false;

        $booking->status = 'played';
        $booking->save();

        $this->refreshBookings();
        return true;
    }

    /**
     * Alias for checkAndUpdateBookingStatuses (for backward compatibility)
     */
    public function checkNoShows()
    {
        return $this->checkAndUpdateBookingStatuses();
    }

    /**
     * Check and update booking statuses based on current time
     * - Past confirmed/pending bookings become "No-Show"
     * - Playing bookings past end time become "Completed"
     */
    public function checkAndUpdateBookingStatuses()
    {
        $now = Carbon::now();

        // Find confirmed/pending bookings where end time has passed -> No-Show
        $expiredBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->whereIn('status', ['Confirmed', 'Pending'])
            ->whereDate('booking_date', '<=', $now->toDateString())
            ->get()
            ->filter(function ($booking) use ($now) {
                try {
                    $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
                    $endTimeStr = $booking->end_time;
                    if (strpos($endTimeStr, ' ') !== false) {
                        $endTimeStr = Carbon::parse($endTimeStr)->format('H:i:s');
                    }
                    $bookingEnd = Carbon::parse($bookingDate . ' ' . $endTimeStr);
                    return $bookingEnd->lessThan($now);
                } catch (\Exception $e) {
                    return false;
                }
            });

        foreach ($expiredBookings as $booking) {
            $booking->status = 'No-Show';
            $booking->save();
        }

        // Find playing bookings where end time has passed -> Completed
        $playingBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('status', 'Playing')
            ->whereDate('booking_date', '<=', $now->toDateString())
            ->get()
            ->filter(function ($booking) use ($now) {
                try {
                    $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
                    $endTimeStr = $booking->end_time;
                    if (strpos($endTimeStr, ' ') !== false) {
                        $endTimeStr = Carbon::parse($endTimeStr)->format('H:i:s');
                    }
                    $bookingEnd = Carbon::parse($bookingDate . ' ' . $endTimeStr);
                    return $bookingEnd->lessThan($now);
                } catch (\Exception $e) {
                    return false;
                }
            });

        foreach ($playingBookings as $booking) {
            $booking->status = 'Completed';
            $booking->save();
        }
    }

    /**
     * Handle real-time booking creation via WebSocket
     * Called when a new booking is created from mobile app or staff dashboard
     */
    #[On('echo:bookings.complex.{complex_id},booking.created')]
    public function handleNewBooking($data)
    {
        // Reload all bookings to show the new one
        $this->loadSports();
        $this->updateChangeTracking();
        
        // Send notification to user
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'New Booking Created',
            'message' => "{$data['user_name']} booked {$data['game_name']} on {$data['start_time']}",
        ]);
    }

    /**
     * Handle real-time booking updates via WebSocket
     * Called when a booking status, payment, or other details change
     */
    #[On('echo:bookings.complex.{complex_id},booking.updated')]
    public function handleUpdatedBooking($data)
    {
        Log::info('Real-time booking update received', [
            'booking_id' => $data['id'],
            'status' => $data['status'],
        ]);

        // Reload bookings to reflect the update
        $this->loadSports();
        $this->updateChangeTracking();
        
        // Send notification to user
        $this->dispatch('notify', [
            'type' => 'info',
            'title' => 'Booking Updated',
            'message' => "Booking #{$data['id']} status changed to {$data['status']}",
        ]);
    }

    /**
     * Handle real-time booking deletion via WebSocket
     * Called when a booking is cancelled or deleted
     */
    #[On('echo:bookings.complex.{complex_id},booking.deleted')]
    public function handleDeletedBooking($data)
    {
        Log::info('Real-time booking deletion received', [
            'booking_id' => $data['id'],
        ]);

        // Reload bookings to remove the deleted one
        $this->loadSports();
        $this->updateChangeTracking();
        
        // Send notification to user
        $this->dispatch('notify', [
            'type' => 'warning',
            'title' => 'Booking Deleted',
            'message' => "Booking #{$data['id']} has been deleted",
        ]);
    }

    // =========================================================
    // Feature #9: Slot Blocking
    // =========================================================

    /**
     * Get blocked slots for a sport on a given date.
     * blocked_slots format: { "2026-04-08": { "09:00:00": { "court_1": "Maintenance" } } }
     */
    public function getBlockedSlots($sportId, $date)
    {
        $sport = BookingSport::find($sportId);
        if (!$sport || !$sport->blocked_slots) return [];
        $slots = is_array($sport->blocked_slots) ? $sport->blocked_slots : json_decode($sport->blocked_slots, true);
        return $slots[$date] ?? [];
    }

    public function openBlockModal($sportId, $date, $time, $court)
    {
        $this->blockingSlot = compact('sportId', 'date', 'time', 'court');
        $this->blockReason = 'Maintenance';
        $this->showBlockModal = true;
    }

    public function closeBlockModal()
    {
        $this->showBlockModal = false;
        $this->blockingSlot = null;
        $this->blockReason = 'Maintenance';
    }

    public function blockSlot()
    {
        if (!$this->blockingSlot) return;

        $sport = BookingSport::find($this->blockingSlot['sportId']);
        if (!$sport) return;

        $slots = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
        $date  = $this->blockingSlot['date'];
        $time  = $this->blockingSlot['time'];
        $court = $this->blockingSlot['court'];

        $slots[$date][$time][$court] = $this->blockReason;
        $sport->update(['blocked_slots' => $slots]);

        $this->closeBlockModal();
        $this->loadSports();
        $this->dispatch('refreshBlockedSlots');
        session()->flash('message', 'Slot blocked successfully.');
    }

    public function unblockSlot($sportId, $date, $time, $court)
    {
        $sport = BookingSport::find($sportId);
        if (!$sport) return;

        $slots = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
        unset($slots[$date][$time][$court]);
        if (empty($slots[$date][$time])) unset($slots[$date][$time]);
        if (empty($slots[$date]))        unset($slots[$date]);

        $sport->update(['blocked_slots' => $slots]);
        $this->loadSports();
        $this->dispatch('refreshBlockedSlots');
        session()->flash('message', 'Slot unblocked.');
    }

    /**
     * Return current blocked slots map for all sports — used by JS after block/unblock.
     */
    public function getBlockedSlotsData(): array
    {
        return ($this->sports ?? collect())->mapWithKeys(function ($sport) {
            $bs = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
            return [$sport->id => $bs];
        })->toArray();
    }

    // =========================================================
    // Feature #12: Waitlist / Queue System
    // =========================================================

    public function openWaitlistModal($sportId, $date, $time, $court)
    {
        $this->waitlistSportId = $sportId;
        $this->waitlistDate    = $date;
        $this->waitlistTime    = $time;
        $this->waitlistCourt   = $court;
        $this->waitlistEntries = BookingWaitlist::where('sport_id', $sportId)
            ->where('booking_date', $date)
            ->where('time_slot', $time)
            ->orderBy('created_at')
            ->get()
            ->toArray();
        $this->showWaitlistModal = true;
    }

    public function closeWaitlistModal()
    {
        $this->showWaitlistModal = false;
        $this->waitlistName  = '';
        $this->waitlistPhone = '';
    }

    public function addToWaitlist()
    {
        $this->validate([
            'waitlistName'  => 'required|string|max:255',
            'waitlistPhone' => 'required|string|max:30',
        ]);

        BookingWaitlist::create([
            'sport_id'      => $this->waitlistSportId,
            'booking_date'  => $this->waitlistDate,
            'time_slot'     => $this->waitlistTime,
            'court_number'  => $this->waitlistCourt,
            'customer_name' => $this->waitlistName,
            'customer_phone'=> $this->waitlistPhone,
            'status'        => 'waiting',
        ]);

        $this->waitlistName  = '';
        $this->waitlistPhone = '';
        session()->flash('message', 'Added to waitlist successfully.');
        $this->openWaitlistModal(
            $this->waitlistSportId,
            $this->waitlistDate,
            $this->waitlistTime,
            $this->waitlistCourt
        );
    }

    public function notifyWaitlistNext($waitlistId)
    {
        $entry = BookingWaitlist::find($waitlistId);
        if ($entry) {
            $entry->update(['status' => 'notified', 'notified_at' => now()]);
            session()->flash('message', "Notified {$entry->customer_name} ({$entry->customer_phone}) — slot is available.");
            $this->openWaitlistModal(
                $this->waitlistSportId,
                $this->waitlistDate,
                $this->waitlistTime,
                $this->waitlistCourt
            );
        }
    }

    public function removeFromWaitlist($waitlistId)
    {
        BookingWaitlist::find($waitlistId)?->delete();
        session()->flash('message', 'Removed from waitlist.');
        $this->openWaitlistModal(
            $this->waitlistSportId,
            $this->waitlistDate,
            $this->waitlistTime,
            $this->waitlistCourt
        );
    }

    public function render()
    {
        return view('livewire.staff.bookings-management', [
            'games'          => $this->games ?? [],
            'bookingdetails' => $this->bookingdetails ?? [],
            'complex_id'     => $this->complex_id,
            'opening_hours'  => $this->opening_hours ?? [],
            'sports'         => $this->sports ?? collect(),
        ]);
    }
}

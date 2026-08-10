<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Carbon\Carbon;
use App\Models\BookingBooking;
use App\Models\BookingPermanentbooking;
use App\Models\BookingSport;
use App\Models\BookingWaitlist;
use App\Models\UserUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Storage;

#[Title("Staff Dashboard")]
#[Layout("components.layouts.staff")]
class BookingsManagement extends Component
{
    protected $sports;
    protected $bookings;
    public $complex_id;
    public $games;
    public $bookingdetails = [];
    public $opening_hours = [];
    public $hasPool = false;

    public $selectedGame = '';
    public $selectedDate = '';
    public $selectedTime = '';
    public $selectedEndTime = '';
    public $endTimeOptions = [];
    public $selectedCourt = '';
    public $playerName = '';
    public $phoneNumber = '';
    public $num_persons = 1;
    public $status = 'Confirmed';
    public $permanent = false;
    public $is_private = false;
    public $notes = '';

    // Multi-slot Cancellation Modal
    public $showCancelModal = false;
    public $cancelType = 'choose'; // 'choose' or 'specific'
    public $cancelBookingId = null;
    public $cancelContinuousSlots = [];
    public $selectedCancelSlotIds = [];
    public $selectAllCancelSlots = true;

    // Permanent Booking Preview Confirmation
    public $showPermanentConfirmModal = false;
    public $permanentAvailableDates = [];
    public $permanentIgnoredDates = [];
    public $pendingPermanentBookingData = [];
    public $pendingPermanentSportId = null;
    public $permanentDayName = '';

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

    // Payment collection for completed bookings
    public $showPaymentCollectModal = false;
    public $paymentCollectBookingId = null;
    public $paymentCollectMethod = 'cash';
    public $paymentCollectBooking = null;

    /**
     * Trigger an SMS via the configured provider
     */
    protected function triggerSms($to, $message)
    {
        $enabled = env('PHONE_OTP_PROVIDER_ENABLED');
        if ($enabled !== 'True' && $enabled !== true) {
            Log::info("SMS sending disabled. Would have sent to {$to}: {$message}");
            return false;
        }

        try {
            $response = Http::withOptions(['verify' => false])
                ->timeout(env('PHONE_OTP_PROVIDER_TIMEOUT_SECONDS', 5))
                ->post(env('PHONE_OTP_PROVIDER_SEND_URL'), [
                    'user_id' => env('PHONE_OTP_PROVIDER_USER_ID'),
                    'api_key' => env('PHONE_OTP_PROVIDER_API_KEY'),
                    'sender_id' => env('PHONE_OTP_PROVIDER_SENDER_ID'),
                    'contact' => $to,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info("SMS sent to {$to}: {$message}");
                return true;
            } else {
                Log::error("SMS sending failed to {$to}: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("SMS sending exception for {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper to send waitlist notification SMS
     */
    protected function sendWaitlistSms($entry, $sportId, $date, $time, $court)
    {
        $sport = BookingSport::find($sportId);
        $sportName = $sport ? $sport->name : 'Sport';

        $venue = \App\Models\BookingVenue::find($this->complex_id);
        $venueName = $venue ? $venue->name : 'our venue';
        
        // Format date and time for better readability
        $formattedDate = Carbon::parse($date)->format('M d, Y');
        $formattedTime = Carbon::parse($time)->format('h:i A');
        
        $message = "Slot Available at {$venueName}: {$sportName} on {$formattedDate} at {$formattedTime} (Court {$court}) is now available. Book now at Sportynix!";
        
        return $this->triggerSms($entry->customer_phone, $message);
    }

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
        'echo:bookings.complex.{complex_id},.booking.created' => 'handleNewBooking',
        'echo:bookings.complex.{complex_id},.booking.updated' => 'handleUpdatedBooking',
        'echo:bookings.complex.{complex_id},.booking.deleted' => 'handleDeletedBooking',
    ];

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        $this->checkAndUpdateBookingStatuses();
        $this->loadSports();
    }

    #[On('refreshBookings')]
    public function refreshBookings()
    {
        $this->loadSports();
    }

    public function loadSports()
    {
        try {
            $this->sports = BookingSport::where('venue_id', $this->complex_id)
                ->where('status', 'Active')
                ->get();

            $this->hasPool = \App\Models\PoolsPool::where('venue_id', $this->complex_id)->exists();

            // Load and normalize opening hours from venue
            $venue = \App\Models\BookingVenue::find($this->complex_id);
            $dbHours = [];
            if ($venue) {
                $dbHours = is_string($venue->opening_hours) ? json_decode($venue->opening_hours, true) : ($venue->opening_hours ?? []);
            }
            $this->opening_hours = $this->parseOpeningHoursFromDb($dbHours);

            // Only load non-cancelled bookings for display
            $this->bookings = BookingBooking::where('complex_id_id', $this->complex_id)
                ->whereRaw("LOWER(status) != 'cancelled'")
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

                if ($endTime->lte($startTime)) {
                    $endTime->addDay();
                }

                if (!empty($booking->duration) && $booking->duration > 0) {
                    $hours = $booking->duration >= 60 ? (int) round($booking->duration / 60) : (int) $booking->duration;
                } else {
                    $hours = max(1, (int) $startTime->diffInHours($endTime));
                }

                // Extract num_persons from admin_comments if available
                $numPersons = 1;
                if (!empty($booking->admin_comments) && str_contains($booking->admin_comments, '{')) {
                    $json = json_decode($booking->admin_comments, true);
                    if (isset($json['num_persons'])) {
                        $numPersons = max(1, (int) $json['num_persons']);
                    }
                }

                for ($i = 0; $i < $hours; $i++) {
                    $slotKey = $startTime->copy()->addHours($i)->format('H:i:s');
                    $slotEnd = $startTime->copy()->addHours($i + 1)->format('H:i:s');

                    // Get user's profile picture or use default
                    $user = $booking->user;
                    $avatarUrl = '/storage/staff/user.png';
                    if ($user && $user->profile_picture) {
                        $avatarUrl = Storage::url($user->profile_picture);
                    }

                    $isCancelled = strtolower($booking->status ?? '') === 'cancelled';
                    $personCountForSlot = $isCancelled ? 0 : $numPersons;

                    if (!isset($this->bookingdetails[$game][$date][$court][$slotKey])) {
                        $this->bookingdetails[$game][$date][$court][$slotKey] = [
                            'player' => $booking->user_name ?? 'Unknown',
                            'phone' => $booking->user_number ?? 'N/A',
                            'status' => $booking->status ?? 'Pending',
                            'permanent_source_id' => $booking->permanent_source_id,
                            'is_private' => (bool) $booking->is_private,
                            'end' => $slotEnd,
                            'avatar' => $avatarUrl,
                            'id' => $booking->id,
                            'admin_comments' => $booking->admin_comments,
                            'num_persons' => $numPersons,
                            'total_persons' => $personCountForSlot,
                            'bookings_list' => [
                                [
                                    'id' => $booking->id,
                                    'player' => $booking->user_name ?? 'Unknown',
                                    'phone' => $booking->user_number ?? 'N/A',
                                    'num_persons' => $numPersons,
                                    'status' => $booking->status ?? 'Pending',
                                    'is_private' => (bool) $booking->is_private,
                                    'avatar' => $avatarUrl,
                                ]
                            ]
                        ];
                    } else {
                        if (!$isCancelled) {
                            $this->bookingdetails[$game][$date][$court][$slotKey]['total_persons'] += $personCountForSlot;
                        }
                        $this->bookingdetails[$game][$date][$court][$slotKey]['bookings_list'][] = [
                            'id' => $booking->id,
                            'player' => $booking->user_name ?? 'Unknown',
                            'phone' => $booking->user_number ?? 'N/A',
                            'num_persons' => $numPersons,
                            'status' => $booking->status ?? 'Pending',
                            'is_private' => (bool) $booking->is_private,
                            'avatar' => $avatarUrl,
                        ];
                    }
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

        $this->buildEndTimeOptions();
    }

    public function buildEndTimeOptions()
    {
        $this->endTimeOptions = [];
        if (!$this->selectedTime || !$this->selectedDate) {
            return;
        }

        $startTimeStr = strlen($this->selectedTime) === 5 ? $this->selectedTime . ':00' : $this->selectedTime;
        $startHour = (int) Carbon::parse($startTimeStr)->format('H');

        // Determine closing hour for selected date from opening_hours
        $dayName = strtolower(Carbon::parse($this->selectedDate)->format('l'));
        $dayHours = $this->opening_hours[$dayName] ?? null;

        $closeHour = 24; // Default closing at midnight
        if ($dayHours && !empty($dayHours['close']) && empty($dayHours['closed'])) {
            $closeH = (int) explode(':', $dayHours['close'])[0];
            $closeM = (int) (explode(':', $dayHours['close'])[1] ?? 0);
            if ($closeH === 0) {
                $closeHour = 24;
            } else {
                $closeHour = $closeM > 0 ? $closeH + 1 : $closeH;
            }
        }

        if ($closeHour <= $startHour) {
            $closeHour = 24;
        }

        $options = [];
        for ($h = $startHour + 1; $h <= $closeHour; $h++) {
            $val = sprintf('%02d:00:00', $h % 24);
            $durationHours = $h - $startHour;

            $hour12 = $h % 12 ?: 12;
            $ampm = ($h % 24) < 12 ? 'AM' : 'PM';
            if ($h === 24) {
                $label = "12:00 AM (Closing · {$durationHours} " . ($durationHours === 1 ? 'hr' : 'hrs') . ")";
            } else {
                $label = "{$hour12}:00 {$ampm} ({$durationHours} " . ($durationHours === 1 ? 'hr' : 'hrs') . ")";
            }

            $options[] = [
                'value' => $val,
                'label' => $label,
            ];
        }

        $this->endTimeOptions = $options;
        if (!empty($options)) {
            $this->selectedEndTime = $options[0]['value'];
        }
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

        // Normalize start and end times
        $startTime = $this->selectedTime;
        if (strlen($startTime) === 5) {
            $startTime .= ':00';
        }
        $startHour = (int) Carbon::parse($startTime)->format('H');

        $endTime = $this->selectedEndTime;
        if (!$endTime) {
            $endTime = Carbon::parse($startTime)->addMinutes(60)->format('H:i:s');
        }
        if (strlen($endTime) === 5) {
            $endTime .= ':00';
        }
        $endHour = (int) Carbon::parse($endTime)->format('H');
        if ($endHour === 0 || $endTime === '00:00:00' || $endHour <= $startHour) {
            $endHour = 24;
        }

        $numPersonsCount = max(1, (int) ($this->num_persons ?? 1));
        $adminCommentsPayload = json_encode([
            'web_book' => true,
            'num_persons' => $numPersonsCount,
        ]);

        $unitPrice = (float) ($sport->price ?? 1800.00);
        if ($this->is_private) {
            $charges = is_array($sport->additional_charges) ? $sport->additional_charges : (json_decode($sport->additional_charges, true) ?? []);
            if (!empty($sport->private_booking_price)) {
                $unitPrice = (float) $sport->private_booking_price;
            } elseif (!empty($charges['private_booking_price'])) {
                $unitPrice = (float) $charges['private_booking_price'];
            } elseif (!empty($charges['private_price'])) {
                $unitPrice = (float) $charges['private_price'];
            }
        }
        $totalBookingPrice = $unitPrice * $numPersonsCount;

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
            'price' => $totalBookingPrice,
            'advance_amount' => 0,
            'amount_paid' => 0,
            'balance_due' => $totalBookingPrice,
            'financial_status' => 'Pending',
            'is_initial_permanent_occurrence' => false,
            'offline_paid_amount' => 0,
            'online_paid_amount' => 0,
            'points_discount_amount' => 0,
            'requires_advance_payment' => false,
            'reward_status' => 'not_eligible',
            'payment_status' => 'Pending',
            'payment_method' => null,
            'status' => $this->status,
            'notes' => $this->notes ?: '',
            'admin_comments' => $adminCommentsPayload,
            'is_challenge_booking' => false,
            'is_private' => (bool) $this->is_private,
            'opponent_team_id' => null,
            'team_id' => null,
        ];

        // Determine max capacity for selected game
        $charges = is_array($sport->additional_charges) ? $sport->additional_charges : (json_decode($sport->additional_charges, true) ?? []);
        $gameKeyLower = strtolower($this->selectedGame);
        $maxCapForGame = (int) ($charges['max_persons_per_hour'] ?? ($gameKeyLower === 'pools' || $gameKeyLower === 'pool' ? 10 : 1));

        // Create booking(s)
        if ($this->permanent) {
            $startDate = Carbon::parse($this->selectedDate);
            $endDate   = $startDate->copy()->addDays(30);
            $current   = $startDate->copy();
            
            $availableDates = [];
            $ignoredDates   = [];

            $blockedSlotsData = ($sport && $sport->blocked_slots)
                ? (is_array($sport->blocked_slots) ? $sport->blocked_slots : json_decode($sport->blocked_slots, true))
                : [];

            while ($current->lte($endDate)) {
                $dateStr = $current->format('Y-m-d');
                $formattedDate = $current->format('D, M j, Y');

                // Check existing non-cancelled bookings
                $existingBookings = BookingBooking::where('complex_id_id', $this->complex_id)
                    ->whereRaw('LOWER(game_name) = ?', [strtolower($this->selectedGame)])
                    ->where('court_number', $this->selectedCourt)
                    ->where('booking_date', $dateStr)
                    ->where('start_time', $startTime)
                    ->whereRaw('LOWER(status) != ?', ['cancelled'])
                    ->get();

                $alreadyBookedPersons = 0;
                foreach ($existingBookings as $eb) {
                    $pCount = 1;
                    if (!empty($eb->admin_comments) && str_contains($eb->admin_comments, '{')) {
                        $jPayload = json_decode($eb->admin_comments, true);
                        if (isset($jPayload['num_persons'])) {
                            $pCount = max(1, (int) $jPayload['num_persons']);
                        }
                    }
                    $alreadyBookedPersons += $pCount;
                }

                $isCapacityExceeded = ($maxCapForGame > 1)
                    ? (($alreadyBookedPersons + $numPersonsCount) > $maxCapForGame)
                    : ($existingBookings->count() > 0);

                // Check blocked slots
                $isBlocked = false;
                $courtKey = 'court_' . strtolower(str_replace(['court ', 'court'], '', trim(strtolower($this->selectedCourt))));
                if (isset($blockedSlotsData[$dateStr][$startTime])) {
                    $timeBlocks = $blockedSlotsData[$dateStr][$startTime];
                    if (is_array($timeBlocks) && (isset($timeBlocks[$courtKey]) || isset($timeBlocks[$this->selectedCourt]))) {
                        $isBlocked = true;
                    }
                }

                if ($isCapacityExceeded || $isBlocked) {
                    $reason = $isBlocked ? 'Slot Blocked' : ($maxCapForGame > 1 ? "Capacity Full ({$alreadyBookedPersons}/{$maxCapForGame} Booked)" : 'Already Booked');
                    if ($existingBookings->count() > 0 && $existingBookings->first()->user_name && $maxCapForGame <= 1) {
                        $reason .= " ({$existingBookings->first()->user_name})";
                    }
                    $ignoredDates[] = [
                        'date' => $dateStr,
                        'formatted' => $formattedDate,
                        'reason' => $reason
                    ];
                } else {
                    $availableDates[] = [
                        'date' => $dateStr,
                        'formatted' => $formattedDate
                    ];
                }

                $current->addWeek();
            }

            $this->permanentAvailableDates     = $availableDates;
            $this->permanentIgnoredDates       = $ignoredDates;
            $this->pendingPermanentBookingData = $bookingData;
            $this->pendingPermanentSportId     = $sport->id;
            $this->permanentDayName            = $startDate->format('l');
            $this->showPermanentConfirmModal   = true;
            return;
        } else {
            // Multi-hour range booking with collision skipping
            $blockedSlotsData = ($sport && $sport->blocked_slots)
                ? (is_array($sport->blocked_slots) ? $sport->blocked_slots : json_decode($sport->blocked_slots, true))
                : [];
            $dayOfWeek = strtolower(Carbon::parse($this->selectedDate)->format('l'));

            $bookedCount = 0;
            $skippedSlots = [];

            for ($h = $startHour; $h < $endHour; $h++) {
                $slotStartStr = sprintf('%02d:00:00', $h);
                $slotEndStr   = sprintf('%02d:00:00', ($h + 1) % 24);
                $slotStartDisplay = Carbon::parse($slotStartStr)->format('g:i A');
                $slotEndDisplay   = Carbon::parse($slotEndStr)->format('g:i A');
                $slotLabel        = "{$slotStartDisplay} – {$slotEndDisplay}";

                // Check existing non-cancelled bookings for this slot
                $existingBookings = BookingBooking::where('complex_id_id', $this->complex_id)
                    ->whereRaw('LOWER(game_name) = ?', [strtolower($this->selectedGame)])
                    ->where('court_number', $this->selectedCourt)
                    ->where('booking_date', $this->selectedDate)
                    ->where('start_time', $slotStartStr)
                    ->whereRaw('LOWER(status) != ?', ['cancelled'])
                    ->get();

                $alreadyBookedPersons = 0;
                foreach ($existingBookings as $eb) {
                    $pCount = 1;
                    if (!empty($eb->admin_comments) && str_contains($eb->admin_comments, '{')) {
                        $jPayload = json_decode($eb->admin_comments, true);
                        if (isset($jPayload['num_persons'])) {
                            $pCount = max(1, (int) $jPayload['num_persons']);
                        }
                    }
                    $alreadyBookedPersons += $pCount;
                }

                $isCapacityExceeded = ($maxCapForGame > 1)
                    ? (($alreadyBookedPersons + $numPersonsCount) > $maxCapForGame)
                    : ($existingBookings->count() > 0);

                // 2. Check blocked slots
                $isBlocked = false;
                $courtKey = 'court_' . strtolower(str_replace(['court ', 'court'], '', trim(strtolower($this->selectedCourt))));
                if (isset($blockedSlotsData[$this->selectedDate][$slotStartStr])) {
                    $timeBlocks = $blockedSlotsData[$this->selectedDate][$slotStartStr];
                    if (is_array($timeBlocks) && (isset($timeBlocks[$courtKey]) || isset($timeBlocks[$this->selectedCourt]))) {
                        $isBlocked = true;
                    }
                }
                if (isset($blockedSlotsData[$dayOfWeek]) && is_array($blockedSlotsData[$dayOfWeek]) && in_array($slotStartStr, $blockedSlotsData[$dayOfWeek])) {
                    $isBlocked = true;
                }

                if ($isCapacityExceeded || $isBlocked) {
                    if ($isBlocked) {
                        $reason = 'Blocked';
                    } elseif ($maxCapForGame > 1) {
                        $reason = "Capacity Full ({$alreadyBookedPersons}/{$maxCapForGame} Booked)";
                    } else {
                        $firstUser = $existingBookings->first()?->user_name;
                        $reason = 'Booked' . ($firstUser ? " by {$firstUser}" : '');
                    }
                    $skippedSlots[] = "{$slotLabel} ({$reason})";
                    continue;
                }

                BookingBooking::create(array_merge($bookingData, [
                    'start_time' => $slotStartStr,
                    'end_time'   => $slotEndStr,
                    'booking_date' => $this->selectedDate,
                    'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
                ]));
                $bookedCount++;
            }

            if ($bookedCount === 0) {
                $this->addError('general', 'No available slots were booked. Skipped: ' . implode(', ', $skippedSlots));
                return;
            }

            $successMsg = "{$bookedCount} slot(s) booked successfully for {$this->playerName}!";
            if (!empty($skippedSlots)) {
                $successMsg .= " Skipped " . count($skippedSlots) . " slot(s) already booked/blocked: " . implode(', ', $skippedSlots) . ".";
            }
            session()->flash('message', $successMsg);
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

    public function confirmPermanentBooking()
    {
        if (empty($this->pendingPermanentBookingData) || !$this->pendingPermanentSportId || empty($this->permanentAvailableDates)) {
            $this->closePermanentConfirmModal();
            return;
        }

        $sport = BookingSport::find($this->pendingPermanentSportId);
        $staffUser = Auth::user();
        $userUser = UserUser::where('phone_number', $this->phoneNumber)->first();

        $finalUserId = $userUser?->id;
        if (!$finalUserId && $staffUser) {
            $finalUserId = UserUser::where('email', $staffUser->email)->first()?->id;
        }
        if (!$finalUserId) {
            $finalUserId = UserUser::orderBy('id', 'asc')->first()?->id;
        }

        $startTime = Carbon::parse($this->selectedTime)->format('H:i:s');
        $endTime   = Carbon::parse($this->selectedTime)->addHour()->format('H:i:s');

        // Create permanent source record
        $permanentSource = BookingPermanentbooking::create([
            'user_id' => $finalUserId,
            'sport_id' => $sport ? $sport->id : null,
            'complex_id' => $this->complex_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => 60,
            'price' => $sport->price ?? 1800.00,
            'recurring_config' => [
                'months' => 1,
                'selected_days' => [Carbon::parse($this->selectedDate)->dayOfWeekIso],
            ],
            'is_active' => true,
            'status' => 'Active',
            'created_at' => now(),
        ]);

        $bookedCount = 0;
        foreach ($this->permanentAvailableDates as $item) {
            $bookingDate = $item['date'];
            BookingBooking::create(array_merge($this->pendingPermanentBookingData, [
                'booking_date' => $bookingDate,
                'permanent_source_id' => $permanentSource->id,
                'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
            ]));
            $bookedCount++;
        }

        $ignoredCount = count($this->permanentIgnoredDates);

        // Write notification
        try {
            \App\Models\BookingNotification::create([
                'user_id' => auth()->id(),
                'type' => 'booking_created',
                'title' => 'Permanent Booking Created',
                'message' => "Permanent booking created for {$this->selectedGame} on {$this->permanentDayName}s ({$bookedCount} slots booked, {$ignoredCount} ignored)",
                'data' => ['game' => $this->selectedGame, 'date' => $this->selectedDate],
                'is_read' => false,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to write notification: ' . $e->getMessage());
        }

        $msg = "Permanent booking confirmed! {$bookedCount} slot(s) booked for {$this->permanentDayName}s over the next 30 days.";
        if ($ignoredCount > 0) {
            $msg .= " ({$ignoredCount} slot(s) skipped due to existing bookings/blocks).";
        }

        $this->closePermanentConfirmModal();
        $this->loadSports();
        $this->resetFields();

        try {
            $this->dispatch('bookingCreated');
            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            Log::warning('Broadcast event failed', ['error' => $e->getMessage()]);
        }

        session()->flash('message', $msg);
    }

    public function closePermanentConfirmModal()
    {
        $this->showPermanentConfirmModal = false;
        $this->permanentAvailableDates = [];
        $this->permanentIgnoredDates = [];
        $this->pendingPermanentBookingData = [];
        $this->pendingPermanentSportId = null;
        $this->permanentDayName = '';
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
     * Initiate cancellation flow for a booking.
     * If part of a continuous multi-slot booking, opens choice modal (Pic 2).
     */
    public function initiateCancelBooking($bookingId = null)
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

        if (!$booking) {
            $this->addError('general', 'Booking not found.');
            return false;
        }

        // Find all active non-cancelled slots for this user on this game, date, and court
        $query = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('game_name', $booking->game_name)
            ->where('court_number', $booking->court_number)
            ->where('booking_date', $booking->booking_date)
            ->whereRaw("LOWER(status) != 'cancelled'");

        if (!empty($booking->user_name)) {
            $query->where('user_name', $booking->user_name);
        }

        $allSlots = $query->orderBy('start_time', 'asc')->get();

        if ($allSlots->count() > 1) {
            // Multi-slot continuous booking -> Open Choice Modal (Pic 2)
            $this->cancelBookingId = $booking->id;
            $this->cancelContinuousSlots = $allSlots->toArray();
            $this->selectedCancelSlotIds = $allSlots->pluck('id')->map(fn($id) => (string)$id)->toArray();
            $this->selectAllCancelSlots = true;
            $this->cancelType = 'choose';
            $this->showCancelModal = true;
            return 'modal';
        } else {
            // Single slot -> Cancel directly
            return $this->executeSingleCancel($booking);
        }
    }

    public function setCancelType($type)
    {
        $this->cancelType = $type;
    }

    public function updatedSelectAllCancelSlots($value)
    {
        if ($value && !empty($this->cancelContinuousSlots)) {
            $this->selectedCancelSlotIds = array_map(fn($s) => (string)$s['id'], $this->cancelContinuousSlots);
        } else {
            $this->selectedCancelSlotIds = [];
        }
    }

    public function cancelFullSeries()
    {
        if (empty($this->cancelContinuousSlots)) {
            return;
        }

        $ids = array_column($this->cancelContinuousSlots, 'id');
        $count = 0;
        foreach ($ids as $id) {
            $b = BookingBooking::find($id);
            if ($b && $b->status !== 'Cancelled') {
                $b->status = 'Cancelled';
                $b->save();
                $count++;

                // Notify waitlist on cancellation
                try {
                    $waitlisted = BookingWaitlist::where('sport_id', $b->game_id_id)
                        ->where('booking_date', \Carbon\Carbon::parse($b->booking_date)->format('Y-m-d'))
                        ->where('time_slot', $b->start_time)
                        ->where('status', 'waiting')
                        ->first();
                    if ($waitlisted) {
                        $waitlisted->update(['status' => 'notified', 'notified_at' => now()]);
                        $this->sendWaitlistSms($waitlisted, $b->game_id_id, $b->booking_date, $b->start_time, $b->court_number);
                    }
                } catch (\Exception $e) {}
            }
        }

        $this->closeCancelModal();
        $this->dispatch('bookingCancelled');
        $this->loadSports();
        session()->flash('message', "All {$count} continuous booking slot(s) have been cancelled successfully.");
    }

    public function cancelSelectedSpecificSlots()
    {
        if (empty($this->selectedCancelSlotIds)) {
            $this->addError('general', 'Please select at least one slot to cancel.');
            return;
        }

        $count = 0;
        foreach ($this->selectedCancelSlotIds as $id) {
            $b = BookingBooking::find($id);
            if ($b && $b->status !== 'Cancelled') {
                $b->status = 'Cancelled';
                $b->save();
                $count++;

                // Notify waitlist
                try {
                    $waitlisted = BookingWaitlist::where('sport_id', $b->game_id_id)
                        ->where('booking_date', \Carbon\Carbon::parse($b->booking_date)->format('Y-m-d'))
                        ->where('time_slot', $b->start_time)
                        ->where('status', 'waiting')
                        ->first();
                    if ($waitlisted) {
                        $waitlisted->update(['status' => 'notified', 'notified_at' => now()]);
                        $this->sendWaitlistSms($waitlisted, $b->game_id_id, $b->booking_date, $b->start_time, $b->court_number);
                    }
                } catch (\Exception $e) {}
            }
        }

        $this->closeCancelModal();
        $this->dispatch('bookingCancelled');
        $this->loadSports();
        session()->flash('message', "{$count} selected booking slot(s) have been cancelled successfully.");
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancelBookingId = null;
        $this->cancelType = 'choose';
        $this->cancelContinuousSlots = [];
        $this->selectedCancelSlotIds = [];
        $this->selectAllCancelSlots = true;
    }

    private function executeSingleCancel($booking)
    {
        $booking->status = 'Cancelled';
        $booking->save();

        try {
            $waitlisted = BookingWaitlist::where('sport_id', $booking->game_id_id)
                ->where('booking_date', \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d'))
                ->where('time_slot', $booking->start_time)
                ->where('status', 'waiting')
                ->first();
            if ($waitlisted) {
                $waitlisted->update(['status' => 'notified', 'notified_at' => now()]);
                $this->sendWaitlistSms($waitlisted, $booking->game_id_id, $booking->booking_date, $booking->start_time, $booking->court_number);
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

    public function cancelBooking($bookingId = null)
    {
        return $this->initiateCancelBooking($bookingId);
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
     * Open payment collection modal for a completed booking
     */
    public function openPaymentCollectModal($bookingId)
    {
        $booking = BookingBooking::with('venue')->find($bookingId);
        if (!$booking) return;

        $this->paymentCollectBookingId = $bookingId;
        $this->paymentCollectBooking = $booking;
        $this->paymentCollectMethod = $booking->payment_method ?: 'cash';
        $this->showPaymentCollectModal = true;
    }

    public function closePaymentCollectModal()
    {
        $this->showPaymentCollectModal = false;
        $this->paymentCollectBookingId = null;
        $this->paymentCollectBooking = null;
    }

    public function collectBookingPayment()
    {
        if ($this->paymentCollectBookingId) {
            $booking = BookingBooking::find($this->paymentCollectBookingId);
            if ($booking) {
                $booking->payment_status = 'Paid';
                $booking->payment_method = $this->paymentCollectMethod;
                
                $booking->financial_status = 'FullyPaid';
                $booking->amount_paid = $booking->price;
                $booking->balance_due = 0;
                
                if (strtolower($this->paymentCollectMethod) == 'cash') {
                    $booking->offline_paid_amount = $booking->price;
                } else {
                    $booking->online_paid_amount = $booking->price;
                }
                
                $booking->save();

                session()->flash('success', "Payment of LKR " . number_format($booking->price ?: 0, 2) . " collected successfully.");
                
                // Refresh local model with venue info loaded
                $this->paymentCollectBooking = BookingBooking::with('venue')->find($this->paymentCollectBookingId);
                
                $this->refreshBookings();
            }
        }
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
                    $startTimeStr = $booking->start_time;
                    $endTimeStr = $booking->end_time;
                    if (strpos($endTimeStr, ' ') !== false) {
                        $endTimeStr = Carbon::parse($endTimeStr)->format('H:i:s');
                    }
                    if (strpos($startTimeStr, ' ') !== false) {
                        $startTimeStr = Carbon::parse($startTimeStr)->format('H:i:s');
                    }
                    $bookingStart = Carbon::parse($bookingDate . ' ' . $startTimeStr);
                    $bookingEnd = Carbon::parse($bookingDate . ' ' . $endTimeStr);
                    if ($bookingEnd->lte($bookingStart)) {
                        $bookingEnd->addDay();
                    }
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
                    $startTimeStr = $booking->start_time;
                    $endTimeStr = $booking->end_time;
                    if (strpos($endTimeStr, ' ') !== false) {
                        $endTimeStr = Carbon::parse($endTimeStr)->format('H:i:s');
                    }
                    if (strpos($startTimeStr, ' ') !== false) {
                        $startTimeStr = Carbon::parse($startTimeStr)->format('H:i:s');
                    }
                    $bookingStart = Carbon::parse($bookingDate . ' ' . $startTimeStr);
                    $bookingEnd = Carbon::parse($bookingDate . ' ' . $endTimeStr);
                    if ($bookingEnd->lte($bookingStart)) {
                        $bookingEnd->addDay();
                    }
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
    #[On('echo:bookings.complex.{complex_id},.booking.created')]
    public function handleNewBooking($data)
    {
        // Reload all bookings to show the new one
        $this->loadSports();

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
    #[On('echo:bookings.complex.{complex_id},.booking.updated')]
    public function handleUpdatedBooking($data)
    {
        Log::info('Real-time booking update received', [
            'booking_id' => $data['id'],
            'status' => $data['status'],
        ]);

        // Reload bookings to reflect the update
        $this->loadSports();

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
    #[On('echo:bookings.complex.{complex_id},.booking.deleted')]
    public function handleDeletedBooking($data)
    {
        Log::info('Real-time booking deletion received', [
            'booking_id' => $data['id'],
        ]);

        // Reload bookings to remove the deleted one
        $this->loadSports();

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
        if (isset($slots[$date][$time][$court])) {
            unset($slots[$date][$time][$court]);
            if (empty($slots[$date][$time])) unset($slots[$date][$time]);
            if (empty($slots[$date]))        unset($slots[$date]);
        }

        // Also check and remove recurring day-of-week block if present
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
        if (isset($slots[$dayOfWeek]) && is_array($slots[$dayOfWeek])) {
            $slots[$dayOfWeek] = array_values(array_filter($slots[$dayOfWeek], fn($t) => $t !== $time));
            if (empty($slots[$dayOfWeek])) unset($slots[$dayOfWeek]);
        }

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
            
            // Send SMS notification
            $this->sendWaitlistSms($entry, $this->waitlistSportId, $this->waitlistDate, $this->waitlistTime, $this->waitlistCourt);
            
            session()->flash('message', "Notified {$entry->customer_name} ({$entry->customer_phone}) via SMS — slot is available.");
            $this->openWaitlistModal(
                $this->waitlistSportId,
                $this->waitlistDate,
                $this->waitlistTime,
                $this->waitlistCourt
            );
        }
    }

    public function notifyFirstInWaitlist()
    {
        $firstEntry = BookingWaitlist::where('sport_id', $this->waitlistSportId)
            ->where('booking_date', $this->waitlistDate)
            ->where('time_slot', $this->waitlistTime)
            ->where('court_number', $this->waitlistCourt)
            ->where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($firstEntry) {
            $this->notifyWaitlistNext($firstEntry->id);
        } else {
            session()->flash('error', 'No one waiting in the waitlist for this slot.');
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

    public function redirectToCompletedBookings()
    {
        return redirect()->route('staff.completed-bookings');
    }

    public function render()
    {
        $maxCapacityMap = [];
        if (!empty($this->sports)) {
            foreach ($this->sports as $s) {
                $gameKey = strtolower($s->name);
                $charges = is_array($s->additional_charges) ? $s->additional_charges : (json_decode($s->additional_charges, true) ?? []);
                $maxPersons = $charges['max_persons_per_hour'] ?? ($gameKey === 'pools' || $gameKey === 'pool' ? 10 : 1);
                $maxCapacityMap[$gameKey] = (int) $maxPersons;
            }
        }

        return view('livewire.staff.bookings-management', [
            'games'          => $this->games ?? [],
            'bookingdetails' => $this->bookingdetails ?? [],
            'complex_id'     => $this->complex_id,
            'opening_hours'  => $this->opening_hours ?? [],
            'sports'         => $this->sports ?? \App\Models\BookingSport::query()->whereRaw('1=0')->get(),
            'maxCapacityMap' => $maxCapacityMap,
        ]);
    }
}


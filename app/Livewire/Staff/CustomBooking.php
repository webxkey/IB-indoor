<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\UserUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

#[Title("Custom Bookings")]
#[Layout("components.layouts.staff")]
class CustomBooking extends Component
{
    use WithPagination;

    public $complex_id;
    public $sports = [];
    public $availableCourts = [];
    public $timeOptions = [];
    public $endTimeOptions = [];

    // Search and Table Filters
    public $search = '';
    public $filterDate = '';
    
    // Modal visibility states
    public $showCreateModal = false;
    public $showResultModal = false;
    public $showViewPackageModal = false;

    // Cancellation Modal State
    public $showCancelModal = false;
    public $cancelPackageGroup = null;
    public $cancelType = 'choose'; // 'single', 'choose', 'full', 'day_wise', 'slot_wise'
    public $selectedCancelDates = [];
    public $selectedCancelSlotIds = [];

    // View Package State
    public $viewPackageGroup = null;

    // Customer & Package Form inputs (Top of Modal)
    public $playerName = '';
    public $phoneNumber = '';
    public $status = 'Confirmed';
    public $advance_amount = 0;
    public $notes = '';

    // Slot builder inputs (Middle of Modal)
    public $selectedGame = '';
    public $selectedDate = '';
    public $selectedTime = '';
    public $selectedEndTime = '';
    public $selectedCourt = '';
    public $is_private = false;
    public $num_persons = 1;

    // Staging / Package Draft items inside Modal
    public $draftPackageItems = [];

    // Results & Feedback state
    public $bookedSlots = [];
    public $skippedSlots = [];
    public $bookedCount = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterDate' => ['except' => ''],
    ];

    protected $rules = [
        'playerName' => 'required|string|max:255',
        'phoneNumber' => 'required|string|max:20',
        'status' => 'required|in:Confirmed,Pending,Completed,Cancelled,No-Show,Playing',
        'advance_amount' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id ?? null;
        $this->selectedDate = Carbon::today()->format('Y-m-d');
        $this->loadSports();
        $this->buildTimeOptions();
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

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->selectedDate = Carbon::today()->format('Y-m-d');
        $this->draftPackageItems = [];
        $this->is_private = false;
        if (!empty($this->sports) && empty($this->selectedGame)) {
            $this->selectedGame = $this->sports->first()->name ?? '';
            $this->updatedSelectedGame($this->selectedGame);
        }
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function loadSports()
    {
        if (!$this->complex_id) {
            return;
        }

        $this->sports = BookingSport::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->get();

        if ($this->sports->count() > 0 && empty($this->selectedGame)) {
            $this->selectedGame = $this->sports->first()->name;
            $this->updatedSelectedGame($this->selectedGame);
        }
    }

    public function updatedSelectedGame($value)
    {
        $sport = BookingSport::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($value)])
            ->first();

        $maxCourts = $sport ? ($sport->maximum_court ?? 1) : 1;
        $this->availableCourts = [];
        for ($i = 1; $i <= $maxCourts; $i++) {
            $this->availableCourts[] = "{$i}";
        }

        if (!in_array($this->selectedCourt, $this->availableCourts)) {
            $this->selectedCourt = $this->availableCourts[0] ?? '1';
        }
    }

    public function buildTimeOptions()
    {
        $this->timeOptions = [];
        for ($h = 6; $h <= 23; $h++) {
            $val = sprintf('%02d:00', $h);
            $label = Carbon::parse($val)->format('g:i A');
            $this->timeOptions[] = ['value' => $val, 'label' => $label];
        }

        if (empty($this->selectedTime)) {
            $this->selectedTime = '10:00';
        }

        $this->updatedSelectedTime($this->selectedTime);
    }

    public function updatedSelectedTime($value)
    {
        if (!$value) return;

        $startHour = (int) Carbon::parse($value)->format('H');
        $this->endTimeOptions = [];

        for ($h = $startHour + 1; $h <= 24; $h++) {
            $val = sprintf('%02d:00', $h % 24);
            if ($h === 24) $val = '00:00';
            $durationHours = $h - $startHour;
            $label = Carbon::parse(sprintf('%02d:00', $h % 24))->format('g:i A') . " ({$durationHours} " . ($durationHours === 1 ? 'hr' : 'hrs') . ")";
            if ($h === 24) $label = '12:00 AM (Next Day)' . " ({$durationHours} hrs)";
            $this->endTimeOptions[] = ['value' => $val, 'label' => $label];
        }

        if (empty($this->selectedEndTime) || (int) Carbon::parse($this->selectedEndTime)->format('H') <= $startHour) {
            $this->selectedEndTime = $this->endTimeOptions[0]['value'] ?? sprintf('%02d:00', ($startHour + 1) % 24);
        }
    }

    public function selectTimeSlotFromPill($timeVal)
    {
        $this->selectedTime = $timeVal;
        $this->updatedSelectedTime($timeVal);
    }

    public function getIsPrivateBookingEnabledProperty()
    {
        if (empty($this->selectedGame)) {
            return false;
        }

        $sport = BookingSport::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($this->selectedGame)])
            ->first();

        if (!$sport) {
            return false;
        }

        if (!is_null($sport->private_booking_enabled)) {
            return (bool) $sport->private_booking_enabled;
        }

        return !empty($sport->private_booking_price) || ($sport->private_booking_pricing_mode === 'normal_total' && $sport->private_booking_price_multiplier > 1);
    }

    public function getDaySlotsTimeline()
    {
        if (empty($this->selectedGame) || empty($this->selectedCourt) || empty($this->selectedDate)) {
            return [];
        }

        $sport = BookingSport::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($this->selectedGame)])
            ->first();

        if (!$sport) return [];

        $blockedSlotsData = ($sport && $sport->blocked_slots)
            ? (is_array($sport->blocked_slots) ? $sport->blocked_slots : json_decode($sport->blocked_slots, true))
            : [];
        $charges = is_array($sport->additional_charges) ? $sport->additional_charges : (json_decode($sport->additional_charges, true) ?? []);
        $maxCapForGame = (int) ($charges['max_persons_per_hour'] ?? 1);

        $timeline = [];
        for ($h = 6; $h <= 23; $h++) {
            $slotStartStr = sprintf('%02d:00:00', $h);
            $slotEndStr = sprintf('%02d:00:00', ($h + 1) % 24);
            $startDisplay = Carbon::parse($slotStartStr)->format('g:i A');
            $endDisplay = Carbon::parse($slotEndStr)->format('g:i A');

            $existingBookings = BookingBooking::where('complex_id_id', $this->complex_id)
                ->whereRaw('LOWER(game_name) = ?', [strtolower($this->selectedGame)])
                ->where('court_number', $this->selectedCourt)
                ->where('booking_date', $this->selectedDate)
                ->where('start_time', $slotStartStr)
                ->whereRaw('LOWER(status) != ?', ['cancelled'])
                ->get();

            $courtKey = 'court_' . strtolower(str_replace(['court ', 'court'], '', trim(strtolower($this->selectedCourt))));
            $isBlocked = isset($blockedSlotsData[$this->selectedDate][$slotStartStr][$courtKey]) || isset($blockedSlotsData[$this->selectedDate][$slotStartStr][$this->selectedCourt]);

            $status = 'available';
            $bookedBy = null;

            if ($isBlocked) {
                $status = 'blocked';
            } elseif ($existingBookings->count() >= $maxCapForGame) {
                $status = 'booked';
                $bookedBy = $existingBookings->first()->user_name ?: 'Booked';
            }

            $timeline[] = [
                'hour' => $h,
                'time_val' => sprintf('%02d:00', $h),
                'label' => $startDisplay,
                'slot_range' => "{$startDisplay} - {$endDisplay}",
                'status' => $status,
                'booked_by' => $bookedBy,
            ];
        }

        return $timeline;
    }

    public function getRangeAvailabilityInfo()
    {
        if (empty($this->selectedGame) || empty($this->selectedCourt) || empty($this->selectedDate) || empty($this->selectedTime) || empty($this->selectedEndTime)) {
            return null;
        }

        $timeline = $this->getDaySlotsTimeline();
        $startHour = (int) Carbon::parse($this->selectedTime)->format('H');
        $endHour = (int) Carbon::parse($this->selectedEndTime)->format('H');
        if ($endHour === 0 || $endHour <= $startHour) $endHour = 24;

        $totalRangeSlots = $endHour - $startHour;
        $availCount = 0;
        $blockedCount = 0;
        $bookedCount = 0;
        $conflictDetails = [];

        foreach ($timeline as $slot) {
            if ($slot['hour'] >= $startHour && $slot['hour'] < $endHour) {
                if ($slot['status'] === 'available') {
                    $availCount++;
                } elseif ($slot['status'] === 'blocked') {
                    $blockedCount++;
                    $conflictDetails[] = "{$slot['slot_range']} (Blocked)";
                } else {
                    $bookedCount++;
                    $nameStr = $slot['booked_by'] ? "by {$slot['booked_by']}" : 'Already Booked';
                    $conflictDetails[] = "{$slot['slot_range']} ({$nameStr})";
                }
            }
        }

        return [
            'total_slots' => $totalRangeSlots,
            'avail_count' => $availCount,
            'blocked_count' => $blockedCount,
            'booked_count' => $bookedCount,
            'conflict_details' => $conflictDetails,
            'is_fully_available' => $availCount === $totalRangeSlots,
        ];
    }

    /**
     * Add a draft booking item to the Package Staging Table inside the Modal
     */
    public function addDraftItem()
    {
        $this->validate([
            'selectedGame' => 'required|string',
            'selectedDate' => 'required|date_format:Y-m-d',
            'selectedTime' => 'required',
            'selectedEndTime' => 'required',
            'selectedCourt' => 'required|string',
        ]);

        $sport = BookingSport::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($this->selectedGame)])
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->first();

        if (!$sport) {
            $this->addError('selectedGame', 'Selected sport is not available.');
            return;
        }

        $startHour = (int) Carbon::parse($this->selectedTime)->format('H');
        $endHour = (int) Carbon::parse($this->selectedEndTime)->format('H');
        if ($endHour === 0 || $endHour <= $startHour) $endHour = 24;

        $duration = $endHour - $startHour;

        // Private booking pricing logic: use private booking price from sport table if is_private is enabled
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

        $itemTotalPrice = $unitPrice * max(1, (int) $this->num_persons) * $duration;

        $dateCarbon = Carbon::parse($this->selectedDate);
        $formattedDate = $dateCarbon->format('Y-m-d') . ' (' . $dateCarbon->format('l') . ')';

        $startDisplay = Carbon::parse($this->selectedTime)->format('g:i A');
        $endDisplay = Carbon::parse($this->selectedEndTime)->format('g:i A');
        $formattedTime = "{$startDisplay} - {$endDisplay} ({$duration} " . ($duration === 1 ? 'hr' : 'hrs') . ")";

        // Pre-check availability for this draft slot item
        $blockedSlotsData = ($sport && $sport->blocked_slots)
            ? (is_array($sport->blocked_slots) ? $sport->blocked_slots : json_decode($sport->blocked_slots, true))
            : [];
        $charges = is_array($sport->additional_charges) ? $sport->additional_charges : (json_decode($sport->additional_charges, true) ?? []);
        $maxCapForGame = (int) ($charges['max_persons_per_hour'] ?? 1);

        $availCount = 0;
        $blockedCount = 0;

        for ($h = $startHour; $h < $endHour; $h++) {
            $slotStartStr = sprintf('%02d:00:00', $h);
            $existingCount = BookingBooking::where('complex_id_id', $this->complex_id)
                ->whereRaw('LOWER(game_name) = ?', [strtolower($this->selectedGame)])
                ->where('court_number', $this->selectedCourt)
                ->where('booking_date', $this->selectedDate)
                ->where('start_time', $slotStartStr)
                ->whereRaw('LOWER(status) != ?', ['cancelled'])
                ->count();

            $courtKey = 'court_' . strtolower(str_replace(['court ', 'court'], '', trim(strtolower($this->selectedCourt))));
            $isBlocked = isset($blockedSlotsData[$this->selectedDate][$slotStartStr][$courtKey]) || isset($blockedSlotsData[$this->selectedDate][$slotStartStr][$this->selectedCourt]);

            if ($existingCount >= $maxCapForGame || $isBlocked) {
                $blockedCount++;
            } else {
                $availCount++;
            }
        }

        $this->draftPackageItems[] = [
            'temp_id' => uniqid(),
            'game_name' => $this->selectedGame,
            'court_number' => $this->selectedCourt,
            'booking_date' => $this->selectedDate,
            'formatted_date' => $formattedDate,
            'day_name' => $dateCarbon->format('l'),
            'start_time' => strlen($this->selectedTime) === 5 ? $this->selectedTime . ':00' : $this->selectedTime,
            'end_time' => strlen($this->selectedEndTime) === 5 ? $this->selectedEndTime . ':00' : $this->selectedEndTime,
            'formatted_time' => $formattedTime,
            'duration' => $duration,
            'is_private' => (bool) $this->is_private,
            'unit_price' => $unitPrice,
            'num_persons' => $this->num_persons,
            'estimated_price' => $itemTotalPrice,
            'avail_count' => $availCount,
            'blocked_count' => $blockedCount,
        ];

        session()->flash('draft_message', 'Item added to package builder table.');
    }

    public function removeDraftItem($tempId)
    {
        $this->draftPackageItems = array_values(array_filter($this->draftPackageItems, fn($i) => $i['temp_id'] !== $tempId));
    }

    /**
     * Create Custom Package Booking with all items in the draft table
     */
    public function createCustomPackageBooking()
    {
        $this->validate([
            'playerName' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
        ]);

        if (empty($this->draftPackageItems)) {
            $this->addError('draft', 'Please add at least one booking item to the package table before confirming.');
            return;
        }

        $staffUser = Auth::user();
        $userUser = UserUser::where('email', $staffUser->email)->first();

        $packageCode = 'PKG-' . strtoupper(substr(md5(uniqid()), 0, 6));
        $this->bookedSlots = [];
        $this->skippedSlots = [];
        $this->bookedCount = 0;

        $advancePaid = max(0, (float) ($this->advance_amount ?? 0));

        foreach ($this->draftPackageItems as $item) {
            $sport = BookingSport::where('venue_id', $this->complex_id)
                ->whereRaw('LOWER(name) = ?', [strtolower($item['game_name'])])
                ->first();

            $startHour = (int) Carbon::parse($item['start_time'])->format('H');
            $endHour = (int) Carbon::parse($item['end_time'])->format('H');
            if ($endHour === 0 || $endHour <= $startHour) $endHour = 24;

            $numPersonsCount = max(1, (int) ($item['num_persons'] ?? 1));
            $adminCommentsPayload = json_encode([
                'custom_booking' => true,
                'package_code' => $packageCode,
                'num_persons' => $numPersonsCount,
            ]);

            $unitPrice = $item['unit_price'] ?? ($sport->price ?? 1800.00);
            $slotPrice = $unitPrice * $numPersonsCount;

            $baseData = [
                'user_id_id' => $userUser?->id,
                'complex_id_id' => $this->complex_id,
                'game_id_id' => $sport?->id,
                'game_name' => $item['game_name'],
                'user_name' => $this->playerName,
                'user_number' => $this->phoneNumber,
                'court_number' => $item['court_number'],
                'duration' => 60,
                'price' => $slotPrice,
                'advance_amount' => $advancePaid,
                'amount_paid' => $advancePaid,
                'balance_due' => max(0, $slotPrice - $advancePaid),
                'financial_status' => $advancePaid > 0 ? 'Partial' : 'Pending',
                'is_initial_permanent_occurrence' => false,
                'offline_paid_amount' => $advancePaid,
                'online_paid_amount' => 0,
                'points_discount_amount' => 0,
                'requires_advance_payment' => $advancePaid > 0,
                'reward_status' => 'not_eligible',
                'payment_status' => $advancePaid > 0 ? 'Partial' : 'Pending',
                'payment_method' => $advancePaid > 0 ? 'Cash' : null,
                'status' => $this->status,
                'notes' => $this->notes ?: '',
                'admin_comments' => $adminCommentsPayload,
                'is_challenge_booking' => false,
                'is_private' => !empty($item['is_private']),
            ];

            $charges = is_array($sport?->additional_charges) ? $sport->additional_charges : (json_decode($sport?->additional_charges, true) ?? []);
            $maxCapForGame = (int) ($charges['max_persons_per_hour'] ?? 1);
            $blockedSlotsData = ($sport && $sport->blocked_slots)
                ? (is_array($sport->blocked_slots) ? $sport->blocked_slots : json_decode($sport->blocked_slots, true))
                : [];

            $dStr = $item['booking_date'];
            $formattedDate = Carbon::parse($dStr)->format('D, M j, Y') . ' (' . Carbon::parse($dStr)->format('l') . ')';

            for ($h = $startHour; $h < $endHour; $h++) {
                $slotStartStr = sprintf('%02d:00:00', $h);
                $slotEndStr = sprintf('%02d:00:00', ($h + 1) % 24);
                $slotLabel = Carbon::parse($slotStartStr)->format('g:i A') . ' - ' . Carbon::parse($slotEndStr)->format('g:i A');

                $existingBookings = BookingBooking::where('complex_id_id', $this->complex_id)
                    ->whereRaw('LOWER(game_name) = ?', [strtolower($item['game_name'])])
                    ->where('court_number', $item['court_number'])
                    ->where('booking_date', $dStr)
                    ->where('start_time', $slotStartStr)
                    ->whereRaw('LOWER(status) != ?', ['cancelled'])
                    ->get();

                $courtKey = 'court_' . strtolower(str_replace(['court ', 'court'], '', trim(strtolower($item['court_number']))));
                $isBlocked = isset($blockedSlotsData[$dStr][$slotStartStr][$courtKey]) || isset($blockedSlotsData[$dStr][$slotStartStr][$item['court_number']]);

                if ($existingBookings->count() >= $maxCapForGame || $isBlocked) {
                    $reason = $isBlocked ? 'Blocked' : 'Already Booked';
                    if ($existingBookings->count() > 0 && $existingBookings->first()->user_name) {
                        $reason .= " by {$existingBookings->first()->user_name}";
                    }
                    $this->skippedSlots[] = [
                        'date' => $formattedDate,
                        'slot' => $slotLabel,
                        'court' => $item['court_number'],
                        'reason' => $reason,
                    ];
                } else {
                    BookingBooking::create(array_merge($baseData, [
                        'start_time' => $slotStartStr,
                        'end_time' => $slotEndStr,
                        'booking_date' => $dStr,
                        'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
                    ]));

                    $this->bookedSlots[] = [
                        'date' => $formattedDate,
                        'slot' => $slotLabel,
                        'court' => $item['court_number'],
                    ];
                    $this->bookedCount++;
                }
            }
        }

        if ($this->bookedCount > 0) {
            try {
                \App\Models\BookingNotification::create([
                    'user_id' => auth()->id(),
                    'type' => 'custom_package_created',
                    'title' => 'Custom Package Created',
                    'message' => "Package {$packageCode}: {$this->bookedCount} slot(s) booked for {$this->playerName}",
                    'data' => ['package_code' => $packageCode, 'player' => $this->playerName],
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed writing notification: ' . $e->getMessage());
            }
        }

        $this->showCreateModal = false;
        $this->showResultModal = true;
        $this->resetPage();
    }

    public function viewPackageDetails($packageCode)
    {
        $allPackages = $this->getGroupedPackages();
        foreach ($allPackages as $pkg) {
            if ($pkg['package_code'] === $packageCode) {
                $this->viewPackageGroup = $pkg;
                $this->showViewPackageModal = true;
                break;
            }
        }
    }

    public function closeViewPackageModal()
    {
        $this->showViewPackageModal = false;
        $this->viewPackageGroup = null;
    }

    /**
     * Initiate Package Cancellation flow with 3 options: Full Package, Day-Wise, or Slot-Wise
     */
    public function initiateCancelPackage($packageCode)
    {
        $allPackages = $this->getGroupedPackages();
        $targetPkg = null;

        foreach ($allPackages as $pkg) {
            if ($pkg['package_code'] === $packageCode) {
                $targetPkg = $pkg;
                break;
            }
        }

        if (!$targetPkg) {
            session()->flash('message', 'Package not found.');
            return;
        }

        $activeSlots = array_filter($targetPkg['slot_details'], fn($s) => strtolower($s['status']) !== 'cancelled');

        if (count($activeSlots) === 0) {
            session()->flash('message', 'All slots in this package are already cancelled.');
            return;
        }

        $this->cancelPackageGroup = $targetPkg;
        $this->selectedCancelDates = array_values(array_unique(array_column($activeSlots, 'booking_date')));
        $this->selectedCancelSlotIds = array_map(fn($s) => (string)$s['id'], array_values($activeSlots));
        $this->cancelType = 'choose';
        $this->showCancelModal = true;
    }

    public function setCancelType($type)
    {
        $this->cancelType = $type;
    }

    public function cancelEntirePackage()
    {
        if (empty($this->cancelPackageGroup)) return;

        $count = 0;
        foreach ($this->cancelPackageGroup['slot_ids'] as $id) {
            $b = BookingBooking::find($id);
            if ($b && strtolower($b->status) !== 'cancelled') {
                $b->status = 'Cancelled';
                $b->save();
                $count++;
            }
        }

        $this->closeCancelModal();
        session()->flash('message', "Entire package ({$count} slot(s)) has been cancelled successfully.");
    }

    public function cancelSelectedDays()
    {
        if (empty($this->selectedCancelDates)) {
            $this->addError('cancel_error', 'Please select at least one day to cancel.');
            return;
        }

        $count = 0;
        foreach ($this->cancelPackageGroup['slot_details'] as $slot) {
            if (in_array($slot['booking_date'], $this->selectedCancelDates) && strtolower($slot['status']) !== 'cancelled') {
                $b = BookingBooking::find($slot['id']);
                if ($b) {
                    $b->status = 'Cancelled';
                    $b->save();
                    $count++;
                }
            }
        }

        $this->closeCancelModal();
        session()->flash('message', "Selected day(s) ({$count} slot(s)) cancelled successfully.");
    }

    public function cancelSelectedSpecificSlots()
    {
        if (empty($this->selectedCancelSlotIds)) {
            $this->addError('cancel_error', 'Please select at least one time slot to cancel.');
            return;
        }

        $count = 0;
        foreach ($this->selectedCancelSlotIds as $id) {
            $b = BookingBooking::find($id);
            if ($b && strtolower($b->status) !== 'cancelled') {
                $b->status = 'Cancelled';
                $b->save();
                $count++;
            }
        }

        $this->closeCancelModal();
        session()->flash('message', "{$count} selected slot(s) cancelled successfully.");
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancelPackageGroup = null;
        $this->cancelType = 'choose';
        $this->selectedCancelDates = [];
        $this->selectedCancelSlotIds = [];
    }

    public function resetForm()
    {
        $this->showResultModal = false;
        $this->showCreateModal = false;
        $this->playerName = '';
        $this->phoneNumber = '';
        $this->advance_amount = 0;
        $this->notes = '';
        $this->is_private = false;
        $this->num_persons = 1;
        $this->draftPackageItems = [];
        $this->bookedSlots = [];
        $this->skippedSlots = [];
        $this->bookedCount = 0;
    }

    /**
     * Group custom bookings by Package Code / Group Session
     */
    protected function getGroupedPackages()
    {
        $query = BookingBooking::where('complex_id_id', $this->complex_id);

        if (!empty($this->search)) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('user_name', 'like', '%' . $s . '%')
                  ->orWhere('user_number', 'like', '%' . $s . '%')
                  ->orWhere('game_name', 'like', '%' . $s . '%')
                  ->orWhere('court_number', 'like', '%' . $s . '%')
                  ->orWhere('admin_comments', 'like', '%' . $s . '%')
                  ->orWhere('id', 'like', '%' . $s . '%');
            });
        }

        if (!empty($this->filterDate)) {
            $query->where('booking_date', $this->filterDate);
        }

        $allBookings = $query->orderBy('created_at', 'desc')
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $grouped = [];
        foreach ($allBookings as $b) {
            $pkgCode = null;
            if (!empty($b->admin_comments) && str_contains($b->admin_comments, '{')) {
                $jPayload = json_decode($b->admin_comments, true);
                if (isset($jPayload['package_code']) && !empty($jPayload['package_code'])) {
                    $pkgCode = $jPayload['package_code'];
                }
            }

            // Only display custom package bookings in this table
            if (!$pkgCode) {
                continue;
            }

            if (!isset($grouped[$pkgCode])) {
                $grouped[$pkgCode] = [];
            }
            $grouped[$pkgCode][] = $b;
        }

        $mergedList = [];
        foreach ($grouped as $pkgCode => $items) {
            $first = $items[0];
            $displayCode = str_starts_with($pkgCode, 'PKG-LEGACY-') ? '#' . $first->id : $pkgCode;

            $totalPrice = 0;
            $totalAdvance = 0;
            $slotIds = [];
            $allCancelled = true;
            $anyCancelled = false;
            $slotDetails = [];
            $datesMap = [];
            $sportsMap = [];
            $dateSportGroupMap = [];

            foreach ($items as $item) {
                $totalPrice += (float) ($item->price ?? 0);
                $totalAdvance += (float) ($item->advance_amount ?? 0);
                $slotIds[] = $item->id;

                $isCancelled = strtolower($item->status) === 'cancelled';
                if ($isCancelled) {
                    $anyCancelled = true;
                } else {
                    $allCancelled = false;
                }

                $dStr = is_object($item->booking_date) ? $item->booking_date->format('Y-m-d') : (string) $item->booking_date;
                if (!isset($datesMap[$dStr])) {
                    $datesMap[$dStr] = [
                        'date' => $dStr,
                        'formatted' => Carbon::parse($dStr)->format('D, M j, Y') . ' (' . Carbon::parse($dStr)->format('l') . ')',
                        'slots_count' => 0,
                    ];
                }
                $datesMap[$dStr]['slots_count']++;

                $spKey = $item->game_name . ' (Court ' . $item->court_number . ')';
                $sportsMap[$spKey] = true;

                $slotData = [
                    'id' => $item->id,
                    'booking_date' => $dStr,
                    'formatted_date' => Carbon::parse($dStr)->format('D, M j, Y') . ' (' . Carbon::parse($dStr)->format('l') . ')',
                    'start_time' => $item->start_time,
                    'end_time' => $item->end_time,
                    'formatted_time' => Carbon::parse($item->start_time)->format('g:i A') . ' - ' . Carbon::parse($item->end_time)->format('g:i A'),
                    'game_name' => $item->game_name,
                    'court_number' => $item->court_number,
                    'price' => (float) ($item->price ?? 0),
                    'status' => $item->status,
                    'is_private' => (bool) $item->is_private,
                ];

                $slotDetails[] = $slotData;

                $dsKey = $dStr . '|' . strtolower($item->game_name) . '|' . strtolower($item->court_number);
                if (!isset($dateSportGroupMap[$dsKey])) {
                    $dateSportGroupMap[$dsKey] = [
                        'date' => $dStr,
                        'formatted_date' => Carbon::parse($dStr)->format('D, M j, Y') . ' (' . Carbon::parse($dStr)->format('l') . ')',
                        'game_name' => $item->game_name,
                        'court_number' => $item->court_number,
                        'slots' => [],
                        'total_price' => 0,
                        'slot_count' => 0,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'is_private' => (bool) $item->is_private,
                    ];
                }
                $dateSportGroupMap[$dsKey]['slots'][] = $slotData;
                $dateSportGroupMap[$dsKey]['total_price'] += (float) ($item->price ?? 0);
                $dateSportGroupMap[$dsKey]['slot_count']++;
                $dateSportGroupMap[$dsKey]['end_time'] = $item->end_time;
            }

            $groupedByDateSport = [];
            foreach ($dateSportGroupMap as $dsGroup) {
                $dsGroup['time_range'] = Carbon::parse($dsGroup['start_time'])->format('g:i A') . ' - ' . Carbon::parse($dsGroup['end_time'])->format('g:i A');
                $groupedByDateSport[] = $dsGroup;
            }

            $overallStatus = $allCancelled ? 'Cancelled' : ($anyCancelled ? 'Partially Cancelled' : $first->status);
            $uniqueDates = array_values($datesMap);
            $dateRangeStr = count($uniqueDates) === 1
                ? $uniqueDates[0]['formatted']
                : $uniqueDates[0]['formatted'] . ' to ' . end($uniqueDates)['formatted'];

            $mergedList[] = [
                'package_code' => $displayCode,
                'raw_package_code' => $pkgCode,
                'user_name' => $first->user_name,
                'user_number' => $first->user_number,
                'sports_summary' => implode(', ', array_keys($sportsMap)),
                'date_range_summary' => $dateRangeStr,
                'dates_list' => $uniqueDates,
                'total_slots' => count($items),
                'total_price' => $totalPrice,
                'total_advance' => $totalAdvance,
                'payment_status' => $first->payment_status,
                'status' => $overallStatus,
                'slot_ids' => $slotIds,
                'slot_details' => $slotDetails,
                'grouped_by_date_sport' => $groupedByDateSport,
                'is_private' => (bool) $first->is_private,
                'created_at' => $first->created_at,
            ];
        }

        usort($mergedList, function($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });

        return $mergedList;
    }

    public function render()
    {
        $allPackages = $this->getGroupedPackages();

        $currentPage = $this->getPage();
        $perPage = 10;
        $total = count($allPackages);
        $offset = ($currentPage - 1) * $perPage;
        $itemsForCurrentPage = array_slice($allPackages, $offset, $perPage);

        $paginatedPackages = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $total,
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('livewire.staff.custom-booking', [
            'groupedPackages' => $paginatedPackages,
        ]);
    }
}

<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\PoolsPool;
use App\Models\PoolsPooladmissiontype;
use App\Models\PoolsPoolbooking;
use App\Models\PoolsPoolbookingitem;
use App\Models\PoolsPoolsessionoccurrence;
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

    // Swimming Pool Admissions / Tiers State
    public $poolAdmissionTypes = [];
    public $ticketQuantities = [];

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
            $this->selectedGame = $this->sports[0]['name'] ?? '';
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

        $courtSports = BookingSport::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'type' => 'sport',
                    'maximum_court' => $s->maximum_court ?? 1,
                    'price' => (float) ($s->price ?? 0),
                    'private_booking_enabled' => (bool) $s->private_booking_enabled,
                    'blocked_slots' => $s->blocked_slots,
                    'additional_charges' => $s->additional_charges,
                ];
            });

        $pools = PoolsPool::where('venue_id', $this->complex_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'type' => 'pool',
                    'maximum_court' => 1,
                    'price' => (float) ($p->private_booking_price ?? 0),
                    'private_booking_enabled' => !is_null($p->private_booking_enabled) ? (bool) $p->private_booking_enabled : true,
                    'blocked_slots' => $p->blocked_slots,
                    'additional_charges' => null,
                ];
            });

        $this->sports = $courtSports->concat($pools)->toArray();

        if (count($this->sports) > 0 && empty($this->selectedGame)) {
            $this->selectedGame = $this->sports[0]['name'];
            $this->updatedSelectedGame($this->selectedGame);
        }
    }

    protected function getFacilityByName($name)
    {
        foreach ($this->sports as $facility) {
            if (is_array($facility) && strtolower($facility['name']) === strtolower($name)) {
                return $facility;
            }
        }
        return null;
    }

    public function updatedSelectedGame($value)
    {
        $facility = $this->getFacilityByName($value);
        if ($facility && $facility['type'] === 'pool') {
            $this->availableCourts = ['Main Pool'];
            $admissions = PoolsPooladmissiontype::where('pool_id', $facility['id'])
                ->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();
            $this->poolAdmissionTypes = $admissions->toArray();
            $this->ticketQuantities = [];
            foreach ($this->poolAdmissionTypes as $type) {
                $this->ticketQuantities[$type['id']] = 0;
            }
            if (!empty($this->poolAdmissionTypes)) {
                $firstId = $this->poolAdmissionTypes[0]['id'];
                $this->ticketQuantities[$firstId] = 1;
            }
        } else {
            $maxCourts = $facility ? ($facility['maximum_court'] ?? 1) : 1;
            $this->availableCourts = [];
            for ($i = 1; $i <= $maxCourts; $i++) {
                $this->availableCourts[] = "{$i}";
            }
            $this->poolAdmissionTypes = [];
            $this->ticketQuantities = [];
        }

        if (!in_array($this->selectedCourt, $this->availableCourts)) {
            $this->selectedCourt = $this->availableCourts[0] ?? '1';
        }
    }

    public function incrementTicket($typeId)
    {
        $this->ticketQuantities[$typeId] = ($this->ticketQuantities[$typeId] ?? 0) + 1;
    }

    public function decrementTicket($typeId)
    {
        $this->ticketQuantities[$typeId] = max(0, ($this->ticketQuantities[$typeId] ?? 0) - 1);
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

        $facility = $this->getFacilityByName($this->selectedGame);
        if (!$facility) {
            return false;
        }

        return (bool) ($facility['private_booking_enabled'] ?? false);
    }

    public function getDaySlotsTimeline()
    {
        if (empty($this->selectedGame) || empty($this->selectedCourt) || empty($this->selectedDate)) {
            return [];
        }

        $facility = $this->getFacilityByName($this->selectedGame);
        if (!$facility) return [];

        $isPool = ($facility['type'] === 'pool');
        $pool = $isPool ? PoolsPool::find($facility['id']) : null;
        $sport = !$isPool ? BookingSport::find($facility['id']) : null;

        $rawBlocked = $isPool ? ($pool ? $pool->blocked_slots : []) : ($sport ? $sport->blocked_slots : []);
        $blockedSlotsData = is_array($rawBlocked) ? $rawBlocked : (json_decode($rawBlocked, true) ?? []);

        $charges = !$isPool && $sport ? (is_array($sport->additional_charges) ? $sport->additional_charges : (json_decode($sport->additional_charges, true) ?? [])) : [];
        $maxCapForGame = $isPool ? ($pool->capacity ?: 50) : (int) ($charges['max_persons_per_hour'] ?? 1);

        $timeline = [];
        for ($h = 6; $h <= 23; $h++) {
            $slotStartStr = sprintf('%02d:00:00', $h);
            $slotEndStr = sprintf('%02d:00:00', ($h + 1) % 24);
            $startDisplay = Carbon::parse($slotStartStr)->format('g:i A');
            $endDisplay = Carbon::parse($slotEndStr)->format('g:i A');

            if ($isPool && $pool) {
                $existingCount = PoolsPoolbooking::where('pool_id', $pool->id)
                    ->where(function($q) use ($slotStartStr) {
                        $q->whereDate('created_at', $this->selectedDate)
                          ->orWhereHas('occurrence', function($occQ) use ($slotStartStr) {
                              $occQ->whereDate('session_date', $this->selectedDate)
                                   ->where('start_time', '<=', $slotStartStr)
                                   ->where('end_time', '>', $slotStartStr);
                          });
                    })
                    ->whereRaw('LOWER(status) != ?', ['cancelled'])
                    ->sum('total_admissions');
                
                $isBlocked = isset($blockedSlotsData[$this->selectedDate][$slotStartStr]);
            } else {
                $existingBookings = BookingBooking::where('complex_id_id', $this->complex_id)
                    ->whereRaw('LOWER(game_name) = ?', [strtolower($this->selectedGame)])
                    ->where('court_number', $this->selectedCourt)
                    ->where('booking_date', $this->selectedDate)
                    ->where('start_time', $slotStartStr)
                    ->whereRaw('LOWER(status) != ?', ['cancelled'])
                    ->get();
                $existingCount = $existingBookings->count();

                $courtKey = 'court_' . strtolower(str_replace(['court ', 'court'], '', trim(strtolower($this->selectedCourt))));
                $isBlocked = isset($blockedSlotsData[$this->selectedDate][$slotStartStr][$courtKey]) || isset($blockedSlotsData[$this->selectedDate][$slotStartStr][$this->selectedCourt]);
            }

            $status = 'available';
            $bookedBy = null;

            if ($isBlocked) {
                $status = 'blocked';
            } elseif ($existingCount >= $maxCapForGame) {
                $status = 'booked';
                $bookedBy = 'Booked';
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

        $facility = $this->getFacilityByName($this->selectedGame);
        if (!$facility) {
            $this->addError('selectedGame', 'Selected facility is not available.');
            return;
        }

        $isPool = ($facility['type'] === 'pool');

        $startHour = (int) Carbon::parse($this->selectedTime)->format('H');
        $endHour = (int) Carbon::parse($this->selectedEndTime)->format('H');
        if ($endHour === 0 || $endHour <= $startHour) $endHour = 24;

        $duration = $endHour - $startHour;

        $ticketBreakdown = [];
        $totalPersons = 0;
        $poolTicketsTotal = 0;

        if ($isPool && !empty($this->poolAdmissionTypes)) {
            foreach ($this->poolAdmissionTypes as $type) {
                $q = (int) ($this->ticketQuantities[$type['id']] ?? 0);
                if ($q > 0) {
                    $totalPersons += $q;
                    $lineVal = $q * (float) $type['price'];
                    $poolTicketsTotal += $lineVal;
                    $ticketBreakdown[] = [
                        'admission_type_id' => $type['id'],
                        'name' => $type['name'],
                        'quantity' => $q,
                        'unit_price' => (float) $type['price'],
                        'line_total' => $lineVal,
                    ];
                }
            }
        }

        if ($totalPersons === 0) {
            $totalPersons = max(1, (int) $this->num_persons);
        }

        $unitPrice = (float) ($facility['price'] ?? 1800.00);
        if ($isPool) {
            if ($this->is_private) {
                $unitPrice = (float) ($facility['price'] ?? 5000.00);
                $itemTotalPrice = $unitPrice * $duration;
            } elseif (!empty($ticketBreakdown)) {
                $unitPrice = $totalPersons > 0 ? ($poolTicketsTotal / $totalPersons) : 500.00;
                $itemTotalPrice = $poolTicketsTotal * $duration;
            } else {
                $unitPrice = 500.00;
                $itemTotalPrice = $unitPrice * $totalPersons * $duration;
            }
        } else {
            $itemTotalPrice = $unitPrice * $totalPersons * $duration;
        }

        $dateCarbon = Carbon::parse($this->selectedDate);
        $formattedDate = $dateCarbon->format('Y-m-d') . ' (' . $dateCarbon->format('l') . ')';

        $startDisplay = Carbon::parse($this->selectedTime)->format('g:i A');
        $endDisplay = Carbon::parse($this->selectedEndTime)->format('g:i A');
        $formattedTime = "{$startDisplay} - {$endDisplay} ({$duration} " . ($duration === 1 ? 'hr' : 'hrs') . ")";

        $this->draftPackageItems[] = [
            'temp_id' => uniqid(),
            'game_name' => $this->selectedGame,
            'facility_type' => $facility['type'],
            'facility_id' => $facility['id'],
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
            'num_persons' => $totalPersons,
            'ticket_breakdown' => $ticketBreakdown,
            'estimated_price' => $itemTotalPrice,
            'avail_count' => $duration,
            'blocked_count' => 0,
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
            $isPool = ($item['facility_type'] ?? 'sport') === 'pool';

            $startHour = (int) Carbon::parse($item['start_time'])->format('H');
            $endHour = (int) Carbon::parse($item['end_time'])->format('H');
            if ($endHour === 0 || $endHour <= $startHour) $endHour = 24;

            $numPersonsCount = max(1, (int) ($item['num_persons'] ?? 1));
            $adminCommentsPayload = json_encode([
                'custom_booking' => true,
                'package_code' => $packageCode,
                'num_persons' => $numPersonsCount,
                'game_name' => $item['game_name'],
                'court_number' => $item['court_number'],
            ]);

            $dStr = $item['booking_date'];
            $formattedDate = Carbon::parse($dStr)->format('D, M j, Y') . ' (' . Carbon::parse($dStr)->format('l') . ')';

            for ($h = $startHour; $h < $endHour; $h++) {
                $slotStartStr = sprintf('%02d:00:00', $h);
                $slotEndStr = sprintf('%02d:00:00', ($h + 1) % 24);
                $slotLabel = Carbon::parse($slotStartStr)->format('g:i A') . ' - ' . Carbon::parse($slotEndStr)->format('g:i A');

                if ($isPool) {
                    $pool = PoolsPool::find($item['facility_id']) ?: PoolsPool::where('venue_id', $this->complex_id)->first();
                    if ($pool) {
                        $occ = PoolsPoolsessionoccurrence::firstOrCreate([
                            'pool_id' => $pool->id,
                            'session_date' => $dStr,
                            'start_time' => $slotStartStr,
                            'end_time' => $slotEndStr,
                        ], [
                            'name' => "Session ({$slotLabel})",
                            'capacity' => $pool->capacity ?: 50,
                            'status' => 'open',
                        ]);

                        $unitPrice = $item['unit_price'] ?? ($pool->private_booking_price ?? 500.00);
                        $slotPrice = $item['estimated_price'] / max(1, $item['duration']);

                        $poolBooking = PoolsPoolbooking::create([
                            'booking_reference' => 'PKG-POOL-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                            'pool_id' => $pool->id,
                            'user_id' => $userUser?->id,
                            'occurrence_id' => $occ->id,
                            'user_name' => $this->playerName,
                            'user_number' => $this->phoneNumber,
                            'status' => $this->status,
                            'total_admissions' => $numPersonsCount,
                            'booking_total' => $slotPrice,
                            'total_amount' => $slotPrice,
                            'advance_amount' => $advancePaid,
                            'amount_paid' => $advancePaid,
                            'balance_due' => max(0, $slotPrice - $advancePaid),
                            'financial_status' => $advancePaid > 0 ? 'Partial' : 'Pending',
                            'payment_status' => $advancePaid > 0 ? 'Partial' : 'Pending',
                            'is_private' => !empty($item['is_private']),
                            'notes' => $adminCommentsPayload,
                        ]);

                        if (!empty($item['ticket_breakdown'])) {
                            foreach ($item['ticket_breakdown'] as $tb) {
                                PoolsPoolbookingitem::create([
                                    'pool_booking_id' => $poolBooking->id,
                                    'admission_type_id' => $tb['admission_type_id'],
                                    'quantity' => $tb['quantity'],
                                    'unit_price' => $tb['unit_price'],
                                    'line_total' => $tb['line_total'],
                                    'guest_name' => $this->playerName,
                                    'created_at' => now(),
                                ]);
                            }
                        }

                        $this->bookedSlots[] = [
                            'date' => $formattedDate,
                            'slot' => $slotLabel,
                            'court' => $item['court_number'],
                        ];
                        $this->bookedCount++;
                    }
                } else {
                    $sport = BookingSport::where('venue_id', $this->complex_id)
                        ->whereRaw('LOWER(name) = ?', [strtolower($item['game_name'])])
                        ->first();

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
        foreach ($this->cancelPackageGroup['slot_details'] as $slot) {
            if (strtolower($slot['status']) !== 'cancelled') {
                if (($slot['item_type'] ?? 'sport') === 'pool') {
                    $pb = PoolsPoolbooking::find($slot['id']);
                    if ($pb) {
                        $pb->status = 'Cancelled';
                        $pb->save();
                        $count++;
                    }
                } else {
                    $b = BookingBooking::find($slot['id']);
                    if ($b) {
                        $b->status = 'Cancelled';
                        $b->save();
                        $count++;
                    }
                }
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
                if (($slot['item_type'] ?? 'sport') === 'pool') {
                    $pb = PoolsPoolbooking::find($slot['id']);
                    if ($pb) {
                        $pb->status = 'Cancelled';
                        $pb->save();
                        $count++;
                    }
                } else {
                    $b = BookingBooking::find($slot['id']);
                    if ($b) {
                        $b->status = 'Cancelled';
                        $b->save();
                        $count++;
                    }
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
        foreach ($this->cancelPackageGroup['slot_details'] as $slot) {
            if (in_array((string)$slot['id'], $this->selectedCancelSlotIds) && strtolower($slot['status']) !== 'cancelled') {
                if (($slot['item_type'] ?? 'sport') === 'pool') {
                    $pb = PoolsPoolbooking::find($slot['id']);
                    if ($pb) {
                        $pb->status = 'Cancelled';
                        $pb->save();
                        $count++;
                    }
                } else {
                    $b = BookingBooking::find($slot['id']);
                    if ($b) {
                        $b->status = 'Cancelled';
                        $b->save();
                        $count++;
                    }
                }
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
        $this->poolAdmissionTypes = [];
        $this->ticketQuantities = [];
        $this->draftPackageItems = [];
        $this->bookedSlots = [];
        $this->skippedSlots = [];
        $this->bookedCount = 0;
    }

    protected function getGroupedPackages()
    {
        $grouped = [];

        // 1. Fetch BookingBooking records
        $sportQuery = BookingBooking::where('complex_id_id', $this->complex_id);
        if (!empty($this->search)) {
            $s = trim($this->search);
            $sportQuery->where(function ($q) use ($s) {
                $q->where('user_name', 'like', '%' . $s . '%')
                  ->orWhere('user_number', 'like', '%' . $s . '%')
                  ->orWhere('game_name', 'like', '%' . $s . '%')
                  ->orWhere('admin_comments', 'like', '%' . $s . '%');
            });
        }
        if (!empty($this->filterDate)) {
            $sportQuery->where('booking_date', $this->filterDate);
        }

        $sportBookings = $sportQuery->orderBy('created_at', 'desc')->get();

        foreach ($sportBookings as $b) {
            $pkgCode = null;
            if (!empty($b->admin_comments) && str_contains($b->admin_comments, '{')) {
                $jPayload = json_decode($b->admin_comments, true);
                if (isset($jPayload['package_code']) && !empty($jPayload['package_code'])) {
                    $pkgCode = $jPayload['package_code'];
                }
            }

            if (!$pkgCode) continue;

            if (!isset($grouped[$pkgCode])) {
                $grouped[$pkgCode] = [];
            }

            $grouped[$pkgCode][] = [
                'id' => $b->id,
                'item_type' => 'sport',
                'package_code' => $pkgCode,
                'user_name' => $b->user_name,
                'user_number' => $b->user_number,
                'game_name' => $b->game_name,
                'court_number' => $b->court_number,
                'booking_date' => is_object($b->booking_date) ? $b->booking_date->format('Y-m-d') : (string) $b->booking_date,
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'price' => (float) ($b->price ?? 0),
                'advance_amount' => (float) ($b->advance_amount ?? 0),
                'status' => $b->status,
                'payment_status' => $b->payment_status ?: $b->financial_status,
                'is_private' => (bool) $b->is_private,
                'created_at' => $b->created_at,
            ];
        }

        // 2. Fetch PoolsPoolbooking records
        $poolQuery = PoolsPoolbooking::whereHas('pool', function($q) {
            $q->where('venue_id', $this->complex_id);
        })->with(['pool', 'occurrence']);

        if (!empty($this->search)) {
            $s = trim($this->search);
            $poolQuery->where(function ($q) use ($s) {
                $q->where('user_name', 'like', '%' . $s . '%')
                  ->orWhere('user_number', 'like', '%' . $s . '%')
                  ->orWhere('notes', 'like', '%' . $s . '%');
            });
        }
        if (!empty($this->filterDate)) {
            $poolQuery->where(function($q) {
                $q->whereDate('created_at', $this->filterDate)
                  ->orWhereHas('occurrence', function($occQ) {
                      $occQ->whereDate('session_date', $this->filterDate);
                  });
            });
        }

        $poolBookings = $poolQuery->orderBy('created_at', 'desc')->get();

        foreach ($poolBookings as $pb) {
            $pkgCode = null;
            if (!empty($pb->notes) && str_contains($pb->notes, '{')) {
                $jPayload = json_decode($pb->notes, true);
                if (isset($jPayload['package_code']) && !empty($jPayload['package_code'])) {
                    $pkgCode = $jPayload['package_code'];
                }
            }

            if (!$pkgCode) continue;

            if (!isset($grouped[$pkgCode])) {
                $grouped[$pkgCode] = [];
            }

            $bDate = $pb->occurrence ? Carbon::parse($pb->occurrence->session_date)->format('Y-m-d') : Carbon::parse($pb->created_at)->format('Y-m-d');
            $sTime = $pb->occurrence ? $pb->occurrence->start_time : '09:00:00';
            $eTime = $pb->occurrence ? $pb->occurrence->end_time : '10:00:00';
            $gameName = $pb->pool ? $pb->pool->name : 'Swimming Pool';

            $grouped[$pkgCode][] = [
                'id' => $pb->id,
                'item_type' => 'pool',
                'package_code' => $pkgCode,
                'user_name' => $pb->user_name,
                'user_number' => $pb->user_number,
                'game_name' => $gameName,
                'court_number' => 'Main Pool',
                'booking_date' => $bDate,
                'start_time' => $sTime,
                'end_time' => $eTime,
                'price' => (float) ($pb->total_amount ?? $pb->booking_total ?? 0),
                'advance_amount' => (float) ($pb->advance_amount ?? 0),
                'status' => $pb->status,
                'payment_status' => $pb->payment_status ?: $pb->financial_status,
                'is_private' => (bool) $pb->is_private,
                'created_at' => $pb->created_at,
            ];
        }

        // 3. Build merged list
        $mergedList = [];
        foreach ($grouped as $pkgCode => $items) {
            $first = $items[0];
            $displayCode = str_starts_with($pkgCode, 'PKG-LEGACY-') ? '#' . $first['id'] : $pkgCode;

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
                $totalPrice += (float) ($item['price'] ?? 0);
                $totalAdvance += (float) ($item['advance_amount'] ?? 0);
                $slotIds[] = $item['id'];

                $isCancelled = strtolower($item['status']) === 'cancelled';
                if ($isCancelled) {
                    $anyCancelled = true;
                } else {
                    $allCancelled = false;
                }

                $dStr = $item['booking_date'];
                if (!isset($datesMap[$dStr])) {
                    $datesMap[$dStr] = [
                        'date' => $dStr,
                        'formatted' => Carbon::parse($dStr)->format('D, M j, Y') . ' (' . Carbon::parse($dStr)->format('l') . ')',
                        'slots_count' => 0,
                    ];
                }
                $datesMap[$dStr]['slots_count']++;

                $spKey = $item['game_name'] . ' (' . $item['court_number'] . ')';
                $sportsMap[$spKey] = true;

                $slotData = [
                    'id' => $item['id'],
                    'item_type' => $item['item_type'],
                    'booking_date' => $dStr,
                    'formatted_date' => Carbon::parse($dStr)->format('D, M j, Y') . ' (' . Carbon::parse($dStr)->format('l') . ')',
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                    'formatted_time' => Carbon::parse($item['start_time'])->format('g:i A') . ' - ' . Carbon::parse($item['end_time'])->format('g:i A'),
                    'game_name' => $item['game_name'],
                    'court_number' => $item['court_number'],
                    'price' => (float) ($item['price'] ?? 0),
                    'status' => $item['status'],
                    'is_private' => (bool) $item['is_private'],
                ];

                $slotDetails[] = $slotData;

                $dsKey = $dStr . '|' . strtolower($item['game_name']) . '|' . strtolower($item['court_number']);
                if (!isset($dateSportGroupMap[$dsKey])) {
                    $dateSportGroupMap[$dsKey] = [
                        'date' => $dStr,
                        'formatted_date' => Carbon::parse($dStr)->format('D, M j, Y') . ' (' . Carbon::parse($dStr)->format('l') . ')',
                        'game_name' => $item['game_name'],
                        'court_number' => $item['court_number'],
                        'slots' => [],
                        'total_price' => 0,
                        'slot_count' => 0,
                        'start_time' => $item['start_time'],
                        'end_time' => $item['end_time'],
                        'is_private' => (bool) $item['is_private'],
                    ];
                }
                $dateSportGroupMap[$dsKey]['slots'][] = $slotData;
                $dateSportGroupMap[$dsKey]['total_price'] += (float) ($item['price'] ?? 0);
                $dateSportGroupMap[$dsKey]['slot_count']++;
                $dateSportGroupMap[$dsKey]['end_time'] = $item['end_time'];
            }

            $groupedByDateSport = [];
            foreach ($dateSportGroupMap as $dsGroup) {
                $dsGroup['time_range'] = Carbon::parse($dsGroup['start_time'])->format('g:i A') . ' - ' . Carbon::parse($dsGroup['end_time'])->format('g:i A');
                $groupedByDateSport[] = $dsGroup;
            }

            $overallStatus = $allCancelled ? 'Cancelled' : ($anyCancelled ? 'Partially Cancelled' : $first['status']);
            $uniqueDates = array_values($datesMap);
            $dateRangeStr = count($uniqueDates) === 1
                ? $uniqueDates[0]['formatted']
                : $uniqueDates[0]['formatted'] . ' to ' . end($uniqueDates)['formatted'];

            $mergedList[] = [
                'package_code' => $displayCode,
                'raw_package_code' => $pkgCode,
                'user_name' => $first['user_name'],
                'user_number' => $first['user_number'],
                'sports_summary' => implode(', ', array_keys($sportsMap)),
                'date_range_summary' => $dateRangeStr,
                'dates_list' => $uniqueDates,
                'total_slots' => count($items),
                'total_price' => $totalPrice,
                'total_advance' => $totalAdvance,
                'payment_status' => $first['payment_status'],
                'status' => $overallStatus,
                'slot_ids' => $slotIds,
                'slot_details' => $slotDetails,
                'grouped_by_date_sport' => $groupedByDateSport,
                'is_private' => (bool) $first['is_private'],
                'created_at' => $first['created_at'],
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

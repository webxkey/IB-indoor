<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use App\Models\PoolsPool;
use App\Models\PoolsPooladmissiontype;
use App\Models\PoolsPoolbooking;
use App\Models\PoolsPoolbookingitem;
use App\Models\PoolsPoolsessionoccurrence;
use App\Events\BookingCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

#[Title("Pool Bookings Management")]
#[Layout("components.layouts.staff")]
class PoolBookingsManagement extends Component
{
    public $selectedDate;
    public $pool;
    public $admissionTypes;
    public $occurrences;
    public $bookings;

    // Booking modal state
    public $showCreateModal = false;
    public $selectedOccurrenceId = null;
    public $slotStartTime = '16:00:00';
    public $slotEndTime = '17:00:00';
    public $selectedEndTime = '17:00:00';
    public $endTimeOptions = [];
    public $userName = '';
    public $userNumber = '';
    public $ticketQuantities = [];
    public $paymentStatus = 'Pending';
    public $permanent = false;
    public $isPrivate = false;
    public $notes = '';

    // Detail & Timer modal state
    public $showDetailModal = false;
    public $selectedBooking = null;

    // Cancel modal state
    public $showCancelModal = false;
    public $cancelBookingId = null;
    public $cancelSlotStart = null;
    public $cancelType = 'overall'; // 'choose', 'overall', 'individual'
    public $isMultiSlotBooking = false;

    // Payment Collect Modal State
    public $showPaymentCollectModal = false;
    public $paymentCollectBookingId = null;
    public $paymentCollectBooking = null;
    public $paymentCollectMethod = 'cash';

    public function mount()
    {
        $this->selectedDate = Carbon::today()->format('Y-m-d');
        $this->loadData();
    }

    public function updatedSelectedDate()
    {
        $this->loadData();
    }

    public function prevDay()
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->format('Y-m-d');
        $this->loadData();
    }

    public function nextDay()
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->format('Y-m-d');
        $this->loadData();
    }

    public function getTimeSlotsProperty()
    {
        $slots = [];
        for ($h = 6; $h < 23; $h++) {
            $start = sprintf('%02d:00:00', $h);
            $end = sprintf('%02d:00:00', $h + 1);

            $startFormatted = Carbon::parse($start)->format('g:i A');
            $endFormatted = Carbon::parse($end)->format('g:i A');

            $slots[] = [
                'start_time' => $start,
                'end_time' => $end,
                'label' => "{$startFormatted} - {$endFormatted}",
                'hour' => $h,
            ];
        }
        return $slots;
    }

    public function loadData()
    {
        $venueId = Auth::user()->complex_id;
        $this->pool = PoolsPool::where('venue_id', $venueId)->first();

        if ($this->pool) {
            $this->admissionTypes = PoolsPooladmissiontype::where('pool_id', $this->pool->id)
                ->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();

            // Initialize ticketQuantities array
            foreach ($this->admissionTypes as $type) {
                if (!isset($this->ticketQuantities[$type->id])) {
                    $this->ticketQuantities[$type->id] = 0;
                }
            }

            // Ensure occurrences exist for the selected date
            $this->ensureOccurrencesExist($this->pool, $this->selectedDate);

            $occQuery = PoolsPoolsessionoccurrence::where('pool_id', $this->pool->id)
                ->whereDate('session_date', $this->selectedDate);

            $this->occurrences = $occQuery->orderBy('start_time', 'asc')->get();

            $this->bookings = PoolsPoolbooking::where('pool_id', $this->pool->id)
                ->where(function($q) {
                    $q->whereDate('created_at', $this->selectedDate)
                      ->orWhereHas('occurrence', function($occQ) {
                          $occQ->whereDate('session_date', $this->selectedDate);
                      });
                })
                ->with(['items.admissionType', 'user', 'occurrence'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->admissionTypes = collect();
            $this->occurrences = collect();
            $this->bookings = collect();
        }
    }

    protected function ensureOccurrencesExist(PoolsPool $pool, string $dateStr)
    {
        $exists = PoolsPoolsessionoccurrence::where('pool_id', $pool->id)
            ->whereDate('session_date', $dateStr)
            ->exists();

        if (!$exists) {
            $defaultSessions = [
                ['name' => 'Morning Swim (Session 1)', 'start' => '06:00:00', 'end' => '08:00:00'],
                ['name' => 'Morning Swim (Session 2)', 'start' => '08:30:00', 'end' => '10:30:00'],
                ['name' => 'Afternoon Swim',           'start' => '14:00:00', 'end' => '16:00:00'],
                ['name' => 'Evening Swim (Session 1)', 'start' => '16:00:00', 'end' => '18:00:00'],
                ['name' => 'Evening Swim (Session 2)', 'start' => '18:30:00', 'end' => '20:30:00'],
            ];

            foreach ($defaultSessions as $sess) {
                PoolsPoolsessionoccurrence::create([
                    'pool_id' => $pool->id,
                    'session_date' => $dateStr,
                    'start_time' => $sess['start'],
                    'end_time' => $sess['end'],
                    'name' => $sess['name'],
                    'capacity' => $pool->capacity ?: 50,
                    'status' => 'open',
                ]);
            }
        }
    }

    public function openCreateModal($occurrenceId = null)
    {
        $this->resetBookingForm();
        $this->selectedOccurrenceId = $occurrenceId;

        if ($occurrenceId) {
            $occ = PoolsPoolsessionoccurrence::find($occurrenceId);
            if ($occ) {
                $this->slotStartTime = Carbon::parse($occ->start_time)->format('H:i:s');
            }
        } else {
            $this->slotStartTime = '16:00:00';
        }

        $this->updateEndTimeOptions();
        $this->showCreateModal = true;
    }

    public function openCreateModalWithSlot($startTime, $endTime)
    {
        $this->resetBookingForm();
        $this->slotStartTime = strlen($startTime) === 5 ? $startTime . ':00' : $startTime;
        $this->updateEndTimeOptions();
        if (!empty($endTime)) {
            $this->selectedEndTime = strlen($endTime) === 5 ? $endTime . ':00' : $endTime;
        }
        $this->showCreateModal = true;
    }

    public function openDetailModal($bookingId)
    {
        $this->selectedBooking = PoolsPoolbooking::with(['items.admissionType', 'pool', 'occurrence'])->find($bookingId);
        if ($this->selectedBooking) {
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedBooking = null;
    }

    public function completeBooking($bookingId)
    {
        $booking = null;
        if (is_numeric($bookingId)) {
            $booking = PoolsPoolbooking::with(['items.admissionType', 'pool', 'occurrence'])->find((int) $bookingId);
        }

        if (!$booking && !empty($bookingId)) {
            $booking = PoolsPoolbooking::with(['items.admissionType', 'pool', 'occurrence'])
                ->where('booking_reference', (string) $bookingId)
                ->orWhere('booking_reference', 'LIKE', (string) $bookingId . '%')
                ->first();
        }

        if (!$booking) {
            return;
        }

        // Mark booking status as Played / Completed
        $booking->update(['status' => 'played']);

        // Close the Detail modal automatically
        $this->closeDetailModal();

        // If payment status is Pending, open Payment Collect modal automatically
        if (strtolower($booking->financial_status ?: $booking->payment_status) !== 'paid') {
            $this->openPaymentCollectModal($booking->id);
        } else {
            session()->flash('message', "Booking #{$booking->booking_reference} force completed successfully.");
        }

        $this->loadData();
    }

    public function openPaymentCollectModal($bookingId)
    {
        $booking = PoolsPoolbooking::with(['items.admissionType', 'pool', 'occurrence'])->find($bookingId);
        if (!$booking) return;

        $this->paymentCollectBookingId = $bookingId;
        $this->paymentCollectBooking = $booking;
        $this->paymentCollectMethod = 'cash';
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
        if (!$this->paymentCollectBookingId) {
            return;
        }

        $booking = PoolsPoolbooking::find($this->paymentCollectBookingId);
        if ($booking) {
            $parentGroup = $this->extractParentGroupFromNotes($booking->notes);
            if ($parentGroup) {
                PoolsPoolbooking::where('notes', 'LIKE', "%Continuous Group: {$parentGroup}%")->update([
                    'financial_status' => 'Paid',
                    'payment_status' => 'Paid',
                    'amount_paid' => $booking->booking_total,
                    'balance_due' => 0.00,
                ]);
            } else {
                $booking->update([
                    'financial_status' => 'Paid',
                    'payment_status' => 'Paid',
                    'amount_paid' => $booking->booking_total,
                    'balance_due' => 0.00,
                ]);
            }

            session()->flash('message', "Payment of Rs. " . number_format($booking->booking_total, 2) . " collected successfully via " . ucfirst($this->paymentCollectMethod) . "!");
        }

        $this->closePaymentCollectModal();
        $this->loadData();
    }

    public function markAsPaid($bookingId)
    {
        $this->openPaymentCollectModal($bookingId);
    }

    public function updateEndTimeOptions()
    {
        $this->endTimeOptions = [];
        if (!$this->slotStartTime || !$this->selectedDate) {
            return;
        }

        $startTimeStr = strlen($this->slotStartTime) === 5 ? $this->slotStartTime . ':00' : $this->slotStartTime;
        $startHour = (int) Carbon::parse($startTimeStr)->format('H');

        $closeHour = 24;

        $options = [];
        for ($h = $startHour + 1; $h <= $closeHour; $h++) {
            $val = sprintf('%02d:00:00', $h % 24);
            $durationHours = $h - $startHour;

            $hour12 = $h % 12 ?: 12;
            $ampm = ($h % 24) < 12 ? 'AM' : 'PM';
            if ($h === 24) {
                $label = "12:00 AM ({$durationHours} " . ($durationHours === 1 ? 'hr' : 'hrs') . ")";
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

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetBookingForm();
    }

    public function resetBookingForm()
    {
        $this->selectedOccurrenceId = null;
        $this->slotStartTime = '16:00:00';
        $this->selectedEndTime = '17:00:00';
        $this->endTimeOptions = [];
        $this->userName = '';
        $this->userNumber = '';
        $this->paymentStatus = 'Pending';
        $this->permanent = false;
        $this->isPrivate = false;
        $this->notes = '';

        if ($this->admissionTypes) {
            foreach ($this->admissionTypes as $type) {
                $this->ticketQuantities[$type->id] = 0;
            }
        }
    }

    public function incrementTicket($typeId)
    {
        $current = $this->ticketQuantities[$typeId] ?? 0;
        $this->ticketQuantities[$typeId] = $current + 1;
    }

    public function decrementTicket($typeId)
    {
        $current = $this->ticketQuantities[$typeId] ?? 0;
        if ($current > 0) {
            $this->ticketQuantities[$typeId] = $current - 1;
        }
    }

    public function getTotalAmountProperty()
    {
        $total = 0;
        if ($this->admissionTypes) {
            foreach ($this->admissionTypes as $type) {
                $qty = $this->ticketQuantities[$type->id] ?? 0;
                $total += $qty * $type->price;
            }
        }
        return $total;
    }

    public function getTotalAdmissionsProperty()
    {
        $total = 0;
        if ($this->ticketQuantities) {
            foreach ($this->ticketQuantities as $qty) {
                $total += (int) $qty;
            }
        }
        return $total;
    }

    public function saveBooking()
    {
        $this->validate([
            'userName' => 'required|string|max:100',
            'userNumber' => 'required|string|max:20',
        ]);

        if (!$this->pool) {
            session()->flash('error', 'No pool configured for this venue.');
            return;
        }

        $totalAdmissions = $this->totalAdmissions;
        $totalAmount = $this->totalAmount;

        if ($totalAdmissions <= 0 && !$this->isPrivate) {
            session()->flash('error', 'Please select at least one admission ticket.');
            return;
        }

        // Shared parent group identifier for continuous multi-hour booking
        $parentRef = 'POOL-' . strtoupper(Str::random(6));

        $startHour = (int) Carbon::parse($this->slotStartTime)->format('H');
        $endHour = (int) Carbon::parse($this->selectedEndTime)->format('H');
        if ($endHour <= $startHour) {
            $endHour = $startHour + 1;
        }

        $isMulti = ($endHour - $startHour > 1);
        $createdBookingsCount = 0;

        // Create separate booking entry in pools_poolbooking for each 1-hour slot with unique reference and notes tag
        for ($h = $startHour; $h < $endHour; $h++) {
            $slotIndex = $h - $startHour + 1;
            $sFormatted = sprintf('%02d:00:00', $h % 24);
            $eFormatted = sprintf('%02d:00:00', ($h + 1) % 24);

            $occ = PoolsPoolsessionoccurrence::firstOrCreate(
                [
                    'pool_id' => $this->pool->id,
                    'session_date' => $this->selectedDate,
                    'start_time' => $sFormatted,
                    'end_time' => $eFormatted,
                ],
                [
                    'name' => 'Pool Slot ' . $sFormatted,
                    'capacity' => $this->pool->capacity ?: 50,
                    'status' => 'open',
                ]
            );

            // Unique booking_reference per row to satisfy Postgres UNIQUE constraint
            $slotRef = $isMulti ? "{$parentRef}-{$slotIndex}" : $parentRef;

            // Use notes column to tag continuous booking group
            $noteText = $isMulti ? "Continuous Group: {$parentRef}" : ($this->notes ?: '');
            if ($isMulti && !empty($this->notes)) {
                $noteText .= " | " . $this->notes;
            }

            $booking = PoolsPoolbooking::create([
                'booking_reference' => $slotRef,
                'pool_id' => $this->pool->id,
                'user_id' => Auth::id(),
                'occurrence_id' => $occ->id,
                'user_name' => $this->userName,
                'user_number' => $this->userNumber,
                'status' => 'Confirmed',
                'total_admissions' => $totalAdmissions,
                'booking_total' => $totalAmount,
                'total_amount' => $totalAmount,
                'amount_paid' => 0.00,
                'balance_due' => $totalAmount,
                'financial_status' => 'Pending',
                'payment_status' => 'Pending',
                'is_private' => (bool) $this->isPrivate,
                'notes' => $noteText,
            ]);

            // Save line items in pools_poolbookingitem table
            if ($this->admissionTypes) {
                foreach ($this->admissionTypes as $type) {
                    $qty = $this->ticketQuantities[$type->id] ?? 0;
                    if ($qty > 0) {
                        PoolsPoolbookingitem::create([
                            'pool_booking_id' => $booking->id,
                            'admission_type_id' => $type->id,
                            'quantity' => $qty,
                            'unit_price' => $type->price,
                            'line_total' => $qty * $type->price,
                            'guest_name' => $this->userName,
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            try {
                event(new BookingCreated($booking));
            } catch (\Throwable $e) {
                Log::warning('Pool booking event broadcast failed: ' . $e->getMessage());
            }

            $createdBookingsCount++;
        }

        session()->flash('message', "Pool Booking created successfully across {$createdBookingsCount} slot(s)! Ref: {$parentRef}");
        $this->closeCreateModal();
        $this->loadData();
    }

    public function initiateCancelBooking($bookingId, $slotStart = null)
    {
        $booking = PoolsPoolbooking::with('occurrence')->find($bookingId);
        if (!$booking) {
            return;
        }

        $this->cancelBookingId = $bookingId;
        $this->cancelSlotStart = $slotStart;

        $parentGroup = $this->extractParentGroupFromNotes($booking->notes);
        $isMulti = false;

        if ($parentGroup) {
            $count = PoolsPoolbooking::where('notes', 'LIKE', "%Continuous Group: {$parentGroup}%")
                ->where('status', 'Confirmed')
                ->count();
            if ($count > 1) {
                $isMulti = true;
            }
        }

        $this->isMultiSlotBooking = $isMulti;
        $this->cancelType = $isMulti ? 'choose' : 'overall';
        $this->showCancelModal = true;
    }

    private function extractParentGroupFromNotes(?string $notes): ?string
    {
        if (empty($notes)) return null;
        if (preg_match('/Continuous Group:\s*(POOL-[A-Z0-9]+)/i', $notes, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function setCancelType($type)
    {
        $this->cancelType = $type;
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancelBookingId = null;
        $this->cancelSlotStart = null;
        $this->cancelType = 'overall';
        $this->isMultiSlotBooking = false;
    }

    public function confirmCancelBooking()
    {
        if (!$this->cancelBookingId) {
            return;
        }

        $booking = PoolsPoolbooking::with('occurrence')->find($this->cancelBookingId);
        if (!$booking) {
            $this->closeCancelModal();
            return;
        }

        $parentGroup = $this->extractParentGroupFromNotes($booking->notes);

        if ($this->cancelType === 'overall' || !$this->isMultiSlotBooking) {
            if ($parentGroup) {
                PoolsPoolbooking::where('notes', 'LIKE', "%Continuous Group: {$parentGroup}%")
                    ->where('status', 'Confirmed')
                    ->update([
                        'status' => 'Cancelled',
                        'cancelled_at' => now(),
                    ]);
                session()->flash('message', "Overall continuous booking group {$parentGroup} cancelled successfully.");
            } else {
                $booking->update([
                    'status' => 'Cancelled',
                    'cancelled_at' => now(),
                ]);
                session()->flash('message', "Booking {$booking->booking_reference} cancelled successfully.");
            }
        } else {
            // Cancel ONLY this specific slot booking record individually
            $booking->update([
                'status' => 'Cancelled',
                'cancelled_at' => now(),
            ]);
            session()->flash('message', "Slot booking for {$booking->user_name} at {$this->cancelSlotStart} cancelled individually.");
        }

        if ($this->selectedBooking && $this->selectedBooking->id == $this->cancelBookingId) {
            $this->closeDetailModal();
        }

        $this->closeCancelModal();
        $this->loadData();
    }

    public function cancelBooking($bookingId)
    {
        return $this->initiateCancelBooking($bookingId);
    }

    public function render()
    {
        return view('livewire.staff.pool-bookings-management');
    }
}

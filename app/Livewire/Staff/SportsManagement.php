<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\BookingSport;
use App\Models\BookingVenue;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

#[Title("Sports Management")]
#[Layout("components.layouts.staff")]
class SportsManagement extends Component
{
    // No file uploads for sports images: use default images from public/images/sports_images

    public $sports;
    public $editSportId;

    public $complex_id;
    public $game_name;
    public $game_type = 'Outdoor';
    public $rate_type = 'Per hour';
    public $price;
    public $maximum_court;
    public $status;
    public $game_image;
    public $description;
    public $additional_charges = [];
    public $advance_required = false;
    public $existingImage;

    public $pricingRules = [
        'peak_price' => '',
        'offpeak_price' => '',
        'weekend_price' => '',
        'advance_discount' => '',  // percentage
        'advance_days' => '',      // days in advance
        'peak_schedule_enabled' => true,
        'peak_schedule' => [
            'monday'    => ['enabled' => true,  'start_time' => '17:00', 'end_time' => '22:00'],
            'tuesday'   => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
            'wednesday' => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
            'thursday'  => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
            'friday'    => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
            'saturday'  => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
            'sunday'    => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
        ],
    ];
    public $showPricingModal = false;
    public $pricingEditSportId = null;

    // Payment Policy Customization Fields
    public $advance_payment_required_override = false;
    public $booking_payment_mode_override = ''; // '' (null), 'no_payment', 'advance_only', 'full_only', 'advance_or_full'
    public $advance_payment_type_override = 'percentage'; // 'percentage', 'fixed'
    public $advance_payment_value_override = 20;

    public function getVenueProperty()
    {
        $venueId = $this->complex_id ?: auth()->user()->complex_id;
        return $venueId ? BookingVenue::find($venueId) : null;
    }

    public function getIsOnlinePaymentEnabledProperty()
    {
        $venue = $this->venue;
        return $venue ? filter_var($venue->online_payments_enabled ?? true, FILTER_VALIDATE_BOOLEAN) : true;
    }

    // Capacity / Limited Persons Allowed Limit Per Hour (Pool & Sports)
    public $capacity_limit_enabled = false;
    public $max_persons_per_hour = 10;

    // Private Booking Customization Fields
    public $private_booking_enabled = false;
    public $private_booking_price = '';
    public $private_booking_min_slots = 3;
    public $private_booking_min_duration_minutes = 180;
    public $private_booking_price_multiplier = 1.00;
    public $private_booking_pricing_mode = 'flat_total'; // 'flat_total', 'hourly_flat', 'normal_total', 'normal_multiplier'

    public function mount()
    {
        $this->loadSports();
    }

    /**
     * Load sports for a given venue id. If no venue id provided, use the current user's complex_id.
     */
    public function loadSports($venueId = null)
    {
        // Refresh user data from database to get latest complex_id
        $freshUser = auth()->user()->fresh();
        $this->complex_id = $venueId ?? $freshUser->complex_id;
        
        if ($this->complex_id) {
            $this->sports = BookingSport::where('venue_id', $this->complex_id)->get();
            Log::info("Loaded {$this->sports->count()} sports for venue_id {$this->complex_id}");
        } else {
            $this->sports = collect();
            Log::warning('No complex_id found for user ' . auth()->id());
        }
    }

    public function openModal()
    {
        $this->resetForm();
        $this->dispatch('showModal');
    }

    public function closeModal()
    {
        $this->dispatch('hideModal');
    }

    public function saveSport()
    {
        $this->complex_id = auth()->user()->complex_id;

        $validated = $this->validate([
            'game_name' => 'required|string|in:Football,Cricket,Badminton,Basketball,Pools,Pooltable,Cricket & Football',
            'game_type' => 'required|string',
            'rate_type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'maximum_court' => 'required|integer|min:1',
            'status' => 'required|string|in:Active,Inactive,Maintenance',
            // game_image removed: we use default images based on sport name
            'description' => 'nullable|string',
            'advance_required' => 'boolean',
        ]);
        // Ensure we have a valid BookingVenue id to satisfy the foreign key
        $venueId = $this->complex_id;
        $needsUserUpdate = false;
        
        if (! $venueId || ! BookingVenue::where('id', $venueId)->exists()) {
            // Try to fallback to an existing venue; if none exists, create a minimal one.
            $existing = BookingVenue::first();
            if ($existing) {
                Log::warning("Requested venue_id {$this->complex_id} not found; falling back to venue id {$existing->id}");
                $venueId = $existing->id;
                $needsUserUpdate = true;
            } else {
                $created = BookingVenue::create([
                    'name' => auth()->user()->name . "'s Venue",
                    'address' => 'Auto-generated',
                    'status' => 'Active'
                ]);
                Log::warning("No BookingVenue found; created fallback venue id {$created->id} for user " . auth()->id());
                $venueId = $created->id;
                $needsUserUpdate = true;
            }
        }
        
        // Always update user's complex_id when using a fallback venue
        if ($needsUserUpdate) {
            try {
                $user = auth()->user();
                $user->complex_id = $venueId;
                $user->save();
                // Refresh the authenticated user instance
                auth()->setUser($user->fresh());
                Log::info("Updated user {$user->id} complex_id to {$venueId}");
            } catch (\Exception $e) {
                Log::error("Failed to update user complex_id: " . $e->getMessage());
            }
        }

        // Determine default image for the sport name
        $imagePath = $this->getDefaultImageForName($validated['game_name']);

        $charges = is_array($this->additional_charges) ? $this->additional_charges : [];
        $charges['capacity_limit_enabled'] = (bool) $this->capacity_limit_enabled;
        $charges['max_persons_per_hour'] = $this->capacity_limit_enabled ? (int) $this->max_persons_per_hour : 1;

        $mode = $this->booking_payment_mode_override;
        $isAdvanceMode = in_array($mode, ['advance_only', 'advance_or_full', 'partial']);
        if (empty($mode) || $mode === 'venue_default') {
            $venue = $this->venue;
            if ($venue && (in_array($venue->booking_payment_mode, ['partial', 'advance_only', 'advance_or_full']) || $venue->advance_payment_required)) {
                $isAdvanceMode = true;
            }
        }
        $advanceValue = ($isAdvanceMode && !empty($this->advance_payment_value_override))
            ? (float) $this->advance_payment_value_override
            : null;

        $createdSport = BookingSport::create([
            'venue_id' => $venueId,
            'name' => $validated['game_name'],
            'game_type' => $validated['game_type'],
            'rate_type' => $validated['rate_type'],
            'price' => $validated['price'],
            'maximum_court' => $validated['maximum_court'],
            'status' => $validated['status'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'additional_charges' => json_encode($charges),
            'advance_required' => $isAdvanceMode,
            'average_rating' => 0.0,

            // Payment policy customization
            'advance_payment_required_override' => $isAdvanceMode,
            'booking_payment_mode_override' => !empty($this->booking_payment_mode_override) ? $this->booking_payment_mode_override : null,
            'advance_payment_type_override' => $this->advance_payment_type_override ?: 'percentage',
            'advance_payment_value_override' => $advanceValue,

            // Private booking customization
            'private_booking_price' => (!empty($this->private_booking_price) && $this->private_booking_enabled) ? (float) $this->private_booking_price : null,
            'private_booking_min_duration_minutes' => max(60, ((int) ($this->private_booking_min_slots ?: 3)) * 60),
            'private_booking_price_multiplier' => (float) ($this->private_booking_price_multiplier ?: 1.00),
            'private_booking_pricing_mode' => $this->private_booking_pricing_mode ?: 'flat_total',
        ]);

        // Sync with pools_pool if this sport is Pools
        if (strtolower($validated['game_name']) === 'pools' || strtolower($validated['game_name']) === 'pool') {
            $pool = \DB::table('pools_pool')->where('venue_id', $venueId)->first();
            if ($pool) {
                \DB::table('pools_pool')->where('id', $pool->id)->update([
                    'capacity' => $this->capacity_limit_enabled ? (int) $this->max_persons_per_hour : 1,
                    'private_booking_price' => (!empty($this->private_booking_price) && $this->private_booking_enabled) ? (float) $this->private_booking_price : null,
                    'private_request_enabled' => $this->private_booking_enabled,
                ]);
            }
        }

        session()->flash('message', 'Sport added successfully!');
        // Reload sports for the venue actually used so the UI reflects the new sport
        $this->loadSports($venueId);
        $this->dispatch('hideModal');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'game_name',
            'game_type',
            'rate_type',
            'price',
            'maximum_court',
            'status',
            'description',
            'additional_charges',
            'advance_required',
            'existingImage',
            'editSportId',
            'advance_payment_required_override',
            'booking_payment_mode_override',
            'advance_payment_type_override',
            'advance_payment_value_override',
            'capacity_limit_enabled',
            'max_persons_per_hour',
            'private_booking_enabled',
            'private_booking_price',
            'private_booking_min_slots',
            'private_booking_min_duration_minutes',
            'private_booking_price_multiplier',
            'private_booking_pricing_mode',
        ]);
        $this->game_type = 'Outdoor';
        $this->rate_type = 'Per hour';
        $this->status = 'Active';
        $this->advance_required = false;
        $this->additional_charges = [];
        $this->booking_payment_mode_override = 'full_only';
        $this->advance_payment_type_override = 'percentage';
        $this->advance_payment_value_override = 20;
        $this->capacity_limit_enabled = false;
        $this->max_persons_per_hour = 10;
        $this->private_booking_enabled = false;
        $this->private_booking_min_slots = 3;
        $this->private_booking_min_duration_minutes = 180;
        $this->private_booking_price_multiplier = 1.00;
        $this->private_booking_pricing_mode = 'flat_total';
    }

    public function openPricingModal($sportId)
    {
        $sport = BookingSport::find($sportId);
        if (!$sport) return;
        $this->pricingEditSportId = $sportId;
        $rules = is_array($sport->pricing_rules) ? $sport->pricing_rules : (json_decode($sport->pricing_rules, true) ?? []);

        $defaultSchedule = [
            'monday'    => ['enabled' => true,  'start_time' => '17:00', 'end_time' => '22:00'],
            'tuesday'   => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
            'wednesday' => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
            'thursday'  => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
            'friday'    => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
            'saturday'  => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
            'sunday'    => ['enabled' => false, 'start_time' => '17:00', 'end_time' => '22:00'],
        ];

        $savedSchedule = $rules['peak_hours'] ?? $rules['peak_schedule'] ?? [];
        $normalizedSaved = [];
        foreach ($savedSchedule as $day => $val) {
            $normalizedSaved[$day] = [
                'enabled'    => filter_var($val['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'start_time' => $val['start'] ?? $val['start_time'] ?? '17:00',
                'end_time'   => $val['end'] ?? $val['end_time'] ?? '22:00',
            ];
        }

        $mergedSchedule = array_replace_recursive($defaultSchedule, $normalizedSaved);
        $overrideEnabled = filter_var($rules['override_peak_hours'] ?? $rules['peak_schedule_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $privateBookingEnabled = !empty($sport->private_booking_price) || ($sport->private_booking_pricing_mode === 'normal_total' && $sport->private_booking_price_multiplier > 1);
        $minDurationMinutes = (int) ($sport->private_booking_min_duration_minutes ?: 180);
        $minSlots = max(1, (int) round($minDurationMinutes / 60));

        $this->pricingRules = [
            'mode'                                 => $rules['mode'] ?? 'override',
            'peak_price'                           => $rules['peak_price'] ?? '',
            'offpeak_price'                        => $rules['offpeak_price'] ?? '',
            'weekend_price'                        => $rules['weekend_price'] ?? '',
            'advance_discount'                     => $rules['advance_discount'] ?? '',
            'advance_days'                         => $rules['advance_days'] ?? '',
            'peak_schedule_enabled'                => $overrideEnabled,
            'peak_schedule'                        => $mergedSchedule,
            'private_booking_enabled'              => $privateBookingEnabled,
            'private_booking_price'                => $sport->private_booking_price ?? '',
            'private_booking_min_slots'            => $minSlots,
            'private_booking_pricing_mode'        => $sport->private_booking_pricing_mode ?? 'flat_total',
            'private_booking_price_multiplier'     => $sport->private_booking_price_multiplier ?? 1.00,
        ];

        $this->showPricingModal = true;
    }

    public function closePricingModal()
    {
        $this->showPricingModal = false;
        $this->pricingEditSportId = null;
        $this->pricingRules = [
            'mode'                                 => 'override',
            'peak_price'                           => '',
            'offpeak_price'                        => '',
            'weekend_price'                        => '',
            'advance_discount'                     => '',
            'advance_days'                         => '',
            'peak_schedule_enabled'                => false,
            'peak_schedule'                        => [],
            'private_booking_enabled'              => false,
            'private_booking_price'                => '',
            'private_booking_min_slots'            => 3,
            'private_booking_pricing_mode'        => 'flat_total',
            'private_booking_price_multiplier'     => 1.00,
        ];
    }

    public function savePricingRules()
    {
        $this->validate([
            'pricingRules.peak_price' => 'nullable|numeric|min:0',
            'pricingRules.offpeak_price' => 'nullable|numeric|min:0',
            'pricingRules.weekend_price' => 'nullable|numeric|min:0',
            'pricingRules.advance_discount' => 'nullable|numeric|min:0|max:100',
            'pricingRules.advance_days' => 'nullable|integer|min:1',
            'pricingRules.private_booking_price' => 'nullable|numeric|min:0',
            'pricingRules.private_booking_min_slots' => 'nullable|integer|min:1',
        ]);

        $sport = BookingSport::find($this->pricingEditSportId);
        if ($sport) {
            $overridePeakHours = filter_var($this->pricingRules['peak_schedule_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $peakHoursFormatted = [];

            foreach ($days as $day) {
                $dayData = $this->pricingRules['peak_schedule'][$day] ?? [];
                $peakHoursFormatted[$day] = [
                    'enabled' => filter_var($dayData['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'start'   => $dayData['start_time'] ?? $dayData['start'] ?? '17:00',
                    'end'     => $dayData['end_time'] ?? $dayData['end'] ?? '22:00',
                ];
            }

            $payload = [
                'mode'                  => 'override',
                'override_peak_hours'   => $overridePeakHours,
                'peak_price'            => (string) ($this->pricingRules['peak_price'] ?? ''),
                'offpeak_price'         => (string) ($this->pricingRules['offpeak_price'] ?? ''),
                'weekend_price'         => (string) ($this->pricingRules['weekend_price'] ?? ''),
                'advance_discount'      => (string) ($this->pricingRules['advance_discount'] ?? ''),
                'advance_days'          => (string) ($this->pricingRules['advance_days'] ?? ''),
                'peak_hours'            => $peakHoursFormatted,
                'peak_schedule_enabled' => $overridePeakHours,
                'peak_schedule'         => $this->pricingRules['peak_schedule'] ?? [],
            ];

            $privateEnabled = filter_var($this->pricingRules['private_booking_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $privatePrice = ($privateEnabled && !empty($this->pricingRules['private_booking_price']))
                ? (float) $this->pricingRules['private_booking_price']
                : null;
            $minSlots = (int) ($this->pricingRules['private_booking_min_slots'] ?? 3);
            $minMinutes = max(60, $minSlots * 60);
            $pricingMode = $this->pricingRules['private_booking_pricing_mode'] ?? 'flat_total';
            $priceMultiplier = (float) ($this->pricingRules['private_booking_price_multiplier'] ?? 1.00);

            $sport->update([
                'pricing_rules'                        => $payload,
                'private_booking_price'                => $privatePrice,
                'private_booking_min_duration_minutes' => $minMinutes,
                'private_booking_pricing_mode'        => $pricingMode,
                'private_booking_price_multiplier'     => $priceMultiplier,
            ]);

            // Sync with pools_pool if this sport is Pools
            if (strtolower($sport->name) === 'pools' || strtolower($sport->name) === 'pool') {
                $pool = \DB::table('pools_pool')->where('venue_id', $sport->venue_id)->first();
                if ($pool) {
                    \DB::table('pools_pool')->where('id', $pool->id)->update([
                        'private_booking_price'   => $privatePrice,
                        'private_request_enabled' => $privateEnabled,
                    ]);
                }
            }

            session()->flash('message', 'Pricing rules saved successfully!');
        }
        $this->closePricingModal();
        $this->loadSports();
    }

    // ==========================================
    // Slot Blocking (Sports Page)
    // ==========================================

    public $showSlotBlockModal = false;
    public $slotBlockSportId = null;
    public $slotBlockSportName = '';
    public $slotBlockDate = '';
    public $slotBlockTime = '';
    public $slotBlockEndTime = '';
    public $slotBlockCourt = '1';
    public $slotBlockReason = 'Maintenance';
    public $existingBlocks = [];

    public function openSlotBlockModal($sportId)
    {
        $sport = BookingSport::find($sportId);
        if (!$sport) return;
        $this->slotBlockSportId = $sportId;
        $this->slotBlockSportName = $sport->name;
        $this->slotBlockDate = now()->format('Y-m-d');
        $this->slotBlockTime = '09:00:00';
        $this->slotBlockEndTime = '10:00:00';
        $this->slotBlockCourt = '1';
        $this->slotBlockReason = 'Maintenance';
        $this->loadExistingBlocks($sport);
        $this->showSlotBlockModal = true;
    }

    private function loadExistingBlocks(BookingSport $sport): void
    {
        $raw = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
        $list = [];
        foreach ($raw as $date => $times) {
            foreach ($times as $time => $courts) {
                foreach ($courts as $court => $value) {
                    // Support both old string format and new array format
                    if (is_array($value)) {
                        $reason   = $value['reason'] ?? 'Unavailable';
                        $end_time = $value['end_time'] ?? '';
                    } else {
                        $reason   = $value;
                        $end_time = '';
                    }
                    $list[] = compact('date', 'time', 'end_time', 'court', 'reason');
                }
            }
        }
        // Sort by date desc then time
        usort($list, fn($a, $b) => strcmp($b['date'] . $b['time'], $a['date'] . $a['time']));
        $this->existingBlocks = $list;
    }

    public function closeSlotBlockModal()
    {
        $this->showSlotBlockModal = false;
        $this->slotBlockSportId = null;
        $this->existingBlocks = [];
    }

    public function addSlotBlock()
    {
        $this->validate([
            'slotBlockDate'    => 'required|date',
            'slotBlockTime'    => 'required',
            'slotBlockEndTime' => 'required',
            'slotBlockCourt'   => 'required',
            'slotBlockReason'  => 'required|string',
        ]);

        $sport = BookingSport::find($this->slotBlockSportId);
        if (!$sport) return;

        $slots    = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
        $time     = strlen($this->slotBlockTime) === 5 ? $this->slotBlockTime . ':00' : $this->slotBlockTime;
        $endTime  = strlen($this->slotBlockEndTime) === 5 ? $this->slotBlockEndTime . ':00' : $this->slotBlockEndTime;

        $slots[$this->slotBlockDate][$time][$this->slotBlockCourt] = [
            'reason'   => $this->slotBlockReason,
            'end_time' => $endTime,
        ];
        $sport->update(['blocked_slots' => $slots]);

        $this->loadExistingBlocks($sport->fresh());
        session()->flash('message', 'Slot blocked successfully.');
    }

    public function removeSlotBlock($date, $time, $court)
    {
        $sport = BookingSport::find($this->slotBlockSportId);
        if (!$sport) return;

        $slots = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
        unset($slots[$date][$time][$court]);
        if (empty($slots[$date][$time])) unset($slots[$date][$time]);
        if (empty($slots[$date]))        unset($slots[$date]);
        $sport->update(['blocked_slots' => $slots]);

        $this->loadExistingBlocks($sport->fresh());
        session()->flash('message', 'Block removed.');
    }

    public function render()
    {
        return view('livewire.staff.sports-management');
    }

    public function editSport($id)
    {
        $sport = BookingSport::findOrFail($id);

        $this->editSportId = $sport->id;
        $this->complex_id = $sport->venue_id;
        $this->game_name = $sport->name;
        $this->game_type = $sport->game_type;
        $this->rate_type = $sport->rate_type;
        $this->price = $sport->price;
        $this->maximum_court = $sport->maximum_court;
        $this->status = $sport->status;
        $this->description = $sport->description;
        $this->advance_required = $sport->advance_required;
        $this->additional_charges = is_array($sport->additional_charges) ? $sport->additional_charges : (json_decode($sport->additional_charges, true) ?? []);
        $this->existingImage = $sport->image;

        // Customization fields
        $this->advance_payment_required_override = (bool) $sport->advance_payment_required_override;
        $rawMode = $sport->booking_payment_mode_override;
        if (in_array($rawMode, ['pay_at_venue', 'no_payment'])) {
            $this->booking_payment_mode_override = 'no_payment';
        } elseif (in_array($rawMode, ['partial', 'advance_only'])) {
            $this->booking_payment_mode_override = 'advance_only';
        } elseif (in_array($rawMode, ['full', 'full_only'])) {
            $this->booking_payment_mode_override = 'full_only';
        } elseif ($rawMode === 'advance_or_full') {
            $this->booking_payment_mode_override = 'advance_or_full';
        } else {
            $this->booking_payment_mode_override = '';
        }
        $this->advance_payment_type_override = $sport->advance_payment_type_override ?? 'percentage';
        $this->advance_payment_value_override = $sport->advance_payment_value_override ?? 20;

        $this->max_persons_per_hour = $this->additional_charges['max_persons_per_hour'] ?? 10;
        $this->capacity_limit_enabled = isset($this->additional_charges['capacity_limit_enabled']) 
            ? (bool) $this->additional_charges['capacity_limit_enabled'] 
            : ($this->max_persons_per_hour > 1 || strtolower($sport->name) === 'pools' || strtolower($sport->name) === 'pool');

        $this->private_booking_price = $sport->private_booking_price;
        $this->private_booking_min_duration_minutes = $sport->private_booking_min_duration_minutes ?? 180;
        $this->private_booking_min_slots = max(1, (int) round(($this->private_booking_min_duration_minutes) / 60));
        $this->private_booking_price_multiplier = $sport->private_booking_price_multiplier ?? 1.00;
        $this->private_booking_pricing_mode = $sport->private_booking_pricing_mode ?? 'flat_total';
        $this->private_booking_enabled = !empty($sport->private_booking_price) || ($sport->private_booking_pricing_mode === 'normal_total' && $sport->private_booking_price_multiplier > 1);

        $this->dispatch('showEditSportModal');
    }

    public function updateSport()
    {
        $validated = $this->validate([
            'game_type' => 'required|string',
            'rate_type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'maximum_court' => 'required|integer|min:1',
            'status' => 'required|string|in:Active,Inactive,Maintenance',
            'description' => 'nullable|string',
            'advance_required' => 'boolean'
        ]);

        $sport = BookingSport::findOrFail($this->editSportId);

        // Keep existing image if present, otherwise pick a default based on name
        $imagePath = $sport->image ?? $this->getDefaultImageForName($this->game_name ?? $sport->name);

        // Ensure venue id is valid before updating to avoid FK errors
        $venueId = $this->complex_id ?: $sport->venue_id;
        if (! $venueId || ! BookingVenue::where('id', $venueId)->exists()) {
            $fallback = BookingVenue::first();
            $venueId = $fallback ? $fallback->id : $sport->venue_id;
            Log::warning("Invalid venue for update; using venue id {$venueId}");
        }

        $charges = is_array($this->additional_charges) ? $this->additional_charges : [];
        $charges['capacity_limit_enabled'] = (bool) $this->capacity_limit_enabled;
        $charges['max_persons_per_hour'] = $this->capacity_limit_enabled ? (int) $this->max_persons_per_hour : 1;

        $mode = $this->booking_payment_mode_override;
        $isAdvanceMode = in_array($mode, ['advance_only', 'advance_or_full', 'partial']);
        if (empty($mode) || $mode === 'venue_default') {
            $venue = $this->venue;
            if ($venue && (in_array($venue->booking_payment_mode, ['partial', 'advance_only', 'advance_or_full']) || $venue->advance_payment_required)) {
                $isAdvanceMode = true;
            }
        }
        $advanceValue = ($isAdvanceMode && !empty($this->advance_payment_value_override))
            ? (float) $this->advance_payment_value_override
            : null;

        $sport->update([
            'venue_id' => $venueId,
            'name' => $this->game_name,
            'game_type' => $validated['game_type'],
            'rate_type' => $validated['rate_type'],
            'price' => $validated['price'],
            'maximum_court' => $validated['maximum_court'],
            'status' => $validated['status'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'additional_charges' => json_encode($charges),
            'advance_required' => $isAdvanceMode,

            // Payment policy customization
            'advance_payment_required_override' => $isAdvanceMode,
            'booking_payment_mode_override' => !empty($this->booking_payment_mode_override) ? $this->booking_payment_mode_override : null,
            'advance_payment_type_override' => $this->advance_payment_type_override ?: 'percentage',
            'advance_payment_value_override' => $advanceValue,

            // Private booking customization
            'private_booking_price' => (!empty($this->private_booking_price) && $this->private_booking_enabled) ? (float) $this->private_booking_price : null,
            'private_booking_min_duration_minutes' => max(60, ((int) ($this->private_booking_min_slots ?: 3)) * 60),
            'private_booking_price_multiplier' => (float) ($this->private_booking_price_multiplier ?: 1.00),
            'private_booking_pricing_mode' => $this->private_booking_pricing_mode ?: 'flat_total',
        ]);

        // Sync with pools_pool if this sport is Pools
        if (strtolower($this->game_name) === 'pools' || strtolower($this->game_name) === 'pool') {
            $pool = \DB::table('pools_pool')->where('venue_id', $venueId)->first();
            if ($pool) {
                \DB::table('pools_pool')->where('id', $pool->id)->update([
                    'capacity' => $this->capacity_limit_enabled ? (int) $this->max_persons_per_hour : 1,
                    'private_booking_price' => (!empty($this->private_booking_price) && $this->private_booking_enabled) ? (float) $this->private_booking_price : null,
                    'private_request_enabled' => $this->private_booking_enabled,
                ]);
            }
        }

        session()->flash('message', 'Sport updated successfully.');
        // Reload sports for the venue actually used so the UI reflects updates
        $this->loadSports($venueId);
        $this->dispatch('hideEditSportModal');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->editSportId = $id;
        $this->dispatch('showConfirmation', id: $id);
    }

    #[On('deleteConfirmed')]
    public function deleteConfirmed($id)
    {
        try {
            $sport = BookingSport::findOrFail($id);
            $venueId = $sport->venue_id;
            $sport->delete();

            session()->flash('message', 'Sport deleted successfully.');
            $this->dispatch('sportDeleted');
            // Reload sports for the same venue
            $this->loadSports($venueId);
            $this->dispatch('hideEditSportModal');
        } catch (\Exception $e) {
            $this->dispatch('deleteError', message: 'Error deleting sport: ' . $e->getMessage());
        }
    }

    public function deleteSport($id)
    {
        $this->confirmDelete($id);
    }

    public function game_url()
    {
        return $this->game_image;
    }

    // Determine a default image URL for a given sport/game name. Falls back to default.jpg.
    protected function getDefaultImageForName($name)
    {
        $n = strtolower((string) $name);

        if (str_contains($n, 'cricket') && str_contains($n, 'football')) {
            return asset('images/sports_images/cricket&football.jpg');
        }
        if (str_contains($n, 'football')) {
            return asset('images/sports_images/football.jpg');
        }
        if (str_contains($n, 'cricket')) {
            return asset('images/sports_images/cricket.jpg');
        }
        if (str_contains($n, 'badminton')) {
            return asset('images/sports_images/badminton.jpg');
        }
        if (str_contains($n, 'basketball')) {
            return asset('images/sports_images/basketball.jpeg');
        }
        if (str_contains($n, 'pooltable') || str_contains($n, 'pool table')) {
            return asset('images/sports_images/pooltable.jpg');
        }
        if (str_contains($n, 'pool') || str_contains($n, 'swim')) {
            return asset('images/sports_images/pools.jpg');
        }

        return asset('images/sports_images/default.jpg');
    }
}
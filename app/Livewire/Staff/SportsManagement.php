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
    ];
    public $showPricingModal = false;
    public $pricingEditSportId = null;

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

        BookingSport::create([
            'venue_id' => $venueId,
            'name' => $validated['game_name'],
            'game_type' => $validated['game_type'],
            'rate_type' => $validated['rate_type'],
            'price' => $validated['price'],
            'maximum_court' => $validated['maximum_court'],
            'status' => $validated['status'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'additional_charges' => json_encode($this->additional_charges),
            'advance_required' => $validated['advance_required'],
            'average_rating' => 0.0,
        ]);

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
            //'game_image', // no upload
            'description',
            'additional_charges',
            'advance_required',
            'existingImage',
            'editSportId'
        ]);
        $this->game_type = 'Outdoor';
        $this->rate_type = 'Per hour';
        $this->status = 'Active';
        $this->advance_required = false;
        $this->additional_charges = [];
    }

    public function openPricingModal($sportId)
    {
        $sport = BookingSport::find($sportId);
        if (!$sport) return;
        $this->pricingEditSportId = $sportId;
        $rules = $sport->pricing_rules ?? [];
        $this->pricingRules = array_merge([
            'peak_price' => '',
            'offpeak_price' => '',
            'weekend_price' => '',
            'advance_discount' => '',
            'advance_days' => '',
        ], $rules);
        $this->showPricingModal = true;
    }

    public function closePricingModal()
    {
        $this->showPricingModal = false;
        $this->pricingEditSportId = null;
        $this->pricingRules = ['peak_price'=>'','offpeak_price'=>'','weekend_price'=>'','advance_discount'=>'','advance_days'=>''];
    }

    public function savePricingRules()
    {
        $this->validate([
            'pricingRules.peak_price' => 'nullable|numeric|min:0',
            'pricingRules.offpeak_price' => 'nullable|numeric|min:0',
            'pricingRules.weekend_price' => 'nullable|numeric|min:0',
            'pricingRules.advance_discount' => 'nullable|numeric|min:0|max:100',
            'pricingRules.advance_days' => 'nullable|integer|min:1',
        ]);

        $sport = BookingSport::find($this->pricingEditSportId);
        if ($sport) {
            $sport->update(['pricing_rules' => array_filter($this->pricingRules, fn($v) => $v !== '')]);
            session()->flash('message', 'Pricing rules saved!');
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
        $this->additional_charges = json_decode($sport->additional_charges, true) ?? [];
        $this->existingImage = $sport->image;

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
            // No image upload allowed/required; keep existing or use default
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

        $sport->update([
            'venue_id' => $venueId,
            'name' => $this->game_name,
            'game_type' => $validated['game_type'],
            'rate_type' => $validated['rate_type'],
            'price' => $validated['price'],
            'maximum_court' => $validated['maximum_court'],
            'status' => $validated['status'],
            'image' => $imagePath, // Store full URL
            'description' => $validated['description'] ?? null,
            'additional_charges' => json_encode($this->additional_charges),
            'advance_required' => $validated['advance_required']
        ]);

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
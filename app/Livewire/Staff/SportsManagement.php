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
            'game_name' => 'required|string|in:Cricket,Badminton,Pools,Pooltable',
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
        if (str_contains($n, 'cricket')) {
            return asset('images/sports_images/cricket.jpg');
        }
        if (str_contains($n, 'badminton')) {
            return asset('images/sports_images/badminton.jpg');
        }
        if (str_contains($n, 'pool') || str_contains($n, 'swim')) {
            // pooltable vs pool
            if (str_contains($n, 'table') || str_contains($n, 'pooltable')) {
                return asset('images/sports_images/pooltable.jpg');
            }
            return asset('images/sports_images/pools.jpg');
        }

        // Generic fallback
        return asset('images/sports_images/default.jpg');
    }
}
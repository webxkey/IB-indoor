<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\BookingSport;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

#[Title("Sports Management")]
#[Layout("components.layouts.staff")]
class SportsManagement extends Component
{
    use WithFileUploads;

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

    public function loadSports()
    {
        $this->complex_id = auth()->user()->complex_id;
        $this->sports = BookingSport::where('venue_id', $this->complex_id)->get();
        Log::info('Sports image paths: ' . $this->sports->pluck('game_image')->toJson());
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
            'game_name' => 'required|string|max:255',
            'game_type' => 'required|string',
            'rate_type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'maximum_court' => 'required|integer|min:1',
            'status' => 'required|string|in:Active,Inactive,Maintenance',
            'game_image' => 'required|image|max:1024',
            'description' => 'nullable|string',
            'advance_required' => 'boolean',
        ]);

        // Store the file and get the path
        $imagePath = null;
        if ($this->game_image) {
            // Store file and get relative path (e.g., 'sports/filename.jpg')
            $relativePath = $this->game_image->store('sports', 'public');
            // Convert to full URL
            $imagePath = asset('storage/' . $relativePath);
        }

        BookingSport::create([
            'venue_id' => $this->complex_id,
            'name' => $validated['game_name'],
            'game_type' => $validated['game_type'],
            'rate_type' => $validated['rate_type'],
            'price' => $validated['price'],
            'maximum_court' => $validated['maximum_court'],
            'status' => $validated['status'],
            'image' => $imagePath, // Store full URL
            'description' => $validated['description'] ?? null,
            'additional_charges' => json_encode($this->additional_charges),
            'advance_required' => $validated['advance_required'],
            'average_rating' => 0.0,
        ]);

        session()->flash('message', 'Sport added successfully!');
        $this->loadSports();
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
            'game_image',
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
            'game_name' => 'required|string|max:255',
            'game_type' => 'required|string',
            'rate_type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'maximum_court' => 'required|integer|min:1',
            'status' => 'required|string|in:Active,Inactive,Maintenance',
            'game_image' => 'nullable|image|max:1024',
            'description' => 'nullable|string',
            'advance_required' => 'boolean'
        ]);

        $sport = BookingSport::findOrFail($this->editSportId);

        $imagePath = $this->existingImage; // Keep existing by default

        if ($this->game_image) {
            // Delete old image if it exists
            if ($sport->image) {
                // Extract relative path from full URL
                $oldPath = str_replace(asset('storage/'), '', $sport->image);
                if (Storage::exists('public/' . $oldPath)) {
                    Storage::delete('public/' . $oldPath);
                }
            }
            
            // Store new image and convert to full URL
            $relativePath = $this->game_image->store('sports', 'public');
            $imagePath = asset('storage/' . $relativePath);
        }

        $sport->update([
            'venue_id' => $this->complex_id,
            'name' => $validated['game_name'],
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
        $this->loadSports();
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

            if ($sport->image) {
                // Extract relative path from full URL
                $relativePath = str_replace(asset('storage/'), '', $sport->image);
                if (Storage::exists('public/' . $relativePath)) {
                    Storage::delete('public/' . $relativePath);
                }
            }

            $sport->delete();

            session()->flash('message', 'Sport deleted successfully.');
            $this->dispatch('sportDeleted');
            $this->loadSports();
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
}
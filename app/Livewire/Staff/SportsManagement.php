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
            'game_image' => 'nullable|image|max:1024',
            'description' => 'nullable|string',
            'advance_required' => 'boolean'
        ]);

        if ($this->game_image) {
            $validated['game_image'] = $this->game_image->store('sports', 'public');
        }

        bookingSport::create([
            'venue_id' => $this->complex_id,
            'name' => $validated['game_name'],
            'game_type' => $validated['game_type'],
            'rate_type' => $validated['rate_type'],
            'price' => $validated['price'],
            'maximum_court' => $validated['maximum_court'],
            'status' => $validated['status'],
            'image' => $validated['game_image'] ?? null,
            'description' => $validated['description'] ?? null,
            'additional_charges' => json_encode($this->additional_charges),
            'advance_required' => $validated['advance_required'],
            'average_rating' => 0.0, // Default value
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

        if ($this->game_image) {
            if ($sport->image && Storage::exists('public/' . $sport->image)) {
                Storage::delete('public/' . $sport->image);
            }
            $validated['game_image'] = $this->game_image->store('sports', 'public');
        } else {
            $validated['game_image'] = $this->existingImage;
        }

        $sport->update([
            'venue_id' => $this->complex_id,
            'name' => $validated['game_name'],
            'game_type' => $validated['game_type'],
            'rate_type' => $validated['rate_type'],
            'price' => $validated['price'],
            'maximum_court' => $validated['maximum_court'],
            'status' => $validated['status'],
            'image' => $validated['game_image'],
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

            if ($sport->image && Storage::exists('public/' . $sport->image)) {
                Storage::delete('public/' . $sport->image);
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
        return asset('storage/' . $this->game_image);
    }
}
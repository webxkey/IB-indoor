<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\BookingVenue;
use App\Models\BookingSport;
use App\Models\BookingGalleryImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
#[Title('Indoors')]
class Indoors extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public $locationFilter = '';
    public $capacityFilter = '';
    public $viewMode = 'grid';

    public $addModalVisible = false;
    public $showViewModalVisible = false;
    public $showEditModalVisible = false;
    public $viewVenue = null;
    public $editingVenue = null;

    // Form properties
    public $name;
    public $address;
    public $county;
    public $location;
    public $postal_code;
    public $contact_number;
    public $email_address;
    public $website;
    public $status = 'active';
    public $opening_hours = [];
    public $amenities = [];
    public $cover_image;
    public $gallery_images = [];
    public $description;
    public $sport_type;
    public $capacity;
    public $hourly_rate;
    public $length;
    public $width;
    public $terms = false;

    public $availableStatuses = ['active', 'inactive', 'maintenance', 'new'];
    public $availableCounties = [];
    public $availableAmenities = [
        'parking',
        'locker_rooms',
        'showers',
        'cafeteria',
        'wifi',
        'equipment_rental',
        'air_conditioning',
        'heating',
        'bleachers',
        'scoreboard',
        'lighting',
        'sound_system'
    ];
    public $availableSportTypes = [
        'football',
        'basketball',
        'badminton',
        'tennis',
        'volleyball',
        'swimming',
        'gym',
        'other'
    ];

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'address' => 'required|min:5|max:500',
        'county' => 'required',
        'location' => 'required',
        'postal_code' => 'nullable|max:20',
        'contact_number' => 'required|max:20',
        'email_address' => 'nullable|email',
        'website' => 'nullable|url',
        'status' => 'required|in:active,inactive,maintenance,new',
        'opening_hours' => 'required|array',
        'amenities' => 'array',
        'cover_image' => 'nullable|image|max:2048',
        'gallery_images.*' => 'nullable|image|max:2048',
        'description' => 'required|min:10|max:1000',
        'sport_type' => 'required',
        'capacity' => 'integer|min:1',
        'hourly_rate' => 'required|numeric|min:0',
        'length' => 'required|numeric|min:1',
        'width' => 'required|numeric|min:1',
        'terms' => 'accepted'
    ];

    public function mount()
    {
        $this->availableCounties = BookingVenue::distinct('county')
            ->whereNotNull('county')
            ->pluck('county')
            ->toArray();

        // Initialize opening hours with proper structure
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            $this->opening_hours[$day] = ['open' => '09:00', 'close' => '17:00', 'closed' => false];
        }
    }

    public function render()
    {

        $query = BookingVenue::withCount(['sports as bookings_count', 'reviews as reviews_count'])
            ->withAvg('reviews', 'rating');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%')
                    ->orWhere('location', 'like', '%' . $this->search . '%')
                    ->orWhere('county', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->locationFilter) {
            $query->where('county', $this->locationFilter);
        }

        if ($this->capacityFilter) {
            $query->where('capacity', '<=', $this->capacityFilter);
        }

        $venues = $query->orderBy('name')->paginate(12);

        $stats = [
            'active' => BookingVenue::where('status', 'active')->count(),
            'inactive' => BookingVenue::where('status', 'inactive')->count(),
            'maintenance' => BookingVenue::where('status', 'maintenance')->count(),
            'new' => BookingVenue::where('status', 'new')->count(),
            'total' => BookingVenue::count(),
        ];

        return view('livewire.admin.indoors', [
            'venues' => $venues,
            'stats' => $stats
        ]);
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->locationFilter = '';
        $this->capacityFilter = '';
        $this->resetPage();
    }

    public function toggleViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function showAddModal()
    {
        $this->resetForm();
        $this->addModalVisible = true;
        $this->dispatch('showAddModal');
    }

    public function showViewModal($id)
    {
        $this->viewVenue = BookingVenue::with('sports')->findOrFail($id);
        $this->showViewModalVisible = true;
        $this->dispatch('showViewModal');
    }


    // In Indoors.php, update showEditModal to handle default opening hours if not set

    public function showEditModal($id)
    {
        $this->resetForm();
        $this->editingVenue = BookingVenue::with('sports')->findOrFail($id);
        $venue = $this->editingVenue;

        $this->name = $venue->name;
        $this->address = $venue->address;
        $this->county = $venue->county;
        $this->location = $venue->location;
        $this->postal_code = $venue->postal_code;
        $this->contact_number = $venue->contact_number;
        $this->email_address = $venue->email_address;
        $this->website = $venue->website;
        $this->status = $venue->status ?? 'active';
        $this->amenities = $venue->amenities ?? [];
        $this->description = $venue->description;
        $this->capacity = $venue->capacity;
        $this->hourly_rate = $venue->hourly_rate;

        // Extract dimensions if stored as a single field
        if ($venue->dimensions) {
            $dimensions = explode('x', $venue->dimensions);
            if (count($dimensions) === 2) {
                $this->length = trim($dimensions[0]);
                $this->width = trim($dimensions[1]);
            }
        } else {
            $this->length = null;
            $this->width = null;
        }

        $this->sport_type = $venue->primary_sport_type;

        // Handle opening hours - ensure proper structure
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        if (!empty($venue->opening_hours) && is_array($venue->opening_hours)) {
            foreach ($days as $day) {
                // Check if the day exists in the stored data and has the correct structure
                if (isset($venue->opening_hours[$day]) && is_array($venue->opening_hours[$day])) {
                    $this->opening_hours[$day] = [
                        'open' => $venue->opening_hours[$day]['open'] ?? '09:00',
                        'close' => $venue->opening_hours[$day]['close'] ?? '17:00',
                        'closed' => $venue->opening_hours[$day]['closed'] ?? false
                    ];
                } else {
                    // Set default values if day data is missing or malformed
                    $this->opening_hours[$day] = ['open' => '09:00', 'close' => '17:00', 'closed' => false];
                }
            }
        } else {
            // Set default opening hours if none exist
            foreach ($days as $day) {
                $this->opening_hours[$day] = ['open' => '09:00', 'close' => '17:00', 'closed' => false];
            }
        }

        $this->showEditModalVisible = true;
        $this->dispatch('showEditModal');
    }
    public function setAllHours()
    {
        // Get the Monday hours as the template
        $mondayHours = $this->opening_hours['monday'] ?? ['open' => '09:00', 'close' => '17:00', 'closed' => false];

        $days = ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            $this->opening_hours[$day] = $mondayHours;
        }
    }
    public function saveVenue()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'county' => $this->county,
            'location' => $this->location,
            'postal_code' => $this->postal_code,
            'contact_number' => $this->contact_number,
            'email_address' => $this->email_address,
            'website' => $this->website,
            'status' => $this->status,
            'opening_hours' => $this->opening_hours,
            'amenities' => $this->amenities,
            'description' => $this->description,
            // 'capacity' => $this->capacity,
            // 'hourly_rate' => $this->hourly_rate,
            // 'dimensions' => $this->length . 'x' . $this->width,
            // 'primary_sport_type' => $this->sport_type,
        ];

        if ($this->cover_image) {
            $filename = 'cover_' . time() . '.' . $this->cover_image->getClientOriginalExtension();
            $path = $this->cover_image->storeAs('venues', $filename, 'public');
            $data['cover_image'] = $path ?? null;

            if ($this->editingVenue && $this->editingVenue->cover_image) {
                Storage::disk('public')->delete($this->editingVenue->cover_image);
            }
        }

        if ($this->editingVenue) {
            // Update existing venue
            $this->editingVenue->update($data);
            $venue = $this->editingVenue;
            session()->flash('message', 'Venue updated successfully.');
        } else {
            // Add a default rating for new venues
            $data['rating'] = 0;
            $data['reviews'] = 0;

            // Create new venue
            $venue = BookingVenue::create($data);

            // Also create a sport entry
            BookingSport::create([
                'name' => $this->name,
                'price' => $this->hourly_rate,
                'available' => $this->status === 'active',
                'game_type' => $this->sport_type ?? 'other',
                'venue_id' => $venue->id,
                'description' => $this->description,
                'image_url' => $data['cover_image'] ?? null // Use venue's cover image or null
            ]);

            session()->flash('message', 'Venue created successfully.');
        }

        if ($this->gallery_images) {
            foreach ($this->gallery_images as $img) {
                $filename = 'gallery_' . time() . '_' . Str::random(5) . '.' . $img->getClientOriginalExtension();
                $path = $img->storeAs('venues/gallery', $filename, 'public');

                // Create gallery image record with the correct venue_id
                BookingGalleryImage::create([
                    'image_url' => $path ?? null,
                    'venue_id' => $venue->id
                ]);
            }
        }

        $this->closeModals();
        $this->resetForm();
    }

    public function updateStatus($id, $status)
    {
        $venue = BookingVenue::findOrFail($id);
        $venue->update(['status' => $status]);

        session()->flash('message', "Venue status updated to {$status}.");
    }

    // In Indoors.php, update the deleteVenue method to properly handle relations

    public function deleteVenue($id)
    {
        $venue = BookingVenue::findOrFail($id);

        // Delete cover image
        if ($venue->cover_image) {
            Storage::disk('public')->delete($venue->cover_image);
        }

        // Delete gallery images and records
        foreach ($venue->gallery as $galleryImage) {
            if ($galleryImage->image_url) {
                Storage::disk('public')->delete($galleryImage->image_url);
            }
            $galleryImage->delete();
        }

        // Delete associated sports
        $venue->sports()->delete();

        // Delete associated reviews
        $venue->reviews()->delete();

        // Delete the venue
        $venue->delete();

        session()->flash('message', "Venue '{$venue->name}' deleted successfully.");
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'address', 'county', 'location', 'postal_code', 
            'contact_number', 'email_address', 'website', 'status', 
            'opening_hours', 'amenities', 'cover_image', 'gallery_images', 
            'description', 'sport_type', 'capacity', 'hourly_rate', 
            'length', 'width', 'terms', 'editingVenue'
        ]);
        
        $this->resetErrorBag();
        
        // Reset opening hours with proper structure
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            $this->opening_hours[$day] = ['open' => '09:00', 'close' => '17:00', 'closed' => false];
        }
    }

    public function closeModals()
    {
        $this->addModalVisible = false;
        $this->showViewModalVisible = false;
        $this->showEditModalVisible = false;
        $this->resetForm();
        $this->dispatch('closeModals');
    }
    public function exportData()
    {


        dd('Export functionality is not implemented yet.');
        // Placeholder for export functionality
    }
}

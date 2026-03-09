<?php

namespace App\Livewire\Staff;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\BookingVenue;

#[Title("Staff Dashboard")]
#[Layout("components.layouts.staff")]
class StaffSetting extends Component
{
    use WithFileUploads;

    public $complex_id;
    public $complexes;
    public $isEditModalOpen = false;

    public $complex_name;
    public $complex_type;
    public $status;
    public $cover_image;
    public $existing_cover_image;
    public $email_address;
    public $contact_number;
    public $website;
    public $county;
    public $location;
    public $address;
    public $postal_code;
    public $opening_hours = [
        // will be normalized from DB as lowercase keys with structure ['open' => '09:00','close'=>'17:00','closed'=>false]
    ];
    public $showOpeningHoursEditor = false;
    public $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
    public $amenities = [];
    public $new_amenity = '';
    public $video_tour_url;
    public $gallery_images = [];
    public $existing_gallery_images = [];
    public $description;
    public $terms;
    public $social_links = [];
    public $new_social_platform = '';
    public $new_social_url = '';
    public $activeSection = 'profile';
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public function mount()
    {
        try {
            // Refresh user data to get latest complex_id
            $freshUser = Auth::user()->fresh();
            $this->complex_id = $freshUser->complex_id;
            
            if (!$this->complex_id) {
                session()->flash('error', 'No complex assigned to your account. Please contact administrator.');
                // Initialize with empty defaults to prevent null errors
                $this->opening_hours = $this->getDefaultOpeningHours();
                return;
            }
            
            $this->complexes = BookingVenue::find($this->complex_id);

            if (!$this->complexes) {
                session()->flash('error', 'Complex not found.');
                // Initialize with empty defaults to prevent null errors
                $this->opening_hours = $this->getDefaultOpeningHours();
                return;
            }

            $this->loadComplexData();

            if (request()->has('section')) {
                $this->activeSection = request('section');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading complex data: ' . $e->getMessage());
            Log::error('StaffSetting mount error: ' . $e->getMessage());
            // Initialize with empty defaults to prevent null errors
            $this->opening_hours = $this->getDefaultOpeningHours();
        }
    }

    /**
     * Get default opening hours structure to prevent null errors
     */
    private function getDefaultOpeningHours()
    {
        $result = [];
        foreach ($this->days as $day) {
            $result[$day] = ['open' => null, 'close' => null, 'closed' => false];
        }
        return $result;
    }

    protected function loadComplexData()
    {
        $this->complex_name = $this->complexes->name; // <-- use 'name' from model
        $this->complex_type = $this->complexes->complex_type;
        $this->status = $this->complexes->status;
        $this->existing_cover_image = $this->complexes->cover_image;
        $this->email_address = $this->complexes->email_address;
        $this->contact_number = $this->complexes->contact_number;
        $this->website = $this->complexes->website;
        $this->county = $this->complexes->county;
        $this->location = $this->complexes->location;
        $this->address = $this->complexes->address;
        $this->postal_code = $this->complexes->postal_code;

        // Load opening hours and normalize to canonical structure
        $dbHours = $this->complexes->opening_hours;
        if (is_string($dbHours)) {
            $decodedHours = json_decode($dbHours, true) ?: [];
        } else {
            $decodedHours = $dbHours ?: [];
        }
        $parsedHours = $this->parseOpeningHoursFromDb($decodedHours);
        // Ensure opening_hours is always an array, never null
        $this->opening_hours = is_array($parsedHours) ? $parsedHours : $this->getDefaultOpeningHours();

        $dbAmenities = $this->complexes->amenities;
        if (is_string($dbAmenities)) {
            $this->amenities = json_decode($dbAmenities, true) ?: [];
        } else {
            $this->amenities = $dbAmenities ?: [];
        }

        $this->video_tour_url = $this->complexes->video_tour_url;

        // Use gallery_images_json for gallery images
        $dbGalleryImages = $this->complexes->gallery_images_json ?? $this->complexes->gallery_images ?? [];
        if (is_string($dbGalleryImages)) {
            $this->existing_gallery_images = json_decode($dbGalleryImages, true) ?: [];
        } else {
            $this->existing_gallery_images = $dbGalleryImages ?: [];
        }

        $this->description = $this->complexes->description;
        $this->terms = $this->complexes->terms;

        $dbSocialLinks = $this->complexes->social_links;
        if (is_string($dbSocialLinks)) {
            $this->social_links = json_decode($dbSocialLinks, true) ?: [];
        } else {
            $this->social_links = $dbSocialLinks ?: [];
        }
    }

    public function addAmenity()
    {
        $this->validate([
            'new_amenity' => 'required|string|max:255'
        ]);

        if (!in_array($this->new_amenity, $this->amenities)) {
            $this->amenities[] = $this->new_amenity;
            $this->new_amenity = '';
            session()->flash('message', 'Amenity added successfully!');
        } else {
            session()->flash('error', 'This amenity already exists.');
        }
    }

    public function removeAmenity($index)
    {
        if (isset($this->amenities[$index])) {
            unset($this->amenities[$index]);
            $this->amenities = array_values($this->amenities);
            session()->flash('message', 'Amenity removed successfully!');
        }
    }

    public function showSection($section)
    {
        $this->activeSection = $section;
    }

    protected $listeners = ['showNotificationsSection' => 'activateNotifications'];

    public function activateNotifications()
    {
        $this->activeSection = 'notifications';
    }

    public function openEditModal()
    {
        $this->loadComplexData();
        $this->isEditModalOpen = true;
    }

    /**
     * Toggle inline opening hours editor visibility
     */
    public function toggleOpeningHoursEditor()
    {
        $this->showOpeningHoursEditor = ! $this->showOpeningHoursEditor;
        // Ensure we have fresh data when opening editor
        if ($this->showOpeningHoursEditor) {
            $this->loadComplexData();
        }
    }

    public function closeEditModal()
    {
        $this->isEditModalOpen = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function saveChanges()
    {
        try {
            // Validate all fields
            $validated = $this->validate([
                'complex_name' => 'required|string|max:255',
                'complex_type' => 'required|string|max:255',
                'status' => 'required|string|in:Active,Inactive',
                'cover_image' => 'nullable|image|max:2048',
                'email_address' => 'required|email|max:255',
                'contact_number' => 'required|string|max:20',
                'website' => 'nullable|url|max:255',
                'county' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'postal_code' => 'required|string|max:20',
                // opening_hours validated as structured array: opening_hours.{day}.open/close/closed
                'opening_hours.*.open' => 'nullable|date_format:H:i',
                'opening_hours.*.close' => 'nullable|date_format:H:i',
                'opening_hours.*.closed' => 'nullable|boolean',
                'amenities' => 'nullable|array',
                'video_tour_url' => 'nullable|url|max:255',
                'description' => 'nullable|string|max:5000',
                'terms' => 'nullable|string|max:5000',
            ]);
            // dd($validated);
            $complex = BookingVenue::find($this->complex_id);
            // dd($complex);

            if (!$complex) {
                session()->flash('error', 'Complex not found.');
                return;
            }

            // Handle cover image upload
            $coverImagePath = $this->existing_cover_image;
            if ($this->cover_image) {
                // Delete old image if exists
                if ($complex->cover_image && Storage::exists('public/' . $complex->cover_image)) {
                    Storage::delete('public/' . $complex->cover_image);
                }
                // Store new image
                $coverImagePath = $this->cover_image->store('complex', 'public');
            }
            $imagePath = asset('storage/' . $coverImagePath);

            // Prepare update array
            $updateArray = [
                'name' => $validated['complex_name'], // <-- use 'name' for DB
                'complex_type' => $validated['complex_type'],
                'status' => $validated['status'],
                'image_url' => $imagePath,
                'cover_image' => $coverImagePath,
                'email_address' => $validated['email_address'],
                'contact_number' => $validated['contact_number'],
                'website' => $this->website,
                'county' => $validated['county'],
                'location' => $validated['location'],
                'address' => $validated['address'],
                'postal_code' => $validated['postal_code'],
                'opening_hours' => $this->opening_hours,
                'amenities' => $this->amenities,
                'video_tour_url' => $this->video_tour_url,
                'gallery_images_json' => $this->existing_gallery_images,
                'description' => $this->description,
                'terms' => $this->terms,
                'social_links' => $this->social_links,
            ];

            // Debug log
            Log::debug('StaffSetting saveChanges update array:', $updateArray);

            $complex->update($updateArray);

            // Refresh data
            $this->complexes = $complex->fresh();
            $this->existing_cover_image = $complex->cover_image;
            $this->cover_image = null;

            session()->flash('message', 'Complex settings updated successfully!');
            $this->isEditModalOpen = false;

        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Validation failed. Please check all fields.');
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating complex: ' . $e->getMessage());
            Log::error('Complex update error: ' . $e->getMessage());
        }
    }

    /**
     * Parse and normalize opening hours loaded from DB.
     * Expected DB format: either null, a JSON string or an array like
     * ['monday' => ['open'=>'09:00','close'=>'17:00','closed'=>false], ...]
     * Returns canonical array with lowercase day keys and fields open/close/closed.
     *
     * @param mixed $dbHours
     * @return array
     */
    private function parseOpeningHoursFromDb($dbHours)
    {
        $result = [];
        foreach ($this->days as $day) {
            $result[$day] = ['open' => null, 'close' => null, 'closed' => false];
        }

        if (!is_array($dbHours)) {
            return $result;
        }

        foreach ($dbHours as $key => $value) {
            $k = strtolower($key);
            if (!in_array($k, $this->days)) {
                continue;
            }

            // If value is string like "09:00-17:00" or array
            if (is_string($value)) {
                // try to split by - or to a single value
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

    /**
     * Update opening hours only (inline editor)
     */
    public function updateOpeningHours()
    {
        try {
            $this->validate([
                'opening_hours.*.open' => 'nullable|date_format:H:i',
                'opening_hours.*.close' => 'nullable|date_format:H:i',
                'opening_hours.*.closed' => 'nullable|boolean',
            ]);

            $complex = BookingVenue::find($this->complex_id);
            if (!$complex) {
                session()->flash('error', 'Complex not found.');
                return;
            }

            $complex->update(['opening_hours' => $this->opening_hours]);
            $this->complexes = $complex->fresh();
            // refresh parsed opening hours
            $this->opening_hours = $this->parseOpeningHoursFromDb($this->complexes->opening_hours ?: []);

            $this->showOpeningHoursEditor = false;
            session()->flash('message', 'Opening hours updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Invalid opening hours format. Please use HH:MM.');
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating opening hours: ' . $e->getMessage());
            Log::error('Opening hours update error: ' . $e->getMessage());
        }
    }

    public function uploadGallery()
    {
        try {
            $this->validate([
                'gallery_images' => 'required|array|max:4',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $newImages = [];
            foreach ($this->gallery_images as $image) {
                $path = $image->store('complex/gallery', 'public');
                $newImages[] = 'storage/' . $path;
            }

            $this->existing_gallery_images = array_merge($this->existing_gallery_images, $newImages);
            $this->complexes->update(['gallery_images_json' => $this->existing_gallery_images]);

            $this->gallery_images = [];
            session()->flash('message', 'Images uploaded successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Error uploading images: ' . $e->getMessage());
        }
    }

    public function removeImage($index)
    {
        try {
            if (isset($this->existing_gallery_images[$index])) {
                $imagePath = str_replace('storage/', 'public/', $this->existing_gallery_images[$index]);
                
                if (Storage::exists($imagePath)) {
                    Storage::delete($imagePath);
                }

                unset($this->existing_gallery_images[$index]);
                $this->existing_gallery_images = array_values($this->existing_gallery_images);

                $this->complexes->update(['gallery_images_json' => $this->existing_gallery_images]);

                session()->flash('message', 'Image removed successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error removing image: ' . $e->getMessage());
        }
    }

    public function addSocialLink()
    {
        try {
            $this->validate([
                'new_social_platform' => 'required|string',
                'new_social_url' => 'required|url',
            ]);

            $platform = ucfirst(strtolower($this->new_social_platform));
            $this->social_links[$platform] = $this->new_social_url;

            $this->complexes->update(['social_links' => $this->social_links]);

            $this->new_social_platform = '';
            $this->new_social_url = '';

            session()->flash('message', 'Social link added successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Error adding social link: ' . $e->getMessage());
        }
    }

    public function removeSocialLink($platform)
    {
        try {
            if (isset($this->social_links[$platform])) {
                unset($this->social_links[$platform]);
                $this->complexes->update(['social_links' => $this->social_links]);
                session()->flash('message', 'Social link removed successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error removing social link: ' . $e->getMessage());
        }
    }

    public function changePassword()
    {
        try {
            $this->validate([
                'current_password' => ['required', 'current_password'],
                'new_password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
                ],
                'new_password_confirmation' => ['required'],
            ], [
                'current_password.current_password' => 'The current password is incorrect.',
                'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            ]);

            auth()->user()->update([
                'password' => Hash::make($this->new_password)
            ]);

            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

            session()->flash('password_message', 'Password changed successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Error changing password: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.staff.staff-setting');
    }
}
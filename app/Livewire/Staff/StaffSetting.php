<?php

namespace App\Livewire\Staff;

use App\Models\Complex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

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
    public $cover_image; // For new uploads
    public $existing_cover_image; // To store current image path
    public $email_address; // Assuming you have an email field
    public $contact_number;
    public $website;
    public $county;
    public $location;
    public $address;
    public $postal_code;
    public $opening_hours = [
        'Monday' => '',
        'Tuesday' => '',
        'Wednesday' => '',
        'Thursday' => '',
        'Friday' => '',
        'Saturday' => '',
        'Sunday' => ''
    ];
    public $amenities = [];
    public $new_amenity = '';
    public $video_tour_url;
    public $gallery_images = [];
    public $existing_gallery_images = [];
    public $upload_batch = 1;
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
        $this->complex_id = Auth::user()->complex_id;
        $this->complexes = Complex::find($this->complex_id);

        $this->complex_name = $this->complexes->complex_name;
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


        $dbHours = $this->complexes->opening_hours;

        if ($dbHours) {
            $decodedHours = is_array($dbHours) ? $dbHours : json_decode($dbHours, true);
            $this->opening_hours = array_merge($this->opening_hours, $decodedHours);
        }
        // dd($this->opening_hours); passing this variable to the view

        $this->amenities = $this->complexes->amenities ?? [];
        $this->video_tour_url = $this->complexes->video_tour_url;
        $this->existing_gallery_images = $this->complexes->gallery_images ?? [];
        $this->description = $this->complexes->description;
        $this->terms = $this->complexes->terms;
        $this->social_links = $this->complexes->social_links ?? [];

        // Set active section from URL if present
        if (request()->has('section')) {
            $this->activeSection = request('section');
        };
    }

    public function addAmenity()
    {
        $this->validate([
            'new_amenity' => 'required|string|max:255'
        ]);

        if (!in_array($this->new_amenity, $this->amenities)) {
            $this->amenities[] = $this->new_amenity;
            $this->new_amenity = '';
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
    public function removeAmenity($index)
    {
        unset($this->amenities[$index]);
        $this->amenities = array_values($this->amenities); // Reindex array
    }


    public function openEditModal()
    {
        $this->isEditModalOpen = true;
    }

    public function closeEditModal()
    {
        $this->isEditModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->complex_name = $this->complexes->complex_name;
        $this->complex_type = $this->complexes->complex_type;
        $this->status = $this->complexes->status;
        $this->cover_image = null; // Reset file input
        $this->existing_cover_image = $this->complexes->cover_image;
        $this->email_address = $this->complexes->email_address;
        $this->contact_number = $this->complexes->contact_number;
        $this->website = $this->complexes->website;
        $this->county = $this->complexes->county;
        $this->location = $this->complexes->location;
        $this->address = $this->complexes->address;
        $this->postal_code = $this->complexes->postal_code;
        $dbHours = $this->complexes->opening_hours;
        $this->opening_hours = [
            'Monday' => '',
            'Tuesday' => '',
            'Wednesday' => '',
            'Thursday' => '',
            'Friday' => '',
            'Saturday' => '',
            'Sunday' => ''
        ];


        if ($dbHours) {
            if (is_array($dbHours)) {
                $this->opening_hours = array_merge($this->opening_hours, $dbHours);
            } else {
                $decoded = json_decode($dbHours, true) ?? [];
                $this->opening_hours = array_merge($this->opening_hours, $decoded);
            }
        };

        $this->description = $this->complexes->description;
        $this->terms = $this->complexes->terms;
    }

    public function saveChanges()
    {
        $validated = $this->validate([
            'complex_name' => 'required|string|max:255',
            'complex_type' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'cover_image' => 'nullable|image|max:2048', // 2MB Max
            'email_address' => 'required|email|max:255',
            'contact_number' => 'required|string|max:255',
            'website' => 'required|url|max:255',
            'county' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'opening_hours.monday' => 'nullable|string',
            'opening_hours.tuesday' => 'nullable|string',
            'opening_hours.wednesday' => 'nullable|string',
            'opening_hours.thursday' => 'nullable|string',
            'opening_hours.friday' => 'nullable|string',
            'opening_hours.saturday' => 'nullable|string',
            'opening_hours.sunday' => 'nullable|string',
            'amenities' => 'array',
            'video_tour_url' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        $complex = Complex::find($this->complex_id);

        // Handle image upload
        if ($this->cover_image) {
            // Delete old image if exists
            if ($complex->cover_image && Storage::exists('public/' . $complex->cover_image)) {
                Storage::delete('public/' . $complex->cover_image);
            }
            // Store new image
            $validated['cover_image'] = $this->cover_image->store('complex', 'public');
        } else {
            // Keep existing image if no new upload
            $validated['cover_image'] = $this->existing_cover_image;
        }
        $complex->video_tour_url = $this->video_tour_url;

        // Update complex
        $complex->update([
            'complex_name' => $validated['complex_name'],
            'complex_type' => $validated['complex_type'],
            'status' => $validated['status'],
            'cover_image' => $validated['cover_image'],
            'email_address' => $validated['email_address'],
            'contact_number' => $validated['contact_number'],
            'website' => $validated['website'],
            'county' => $validated['county'],
            'location' => $validated['location'],
            'address' => $validated['address'],
            'postal_code' => $validated['postal_code'],
            'opening_hours' => $this->opening_hours,
            'amenities' => $this->amenities,
            'video_tour_url' => $complex->video_tour_url,
            'gallery_images' => $complex->gallery_images,
            'description' => $this->description,
            'terms' => $this->terms,

        ]);

        // Refresh data
        $this->complexes = $complex;
        $this->existing_cover_image = $complex->cover_image;

        session()->flash('message', 'Complex settings updated successfully.');
        $this->isEditModalOpen = false;
    }

    // Handle gallery image uploads
    public function uploadGallery()
    {
        $this->validate([
            'gallery_images' => 'required|array|max:4',
            'gallery_images.*' => 'image|max:2048', // 2MB max
        ]);

        $newImages = [];
        foreach ($this->gallery_images as $image) {
            $path = $image->store('public/complex/gallery');
            $newImages[] = str_replace('public/', 'storage/', $path);
        }

        $this->existing_gallery_images = array_merge($this->existing_gallery_images, $newImages);
        $this->complexes->update(['gallery_images' => $this->existing_gallery_images]);

        $this->gallery_images = [];
        $this->dispatch('notify', type: 'success', message: 'Images uploaded successfully!');
    }

    public function removeImage($index)
    {
        $imagePath = str_replace('storage/', 'public/', $this->existing_gallery_images[$index]);
        Storage::delete($imagePath);

        unset($this->existing_gallery_images[$index]);
        $this->existing_gallery_images = array_values($this->existing_gallery_images);

        $this->complexes->update(['gallery_images' => $this->existing_gallery_images]);

        $this->dispatch('notify', type: 'success', message: 'Image removed successfully!');
    }

    public function addSocialLink()
    {
        $this->validate([
            'new_social_platform' => 'required|string',
            'new_social_url' => 'required|url',
        ]);

        $platform = strtolower($this->new_social_platform);
        $this->social_links[$platform] = $this->new_social_url;

        $this->complexes->update(['social_links' => $this->social_links]);

        $this->new_social_platform = '';
        $this->new_social_url = '';

        $this->dispatch('notify', type: 'success', message: 'Social link added!');
    }

    public function removeSocialLink($platform)
    {
        unset($this->social_links[$platform]);
        $this->complexes->update(['social_links' => $this->social_links]);
        $this->dispatch('notify', type: 'success', message: 'Social link removed!');
    }

    protected $rules = [
        'current_password' => ['required', 'current_password'],
        'new_password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
        ],
        'new_password_confirmation' => ['required'],
    ];

    protected $messages = [
        'current_password.current_password' => 'The current password is incorrect.',
        'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
    ];

    public function changePassword()
    {
        $this->validate();

        // Update password
        auth()->user()->update([
            'password' => Hash::make($this->new_password)
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        session()->flash('password_message', 'Password changed successfully!');
    }

    public function render()
    {
        return view('livewire.staff.staff-setting');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\BookingVenue;
use App\Models\BookingSport;
use App\Models\BookingGalleryImage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
class CustomRegister extends Component
{
    use WithFileUploads;

    public $currentStep = 1;

    // Step 1: Personal Info
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $contact;

    // Step 2: Venue Info (directly to booking_venue)
    public $complex_name;
    public $complex_type = 'Indoor';
    public $county;
    public $location;
    public $address;
    public $postal_code;
    public $contact_number;
    public $email_address;
    public $website;
    public $status = 'Active';
    public $description;

    // Step 3: Facility Details
    public $opening_hours = [];
    public $amenities = [];
    public $cover_image;
    public $gallery_images = [];
    public $sport_type;
    public $capacity;
    public $hourly_rate;
    public $length;
    public $width;
    public $terms = false;

    public $video_tour_url;
    public $facebook_url;
    public $twitter_url;
    public $instagram_url;

    public $availableCounties = [
        'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika',
        'Malindi', 'Kitale', 'Garissa', 'Kakamega', 'Nyeri', 'Meru'
    ];

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

    public $availableStatuses = ['Active', 'Inactive', 'Maintenance', 'New'];

    protected function step1Rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => 'required|min:6|confirmed',
            'contact' => 'required|string|max:20',
        ];
    }

    protected function step2Rules()
    {
        return [
            'complex_name' => 'required|min:3|max:255',
            'complex_type' => 'required|in:Indoor,Outdoor,Both',
            'county' => 'required',
            'location' => 'required',
            'address' => 'required|min:5|max:500',
            'postal_code' => 'nullable|max:20',
            'contact_number' => 'required|max:20',
            'email_address' => 'nullable|email',
            'website' => 'nullable|url',
            'status' => 'required|in:Active,Inactive,Maintenance,New',
            'description' => 'required|min:10|max:1000',
        ];
    }
    
    protected function step3Rules()
    {
        return [
            'opening_hours' => 'required|array',
            'amenities' => 'array',
            'cover_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'sport_type' => 'required',
            'capacity' => 'integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:1',
            'width' => 'required|numeric|min:1',
            'terms' => 'accepted',
            'video_tour_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
        ];
    }

    public function mount()
    {
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        foreach ($days as $day) {
            $this->opening_hours[$day] = ['open' => '09:00', 'close' => '17:00', 'closed' => false];
        }
    }

    public function updated($propertyName)
    {
        if ($this->currentStep === 1) {
            $this->validateOnly($propertyName, $this->step1Rules());
        } elseif ($this->currentStep === 2) {
            $this->validateOnly($propertyName, $this->step2Rules());
        } elseif ($this->currentStep === 3) {
            $this->validateOnly($propertyName, $this->step3Rules());
        }
    }
    
    public function nextStep()
    {
        if ($this->currentStep === 1) {
            $this->validate($this->step1Rules());
        } elseif ($this->currentStep === 2) {
            $this->validate($this->step2Rules());
        }
        $this->currentStep++;
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function setAllHours()
    {
        $mondayHours = $this->opening_hours['monday'] ?? ['open' => '09:00', 'close' => '17:00', 'closed' => false];
        foreach (['tuesday','wednesday','thursday','friday','saturday','sunday'] as $day) {
            $this->opening_hours[$day] = $mondayHours;
        }
    }
    
    public function removeGalleryImage($index)
    {
        if (isset($this->gallery_images[$index])) {
            array_splice($this->gallery_images, $index, 1);
        }
    }

    public function resetCoverImage()
    {
        $this->cover_image = null;
        $this->resetErrorBag('cover_image');
    }

    public function register()
    {
        $this->validate(array_merge(
            $this->step1Rules(),
            $this->step2Rules(),
            $this->step3Rules()
        ));

        try {
            DB::transaction(function () {
                $socialLinks = [
                    'facebook' => $this->facebook_url,
                    'twitter' => $this->twitter_url,
                    'instagram' => $this->instagram_url,
                ];

                $coverImagePath = $this->cover_image ? $this->cover_image->store('venues', 'public') : null;

                $galleryImagePaths = [];
                if (!empty($this->gallery_images)) {
                    foreach ($this->gallery_images as $image) {
                        $galleryImagePaths[] = $image->store('venues/gallery', 'public');
                    }
                }

                // Create BookingVenue directly (no Complex model)
                $data = [
                    'name' => $this->complex_name,
                    'complex_type' => $this->complex_type,
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
                    'capacity' => $this->capacity,
                    'hourly_rate' => $this->hourly_rate,
                    'dimensions' => $this->length . 'x' . $this->width,
                    'primary_sport_type' => $this->sport_type,
                    'video_tour_url' => $this->video_tour_url,
                    'social_links' => $socialLinks,
                    'terms' => $this->terms,
                ];

                // Add default rating for new venues
                $data['rating'] = 0;
                $data['reviews'] = 0;

                if ($this->cover_image) {
                    $filename = 'cover_' . time() . '.' . $this->cover_image->getClientOriginalExtension();
                    $path = $this->cover_image->storeAs('venues', $filename, 'public');
                    $data['cover_image'] = $path ?? null;
                }

                // Create new venue
                $venue = BookingVenue::create($data);

                // Create sport entry
                BookingSport::create([
                    'name' => $this->complex_name,
                    'price' => $this->hourly_rate,
                    'available' => $this->status === 'Active',
                    'game_type' => $this->sport_type ?? 'other',
                    'venue_id' => $venue->id,
                    'description' => $this->description,
                    'image_url' => $data['cover_image'] ?? null
                ]);

                // Handle gallery images
                if ($this->gallery_images) {
                    foreach ($this->gallery_images as $img) {
                        $filename = 'gallery_' . time() . '_' . Str::random(5) . '.' . $img->getClientOriginalExtension();
                        $path = $img->storeAs('venues/gallery', $filename, 'public');

                        BookingGalleryImage::create([
                            'image_url' => $path ?? null,
                            'venue_id' => $venue->id
                        ]);
                    }
                }

                // Create user (without complex_id since we're not using Complex model)
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'role' => 'facility_owner',
                    'contact' => $this->contact,
                    // Remove complex_id since we're not using Complex model
                ]);

                Auth::login($user);
            });

            session()->flash('success', 'Registration successful! Welcome to your dashboard.');
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            session()->flash('error', 'An error occurred during registration. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.custom-register');
    }
}
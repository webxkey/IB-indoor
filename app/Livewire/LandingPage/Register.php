<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\BookingVenue;
use App\Models\BookingSport;
use App\Models\BookingGalleryImage;
use Illuminate\Validation\Rule;
use App\Models\UserUser;


class Register extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $showSuccessModal = false;

    // Step 1: Personal Info
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $contact;

    // Step 2: Venue  Info (directly to booking_venue)
    public $complex_name;
    public $complex_type = 'Indoor';
    public $county;
    public $location;
    public $address;
    public $postal_code;
    public $contact_number;
    public $email_address;
    public $website;
    public $status = 'New';
    public $description;

    // Step 3: Facility Details
    public $opening_hours = [];
    public $amenities = [];
    public $cover_image;
    public $gallery_images = [];
    public $sport_types = []; // array to allow multiple sports
    public $sport_type;       // kept for backward compatibility
    public $capacity;
    public $hourly_rate;
    public $length;
    public $width;
    public $terms = false;
    public $gameType = 'Indoor';

    public $video_tour_url;
    public $facebook_url;
    public $twitter_url;
    public $instagram_url;

    public $availableCity = [
        'Colombo',
        'Galle',
        'Gampaha',
        'Kandy',
        'Jaffna',
        'Nuwara Eliya',
        'Kurunegala',
        'Kalutara',
        'vavuniya',
        'puttalam',
        'Anuradhapura',
        'Trincomalee',
        'Batticaloa',
        'Matara',
        'Hambantota',
        'Badulla',
        'Monaragala',
        'Ratnapura',
        'Kegalle',
        'Matale',
        'Polonnaruwa',
        'Ampara',
        'Mullaitivu'

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
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('users_user', 'email'),
            ],
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
            'sport_types' => 'required|array|min:1',
            'capacity' => 'required|integer|min:1',
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
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
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
        foreach (['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
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

    public function redirectToLogin()
    {
        return redirect()->route('login');
    }

    public function register()
    {
        $this->email = strtolower(trim((string) $this->email));

        $this->validate(array_merge(
            $this->step1Rules(),
            $this->step2Rules(),
            $this->step3Rules()
        ));

        try {
            $registeredUser = null;

            DB::transaction(function () use (&$registeredUser) {
                // Prepare social links
                $socialLinks = [
                    'facebook' => $this->facebook_url,
                    'twitter' => $this->twitter_url,
                    'instagram' => $this->instagram_url,
                ];

                // Store cover image
                $coverImagePath = null;
                if ($this->cover_image) {
                    $filename = 'cover_' . time() . '_' . Str::random(5) . '.' . $this->cover_image->getClientOriginalExtension();
                    $coverImagePath = $this->cover_image->storeAs('venues', $filename, 'public');
                }

                // Store gallery images
                $galleryImagePaths = [];
                if ($this->gallery_images) {
                    foreach ($this->gallery_images as $img) {
                        $filename = 'gallery_' . time() . '_' . Str::random(5) . '.' . $img->getClientOriginalExtension();
                        $galleryImagePaths[] = $img->storeAs('venues/gallery', $filename, 'public');
                    }
                }

                // Create venue
                // No Complex model: create only booking_venue and use its id for users.complex_id
            
                $base_image_url = $coverImagePath ? asset('storage/'.$coverImagePath) : 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg';
                $venue = BookingVenue::create([
                    'name' => $this->complex_name,
                    'complex_type' => $this->complex_type,
                    'address' => $this->address,
                    'county' => $this->county,
                    'location' => $this->location,
                    'postal_code' => $this->postal_code,
                    'contact_number' => $this->contact_number,
                    'email_address' => $this->email_address,
                    'image_url' => $base_image_url,
                    'website' => $this->website,
                    'status' => 'New', // Force status to New for manual approval
                    'opening_hours' => $this->opening_hours,
                    'amenities' => $this->amenities,
                    'description' => $this->description,
                    'cover_image' => $coverImagePath,
                    'gallery_images_json' => $galleryImagePaths,
                    'video_tour_url' => $this->video_tour_url,
                    'social_links' => $socialLinks,
                    'terms' => $this->terms,
                    'rating' => 0,
                    'reviews' => 0,
                    'analytics_enabled' => true,
                    'venue_category' => strtolower($this->complex_type),
                    'is_featured' => false,
                ]);

                // Create sports (one per selected type)
                $sportsToCreate = !empty($this->sport_types) ? $this->sport_types : [$this->sport_type ?? 'Football'];
                foreach ($sportsToCreate as $sportName) {
                    BookingSport::create([
                        'name' => ucfirst($sportName),
                        'price' => $this->hourly_rate,
                        'available' => false, // Default to false until approved
                        'game_type' => $this->gameType ?? 'Indoor',
                        'rate_type' => 'Per hour',
                        'venue_id' => $venue->id,
                        'description' => $this->description,
                        'image' => $base_image_url,
                        'maximum_court' => 1,
                        'status' => 'Inactive', // Default to Inactive until approved
                    ]);
                }

                // Create gallery images
                foreach ($galleryImagePaths as $path) {
                    BookingGalleryImage::create([
                        'venue_id' => $venue->id,
                        'image_url' => $path
                    ]);
                }

                // Create user in users table
                $registeredUser = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'role' => 'facility_owner',
                    'contact' => $this->contact,
                    // link the user to the booking_venue record id
                    'complex_id' => $venue->id,
                ]);

                // Also create user in users_user table for app compatibility
                UserUser::create([
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'first_name' => explode(' ', $this->name)[0] ?? $this->name,
                    'last_name' => implode(' ', array_slice(explode(' ', $this->name), 1)) ?? '',
                    'phone_number' => $this->contact,
                    'is_active' => true,
                    'is_staff' => false,
                    'is_superuser' => false,
                    'points' => 0,
                    'referral_code' => $this->rondomReferralCode(),
                    'is_public_profile' => true,
                    'is_show_contact' => true,
                    'availability' => 'both',
                    'username' => $this->generateUniqueUsername($this->email),
                    'username_changes_used' => 0,
                    'accepted_tnc' => (bool) $this->terms,
                    'gender' => 'prefer_not_to_say',
                ]);
            });

            // Show success modal instead of auto-login
            $this->showSuccessModal = true;
        } catch (QueryException $e) {
            Log::error('Registration failed with database error: ' . $e->getMessage());

            if (($e->getCode() === '23505') || str_contains($e->getMessage(), 'users_user_email_key') || str_contains($e->getMessage(), 'users_email_key')) {
                $this->addError('email', 'This email address is already registered. Please use a different email or sign in to your existing account.');
                session()->flash('error', 'This email address is already registered. Please use a different email or sign in to your existing account.');
                return;
            }

            $errorMessage = config('app.debug') ? 'Database error: ' . $e->getMessage() : 'An error occurred during registration. Please try again.';
            session()->flash('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            $errorMessage = config('app.debug') ? 'Error: ' . $e->getMessage() : 'An error occurred during registration. Please try again.';
            session()->flash('error', $errorMessage);
        }
    }


    public function render()
    {
        return view('livewire.landing-page.register');
    }

    /**
     * Generate a unique referral code.
     *
     * Keeps generating until a unique code is found in the users_user table.
     * The method name preserves the original typo to avoid changing other callers.
     *
     * @return string
     */
    private function rondomReferralCode()
    {
        do {
            // 8 character uppercase alphanumeric code
            $code = strtoupper(Str::random(8));
        } while (UserUser::where('referral_code', $code)->exists());

        return $code;
    }

    private function generateUniqueUsername($email)
    {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $email)[0]));
        if ($base === '') {
            $base = 'user';
        }
        $base = substr($base, 0, 24);

        $candidate = $base;
        while (UserUser::where('username', $candidate)->exists()) {
            $candidate = substr($base, 0, 24) . rand(100, 9999);
        }

        return substr($candidate, 0, 30);
    }
}

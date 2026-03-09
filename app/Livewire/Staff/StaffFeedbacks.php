<?php

namespace App\Livewire\Staff;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\BookingVenueReview;
use App\Models\UserUser;
use Illuminate\Support\Facades\Auth;



#[Title("Feedback Dashboard")]
#[Layout("components.layouts.staff")]
class StaffFeedbacks extends Component
{
    public $totalReviews = 0;
    public $averageRating = 0.0;
    public $reviews = [];
    public $venue;
    public $imageUrl;
    public $complex_id;
    public $profile_picture_url;

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        $this->loadFeedbacks();
    }

    public function loadFeedbacks()
    {
        // Get reviews only for the current venue
        $reviews = BookingVenueReview::with(['user', 'venue'])
            ->where('venue_id', $this->complex_id)
            ->latest()
            ->get();
        $userids = $reviews->pluck('user_id')->unique()->toArray();

        // Load profile picture filenames for the users and key by user id
        $userProfileImages = UserUser::whereIn('id', $userids)
            ->get(['id', 'profile_picture'])
            ->keyBy('id');

        // Attach a fully-qualified profile picture URL to each review using users_user table
        foreach ($reviews as $review) {
            $imgFilename = null;
            if (isset($userProfileImages[$review->user_id]) && $userProfileImages[$review->user_id]->profile_picture) {
                $imgFilename = $userProfileImages[$review->user_id]->profile_picture;
            }

            $review->profile_picture_url = $imgFilename
                ? 'https://api.indoorbooking.com/media/' . ltrim($imgFilename, '/')
                : null;
        }

        // For compatibility keep a single property pointing to the first review image (if present)
        $this->profile_picture_url = $reviews->first()->profile_picture_url ?? null;
        $this->totalReviews = $reviews->count();
        $this->averageRating = round($reviews->avg('rating'), 1);
        $this->reviews = $reviews;
    }

    public function render()
    {
        return view('livewire.staff.staff-feedbacks', [
            'reviews' => $this->reviews,
            'totalReviews' => $this->totalReviews,
            'averageRating' => $this->averageRating,
        ]);
    }
}

<?php

namespace App\Livewire\Staff;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\BookingVenueReview;
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

<?php

namespace App\Livewire\Staff;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\BookingVenueReview;


#[Title("Feedback Dashboard")]
#[Layout("components.layouts.staff")]
class StaffFeedbacks extends Component
{
    public $totalReviews = 0;
    public $averageRating = 0.0;
    public $reviews = [];

    public function mount()
    {
        $this->loadFeedbacks();
    }

    public function loadFeedbacks()
    {
        // Get all reviews with related user and venue
        $reviews = BookingVenueReview::with(['user', 'venue'])
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
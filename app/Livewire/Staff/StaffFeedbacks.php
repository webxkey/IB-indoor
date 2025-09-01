<?php

namespace App\Livewire\Staff;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Feedback Dashboard")]
#[Layout("components.layouts.staff")]
class StaffFeedbacks extends Component
{
    public $totalReviews = 0;
    public $averageRating = 0.0;
    public $reviews = [];
    

    public function mount()
    {
        // Hardcoded dummy data
        $this->totalReviews = 10; // Total number of reviews
        $this->averageRating = 4.0; // Average rating out of 5

        $this->reviews = [
            [
                'id' => 1,
                'user' => [
                    'name' => 'Towhidur Rahman',
                    'profile_image' => 'https://via.placeholder.com/50', // Dummy image URL
                ],
                'rating' => 5,
                'comment' => 'My first and only mala order on Etsy, and beyond delighted! Requested a custom mala based on two stones I was drawn to invite together in this kind of creation. The fun and genuine joy I invite together in this kind.',
                'created_at' => '2025-08-12', // Yesterday
            ],
            [
                'id' => 2,
                'user' => [
                    'name' => 'Towhidur Rahman',
                    'profile_image' => 'https://via.placeholder.com/50',
                ],
                'rating' => 4,
                'comment' => 'My first and only mala order on Etsy, and I’m beyond delighted! Requested a custom mala based on two stones I was drawn to invite together in this kind of creation. The fun and genuine joy I invite together.',
                'created_at' => '2025-08-11', // Two days ago
            ],
            [
                'id' => 3,
                'user' => [
                    'name' => 'Sarah Johnson',
                    'profile_image' => 'https://via.placeholder.com/50',
                ],
                'rating' => 3,
                'comment' => 'Nice product, but the delivery took longer than expected. The quality is good though.',
                'created_at' => '2025-08-10', // Three days ago
            ],
        ];
    }

    public function render()
    {
        return view('livewire.staff.staff-feedbacks');
    }
}
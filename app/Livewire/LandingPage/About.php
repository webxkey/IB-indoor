<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\LandingPage;
use App\Models\BookingVenue;
use App\Models\BookingBooking;
use App\Models\BookingVenueReview;

#[Layout('components.layouts.main')]
class About extends Component
{
    public $bookingCount;
    public $reviewCount;
    public $venueCount;
    public $landingPageSections;
    public $aboutImageUrl;
    public $aboutTitle;
    public $aboutDescription;
    public $feedbacks; // user feedback list

    public function render()
    {
        // Stats
        // $this->bookingCount = BookingBooking::count();
        // $this->reviewCount = BookingVenueReview::count();
        // $this->venueCount = BookingVenue::count();

        // About content
        $this->landingPageSections = LandingPage::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->get();

        $aboutSection = $this->landingPageSections->firstWhere('page_name', 'about');
        if ($aboutSection) {
            $this->aboutImageUrl = $aboutSection->images[0] ?? null;
            $this->aboutTitle = $aboutSection->section_title ?? '';
            $this->aboutDescription = $aboutSection->section_description ?? '';
        }

        // ✅ Fetch latest 5 reviews with user names
        $this->feedbacks = BookingVenueReview::with('user:id,first_name')
            ->select('id', 'user_id', 'comment')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.landing-page.about', [
            'landingPageSections' => $this->landingPageSections,
            'bookingCount' => $this->bookingCount,
            'reviewCount' => $this->reviewCount,
            'venueCount' => $this->venueCount,
            'feedbacks' => $this->feedbacks,
        ]);
    }
}

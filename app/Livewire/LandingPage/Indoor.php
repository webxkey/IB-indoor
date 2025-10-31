<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\BookingVenue;

#[Layout('components.layouts.main')]
class Indoor extends Component
{
    public $VenuesDetails;

    public function render()
    {
        $this->VenuesDetails = BookingVenue::select(
            'name',
            'description',
            'location',
            'image_url'
        )->get();
        
        return view('livewire.landing-page.indoor', [
            'VenuesDetails' => $this->VenuesDetails,
        ]);
    }
}

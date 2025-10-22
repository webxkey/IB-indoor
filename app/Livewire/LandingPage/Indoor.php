<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\BookingVenue;



#[Layout('components.layouts.main')]

class Indoor extends Component
{
    public $VenuesDetails;
    public $landingPageSections;
    public function render()
    {
         $this->VenuesDetails = BookingVenue::latest()->take(6)->get(); // fetch 6 recent venues


        
        return view('livewire.landing-page.indoor',[
            'VenuesDetails' => $this->VenuesDetails,

        ]);
    }
}

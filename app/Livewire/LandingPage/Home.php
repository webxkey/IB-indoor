<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\BookingVenue;
use App\Models\LandingPage;

#[Layout('components.layouts.main')]
class Home extends Component
{
    public $VenuesDetails;
    public $landingPageSections;
    public $heroImageUrl;
    public $sectionTitle;
    public $sectionDescription;
    public $bannerImageUrl;
    public $bannerImageUrl1;
    public $bannerImageUrl2;
    public $aboutImageUrl;
    public $aboutTitle;
    public $aboutDescription;
    public $socialImageurl;
    public $socialTitle;
    public $socialDescription;


    public function render()
    {
        $this->VenuesDetails = BookingVenue::latest()->take(3)->get(); // fetch 3 recent venues
        $this->landingPageSections = LandingPage::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->get();    
            // dd($this->landingPageSections);
        $this->heroImageUrl = $this->landingPageSections->firstWhere('page_name', 'home-hero')->images[0] ;
        $this->sectionTitle = $this->landingPageSections->firstWhere('page_name', 'home-hero')->section_title;
        $this->sectionDescription = $this->landingPageSections->firstWhere('page_name', 'home-hero')->section_description;
        $this->bannerImageUrl = $this->landingPageSections->firstWhere('page_name', 'home-banner')->images[0] ;
        $this->bannerImageUrl1 = $this->landingPageSections->firstWhere('page_name', 'home-banner2')->images[0] ;
        $this->bannerImageUrl2 = $this->landingPageSections->firstWhere('page_name', 'home-banner3')->images[0] ;
        $this->aboutImageUrl = $this->landingPageSections->firstWhere('page_name', 'home-about')->images[0] ;
        $this->aboutTitle = $this->landingPageSections->firstWhere('page_name', 'home-about')->section_title;
        $this->aboutDescription = $this->landingPageSections->firstWhere('page_name', 'home-about')->section_description;
        $this->socialImageurl = $this->landingPageSections->firstWhere('page_name', 'home-social')->images[0] ;
        $this->socialTitle = $this->landingPageSections->firstWhere('page_name', 'home-social')->section_title;
        $this->socialDescription = $this->landingPageSections->firstWhere('page_name', 'home-social')->section_description;
        
        return view('livewire.landing-page.home', [
            'VenuesDetails' => $this->VenuesDetails,
            'landingPageSections' => $this->landingPageSections,
        ]);
    }
}

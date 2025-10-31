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

    public $socialImageUrl;
    public $socialTitle;
    public $socialDescription;

    public function render()
    {
        // 🏟 Fetch latest 3 venues
        $this->VenuesDetails = BookingVenue::latest()->take(3)->get();

        // 📄 Load all landing page sections
        $this->landingPageSections = LandingPage::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // 🦺 Always use "optional()" or null checks to prevent crashes
        $hero = $this->landingPageSections->firstWhere('page_name', 'home-hero');
        $banner1 = $this->landingPageSections->firstWhere('page_name', 'home-banner');
        $banner2 = $this->landingPageSections->firstWhere('page_name', 'home-banner2');
        $banner3 = $this->landingPageSections->firstWhere('page_name', 'home-banner3');
        $about = $this->landingPageSections->firstWhere('page_name', 'home-about');
        $social = $this->landingPageSections->firstWhere('page_name', 'home-social');

        // 🖼 Assign safely with fallbacks
        $this->heroImageUrl = optional($hero->images)[0] ?? 'https://via.placeholder.com/800x400';
        $this->sectionTitle = $hero->section_title ?? '';
        $this->sectionDescription = $hero->section_description ?? '';

        $this->bannerImageUrl = optional($banner1->images)[0] ?? 'https://via.placeholder.com/600x300';
        $this->bannerImageUrl1 = optional($banner2->images)[0] ?? 'https://via.placeholder.com/600x300';
        $this->bannerImageUrl2 = optional($banner3->images)[0] ?? 'https://via.placeholder.com/600x300';

        $this->aboutImageUrl = optional($about->images)[0] ?? 'https://via.placeholder.com/500x300';
        $this->aboutTitle = $about->section_title ?? '';
        $this->aboutDescription = $about->section_description ?? '';

        $this->socialImageUrl = optional($social->images)[0] ?? 'https://via.placeholder.com/400x400';
        $this->socialTitle = $social->section_title ?? '';
        $this->socialDescription = $social->section_description ?? '';

        // 🎨 Return data to the view
        return view('livewire.landing-page.home', [
            
            'VenuesDetails' => $this->VenuesDetails,
            'landingPageSections' => $this->landingPageSections,
        ]);
    }
}

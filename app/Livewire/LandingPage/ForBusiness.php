<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\Layout;
#[Layout('components.layouts.main')]


class ForBusiness extends Component
{
    public function render()
    {
        return view('livewire.landing-page.for-business');
    }
}

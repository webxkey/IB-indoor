<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\Layout;
#[Layout('components.layouts.main')]


class Privacy extends Component
{
    public function render()
    {
        return view('livewire.landing-page.privacy');
    }
}

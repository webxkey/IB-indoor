<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Indoor admin')]
class IndoorAdmins extends Component
{
    public function render()
    {
        return view('livewire.admin.indoor-admins');
    }
}

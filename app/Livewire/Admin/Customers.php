<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('App User')]
class Customers extends Component
{
    public function render()
    {
        return view('livewire.admin.customers');
    }
}

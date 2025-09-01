<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
#[Title('Dashboard')]
class AdminDashboard extends Component
{

  
    public function render()
    {
        return view('livewire.admin.admin-dashboard');
    }
}

<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomLogin extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    public function render()
    {
        return view('livewire.custom-login');
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            
            $user = Auth::user();

            // Check if facility owner is approved
            if ($user && $user->role === 'facility_owner') {
                $venue = $user->complex; // Relationship defined in User model
                if ($venue && $venue->status === 'New') {
                    Auth::logout();
                    $this->addError('email', 'Your facility registration is pending manual approval. our team will contact you shortly and then you can use our service.');
                    return;
                }
            }
            
            if ($user && $user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else if ($user && $user->role === 'staff') {
                return redirect()->route('staff.dashboard');
            } else if ($user && $user->role === 'facility_owner') {
                return redirect()->route('staff.dashboard');
            } else {
                return redirect()->route('dashboard');
            }
        }

        $this->addError('email', 'These credentials do not match our records.');
    }
}

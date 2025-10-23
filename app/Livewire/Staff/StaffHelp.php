<?php

namespace App\Livewire\Staff;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;

#[Title("Staff Dashboard")]
#[Layout("components.layouts.staff")]
class StaffHelp extends Component
{
    public $contact_subject;
    public $contact_message;

    protected $rules = [
        'contact_subject' => 'required|min:3',
        'contact_message' => 'required|min:10',
    ];

    public function submitContactForm()
    {
        $this->validate();

        $user = Auth::user();

        $contactData = [
            'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
            'email' => $user->email,
            'phone' => $user->phone ?? 'N/A',
            'subject' => $this->contact_subject,
            'message' => $this->contact_message,
        ];

        // Send email to admin
        Mail::to('mohammedrifam2624@gmail.com')->send(new ContactMessageMail($contactData));

        session()->flash('contact_message', '✅ Thank you! Your message has been sent successfully.');

        $this->reset(['contact_subject', 'contact_message']);
    }

    public function render()
    {
        return view('livewire.staff.staff-help');
    }
}

<?php

namespace App\Livewire\Staff;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Staff Dashboard")]
#[Layout("components.layouts.staff")]
class StaffHelp extends Component
{

    public $contact_name;
    public $contact_email;
    public $contact_phone;
    public $contact_subject;
    public $contact_message;
    public $contact_newsletter = false;
    

    protected $rules = [
        'contact_name' => 'required|min:3',
        'contact_email' => 'required|email',
        'contact_phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
        'contact_subject' => 'required',
        'contact_message' => 'required|min:10',
    ];

    public function submitContactForm()
    {
        $this->validate();

        // Process form submission (save to database, send email, etc.)
        // Example:
        // Contact::create([...]);
        // Mail::to('support@example.com')->send(new ContactFormSubmitted($this->contactData()));

        session()->flash('contact_message', 'Thank you for your message! We will get back to you soon.');

        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'contact_name',
            'contact_email',
            'contact_phone',
            'contact_subject',
            'contact_message',
            'contact_newsletter'
        ]);
    }
    public function render()
    {
        return view('livewire.staff.staff-help');
    }
}

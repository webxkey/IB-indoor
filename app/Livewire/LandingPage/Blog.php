<?php

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Blog as BlogModel;

#[Layout('components.layouts.main')]
class Blog extends Component
{
    public $blogs;

    public function mount()
    {
        // Fetch all blogs from database
        $this->blogs = BlogModel::orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.landing-page.blog', [
            'blogs' => $this->blogs
        ]);
    }
}

<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\LandingPage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Landing Page Management')]
class LandingPageCMS extends Component
{
    public $pages;
    public $page_id;
    public $page_name;
    public $section_title;
    public $section_description;
    public $display_order;
    public $is_active = true;

    public $images = [];   // array of image URLs
    public $new_image_url; // temp field for adding new URL

    public function mount()
    {
        $this->loadPages();
    }

    public function loadPages()
    {
        $this->pages = LandingPage::orderBy('display_order')->get();
    }

    public function edit($id)
    {
        $page = LandingPage::findOrFail($id);

        $this->page_id = $page->id;
        $this->page_name = $page->page_name;
        $this->section_title = $page->section_title;
        $this->section_description = $page->section_description;
        $this->images = $page->images ?? [];
        $this->display_order = $page->display_order;
        $this->is_active = $page->is_active;
    }

    public function addImage()
    {
        if ($this->new_image_url) {
            $this->images[] = $this->new_image_url;
            $this->new_image_url = '';
        }
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    public function save()
    {
        $this->validate([
            'page_name' => 'required|string|max:255',
            'section_title' => 'required|string|max:255',
            'section_description' => 'nullable|string',
            'display_order' => 'nullable|integer',
            
            
        ]);

        LandingPage::updateOrCreate(
            ['id' => $this->page_id],
            [
                'page_name' => $this->page_name,
                'section_title' => $this->section_title,
                'section_description' => $this->section_description,
                'images' => $this->images,
                'display_order' => $this->display_order,
                'is_active' => $this->is_active,
            ]
        );
        

        $this->resetInput();
        $this->loadPages();
        session()->flash('success', 'Landing page section updated successfully!');
    }

    public function delete($id)
    {
        LandingPage::findOrFail($id)->delete();
        $this->loadPages();
        session()->flash('success', 'Section deleted successfully!');
    }

    public function resetInput()
    {
        $this->page_id = null;
        $this->page_name = '';
        $this->section_title = '';
        $this->section_description = '';
        $this->images = [];
        $this->new_image_url = '';
        $this->display_order = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.landing-page-c-m-s');
    }
}

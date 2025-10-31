<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\LandingPage;

#[Layout('components.layouts.admin')]
class HomeSectionManager extends Component
{
    use WithFileUploads;

    public $sections;
    public $editing = false;
    public $selectedSection;
    
    // Form fields
    public $page_name;
    public $section_title;
    public $section_description;
    public $display_order;
    public $is_active = true;
    public $images = [];
    public $newImages = [];

    protected $rules = [
        'page_name' => 'required|string',
        'section_title' => 'required|string',
        'section_description' => 'nullable|string',
        'display_order' => 'nullable|integer',
        'is_active' => 'boolean',
        'newImages.*' => 'nullable|image|max:2048'
    ];

    public function mount()
    {
        $this->loadSections();
    }

    public function loadSections()
    {
        $this->sections = LandingPage::orderBy('display_order')->get();
    }

    public function edit($id)
    {
        $this->selectedSection = LandingPage::findOrFail($id);
        $this->page_name = $this->selectedSection->page_name;
        $this->section_title = $this->selectedSection->section_title;
        $this->section_description = $this->selectedSection->section_description;
        $this->display_order = $this->selectedSection->display_order;
        $this->is_active = $this->selectedSection->is_active;
        $this->images = json_decode($this->selectedSection->images, true) ?? [];
        $this->newImages = [];
        $this->editing = true;
    }

    public function toggleActive($id)
    {
        $section = LandingPage::findOrFail($id);
        $section->is_active = !$section->is_active;
        $section->save();
        $this->loadSections();
        session()->flash('message', 'Section status updated successfully.');
    }

    public function save()
    {
        $this->validate();

        // Handle new image uploads
        $uploadedImages = [];
        if (!empty($this->newImages)) {
            foreach ($this->newImages as $image) {
                $path = $image->store('landing_images', 'public');
                if ($path) {
                    $uploadedImages[] = $path;
                }
            }
        }

        // Combine existing and new images
        $allImages = array_merge($this->images ?? [], $uploadedImages);
        
        if ($this->selectedSection) {
            $this->selectedSection->update([
                'page_name' => $this->page_name,
                'section_title' => $this->section_title,
                'section_description' => $this->section_description,
                'display_order' => $this->display_order,
                'is_active' => $this->is_active,
                'images' => json_encode(array_values(array_filter($allImages)))
            ]);
        }

        $this->editing = false;
        $this->loadSections();
        $this->reset(['page_name', 'section_title', 'section_description', 'display_order', 'images', 'newImages']);
        session()->flash('message', 'Section updated successfully.');
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    public function cancelEdit()
    {
        $this->editing = false;
        $this->reset(['page_name', 'section_title', 'section_description', 'display_order', 'images', 'newImages']);
    }

    public function render()
    {
        return view('livewire.admin.home-section-manager');
    }
}
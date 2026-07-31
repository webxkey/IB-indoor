<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout("components.layouts.staff")]
#[Title("Banner Management")]
class BannerManagement extends Component
{
    use WithFileUploads;

    public $banners = [];
    public $activeBannerId = null;
    
    // Form fields
    public $name = 'New Banner';
    public $layout_type = 'layout_1'; // 1 to 6
    public $gap_size = 8;
    public $border_radius = 8;
    public $display_duration = 10;
    public $sort_order = 0;
    public $is_active = false;
    
    // Holds the uploaded media files temporarily: mediaUploads[section_index]
    public $mediaUploads = [];
    
    // Holds the final/existing media metadata: mediaItems[section_index] = ['type' => 'image|video', 'url' => '...']
    public $mediaItems = [];
    
    public $isEditing = false;
    
    public $showPreviewModal = false;
    public $previewBanner = null;
    
    public $showCarouselModal = false;
    public $activeBanners = [];
    
    public function mount()
    {
        $this->loadBanners();
    }

    public function viewBanner($id)
    {
        $banner = Banner::find($id);
        if ($banner) {
            $this->previewBanner = $banner;
            $this->showPreviewModal = true;
        }
    }

    public function closePreviewModal()
    {
        $this->showPreviewModal = false;
        $this->previewBanner = null;
    }

    public function toggleActiveStatus($id)
    {
        $banner = Banner::find($id);
        if ($banner) {
            $banner->update(['is_active' => !$banner->is_active]);
            $this->loadBanners();
            session()->flash('success', 'Banner status updated successfully!');
        }
    }

    public function openCarouselModal()
    {
        $this->activeBanners = Banner::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $this->showCarouselModal = true;
    }

    public function closeCarouselModal()
    {
        $this->showCarouselModal = false;
    }

    public function loadBanners()
    {
        $user = Auth::user();
        $complexId = $user->complex_id ?? null;
        
        if ($complexId) {
            $this->banners = Banner::where('complex_id', $complexId)->get();
        } else {
            $this->banners = Banner::all();
        }
    }

    public function createNew()
    {
        $this->resetForm();
        $this->isEditing = true;
    }

    public function editBanner($id)
    {
        $banner = Banner::find($id);
        if ($banner) {
            $this->activeBannerId = $banner->id;
            $this->name = $banner->name;
            $this->layout_type = $banner->layout_type;
            
            // Handle legacy "gap-2" values gracefully by falling back to 8
            $legacyGap = str_replace('gap-', '', $banner->gap_size);
            $this->gap_size = is_numeric($legacyGap) ? $legacyGap : 8;
            
            $this->border_radius = $banner->border_radius ?? 8;
            $this->display_duration = $banner->display_duration ?? 10;
            $this->sort_order = $banner->sort_order ?? 0;
            $this->is_active = $banner->is_active;
            
            $this->mediaItems = [];
            if ($banner->media_items) {
                foreach ($banner->media_items as $index => $item) {
                    $this->mediaItems[$index] = $item;
                }
            }
            
            $this->mediaUploads = [];
            $this->isEditing = true;
        }
    }

    public function resetForm()
    {
        $this->activeBannerId = null;
        $this->name = 'New Banner';
        $this->layout_type = 'layout_1';
        $this->gap_size = 8;
        $this->border_radius = 8;
        $this->display_duration = 10;
        $this->sort_order = 0;
        $this->is_active = false;
        $this->mediaUploads = [];
        $this->mediaItems = [];
        $this->isEditing = false;
    }

    public function updatedMediaUploads($value, $key)
    {
        $this->validate([
            "mediaUploads.{$key}" => 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:51200', // max 50MB
        ]);
    }

    public function removeMedia($index)
    {
        if (isset($this->mediaUploads[$index])) {
            unset($this->mediaUploads[$index]);
        }
        if (isset($this->mediaItems[$index])) {
            unset($this->mediaItems[$index]);
        }
    }

    public function saveBanner()
    {
        $nameValidation = 'required|string|max:255|unique:banners,name';
        if ($this->activeBannerId) {
            $nameValidation .= ',' . $this->activeBannerId;
        }

        $this->validate([
            'name' => $nameValidation,
            'layout_type' => 'required|string|in:layout_1,layout_2,layout_3,layout_4,layout_5,layout_6',
            'gap_size' => 'required|numeric|min:0|max:100',
            'border_radius' => 'required|numeric|min:0|max:100',
            'display_duration' => 'required|numeric|min:1|max:3600',
            'sort_order' => 'required|numeric|min:0|max:1000',
        ]);
        
        $sectionsCount = (int) str_replace('layout_', '', $this->layout_type);
        for ($i = 0; $i < $sectionsCount; $i++) {
            $hasUploaded = isset($this->mediaUploads[$i]);
            $hasExisting = isset($this->mediaItems[$i]) && !empty($this->mediaItems[$i]['url']);
            
            if (!$hasUploaded && !$hasExisting) {
                $this->addError("mediaUploads.$i", "Media is required for Section " . ($i + 1));
                return;
            }
        }

        $user = Auth::user();
        $complexId = $user->complex_id ?? null;

        // Process uploaded files
        foreach ($this->mediaUploads as $index => $file) {
            if ($file) {
                $path = $file->store('banners', 'public');
                $mimeType = $file->getMimeType();
                $type = str_starts_with($mimeType, 'video') ? 'video' : 'image';
                
                $this->mediaItems[$index] = [
                    'type' => $type,
                    'url' => Storage::url($path),
                ];
            }
        }

        if ($this->activeBannerId) {
            $banner = Banner::find($this->activeBannerId);
            $banner->update([
                'name' => $this->name,
                'layout_type' => $this->layout_type,
                'gap_size' => $this->gap_size,
                'border_radius' => $this->border_radius,
                'display_duration' => $this->display_duration,
                'sort_order' => $this->sort_order,
                'media_items' => $this->mediaItems,
                'is_active' => $this->is_active,
            ]);
        } else {
            Banner::create([
                'name' => $this->name,
                'layout_type' => $this->layout_type,
                'gap_size' => $this->gap_size,
                'border_radius' => $this->border_radius,
                'display_duration' => $this->display_duration,
                'sort_order' => $this->sort_order,
                'media_items' => $this->mediaItems,
                'is_active' => $this->is_active,
                'complex_id' => $complexId,
            ]);
        }

        $this->loadBanners();
        $this->resetForm();
        session()->flash('success', 'Banner layout saved successfully.');
    }

    public function deleteBanner($id)
    {
        $banner = Banner::find($id);
        if ($banner) {
            $banner->delete();
            $this->loadBanners();
            session()->flash('success', 'Banner deleted successfully.');
        }
    }

    public function render()
    {
        return view('livewire.staff.banner-management');
    }
}

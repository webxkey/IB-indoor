<?php

namespace App\Livewire\Staff;

use App\Models\BookingAnnouncement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Announcements')]
#[Layout('components.layouts.staff')]
class Announcements extends Component
{
    use WithFileUploads;

    public ?int $venueId = null;
    public $announcements = [];

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $title = '';
    public string $subtitle = '';
    public string $short_description = '';
    public string $full_description = '';
    public string $label = 'Update';
    public string $fallback_bg_color = '#19722d';
    public string $expires_at = '';
    public string $read_more_label = '';
    public string $read_more_url = '';
    public $image; // upload OR keep existing path
    public ?string $existing_image = null;

    protected function rules(): array
    {
        return [
            'title'             => 'required|string|max:255',
            'subtitle'          => 'required|string|max:255',
            'short_description' => 'required|string|max:320',
            'full_description'  => 'required|string',
            'label'             => 'required|string|max:50',
            'fallback_bg_color' => 'required|string|max:20',
            'expires_at'        => 'required|date',
            'read_more_label'   => 'nullable|string|max:50',
            'read_more_url'     => 'nullable|string|max:200',
            'image'             => 'nullable|image|max:2048',
        ];
    }

    public function mount(): void
    {
        $this->venueId = Auth::user()?->complex_id;
        $this->expires_at = Carbon::now()->addDays(30)->format('Y-m-d\TH:i');
        $this->loadList();
    }

    protected function loadList(): void
    {
        if (!$this->venueId) {
            $this->announcements = [];
            return;
        }
        $this->announcements = BookingAnnouncement::where('venue_id', $this->venueId)
            ->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'title', 'subtitle', 'short_description', 'full_description',
            'read_more_label', 'read_more_url', 'image', 'existing_image']);
        $this->label             = 'Update';
        $this->fallback_bg_color = '#19722d';
        $this->expires_at        = Carbon::now()->addDays(30)->format('Y-m-d\TH:i');
        $this->showForm          = true;
    }

    public function openEdit(int $id): void
    {
        $a = BookingAnnouncement::where('id', $id)->where('venue_id', $this->venueId)->first();
        if (!$a) { session()->flash('error', 'Announcement not found.'); return; }

        $this->editingId         = $a->id;
        $this->title             = (string) $a->title;
        $this->subtitle          = (string) $a->subtitle;
        $this->short_description = (string) $a->short_description;
        $this->full_description  = (string) $a->full_description;
        $this->label             = (string) $a->label;
        $this->fallback_bg_color = (string) $a->fallback_bg_color;
        $this->expires_at        = $a->expires_at ? Carbon::parse($a->expires_at)->format('Y-m-d\TH:i') : '';
        $this->read_more_label   = (string) $a->read_more_label;
        $this->read_more_url     = (string) $a->read_more_url;
        $this->image             = null;
        $this->existing_image    = $a->image;
        $this->showForm          = true;
    }

    public function closeForm(): void
    {
        $this->showForm  = false;
        $this->editingId = null;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate();

        if (!$this->venueId) {
            session()->flash('error', 'Your account has no venue assigned.');
            return;
        }

        $imagePath = $this->existing_image;
        if ($this->image) {
            $imagePath = $this->image->store('announcements', 'public');
        }

        // Content-only payload — visibility, pin, and sort_order are admin-controlled.
        $contentPayload = [
            'title'             => $this->title,
            'subtitle'          => $this->subtitle,
            'short_description' => $this->short_description,
            'full_description'  => $this->full_description,
            'image'             => $imagePath,
            'fallback_bg_color' => $this->fallback_bg_color,
            'label'             => $this->label,
            'expires_at'        => Carbon::parse($this->expires_at),
            'read_more_label'   => $this->read_more_label ?: '',
            'read_more_url'     => $this->read_more_url ?: '',
        ];

        if ($this->editingId) {
            // Edit: leave is_active/is_pinned/sort_order untouched (admin-controlled).
            $a = BookingAnnouncement::where('id', $this->editingId)
                ->where('venue_id', $this->venueId)->first();
            if ($a) {
                $a->update($contentPayload);
                session()->flash('message', 'Announcement updated. Visibility/pin/sort changes require admin approval.');
            }
        } else {
            // New: starts as pending — inactive until admin approves.
            BookingAnnouncement::create(array_merge($contentPayload, [
                'venue_id'   => $this->venueId,
                'is_active'  => false,
                'is_pinned'  => false,
                'sort_order' => 0,
            ]));
            session()->flash('message', 'Announcement submitted — pending admin approval.');
        }

        $this->closeForm();
        $this->loadList();
    }

    public function deleteAnnouncement(int $id): void
    {
        BookingAnnouncement::where('id', $id)->where('venue_id', $this->venueId)->delete();
        session()->flash('message', 'Announcement deleted.');
        $this->loadList();
    }

    public function render()
    {
        return view('livewire.staff.announcements');
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\BookingAnnouncement;
use App\Models\BookingVenue;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Announcements')]
#[Layout('components.layouts.admin')]
class Announcements extends Component
{
    use WithPagination;

    public string $search = '';
    public string $venueFilter = '';      // venue id, empty = all
    public string $statusFilter = '';     // 'active' | 'inactive' | ''

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingVenueFilter(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function toggleActive(int $id): void
    {
        $a = BookingAnnouncement::find($id);
        if (!$a) return;
        $a->update(['is_active' => !$a->is_active]);
        session()->flash('message', $a->is_active ? 'Announcement approved & published.' : 'Announcement hidden.');
    }

    public function togglePin(int $id): void
    {
        $a = BookingAnnouncement::find($id);
        if (!$a) return;
        if (!$a->is_active) {
            session()->flash('error', 'Approve (activate) the announcement before pinning.');
            return;
        }
        $a->update(['is_pinned' => !$a->is_pinned]);
    }

    public function updateSortOrder(int $id, int $sortOrder): void
    {
        $a = BookingAnnouncement::find($id);
        if (!$a) return;
        if (!$a->is_active) {
            session()->flash('error', 'Approve (activate) the announcement before changing sort order.');
            return;
        }
        $a->update(['sort_order' => max(0, $sortOrder)]);
    }

    public function deleteAnnouncement(int $id): void
    {
        BookingAnnouncement::destroy($id);
        session()->flash('message', 'Announcement deleted.');
    }

    public function render()
    {
        $q = BookingAnnouncement::with('venue:id,name')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');

        if ($this->search !== '') {
            $q->where(function ($w) {
                $w->where('title', 'ilike', '%' . $this->search . '%')
                  ->orWhere('subtitle', 'ilike', '%' . $this->search . '%')
                  ->orWhere('label', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->venueFilter !== '') {
            if ($this->venueFilter === 'global') {
                $q->whereNull('venue_id');
            } else {
                $q->where('venue_id', (int) $this->venueFilter);
            }
        }

        if ($this->statusFilter !== '') {
            $q->where('is_active', $this->statusFilter === 'active');
        }

        $announcements = $q->paginate(15);
        $venues = BookingVenue::orderBy('name')->get(['id', 'name']);

        $stats = [
            'total'    => BookingAnnouncement::count(),
            'active'   => BookingAnnouncement::where('is_active', true)->count(),
            'inactive' => BookingAnnouncement::where('is_active', false)->count(),
            'venues'   => BookingAnnouncement::whereNotNull('venue_id')->distinct('venue_id')->count('venue_id'),
        ];

        return view('livewire.admin.announcements', [
            'announcements' => $announcements,
            'venues'        => $venues,
            'stats'         => $stats,
        ]);
    }
}

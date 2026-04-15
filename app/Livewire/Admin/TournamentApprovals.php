<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\LeagueLeague;
use App\Models\LeagueTeam;
use App\Models\LeagueMatch;
use App\Models\LeagueMatchInning;
use App\Models\LeaguePointsTable;

#[Layout('components.layouts.admin')]
#[Title('Tournament Approvals')]
class TournamentApprovals extends Component
{
    use WithPagination;

    public string  $filterStatus  = 'published'; // published = pending approval
    public string  $search        = '';
    public ?string $detailId      = null;
    public string  $rejectReason  = '';
    public bool    $showRejectModal = false;
    public ?string $rejectTargetId = null;

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    // ── Detail view ─────────────────────────────────────────────────────────

    public function openDetail(string $id): void
    {
        $this->detailId      = $id;
        $this->rejectReason  = '';
        $this->showRejectModal = false;
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
    }

    // ── Approve ──────────────────────────────────────────────────────────────

    public function approve(string $id): void
    {
        $t = LeagueLeague::find($id);
        if (!$t) return;

        $t->update([
            'status'       => 'active',
            'is_active'    => true,
            'published_at' => now(),
        ]);

        session()->flash('success', '"' . $t->name . '" approved and set to Active.');
        $this->detailId = null;
    }

    // ── Reject ───────────────────────────────────────────────────────────────

    public function openReject(string $id): void
    {
        $this->rejectTargetId  = $id;
        $this->rejectReason    = '';
        $this->showRejectModal = true;
    }

    public function confirmReject(): void
    {
        $this->validate(['rejectReason' => 'required|string|min:5|max:500']);

        $t = LeagueLeague::find($this->rejectTargetId);
        if (!$t) return;

        $t->update([
            'status'    => 'cancelled',
            'is_active' => false,
            'metadata'  => array_merge((array)($t->metadata ?? []), [
                'rejection_reason' => $this->rejectReason,
                'rejected_at'      => now()->toISOString(),
            ]),
        ]);

        session()->flash('success', '"' . $t->name . '" rejected.');
        $this->showRejectModal = false;
        $this->rejectTargetId  = null;
        $this->rejectReason    = '';
        $this->detailId        = null;
    }

    public function cancelReject(): void
    {
        $this->showRejectModal = false;
        $this->rejectTargetId  = null;
        $this->rejectReason    = '';
    }

    // ── Quick status change ──────────────────────────────────────────────────

    public function setStatus(string $id, string $status): void
    {
        $t = LeagueLeague::find($id);
        if (!$t) return;

        $updates = ['status' => $status];
        if ($status === 'active')    $updates['is_active'] = true;
        if ($status === 'cancelled') $updates['is_active'] = false;
        if ($status === 'completed') $updates['is_active'] = false;

        $t->update($updates);
        session()->flash('success', 'Status updated to "' . ucfirst($status) . '".');
    }

    // ── Delete ───────────────────────────────────────────────────────────────

    public function delete(string $id): void
    {
        $t = LeagueLeague::find($id);
        if (!$t) return;

        LeagueMatch::where('league_id', $id)->each(function ($m) {
            LeagueMatchInning::where('match_id', $m->id)->delete();
            $m->delete();
        });
        LeaguePointsTable::where('league_id', $id)->delete();
        LeagueTeam::where('league_id', $id)->delete();
        $t->delete();

        session()->flash('success', 'Tournament deleted.');
        $this->detailId = null;
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $query = LeagueLeague::where('sport_type', 'cricket')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->filterStatus !== 'all', fn($q) => $q->where('status', $this->filterStatus))
            ->orderByDesc('created_at');

        $tournaments = $query->paginate(12);

        $counts = [
            'all'       => LeagueLeague::where('sport_type', 'cricket')->count(),
            'published' => LeagueLeague::where('sport_type', 'cricket')->where('status', 'published')->count(),
            'active'    => LeagueLeague::where('sport_type', 'cricket')->where('status', 'active')->count(),
            'draft'     => LeagueLeague::where('sport_type', 'cricket')->where('status', 'draft')->count(),
            'completed' => LeagueLeague::where('sport_type', 'cricket')->where('status', 'completed')->count(),
            'cancelled' => LeagueLeague::where('sport_type', 'cricket')->where('status', 'cancelled')->count(),
        ];

        $detail = $this->detailId
            ? LeagueLeague::with([
                'teams',
                'matches' => fn($q) => $q->with(['team1','team2','winner','innings'])->orderBy('match_number'),
                'pointsTable.leagueTeam',
              ])->find($this->detailId)
            : null;

        return view('livewire.admin.tournament-approvals', [
            'tournaments' => $tournaments,
            'counts'      => $counts,
            'detail'      => $detail,
        ]);
    }
}

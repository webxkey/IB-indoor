<div>

{{-- Flash --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     REJECT MODAL
══════════════════════════════════════════════════════════ --}}
@if($showRejectModal)
<div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);z-index:1055;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="fas fa-times-circle me-2"></i>Reject Tournament</h5>
                <button type="button" class="btn-close" wire:click="cancelReject"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Provide a reason for rejection. The organiser will see this.</p>
                <textarea wire:model="rejectReason" rows="3"
                    class="form-control @error('rejectReason') is-invalid @enderror"
                    placeholder="e.g. Incomplete team information, invalid format..."></textarea>
                @error('rejectReason')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="modal-footer border-0">
                <button wire:click="cancelReject" class="btn btn-outline-secondary">Cancel</button>
                <button wire:click="confirmReject" class="btn btn-danger">
                    <i class="fas fa-times me-1"></i> Reject Tournament
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     DETAIL PANEL (slide-in overlay)
══════════════════════════════════════════════════════════ --}}
@if($detail)
<div style="position:fixed;top:0;right:0;bottom:0;width:min(680px,100vw);background:#fff;z-index:1050;
     box-shadow:-4px 0 30px rgba(0,0,0,.15);overflow-y:auto;display:flex;flex-direction:column;">

    {{-- Detail header --}}
    <div style="background:linear-gradient(135deg,#19722d,#28a745);color:#fff;padding:20px 24px;flex-shrink:0;">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fas fa-trophy" style="font-size:1.2rem;"></i>
                    <span style="font-size:.75rem;opacity:.8;text-transform:uppercase;letter-spacing:.05em;">Cricket Tournament</span>
                </div>
                <h4 class="mb-1 fw-bold">{{ $detail->name }}</h4>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge {{ $detail->status==='published'?'bg-warning text-dark':($detail->status==='active'?'bg-success':($detail->status==='cancelled'?'bg-danger':'bg-secondary')) }}">
                        {{ ucfirst($detail->status) }}
                    </span>
                    @if($detail->season_name)<span class="badge bg-white bg-opacity-25">{{ $detail->season_name }}</span>@endif
                    <span class="badge bg-white bg-opacity-25">{{ $detail->season_year }}</span>
                </div>
            </div>
            <button wire:click="closeDetail" style="background:rgba(255,255,255,.2);border:none;color:#fff;border-radius:8px;padding:6px 10px;cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div style="padding:20px 24px;flex:1;">

        {{-- Action buttons --}}
        @if($detail->status === 'published')
        <div class="d-flex gap-2 mb-4">
            <button wire:click="approve('{{ $detail->id }}')" class="btn btn-success flex-grow-1">
                <i class="fas fa-check me-2"></i>Approve & Activate
            </button>
            <button wire:click="openReject('{{ $detail->id }}')" class="btn btn-danger flex-grow-1">
                <i class="fas fa-times me-2"></i>Reject
            </button>
        </div>
        @else
        <div class="d-flex gap-2 mb-4 flex-wrap">
            @if($detail->status !== 'active')
            <button wire:click="approve('{{ $detail->id }}')" class="btn btn-success btn-sm">
                <i class="fas fa-check me-1"></i>Set Active
            </button>
            @endif
            @if($detail->status !== 'draft')
            <button wire:click="setStatus('{{ $detail->id }}','draft')" class="btn btn-outline-secondary btn-sm">Set Draft</button>
            @endif
            @if($detail->status !== 'completed')
            <button wire:click="setStatus('{{ $detail->id }}','completed')" class="btn btn-outline-primary btn-sm">Set Completed</button>
            @endif
            <button wire:click="delete('{{ $detail->id }}')" onclick="return confirm('Delete this tournament?')" class="btn btn-outline-danger btn-sm ms-auto">
                <i class="fas fa-trash me-1"></i>Delete
            </button>
        </div>
        @endif

        {{-- Info grid --}}
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="p-3 rounded" style="background:#f8f9fa;">
                    <div class="text-muted small">Format</div>
                    <div class="fw-semibold">{{ ucfirst(str_replace('_',' ',$detail->format ?? 'round_robin')) }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 rounded" style="background:#f8f9fa;">
                    <div class="text-muted small">Overs / Innings</div>
                    <div class="fw-semibold">{{ $detail->cricket_config['overs'] ?? '?' }} overs</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 rounded" style="background:#f8f9fa;">
                    <div class="text-muted small">Teams (registered / planned)</div>
                    <div class="fw-semibold">{{ $detail->teams->count() }} / {{ $detail->num_teams }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 rounded" style="background:#f8f9fa;">
                    <div class="text-muted small">Matches</div>
                    <div class="fw-semibold">
                        {{ $detail->matches->where('status','completed')->count() }} / {{ $detail->matches->count() }} played
                    </div>
                </div>
            </div>
            @if($detail->start_date)
            <div class="col-6">
                <div class="p-3 rounded" style="background:#f8f9fa;">
                    <div class="text-muted small">Start Date</div>
                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($detail->start_date)->format('d M Y') }}</div>
                </div>
            </div>
            @endif
            @if($detail->end_date)
            <div class="col-6">
                <div class="p-3 rounded" style="background:#f8f9fa;">
                    <div class="text-muted small">End Date</div>
                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($detail->end_date)->format('d M Y') }}</div>
                </div>
            </div>
            @endif
            @if($detail->prize_pool)
            <div class="col-6">
                <div class="p-3 rounded" style="background:#fff3cd;">
                    <div class="text-muted small">Prize Pool</div>
                    <div class="fw-bold text-warning">Rs. {{ number_format($detail->prize_pool) }}</div>
                </div>
            </div>
            @endif
            @if($detail->contact_email || $detail->contact_phone)
            <div class="col-6">
                <div class="p-3 rounded" style="background:#f8f9fa;">
                    <div class="text-muted small">Contact</div>
                    <div class="fw-semibold small">{{ $detail->contact_email ?? $detail->contact_phone }}</div>
                </div>
            </div>
            @endif
        </div>

        @if($detail->description)
        <div class="mb-4">
            <div class="text-muted small mb-1">Description</div>
            <p class="mb-0">{{ $detail->description }}</p>
        </div>
        @endif

        {{-- Rejection reason if cancelled --}}
        @if($detail->status === 'cancelled' && isset($detail->metadata['rejection_reason']))
        <div class="alert alert-danger py-2 mb-4">
            <strong>Rejection reason:</strong> {{ $detail->metadata['rejection_reason'] }}
        </div>
        @endif

        {{-- Teams --}}
        @if($detail->teams->count())
        <div class="mb-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-users me-2 text-success"></i>Teams ({{ $detail->teams->count() }})</h6>
            <div class="row g-2">
                @foreach($detail->teams as $team)
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 p-2 border rounded">
                        <div style="width:28px;height:28px;border-radius:50%;background:{{ $team->jersey_color ?? '#19722d' }};
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid rgba(0,0,0,.1);">
                            <span style="font-size:.6rem;font-weight:700;color:#fff;">{{ strtoupper(substr($team->team_short_name ?? $team->team_name_override ?? 'T',0,2)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-semibold" style="font-size:.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $team->team_name_override }}</div>
                            @if($team->team_short_name)<small class="text-muted">{{ $team->team_short_name }}</small>@endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Points Table preview --}}
        @if($detail->pointsTable->count() && $detail->matches->where('status','completed')->count() > 0)
        <div class="mb-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-table me-2 text-success"></i>Points Table</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:.82rem;">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Team</th>
                            <th class="text-center">P</th>
                            <th class="text-center">W</th>
                            <th class="text-center">L</th>
                            <th class="text-center fw-bold">Pts</th>
                            <th class="text-center">NRR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detail->pointsTable->sortBy('rank') as $row)
                        <tr>
                            <td class="fw-bold {{ $row->rank <= 4 ? 'text-success' : 'text-muted' }}">{{ $row->rank ?: '-' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <div style="width:10px;height:10px;border-radius:50%;background:{{ $row->leagueTeam->jersey_color ?? '#19722d' }};flex-shrink:0;"></div>
                                    {{ $row->leagueTeam->team_name_override ?? 'Unknown' }}
                                </div>
                            </td>
                            <td class="text-center">{{ $row->matches_played }}</td>
                            <td class="text-center text-success">{{ $row->wins }}</td>
                            <td class="text-center text-danger">{{ $row->losses }}</td>
                            <td class="text-center fw-bold text-success">{{ $row->points }}</td>
                            <td class="text-center">
                                @if($row->net_run_rate !== null)
                                    <span class="{{ $row->net_run_rate >= 0 ? 'text-success' : 'text-danger' }}">{{ $row->net_run_rate >= 0 ? '+' : '' }}{{ number_format($row->net_run_rate,3) }}</span>
                                @else -@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Recent matches --}}
        @if($detail->matches->count())
        <div class="mb-2">
            <h6 class="fw-bold mb-2"><i class="fas fa-cricket-bat-ball me-2 text-success"></i>Fixtures ({{ $detail->matches->count() }} total)</h6>
            @foreach($detail->matches->take(8) as $match)
            @php
                $t1Score = $match->innings->where('batting_team_id',$match->team1_id)->first();
                $t2Score = $match->innings->where('batting_team_id',$match->team2_id)->first();
            @endphp
            <div class="d-flex align-items-center gap-2 py-2 border-bottom" style="font-size:.82rem;">
                <span class="text-muted" style="width:36px;flex-shrink:0;">
                    @if($match->match_type !== 'league')
                        <span class="badge bg-warning text-dark" style="font-size:.6rem;">{{ strtoupper(substr($match->match_type??'',0,2)) }}</span>
                    @else
                        M{{ $match->match_number }}
                    @endif
                </span>
                <div class="d-flex align-items-center gap-2 flex-grow-1">
                    <div class="text-end" style="min-width:90px;">
                        <span class="fw-semibold {{ (string)$match->winner_id===(string)$match->team1_id?'text-success':'' }}">
                            {{ $match->team1->team_short_name ?? $match->team1->team_name_override ?? 'TBD' }}
                        </span>
                        @if($t1Score)<span class="text-success ms-1">{{ $t1Score->total_runs }}/{{ $t1Score->wickets_fallen }}</span>@endif
                    </div>
                    <span class="text-muted">vs</span>
                    <div style="min-width:90px;">
                        <span class="fw-semibold {{ (string)$match->winner_id===(string)$match->team2_id?'text-success':'' }}">
                            {{ $match->team2->team_short_name ?? $match->team2->team_name_override ?? 'TBD' }}
                        </span>
                        @if($t2Score)<span class="text-success ms-1">{{ $t2Score->total_runs }}/{{ $t2Score->wickets_fallen }}</span>@endif
                    </div>
                </div>
                <span class="badge {{ $match->status==='completed'?'bg-success':($match->status==='live'?'bg-danger':'bg-light text-dark border') }}" style="font-size:.65rem;">
                    {{ ucfirst($match->status) }}
                </span>
                <span class="text-muted" style="font-size:.72rem;min-width:56px;text-align:right;">
                    {{ $match->scheduled_date ? \Carbon\Carbon::parse($match->scheduled_date)->format('d M') : '-' }}
                </span>
            </div>
            @endforeach
            @if($detail->matches->count() > 8)
            <div class="text-muted small mt-2">+ {{ $detail->matches->count() - 8 }} more matches</div>
            @endif
        </div>
        @endif

        <div class="text-muted small mt-4">
            Created {{ \Carbon\Carbon::parse($detail->created_at)->diffForHumans() }}
            @if($detail->created_by_id) &bull; by User #{{ $detail->created_by_id }} @endif
        </div>
    </div>
</div>
{{-- backdrop --}}
<div wire:click="closeDetail" style="position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1049;cursor:pointer;"></div>
@endif

{{-- ══════════════════════════════════════════════════════════
     MAIN LIST
══════════════════════════════════════════════════════════ --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-trophy text-warning me-2"></i>Tournaments</h4>
        <small class="text-muted">Review, approve or reject cricket tournaments submitted by venues</small>
    </div>
</div>

{{-- Status filter tabs --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2 px-3">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            @php
                $tabs = [
                    ['key'=>'published','label'=>'Pending Approval','icon'=>'fa-clock','color'=>'warning'],
                    ['key'=>'active',   'label'=>'Active',          'icon'=>'fa-check-circle','color'=>'success'],
                    ['key'=>'draft',    'label'=>'Draft',           'icon'=>'fa-edit','color'=>'secondary'],
                    ['key'=>'completed','label'=>'Completed',       'icon'=>'fa-flag-checkered','color'=>'primary'],
                    ['key'=>'cancelled','label'=>'Rejected',        'icon'=>'fa-times-circle','color'=>'danger'],
                    ['key'=>'all',      'label'=>'All',             'icon'=>'fa-list','color'=>'dark'],
                ];
            @endphp
            @foreach($tabs as $tab)
            <button wire:click="$set('filterStatus','{{ $tab['key'] }}')"
                class="btn btn-sm {{ $filterStatus===$tab['key'] ? 'btn-'.$tab['color'] : 'btn-outline-'.$tab['color'] }}">
                <i class="fas {{ $tab['icon'] }} me-1"></i>
                {{ $tab['label'] }}
                <span class="badge {{ $filterStatus===$tab['key'] ? 'bg-white text-'.$tab['color'] : 'bg-'.$tab['color'].' text-white' }} ms-1" style="font-size:.65rem;">
                    {{ $counts[$tab['key']] ?? 0 }}
                </span>
            </button>
            @endforeach

            <div class="ms-auto">
                <input wire:model.live.debounce.300ms="search" type="text" class="form-control form-control-sm" placeholder="Search tournaments..." style="width:200px;">
            </div>
        </div>
    </div>
</div>

{{-- Pending banner --}}
@if($filterStatus === 'published' && $counts['published'] > 0)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4 py-2">
    <i class="fas fa-clock fa-lg"></i>
    <span><strong>{{ $counts['published'] }} tournament{{ $counts['published']>1?'s':'' }}</strong> waiting for your approval. Click on any to review details.</span>
</div>
@endif

@if($tournaments->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="fas fa-trophy fa-3x text-muted mb-3 d-block"></i>
        <h5 class="text-muted">
            @if($filterStatus === 'published') No pending approvals @elseif($filterStatus === 'all') No tournaments yet @else No {{ $filterStatus }} tournaments @endif
        </h5>
        @if($search)<p class="text-muted">No results for "{{ $search }}"</p>@endif
    </div>
</div>
@else
<div class="row g-3">
    @foreach($tournaments as $t)
    @php
        $teamsCount = $t->teams()->count();
        $matchCount = $t->matches()->count();
        $completedCount = $t->matches()->where('status','completed')->count();
        $pct = $matchCount > 0 ? round($completedCount/$matchCount*100) : 0;
        $isPending = $t->status === 'published';
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 {{ $isPending ? 'border-warning' : '' }}"
             style="{{ $isPending ? 'border:2px solid #ffc107 !important;' : 'border-top:3px solid #19722d;' }}border-radius:10px;cursor:pointer;"
             wire:click="openDetail('{{ $t->id }}')">
            <div class="card-body">
                {{-- Top row --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge
                        {{ $t->status==='published'?'bg-warning text-dark':
                           ($t->status==='active'?'bg-success':
                           ($t->status==='cancelled'?'bg-danger':
                           ($t->status==='completed'?'bg-primary':'bg-secondary'))) }}">
                        @if($t->status==='published')<i class="fas fa-clock me-1"></i>Pending Approval
                        @elseif($t->status==='active')<i class="fas fa-check-circle me-1"></i>Active
                        @elseif($t->status==='cancelled')<i class="fas fa-times-circle me-1"></i>Rejected
                        @elseif($t->status==='completed')<i class="fas fa-flag-checkered me-1"></i>Completed
                        @else {{ ucfirst($t->status) }}
                        @endif
                    </span>
                    <small class="text-muted">{{ $t->season_year }}</small>
                </div>

                <h5 class="fw-bold mb-1">{{ $t->name }}</h5>
                @if($t->season_name)<small class="text-muted d-block mb-1">{{ $t->season_name }}</small>@endif
                @if($t->description)<p class="text-muted small mb-2">{{ Str::limit($t->description,70) }}</p>@endif

                {{-- Stats --}}
                <div class="row text-center g-1 mb-2">
                    <div class="col-3">
                        <div class="fw-bold text-success">{{ $teamsCount }}</div>
                        <div style="font-size:.68rem;" class="text-muted">Teams</div>
                    </div>
                    <div class="col-3">
                        <div class="fw-bold text-primary">{{ $matchCount }}</div>
                        <div style="font-size:.68rem;" class="text-muted">Matches</div>
                    </div>
                    <div class="col-3">
                        <div class="fw-bold">{{ $t->cricket_config['overs'] ?? '?' }}</div>
                        <div style="font-size:.68rem;" class="text-muted">Overs</div>
                    </div>
                    <div class="col-3">
                        @if($t->prize_pool)
                        <div class="fw-bold text-warning" style="font-size:.82rem;">Rs.{{ number_format($t->prize_pool/1000,0) }}k</div>
                        @else
                        <div class="fw-bold text-muted">-</div>
                        @endif
                        <div style="font-size:.68rem;" class="text-muted">Prize</div>
                    </div>
                </div>

                @if($matchCount > 0)
                <div class="mb-2">
                    <div class="progress" style="height:4px;border-radius:2px;">
                        <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                    </div>
                    <div style="font-size:.7rem;" class="text-muted mt-1">{{ $completedCount }}/{{ $matchCount }} matches played</div>
                </div>
                @endif

                <div class="d-flex gap-1 mt-2">
                    <span style="font-size:.75rem;" class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $t->start_date ? \Carbon\Carbon::parse($t->start_date)->format('d M Y') : 'No date set' }}
                    </span>
                    <span class="ms-auto text-muted" style="font-size:.72rem;">{{ \Carbon\Carbon::parse($t->created_at)->diffForHumans() }}</span>
                </div>

                {{-- Pending call-to-action --}}
                @if($isPending)
                <div class="d-flex gap-2 mt-3" wire:click.stop>
                    <button wire:click="approve('{{ $t->id }}')" class="btn btn-success btn-sm flex-grow-1">
                        <i class="fas fa-check me-1"></i>Approve
                    </button>
                    <button wire:click="openReject('{{ $t->id }}')" class="btn btn-outline-danger btn-sm flex-grow-1">
                        <i class="fas fa-times me-1"></i>Reject
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-3">{{ $tournaments->links() }}</div>
@endif

</div>

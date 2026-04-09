<div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     LIST
══════════════════════════════════════════════════════════ --}}
@if($view === 'list')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-trophy text-warning me-2"></i>Tournaments</h4>
        <small class="text-muted">Cricket • IPL-style fixtures</small>
    </div>
    <button wire:click="showCreate" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> New Tournament
    </button>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body pb-1">
        <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Search tournaments...">
    </div>
</div>

@if($tournaments->isEmpty())
<div class="text-center py-5">
    <i class="fas fa-trophy fa-3x text-muted mb-3 d-block"></i>
    <h5 class="text-muted">No tournaments yet</h5>
    <p class="text-muted">Create your first cricket tournament.</p>
    <button wire:click="showCreate" class="btn btn-success"><i class="fas fa-plus me-1"></i> Create Tournament</button>
</div>
@else
<div class="row g-3">
    @foreach($tournaments as $t)
    @php
        $total     = $t->matches()->count();
        $completed = $t->matches()->where('status','completed')->count();
        $pct       = $total > 0 ? round($completed / $total * 100) : 0;
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-top:4px solid #19722d;border-radius:10px;">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge {{ $t->status==='active'?'bg-success':($t->status==='completed'?'bg-secondary':($t->status==='draft'?'bg-warning text-dark':'bg-info')) }}">
                        {{ ucfirst($t->status) }}
                    </span>
                    <small class="text-muted">{{ $t->season_year }}</small>
                </div>
                <h5 class="fw-bold mb-1">{{ $t->name }}</h5>
                @if($t->season_name)<small class="text-muted d-block mb-1">{{ $t->season_name }}</small>@endif
                <p class="text-muted small mb-2">{{ Str::limit($t->description,70) }}</p>
                <div class="row text-center g-1 mb-2">
                    <div class="col-4">
                        <div class="fw-bold text-success">{{ $t->teams()->count() }}</div>
                        <small class="text-muted" style="font-size:.7rem;">Teams</small>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-primary">{{ $total }}</div>
                        <small class="text-muted" style="font-size:.7rem;">Matches</small>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-warning">{{ $t->cricket_config['overs'] ?? '?' }}</div>
                        <small class="text-muted" style="font-size:.7rem;">Overs</small>
                    </div>
                </div>
                @if($total > 0)
                <div class="mb-3">
                    <div class="d-flex justify-content-between" style="font-size:.75rem;">
                        <span class="text-muted">Progress</span>
                        <span class="text-success fw-semibold">{{ $completed }}/{{ $total }}</span>
                    </div>
                    <div class="progress" style="height:5px;border-radius:3px;">
                        <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endif
                <div class="d-flex gap-2">
                    <button wire:click="showDetail('{{ $t->id }}')" class="btn btn-success btn-sm flex-grow-1">
                        <i class="fas fa-eye me-1"></i> Open
                    </button>
                    <button wire:click="deleteTournament('{{ $t->id }}')"
                        onclick="return confirm('Delete \'{{ addslashes($t->name) }}\'? All matches and teams will be removed.')"
                        class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-3">{{ $tournaments->links() }}</div>
@endif
@endif

{{-- ══════════════════════════════════════════════════════════
     CREATE
══════════════════════════════════════════════════════════ --}}
@if($view === 'create')
<div class="d-flex align-items-center mb-4 gap-2">
    <button wire:click="backToList" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i></button>
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-success me-2"></i>New Tournament</h4>
        <small class="text-muted">Cricket • Fixtures auto-generated after adding teams</small>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form wire:submit.prevent="createTournament">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Tournament Name <span class="text-danger">*</span></label>
                    <input wire:model="form_name" type="text" class="form-control @error('form_name') is-invalid @enderror" placeholder="e.g. IndoorB Premier League 2026">
                    @error('form_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select wire:model="form_status" class="form-select">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea wire:model="form_description" class="form-control" rows="2" placeholder="Tournament description..."></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Format <span class="text-danger">*</span></label>
                    <select wire:model="form_format" class="form-select @error('form_format') is-invalid @enderror">
                        <option value="round_robin">Round Robin (IPL style)</option>
                        <option value="knockout">Knockout</option>
                        <option value="group_knockout">Group + Knockout</option>
                        <option value="league">League</option>
                        <option value="bilateral">Bilateral Series</option>
                    </select>
                    @error('form_format')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Overs per Innings <span class="text-danger">*</span></label>
                    <input wire:model="form_overs" type="number" class="form-control @error('form_overs') is-invalid @enderror" min="1" max="100">
                    @error('form_overs')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">No. of Teams <span class="text-danger">*</span></label>
                    <input wire:model="form_num_teams" type="number" class="form-control @error('form_num_teams') is-invalid @enderror" min="2" max="32">
                    @error('form_num_teams')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Season Year</label>
                    <input wire:model="form_season_year" type="number" class="form-control" min="2020" max="2035">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Season Name</label>
                    <input wire:model="form_season_name" type="text" class="form-control" placeholder="e.g. Season 1">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Prize Pool (Rs.)</label>
                    <input wire:model="form_prize_pool" type="number" class="form-control" placeholder="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input wire:model="form_start_date" type="date" class="form-control @error('form_start_date') is-invalid @enderror">
                    @error('form_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">End Date</label>
                    <input wire:model="form_end_date" type="date" class="form-control @error('form_end_date') is-invalid @enderror">
                    @error('form_end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contact Email</label>
                    <input wire:model="form_contact_email" type="email" class="form-control" placeholder="contact@example.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contact Phone</label>
                    <input wire:model="form_contact_phone" type="text" class="form-control" placeholder="+92 300 0000000">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success px-4"><i class="fas fa-save me-1"></i> Create & Add Teams</button>
                <button type="button" wire:click="backToList" class="btn btn-outline-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     DETAIL
══════════════════════════════════════════════════════════ --}}
@if($view === 'detail' && $selectedTournament)
@php $t = $selectedTournament; $teamsCount = $t->teams->count(); @endphp

{{-- Header --}}
<div class="d-flex align-items-start mb-4 gap-2 flex-wrap">
    <button wire:click="backToList" class="btn btn-outline-secondary btn-sm mt-1"><i class="fas fa-arrow-left"></i></button>
    <div class="flex-grow-1">
        <h4 class="mb-0 fw-bold">
            <i class="fas fa-trophy text-warning me-2"></i>{{ $t->name }}
        </h4>
        <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
            <span class="badge {{ $t->status==='active'?'bg-success':($t->status==='completed'?'bg-secondary':($t->status==='draft'?'bg-warning text-dark':'bg-info')) }}">{{ ucfirst($t->status) }}</span>
            <small class="text-muted">{{ ucfirst(str_replace('_',' ',$t->format)) }}</small>
            <small class="text-muted">&bull; {{ $t->cricket_config['overs'] ?? '?' }} overs</small>
            @if($t->start_date)<small class="text-muted">&bull; {{ \Carbon\Carbon::parse($t->start_date)->format('d M Y') }}</small>@endif
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button wire:click="showAddTeam" class="btn btn-outline-success btn-sm">
            <i class="fas fa-user-plus me-1"></i><span class="d-none d-md-inline">Add Team</span>
        </button>
        @if($teamsCount >= 2)
        <button wire:click="generateFixtures"
            onclick="return confirm('This will regenerate ALL scheduled matches. Completed matches are kept. Continue?')"
            class="btn btn-warning btn-sm text-dark fw-semibold">
            <i class="fas fa-bolt me-1"></i><span class="d-none d-md-inline">Generate Fixtures</span>
        </button>
        @endif
    </div>
</div>

{{-- Stats bar --}}
<div class="row g-2 mb-4">
    @php
        $totalM     = $t->matches->count();
        $completedM = $t->matches->where('status','completed')->count();
        $liveM      = $t->matches->where('status','live')->count();
        $pct        = $totalM > 0 ? round($completedM/$totalM*100) : 0;
    @endphp
    <div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center py-3"><div class="fw-bold fs-5 text-success">{{ $teamsCount }}</div><small class="text-muted">Teams</small></div></div>
    <div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center py-3"><div class="fw-bold fs-5 text-primary">{{ $totalM }}</div><small class="text-muted">Matches</small></div></div>
    <div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center py-3"><div class="fw-bold fs-5 text-success">{{ $completedM }}</div><small class="text-muted">Completed</small></div></div>
    <div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center py-3"><div class="fw-bold fs-5 text-danger">{{ $liveM }}</div><small class="text-muted">Live</small></div></div>
</div>

@if($totalM > 0)
<div class="mb-3">
    <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;"><span class="text-muted">Tournament Progress</span><span class="fw-semibold text-success">{{ $pct }}% ({{ $completedM }}/{{ $totalM }} matches)</span></div>
    <div class="progress" style="height:8px;border-radius:4px;"><div class="progress-bar bg-success" style="width:{{ $pct }}%"></div></div>
</div>
@endif

{{-- Tabs --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <button class="nav-link {{ $fixturesTab==='fixtures'?'active':'' }}" wire:click="$set('fixturesTab','fixtures')">
            <i class="fas fa-calendar-alt me-1"></i> Fixtures
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ $fixturesTab==='points'?'active':'' }}" wire:click="$set('fixturesTab','points')">
            <i class="fas fa-table me-1"></i> Points Table
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ $fixturesTab==='teams'?'active':'' }}" wire:click="$set('fixturesTab','teams')">
            <i class="fas fa-users me-1"></i> Teams
            <span class="badge bg-secondary ms-1">{{ $teamsCount }}</span>
        </button>
    </li>
</ul>

{{-- ── FIXTURES TAB ─────────────────────────────────────────────────────── --}}
@if($fixturesTab === 'fixtures')

@if($groupedMatches->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
        <h5 class="text-muted">No fixtures generated yet</h5>
        @if($teamsCount < 2)
            <p class="text-muted">Add at least 2 teams first, then generate fixtures.</p>
            <button wire:click="showAddTeam" class="btn btn-success"><i class="fas fa-user-plus me-1"></i> Add Teams</button>
        @else
            <p class="text-muted">Click <strong>"Generate Fixtures"</strong> to auto-create all {{ $teamsCount }}×{{ $teamsCount-1 }} IPL-style matches + playoffs.</p>
            <button wire:click="generateFixtures"
                onclick="return confirm('Generate all fixtures?')"
                class="btn btn-warning text-dark fw-semibold">
                <i class="fas fa-bolt me-1"></i> Generate Fixtures Now
            </button>
        @endif
    </div>
</div>
@else

{{-- Fixture groups (rounds + playoffs) --}}
@foreach($groupedMatches as $group)
<div class="card border-0 shadow-sm mb-3">
    {{-- Group header --}}
    <div class="card-header d-flex justify-content-between align-items-center"
         style="background: {{ $group['type']==='playoffs' ? '#fff3cd' : '#f8f9fa' }}; border-bottom: 2px solid {{ $group['type']==='playoffs' ? '#ffc107' : '#19722d' }};">
        <span class="fw-bold {{ $group['type']==='playoffs' ? 'text-warning' : 'text-success' }}" style="font-size:.95rem;">
            @if($group['type']==='playoffs')
                <i class="fas fa-star me-2 text-warning"></i>{{ $group['label'] }}
            @else
                <i class="fas fa-cricket-bat-ball me-2 text-success"></i>{{ $group['label'] }}
            @endif
        </span>
        <small class="text-muted">{{ $group['matches']->count() }} match{{ $group['matches']->count()>1?'es':'' }}</small>
    </div>

    <div class="list-group list-group-flush">
        @foreach($group['matches'] as $match)
        @php
            $inn1    = $match->innings->where('inning_number',1)->first();
            $inn2    = $match->innings->where('inning_number',2)->first();
            $t1Name  = $match->team1->team_short_name ?? $match->team1->team_name_override ?? 'TBD';
            $t2Name  = $match->team2->team_short_name ?? $match->team2->team_name_override ?? 'TBD';
            $t1Full  = $match->team1->team_name_override ?? 'TBD';
            $t2Full  = $match->team2->team_name_override ?? 'TBD';
            $t1Color = $match->team1->jersey_color ?? '#19722d';
            $t2Color = $match->team2->jersey_color ?? '#6c757d';

            // Get score for each team
            $t1Score = null; $t2Score = null;
            foreach($match->innings as $inn) {
                if((string)$inn->batting_team_id === (string)$match->team1_id)
                    $t1Score = $inn;
                if((string)$inn->batting_team_id === (string)$match->team2_id)
                    $t2Score = $inn;
            }
        @endphp
        <div class="list-group-item px-3 py-2">
            <div class="d-flex align-items-center gap-2 justify-content-between flex-wrap">

                {{-- Match info left --}}
                <div style="min-width:60px;">
                    @if($match->match_name && $match->match_type !== 'league')
                        <div class="badge bg-warning text-dark mb-1" style="font-size:.7rem;">{{ Str::upper($match->match_type === 'final' ? 'FINAL' : ($match->match_type === 'qualifier_1' ? 'Q1' : ($match->match_type === 'qualifier_2' ? 'Q2' : ($match->match_type === 'eliminator' ? 'EL' : str_replace('_','',strtoupper($match->match_type)))))) }}</div>
                    @else
                        <div class="text-muted fw-semibold" style="font-size:.75rem;">M{{ $match->match_number }}</div>
                    @endif
                    <div style="font-size:.7rem;" class="text-muted">
                        {{ $match->scheduled_date ? \Carbon\Carbon::parse($match->scheduled_date)->format('d M') : '-' }}
                    </div>
                    @if($match->scheduled_time)
                    <div style="font-size:.68rem;" class="text-muted">{{ \Carbon\Carbon::parse($match->scheduled_time)->format('h:i A') }}</div>
                    @endif
                </div>

                {{-- Team 1 --}}
                <div class="d-flex align-items-center gap-1 flex-grow-1 justify-content-end" style="max-width:200px;">
                    <div class="text-end">
                        <div class="fw-bold" style="font-size:.9rem;">{{ $t1Name }}</div>
                        @if($t1Score)
                        <div class="fw-bold {{ (string)$match->winner_id===(string)$match->team1_id ? 'text-success' : '' }}" style="font-size:.85rem;">
                            {{ $t1Score->total_runs }}/{{ $t1Score->wickets_fallen }}
                        </div>
                        <div style="font-size:.7rem;" class="text-muted">({{ $t1Score->overs_bowled }} ov)</div>
                        @else
                        <div style="font-size:.8rem;" class="text-muted">-</div>
                        @endif
                    </div>
                    <div style="width:12px;height:12px;border-radius:50%;background:{{ $t1Color }};border:1px solid #ddd;flex-shrink:0;"></div>
                </div>

                {{-- VS / Result --}}
                <div class="text-center px-1" style="min-width:50px;">
                    @if($match->status === 'completed')
                        <div style="font-size:.7rem;color:#19722d;font-weight:700;">FT</div>
                    @elseif($match->status === 'live')
                        <div class="badge bg-danger" style="font-size:.65rem;animation:blink 1s infinite;">LIVE</div>
                    @else
                        <div style="font-size:.8rem;color:#94a3b8;font-weight:600;">VS</div>
                    @endif
                    @if($match->venue)
                    <div style="font-size:.62rem;" class="text-muted d-none d-md-block">{{ Str::limit($match->venue,15) }}</div>
                    @endif
                </div>

                {{-- Team 2 --}}
                <div class="d-flex align-items-center gap-1 flex-grow-1" style="max-width:200px;">
                    <div style="width:12px;height:12px;border-radius:50%;background:{{ $t2Color }};border:1px solid #ddd;flex-shrink:0;"></div>
                    <div>
                        <div class="fw-bold" style="font-size:.9rem;">{{ $t2Name }}</div>
                        @if($t2Score)
                        <div class="fw-bold {{ (string)$match->winner_id===(string)$match->team2_id ? 'text-success' : '' }}" style="font-size:.85rem;">
                            {{ $t2Score->total_runs }}/{{ $t2Score->wickets_fallen }}
                        </div>
                        <div style="font-size:.7rem;" class="text-muted">({{ $t2Score->overs_bowled }} ov)</div>
                        @else
                        <div style="font-size:.8rem;" class="text-muted">-</div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex flex-column gap-1 ms-auto">
                    <button wire:click="showScoreboard('{{ $match->id }}')" class="btn btn-outline-success btn-sm" style="font-size:.75rem;padding:3px 8px;">
                        <i class="fas fa-edit"></i> Score
                    </button>
                    <button wire:click="deleteMatch('{{ $match->id }}')"
                        onclick="return confirm('Delete match #{{ $match->match_number }}?')"
                        class="btn btn-outline-danger btn-sm" style="font-size:.75rem;padding:3px 8px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            {{-- Result row --}}
            @if($match->status === 'completed' && ($match->winner_id || $match->margin))
            <div class="mt-1 ps-1" style="font-size:.75rem;">
                <span class="text-success fw-semibold">
                    <i class="fas fa-trophy me-1"></i>
                    {{ $match->winner->team_name_override ?? 'Unknown' }} won
                    @if($match->margin) &bull; {{ $match->margin }} @endif
                </span>
                @if($match->toss_winner_id)
                <span class="text-muted ms-2">
                    Toss: {{ $match->toss_winner_id === $match->team1_id ? ($match->team1->team_short_name ?? $t1Name) : ($match->team2->team_short_name ?? $t2Name) }}
                    @if($match->toss_decision) ({{ $match->toss_decision }}) @endif
                </span>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endforeach

{{-- Summary --}}
<div class="card border-0 shadow-sm mb-3" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
    <div class="card-body py-2 px-3">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <span style="font-size:.82rem;"><i class="fas fa-info-circle text-success me-1"></i>
                <strong>IPL Format:</strong> Every team plays every other team <strong>twice</strong> (home & away).
                Top 4 advance to playoffs.
            </span>
            <span class="ms-auto text-muted" style="font-size:.78rem;">
                Total: {{ $t->matches->where('match_type','league')->count() }} league +
                {{ $t->matches->whereNotIn('match_type',['league'])->count() }} playoff matches
            </span>
        </div>
    </div>
</div>
@endif
@endif

{{-- ── POINTS TABLE TAB ─────────────────────────────────────────────────── --}}
@if($fixturesTab === 'points')
@if($pointsTable->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-table fa-2x mb-2 d-block"></i>No data yet. Enter match scores to populate the table.
    </div>
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <span class="fw-bold"><i class="fas fa-table me-2 text-success"></i>IPL Points Table</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.85rem;">
            <thead class="table-light">
                <tr>
                    <th style="width:36px;">#</th>
                    <th>Team</th>
                    <th class="text-center">P</th>
                    <th class="text-center">W</th>
                    <th class="text-center">L</th>
                    <th class="text-center">NR</th>
                    <th class="text-center fw-bold text-success">PTS</th>
                    <th class="text-center d-none d-md-table-cell">NRR</th>
                    <th class="text-center d-none d-lg-table-cell">For</th>
                    <th class="text-center d-none d-lg-table-cell">Against</th>
                    <th class="text-center d-none d-md-table-cell">Form</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pointsTable as $i => $row)
                @php
                    $isQualified = $i < 4 && $row->matches_played > 0;
                    $teamColor   = $row->leagueTeam->jersey_color ?? '#19722d';
                @endphp
                <tr class="{{ $isQualified ? 'table-success bg-opacity-25' : '' }}">
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            @if($row->rank <= 4 && $row->matches_played > 0)
                                <span class="fw-bold text-success">{{ $row->rank }}</span>
                                @if($row->rank <= 2)<i class="fas fa-arrow-up text-success" style="font-size:.7rem;"></i>@endif
                            @else
                                <span class="text-muted">{{ $row->rank ?: '-' }}</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:14px;height:14px;border-radius:50%;background:{{ $teamColor }};border:1px solid #ddd;flex-shrink:0;"></div>
                            <div>
                                <div class="fw-semibold">{{ $row->leagueTeam->team_name_override ?? 'Unknown' }}</div>
                                <small class="text-muted d-none d-md-block">{{ $row->leagueTeam->team_short_name ?? '' }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">{{ $row->matches_played }}</td>
                    <td class="text-center text-success fw-semibold">{{ $row->wins }}</td>
                    <td class="text-center text-danger">{{ $row->losses }}</td>
                    <td class="text-center text-muted">{{ $row->no_results }}</td>
                    <td class="text-center fw-bold text-success" style="font-size:1rem;">{{ $row->points }}</td>
                    <td class="text-center d-none d-md-table-cell">
                        @if($row->net_run_rate !== null)
                            <span class="{{ $row->net_run_rate >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $row->net_run_rate >= 0 ? '+' : '' }}{{ number_format($row->net_run_rate, 3) }}
                            </span>
                        @else <span class="text-muted">-</span> @endif
                    </td>
                    <td class="text-center d-none d-lg-table-cell text-muted small">
                        {{ $row->total_runs_scored }}/{{ number_format($row->total_overs_faced,1) }}
                    </td>
                    <td class="text-center d-none d-lg-table-cell text-muted small">
                        {{ $row->total_runs_conceded }}/{{ number_format($row->total_overs_bowled,1) }}
                    </td>
                    <td class="text-center d-none d-md-table-cell">
                        @if($row->recent_form)
                            @foreach(array_slice((array)$row->recent_form, -5) as $f)
                            <span class="badge {{ $f==='W'?'bg-success':($f==='L'?'bg-danger':'bg-secondary') }}" style="font-size:.6rem;margin:1px;padding:3px 5px;">{{ $f }}</span>
                            @endforeach
                        @else <span class="text-muted small">-</span> @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-top-0">
        <div class="d-flex gap-3 flex-wrap" style="font-size:.75rem;">
            <span><span class="badge bg-success" style="font-size:.65rem;">Win = 2 pts</span></span>
            <span><span class="badge bg-secondary" style="font-size:.65rem;">No Result = 1 pt</span></span>
            <span><span class="badge bg-danger" style="font-size:.65rem;">Loss = 0 pts</span></span>
            <span class="text-muted">Top 4 qualify for playoffs &bull; NRR used as tiebreaker</span>
        </div>
    </div>
</div>
@endif
@endif

{{-- ── TEAMS TAB ────────────────────────────────────────────────────────── --}}
@if($fixturesTab === 'teams')
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="fw-semibold">{{ $teamsCount }} / {{ $t->num_teams }} teams registered</span>
    <button wire:click="showAddTeam" class="btn btn-outline-success btn-sm">
        <i class="fas fa-user-plus me-1"></i> Add Team
    </button>
</div>

@if($t->teams->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-4 text-muted">
        <i class="fas fa-users-slash fa-2x mb-2 d-block"></i>
        No teams yet. <button wire:click="showAddTeam" class="btn btn-link p-0 text-success">Add first team</button>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($t->teams->sortBy('slot_number') as $idx => $team)
    @php
        $pts = $team->pointsEntry;
        $won  = $pts->wins ?? 0;
        $lost = $pts->losses ?? 0;
        $played = $pts->matches_played ?? 0;
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    {{-- Jersey icon --}}
                    <div style="width:44px;height:44px;border-radius:50%;background:{{ $team->jersey_color ?? '#19722d' }};
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid rgba(0,0,0,.1);">
                        <span class="fw-bold text-white" style="font-size:.75rem;">{{ $team->team_short_name ?? strtoupper(substr($team->team_name_override??'T',0,2)) }}</span>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-bold">{{ $team->team_name_override }}</div>
                        @if($team->team_short_name)<small class="text-muted">{{ $team->team_short_name }}</small>@endif
                    </div>
                    <button wire:click="deleteTeam('{{ $team->id }}')"
                        onclick="return confirm('Remove {{ addslashes($team->team_name_override) }}?')"
                        class="btn btn-link text-danger btn-sm p-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @if($played > 0)
                <div class="row text-center g-1">
                    <div class="col-3"><div class="fw-bold">{{ $played }}</div><small class="text-muted" style="font-size:.7rem;">P</small></div>
                    <div class="col-3"><div class="fw-bold text-success">{{ $won }}</div><small class="text-muted" style="font-size:.7rem;">W</small></div>
                    <div class="col-3"><div class="fw-bold text-danger">{{ $lost }}</div><small class="text-muted" style="font-size:.7rem;">L</small></div>
                    <div class="col-3"><div class="fw-bold text-success">{{ ($pts->points ?? 0) }}</div><small class="text-muted" style="font-size:.7rem;">Pts</small></div>
                </div>
                @else
                <small class="text-muted">No matches played yet</small>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($teamsCount >= 2)
<div class="mt-3 alert alert-success alert-sm d-flex align-items-center gap-2 py-2">
    <i class="fas fa-bolt"></i>
    <span style="font-size:.85rem;">
        Ready! <strong>{{ $teamsCount }} teams</strong> will generate
        <strong>{{ $teamsCount * ($teamsCount - 1) }}</strong> league matches
        + 4 playoff matches.
    </span>
    <button wire:click="generateFixtures"
        onclick="return confirm('Generate all fixtures?')"
        class="btn btn-warning btn-sm text-dark fw-semibold ms-auto">
        <i class="fas fa-bolt me-1"></i> Generate
    </button>
</div>
@endif
@endif
@endif

@endif {{-- end detail --}}

{{-- ══════════════════════════════════════════════════════════
     ADD TEAM
══════════════════════════════════════════════════════════ --}}
@if($view === 'add_team' && $selectedTournament)
<div class="d-flex align-items-center mb-4 gap-2">
    <button wire:click="backToDetail" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i></button>
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-user-plus text-success me-2"></i>Add Team</h4>
        <small class="text-muted">{{ $selectedTournament->name }} &bull; {{ $selectedTournament->teams->count() }}/{{ $selectedTournament->num_teams }} teams</small>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form wire:submit.prevent="addTeam">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Team Name <span class="text-danger">*</span></label>
                        <input wire:model="team_name" type="text" class="form-control @error('team_name') is-invalid @enderror" placeholder="e.g. Lahore Lions">
                        @error('team_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Short Code <small class="text-muted">(3–4 chars)</small></label>
                        <input wire:model="team_short_name" type="text" class="form-control" maxlength="10" placeholder="LAH">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Jersey Color</label>
                        <div class="d-flex align-items-center gap-2">
                            <input wire:model="team_jersey_color" type="color" class="form-control form-control-color" style="width:50px;height:40px;">
                            <input wire:model="team_jersey_color" type="text" class="form-control" maxlength="7" placeholder="#19722d">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4"><i class="fas fa-plus me-1"></i> Add Team</button>
                        <button type="button" wire:click="backToDetail" class="btn btn-outline-secondary">Done</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Teams already added --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom fw-semibold">
                Teams Added ({{ $selectedTournament->teams->count() }})
            </div>
            @if($selectedTournament->teams->isEmpty())
            <div class="card-body text-center text-muted py-3">No teams yet</div>
            @else
            <div class="list-group list-group-flush">
                @foreach($selectedTournament->teams as $i => $team)
                <div class="list-group-item d-flex align-items-center gap-3 py-2">
                    <span class="text-muted" style="width:24px;text-align:center;">{{ $i+1 }}</span>
                    <div style="width:32px;height:32px;border-radius:50%;background:{{ $team->jersey_color ?? '#19722d' }};
                        display:flex;align-items:center;justify-content:center;border:1px solid rgba(0,0,0,.15);flex-shrink:0;">
                        <span class="fw-bold text-white" style="font-size:.65rem;">{{ strtoupper(substr($team->team_short_name ?? $team->team_name_override ?? 'T',0,2)) }}</span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $team->team_name_override }}</div>
                        @if($team->team_short_name)<small class="text-muted">{{ $team->team_short_name }}</small>@endif
                    </div>
                    <button wire:click="deleteTeam('{{ $team->id }}')"
                        onclick="return confirm('Remove team?')"
                        class="btn btn-link text-danger btn-sm p-0"><i class="fas fa-times"></i></button>
                </div>
                @endforeach
            </div>
            @endif
            @if($selectedTournament->teams->count() >= 2)
            <div class="card-footer bg-light border-top py-2">
                <small class="text-success fw-semibold">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ $selectedTournament->teams->count() }} teams ready &bull;
                    Will generate {{ $selectedTournament->teams->count() * ($selectedTournament->teams->count()-1) }} league matches
                </small>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     SCOREBOARD
══════════════════════════════════════════════════════════ --}}
@if($view === 'scoreboard' && $selectedMatch)
@php $match = $selectedMatch; @endphp
<div class="d-flex align-items-center mb-4 gap-2">
    <button wire:click="backToDetail" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i></button>
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-edit text-success me-2"></i>Enter Score</h4>
        <small class="text-muted">
            {{ $match->team1->team_name_override ?? 'Team 1' }} vs {{ $match->team2->team_name_override ?? 'Team 2' }}
            @if($match->match_number) &bull; Match #{{ $match->match_number }} @endif
            @if($match->match_name && $match->match_type !== 'league') &bull; <strong>{{ $match->match_name }}</strong> @endif
        </small>
    </div>
</div>

{{-- Current scorecard --}}
@if($match->innings->count())
<div class="row g-3 mb-4">
    @foreach($match->innings->sortBy('inning_number') as $inn)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #19722d !important;">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size:.7rem;">{{ $inn->inning_number == 1 ? '1st' : '2nd' }} Innings</small>
                        <div class="fw-bold">{{ $inn->battingTeam->team_name_override ?? 'Unknown' }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success" style="font-size:1.5rem;line-height:1.1;">{{ $inn->total_runs }}/{{ $inn->wickets_fallen }}</div>
                        <div class="text-muted small">({{ $inn->overs_bowled }} overs)</div>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-2 text-muted" style="font-size:.75rem;">
                    <span>Extras: {{ $inn->extras_total }}</span>
                    <span>W:{{ $inn->wides }} NB:{{ $inn->no_balls }} B:{{ $inn->byes }} LB:{{ $inn->leg_byes }}</span>
                </div>
                @if($inn->is_completed)<span class="badge bg-secondary mt-1" style="font-size:.68rem;">Completed</span>@endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom fw-semibold">
        <i class="fas fa-cricket-bat-ball me-2 text-success"></i>Update Score
    </div>
    <div class="card-body">
        <form wire:submit.prevent="saveScore">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Innings</label>
                    <select wire:model="score_inning" class="form-select">
                        <option value="1">1st Innings</option>
                        <option value="2">2nd Innings</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Batting Team <span class="text-danger">*</span></label>
                    <select wire:model="score_batting_team" class="form-select @error('score_batting_team') is-invalid @enderror">
                        <option value="">Select...</option>
                        <option value="{{ $match->team1_id }}">{{ $match->team1->team_name_override ?? 'Team 1' }}</option>
                        <option value="{{ $match->team2_id }}">{{ $match->team2->team_name_override ?? 'Team 2' }}</option>
                    </select>
                    @error('score_batting_team')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-4">
                    <label class="form-label fw-semibold">Runs <span class="text-danger">*</span></label>
                    <input wire:model="score_runs" type="number" min="0" class="form-control @error('score_runs') is-invalid @enderror">
                    @error('score_runs')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-4">
                    <label class="form-label fw-semibold">Wickets <span class="text-danger">*</span></label>
                    <input wire:model="score_wickets" type="number" min="0" max="10" class="form-control @error('score_wickets') is-invalid @enderror">
                    @error('score_wickets')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-4">
                    <label class="form-label fw-semibold">Overs <span class="text-danger">*</span></label>
                    <input wire:model="score_overs" type="number" min="0" step="0.1" class="form-control @error('score_overs') is-invalid @enderror">
                    @error('score_overs')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12"><label class="form-label fw-semibold text-muted small mb-0">Extras breakdown</label></div>
                <div class="col-3"><label class="form-label small">Wides</label><input wire:model="score_wides" type="number" min="0" class="form-control form-control-sm" placeholder="0"></div>
                <div class="col-3"><label class="form-label small">No Balls</label><input wire:model="score_no_balls" type="number" min="0" class="form-control form-control-sm" placeholder="0"></div>
                <div class="col-3"><label class="form-label small">Byes</label><input wire:model="score_byes" type="number" min="0" class="form-control form-control-sm" placeholder="0"></div>
                <div class="col-3"><label class="form-label small">Leg Byes</label><input wire:model="score_leg_byes" type="number" min="0" class="form-control form-control-sm" placeholder="0"></div>

                <div class="col-12">
                    <div class="form-check">
                        <input wire:model="score_is_completed" type="checkbox" class="form-check-input" id="inningDone">
                        <label class="form-check-label" for="inningDone">Innings completed / declared</label>
                    </div>
                </div>
            </div>

            <hr class="my-3">
            <h6 class="fw-bold mb-3">Match Result & Info</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Match Status <span class="text-danger">*</span></label>
                    <select wire:model="match_status" class="form-select @error('match_status') is-invalid @enderror">
                        <option value="scheduled">Scheduled</option>
                        <option value="live">Live</option>
                        <option value="completed">Completed</option>
                        <option value="no_result">No Result</option>
                        <option value="abandoned">Abandoned</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Toss Won By</label>
                    <select wire:model="match_toss_winner_id" class="form-select">
                        <option value="">-</option>
                        <option value="{{ $match->team1_id }}">{{ $match->team1->team_short_name ?? $match->team1->team_name_override }}</option>
                        <option value="{{ $match->team2_id }}">{{ $match->team2->team_short_name ?? $match->team2->team_name_override }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Elected To</label>
                    <select wire:model="match_toss_decision" class="form-select">
                        <option value="">-</option>
                        <option value="bat">Bat</option>
                        <option value="bowl">Bowl / Field</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Match Winner</label>
                    <select wire:model="match_winner_id" class="form-select">
                        <option value="">No result / TBD</option>
                        <option value="{{ $match->team1_id }}">{{ $match->team1->team_name_override ?? 'Team 1' }}</option>
                        <option value="{{ $match->team2_id }}">{{ $match->team2->team_name_override ?? 'Team 2' }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Win Margin</label>
                    <input wire:model="match_margin" type="text" class="form-control" placeholder="e.g. 6 wickets or 45 runs">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success px-4"><i class="fas fa-save me-1"></i> Save Score</button>
                <button type="button" wire:click="backToDetail" class="btn btn-outline-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endif

<style>
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.4} }
</style>

</div>

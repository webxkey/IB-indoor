<div class="container-fluid p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-bullhorn text-primary me-2"></i> Announcements
        </h4>
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ─── Stats ────────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total</div>
                    <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Published</div>
                    <div class="fs-3 fw-bold text-success">{{ $stats['active'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Pending review</div>
                    <div class="fs-3 fw-bold text-warning">{{ $stats['inactive'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Venues posting</div>
                    <div class="fs-3 fw-bold text-info">{{ $stats['venues'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Filters ──────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" class="form-control form-control-sm"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Title, subtitle or label...">
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Venue</label>
                    <select class="form-select form-select-sm" wire:model.live="venueFilter">
                        <option value="">All venues</option>
                        <option value="global">— Global (Django app) —</option>
                        @foreach($venues as $v)
                            <option value="{{ $v->id }}">{{ $v->name }} (#{{ $v->id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Status</label>
                    <select class="form-select form-select-sm" wire:model.live="statusFilter">
                        <option value="">All</option>
                        <option value="active">Published</option>
                        <option value="inactive">Pending review</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── List ─────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($announcements->total() === 0)
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-bullhorn fa-3x mb-3 opacity-25"></i>
                    <p>No announcements match the current filters.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-muted">
                            <tr>
                                <th class="ps-3">Title</th>
                                <th>Venue</th>
                                <th>Label</th>
                                <th>Status</th>
                                <th>Pin</th>
                                <th style="width:90px">Sort</th>
                                <th>Expires</th>
                                <th>Created</th>
                                <th class="pe-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $a)
                                <tr class="{{ $a->is_active ? '' : 'table-warning' }}">
                                    <td class="ps-3" style="max-width:360px">
                                        <div class="fw-semibold">{{ $a->title }}</div>
                                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($a->subtitle, 100) }}</div>
                                    </td>
                                    <td class="small">
                                        @if($a->venue)
                                            <span class="badge bg-info bg-opacity-25 text-info">
                                                {{ $a->venue->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-25 text-secondary">Global</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-primary bg-opacity-25 text-primary">{{ $a->label }}</span></td>
                                    <td>
                                        @if($a->is_active)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($a->is_active)
                                            <button type="button"
                                                    class="btn btn-sm btn-link p-0 {{ $a->is_pinned ? 'text-warning' : 'text-muted' }}"
                                                    wire:click="togglePin({{ $a->id }})"
                                                    title="{{ $a->is_pinned ? 'Unpin' : 'Pin to top' }}">
                                                <i class="fas fa-thumbtack"></i>
                                            </button>
                                        @else
                                            <i class="fas fa-thumbtack text-muted opacity-25" title="Approve first to enable pin"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($a->is_active)
                                            <input type="number" min="0"
                                                class="form-control form-control-sm"
                                                style="width:70px"
                                                value="{{ $a->sort_order }}"
                                                wire:change="updateSortOrder({{ $a->id }}, $event.target.value)">
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $a->expires_at ? \Carbon\Carbon::parse($a->expires_at)->format('M d, Y') : '—' }}</td>
                                    <td class="small">{{ $a->created_at?->format('M d, Y') }}</td>
                                    <td class="pe-3 text-end">
                                        @if($a->is_active)
                                            <button class="btn btn-sm btn-outline-secondary"
                                                    wire:click="toggleActive({{ $a->id }})"
                                                    title="Hide / unpublish">
                                                <i class="fas fa-eye-slash me-1"></i> Hide
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-success"
                                                    wire:click="toggleActive({{ $a->id }})"
                                                    title="Approve and publish">
                                                <i class="fas fa-check me-1"></i> Approve
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteAnnouncement({{ $a->id }})"
                                                wire:confirm="Delete this announcement?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-3 py-3 border-top">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

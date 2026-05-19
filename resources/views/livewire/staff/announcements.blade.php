<div class="container-fluid p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-bullhorn text-success me-2"></i> Announcements
        </h4>
        @if(!$showForm)
            <button class="btn btn-success" wire:click="openCreate">
                <i class="fas fa-plus me-1"></i> New Announcement
            </button>
        @endif
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ─── Create / Edit form ───────────────────────────────────────────── --}}
    @if($showForm)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">{{ $editingId ? 'Edit Announcement' : 'New Announcement' }}</h5>
                    <button class="btn btn-sm btn-outline-secondary" wire:click="closeForm">Cancel</button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small">Title *</label>
                            <input type="text" class="form-control" wire:model.live="title">
                            @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Label *</label>
                            <input type="text" class="form-control" wire:model.live="label" placeholder="e.g. Update, Event, Promo">
                            @error('label') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small">Subtitle *</label>
                            <input type="text" class="form-control" wire:model.live="subtitle">
                            @error('subtitle') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small">Short description (max 320 chars) *</label>
                            <textarea class="form-control" rows="2" wire:model.live="short_description" maxlength="320"></textarea>
                            @error('short_description') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small">Full description *</label>
                            <textarea class="form-control" rows="5" wire:model.live="full_description"></textarea>
                            @error('full_description') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">Image (optional)</label>
                            <input type="file" class="form-control" wire:model="image" accept="image/*">
                            @if($existing_image && !$image)
                                <div class="mt-2 small text-muted">Current:
                                    <img src="{{ asset('storage/'.$existing_image) }}" alt="" style="height:48px" class="rounded">
                                </div>
                            @endif
                            @error('image') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Fallback bg color *</label>
                            <input type="color" class="form-control form-control-color" wire:model.live="fallback_bg_color" style="height:38px">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Expires at *</label>
                            <input type="datetime-local" class="form-control" wire:model.live="expires_at">
                            @error('expires_at') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">"Read more" link label</label>
                            <input type="text" class="form-control" wire:model.live="read_more_label" placeholder="e.g. Learn more">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">"Read more" URL</label>
                            <input type="url" class="form-control" wire:model.live="read_more_url" placeholder="https://...">
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info small mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Once submitted, this announcement goes into <strong>Pending review</strong>.
                                Visibility, pinning, and sort order are managed by the admin.
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-success" type="submit">
                            <i class="fas fa-save me-1"></i> {{ $editingId ? 'Update' : 'Create' }}
                        </button>
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeForm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ─── List ─────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if(count($announcements) === 0)
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-bullhorn fa-3x mb-3 opacity-25"></i>
                    <p>No announcements yet. Click "New Announcement" to create one.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-muted">
                            <tr>
                                <th class="ps-3">Title</th>
                                <th>Label</th>
                                <th>Status</th>
                                <th>Image</th>
                                <th>Expires</th>
                                <th>Created</th>
                                <th class="pe-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $a)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">
                                            {{ $a->title }}
                                            @if($a->is_pinned)
                                                <i class="fas fa-thumbtack text-warning ms-1" title="Pinned by admin"></i>
                                            @endif
                                        </div>
                                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($a->subtitle, 80) }}</div>
                                    </td>
                                    <td><span class="badge bg-info bg-opacity-25 text-info">{{ $a->label }}</span></td>
                                    <td>
                                        @if($a->is_active)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending review</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($a->image)
                                            <img src="{{ asset('storage/'.$a->image) }}" alt="" style="height:32px" class="rounded">
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $a->expires_at ? \Carbon\Carbon::parse($a->expires_at)->format('M d, Y H:i') : '—' }}</td>
                                    <td class="small">{{ $a->created_at?->format('M d, Y H:i') }}</td>
                                    <td class="pe-3 text-end">
                                        <button class="btn btn-sm btn-outline-primary"
                                                wire:click="openEdit({{ $a->id }})">
                                            <i class="fas fa-pen"></i>
                                        </button>
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
            @endif
        </div>
    </div>
</div>

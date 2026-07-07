<div class="container-fluid p-3 p-md-4">
    <style>
        .page-header-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px;
            padding: 24px;
            color: #ffffff;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.15);
        }
        .announcement-card {
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            background: #ffffff;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
            border-color: #e2e8f0;
        }
        .color-strip {
            height: 6px;
            width: 100%;
        }
        .badge-pill-custom {
            border-radius: 50px;
            padding: 5px 12px;
            font-size: 0.72rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .form-control-custom {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .form-control-custom:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
            outline: none;
        }
        .btn-custom-success {
            background-color: #10b981;
            border-color: #10b981;
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            padding: 8px 18px;
            transition: all 0.2s ease;
        }
        .btn-custom-success:hover {
            background-color: #059669;
            border-color: #059669;
            color: #ffffff;
            transform: translateY(-1px);
        }
        .btn-custom-outline {
            border-radius: 10px;
            padding: 8px 18px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .image-preview-container {
            height: 160px;
            background-size: cover;
            background-position: center;
            position: relative;
            background-color: #f8fafc;
        }
        .image-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 6px;
        }
        .pin-icon {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255,255,255,0.9);
            color: #f59e0b;
            padding: 6px;
            border-radius: 50%;
            font-size: 0.8rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
    </style>

    {{-- Header Banner --}}
    <div class="page-header-gradient d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-white">
                <i class="fas fa-bullhorn me-2"></i> Announcements
            </h3>
            <p class="text-white text-opacity-75 mb-0 small">Create and manage venue-level announcements and event notifications.</p>
        </div>
        @if(!$showForm)
            <button class="btn btn-light fw-bold text-success px-4 py-2" style="border-radius: 10px;" wire:click="openCreate">
                <i class="fas fa-plus me-1"></i> New Announcement
            </button>
        @endif
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Create / Edit Form --}}
    @if($showForm)
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-success me-2"></i>{{ $editingId ? 'Edit Announcement' : 'New Announcement' }}</h5>
                    <button class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 8px;" wire:click="closeForm">Cancel</button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted mb-1">Title *</label>
                            <input type="text" class="form-control form-control-custom" wire:model.live="title" placeholder="Enter headline title">
                            @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">Label *</label>
                            <input type="text" class="form-control form-control-custom" wire:model.live="label" placeholder="e.g. Update, Event, Promo">
                            @error('label') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">Subtitle *</label>
                            <input type="text" class="form-control form-control-custom" wire:model.live="subtitle" placeholder="Short single-line subtitle">
                            @error('subtitle') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">Short description (max 320 chars) *</label>
                            <textarea class="form-control form-control-custom" rows="2" wire:model.live="short_description" maxlength="320" placeholder="Brief summary of the announcement"></textarea>
                            @error('short_description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">Full description *</label>
                            <textarea class="form-control form-control-custom" rows="4" wire:model.live="full_description" placeholder="Write full details here..."></textarea>
                            @error('full_description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Upload Image (optional)</label>
                            <input type="file" class="form-control form-control-custom" wire:model="image" accept="image/*">
                            @if($existing_image && !$image)
                                <div class="mt-2 small text-muted d-flex align-items-center gap-2">
                                    <span>Current:</span>
                                    <img src="{{ asset('storage/'.$existing_image) }}" alt="" style="height:40px; width:40px; object-fit: cover;" class="rounded border shadow-sm">
                                </div>
                            @endif
                            @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">Fallback Accent Color *</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" class="form-control form-control-color" wire:model.live="fallback_bg_color" style="height:40px; width:60px; border-radius: 8px;">
                                <span class="small text-muted">{{ $fallback_bg_color ?: '#10b981' }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">Expires at *</label>
                            <input type="datetime-local" class="form-control form-control-custom" wire:model.live="expires_at">
                            @error('expires_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">"Read more" link label</label>
                            <input type="text" class="form-control form-control-custom" wire:model.live="read_more_label" placeholder="e.g. Learn more">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">"Read more" URL</label>
                            <input type="url" class="form-control form-control-custom" wire:model.live="read_more_url" placeholder="https://...">
                        </div>

                        <div class="col-12 mt-3">
                            <div class="alert alert-warning small mb-0 d-flex align-items-center gap-2" style="border-radius: 10px; background-color: #fffbeb; border-color: #fef3c7; color: #b45309;">
                                <i class="fas fa-info-circle" style="font-size: 1.1rem;"></i>
                                <span>Once submitted, this announcement goes into <strong>Pending review</strong>. The admin will verify, publish, pin, or reorder the post.</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-custom-success" type="submit">
                            <i class="fas fa-save me-1"></i> {{ $editingId ? 'Update Announcement' : 'Create Announcement' }}
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-custom-outline" wire:click="closeForm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Grid List Layout --}}
    @if(count($announcements) === 0)
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body text-center py-5 text-muted">
                <div class="mb-3">
                    <span class="material-symbols-outlined text-muted opacity-25" style="font-size: 4rem;">campaign</span>
                </div>
                <h5 class="fw-bold text-dark">No announcements found</h5>
                <p class="small text-muted mb-4">Click the button above to publish your first update or promotional notification.</p>
                <button class="btn btn-custom-success" wire:click="openCreate">
                    <i class="fas fa-plus me-1"></i> Create First Announcement
                </button>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($announcements as $a)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="announcement-card">
                        {{-- Top Accent color strip --}}
                        <div class="color-strip" style="background-color: {{ $a->fallback_bg_color ?: '#10b981' }};"></div>

                        {{-- Image Area / Placeholder --}}
                        <div class="image-preview-container" 
                             style="background-image: url('{{ $a->image ? asset('storage/'.$a->image) : 'none' }}');">
                            @if(!$a->image)
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted" style="background-color: {{ ($a->fallback_bg_color ?: '#10b981') }}1a;">
                                    <i class="fas fa-bullhorn fa-2x" style="color: {{ $a->fallback_bg_color ?: '#10b981' }}; opacity: 0.6;"></i>
                                </div>
                            @endif

                            {{-- Pinned indicator --}}
                            @if($a->is_pinned)
                                <span class="pin-icon" title="Pinned Announcement">
                                    <i class="fas fa-thumbtack"></i>
                                </span>
                            @endif

                            {{-- Badges overlay --}}
                            <div class="image-overlay">
                                <span class="badge-pill-custom bg-white shadow-sm text-dark">{{ $a->label }}</span>
                                @if($a->is_active)
                                    <span class="badge-pill-custom bg-success text-white shadow-sm"><i class="fas fa-check-circle"></i> Published</span>
                                @else
                                    <span class="badge-pill-custom bg-warning text-dark shadow-sm"><i class="fas fa-hourglass-half"></i> Pending</span>
                                @endif
                            </div>
                        </div>

                        {{-- Card Content --}}
                        <div class="card-body p-3 d-flex flex-column flex-grow-1">
                            <h6 class="fw-bold text-dark mb-1">{{ $a->title }}</h6>
                            <p class="text-muted small mb-2" style="font-size: 0.78rem;">{{ $a->subtitle }}</p>
                            
                            <p class="text-secondary mb-3 flex-grow-1" style="font-size: 0.82rem; line-height: 1.4;">
                                {{ \Illuminate\Support\Str::limit($a->short_description, 160) }}
                            </p>

                            <hr class="my-2" style="opacity: 0.1;">

                            {{-- Footer Dates --}}
                            <div class="small text-muted mb-3" style="font-size: 0.75rem;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Created:</span>
                                    <span class="fw-semibold text-dark">{{ $a->created_at?->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Expires:</span>
                                    <span class="fw-semibold text-danger">{{ $a->expires_at ? \Carbon\Carbon::parse($a->expires_at)->format('M d, Y H:i') : '—' }}</span>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="d-flex justify-content-end gap-2 mt-auto">
                                <button class="btn btn-sm btn-outline-primary px-3" style="border-radius: 8px;"
                                        wire:click="openEdit({{ $a->id }})" title="Edit Announcement">
                                    <i class="fas fa-pen me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger px-3" style="border-radius: 8px;"
                                        wire:click="deleteAnnouncement({{ $a->id }})"
                                        wire:confirm="Are you sure you want to delete this announcement?" title="Delete Announcement">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

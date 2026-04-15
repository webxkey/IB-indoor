<div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-paint-brush text-success me-2"></i>Landing Page CMS</h4>
        <small class="text-muted">Manage sections and content shown on the public landing page</small>
    </div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#sectionModal" wire:click="resetInput">
        <i class="fas fa-plus me-2"></i>Add Section
    </button>
</div>

{{-- ── Sections list ──────────────────────────────────────────────────────── --}}
@if($pages->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="fas fa-paint-brush fa-3x text-muted mb-3 d-block"></i>
        <h5 class="text-muted">No sections yet</h5>
        <p class="text-muted">Add your first landing page section to get started.</p>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#sectionModal" wire:click="resetInput">
            <i class="fas fa-plus me-2"></i>Add Section
        </button>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($pages as $page)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-top:3px solid {{ $page->is_active ? '#19722d' : '#6c757d' }};border-radius:10px;">
            {{-- Cover image --}}
            @if($page->images && count($page->images) > 0)
            <div style="height:140px;overflow:hidden;border-radius:10px 10px 0 0;background:#f1f5f2;">
                <img src="{{ $page->images[0] }}" alt="{{ $page->section_title }}"
                     style="width:100%;height:100%;object-fit:cover;">
            </div>
            @else
            <div style="height:80px;border-radius:10px 10px 0 0;background:linear-gradient(135deg,#f0fdf4,#dcfce7);
                display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-image fa-2x text-success opacity-50"></i>
            </div>
            @endif

            <div class="card-body pb-2">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="badge {{ $page->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-size:.68rem;">
                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="badge bg-light text-muted border" style="font-size:.68rem;">
                        Order: {{ $page->display_order }}
                    </span>
                </div>
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size:.68rem;letter-spacing:.05em;">
                    {{ $page->page_name }}
                </div>
                <h6 class="fw-bold mb-1">{{ $page->section_title }}</h6>
                @if($page->section_description)
                <p class="text-muted small mb-2">{{ Str::limit($page->section_description, 80) }}</p>
                @endif
                @if($page->images && count($page->images) > 1)
                <small class="text-muted"><i class="fas fa-images me-1"></i>{{ count($page->images) }} images</small>
                @endif
            </div>
            <div class="card-footer bg-transparent border-top d-flex gap-2 pt-2 pb-3">
                <button wire:click="edit({{ $page->id }})" data-bs-toggle="modal" data-bs-target="#sectionModal"
                    class="btn btn-outline-success btn-sm flex-grow-1">
                    <i class="fas fa-edit me-1"></i>Edit
                </button>
                <button wire:click="delete({{ $page->id }})"
                    onclick="return confirm('Delete section \'{{ addslashes($page->section_title) }}\'?')"
                    class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ── Modal ──────────────────────────────────────────────────────────────── --}}
<div class="modal fade" id="sectionModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-{{ $page_id ? 'edit' : 'plus-circle' }} text-success me-2"></i>
                    {{ $page_id ? 'Edit Section' : 'Add New Section' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetInput"></button>
            </div>
            <div class="modal-body px-4">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Page Name <span class="text-danger">*</span></label>
                            <input wire:model="page_name" type="text" class="form-control @error('page_name') is-invalid @enderror"
                                   placeholder="e.g. Home, About, Features">
                            @error('page_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Which page this section belongs to.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Section Title <span class="text-danger">*</span></label>
                            <input wire:model="section_title" type="text" class="form-control @error('section_title') is-invalid @enderror"
                                   placeholder="e.g. Why Choose Us">
                            @error('section_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Section Description</label>
                            <textarea wire:model="section_description" class="form-control" rows="3"
                                      placeholder="Short paragraph shown below the title..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Display Order</label>
                            <input wire:model="display_order" type="number" class="form-control" placeholder="1" min="0">
                            <small class="text-muted">Lower numbers appear first.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select wire:model="is_active" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-3">
                    <label class="form-label fw-semibold">Images</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                        <input wire:model="new_image_url" type="url" class="form-control"
                               placeholder="https://example.com/image.jpg">
                        <button type="button" wire:click="addImage" class="btn btn-outline-success">
                            <i class="fas fa-plus me-1"></i>Add
                        </button>
                    </div>

                    @if($images)
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($images as $index => $img)
                        <div style="width:140px;position:relative;border-radius:8px;overflow:hidden;border:1px solid #dee2e6;">
                            <img src="{{ $img }}" style="width:100%;height:90px;object-fit:cover;" alt="">
                            <button type="button" wire:click="removeImage({{ $index }})"
                                style="position:absolute;top:4px;right:4px;background:rgba(220,53,69,.85);
                                color:#fff;border:none;border-radius:4px;width:22px;height:22px;
                                font-size:.7rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-times"></i>
                            </button>
                            @if($index === 0)
                            <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(25,114,45,.75);
                                color:#fff;font-size:.65rem;text-align:center;padding:2px;">Cover</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" wire:click="resetInput">
                    Cancel
                </button>
                <button type="button" wire:click="save" class="btn btn-success px-4">
                    <i class="fas fa-save me-2"></i>{{ $page_id ? 'Update Section' : 'Create Section' }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-close modal after save
document.addEventListener('livewire:initialized', () => {
    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        succeed(({ snapshot, effect }) => {
            if (effect.dispatches && effect.dispatches.some(e => e.name === 'sectionSaved')) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('sectionModal'));
                if (modal) modal.hide();
            }
        });
    });
});
</script>

</div>

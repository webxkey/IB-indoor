@push('styles')
    <style>
        container-fluid {
        background: var(--background-color);
        min-height: 100vh;
        padding:0;
    }
    .btn {
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.95rem;
    }
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-label {
        font-weight: 500;
        color: #374151;
    }
        .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        padding: 12px 16px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    </style>
@endpush
<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Sports Management</h1>
            <p class="text-muted">Manage all sports activities and their booking status</p>
        </div>
        <div>
            <button class="btn btn-success me-2" wire:click="openModal">
                <i class="fas fa-plus me-1"></i> Add New Sport
            </button>
        </div>
    </div>

    @if(session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        @foreach($sports as $sport)
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="{{ $sport->image ? asset('storage/' . $sport->image) : asset('images/default-sport.jpg') }}" 
                         alt="Sport Image" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                   <span class="position-absolute top-0 start-0 text-white px-2 py-1 rounded-end 
                    {{ $sport->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                    {{ $sport->status }}
                </span>

                </div>

                <div class="card-body">
                    <h5 class="card-title">{{ $sport->name }}</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Hourly Rate:</span>
                        <span class="fw-bold">${{ number_format($sport->price / 100, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Available Courts:</span>
                        <span class="fw-bold">{{ $sport->maximum_court }}</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-sm btn-primary w-100"
                        wire:click="editSport({{ $sport->id ?? $sport->id }})" data-bs-toggle="modal"
                        data-bs-target="#editSportModal">
                        <i class="fas fa-edit me-1"></i> Manage
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Sport Modal -->
    <div class="modal fade" id="addSportModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New Sport</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        wire:click="closeModal"></button>
                </div>

                <form wire:submit.prevent="saveSport">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="game_name" class="form-label fw-semibold">Game Name*</label>
                                <input type="text" class="form-control" id="game_name" wire:model="game_name" required>
                                @error('game_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="game_type" class="form-label fw-semibold">Game Type*</label>
                                <select class="form-select" id="game_type" wire:model="game_type" required>
                                    <option value="" disabled>Select game type</option>
                                    <option value="Indoor">Indoor</option>
                                    <option value="Outdoor">Outdoor</option>
                                </select>
                                @error('game_type') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="rate_type" class="form-label fw-semibold">Rate Type*</label>
                                <select class="form-select" id="rate_type" wire:model="rate_type" required>
                                    <option value="" disabled>Select rate type</option>
                                    <option value="Per hour">Per hour</option>
                                    <option value="Per session">Per session</option>
                                    <option value="Per day">Per day</option>
                                </select>
                                @error('rate_type') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="price" class="form-label fw-semibold">Price*</label>
                                <input type="number" class="form-control" id="price" wire:model="price" step="0.01"
                                    required>
                                @error('price') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="maximum_court" class="form-label fw-semibold">Maximum Court*</label>
                                <input type="number" class="form-control" id="maximum_court" wire:model="maximum_court"
                                    min="1" required>
                                @error('maximum_court') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">Status*</label>
                                <select class="form-select" id="status" wire:model="status" required>
                                    <option value="" disabled>Select status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                                @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="game_image" class="form-label fw-semibold">Game Image</label>
                                <input type="file" class="form-control" id="game_image" wire:model="game_image"
                                    accept="image/*">
                                @error('game_image') <span class="text-danger small">{{ $message }}</span> @enderror

                                @if($game_image)
                                <div class="mt-2">
                                    <img src="{{ $game_image->temporaryUrl() }}" alt="Preview"
                                        class="img-thumbnail rounded shadow-sm" width="120">
                                </div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" id="description" wire:model="description" rows="3"
                                    placeholder="Optional"></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="advance_required"
                                        wire:model="advance_required">
                                    <label class="form-check-label fw-semibold" for="advance_required">Advance Payment
                                        Required</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Sport</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Sport Modal -->
    <div class="modal fade" id="editSportModal" tabindex="-1" aria-labelledby="editSportModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editSportModalLabel">Edit Sport</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        wire:click="resetForm"></button>
                </div>

                <form wire:submit.prevent="updateSport">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Game Name*</label>
                                <input type="text" class="form-control" wire:model="game_name" required>
                                @error('game_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Game Type*</label>
                                <select class="form-select" wire:model="game_type" required>
                                    <option value="" disabled>Select game type</option>
                                    <option value="Indoor">Indoor</option>
                                    <option value="Outdoor">Outdoor</option>
                                </select>
                                @error('game_type') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Rate Type*</label>
                                <select class="form-select" wire:model="rate_type" required>
                                    <option value="" disabled>Select rate type</option>
                                    <option value="Per hour">Per hour</option>
                                    <option value="Per session">Per session</option>
                                    <option value="Per day">Per day</option>
                                </select>
                                @error('rate_type') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price (LKR)*</label>
                                <input type="number" class="form-control" wire:model="price" min="0" step="0.01"
                                    required>
                                @error('price') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Maximum Courts*</label>
                                <input type="number" class="form-control" wire:model="maximum_court" min="1" required>
                                @error('maximum_court') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status*</label>
                                <select class="form-select" wire:model="status" required>
                                    <option value="" disabled>Select status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                                @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Current Sport Image</label>
                                <div>
                                    <img src="{{ $existingImage ? asset('storage/' . $existingImage) : asset('images/default-sport.jpg') }}" 
                                         class="img-thumbnail" width="150">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="game_image" class="form-label">Upload New Image</label>
                                <input type="file" class="form-control" id="game_image" wire:model="game_image"
                                    accept="image/*">
                                @error('game_image') <span class="text-danger">{{ $message }}</span> @enderror

                                @if ($game_image)
                                <div class="mt-2">
                                    <label class="form-label">Preview New Image:</label>
                                    <img src="{{ $game_image->temporaryUrl() }}" class="img-thumbnail" width="150">
                                </div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" wire:model="description" rows="3"></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:click="resetForm">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="confirmDelete({{ $editSportId }})">
                            Delete
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading wire:target="updateSport" class="spinner-border spinner-border-sm"
                                role="status" aria-hidden="true"></span>
                            Update Sport
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize modals
        const addSportModalEl = document.getElementById('addSportModal');
        const editSportModalEl = document.getElementById('editSportModal');
        const addSportModal = new bootstrap.Modal(addSportModalEl);
        const editSportModal = new bootstrap.Modal(editSportModalEl);

        // Add Sport Modal events
        window.addEventListener('showModal', () => {
            addSportModal.show();
        });

        window.addEventListener('hideModal', () => {
            if (addSportModal._isShown) {
                addSportModal.hide();
            }
        });

        addSportModalEl.addEventListener('hidden.bs.modal', () => {
            Livewire.dispatch('resetForm');
        });

        // Edit Sport Modal events
        window.addEventListener('showEditSportModal', () => {
            editSportModal.show();
        });

        window.addEventListener('hideEditSportModal', () => {
            if (editSportModal._isShown) {
                editSportModal.hide();
            }
        });

        editSportModalEl.addEventListener('hidden.bs.modal', () => {
            Livewire.dispatch('resetForm');
        });

        // Delete confirmation with SweetAlert2
        window.addEventListener('showConfirmation', event => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'animated shake',
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteConfirmed', { id: event.detail.id });
                }
            });
        });

        // Handle successful deletion
        window.addEventListener('sportDeleted', () => {
            Swal.fire({
                title: 'Deleted!',
                text: 'The sport has been deleted.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    popup: 'animated fadeIn'
                }
            }).then(() => {
                // Ensure modal is closed after deletion
                if (editSportModal._isShown) {
                    editSportModal.hide();
                }
            });
        });

        // Handle deletion error
        window.addEventListener('deleteError', event => {
            Swal.fire({
                title: 'Error!',
                text: event.detail.message,
                icon: 'error',
                confirmButtonColor: '#3085d6',
                customClass: {
                    popup: 'animated shake',
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        });
    });
</script>
@endpush
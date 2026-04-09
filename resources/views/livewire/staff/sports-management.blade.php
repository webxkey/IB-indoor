@push('styles')
<style>
    container-fluid {
        background: var(--background-color);
        min-height: 100vh;
        padding: 0;
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

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        padding: 12px 16px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
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
            <button id="addSportButton" class="btn btn-success me-2" wire:click="openModal">
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
                    <img src="{{ $sport->image ?? asset('images/sports_images/default.jpg') }}"
                        alt="Sport Image" class="img-fluid rounded" style="max-height: 245px; object-fit: cover;">
                    <span class="position-absolute top-0 start-0 text-white px-2 py-1 rounded-end 
                    {{ $sport->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                        {{ $sport->status }}
                    </span>

                </div>

                <div class="card-body">
                    <h5 class="card-title">{{ $sport->name }}</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Hourly Rate:</span>
                        <span class="fw-bold">Rs. {{ number_format($sport->price) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Available Courts:</span>
                        <span class="fw-bold">{{ $sport->maximum_court }}</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-sm btn-primary flex-fill"
                            wire:click="editSport({{ $sport->id ?? $sport->id }})" data-bs-toggle="modal"
                            data-bs-target="#editSportModal">
                            <i class="fas fa-edit me-1"></i> Manage
                        </button>
                        <button class="btn btn-sm btn-outline-info" wire:click="openPricingModal({{ $sport->id }})" title="Set Pricing Rules">
                            <i class="fas fa-tags"></i> Pricing
                        </button>
                        <button class="btn btn-sm btn-outline-warning" wire:click="openSlotBlockModal({{ $sport->id }})" title="Manage blocked slots">
                            <i class="fas fa-ban"></i> Blocked Slots
                        </button>
                    </div>
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
                                <select class="form-select" id="game_name" wire:model="game_name" required>
                                    <option value="">Select Sport</option>
                                    <option value="Football">Football</option>
                                    <option value="Cricket">Cricket</option>
                                    <option value="Badminton">Badminton</option>
                                    <option value="Basketball">Basketball</option>
                                    <option value="Pools">Pools</option>
                                    <option value="Pooltable">Pool Table</option>
                                </select>
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

    <!-- Pricing Rules Modal -->
    @if($showPricingModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-tags me-2"></i>Dynamic Pricing Rules</h5>
                    <button type="button" class="btn-close" wire:click="closePricingModal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Leave blank to use the base price. These override the default price per hour.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Peak Hour Price (LKR/hr)</label>
                            <input type="number" class="form-control" wire:model="pricingRules.peak_price" placeholder="e.g. 2500">
                            <small class="text-muted">Weekdays 6–10AM, 4–9PM</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Off-Peak Price (LKR/hr)</label>
                            <input type="number" class="form-control" wire:model="pricingRules.offpeak_price" placeholder="e.g. 1500">
                            <small class="text-muted">All other hours</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Weekend Price (LKR/hr)</label>
                            <input type="number" class="form-control" wire:model="pricingRules.weekend_price" placeholder="e.g. 3000">
                            <small class="text-muted">Saturday & Sunday</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Advance Booking Discount (%)</label>
                            <input type="number" class="form-control" wire:model="pricingRules.advance_discount" placeholder="e.g. 10" min="0" max="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Book X Days in Advance</label>
                            <input type="number" class="form-control" wire:model="pricingRules.advance_days" placeholder="e.g. 3">
                            <small class="text-muted">Discount applies if booked this many days early</small>
                        </div>
                    </div>
                    @error('pricingRules.peak_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    @error('pricingRules.advance_discount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="closePricingModal">Cancel</button>
                    <button type="button" class="btn btn-success" wire:click="savePricingRules" wire:loading.attr="disabled">
                        <span wire:loading wire:target="savePricingRules" class="spinner-border spinner-border-sm me-1"></span>
                        Save Pricing Rules
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Slot Blocking Modal --}}
    @if($showSlotBlockModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-ban me-2"></i>Blocked Slots — {{ $slotBlockSportName }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeSlotBlockModal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Blocked slots appear as <strong>Unavailable</strong> in the booking calendar and cannot be booked from the mobile app.</p>

                    {{-- Add New Block --}}
                    <div class="card border-warning mb-4">
                        <div class="card-header bg-warning bg-opacity-10 fw-semibold small text-uppercase">
                            <i class="fas fa-plus me-1"></i> Add Blocked Slot
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small">Date *</label>
                                    <input type="date" class="form-control form-control-sm" wire:model="slotBlockDate">
                                    @error('slotBlockDate') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Start Time *</label>
                                    <select class="form-select form-select-sm" wire:model="slotBlockTime">
                                        @for($h = 6; $h <= 22; $h++)
                                        <option value="{{ sprintf('%02d', $h) }}:00:00">{{ sprintf('%02d', $h) }}:00</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">End Time *</label>
                                    <select class="form-select form-select-sm" wire:model="slotBlockEndTime">
                                        @for($h = 7; $h <= 23; $h++)
                                        <option value="{{ sprintf('%02d', $h) }}:00:00">{{ sprintf('%02d', $h) }}:00</option>
                                        @endfor
                                    </select>
                                    @error('slotBlockEndTime') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Court *</label>
                                    <input type="text" class="form-control form-control-sm" wire:model="slotBlockCourt" placeholder="1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Reason *</label>
                                    <select class="form-select form-select-sm" wire:model="slotBlockReason">
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Private Event">Private Event</option>
                                        <option value="Tournament">Tournament</option>
                                        <option value="Staff Training">Staff Training</option>
                                        <option value="Unavailable">Unavailable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-warning text-white" wire:click="addSlotBlock" wire:loading.attr="disabled">
                                    <span wire:loading wire:target="addSlotBlock" class="spinner-border spinner-border-sm me-1"></span>
                                    <i class="fas fa-plus me-1"></i>Block This Slot
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Existing Blocks --}}
                    <h6 class="fw-semibold small text-uppercase text-muted mb-2">Current Blocked Slots</h6>
                    @if(count($existingBlocks) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Time Range</th>
                                    <th>Court</th>
                                    <th>Reason</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($existingBlocks as $block)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($block['date'])->format('D, M d Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse('2000-01-01 ' . $block['time'])->format('g:i A') }}
                                        @if(!empty($block['end_time']))
                                        – {{ \Carbon\Carbon::parse('2000-01-01 ' . $block['end_time'])->format('g:i A') }}
                                        @endif
                                    </td>
                                    <td>Court {{ $block['court'] }}</td>
                                    <td><span class="badge bg-warning text-dark">{{ $block['reason'] }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger py-0 px-1"
                                            wire:click="removeSlotBlock('{{ $block['date'] }}', '{{ $block['time'] }}', '{{ $block['court'] }}')"
                                            title="Remove block">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-3 small">
                        <i class="fas fa-check-circle me-1 text-success"></i>No slots blocked for this sport.
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="closeSlotBlockModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Sport Modal -->
    <div class="modal fade" id="editSportModal" tabindex="-1" aria-labelledby="editSportModalLabel" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editSportModalLabel">Edit Sport</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form wire:submit.prevent="updateSport">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Game Name*</label>
                                <input type="text" class="form-control" wire:model="game_name" disabled>
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


                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" wire:model="description" rows="3"></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="edit_advance_required"
                                        wire:model="advance_required">
                                    <label class="form-check-label fw-semibold" for="edit_advance_required">Advance Payment Required</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="deleteSportBtn" wire:click="confirmDelete({{ $editSportId }})">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modals
        const addSportModalEl = document.getElementById('addSportModal');
        const editSportModalEl = document.getElementById('editSportModal');
        const addSportModal = new bootstrap.Modal(addSportModalEl);
        const editSportModal = new bootstrap.Modal(editSportModalEl);
        
        // Helper function to safely move focus out of modal
        function moveFocusOutOfModal() {
            const addBtn = document.getElementById('addSportButton');
            if (addBtn) {
                addBtn.focus();
            } else {
                document.body.focus();
            }
        }
        
        // Helper function to clean up modal artifacts
        function cleanupModalArtifacts() {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }

        // Add Sport Modal events
        window.addEventListener('showModal', () => {
            addSportModal.show();
        });

        window.addEventListener('hideModal', () => {
            moveFocusOutOfModal();
            addSportModal.hide();
        });

        addSportModalEl.addEventListener('hidden.bs.modal', () => {
            Livewire.dispatch('resetForm');
            cleanupModalArtifacts();
        });

        // Edit Sport Modal events
        window.addEventListener('showEditSportModal', () => {
            editSportModal.show();
        });

        window.addEventListener('hideEditSportModal', () => {
            moveFocusOutOfModal();
            editSportModal.hide();
        });

        editSportModalEl.addEventListener('hidden.bs.modal', () => {
            Livewire.dispatch('resetForm');
            cleanupModalArtifacts();
        });

        // Track pending delete confirmation
        let pendingDeleteId = null;

        // Delete confirmation with SweetAlert2
        window.addEventListener('showConfirmation', event => {
            pendingDeleteId = event.detail.id;
            
            // First move focus out of modal to prevent aria-hidden conflict
            moveFocusOutOfModal();
            
            // Then hide the modal
            editSportModal.hide();
        });
        
        // Wait for modal to be fully hidden before showing SweetAlert
        editSportModalEl.addEventListener('hidden.bs.modal', function onHiddenForDelete() {
            if (pendingDeleteId === null) return;
            
            const deleteId = pendingDeleteId;
            pendingDeleteId = null;
            
            // Clean up any remaining modal artifacts
            cleanupModalArtifacts();
            
            // Now show SweetAlert
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
                    confirmButton: 'btn btn-danger mx-1',
                    cancelButton: 'btn btn-secondary mx-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteConfirmed', { id: deleteId });
                } else {
                    // Reopen modal if user cancels
                    editSportModal.show();
                }
            });
        });

        // Handle successful deletion
        window.addEventListener('sportDeleted', () => {
            cleanupModalArtifacts();
            
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
                moveFocusOutOfModal();
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
   <div>
    <div class="container-fluid">
        <!-- Header Card with Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-people-fill me-2"></i>Facility Owners Management
                    </h4>
                    <div class="card-tools">
                        <button class="btn btn-primary" wire:click="createIndoorAdmin">
                            <i class="bi bi-plus-circle me-1"></i> Add Facility Owner
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filters -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search by name, email, contact..."
                                wire:model.live.debounce.300ms="search">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <select class="form-select" wire:model.live="venueFilter">
                            <option value="">All Venues/Complexes</option>
                            @foreach ($venues as $venue)
                                <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <button class="btn btn-outline-secondary w-100" wire:click="clearFilters">
                            <i class="bi bi-x-circle me-1"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indoor Admins Cards View -->
        <div class="row g-4">
            @forelse ($indoorAdmins as $admin)
                <div class="col-12 col-md-6 col-lg-4" wire:key="admin-card-{{ $admin->id }}">
                    <div class="card admin-card h-100 shadow-sm">
                        <!-- Card Header with Avatar -->
                        <div class="card-header-custom">
                            <div class="admin-avatar">
                                @if ($admin->profile_photo_path)
                                    <img src="{{ asset('storage/' . $admin->profile_photo_path) }}" 
                                         alt="{{ $admin->name }}" class="avatar-img">
                                @else
                                    <div class="avatar-initials">
                                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="status-badge">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Active
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body text-center pt-4">
                            <h5 class="card-title mb-1">{{ $admin->name }}</h5>
                            <p class="text-muted small mb-3">
                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</span>
                            </p>

                            <!-- Contact Info -->
                            <div class="admin-details">
                                <div class="detail-item">
                                    <i class="bi bi-envelope text-primary"></i>
                                    <a href="mailto:{{ $admin->email }}" class="text-decoration-none">
                                        {{ $admin->email }}
                                    </a>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-telephone text-success"></i>
                                    <span>{{ $admin->contact ?? 'No contact' }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-calendar3 text-info"></i>
                                    <span>Joined: {{ $admin->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <!-- Assigned Venue Section -->
                            <div class="venue-section mt-3">
                                <h6 class="text-muted small mb-2">
                                    <i class="bi bi-building me-1"></i>Assigned Venue
                                </h6>
                                @if ($admin->complex)
                                    <div class="venue-card">
                                        <div class="venue-image">
                                            @if ($admin->complex->cover_image)
                                                <img src="{{ asset('storage/' . $admin->complex->cover_image) }}" 
                                                     alt="{{ $admin->complex->name }}">
                                            @else
                                                <div class="venue-placeholder">
                                                    <i class="bi bi-building fs-4"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="venue-info">
                                            <strong>{{ $admin->complex->name }}</strong>
                                            <small class="d-block text-muted">
                                                {{ Str::limit($admin->complex->address ?? 'No address', 30) }}
                                            </small>
                                            <span class="badge bg-{{ $admin->complex->status === 'active' ? 'success' : 'secondary' }} mt-1">
                                                {{ ucfirst($admin->complex->status ?? 'N/A') }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning py-2 mb-0">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <small>No venue assigned</small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Footer with Actions -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-info" title="View Details"
                                    wire:click="viewDetails({{ $admin->id }})" wire:loading.attr="disabled">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="Edit"
                                    wire:click="editIndoorAdmin({{ $admin->id }})" wire:loading.attr="disabled">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Reset Password"
                                    wire:click="openResetPasswordModal({{ $admin->id }})" wire:loading.attr="disabled">
                                    <i class="bi bi-key"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete"
                                    wire:click="confirmDelete({{ $admin->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-people display-1 text-muted"></i>
                                <h5 class="mt-3 text-muted">No Indoor Admins Found</h5>
                                <p class="text-muted">
                                    @if ($search || $venueFilter)
                                        Try adjusting your search filters.
                                    @else
                                        Click "Add Indoor Admin" to create your first admin.
                                    @endif
                                </p>
                                @if ($search || $venueFilter)
                                    <button class="btn btn-outline-primary" wire:click="clearFilters">
                                        <i class="bi bi-x-circle me-1"></i>Clear Filters
                                    </button>
                                @else
                                    <button class="btn btn-primary" wire:click="createIndoorAdmin">
                                        <i class="bi bi-plus-circle me-1"></i>Add Indoor Admin
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($indoorAdmins->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $indoorAdmins->links() }}
            </div>
        @endif

        <!-- Create Indoor Admin Modal -->
        <div wire:ignore.self class="modal fade" id="createIndoorAdminModal" tabindex="-1"
            aria-labelledby="createIndoorAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="createIndoorAdminModalLabel">
                            <i class="bi bi-person-plus me-2"></i>Create Indoor Admin
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" wire:model="name"
                                    placeholder="Enter full name">
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="contactNumber" class="form-label">Contact Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contactNumber"
                                    wire:model="contactNumber" placeholder="Enter contact number">
                                @error('contactNumber')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" wire:model="email"
                                    placeholder="Enter email address">
                                @error('email')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="complexId" class="form-label">Assign to Venue/Complex <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="complexId" wire:model="complexId">
                                    <option value="">Select Venue/Complex</option>
                                    @foreach ($venues as $venue)
                                        <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                                    @endforeach
                                </select>
                                @error('complexId')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-12 col-md-6">
                                <label for="createPassword" class="form-label">Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="createPassword"
                                        wire:model="password" placeholder="Enter password">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePasswordVisibility('createPassword')">
                                        <i class="bi bi-eye" id="createPasswordToggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="createConfirmPassword" class="form-label">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="createConfirmPassword"
                                        wire:model="confirmPassword" placeholder="Confirm password">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePasswordVisibility('createConfirmPassword')">
                                        <i class="bi bi-eye" id="createConfirmPasswordToggleIcon"></i>
                                    </button>
                                </div>
                                @error('confirmPassword')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="saveIndoorAdmin"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveIndoorAdmin">
                                <i class="bi bi-check-circle me-1"></i>Create Admin
                            </span>
                            <span wire:loading wire:target="saveIndoorAdmin">
                                <i class="spinner-border spinner-border-sm me-1"></i>Creating...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Indoor Admin Modal -->
        <div wire:ignore.self class="modal fade" id="editIndoorAdminModal" tabindex="-1"
            aria-labelledby="editIndoorAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editIndoorAdminModalLabel">
                            <i class="bi bi-pencil-square me-2"></i>Edit Indoor Admin
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="editName" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editName" wire:model="editName"
                                    placeholder="Enter full name">
                                @error('editName')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="editContactNumber" class="form-label">Contact Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editContactNumber"
                                    wire:model="editContactNumber" placeholder="Enter contact number">
                                @error('editContactNumber')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-12 col-md-6">
                                <label for="editEmail" class="form-label">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="editEmail" wire:model="editEmail"
                                    placeholder="Enter email address">
                                @error('editEmail')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="editComplexId" class="form-label">Assign to Venue/Complex <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="editComplexId" wire:model="editComplexId">
                                    <option value="">Select Venue/Complex</option>
                                    @foreach ($venues as $venue)
                                        <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                                    @endforeach
                                </select>
                                @error('editComplexId')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle me-1"></i>
                            Leave password fields blank to keep the current password.
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="editPassword" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="editPassword"
                                        wire:model="editPassword" placeholder="Enter new password">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePasswordVisibility('editPassword')">
                                        <i class="bi bi-eye" id="editPasswordToggleIcon"></i>
                                    </button>
                                </div>
                                @error('editPassword')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="editConfirmPassword" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="editConfirmPassword"
                                        wire:model="editConfirmPassword" placeholder="Confirm new password">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePasswordVisibility('editConfirmPassword')">
                                        <i class="bi bi-eye" id="editConfirmPasswordToggleIcon"></i>
                                    </button>
                                </div>
                                @error('editConfirmPassword')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="updateIndoorAdmin"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updateIndoorAdmin">
                                <i class="bi bi-check-circle me-1"></i>Update Admin
                            </span>
                            <span wire:loading wire:target="updateIndoorAdmin">
                                <i class="spinner-border spinner-border-sm me-1"></i>Updating...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Details Modal -->
        <div wire:ignore.self class="modal fade" id="viewIndoorAdminModal" tabindex="-1"
            aria-labelledby="viewIndoorAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="viewIndoorAdminModalLabel">
                            <i class="bi bi-person-badge me-2"></i>Indoor Admin Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($viewAdmin)
                            <div class="row">
                                <div class="col-12 col-md-4 text-center mb-4">
                                    <div class="avatar-circle-lg mx-auto mb-3">
                                        {{ strtoupper(substr($viewAdmin->name, 0, 1)) }}
                                    </div>
                                    <h5 class="mb-1">{{ $viewAdmin->name }}</h5>
                                    <span class="badge bg-primary">{{ ucfirst($viewAdmin->role) }}</span>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-3 text-muted">
                                                <i class="bi bi-person me-2"></i>Profile Information
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <label class="text-muted small">Email</label>
                                                    <p class="mb-0">
                                                        <a href="mailto:{{ $viewAdmin->email }}">{{ $viewAdmin->email }}</a>
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <label class="text-muted small">Contact</label>
                                                    <p class="mb-0">{{ $viewAdmin->contact ?? '-' }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <label class="text-muted small">Created</label>
                                                    <p class="mb-0">{{ $viewAdmin->created_at->format('M d, Y H:i') }}
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <label class="text-muted small">Last Updated</label>
                                                    <p class="mb-0">{{ $viewAdmin->updated_at->format('M d, Y H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card bg-light mt-3">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-3 text-muted">
                                                <i class="bi bi-building me-2"></i>Assigned Venue/Complex
                                            </h6>
                                            @if ($viewAdmin->complex)
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if ($viewAdmin->complex->cover_image)
                                                            <img src="{{ asset('storage/' . $viewAdmin->complex->cover_image) }}"
                                                                alt="{{ $viewAdmin->complex->name }}"
                                                                class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded"
                                                                style="width: 60px; height: 60px;">
                                                                <i class="bi bi-building fs-4"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $viewAdmin->complex->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $viewAdmin->complex->address ?? 'No address' }}</small>
                                                        <br>
                                                        <span class="badge bg-{{ $viewAdmin->complex->status === 'active' ? 'success' : 'secondary' }}">
                                                            {{ ucfirst($viewAdmin->complex->status ?? 'N/A') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning mb-0">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    No venue/complex assigned to this admin.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        @if ($viewAdmin)
                            <button type="button" class="btn btn-primary"
                                wire:click="editIndoorAdmin({{ $viewAdmin->id }})" data-bs-dismiss="modal">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Reset Password Modal -->
        <div wire:ignore.self class="modal fade" id="resetPasswordModal" tabindex="-1"
            aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="resetPasswordModalLabel">
                            <i class="bi bi-key me-2"></i>Reset Password
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Resetting password for: <strong>{{ $resetPasswordAdminName ?? '' }}</strong>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" wire:model="newPassword"
                                    placeholder="Enter new password">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePasswordVisibility('newPassword')">
                                    <i class="bi bi-eye" id="newPasswordToggleIcon"></i>
                                </button>
                            </div>
                            @error('newPassword')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="confirmNewPassword" class="form-label">Confirm New Password <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmNewPassword"
                                    wire:model="confirmNewPassword" placeholder="Confirm new password">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePasswordVisibility('confirmNewPassword')">
                                    <i class="bi bi-eye" id="confirmNewPasswordToggleIcon"></i>
                                </button>
                            </div>
                            @error('confirmNewPassword')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="generatePassword">
                            <i class="bi bi-lightning me-1"></i>Generate Random Password
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-warning" wire:click="resetPassword"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="resetPassword">
                                <i class="bi bi-check-circle me-1"></i>Reset Password
                            </span>
                            <span wire:loading wire:target="resetPassword">
                                <i class="spinner-border spinner-border-sm me-1"></i>Resetting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Delete confirmation
        window.addEventListener('confirm-delete-indoor-admin', event => {
            Swal.fire({
                title: "Are you sure?",
                text: "This will permanently delete this indoor admin. You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('confirmDeleteIndoorAdmin');
                }
            });
        });

        // Edit modal
        window.addEventListener('edit-indoor-admin-modal', event => {
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('editIndoorAdminModal'));
                modal.show();
            }, 100);
        });

        // View details modal
        window.addEventListener('view-indoor-admin-modal', event => {
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('viewIndoorAdminModal'));
                modal.show();
            }, 100);
        });

        // Reset password modal
        window.addEventListener('reset-password-modal', event => {
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
                modal.show();
            }, 100);
        });

        // Password toggle visibility function
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(inputId + 'ToggleIcon');

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.classList.remove("bi-eye");
                toggleIcon.classList.add("bi-eye-slash");
            } else {
                passwordInput.type = "password";
                toggleIcon.classList.remove("bi-eye-slash");
                toggleIcon.classList.add("bi-eye");
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        /* Admin Card Styles */
        .admin-card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100px;
            position: relative;
        }

        .admin-avatar {
            position: absolute;
            bottom: -40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
        }

        .avatar-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid #fff;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .avatar-initials {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid #fff;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .admin-card .card-body {
            padding-top: 50px !important;
        }

        .admin-details {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.875rem;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-item i {
            width: 20px;
            text-align: center;
        }

        .detail-item a {
            color: #667eea;
            word-break: break-all;
        }

        .detail-item a:hover {
            color: #764ba2;
        }

        /* Venue Section */
        .venue-section {
            border-top: 1px dashed #dee2e6;
            padding-top: 1rem;
        }

        .venue-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 0.75rem;
            text-align: left;
        }

        .venue-image img {
            width: 50px;
            height: 50px;
            border-radius: 0.5rem;
            object-fit: cover;
        }

        .venue-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .venue-info {
            flex: 1;
            min-width: 0;
        }

        .venue-info strong {
            display: block;
            font-size: 0.875rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Card Footer */
        .admin-card .card-footer {
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        /* Empty State */
        .empty-state {
            padding: 2rem;
        }

        .empty-state i {
            opacity: 0.5;
        }

        /* Avatar circles for modals */
        .avatar-circle {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
        }

        .avatar-circle-lg {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 2rem;
        }

        .btn-group-sm>.btn {
            padding: 0.35rem 0.5rem;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        .modal-header {
            border-radius: 0.375rem 0.375rem 0 0;
        }

        /* Responsive improvements */
        @media (max-width: 767.98px) {
            .admin-card {
                margin-bottom: 1rem;
            }

            .avatar-initials,
            .avatar-img {
                width: 70px;
                height: 70px;
                font-size: 1.5rem;
            }

            .admin-avatar {
                bottom: -35px;
            }

            .admin-card .card-body {
                padding-top: 45px !important;
            }

            .detail-item {
                font-size: 0.8rem;
            }

            .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            .btn-group-sm>.btn {
                padding: 0.25rem 0.4rem;
            }
        }

        /* Modal responsive */
        @media (max-width: 575.98px) {
            .modal-footer {
                flex-direction: column;
            }

            .modal-footer .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .modal-footer .btn:last-child {
                margin-bottom: 0;
            }
        }
    </style>
@endpush

<div class="container-fluid">
    <!-- Flash Messages -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Total Users</h6>
                            <h2 class="mb-0">{{ $totalUsers }}</h2>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                15% from last month
                            </small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Active Users</h6>
                            <h2 class="mb-0">{{ $activeUsers }}</h2>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                8% from last week
                            </small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Registrations -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">New This Week</h6>
                            <h2 class="mb-0">{{ $newThisWeek }}</h2>
                            <small class="text-danger">
                                <i class="fas fa-arrow-down me-1"></i>
                                2% from last week
                            </small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Premium Users</h6>
                            <h2 class="mb-0">{{ $premiumUsers }}</h2>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                22% from last month
                            </small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-crown"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="userStatusFilter" class="form-label">Status</label>
                    <select class="form-select" id="userStatusFilter" wire:model.live="statusFilter">
                        <option value="" selected>All Users</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="registrationDateFilter" class="form-label">Registration Date</label>
                    <select class="form-select" id="registrationDateFilter" wire:model.live="registrationDateFilter">
                        <option value="" selected>All Time</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="searchUsers" class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchUsers" placeholder="Name, email or phone"
                            wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-outline-secondary" wire:click="clearFilters">
                        <i class="fas fa-redo me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table Section -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">App Users</h5>
            <div>
                <button class="btn btn-success me-2" wire:click="openAddUserModal">
                    <i class="fas fa-plus me-1"></i> Add User
                </button>
                <button class="btn btn-outline-primary">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->profile_picture ? asset('storage/'.$user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->first_name.'+'.$user->last_name).'&background=random' }}"
                                        class="rounded-circle me-3" width="40" height="40"
                                        alt="{{ $user->first_name }} {{ $user->last_name }}">
                                    <div>
                                        <h6 class="mb-0">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $user->email }}</div>
                                <small class="text-muted">{{ $user->phone_number ?? 'No phone' }}</small>
                            </td>
                            <td>{{ $user->last_login ? $user->last_login->format('d M Y') : 'Never' }}</td>
                            <td>
                                @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                                @if($user->points >= 1000)
                                <span class="badge bg-warning text-dark ms-1">Premium</span>
                                @endif
                            </td>
                            <td>{{ $user->points }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info me-1" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button
                                    class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                    title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                    wire:click="toggleStatus({{ $user->id }})">
                                    <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                {{ $users->links() }}
            </nav>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true" wire:ignore.self @if($showModal)
        style="display: block; background: rgba(0,0,0,0.5);" @endif>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeAddUserModal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="addUser">
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name*</label>
                            <input type="text" class="form-control @error('firstName') is-invalid @enderror"
                                id="firstName" wire:model="firstName" required>
                            @error('firstName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name*</label>
                            <input type="text" class="form-control @error('lastName') is-invalid @enderror"
                                id="lastName" wire:model="lastName" required>
                            @error('lastName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email*</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                wire:model="email" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone</label>
                            <input type="tel" class="form-control @error('phoneNumber') is-invalid @enderror"
                                id="phoneNumber" wire:model="phoneNumber">
                            @error('phoneNumber') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password*</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" wire:model="password" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isActive" wire:model="isActive">
                                <label class="form-check-label" for="isActive">Active User</label>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="sendWelcomeEmail"
                                wire:model="sendWelcomeEmail">
                            <label class="form-check-label" for="sendWelcomeEmail">
                                Send welcome email
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeAddUserModal">Cancel</button>
                    <button type="button" class="btn btn-success" wire:click="addUser">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Backdrop for modal -->
    @if($showModal)
    <div class="modal-backdrop fade show" wire:click="closeAddUserModal"></div>
    @endif


</div>
@push('scripts')
        <script>
        // Handle modal show/hide with Livewire
        document.addEventListener('livewire:init', function() {
            // Check if the modal element exists before trying to use it
            const modalElement = document.getElementById('addUserModal');
            
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                
                // Show modal when Livewire triggers it
                Livewire.on('showModal', () => {
                    modal.show();
                });
                
                // Hide modal when Livewire triggers it
                Livewire.on('closeModal', () => {
                    modal.hide();
                });
            }
        });
    </script>
@endpush
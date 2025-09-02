<div class="container-fluid">
    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Total Users</h6>
                            <h2 class="mb-0"><?php echo e($totalUsers); ?></h2>
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
                            <h2 class="mb-0"><?php echo e($activeUsers); ?></h2>
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
                            <h2 class="mb-0"><?php echo e($newThisWeek); ?></h2>
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
                            <h2 class="mb-0"><?php echo e($premiumUsers); ?></h2>
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
                <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
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
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo e($user->profile_picture ? asset('storage/'.$user->profile_picture) : 'https://randomuser.me/api/portraits/men/'.rand(1, 99).'.jpg'); ?>"
                                        class="rounded-circle me-3" width="40" height="40"
                                        alt="<?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?>">
                                    <div>
                                        <h6 class="mb-0"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></h6>
                                        <small class="text-muted"><?php echo e($user->email); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><?php echo e($user->email); ?></div>
                                <small class="text-muted"><?php echo e($user->phone_number ?? 'No phone'); ?></small>
                            </td>
                            <td><?php echo e($user->last_login ? $user->last_login->format('d M Y') : 'Never'); ?></td>
                            <td>
                                <!--[if BLOCK]><![endif]--><?php if($user->is_active): ?>
                                <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <!--[if BLOCK]><![endif]--><?php if($user->points >= 1000): ?>
                                <span class="badge bg-warning text-dark ms-1">Premium</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td><?php echo e($user->points); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info me-1" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button
                                    class="btn btn-sm <?php echo e($user->is_active ? 'btn-outline-danger' : 'btn-outline-success'); ?>"
                                    title="<?php echo e($user->is_active ? 'Deactivate' : 'Activate'); ?>"
                                    wire:click="toggleStatus(<?php echo e($user->id); ?>)">
                                    <i class="fas <?php echo e($user->is_active ? 'fa-ban' : 'fa-check'); ?>"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                <?php echo e($users->links('vendor.livewire.bootstrap')); ?>

            </nav>
        </div>
    </div>

    <!-- Add User Modal (hidden by default) -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label for="userFirstName" class="form-label">First Name*</label>
                            <input type="text" class="form-control" id="userFirstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="userLastName" class="form-label">Last Name*</label>
                            <input type="text" class="form-control" id="userLastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email*</label>
                            <input type="email" class="form-control" id="userEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="userPhone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="userPhone">
                        </div>
                        <div class="mb-3">
                            <label for="userStatus" class="form-label">Status*</label>
                            <select class="form-select" id="userStatus" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>

                        </div>
                        <div class="mb-3">
                            <label for="userPassword" class="form-label">Password*</label>
                            <input type="password" class="form-control" id="userPassword" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="sendWelcomeEmail">
                            <label class="form-check-label" for="sendWelcomeEmail">
                                Send welcome email 
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveUserBtn">Save User</button>
                </div>
            </div>
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
    <script>

    document.addEventListener('livewire:init', function() {

    const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
    // Handle Add User button click
    document.querySelector('.btn-success[data-bs-target="#addUserModal"]').addEventListener('click', function() {
        addUserModal.show();
    });

    // Handle Save User button click
    document.getElementById('saveUserBtn').addEventListener('click', function() {
        const form = document.getElementById('addUserForm');
        if (form.checkValidity()) {
            alert('User added successfully! (This is a frontend demo)');
            addUserModal.hide();
            form.reset();
        } else {
            form.reportValidity();
        }
    });
    });
    </script>
    <?php $__env->stopPush(); ?>
</div><?php /**PATH C:\Users\MY\Desktop\indoor\resources\views/livewire/admin/customers.blade.php ENDPATH**/ ?>
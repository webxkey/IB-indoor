
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stat-card {
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }
        .card-img-top {
            height: 180px;
            object-fit: cover;
        }
        .list-view-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        .active-view {
            background-color: #0d6efd;
            color: white !important;
        }
        .badge-icon {
            font-size: 0.8em;
            margin-right: 3px;
        }
    </style>
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
                                    <h2 class="mb-0">1,248</h2>
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
                                    <h2 class="mb-0">892</h2>
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
                                    <h2 class="mb-0">56</h2>
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
                                    <h2 class="mb-0">324</h2>
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
                            <select class="form-select" id="userStatusFilter">
                                <option value="" selected>All Users</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="registrationDateFilter" class="form-label">Registration Date</label>
                            <select class="form-select" id="registrationDateFilter">
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
                                <input type="text" class="form-control" id="searchUsers" placeholder="Name, email or phone">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Apply
                            </button>
                            <button class="btn btn-outline-secondary">
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
                        <button class="btn btn-success me-2">
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
                                    <th>Registration</th>
                                    <th>Status</th>
                                    <th>Last Active</th>
                                    <th>Bookings</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- User 1 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://randomuser.me/api/portraits/men/32.jpg" 
                                                class="rounded-circle me-3" width="40" height="40">
                                            <div>
                                                <h6 class="mb-0">John Smith</h6>
                                                <small class="text-muted">@johnsmith</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>john.smith@example.com</div>
                                        <small class="text-muted">+1 (555) 123-4567</small>
                                    </td>
                                    <td>15 Jan 2023</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>2 hours ago</td>
                                    <td>24</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Suspend">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- User 2 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://randomuser.me/api/portraits/women/44.jpg" 
                                                class="rounded-circle me-3" width="40" height="40">
                                            <div>
                                                <h6 class="mb-0">Sarah Johnson</h6>
                                                <small class="text-muted">@sarahj</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>sarah.j@example.com</div>
                                        <small class="text-muted">+1 (555) 987-6543</small>
                                    </td>
                                    <td>22 Feb 2023</td>
                                    <td><span class="badge bg-warning text-dark">Premium</span></td>
                                    <td>1 day ago</td>
                                    <td>42</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Suspend">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- User 3 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://randomuser.me/api/portraits/men/75.jpg" 
                                                class="rounded-circle me-3" width="40" height="40">
                                            <div>
                                                <h6 class="mb-0">Michael Brown</h6>
                                                <small class="text-muted">@michaelb</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>michael.b@example.com</div>
                                        <small class="text-muted">+1 (555) 456-7890</small>
                                    </td>
                                    <td>05 Mar 2023</td>
                                    <td><span class="badge bg-secondary">Inactive</span></td>
                                    <td>3 weeks ago</td>
                                    <td>8</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" title="Activate">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- User 4 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://randomuser.me/api/portraits/women/68.jpg" 
                                                class="rounded-circle me-3" width="40" height="40">
                                            <div>
                                                <h6 class="mb-0">Emily Davis</h6>
                                                <small class="text-muted">@emilyd</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>emily.d@example.com</div>
                                        <small class="text-muted">+1 (555) 789-0123</small>
                                    </td>
                                    <td>18 Apr 2023</td>
                                    <td><span class="badge bg-danger">Suspended</span></td>
                                    <td>1 month ago</td>
                                    <td>15</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" title="Reinstate">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>


<!-- Add User Modal (hidden by default) -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Add New User</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addUserForm">
                                <div class="mb-3">
                                    <label for="userFullName" class="form-label">Full Name*</label>
                                    <input type="text" class="form-control" id="userFullName" required>
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
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="premium">Premium</option>
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
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modals
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

    // You could add more functionality here like:
    // - Filtering users
    // - Editing existing users
    // - User status toggling
});
</script>
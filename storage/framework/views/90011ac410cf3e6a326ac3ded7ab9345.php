

<div class="container-fluid">
    
    <!-- Flash Messages -->
    <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php if(session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <!-- Today's Bookings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Today's Bookings</h6>
                            <h2 class="mb-0"><?php echo e($todayBookingsCount); ?></h2>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                <!-- This would require comparison with previous day data -->
                                12% from yesterday
                            </small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canceled Bookings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Canceled Bookings</h6>
                            <h2 class="mb-0"><?php echo e($canceledBookingsCount); ?></h2>
                            <small class="text-danger">
                                <i class="fas fa-arrow-up me-1"></i>
                                <!-- This would require comparison with previous period data -->
                                3% from yesterday
                            </small>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Pending Bookings</h6>
                            <h2 class="mb-0"><?php echo e($pendingBookingsCount); ?></h2>
                            <small class="text-warning">
                                <i class="fas fa-arrow-down me-1"></i>
                                <!-- This would require comparison with previous period data -->
                                5% from yesterday
                            </small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Total Revenue</h6>
                            <h2 class="mb-0">$<?php echo e(number_format($totalRevenue, 2)); ?></h2>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                <!-- This would require comparison with previous period data -->
                                18% from yesterday
                            </small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-dollar-sign"></i>
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
                    <label for="dateFilter" class="form-label">Date Range</label>
                    <select class="form-select" id="dateFilter" wire:model="dateFilter" wire:change="updatedDateFilter">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter" wire:model="statusFilter">
                        <option value="" selected>All Status</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="pending">Pending</option>
                        <option value="canceled">Canceled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="venueFilter" class="form-label">Venue</label>
                    <select class="form-select" id="venueFilter" wire:model="venueFilter">
                        <option value="" selected>All Venues</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $venues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($venue->id); ?>"><?php echo e($venue->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary me-2" wire:click="applyFilters">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <button class="btn btn-outline-secondary" wire:click="resetFilters">
                        <i class="fas fa-redo me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Venue-wise Bookings Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Venue Bookings & Revenue</h5>
            <p class="text-muted mb-0">Sorted by highest revenue</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Venue</th>
                            <th>Total Bookings</th>
                            <th>Confirmed</th>
                            <th>Pending</th>
                            <th>Canceled</th>
                            <th>Total Revenue</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $venueStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo e($venue['image'] ?? 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg'); ?>"
                                        class="rounded me-3" width="60" height="40" style="object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0"><?php echo e($venue['name']); ?></h6>
                                        <small class="text-muted"><?php echo e($venue['location']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo e($venue['total_bookings']); ?></td>
                            <td><span class="badge bg-success"><?php echo e($venue['confirmed']); ?></span></td>
                            <td><span class="badge bg-warning"><?php echo e($venue['pending']); ?></span></td>
                            <td><span class="badge bg-danger"><?php echo e($venue['canceled']); ?></span></td>
                            <td class="text-success fw-bold">$<?php echo e(number_format($venue['revenue'], 2)); ?></td>
                            <td>

                                <button class="btn btn-sm btn-outline-primary"
                                    wire:click="$dispatch('showVenueDetails', { id: <?php echo e($venue['id']); ?> })">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Section -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Bookings</h5>
            <button class="btn btn-sm btn-primary">
                <i class="fas fa-download me-1"></i> Export
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Booking ID</th>
                            <th>Venue</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>#BK-<?php echo e($booking->id); ?></td>
                            <td><?php echo e($booking->venue->name ?? 'N/A'); ?></td>
                            <td><?php echo e($booking->user_name); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($booking->booking_date)->format('M d, Y')); ?>, <?php echo e($booking->start_time); ?> - <?php echo e($booking->end_time); ?></td>
                            <td>$<?php echo e(number_format($booking->total, 2)); ?></td>
                            <td>
                                <!--[if BLOCK]><![endif]--><?php if($booking->status == 'confirmed'): ?>
                                <span class="badge bg-success">Confirmed</span>
                                <?php elseif($booking->status == 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                                <?php elseif($booking->status == 'canceled'): ?>
                                <span class="badge bg-danger">Canceled</span>
                                <?php else: ?>
                                <span class="badge bg-secondary"><?php echo e($booking->status); ?></span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" disabled>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" disabled>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
         <?php echo e($recentBookings->links('vendor.livewire.bootstrap')); ?>


        </div>
    </div>

    <!-- View Venue Details Modal -->
    <div class="modal fade" id="viewVenueModal" tabindex="-1" aria-labelledby="viewVenueLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewVenueLabel">Venue Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">
                            <img id="venueImage" src="" alt="Venue Image" class="img-fluid rounded">
                        </div>
                        <div class="col-md-7">
                            <h5 id="venueName">Venue Name</h5>
                            <p id="venueLocation" class="text-muted">Location</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Total Bookings:</strong> <span
                                        id="totalBookings"></span></li>
                                <li class="list-group-item"><strong>Confirmed:</strong> <span
                                        id="confirmedBookings"></span></li>
                                <li class="list-group-item"><strong>Pending:</strong> <span id="pendingBookings"></span>
                                </li>
                                <li class="list-group-item"><strong>Canceled:</strong> <span
                                        id="canceledBookings"></span></li>
                                <li class="list-group-item"><strong>Total Revenue:</strong> <span
                                        id="totalRevenue"></span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        const venueStats = <?php echo json_encode($venueStats, 15, 512) ?>;

    document.addEventListener('livewire:initialized', () => {
        const modal = new bootstrap.Modal(document.getElementById('viewVenueModal'));
        
        // Listen for the event to show venue details
        Livewire.on('showVenueDetails', (event) => {
            const venue = venueStats.find(v => v.id === event.id);
            
            if (venue) {
                document.getElementById('venueName').innerText = venue.name;
                document.getElementById('venueLocation').innerText = venue.location;
                document.getElementById('venueImage').src = venue.image ?? 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg';
                document.getElementById('totalBookings').innerText = venue.total_bookings;
                document.getElementById('confirmedBookings').innerText = venue.confirmed;
                document.getElementById('pendingBookings').innerText = venue.pending;
                document.getElementById('canceledBookings').innerText = venue.canceled;
                document.getElementById('totalRevenue').innerText = '$' + Number(venue.revenue).toFixed(2);
            } else {
                // Fallback if no venue found (unlikely, but good to have)
                document.getElementById('venueName').innerText = 'Venue Not Found';
                document.getElementById('venueLocation').innerText = '';
                document.getElementById('venueImage').src = 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg';
                document.getElementById('totalBookings').innerText = 'N/A';
                document.getElementById('confirmedBookings').innerText = 'N/A';
                document.getElementById('pendingBookings').innerText = 'N/A';
                document.getElementById('canceledBookings').innerText = 'N/A';
                document.getElementById('totalRevenue').innerText = '$0.00';
            }
            
            modal.show();
        });
    });
    </script>
    <?php $__env->stopPush(); ?>
</div><?php /**PATH C:\Users\MY\Desktop\indoor\resources\views/livewire/admin/bookings.blade.php ENDPATH**/ ?>
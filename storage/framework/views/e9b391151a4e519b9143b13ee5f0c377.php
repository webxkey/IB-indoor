
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
    <!-- Dashboard Content (Default) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Dashboard</h1>
            <p class="text-muted">Manage, track, and optimize your indoor ground bookings with ease.</p>
        </div>
        <div>
            <button class="btn btn-success me-2" onclick="showAddBookingModal()">
                <i class="fas fa-plus me-1"></i>
                Add Booking
            </button>
            <button class="btn btn-outline-secondary me-2">
                <i class="fas fa-download me-1"></i>
                Export Data
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title opacity-75">Total Indoors</h6>
                            <h2 class="mb-0"><?php echo e($totalIndoors); ?></h2>
                            <small class="opacity-75">
                                <i class="fas fa-arrow-up me-1"></i>
                                Increased from last month
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Total Bookings</h6>
                            <h2 class="mb-0"><?php echo e($totalBookings); ?></h2>
                            <small class="text-muted">
                                <i class="fas fa-arrow-up text-success me-1"></i>
                                Increased from last month
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Total Users</h6>
                            <h2 class="mb-0"><?php echo e($totalUsers); ?></h2>
                            <small class="text-muted">
                                <i class="fas fa-arrow-down text-warning me-1"></i>
                                Decreased from last month
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Pending Requests</h6>
                            <h2 class="mb-0"><?php echo e($pendingRequests); ?></h2>
                            <small class="text-muted">
                                On Discussion
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Booking Analytics -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Booking Analytics</h5>
                </div>
                <div class="card-body">
                    <canvas id="bookingChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Upcoming Bookings</h5>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>
                        New
                    </button>
                </div>
                <div class="card-body">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $upcomingBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="upcoming-booking mb-3">
                        <div class="d-flex align-items-center">
                            <div class="booking-icon me-3">
                                <i class="fas fa-futbol text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?php echo e($booking->game_name); ?> - <?php echo e($booking->venue->name ?? 'Unknown Venue'); ?></h6>
                                <small class="text-muted">
                                    <?php echo e(\Carbon\Carbon::parse($booking->booking_date)->format('M d, Y')); ?>, 
                                    <?php echo e(\Carbon\Carbon::parse($booking->start_time)->format('g:i A')); ?> - 
                                    <?php echo e(\Carbon\Carbon::parse($booking->end_time)->format('g:i A')); ?>

                                </small>
                            </div>
                            <button class="btn btn-success btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                View Details
                            </button>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-3">
                        <p class="text-muted">No upcoming bookings</p>
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize booking analytics chart
        const ctx = document.getElementById('bookingChart').getContext('2d');
        const bookingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($bookingAnalytics['labels'], 15, 512) ?>,
                datasets: [{
                    label: 'Bookings per Month',
                    data: <?php echo json_encode($bookingAnalytics['data'], 15, 512) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script><?php /**PATH C:\Users\MY\Desktop\indoor\resources\views/livewire/admin/admin-dashboard.blade.php ENDPATH**/ ?>
<div class="container-fluid">
    <!-- Dashboard Content (Default) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Dashboard</h1>
            <p class="text-muted">Manage, track, and optimize your indoor ground bookings with ease.</p>
        </div>
        <div>
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
                            <h2 class="mb-0">{{ $totalIndoors }}</h2>
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
                            <h2 class="mb-0">{{ $totalBookings }}</h2>
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
                            <h2 class="mb-0">{{ $totalUsers }}</h2>
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
                            <h2 class="mb-0">{{ $pendingRequests }}</h2>
                            <small class="text-muted">On Discussion</small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title opacity-75">Total Revenue</h6>
                            <h2 class="mb-0">LKR {{ number_format($totalRevenue, 2) }}</h2>
                            <small class="opacity-75">From completed bookings</small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
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
                    @forelse($upcomingBookings as $booking)
                    <div class="upcoming-booking mb-3">
                        <div class="d-flex align-items-center">
                            <div class="booking-icon me-3">
                                <i class="fas fa-futbol text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $booking->game_name }} - {{ $booking->venue->name ?? 'Unknown Venue'
                                    }}</h6>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }},
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                </small>
                            </div>
                            <button class="btn btn-success btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                View Details
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <p class="text-muted">No upcoming bookings</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Row -->
    <div class="row">
        <!-- Revenue Trend (last 30 days) -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Revenue Trend (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution + Sport Popularity stacked -->
        <div class="col-lg-6 mb-4">
            <!-- Booking Status Distribution -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Booking Status Distribution</h5>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <canvas id="statusChart" width="300" height="200"></canvas>
                </div>
            </div>

            <!-- Sport Popularity -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Sport Popularity (Top 5)</h5>
                </div>
                <div class="card-body">
                    <canvas id="sportChart" width="400" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Occupancy Rate (last 7 days) -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Occupancy Rate (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="occupancyChart" width="400" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
` <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize booking analytics chart
        const ctx = document.getElementById('bookingChart').getContext('2d');
        const bookingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($bookingAnalytics['labels']),
                datasets: [{
                    label: 'Bookings per Month',
                    data: @json($bookingAnalytics['data']),
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

        // Revenue Trend - Line Chart (last 30 days)
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueChartData['labels']),
                datasets: [{
                    label: 'Revenue (LKR)',
                    data: @json($revenueChartData['data']),
                    backgroundColor: 'rgba(75, 192, 192, 0.15)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    pointRadius: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'LKR ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Booking Status Distribution - Pie Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: @json($statusChartData['labels']),
                datasets: [{
                    data: @json($statusChartData['data']),
                    backgroundColor: @json($statusChartData['colors']),
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });

        // Sport Popularity - Horizontal Bar Chart (top 5)
        const sportCtx = document.getElementById('sportChart').getContext('2d');
        new Chart(sportCtx, {
            type: 'bar',
            data: {
                labels: @json($sportPopularityData['labels']),
                datasets: [{
                    label: 'Bookings',
                    data: @json($sportPopularityData['data']),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 205, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // Occupancy Rate - Bar Chart (last 7 days)
        const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
        new Chart(occupancyCtx, {
            type: 'bar',
            data: {
                labels: @json($occupancyChartData['labels']),
                datasets: [{
                    label: 'Bookings per Day',
                    data: @json($occupancyChartData['data']),
                    backgroundColor: 'rgba(153, 102, 255, 0.5)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endpush

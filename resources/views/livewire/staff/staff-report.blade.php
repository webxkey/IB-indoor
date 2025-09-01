@push('styles')
<style>
    :root {
        --primary-color: #195f0b;
        --primary-gradient: linear-gradient(135deg, #195f0b 0%, #198754 100%);
        --secondary-color: #6c757d;
        --available-color: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        --available-border: #bae6fd;
        --available-hover: linear-gradient(135deg, #0ea5e9 0%, #195f0b 100%);
        --booked-color: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
        --booked-border: #fca5a5;
        --pending-color: linear-gradient(135deg, #fffbeb 0%, #fed7aa 100%);
        --pending-border: #fdba74;
        --background-color: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        --card-bg: #ffffff;
        --border-radius: 16px;
        --small-radius: 12px;
        --shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        --small-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        --total-bookings-bg: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        --total-bookings-text: #1e3a8a;
        --upcoming-bookings-bg: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%);
        --upcoming-bookings-text: #713f12;
        --total-revenue-bg: linear-gradient(135deg, #dcfce7 0%, #a7f3d0 100%);
        --total-revenue-text: #166534;
        --cancelled-bookings-bg: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        --cancelled-bookings-text: #991b1b;
        --subtitle-text: #4b5563;
        --booking-btn: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        --revenue-btn: linear-gradient(135deg, #10b981 0%, #065f46 100%);
        --customer-btn: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        --performance-btn: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
    }

    body {
        font-family: 'Inter', sans-serif;
    }
    .container-fluid {
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

    .card.report-card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin-bottom: 24px;
        background-color: var(--card-bg);
        transition: transform 0.3s ease;
        overflow: hidden;
    }

    .card.report-card:hover {
        transform: translateY(-4px);
    }

    .card-header.report-header {
        background: var(--primary-gradient);
        color: white;
        border-bottom: none;
        padding: 30px;
        border-radius: 0;
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        text-align: center;
        justify-content: center;
    }

    .card-header.report-header h5 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }

    .card-header.report-header i {
        margin-right: 15px;
        font-size: 2rem;
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

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .btn {
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: var(--primary-gradient);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #64748b;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: #374151;
    }

    .btn-booking {
        background: var(--booking-btn);
        color: white;
    }

    .btn-booking:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.25);
    }

    .btn-revenue {
        background: var(--revenue-btn);
        color: white;
    }

    .btn-revenue:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.25);
    }

    .btn-customer {
        background: var(--customer-btn);
        color: white;
    }

    .btn-customer:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(139, 92, 246, 0.25);
    }

    .btn-performance {
        background: var(--performance-btn);
        color: white;
    }

    .btn-performance:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.25);
    }

    .alert {
        border-radius: var(--small-radius);
        box-shadow: var(--small-shadow);
        padding: 16px 20px;
        font-size: 0.95rem;
        border: none;
        margin-bottom: 20px;
    }

    .alert-success {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        color: #166534;
        border-left: 4px solid #10b981;
    }

    .table-responsive {
        padding: 20px;
        background: white;
    }

    .report-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--small-shadow);
    }

    .report-table th {
        background: var(--primary-gradient);
        color: white;
        padding: 20px 15px;
        font-weight: 600;
        text-align: left;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .report-table th:first-child {
        border-top-left-radius: var(--border-radius);
    }

    .report-table th:last-child {
        border-top-right-radius: var(--border-radius);
    }

    .report-table td {
        border-bottom: 1px solid #f1f5f9;
        padding: 15px;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: var(--small-radius);
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bg-success {
        background: #dcfce7 !important;
        color: #166534 !important;
    }

    .bg-warning {
        background: #fef3c7 !important;
        color: #92400e !important;
    }

    .card-total-bookings {
        background: var(--total-bookings-bg);
    }

    .card-total-bookings .card-title {
        color: var(--subtitle-text);
    }

    .card-total-bookings h2 {
        color: var(--total-bookings-text);
    }

    .card-upcoming-bookings {
        background: var(--upcoming-bookings-bg);
    }

    .card-upcoming-bookings .card-title {
        color: var(--subtitle-text);
    }

    .card-upcoming-bookings h2 {
        color: var(--upcoming-bookings-text);
    }

    .card-total-revenue {
        background: var(--total-revenue-bg);
    }

    .card-total-revenue .card-title {
        color: var(--subtitle-text);
    }

    .card-total-revenue h2 {
        color: var(--total-revenue-text);
    }

    .card-cancelled-bookings {
        background: var(--cancelled-bookings-bg);
    }

    .card-cancelled-bookings .card-title {
        color: var(--subtitle-text);
    }

    .card-cancelled-bookings h2 {
        color: var(--cancelled-bookings-text);
    }

     @media print {
        body * {
            visibility: hidden;
        }
        .print-section, .print-section * {
            visibility: visible;
        }
        .print-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .report-header {
            background: #198754 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .report-table th {
            background: #198754 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .btn {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 10px;
        }

        .card-header.report-header {
            padding: 20px;
        }

        .card-header.report-header h5 {
            font-size: 1.5rem;
        }

        .report-table th,
        .report-table td {
            padding: 10px 8px;
            font-size: 0.85rem;
        }

        .chart-container {
            height: 300px;
        }
    }

    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
        margin-bottom: 20px;
    }
</style>
@endpush

<div>
    <div class="container-fluid main">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Reports</h1>
            </div>
             <div>
                <button class="btn btn-success me-2" wire:click="exportReport">
                    <i class="fas fa-download me-1"></i> Export Excel
                </button>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif



        <!-- Summary Stats and Chart -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card report-card h-100 card-total-bookings">
                    <div class="card-body">
                        <h6 class="card-title">Total Bookings</h6>
                        <h2 class="mb-0">{{ $totalBookings }}</h2>
                        <small class="text-muted">Filtered by selected criteria</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card report-card h-100 card-upcoming-bookings">
                    <div class="card-body">
                        <h6 class="card-title">Upcoming Bookings (Today)</h6>
                        <h2 class="mb-0">{{ $totalUpcomingBookings }}</h2>
                        <small class="text-muted">Filtered by selected criteria</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card report-card h-100 card-total-revenue">
                    <div class="card-body">
                        <h6 class="card-title">Total Revenue</h6>
                        <h2 class="mb-0"><span class="fs-6">LKR</span> {{ number_format($totalRevenue, 2) }}</h2>
                        <small class="text-muted">Filtered by selected criteria</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card report-card h-100 card-cancelled-bookings">
                    <div class="card-body">
                        <h6 class="card-title">Cancelled Bookings</h6>
                        <h2 class="mb-0">{{ $cancelledBookings }}</h2>
                        <small class="text-muted">Filtered by selected criteria</small>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <div class="card report-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <h6 class="card-title text-muted">Revenue Trend</h6>
                            <div class="card border-none">
                                <select class="form-select" id="period" wire:model="period" wire:change="generateReport">
                                    <option value="daily">Daily (Last 7 Days)</option>
                                    <option value="weekly">Weekly (Last 4 Weeks)</option>
                                    <option value="monthly">Monthly (Last 12 Months)</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Type Buttons -->
        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <button class="btn btn-booking w-100" type="button" data-bs-toggle="modal" data-bs-target="#bookingReportModal">
                    <i class="fas fa-book me-2"></i> Booking Reports
                </button>
            </div>
            <div class="col-md-3 mb-2">
                <button class="btn btn-revenue w-100" type="button" data-bs-toggle="modal" data-bs-target="#revenueReportModal">
                    <i class="fas fa-dollar-sign me-2"></i> Revenue Reports
                </button>
            </div>
            <div class="col-md-3 mb-2">
                <button class="btn btn-customer w-100" wire:click="">
                    <i class="fas fa-users me-2"></i> Customer Reports
                </button>
            </div>
            <div class="col-md-3 mb-2">
                <button class="btn btn-performance w-100" wire:click="">
                    <i class="fas fa-chart-bar me-2"></i> Performance & Utilization
                </button>
            </div>
        </div>

        <!-- Booking Report Modal -->
        <div class="modal fade" id="bookingReportModal" tabindex="-1" aria-labelledby="bookingReportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bookingReportModalLabel">Comprehensive Booking Details Report - Indoor Booking System</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Complex Details -->
                        <div class="text-center mb-4">
                            <img src="{{ $complexPhoto }}" alt="Complex Photo" class="img-fluid complex-img mb-2">
                            <h3>{{ $complexName }}</h3>
                            <p>{{ $complexAddress }}</p>
                        </div>
                        <!-- Report Content -->
                        <p><strong>Dates:</strong> {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}</p>
                        <hr>
                        <h4>BOOKING DETAILS</h4>
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Username</th>
                                    <th>Court</th>
                                    <th>Sport</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Duration (Hours)</th>
                                    <th>Status</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookingDetailModel as $booking)
                                    <tr>
                                        <td>{{ $booking->booking_id }}</td>
                                        <td>{{ $booking->user_name ?? 'N/A' }}</td>
                                        <td>{{ $booking->court_number ?? 'N/A' }}</td>
                                        <td>{{ $booking->sport->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                        </td>
                                        <td>{{ number_format(\Carbon\Carbon::parse($booking->end_time)->diffInHours(\Carbon\Carbon::parse($booking->start_time)), 2) }}</td>
                                        <td>
                                            <span class="badge {{ $booking->status === 'Booked' || $booking->status === 'Upcoming' ? 'bg-success' : ($booking->status === 'Pending' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $booking->status }}
                                            </span>
                                        </td>
                                        <td>LKR {{ number_format($booking->price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted">No bookings found for the selected period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-print" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print Report
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue Report Modal -->
        <div class="modal fade" id="revenueReportModal" tabindex="-1" aria-labelledby="revenueReportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="revenueReportModalLabel">Comprehensive Revenue Report - Indoor Booking System</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Complex Details -->
                        <div class="text-center mb-4">
                            <img src="{{ $complexPhoto }}" alt="Complex Photo" class="img-fluid complex-img mb-2">
                            <h3>{{ $complexName }}</h3>
                            <p>{{ $complexAddress }}</p>
                        </div>
                        <!-- Report Content -->
                        <p><strong>Dates:</strong> {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}</p>
                        <hr>
                        <h4>REVENUE SUMMARY</h4>
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sport</th>
                                    <th>Court</th>
                                    <th>Total Bookings</th>
                                    <th>Total Hours Booked</th>
                                    <th>Total Revenue (LKR)</th>
                                    <th>Average Revenue per Booking (LKR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueReportData as $revenue)
                                    <tr>
                                        <td>{{ $revenue->sport_name ?? 'N/A' }}</td>
                                        <td>{{ $revenue->court_number ?? 'N/A' }}</td>
                                        <td>{{ $revenue->total_bookings }}</td>
                                        <td>{{ number_format($revenue->total_hours, 2) }}</td>
                                        <td>LKR {{ number_format($revenue->total_revenue, 2) }}</td>
                                        <td>LKR {{ number_format($revenue->average_revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No revenue data found for the selected period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total:</th>
                                    <th>{{ collect($revenueReportData)->sum('total_bookings') }}</th>
                                    <th>{{ number_format(collect($revenueReportData)->sum('total_hours'), 2) }}</th>
                                    <th>LKR {{ number_format(collect($revenueReportData)->sum('total_revenue'), 2) }}</th>
                                    <th>
                                        LKR {{ number_format(
                                            collect($revenueReportData)->sum('total_bookings') > 0
                                            ? collect($revenueReportData)->sum('total_revenue') / collect($revenueReportData)->sum('total_bookings')
                                            : 0, 2) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-print" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print Report
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        let revenueChart = null;

        const initialLabels = @json($chartData['labels']);
        const initialRevenueData = @json($chartData['revenueData']);
        const initialBookingsData = @json($chartData['bookingsData']);
        
        // Define chart options
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Revenue (LKR)',
                        font: { size: 14, weight: '600' }
                    },
                    ticks: {
                        callback: function(value) {
                            return 'LKR ' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Number of Bookings',
                        font: { size: 14, weight: '600' }
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    min: 0,
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        font: { size: 14, weight: '600' }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: { size: 12, weight: '500' },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: '#195f0b',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label.includes('Revenue')) {
                                return `${label}: LKR ${context.parsed.y.toLocaleString()}`;
                            }
                            return `${label}: ${context.parsed.y}`;
                        }
                    }
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            }
        };

        // Function to create or update the chart
        function renderChart(labels, revenueData, bookingsData) {
            if (revenueChart) {
                revenueChart.destroy();
            }
            
            revenueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Revenue (LKR)',
                            data: revenueData,
                            type:'line',
                            backgroundColor: 'rgba(25, 95, 11, 0.7)',
                            borderColor: 'rgba(25, 95, 11, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Number of Bookings',
                            data: bookingsData,
                            type: 'line',
                            borderColor: 'rgba(226, 198, 38, 1)',
                            backgroundColor: 'rgba(226, 198, 38, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: 'rgba(226, 198, 38, 1)',
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: chartOptions
            });
        }

        // Initial chart render
        renderChart(initialLabels, initialRevenueData, initialBookingsData);

        // Update event listener
        window.addEventListener('update-chart', function (event) {
            const chartData = event.detail.data;
            renderChart(chartData.labels, chartData.revenueData, chartData.bookingsData);
        });
    });
</script>
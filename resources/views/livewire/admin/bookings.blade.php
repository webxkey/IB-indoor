
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
        <!-- Today's Bookings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title text-muted">Today's Bookings</h6>
                            <h2 class="mb-0">42</h2>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
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
                            <h2 class="mb-0">8</h2>
                            <small class="text-danger">
                                <i class="fas fa-arrow-up me-1"></i>
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
                            <h2 class="mb-0">15</h2>
                            <small class="text-warning">
                                <i class="fas fa-arrow-down me-1"></i>
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
                            <h2 class="mb-0">$3,850</h2>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
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
                    <select class="form-select" id="dateFilter">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="" selected>All Status</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="pending">Pending</option>
                        <option value="canceled">Canceled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="indoorFilter" class="form-label">Indoor Facility</label>
                    <select class="form-select" id="indoorFilter">
                        <option value="" selected>All Facilities</option>
                        <option value="premium-arena">Premium Arena</option>
                        <option value="sports-hub">Sports Hub</option>
                        <option value="elite-center">Elite Center</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Indoor-wise Bookings Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Indoor Facility Bookings & Revenue</h5>
            <p class="text-muted mb-0">Sorted by highest revenue</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Facility</th>
                            <th>Total Bookings</th>
                            <th>Confirmed</th>
                            <th>Pending</th>
                            <th>Canceled</th>
                            <th>Total Revenue</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Highest Revenue Facility -->
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://themotzgroup.com/wp-content/webpc-passthru.php?src=https://themotzgroup.com/wp-content/uploads/2017/07/grand-park-indoor.jpg&nocache=1" 
                                         class="rounded me-3" width="60" height="40" style="object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">Premium Arena</h6>
                                        <small class="text-muted">Downtown Location</small>
                                    </div>
                                </div>
                            </td>
                            <td>24</td>
                            <td><span class="badge bg-success">18</span></td>
                            <td><span class="badge bg-warning">4</span></td>
                            <td><span class="badge bg-danger">2</span></td>
                            <td class="text-success fw-bold">$1,450</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>

                        <!-- Second Highest Revenue Facility -->
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://static.vecteezy.com/system/resources/previews/046/079/327/non_2x/the-indoor-stadium-boasts-the-latest-turf-technology-providing-players-with-a-fast-and-responsive-playing-surface-photo.jpg" 
                                         class="rounded me-3" width="60" height="40" style="object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">Sports Hub Indoor</h6>
                                        <small class="text-muted">Westside Location</small>
                                    </div>
                                </div>
                            </td>
                            <td>18</td>
                            <td><span class="badge bg-success">15</span></td>
                            <td><span class="badge bg-warning">2</span></td>
                            <td><span class="badge bg-danger">1</span></td>
                            <td class="text-success fw-bold">$1,120</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>

                        <!-- Third Highest Revenue Facility -->
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://display.blogto.com/articles/rankedlistings/2023/08/08/20230808-lcisportscomplex.jpeg?w=720&cmd=resize_then_crop&height=480&quality=70&format=jpeg" 
                                         class="rounded me-3" width="60" height="40" style="object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">Elite Sports Center</h6>
                                        <small class="text-muted">Northside Location</small>
                                    </div>
                                </div>
                            </td>
                            <td>12</td>
                            <td><span class="badge bg-success">10</span></td>
                            <td><span class="badge bg-warning">1</span></td>
                            <td><span class="badge bg-danger">1</span></td>
                            <td class="text-success fw-bold">$850</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>

                        <!-- Fourth Highest Revenue Facility -->
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://thumbs.dreamstime.com/b/stunning-soccer-stadium-lights-illuminating-dark-night-sky-image-features-vibrant-american-football-illuminated-bright-350274276.jpg" 
                                         class="rounded me-3" width="60" height="40" style="object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">Community Indoor Center</h6>
                                        <small class="text-muted">Eastside Location</small>
                                    </div>
                                </div>
                            </td>
                            <td>8</td>
                            <td><span class="badge bg-success">6</span></td>
                            <td><span class="badge bg-warning">1</span></td>
                            <td><span class="badge bg-danger">1</span></td>
                            <td class="text-success fw-bold">$430</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
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
                            <th>Facility</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#BK-2023-001</td>
                            <td>Premium Arena</td>
                            <td>John Smith</td>
                            <td>Today, 15:00 - 17:00</td>
                            <td>$120</td>
                            <td><span class="badge bg-success">Confirmed</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#BK-2023-002</td>
                            <td>Sports Hub Indoor</td>
                            <td>Sarah Johnson</td>
                            <td>Today, 18:00 - 20:00</td>
                            <td>$95</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#BK-2023-003</td>
                            <td>Elite Sports Center</td>
                            <td>Michael Brown</td>
                            <td>Tomorrow, 10:00 - 12:00</td>
                            <td>$110</td>
                            <td><span class="badge bg-success">Confirmed</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#BK-2023-004</td>
                            <td>Community Indoor Center</td>
                            <td>Emily Davis</td>
                            <td>Tomorrow, 14:00 - 16:00</td>
                            <td>$85</td>
                            <td><span class="badge bg-danger">Canceled</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
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
    <!-- Add this modal at the bottom of your page -->
<div class="modal fade" id="viewFacilityModal" tabindex="-1" aria-labelledby="viewFacilityLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewFacilityLabel">Facility Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-5">
            <img id="facilityImage" src="" alt="Facility Image" class="img-fluid rounded">
          </div>
          <div class="col-md-7">
            <h5 id="facilityName">Facility Name</h5>
            <p id="facilityLocation" class="text-muted">Location</p>
            <ul class="list-group list-group-flush">
              <li class="list-group-item"><strong>Total Bookings:</strong> <span id="totalBookings"></span></li>
              <li class="list-group-item"><strong>Confirmed:</strong> <span id="confirmedBookings"></span></li>
              <li class="list-group-item"><strong>Pending:</strong> <span id="pendingBookings"></span></li>
              <li class="list-group-item"><strong>Canceled:</strong> <span id="canceledBookings"></span></li>
              <li class="list-group-item"><strong>Total Revenue:</strong> <span id="totalRevenue"></span></li>
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

<!-- Add this JS after Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Select all View buttons
  const viewButtons = document.querySelectorAll('.btn-outline-primary');

  viewButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Get the row of the clicked button
      const row = this.closest('tr');

      // Extract info from the row
      const imgSrc = row.querySelector('td img')?.src;
      const name = row.querySelector('td h6')?.innerText;
      const location = row.querySelector('td small')?.innerText;
      const total = row.cells[1].innerText;
      const confirmed = row.cells[2].innerText;
      const pending = row.cells[3].innerText;
      const canceled = row.cells[4].innerText;
      const revenue = row.cells[5].innerText;

      // Fill modal
      document.getElementById('facilityImage').src = imgSrc || '';
      document.getElementById('facilityName').innerText = name || '';
      document.getElementById('facilityLocation').innerText = location || '';
      document.getElementById('totalBookings').innerText = total;
      document.getElementById('confirmedBookings').innerText = confirmed;
      document.getElementById('pendingBookings').innerText = pending;
      document.getElementById('canceledBookings').innerText = canceled;
      document.getElementById('totalRevenue').innerText = revenue;

      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('viewFacilityModal'));
      modal.show();
    });
  });
</script>

</div>

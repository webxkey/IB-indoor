
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  


<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Sports Management</h1>
            <p class="text-muted">Manage all sports activities and their booking status</p>
        </div>
        <div>
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addSportModal">
                <i class="fas fa-plus me-1"></i>
                Add New Sport
            </button>
        </div>
    </div>

    <!-- Sports Cards Grid -->
    <div class="row">
        <!-- Football Card -->
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                         class="card-img-top" alt="Football" style="height: 160px; object-fit: cover;">
                    <span class="badge bg-success position-absolute top-0 end-0 m-2">Active</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Football</h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Booking Status</span>
                            <span class="fw-bold">78%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 78%" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Hourly Rate:</span>
                        <span class="fw-bold">$25</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Active Bookings:</span>
                        <span class="fw-bold">14/18</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#editSportModal" data-sport="football">
                        <i class="fas fa-edit me-1"></i> Manage
                    </button>
                </div>
            </div>
        </div>

        <!-- Basketball Card -->
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1546519638-68e109498ffc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                         class="card-img-top" alt="Basketball" style="height: 160px; object-fit: cover;">
                    <span class="badge bg-success position-absolute top-0 end-0 m-2">Active</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Basketball</h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Booking Status</span>
                            <span class="fw-bold">65%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Hourly Rate:</span>
                        <span class="fw-bold">$20</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Active Bookings:</span>
                        <span class="fw-bold">13/20</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#editSportModal" data-sport="basketball">
                        <i class="fas fa-edit me-1"></i> Manage
                    </button>
                </div>
            </div>
        </div>

        <!-- Badminton Card -->
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="https://cdn.shopify.com/s/files/1/0020/9407/1890/files/2_480x480.jpg?v=1559302854" 
                         class="card-img-top" alt="Badminton" style="height: 160px; object-fit: cover;">
                    <span class="badge bg-warning position-absolute top-0 end-0 m-2">Maintenance</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Badminton</h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Booking Status</span>
                            <span class="fw-bold">0%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Hourly Rate:</span>
                        <span class="fw-bold">$15</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Active Bookings:</span>
                        <span class="fw-bold">0/12</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#editSportModal" data-sport="badminton">
                        <i class="fas fa-edit me-1"></i> Manage
                    </button>
                </div>
            </div>
        </div>

        <!-- Tennis Card -->
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1554068865-24cecd4e34b8?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                         class="card-img-top" alt="Tennis" style="height: 160px; object-fit: cover;">
                    <span class="badge bg-success position-absolute top-0 end-0 m-2">Active</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Tennis</h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Booking Status</span>
                            <span class="fw-bold">92%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 92%" aria-valuenow="92" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Hourly Rate:</span>
                        <span class="fw-bold">$30</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Active Bookings:</span>
                        <span class="fw-bold">11/12</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#editSportModal" data-sport="tennis">
                        <i class="fas fa-edit me-1"></i> Manage
                    </button>
                </div>
            </div>
        </div>
    </div>


<!-- Add Sport Modal -->
<div class="modal fade" id="addSportModal" tabindex="-1" aria-labelledby="addSportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addSportModalLabel">Add New Sport</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSportForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sportName" class="form-label">Sport Name*</label>
                        <input type="text" class="form-control" id="sportName" required>
                    </div>
                    <div class="mb-3">
                        <label for="hourlyRate" class="form-label">Hourly Rate ($)*</label>
                        <input type="number" class="form-control" id="hourlyRate" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="maxSlots" class="form-label">Maximum Slots*</label>
                        <input type="number" class="form-control" id="maxSlots" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="sportStatus" class="form-label">Status*</label>
                        <select class="form-select" id="sportStatus" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="sportImage" class="form-label">Sport Image</label>
                        <input type="file" class="form-control" id="sportImage" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="sportDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="sportDescription" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Sport</button>
                </div>
            </form>
        </div>
    </div>
</div>


</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modals
    const addSportModal = new bootstrap.Modal(document.getElementById('addSportModal'));
    const editSportModal = new bootstrap.Modal(document.getElementById('editSportModal'));

    // Sample sport data
    const sportsData = {
        'football': {
            name: 'Football',
            hourlyRate: 25,
            maxSlots: 18,
            currentBookings: 14,
            status: 'active',
            image: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
            description: 'Standard 11-a-side football with professional turf'
        },
        'basketball': {
            name: 'Basketball',
            hourlyRate: 20,
            maxSlots: 20,
            currentBookings: 13,
            status: 'active',
            image: 'https://images.unsplash.com/photo-1546519638-68e109498ffc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
            description: 'Full court basketball with professional flooring'
        },
        'badminton': {
            name: 'Badminton',
            hourlyRate: 15,
            maxSlots: 12,
            currentBookings: 0,
            status: 'maintenance',
            image: 'https://images.unsplash.com/photo-1516043827470-d52af543ae2e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
            description: 'Olympic-standard badminton courts'
        },
        'tennis': {
            name: 'Tennis',
            hourlyRate: 30,
            maxSlots: 12,
            currentBookings: 11,
            status: 'active',
            image: 'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
            description: 'Professional tennis courts with synthetic grass'
        }
    };

    // Handle edit sport button clicks
    document.querySelectorAll('[data-bs-target="#editSportModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const sportKey = this.getAttribute('data-sport');
            const sport = sportsData[sportKey];
            
            // Calculate booking percentage
            const bookingPercentage = Math.round((sport.currentBookings / sport.maxSlots) * 100);
            
            // Update modal with sport data
            document.getElementById('editSportModalLabel').textContent = `Manage ${sport.name}`;
            document.getElementById('editSportImage').src = sport.image;
            document.getElementById('editSportName').value = sport.name;
            document.getElementById('editHourlyRate').value = sport.hourlyRate;
            document.getElementById('editMaxSlots').value = sport.maxSlots;
            document.getElementById('editCurrentBookings').value = sport.currentBookings;
            document.getElementById('editSportStatus').value = sport.status;
            document.getElementById('editSportDescription').value = sport.description;
            
            // Update progress bar
            document.getElementById('bookingPercentage').textContent = `${bookingPercentage}%`;
            const progressBar = document.getElementById('bookingProgressBar');
            progressBar.style.width = `${bookingPercentage}%`;
            
            // Change progress bar color based on percentage
            if (bookingPercentage >= 90) {
                progressBar.className = 'progress-bar bg-danger';
            } else if (bookingPercentage >= 75) {
                progressBar.className = 'progress-bar bg-warning';
            } else {
                progressBar.className = 'progress-bar bg-success';
            }
        });
    });

    // Handle add sport form submission
    document.getElementById('addSportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('New sport added successfully! (This is a frontend demo)');
        addSportModal.hide();
        this.reset();
    });

    // Handle edit sport form submission
    document.getElementById('editSportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Sport details updated successfully! (This is a frontend demo)');
        editSportModal.hide();
    });
});
</script>
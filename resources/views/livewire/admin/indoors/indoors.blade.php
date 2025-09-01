
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


    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Indoors</h1>
                <p class="text-muted">Manage, track, and optimize your indoor ground bookings with ease.</p>
            </div>
            <div>
              <!-- Button to trigger modal -->
<button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addIndoorModal">
    <i class="fas fa-plus me-1"></i>
    Add Indoor
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
                                <h6 class="card-title opacity-75">Active Indoors</h6>
                                <h2 class="mb-0">16</h2>
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
                                <h6 class="card-title text-muted">Deactive Indoors</h6>
                                <h2 class="mb-0">23</h2>
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
                                <h6 class="card-title text-muted">Deleted Indoors</h6>
                                <h2 class="mb-0">0</h2>
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
                                <h6 class="card-title text-muted">Pending Indoor Requests</h6>
                                <h2 class="mb-0">5</h2>
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

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="" selected>All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="new">New</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="locationFilter" class="form-label">Location</label>
                        <select class="form-select" id="locationFilter">
                            <option value="" selected>All Locations</option>
                            <option value="downtown">Downtown</option>
                            <option value="westside">Westside</option>
                            <option value="northside">Northside</option>
                            <option value="eastside">Eastside</option>
                            <option value="southside">Southside</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="capacityFilter" class="form-label">Capacity</label>
                        <select class="form-select" id="capacityFilter">
                            <option value="" selected>Any Capacity</option>
                            <option value="100">Up to 100</option>
                            <option value="200">Up to 200</option>
                            <option value="300">Up to 300</option>
                            <option value="400">400+</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary me-2" id="applyFilter">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        <button class="btn btn-outline-secondary" id="resetFilter">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and View Toggle -->
        <div class="d-flex justify-content-between mb-4">
            <div class="w-50">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Search indoor grounds by name, location or features...">
                    <button class="btn btn-primary">Search</button>
                </div>
            </div>
            <div>
                <div class="btn-group" role="group">
                    <button id="gridViewBtn" type="button" class="btn btn-outline-secondary active-view">
                        <i class="fas fa-th-large"></i> Grid
                    </button>
                    <button id="listViewBtn" type="button" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> List
                    </button>
                </div>
                <button class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-cog"></i> View Options
                </button>
            </div>
        </div>

        <!-- Grid View -->
        <div id="gridView" class="row">
            <!-- Card 1 -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="https://themotzgroup.com/wp-content/webpc-passthru.php?src=https://themotzgroup.com/wp-content/uploads/2017/07/grand-park-indoor.jpg&nocache=1" class="card-img-top" alt="Indoor Ground">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Active</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Premium Indoor Arena</h5>
                        <p class="card-text text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>Downtown Location<br>
                            <i class="fas fa-ruler-combined me-2"></i>Dimensions: 40m x 20m<br>
                            <i class="fas fa-users me-2"></i>Capacity: 200 people<br>
                            <i class="fas fa-star me-2"></i>Rating: 4.8/5
                        </p>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark"><i class="fas fa-futbol badge-icon"></i>Football</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-basketball-ball badge-icon"></i>Basketball</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-utensils badge-icon"></i>Cafeteria</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 24 Bookings
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="https://static.vecteezy.com/system/resources/previews/046/079/327/non_2x/the-indoor-stadium-boasts-the-latest-turf-technology-providing-players-with-a-fast-and-responsive-playing-surface-photo.jpg" class="card-img-top" alt="Indoor Ground">
                        <span class="badge bg-warning position-absolute top-0 end-0 m-2">Maintenance</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Sports Hub Indoor</h5>
                        <p class="card-text text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>Westside Location<br>
                            <i class="fas fa-ruler-combined me-2"></i>Dimensions: 50m x 25m<br>
                            <i class="fas fa-users me-2"></i>Capacity: 300 people<br>
                            <i class="fas fa-star me-2"></i>Rating: 4.5/5
                        </p>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark"><i class="fas fa-futbol badge-icon"></i>Football</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-table-tennis badge-icon"></i>Badminton</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-swimmer badge-icon"></i>Swimming</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 18 Bookings
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Activate">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="https://display.blogto.com/articles/rankedlistings/2023/08/08/20230808-lcisportscomplex.jpeg?w=720&cmd=resize_then_crop&height=480&quality=70&format=jpeg" class="card-img-top" alt="Indoor Ground">
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">Inactive</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Elite Sports Center</h5>
                        <p class="card-text text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>Northside Location<br>
                            <i class="fas fa-ruler-combined me-2"></i>Dimensions: 45m x 22m<br>
                            <i class="fas fa-users me-2"></i>Capacity: 250 people<br>
                            <i class="fas fa-star me-2"></i>Rating: 4.2/5
                        </p>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark"><i class="fas fa-basketball-ball badge-icon"></i>Basketball</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-volleyball-ball badge-icon"></i>Volleyball</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-dumbbell badge-icon"></i>Gym</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 0 Bookings
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Activate">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="https://thumbs.dreamstime.com/b/stunning-soccer-stadium-lights-illuminating-dark-night-sky-image-features-vibrant-american-football-illuminated-bright-350274276.jpg" class="card-img-top" alt="Indoor Ground">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Active</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Community Indoor Center</h5>
                        <p class="card-text text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>Eastside Location<br>
                            <i class="fas fa-ruler-combined me-2"></i>Dimensions: 35m x 18m<br>
                            <i class="fas fa-users me-2"></i>Capacity: 150 people<br>
                            <i class="fas fa-star me-2"></i>Rating: 4.0/5
                        </p>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark"><i class="fas fa-table-tennis badge-icon"></i>Badminton</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-running badge-icon"></i>Running Track</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-child badge-icon"></i>Kids Area</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 32 Bookings
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="https://display.blogto.com/articles/rankedlistings/2023/08/08/20230808-centraltechstadium.jpeg?w=720&cmd=resize_then_crop&height=480&quality=70&format=jpeg" class="card-img-top" alt="Indoor Ground">
                        <span class="badge bg-info position-absolute top-0 end-0 m-2">New</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Future Sports Arena</h5>
                        <p class="card-text text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>Southside Location<br>
                            <i class="fas fa-ruler-combined me-2"></i>Dimensions: 55m x 30m<br>
                            <i class="fas fa-users me-2"></i>Capacity: 400 people<br>
                            <i class="fas fa-star me-2"></i>Rating: 4.9/5
                        </p>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark"><i class="fas fa-futbol badge-icon"></i>Football</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-running badge-icon"></i>Athletics</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-utensils badge-icon"></i>Restaurant</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 5 Bookings
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="https://media-cdn.tripadvisor.com/media/photo-s/0a/bf/a9/4e/football-play-area.jpg" class="card-img-top" alt="Indoor Ground">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Active</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">University Sports Complex</h5>
                        <p class="card-text text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>Campus Location<br>
                            <i class="fas fa-ruler-combined me-2"></i>Dimensions: 60m x 30m<br>
                            <i class="fas fa-users me-2"></i>Capacity: 500 people<br>
                            <i class="fas fa-star me-2"></i>Rating: 4.7/5
                        </p>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark"><i class="fas fa-basketball-ball badge-icon"></i>Basketball</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-volleyball-ball badge-icon"></i>Volleyball</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-dumbbell badge-icon"></i>Gym</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 42 Bookings
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 7 -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="https://thumbs.dreamstime.com/b/soccer-ball-green-grass-indoor-playground-26044470.jpg" class="card-img-top" alt="Indoor Ground">
                        <span class="badge bg-warning position-absolute top-0 end-0 m-2">Renovation</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Toldo Sports Center</h5>
                        <p class="card-text text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>Riverside Location<br>
                            <i class="fas fa-ruler-combined me-2"></i>Dimensions: 48m x 24m<br>
                            <i class="fas fa-users me-2"></i>Capacity: 350 people<br>
                            <i class="fas fa-star me-2"></i>Rating: 4.3/5
                        </p>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark"><i class="fas fa-futbol badge-icon"></i>Football</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-swimmer badge-icon"></i>Swimming</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-running badge-icon"></i>Track</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 12 Bookings
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Activate">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 8 -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="https://thumbs.dreamstime.com/b/soccer-ball-green-grass-indoor-playground-26044470.jpg" class="card-img-top" alt="Indoor Ground">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Active</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">MCG Indoor Pavilion</h5>
                        <p class="card-text text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>Central Location<br>
                            <i class="fas fa-ruler-combined me-2"></i>Dimensions: 65m x 35m<br>
                            <i class="fas fa-users me-2"></i>Capacity: 600 people<br>
                            <i class="fas fa-star me-2"></i>Rating: 4.9/5
                        </p>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark"><i class="fas fa-cricket badge-icon"></i>Cricket</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-football-ball badge-icon"></i>AFL</span>
                            <span class="badge bg-light text-dark"><i class="fas fa-concierge-bell badge-icon"></i>VIP Lounge</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 56 Bookings
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- List View (Hidden by default) -->
        <div id="listView" class="d-none">
            <!-- List Item 1 -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="https://themotzgroup.com/wp-content/webpc-passthru.php?src=https://themotzgroup.com/wp-content/uploads/2017/07/grand-park-indoor.jpg&nocache=1" class="list-view-img rounded" alt="Indoor Ground">
                        </div>
                        <div class="col-md-7">
                            <h5>Premium Indoor Arena</h5>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge bg-success">Active</span>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 24 Bookings
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-star me-1"></i> 4.8/5
                                </span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>Downtown Location • 
                                <i class="fas fa-ruler-combined me-2"></i>40m x 20m • 
                                <i class="fas fa-users me-2"></i>200 people
                            </p>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-light text-dark"><i class="fas fa-futbol badge-icon"></i>Football</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-basketball-ball badge-icon"></i>Basketball</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-utensils badge-icon"></i>Cafeteria</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                                <i class="fas fa-ban"></i> Deactivate
                            </button>
                            <button class="btn btn-sm btn-outline-info mt-2 w-100" title="View Details">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Item 2 -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="https://static.vecteezy.com/system/resources/previews/046/079/327/non_2x/the-indoor-stadium-boasts-the-latest-turf-technology-providing-players-with-a-fast-and-responsive-playing-surface-photo.jpg" class="list-view-img rounded" alt="Indoor Ground">
                        </div>
                        <div class="col-md-7">
                            <h5>Sports Hub Indoor</h5>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge bg-warning">Maintenance</span>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 18 Bookings
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-star me-1"></i> 4.5/5
                                </span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>Westside Location • 
                                <i class="fas fa-ruler-combined me-2"></i>50m x 25m • 
                                <i class="fas fa-users me-2"></i>300 people
                            </p>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-light text-dark"><i class="fas fa-futbol badge-icon"></i>Football</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-table-tennis badge-icon"></i>Badminton</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-swimmer badge-icon"></i>Swimming</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-success" title="Activate">
                                <i class="fas fa-check"></i> Activate
                            </button>
                            <button class="btn btn-sm btn-outline-info mt-2 w-100" title="View Details">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Item 3 -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="https://display.blogto.com/articles/rankedlistings/2023/08/08/20230808-lcisportscomplex.jpeg?w=720&cmd=resize_then_crop&height=480&quality=70&format=jpeg" class="list-view-img rounded" alt="Indoor Ground">
                        </div>
                        <div class="col-md-7">
                            <h5>Elite Sports Center</h5>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge bg-danger">Inactive</span>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 0 Bookings
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-star me-1"></i> 4.2/5
                                </span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>Northside Location • 
                                <i class="fas fa-ruler-combined me-2"></i>45m x 22m • 
                                <i class="fas fa-users me-2"></i>250 people
                            </p>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-light text-dark"><i class="fas fa-basketball-ball badge-icon"></i>Basketball</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-volleyball-ball badge-icon"></i>Volleyball</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-dumbbell badge-icon"></i>Gym</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-success" title="Activate">
                                <i class="fas fa-check"></i> Activate
                            </button>
                            <button class="btn btn-sm btn-outline-info mt-2 w-100" title="View Details">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Item 4 -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="https://thumbs.dreamstime.com/b/stunning-soccer-stadium-lights-illuminating-dark-night-sky-image-features-vibrant-american-football-illuminated-bright-350274276.jpg" class="list-view-img rounded" alt="Indoor Ground">
                        </div>
                        <div class="col-md-7">
                            <h5>Community Indoor Center</h5>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge bg-success">Active</span>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 32 Bookings
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-star me-1"></i> 4.0/5
                                </span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>Eastside Location • 
                                <i class="fas fa-ruler-combined me-2"></i>35m x 18m • 
                                <i class="fas fa-users me-2"></i>150 people
                            </p>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-light text-dark"><i class="fas fa-table-tennis badge-icon"></i>Badminton</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-running badge-icon"></i>Running Track</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-child badge-icon"></i>Kids Area</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                                <i class="fas fa-ban"></i> Deactivate
                            </button>
                            <button class="btn btn-sm btn-outline-info mt-2 w-100" title="View Details">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Item 5 -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="https://display.blogto.com/articles/rankedlistings/2023/08/08/20230808-centraltechstadium.jpeg?w=720&cmd=resize_then_crop&height=480&quality=70&format=jpeg" class="list-view-img rounded" alt="Indoor Ground">
                        </div>
                        <div class="col-md-7">
                            <h5>Future Sports Arena</h5>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge bg-info">New</span>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 5 Bookings
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-star me-1"></i> 4.9/5
                                </span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>Southside Location • 
                                <i class="fas fa-ruler-combined me-2"></i>55m x 30m • 
                                <i class="fas fa-users me-2"></i>400 people
                            </p>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-light text-dark"><i class="fas fa-futbol badge-icon"></i>Football</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-running badge-icon"></i>Athletics</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-utensils badge-icon"></i>Restaurant</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                                <i class="fas fa-ban"></i> Deactivate
                            </button>
                            <button class="btn btn-sm btn-outline-info mt-2 w-100" title="View Details">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Item 6 -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="https://media-cdn.tripadvisor.com/media/photo-s/0a/bf/a9/4e/football-play-area.jpg" class="list-view-img rounded" alt="Indoor Ground">
                        </div>
                        <div class="col-md-7">
                            <h5>University Sports Complex</h5>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge bg-success">Active</span>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 42 Bookings
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-star me-1"></i> 4.7/5
                                </span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>Campus Location • 
                                <i class="fas fa-ruler-combined me-2"></i>60m x 30m • 
                                <i class="fas fa-users me-2"></i>500 people
                            </p>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-light text-dark"><i class="fas fa-basketball-ball badge-icon"></i>Basketball</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-volleyball-ball badge-icon"></i>Volleyball</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-dumbbell badge-icon"></i>Gym</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                                <i class="fas fa-ban"></i> Deactivate
                            </button>
                            <button class="btn btn-sm btn-outline-info mt-2 w-100" title="View Details">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Item 7 -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="https://thumbs.dreamstime.com/b/soccer-ball-green-grass-indoor-playground-26044470.jpg" class="list-view-img rounded" alt="Indoor Ground">
                        </div>
                        <div class="col-md-7">
                            <h5>Toldo Sports Center</h5>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge bg-warning">Renovation</span>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 12 Bookings
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-star me-1"></i> 4.3/5
                                </span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>Riverside Location • 
                                <i class="fas fa-ruler-combined me-2"></i>48m x 24m • 
                                <i class="fas fa-users me-2"></i>350 people
                            </p>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-light text-dark"><i class="fas fa-futbol badge-icon"></i>Football</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-swimmer badge-icon"></i>Swimming</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-running badge-icon"></i>Track</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-success" title="Activate">
                                <i class="fas fa-check"></i> Activate
                            </button>
                            <button class="btn btn-sm btn-outline-info mt-2 w-100" title="View Details">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Item 8 -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="https://media-cdn.tripadvisor.com/media/photo-s/0a/bf/a9/4e/football-play-area.jpg" class="list-view-img rounded" alt="Indoor Ground">
                        </div>
                        <div class="col-md-7">
                            <h5>MCG Indoor Pavilion</h5>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge bg-success">Active</span>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> 56 Bookings
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-star me-1"></i> 4.9/5
                                </span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>Central Location • 
                                <i class="fas fa-ruler-combined me-2"></i>65m x 35m • 
                                <i class="fas fa-users me-2"></i>600 people
                            </p>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-light text-dark"><i class="fas fa-cricket badge-icon"></i>Cricket</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-football-ball badge-icon"></i>AFL</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-concierge-bell badge-icon"></i>VIP Lounge</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Deactivate">
                                <i class="fas fa-ban"></i> Deactivate
                            </button>
                            <button class="btn btn-sm btn-outline-info mt-2 w-100" title="View Details">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
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
        <div class="modal fade" id="addIndoorModal" tabindex="-1" aria-labelledby="addIndoorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIndoorModalLabel">Add New Indoor Facility</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addIndoorForm" method="POST" action="" enctype="multipart/form-data">
                
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Facility Name*</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="location" class="form-label">Location*</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Full Address*</label>
                                <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City*</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact & Status -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number*</label>
                                <input type="tel" class="form-control" id="contact_number" name="contact_number" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status*</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="maintenance">Under Maintenance</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="opening_time" class="form-label">Opening Hours*</label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="time" class="form-control" id="opening_time" name="opening_time" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="time" class="form-control" id="closing_time" name="closing_time" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Facility Details -->
                        <div class="col-12">
                            <hr class="my-3">
                            <h6 class="mb-3">Facility Details</h6>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sport_type" class="form-label">Primary Sport Type*</label>
                                <select class="form-select" id="sport_type" name="sport_type" required>
                                    <option value="">Select sport</option>
                                    <option value="football">Football</option>
                                    <option value="basketball">Basketball</option>
                                    <option value="badminton">Badminton</option>
                                    <option value="tennis">Tennis</option>
                                    <option value="volleyball">Volleyball</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Capacity*</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate ($)*</label>
                                <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" min="0" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dimensions" class="form-label">Dimensions (L x W in meters)*</label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" id="length" name="length" placeholder="Length" step="0.01" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" id="width" name="width" placeholder="Width" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amenities" class="form-label">Amenities</label>
                                <select class="form-select" id="amenities" name="amenities[]" multiple>
                                    <option value="parking">Parking</option>
                                    <option value="locker_rooms">Locker Rooms</option>
                                    <option value="showers">Showers</option>
                                    <option value="cafeteria">Cafeteria</option>
                                    <option value="wifi">WiFi</option>
                                    <option value="equipment_rental">Equipment Rental</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Images -->
                        <div class="col-12">
                            <hr class="my-3">
                            <h6 class="mb-3">Images</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cover_image" class="form-label">Cover Image*</label>
                                <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*" required>
                                <small class="text-muted">Main display image for the facility</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gallery_images" class="form-label">Gallery Images</label>
                                <input type="file" class="form-control" id="gallery_images" name="gallery_images[]" multiple accept="image/*">
                                <small class="text-muted">Upload multiple images (max 5)</small>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description*</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                        </div>
                        
                        <!-- Terms -->
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I confirm that all information provided is accurate
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Save Facility
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    </div>
    <!-- Add this modal HTML right before the closing </body> tag -->


<!-- JavaScript to handle the modal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the modal
        const addIndoorModal = new bootstrap.Modal(document.getElementById('addIndoorModal'));
        
        // Show modal when Add Indoor button is clicked
        document.querySelector('.btn-success[data-target="#addIndoorModal"]').addEventListener('click', function() {
            addIndoorModal.show();
        });
        
        // Form submission handling
        document.getElementById('addIndoorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would typically handle the form submission via AJAX
            // For now, we'll just log and close the modal
            console.log('Form submitted', new FormData(this));
            addIndoorModal.hide();
            
            // In a real implementation, you would:
            // 1. Validate the form
            // 2. Submit via AJAX
            // 3. Show success/error message
            // 4. Refresh the data table if successful
        });
    });
</script>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');

            // Grid View Button Click
            gridViewBtn.addEventListener('click', function() {
                gridView.classList.remove('d-none');
                listView.classList.add('d-none');
                gridViewBtn.classList.add('active-view');
                listViewBtn.classList.remove('active-view');
            });

            // List View Button Click
            listViewBtn.addEventListener('click', function() {
                gridView.classList.add('d-none');
                listView.classList.remove('d-none');
                gridViewBtn.classList.remove('active-view');
                listViewBtn.classList.add('active-view');
            });

            // Filter functionality would go here
            document.getElementById('applyFilter').addEventListener('click', function() {
                alert('Filter functionality would be implemented here');
            });

            document.getElementById('resetFilter').addEventListener('click', function() {
                document.getElementById('statusFilter').value = '';
                document.getElementById('locationFilter').value = '';
                document.getElementById('capacityFilter').value = '';
            });
        });
    </script>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Indoors</h1>
            <p class="text-muted">Manage, track, and optimize your indoor ground bookings with ease.</p>
        </div>
        <div>
            <button class="btn btn-primary me-2" wire:click="showAddModal">
                Add Indoor
            </button>
            <button class="btn btn-outline-secondary me-2 d-none d-md-inline" wire:click="exportData">
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
                            <h2 class="mb-0">{{ $stats['active'] }}</h2>
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
                            <h2 class="mb-0">{{ $stats['inactive'] }}</h2>
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
                            <h2 class="mb-0">{{ $stats['new'] }}</h2>
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
                    <select class="form-select" id="statusFilter" wire:model="statusFilter">
                        <option value="" selected>All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="new">New</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="locationFilter" class="form-label">Location</label>
                    <select class="form-select" id="locationFilter" wire:model="locationFilter">
                        <option value="" selected>All Locations</option>
                        @foreach($availableCounties as $county)
                        <option value="{{ $county }}">{{ $county }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="capacityFilter" class="form-label">Capacity</label>
                    <select class="form-select" id="capacityFilter" wire:model="capacityFilter">
                        <option value="" selected>Any Capacity</option>
                        <option value="100">Up to 100</option>
                        <option value="200">Up to 200</option>
                        <option value="300">Up to 300</option>
                        <option value="400">400+</option>
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

    <!-- Search and View Toggle -->
    <div class="d-flex justify-content-between mb-4">
        <div class="w-50">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control"
                    placeholder="Search indoor grounds by name, location or features..."
                    wire:model.debounce.500ms="search">
                <button class="btn btn-primary">Search</button>
            </div>
        </div>
        <div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary {{ $viewMode == 'grid' ? 'active-view' : '' }}"
                    wire:click="toggleViewMode('grid')">
                    <i class="fas fa-th-large"></i> Grid
                </button>
                <button type="button" class="btn btn-outline-secondary {{ $viewMode == 'list' ? 'active-view' : '' }}"
                    wire:click="toggleViewMode('list')">
                    <i class="fas fa-list"></i> List
                </button>
            </div>
            <button class="btn btn-outline-secondary ms-2">
                <i class="fas fa-cog"></i> View Options
            </button>
        </div>
    </div>

    <!-- Grid View -->
    @if($viewMode == 'grid')
    <div class="row">
        @foreach($venues as $venue)
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card h-100 card-clickable" wire:click="showViewModal({{ $venue->id }})">
                <div class="position-relative">
                    <img src="{{ $venue->cover_image ? Storage::url($venue->cover_image) : 'https://via.placeholder.com/300x180?text=No+Image' }}"
                        class="card-img-top" alt="Indoor Ground">
                    <span
                        class="badge bg-{{ $venue->status == 'active' ? 'success' : ($venue->status == 'maintenance' ? 'warning' : 'danger') }} position-absolute top-0 end-0 m-2">
                        {{ ucfirst($venue->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $venue->name }}</h5>
                    <p class="card-text text-muted">
                        <i class="fas fa-map-marker-alt me-2"></i>{{ $venue->county }}<br>
                        <i class="fas fa-ruler-combined me-2"></i>Dimensions: {{ $venue->dimensions }}<br>
                        <i class="fas fa-users me-2"></i>Capacity: {{ $venue->capacity }} people<br>
                        <i class="fas fa-star me-2"></i>Rating: {{ number_format($venue->reviews_avg_rating, 1) }}/5
                    </p>
                    <div class="d-flex flex-wrap gap-1">
                        @if($venue->amenities)
                        @foreach(array_slice($venue->amenities, 0, 3) as $amenity)
                        <span class="badge bg-light text-dark">
                            <i
                                class="fas fa-{{ $amenity == 'parking' ? 'parking' : ($amenity == 'locker_rooms' ? 'lock' : ($amenity == 'cafeteria' ? 'utensils' : 'wifi')) }} badge-icon"></i>
                            {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                        </span>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary">
                                <i class="fas fa-calendar me-1"></i> {{ $venue->bookings_count }} Bookings
                            </span>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-1" title="Edit"
                                wire:click="showEditModal({{ $venue->id }})" onclick="event.stopPropagation()">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if($venue->status == 'active')
                            <button class="btn btn-sm btn-outline-danger" title="Deactivate"
                                wire:click="updateStatus({{ $venue->id }}, 'inactive')"
                                onclick="event.stopPropagation()">
                                <i class="fas fa-ban"></i>
                            </button>
                            @else
                            <button class="btn btn-sm btn-outline-success" title="Activate"
                                wire:click="updateStatus({{ $venue->id }}, 'active')" onclick="event.stopPropagation()">
                                <i class="fas fa-check"></i>
                            </button>

                            @endif
                            {{-- In indoors.blade.php, add delete button to grid view (inside the card footer div, after
                            the activate/deactivate button) --}}

                            <button class="btn btn-sm btn-outline-danger" title="Delete"
                                wire:click="deleteVenue({{ $venue->id }})"
                                wire:confirm="Are you sure you want to delete this venue?"
                                onclick="event.stopPropagation()">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- List View -->
    <div>
        @foreach($venues as $venue)
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <img src="{{ $venue->cover_image ? Storage::url($venue->cover_image) : 'https://via.placeholder.com/300x180?text=No+Image' }}"
                            class="list-view-img rounded" alt="Indoor Ground">
                    </div>
                    <div class="col-md-7">
                        <h5>{{ $venue->name }}</h5>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span
                                class="badge bg-{{ $venue->status == 'active' ? 'success' : ($venue->status == 'maintenance' ? 'warning' : 'danger') }}">
                                {{ ucfirst($venue->status) }}
                            </span>
                            <span class="badge bg-primary">
                                <i class="fas fa-calendar me-1"></i> {{ $venue->bookings_count }} Bookings
                            </span>
                            <span class="badge bg-secondary">
                                <i class="fas fa-star me-1"></i> {{ number_format($venue->reviews_avg_rating, 1) }}/5
                            </span>
                        </div>
                        <p class="text-muted mb-1">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $venue->county }} •
                            <i class="fas fa-ruler-combined me-2"></i>{{ $venue->dimensions }} •
                            <i class="fas fa-users me-2"></i>{{ $venue->capacity }} people
                        </p>
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            @if($venue->amenities)
                            @foreach(array_slice($venue->amenities, 0, 3) as $amenity)
                            <span class="badge bg-light text-dark">
                                <i
                                    class="fas fa-{{ $amenity == 'parking' ? 'parking' : ($amenity == 'locker_rooms' ? 'lock' : ($amenity == 'cafeteria' ? 'utensils' : 'wifi')) }} badge-icon"></i>
                                {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                            </span>
                            @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-sm btn-outline-primary me-1" title="Edit"
                            wire:click="showEditModal({{ $venue->id }})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        {{-- In indoors.blade.php, add delete button to list view (inside the col-md-3 text-end div,
                        after the activate/deactivate button and before the view details button) --}}

                        <button class="btn btn-sm btn-outline-danger me-1" title="Delete"
                            wire:click="deleteVenue({{ $venue->id }})"
                            wire:confirm="Are you sure you want to delete this venue?">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        @if($venue->status == 'active')
                        <button class="btn btn-sm btn-outline-danger" title="Deactivate"
                            wire:click="updateStatus({{ $venue->id }}, 'inactive')">
                            <i class="fas fa-ban"></i> Deactivate
                        </button>
                        @else
                        <button class="btn btn-sm btn-outline-success" title="Activate"
                            wire:click="updateStatus({{ $venue->id }}, 'active')">
                            <i class="fas fa-check"></i> Activate
                        </button>
                        @endif
                        <button class="btn btn-sm btn-outline-info mt-2 w-100" title="View Details"
                            wire:click="showViewModal({{ $venue->id }})">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $venues->links() }}
    </div>

    <!-- Add Indoor Modal -->
    <div class="modal fade" id="addIndoorModal" tabindex="-1" aria-labelledby="addIndoorModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addIndoorModalLabel">Add New Indoor Facility</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="closeModals"></button>
                </div>
                <form wire:submit="saveVenue">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Facility Name*</label>
                                    <input type="text" class="form-control" id="name" wire:model="name" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location*</label>
                                    <input type="text" class="form-control" id="location" wire:model="location"
                                        required>
                                    @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Full Address*</label>
                                    <textarea class="form-control" id="address" wire:model="address" rows="2"
                                        required></textarea>
                                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="county" class="form-label">County*</label>
                                        <select class="form-select" id="county" wire:model="county" required>
                                            <option value="">Select County</option>
                                            @foreach($availableCounties as $countyOption)
                                            <option value="{{ $countyOption }}">{{ $countyOption }}</option>
                                            @endforeach
                                        </select>
                                        @error('county') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="postal_code" class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" id="postal_code"
                                            wire:model="postal_code">
                                        @error('postal_code') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Contact & Status -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_number" class="form-label">Contact Number*</label>
                                    <input type="tel" class="form-control" id="contact_number"
                                        wire:model="contact_number" required>
                                    @error('contact_number') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email_address" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email_address"
                                        wire:model="email_address">
                                    @error('email_address') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="website" wire:model="website">
                                    @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status*</label>
                                    <select class="form-select" id="status" wire:model="status" required>
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="maintenance">Under Maintenance</option>
                                        <option value="new">New</option>
                                    </select>
                                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="opening_time" class="form-label">Opening Hours*</label>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <input type="time" class="form-control" id="opening_time"
                                                wire:model="opening_hours.monday.open" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="time" class="form-control" id="closing_time"
                                                wire:model="opening_hours.monday.close" required>
                                        </div>
                                    </div>
                                    @error('opening_hours') <span class="text-danger">{{ $message }}</span> @enderror
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
                                    <select class="form-select" id="sport_type" wire:model="sport_type" required>
                                        <option value="">Select sport</option>
                                        @foreach($availableSportTypes as $sport)
                                        <option value="{{ $sport }}">{{ ucfirst($sport) }}</option>
                                        @endforeach
                                    </select>
                                    @error('sport_type') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Capacity*</label>
                                    <input type="number" class="form-control" id="capacity" wire:model="capacity"
                                        min="1" required>
                                    @error('capacity') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="hourly_rate" class="form-label">Hourly Rate ($)*</label>
                                    <input type="number" step="0.01" class="form-control" id="hourly_rate"
                                        wire:model="hourly_rate" min="0" required>
                                    @error('hourly_rate') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dimensions" class="form-label">Dimensions (L x W in meters)*</label>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <input type="number" class="form-control" id="length" wire:model="length"
                                                placeholder="Length" step="0.01" required>
                                            @error('length') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control" id="width" wire:model="width"
                                                placeholder="Width" step="0.01" required>
                                            @error('width') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amenities" class="form-label">Amenities</label>
                                    <select class="form-select" id="amenities" wire:model="amenities" multiple>
                                        @foreach($availableAmenities as $amenity)
                                        <option value="{{ $amenity }}">{{ ucfirst(str_replace('_', ' ', $amenity)) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('amenities') <span class="text-danger">{{ $message }}</span> @enderror
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
                                    <input type="file" class="form-control" id="cover_image" wire:model="cover_image"
                                        accept="image/*">
                                    <small class="text-muted">Main display image for the facility</small>
                                    @error('cover_image') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gallery_images" class="form-label">Gallery Images</label>
                                    <input type="file" class="form-control" id="gallery_images"
                                        wire:model="gallery_images" multiple accept="image/*">
                                    <small class="text-muted">Upload multiple images (max 5)</small>
                                    @error('gallery_images') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description*</label>
                                    <textarea class="form-control" id="description" wire:model="description" rows="3"
                                        required></textarea>
                                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Terms -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" wire:model="terms"
                                        required>
                                    <label class="form-check-label" for="terms">
                                        I confirm that all information provided is accurate
                                    </label>
                                    @error('terms') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:click="closeModals">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Save Facility
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Indoor Modal -->
    <div class="modal fade" id="viewIndoorModal" tabindex="-1" aria-labelledby="viewIndoorModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewIndoorModalLabel">Indoor Facility Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="closeModals"></button>
                </div>
                <div class="modal-body">
                    @if($viewVenue)
                    <div class="row">
                        <div class="col-md-6">
                            <img src="{{ $viewVenue->cover_image ? Storage::url($viewVenue->cover_image) : 'https://via.placeholder.com/500x300?text=No+Image' }}"
                                class="img-fluid rounded mb-3" alt="Facility Image">
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-3">{{ $viewVenue->name }}</h4>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span
                                    class="badge bg-{{ $viewVenue->status == 'active' ? 'success' : ($viewVenue->status == 'maintenance' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($viewVenue->status) }}
                                </span>
                                <span class="badge bg-primary">
                                    <i class="fas fa-calendar me-1"></i> {{ $viewVenue->bookings_count }} Bookings
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-star me-1"></i> {{ number_format($viewVenue->reviews_avg_rating, 1)
                                    }}/5
                                </span>
                            </div>
                            <div class="mb-3">
                                <h6>Location</h6>
                                <p class="text-muted">
                                    <i class="fas fa-map-marker-alt me-2"></i>{{ $viewVenue->county }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <h6>Dimensions</h6>
                                <p class="text-muted">
                                    <i class="fas fa-ruler-combined me-2"></i>{{ $viewVenue->dimensions }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <h6>Capacity</h6>
                                <p class="text-muted">
                                    <i class="fas fa-users me-2"></i>{{ $viewVenue->capacity }} people
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Features</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @if($viewVenue->amenities)
                                @foreach($viewVenue->amenities as $amenity)
                                <span class="badge bg-light text-dark">
                                    <i
                                        class="fas fa-{{ $amenity == 'parking' ? 'parking' : ($amenity == 'locker_rooms' ? 'lock' : ($amenity == 'cafeteria' ? 'utensils' : 'wifi')) }} badge-icon"></i>
                                    {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                                </span>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Description</h6>
                            <p class="text-muted">
                                {{ $viewVenue->description }}
                            </p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Opening Hours</h6>
                            <p class="text-muted">
                                @if($viewVenue->opening_hours)
                                @foreach($viewVenue->opening_hours as $day => $hours)
                                <i class="fas fa-clock me-2"></i> {{ ucfirst($day) }}: {{ $hours['open'] }} - {{
                                $hours['close'] }}<br>
                                @endforeach
                                @else
                                <i class="fas fa-clock me-2"></i> Not specified
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Contact Information</h6>
                            <p class="text-muted">
                                <i class="fas fa-phone me-2"></i> {{ $viewVenue->contact_number }}<br>
                                @if($viewVenue->email_address)
                                <i class="fas fa-envelope me-2"></i> {{ $viewVenue->email_address }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        wire:click="closeModals">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="showEditModal({{ $viewVenue->id ?? 0 }})">
                        <i class="fas fa-edit me-1"></i> Edit Facility
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Indoor Modal -->

    <div class="modal fade" id="editIndoorModal" tabindex="-1" aria-labelledby="editIndoorModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editIndoorModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Indoor Facility
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeModals"></button>
                </div>
                <form wire:submit="saveVenue">
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row g-3">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="section-divider">Basic Information</h6>
                                
                                <div class="mb-3">
                                    <label for="editName" class="form-label required-field">Facility Name</label>
                                    <input type="text" class="form-control" id="editName" wire:model="name" value="{{ $venue->name }}" required>
                                    @error('name') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="editLocation" class="form-label required-field">Location</label>
                                    <input type="text" class="form-control" id="editLocation" wire:model="location" required>
                                    @error('location') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="editAddress" class="form-label required-field">Full Address</label>
                                    <textarea class="form-control" id="editAddress" wire:model="address" rows="2" required></textarea>
                                    @error('address') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="editCounty" class="form-label required-field">County</label>
                                        <select class="form-select" id="editCounty" wire:model="county" required>
                                            <option value="">Select County</option>
                                            @foreach($availableCounties as $countyOption)
                                            <option value="{{ $countyOption }}">{{ $countyOption }}</option>
                                            @endforeach
                                        </select>
                                        @error('county') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="editPostalCode" class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" id="editPostalCode" wire:model="postal_code">
                                        @error('postal_code') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="editWebsite" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="editWebsite" wire:model="website">
                                    @error('website') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Contact & Status -->
                            <div class="col-md-6">
                                <h6 class="section-divider">Contact & Status</h6>
                                
                                <div class="mb-3">
                                    <label for="editContactNumber" class="form-label required-field">Contact Number</label>
                                    <input type="tel" class="form-control" id="editContactNumber" wire:model="contact_number" required>
                                    @error('contact_number') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="editEmail" wire:model="email_address">
                                    @error('email_address') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="editStatus" class="form-label required-field">Status</label>
                                    <select class="form-select" id="editStatus" wire:model="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="maintenance">Under Maintenance</option>
                                        <option value="new">New</option>
                                    </select>
                                    @error('status') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>

                                <!-- Improved Opening Hours -->
                                <div class="mb-3">
                                    <label class="form-label required-field">Opening Hours</label>
                                    
                                    <span class="hours-toggle" wire:click="setAllHours">
                                        <i class="fas fa-copy me-1"></i> Set same hours for all days
                                    </span>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered opening-hours-table">
                                            <thead>
                                                <tr>
                                                    <th>Day</th>
                                                    <th>Opening Time</th>
                                                    <th>Closing Time</th>
                                                    <th class="text-center">Closed</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                                <tr>
                                                    <td class="text-capitalize">{{ $day }}</td>
                                                    <td>
                                                        <input type="time" class="form-control form-control-sm"
                                                            wire:model="opening_hours.{{ $day }}.open" required>
                                                    </td>
                                                    <td>
                                                        <input type="time" class="form-control form-control-sm"
                                                            wire:model="opening_hours.{{ $day }}.close" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="checkbox"
                                                            wire:model="opening_hours.{{ $day }}.closed">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @error('opening_hours') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Facility Details -->
                            <div class="col-12">
                                <h6 class="section-divider">Facility Details</h6>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editSportType" class="form-label required-field">Primary Sport Type</label>
                                    <select class="form-select" id="editSportType" wire:model="sport_type" required>
                                        <option value="">Select sport</option>
                                        @foreach($availableSportTypes as $sport)
                                        <option value="{{ $sport }}">{{ ucfirst($sport) }}</option>
                                        @endforeach
                                    </select>
                                    @error('sport_type') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editCapacity" class="form-label required-field">Capacity</label>
                                    <input type="number" class="form-control" id="editCapacity" wire:model="capacity" min="1" required>
                                    @error('capacity') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editHourlyRate" class="form-label required-field">Hourly Rate ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="editHourlyRate" wire:model="hourly_rate" min="0" required>
                                    @error('hourly_rate') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editDimensions" class="form-label required-field">Dimensions (L x W in meters)</label>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <input type="number" class="form-control" id="editLength" wire:model="length" placeholder="Length" step="0.01" required>
                                            @error('length') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control" id="editWidth" wire:model="width" placeholder="Width" step="0.01" required>
                                            @error('width') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAmenities" class="form-label">Amenities</label>
                                    <select class="form-select" id="editAmenities" wire:model="amenities" multiple size="4">
                                        @foreach($availableAmenities as $amenity)
                                        <option value="{{ $amenity }}">{{ ucfirst(str_replace('_', ' ', $amenity)) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl/Cmd to select multiple amenities</small>
                                    @error('amenities') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="col-12">
                                <h6 class="section-divider">Images</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editCoverImage" class="form-label">Cover Image</label>
                                    <input type="file" class="form-control" id="editCoverImage" wire:model="cover_image" accept="image/*">
                                    <small class="text-muted">Main display image for the facility (upload to replace)</small>
                                    @error('cover_image') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                    
                                    <div class="mt-2">
                                        @if($editingVenue && $editingVenue->cover_image)
                                        <img src="{{ Storage::url($editingVenue->cover_image) }}" alt="Current Cover" class="image-preview">
                                        @endif
                                        <div wire:loading wire:target="cover_image" class="text-primary mt-2">
                                            <i class="fas fa-spinner fa-spin"></i> Uploading...
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editGalleryImages" class="form-label">Gallery Images</label>
                                    <input type="file" class="form-control" id="editGalleryImages" wire:model="gallery_images" multiple accept="image/*">
                                    <small class="text-muted">Upload multiple images (max 5; upload to add/replace)</small>
                                    @error('gallery_images') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                    
                                    <div class="d-flex flex-wrap mt-2">
                                        @if($editingVenue && $editingVenue->gallery->count() > 0)
                                        @foreach($editingVenue->gallery as $image)
                                        <img src="{{ Storage::url($image->image_url) }}" alt="Gallery Image" class="image-preview">
                                        @endforeach
                                        @endif
                                        <div wire:loading wire:target="gallery_images" class="text-primary mt-2">
                                            <i class="fas fa-spinner fa-spin"></i> Uploading...
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="editDescription" class="form-label required-field">Description</label>
                                    <textarea class="form-control" id="editDescription" wire:model="description" rows="3" required></textarea>
                                    @error('description') <span class="text-danger error-message">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeModals">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Update Facility
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
        const addModal = new bootstrap.Modal(document.getElementById('addIndoorModal'));
        const viewModal = new bootstrap.Modal(document.getElementById('viewIndoorModal'));
        const editModal = new bootstrap.Modal(document.getElementById('editIndoorModal'));

        window.addEventListener('showAddModal', () => {
            addModal.show();
        });
        
        window.addEventListener('showViewModal', () => {
            viewModal.show();
        });
        
        window.addEventListener('showEditModal', () => {
            // Hide view modal if open, then show edit modal
            if (viewModal._isShown) {
                viewModal.hide();
            }
            editModal.show();
        });
        
        window.addEventListener('closeModals', () => {
            if (addModal._isShown) addModal.hide();
            if (viewModal._isShown) viewModal.hide();
            if (editModal._isShown) editModal.hide();
        });
    });
    </script>
    @endpush
</div>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Indoor Facility</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        }
        
        .modal-header {
            background-color: #3b7ddd;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 15px 20px;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        .section-divider {
            border-bottom: 1px dashed #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #3b7ddd;
            font-weight: 600;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
            color: #495057;
        }
        
        .required-field::after {
            content: "*";
            color: #dc3545;
            margin-left: 3px;
        }
        
        .image-preview {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }
        
        .opening-hours-table {
            font-size: 0.9rem;
        }
        
        .opening-hours-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .error-message {
            font-size: 0.85rem;
        }
        
        .hours-toggle {
            cursor: pointer;
            color: #3b7ddd;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: inline-block;
        }
        
        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 15px 20px;
        }
        
        /* Loading indicator style */
        .loading-indicator {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Edit Indoor Facility Modal -->
    <div class="modal fade" id="editIndoorModal" tabindex="-1" aria-labelledby="editIndoorModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editIndoorModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Indoor Facility: <span id="facilityName">{{ $name }}</span>
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
                                    <input type="text" class="form-control" id="editName" wire:model="name" required>
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
                                            <option value="{{ $countyOption }}" {{ $county == $countyOption ? 'selected' : '' }}>{{ $countyOption }}</option>
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
                                        <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="maintenance" {{ $status == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                        <option value="new" {{ $status == 'new' ? 'selected' : '' }}>New</option>
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
                                        <option value="{{ $sport }}" {{ $sport_type == $sport ? 'selected' : '' }}>{{ ucfirst($sport) }}</option>
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
                                        <option value="{{ $amenity }}" {{ in_array($amenity, $amenities ?? []) ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                                        </option>
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
                                            <div class="loading-indicator"></div> Uploading...
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
                                            <div class="loading-indicator"></div> Uploading...
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Listen for Livewire event to show the modal
        document.addEventListener('DOMContentLoaded', function() {
            // Set same hours for all days functionality
            const setAllHours = function() {
                const openTime = prompt("Enter opening time (HH:MM):", "09:00");
                const closeTime = prompt("Enter closing time (HH:MM):", "17:00");
                
                if (openTime && closeTime) {
                    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    
                    days.forEach(day => {
                        const openInput = document.querySelector(`input[wire\\:model="opening_hours.${day}.open"]`);
                        const closeInput = document.querySelector(`input[wire\\:model="opening_hours.${day}.close"]`);
                        
                        if (openInput && closeInput) {
                            openInput.value = openTime;
                            closeInput.value = closeTime;
                            
                            // Dispatch input events to update Livewire model
                            openInput.dispatchEvent(new Event('input', { bubbles: true }));
                            closeInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                    
                    // Show success message
                    alert('Hours applied to all days successfully!');
                }
            };
            
            // Add event listener to the setAllHours button
            const hoursToggle = document.querySelector('.hours-toggle');
            if (hoursToggle) {
                hoursToggle.addEventListener('click', setAllHours);
            }
            
            // Image preview for cover image
            const coverImageInput = document.getElementById('editCoverImage');
            if (coverImageInput) {
                coverImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            // Create or update preview image
                            let preview = document.querySelector('#coverPreview');
                            if (!preview) {
                                preview = document.createElement('img');
                                preview.id = 'coverPreview';
                                preview.className = 'image-preview mt-2';
                                coverImageInput.parentNode.appendChild(preview);
                            }
                            preview.src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</body>
</html>
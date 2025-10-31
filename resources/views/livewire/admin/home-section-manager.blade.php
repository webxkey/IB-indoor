<div class="container-fluid py-4">
    <h1 class="mb-4">Landing Page Sections</h1>

    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($editing)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Edit Section</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Page Name</label>
                        <input type="text" class="form-control" wire:model="page_name">
                        @error('page_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" wire:model="display_order">
                        @error('display_order') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Section Title</label>
                    <input type="text" class="form-control" wire:model="section_title">
                    @error('section_title') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" wire:model="section_description" rows="3"></textarea>
                    @error('section_description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Images</label>
                    <input type="file" class="form-control" wire:model="newImages" multiple accept="image/*">
                    <div wire:loading wire:target="newImages">Uploading...</div>
                    @error('newImages.*') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="row mb-3">
                    @if($images)
                        @foreach($images as $index => $image)
                        <div class="col-md-3 mb-3">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                        wire:click="removeImage({{$index}})">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active">
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" wire:click="cancelEdit">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Sections</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Page Name</th>
                            <th>Title</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sections as $section)
                        <tr>
                            <td>{{ $section->page_name }}</td>
                            <td>{{ $section->section_title }}</td>
                            <td>{{ $section->display_order }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           wire:click="toggleActive({{ $section->id }})"
                                           {{ $section->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" wire:click="edit({{ $section->id }})">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
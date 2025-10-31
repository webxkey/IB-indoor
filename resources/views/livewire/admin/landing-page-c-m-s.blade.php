<div>
    <h4 class="mb-3">Landing Page CMS</h4>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-4">
                <label>Page Name</label>
                <input type="text" wire:model="page_name" class="form-control">
                @error('page_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4">
                <label>Section Title</label>
                <input type="text" wire:model="section_title" class="form-control">
                @error('section_title') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4">
                <label>Display Order</label>
                <input type="number" wire:model="display_order" class="form-control">
            </div>

            <div class="col-12 mt-3">
                <label>Section Description</label>
                <textarea wire:model="section_description" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-4 mt-3">
                <label>Status</label>
                <select wire:model="is_active" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <hr>

        <div class="mt-3">
            <label>Add Image Link</label>
            <div class="input-group">
                <input type="text" wire:model="new_image_url" class="form-control" placeholder="Enter image URL...">
                <button type="button" wire:click="addImage" class="btn btn-secondary">Add</button>
            </div>
            @if ($images)
                <div class="mt-3 d-flex flex-wrap gap-3">
                    @foreach ($images as $index => $img)
                        <div style="width: 150px; position: relative;">
                            <img src="{{ $img }}" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
                            <button type="button" wire:click="removeImage({{ $index }})"
                                class="btn btn-sm btn-danger position-absolute top-0 end-0 px-1 py-0"
                                style="font-size: 12px;">X</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">
                {{ $page_id ? 'Update Section' : 'Add New Section' }}
            </button>
        </div>
    </form>

    <hr>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Page Name</th>
                <th>Title</th>
                <th>Description</th>
                <th>Order</th>
                <th>Status</th>
                <th>Images</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $page)
                <tr>
                    <td>{{ $page->id }}</td>
                    <td>{{ $page->page_name }}</td>
                    <td>{{ $page->section_title }}</td>
                    <td>{{ Str::limit($page->section_description, 50) }}</td>
                    <td>{{ $page->display_order }}</td>
                    <td>{{ $page->is_active ? '✅' : '❌' }}</td>
                    <td>
                        @if($page->images)
                            <img src="{{ $page->images[0] }}" width="60" height="40" class="rounded">
                        @endif
                    </td>
                    <td>
                        <button wire:click="edit({{ $page->id }})" class="btn btn-sm btn-warning">Edit</button>
                        <button wire:click="delete({{ $page->id }})" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure?')">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

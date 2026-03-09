<div>
    <style>
        .blog-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .blog-card .card-body {
            flex: 1;
        }
    </style>

    @if (session()->has('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Button to open Add Blog modal -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addBlogModal">Add New Blog</button>

    <!-- Add Blog Modal -->
    <div class="modal fade" id="addBlogModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content p-4">

                <h4>Add Blog</h4>

                <input type="text" wire:model="title" class="form-control mb-2" placeholder="Title">
                @error('title') <div class="text-danger small">{{ $message }}</div> @enderror

                <textarea wire:model="description" class="form-control mb-2" placeholder="Description"></textarea>
                @error('description') <div class="text-danger small">{{ $message }}</div> @enderror

                <input type="text" wire:model="image" class="form-control mb-2" placeholder="Image URL ">
                @error('image') <div class="text-danger small">{{ $message }}</div> @enderror

                <button wire:click="saveBlog" class="btn btn-primary w-100">Save Blog</button>
            </div>
        </div>
    </div>

    <!-- Display Blog Cards -->
    <div class="row mt-4">
        @foreach ($blogs as $blog)
        <div class="col-md-4 mb-3" wire:key="blog-{{ $blog->id }}">
            <div class="card shadow-sm">
                <img src="{{ $blog->image }}" class="card-img-top" style="height:200px; object-fit:cover;">

                <div class="card-body">
                    <h5>{{ $blog->title }}</h5>
                    <p>{{ \Illuminate\Support\Str::limit($blog->description, 80) }}</p>

                    <!-- Buttons -->
                    <button class="btn btn-primary btn-sm"
                        wire:click="editBlog({{ $blog->id }})"
                        data-bs-toggle="modal"
                        data-bs-target="#editBlogModal">
                        Edit
                    </button>

                    <button class="btn btn-danger btn-sm"
                        wire:click="deleteBlog({{ $blog->id }})"
                        onclick="return confirm('Are you sure?')">
                        Delete
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

<!-- Edit Blog Modal -->
<div wire:ignore.self class="modal fade" id="editBlogModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit Blog</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="updateBlog">

          <div class="mb-3">
            <label>Title</label>
            <input type="text" class="form-control" wire:model.defer="title">
                        @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label>Description</label>
            <textarea class="form-control" wire:model.defer="description"></textarea>
                        @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label>Image URL</label>
            <input type="text" class="form-control" wire:model.defer="image">
                        @error('image') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>

          <button type="submit" class="btn btn-primary w-100">Update</button>

        </form>
      </div>

    </div>
  </div>
</div>
</div>

<!-- Script to close modal after update -->
<script>
    window.addEventListener('close-edit-modal', () => {
        var modal = bootstrap.Modal.getInstance(document.getElementById('editBlogModal'));
        modal.hide();
    });
    document.addEventListener('livewire:init', () => {
        Livewire.on('close-add-modal', () => {
            const modalEl = document.getElementById('addBlogModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modalInstance.hide();
        });

        Livewire.on('close-edit-modal', () => {
            const modalEl = document.getElementById('editBlogModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modalInstance.hide();
        });
    });
    Livewire.on('close-add-modal', () => {
        const addModal = bootstrap.Modal.getInstance(document.getElementById('addBlogModal'));
        addModal.hide();
    });


    // ✅ Auto hide success alert after 3 seconds
    Livewire.on('alert', () => {
        setTimeout(() => {
            let alert = document.querySelector('.alert');
            if (alert) alert.style.display = 'none';
        }, 3000);
    });
    Livewire.on('show-edit-modal', () => {
    const editModalEl = document.getElementById('editBlogModal');
    const modalInstance = bootstrap.Modal.getOrCreateInstance(editModalEl);
    modalInstance.show();
});
Livewire.on('show-edit-modal', () => {
    const editModalEl = document.getElementById('editBlogModal');
    const modalInstance = bootstrap.Modal.getOrCreateInstance(editModalEl);
    modalInstance.show();
});



</script>
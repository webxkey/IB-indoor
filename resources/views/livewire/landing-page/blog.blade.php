<div class="container my-5">
    <h2 class="text-center mb-4">Our Blog</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($blogs as $blog)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="{{ $blog->image }}" class="card-img-top" alt="{{ $blog->title }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $blog->title }}</h5>
                        <p class="card-text">{{ $blog->description }}</p>
                    </div>
                    <div class="card-footer text-muted">
                        {{ $blog->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

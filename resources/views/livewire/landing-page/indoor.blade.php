<div> <!-- ✅ Root wrapper required by Livewire -->

  <div class="container my-5">
    
    <h2 class="text-center mb-4">Indoor Sports Venues</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      @foreach($VenuesDetails as $index => $venue)
        <div class="col">
          <div class="card h-100 shadow-sm">
            <img src="{{ $venue->image_url }}" class="card-img-top" alt="{{ $venue->name }}">
            <div class="card-body d-flex flex-column text-center">
              <h5 class="card-title">{{ $venue->name }}</h5>
              <p class="card-text">{{ Str::limit($venue->description, 100) }}</p>
              <button 
                class="btn btn-success mt-auto" 
                data-bs-toggle="modal" 
                data-bs-target="#venueModal" 
                onclick="loadSlider({{ $index }})"
              >
                View More
              </button>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Modal with Carousel -->
  <div class="modal fade" id="venueModal" tabindex="-1" aria-labelledby="venueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="venueModalLabel">Venue Gallery</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="venueCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner" id="carouselInner"></div>
            <button class="carousel-control-prev" type="button" data-bs-target="#venueCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#venueCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

</div> <!-- ✅ Close root wrapper -->


@push('scripts')
<script>
  // Convert the gallery_images_json field into usable JS arrays
    const venueImages = @json($VenuesDetails->map(function($v) {
      if (is_string($v->gallery_images_json)) {
        return json_decode($v->gallery_images_json, true) ?? [];
      } elseif (is_array($v->gallery_images_json)) {
        return $v->gallery_images_json;
      } else {
        return [];
      }
    }));
  function loadSlider(index) {
    const carouselInner = document.getElementById("carouselInner");
    carouselInner.innerHTML = "";

    if (!venueImages[index] || venueImages[index].length === 0) {
      carouselInner.innerHTML = `<div class='text-center p-4'>No gallery images available.</div>`;
      return;
    }

    venueImages[index].forEach((img, i) => {
      const div = document.createElement("div");
      div.className = `carousel-item ${i === 0 ? "active" : ""}`;
      div.innerHTML = `<img src="${img.trim()}" class="d-block w-100 rounded" alt="Venue Image ${i + 1}">`;
      carouselInner.appendChild(div);
    });
  }
</script>
@endpush

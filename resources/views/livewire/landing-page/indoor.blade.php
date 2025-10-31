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
                onclick="showVenueDetails('{{ $venue->name }}', '{{ $venue->description }}', '{{ $venue->location }}', '{{ $venue->operating_hours }}', '{{ $venue->contact }}', '{{ $venue->image_url }}')"
              >
                View More
              </button>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Modal with Venue Details -->
  <div class="modal fade" id="venueModal" tabindex="-1" aria-labelledby="venueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="venueModalLabel">Venue Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Left side: Venue Details -->
            <div class="col-md-6">
              <h4 id="venueName"></h4>
              <p id="venueDescription" class="mt-3"></p>
              <div class="mt-3">
                <strong>Location:</strong>
                <p id="venueLocation"></p>
              </div>
              <div class="mt-2">
                <strong>Operating Hours:</strong>
                <p id="venueHours"></p>
              </div>
              <div class="mt-2">
                <strong>Contact:</strong>
                <p id="venueContact"></p>
              </div>
            </div>
            <!-- Right side: Venue Image -->
            <div class="col-md-6">
              <img id="venueImage" class="img-fluid rounded" alt="Venue Image">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript for handling venue details -->
<script>

  function showVenueDetails(name, description, location, hours, contact, imageUrl) {
    document.getElementById('venueName').textContent = name;
    document.getElementById('venueDescription').textContent = description;
    document.getElementById('venueLocation').textContent = location;
    document.getElementById('venueImage').src = imageUrl;
    
    // Optional: clear out non-existing fields
    document.getElementById('venueHours').textContent = '';
    document.getElementById('venueContact').textContent = '';
=======
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
</div> <!-- ✅ Close root wrapper -->


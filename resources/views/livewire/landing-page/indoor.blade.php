<div> 

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
      document.getElementById('venueName').innerText = name;
      document.getElementById('venueDescription').innerText = description;
      document.getElementById('venueLocation').innerText = location;
      document.getElementById('venueHours').innerText = hours;
      document.getElementById('venueContact').innerText = contact;
      document.getElementById('venueImage').src = imageUrl;
    }
  </script>
</div>



<div>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> 
        Venues are managed in the Venues section. This preview shows the latest venues that will appear on the landing page.
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Venue Name</th>
                    <th>Created Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($VenuesDetails as $venue)
                    <tr>
                        <td>
                            <img src="{{ $venue->image_url }}" alt="{{ $venue->venue_name }}" 
                                 style="height: 50px; width: 50px; object-fit: cover;">
                        </td>
                        <td>{{ $venue->venue_name }}</td>
                        <td>{{ $venue->created_at->format('F d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.venues.edit', $venue->id) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        <a href="{{ route('admin.venues') }}" class="btn btn-primary">
            Manage All Venues
        </a>
    </div>
</div>
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingGalleryImage extends Model
{
    protected $table = 'booking_galleryimage';

    protected $fillable = ['image_url','venue_id'];

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'venue_id');
    }
}

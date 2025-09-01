<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingVenue extends Model
{
    protected $table = 'booking_venue';

    protected $fillable = [
        'name','address','rating','reviews','image_url','complex_type',
        'county','location','postal_code','contact_number','email_address',
        'website','status','opening_hours','amenities','cover_image',
        'gallery_images_json','video_tour_url','description','terms',
        'social_links'
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'amenities' => 'array',
        'gallery_images_json' => 'array',
        'social_links' => 'array',
    ];

    public function sports()
    {
        return $this->hasMany(BookingSport::class, 'venue_id');
    }

    public function gallery()
    {
        return $this->hasMany(BookingGalleryImage::class, 'venue_id');
    }

    public function reviews()
    {
        return $this->hasMany(BookingVenueReview::class, 'venue_id');
    }
}

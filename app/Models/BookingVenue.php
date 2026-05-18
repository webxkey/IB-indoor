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
        'social_links','analytics_enabled','venue_category'
    ];

    protected $appends = ['image_url', 'cover_image_url'];

    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg';
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            if (str_contains($value, '127.0.0.1') || str_contains($value, 'localhost')) {
                $path = parse_url($value, PHP_URL_PATH);
                if (str_starts_with($path, '/images/')) {
                    return asset('api/indoor-admin/local-images/' . substr($path, 8));
                }
                if (str_starts_with($path, '/storage/')) {
                    return asset('api/indoor-admin/local-storage/' . substr($path, 9));
                }
                return asset('api/indoor-admin' . $path);
            }
            return $value;
        }

        return asset('api/indoor-admin/local-storage/' . $value);
    }

    public function getCoverImageUrlAttribute()
    {
        if (!$this->cover_image) {
            return $this->image_url;
        }

        if (filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
            if (str_contains($this->cover_image, '127.0.0.1') || str_contains($this->cover_image, 'localhost')) {
                $path = parse_url($this->cover_image, PHP_URL_PATH);
                if (str_starts_with($path, '/images/')) {
                    return asset('api/indoor-admin/local-images/' . substr($path, 8));
                }
                if (str_starts_with($path, '/storage/')) {
                    return asset('api/indoor-admin/local-storage/' . substr($path, 9));
                }
                return asset('api/indoor-admin' . $path);
            }
            return $this->cover_image;
        }

        return asset('api/indoor-admin/local-storage/' . $this->cover_image);
    }




   protected $attributes = [
        'image_url' => 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg',
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
    public function bookings()
    {
        return $this->hasMany(BookingBooking::class, 'complex_id_id');
    }
        public function owner()
    {
        return $this->hasOne(User::class, 'complex_id');
    }
}

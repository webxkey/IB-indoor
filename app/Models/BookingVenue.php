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
        'social_links','analytics_enabled','venue_category', 'is_featured',
        'city', 'district', 'geohash', 'lat', 'lng',
        'advance_payment_required', 'advance_payment_type', 'advance_payment_value',
        'bank_transfer_payments_enabled', 'cash_payments_enabled', 'online_payments_enabled',
        'venue_card_payments_enabled', 'booking_payment_mode'
    ];
   protected $attributes = [
        'image_url' => 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg',
        'is_featured' => false,
    ];
    protected $casts = [
        'opening_hours' => 'array',
        'amenities' => 'array',
        'gallery_images_json' => 'array',
        'social_links' => 'array',
        'is_featured' => 'boolean',
    ];

    protected $appends = ['cover_image_url', 'gallery_images_urls'];

    public function getCoverImageUrlAttribute()
    {
        if (!$this->cover_image) {
            return null;
        }
        
        if (filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
            return $this->cover_image;
        }

        $host = app()->runningInConsole() ? config('app.url') : request()->getSchemeAndHttpHost();
        return rtrim($host, '/') . '/api/indoor-admin/local-storage/' . ltrim($this->cover_image, '/');
    }

    public function getGalleryImagesUrlsAttribute()
    {
        $gallery = $this->gallery_images_json;
        if (!is_array($gallery)) {
            return [];
        }
        
        $host = app()->runningInConsole() ? config('app.url') : request()->getSchemeAndHttpHost();
        $baseUrl = rtrim($host, '/') . '/api/indoor-admin/local-storage/';

        return array_map(function ($path) use ($baseUrl) {
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            return $baseUrl . ltrim($path, '/');
        }, $gallery);
    }

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

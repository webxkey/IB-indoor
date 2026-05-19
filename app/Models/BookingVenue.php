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

    protected $appends = ['image_url', 'cover_image_url', 'gallery_images_urls'];

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

    /**
     * Resolve a single storage path to a full URL.
     */
    protected function resolveStoragePath(?string $path): ?string
    {
        if (!$path) return null;

        // Already a full URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            if (str_contains($path, '127.0.0.1') || str_contains($path, 'localhost')) {
                $parsed = parse_url($path, PHP_URL_PATH);
                if (str_starts_with($parsed, '/storage/')) {
                    return asset('api/indoor-admin/local-storage/' . substr($parsed, 9));
                }
                return asset('api/indoor-admin' . $parsed);
            }
            return $path;
        }

        // Relative path stored via Storage::disk('public')->store()
        return asset('api/indoor-admin/local-storage/' . $path);
    }

    /**
     * Return gallery images as resolved URLs.
     */
    public function getGalleryImagesUrlsAttribute(): array
    {
        $raw = $this->getAttributes()['gallery_images_json'] ?? null;
        $items = is_string($raw) ? json_decode($raw, true) : $raw;
        if (!is_array($items)) return [];

        return array_values(array_filter(array_map(
            fn($path) => $this->resolveStoragePath($path),
            $items
        )));
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

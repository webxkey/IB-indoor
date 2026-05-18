<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSport extends Model
{
    protected $table = 'booking_sport';

    protected $fillable = [
        'name','price','image','available','game_type','rate_type',
        'maximum_court','status','description','additional_charges',
        'advance_required','venue_id','average_rating','pricing_rules','blocked_slots','opening_hours'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg';
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            // Fix local URLs and route them through the /api proxy
            if (str_contains($this->image, '127.0.0.1') || str_contains($this->image, 'localhost')) {
                $path = parse_url($this->image, PHP_URL_PATH);
                if (str_starts_with($path, '/images/')) {
                    return asset('api/indoor-admin/local-images/' . substr($path, 8));
                }
                if (str_starts_with($path, '/storage/')) {
                    return asset('api/indoor-admin/local-storage/' . substr($path, 9));
                }
                return asset('api/indoor-admin' . $path);
            }
            return $this->image;
        }

        // Handle path-based images (stored in storage/app/public/sports)
        return asset('api/indoor-admin/local-storage/' . $this->image);
    }






   protected $attributes = [
        'image' => 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg',
        'available' => true,
        'status' => 'active',
        'average_rating' => 0,
        'advance_required' => false,
    ];
    protected $casts = [
        'available' => 'boolean',
        'advance_required' => 'boolean',
        'additional_charges' => 'array',
        'pricing_rules' => 'array',
        'blocked_slots' => 'array',
        'opening_hours' => 'array',
    ];

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'venue_id');
    }

    public function facilities()
    {
        return $this->hasMany(BookingFacility::class, 'sport_id');
    }

    public function discounts()
    {
        return $this->hasMany(BookingDiscount::class, 'sport_id');
    }

    public function reviews()
    {
        return $this->hasMany(BookingSportReview::class, 'sport_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSport extends Model
{
    protected $table = 'booking_sport';

    protected $fillable = [
        'name','price','image','available','game_type','rate_type',
        'maximum_court','status','description','additional_charges',
        'advance_required','venue_id','average_rating','pricing_rules','blocked_slots','opening_hours',
        'advance_payment_required_override','booking_payment_mode_override',
        'advance_payment_type_override','advance_payment_value_override',
        'private_booking_enabled','private_booking_price','private_booking_min_duration_minutes',
        'private_booking_price_multiplier','private_booking_pricing_mode'
    ];
   protected $attributes = [
        'image' => 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg',
        'available' => true,
        'status' => 'Active',
        'average_rating' => 0,
        'advance_required' => false,
        'private_booking_enabled' => true,
        'private_booking_min_duration_minutes' => 180,
        'private_booking_price_multiplier' => 1.00,
        'private_booking_pricing_mode' => 'flat_total',
    ];
    protected $casts = [
        'available' => 'boolean',
        'advance_required' => 'boolean',
        'private_booking_enabled' => 'boolean',
        'private_booking_min_duration_minutes' => 'integer',
        'private_booking_price_multiplier' => 'float',
        'additional_charges' => 'array',
        'pricing_rules' => 'array',
        'blocked_slots' => 'array',
        'opening_hours' => 'array',
    ];

    protected $appends = ['image_url'];

    public static function getDefaultImageForName($name)
    {
        $n = strtolower((string) $name);

        if (str_contains($n, 'cricket') && str_contains($n, 'football')) {
            return asset('images/sports_images/cricket&football.jpg');
        }
        if (str_contains($n, 'football')) {
            return asset('images/sports_images/football.jpg');
        }
        if (str_contains($n, 'cricket')) {
            return asset('images/sports_images/cricket.jpg');
        }
        if (str_contains($n, 'badminton')) {
            return asset('images/sports_images/badminton.jpg');
        }
        if (str_contains($n, 'basketball')) {
            return asset('images/sports_images/basketball.jpeg');
        }
        if (str_contains($n, 'pooltable') || str_contains($n, 'pool table')) {
            return asset('images/sports_images/pooltable.jpg');
        }
        if (str_contains($n, 'pool') || str_contains($n, 'swim')) {
            return asset('images/sports_images/pools.jpg');
        }

        return asset('images/sports_images/default.jpg');
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return static::getDefaultImageForName($this->name);
        }
        
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        $host = app()->runningInConsole() ? config('app.url') : request()->getSchemeAndHttpHost();
        return rtrim($host, '/') . '/api/indoor-admin/local-storage/' . ltrim($this->image, '/');
    }

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

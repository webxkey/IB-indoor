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
        'private_booking_price','private_booking_min_duration_minutes',
        'private_booking_price_multiplier','private_booking_pricing_mode'
    ];
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

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg';
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

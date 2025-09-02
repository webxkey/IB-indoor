<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSport extends Model
{
    protected $table = 'booking_sport';

    protected $fillable = [
        'name','price','image','available','game_type','rate_type',
        'maximum_court','status','description','additional_charges',
        'advance_required','venue_id','average_rating'
    ];
   protected $attributes = [
        'image' => 'https://p.imgci.com/db/PICTURES/CMS/242000/242055.jpg',
        'available' => true,
        'status' => 'active',
        'average_rating' => 0,
    ];
    protected $casts = [
        'available' => 'boolean',
        'advance_required' => 'boolean',
        'additional_charges' => 'array',
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

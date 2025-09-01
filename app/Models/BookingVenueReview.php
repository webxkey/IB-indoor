<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingVenueReview extends Model
{
    protected $table = 'booking_venuereview';

    protected $fillable = [
        'rating','comment','categories','photos','would_recommend','user_id','venue_id'
    ];

    protected $casts = [
        'categories' => 'array',
        'photos' => 'array',
        'would_recommend' => 'boolean',
    ];

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'venue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingVenueReview extends Model
{
    protected $table = 'booking_venuereview';

    protected $fillable = [
        'rating','comment','categories','photos','would_recommend','user_id','venue_id',
        'owner_reply','owner_replied_at',
    ];

    protected $casts = [
        'categories' => 'array',
        'photos' => 'array',
        'would_recommend' => 'boolean',
        'owner_replied_at' => 'datetime',
    ];

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'venue_id');
    }

    
     public function user()
    {
        return $this->belongsTo(UserUser::class, 'user_id', 'id');
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSportReview extends Model
{
    protected $table = 'booking_sportreview';

    protected $fillable = [
        'rating','comment','visit_date','photos','would_recommend',
        'booking_id','sport_id','user_id','categories'
    ];

    protected $casts = [
        'photos' => 'array',
        'categories' => 'array',
        'visit_date' => 'date',
        'would_recommend' => 'boolean',
    ];

    public function sport()
    {
        return $this->belongsTo(BookingSport::class, 'sport_id');
    }

    public function booking()
    {
        return $this->belongsTo(BookingBooking::class, 'booking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

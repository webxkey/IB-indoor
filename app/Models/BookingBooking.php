<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingBooking extends Model
{
    protected $table = 'booking_booking';

    protected $fillable = [
        'game_name','user_name','user_number','court_number','permanent',
        'booking_date','start_time','end_time','duration','price',
        'payment_status','payment_method','status','notes','qr_code',
        'admin_comments','date','time_slot','total',
        'user_id_id','game_id_id','complex_id_id','sport_id'
    ];

    protected $casts = [
        'permanent' => 'boolean',
        'booking_date' => 'date',
        'date' => 'date',
    ];

    // Relationships
    public function sport()
    {
        return $this->belongsTo(BookingSport::class, 'sport_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_id');
    }

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'complex_id_id');
    }
}

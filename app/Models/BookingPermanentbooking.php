<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPermanentbooking extends Model
{
    protected $table = 'booking_permanentbooking';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'sport_id',
        'team_id',
        'chat_id',
        'recurring_config',
        'start_time',
        'end_time',
        'duration',
        'price',
        'complex_id',
        'created_at'
    ];

    protected $casts = [
        'recurring_config' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany(BookingBooking::class, 'permanent_source_id');
    }
}

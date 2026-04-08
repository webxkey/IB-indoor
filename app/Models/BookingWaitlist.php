<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BookingWaitlist extends Model
{
    protected $table = 'booking_waitlist';
    protected $fillable = ['sport_id','booking_date','time_slot','court_number','customer_name','customer_phone','status','notified_at'];

    protected $casts = [
        'notified_at' => 'datetime',
    ];

    public function sport()
    {
        return $this->belongsTo(BookingSport::class, 'sport_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingFacility extends Model
{
    protected $table = 'booking_facility';

    protected $fillable = ['name','icon','sport_id'];

    public function sport()
    {
        return $this->belongsTo(BookingSport::class, 'sport_id');
    }
}

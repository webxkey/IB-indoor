<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingDiscount extends Model
{
    protected $table = 'booking_discount';

    protected $fillable = ['type','color','sport_id'];

    public function sport()
    {
        return $this->belongsTo(BookingSport::class, 'sport_id');
    }
}

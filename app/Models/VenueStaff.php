<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VenueStaff extends Model
{
    protected $table = 'venue_staff';
    protected $fillable = ['venue_id','name','role','phone','shift','status','email'];

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'venue_id');
    }
}

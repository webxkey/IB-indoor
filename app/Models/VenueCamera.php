<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VenueCamera extends Model
{
    protected $table = 'venue_cameras';
    protected $fillable = ['venue_id','name','location','stream_url','status'];

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'venue_id');
    }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VenueStaff extends Model
{
    protected $table = 'venue_staff';
    protected $fillable = ['venue_id','name','role','phone','shift','status','email','photo','is_online','status_detail'];
    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) return null;
        // Use the proxy route defined in api.php for consistency
        return url('api/indoor-admin/local-storage/' . $this->photo);
    }

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'venue_id');
    }
}

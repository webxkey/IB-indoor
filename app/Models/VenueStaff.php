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
        if (!$this->photo) {
            return null;
        }
        
        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        $host = app()->runningInConsole() ? config('app.url') : request()->getSchemeAndHttpHost();
        return rtrim($host, '/') . '/api/indoor-admin/local-storage/' . ltrim($this->photo, '/');
    }

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'venue_id');
    }
}

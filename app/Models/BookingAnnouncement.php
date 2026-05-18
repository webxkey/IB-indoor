<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAnnouncement extends Model
{
    protected $table = 'booking_announcement';

    protected $fillable = [
        'venue_id',
        'title',
        'subtitle',
        'short_description',
        'full_description',
        'image',
        'fallback_bg_color',
        'label',
        'is_active',
        'is_pinned',
        'sort_order',
        'expires_at',
        'read_more_label',
        'read_more_url',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_pinned'  => 'boolean',
        'sort_order' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'venue_id');
    }
}

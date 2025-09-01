<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingNotification extends Model
{
    protected $table = 'booking_notification';

    protected $fillable = [
        'type','title','message','data','is_read','user_id','created_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    public $timestamps = false; // already storing created_at manually

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

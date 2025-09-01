<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_id';
    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'complex_id',
        'game_id',
        'game_name',
        'user_name',
        'user_number',
        'court_number',
        'permanent',
        'booking_date',
        'start_time',
        'end_time',
        'duration',
        'price',
        'payment_status',
        'payment_method',
        'status',
        'notes',
        'qr_code',
        'admin_comments'
    ];

    protected $casts = [
        'permanent' => 'boolean',
        'booking_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function complex()
    {
        return $this->belongsTo(Complex::class, 'complex_id');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'game_id', 'game_id');
    }
}
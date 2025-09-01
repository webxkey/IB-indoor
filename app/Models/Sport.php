<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $primaryKey = 'game_id';
    protected $table = 'sports';

    protected $fillable = [
        'complex_id',
        'game_name',
        'game_type',
        'rate_type',
        'price',
        'maximum_court',
        'status',
        'game_image',
        'description',
        'additional_charges',
        'advance_required'
    ];

    protected $casts = [
        'additional_charges' => 'array',
        'advance_required' => 'boolean'
    ];

    public function complex()
    {
        return $this->belongsTo(Complex::class, 'complex_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'game_id');
    }
    public function game_url()
    {
        return asset('storage/' . $this->game_image);
    }

}
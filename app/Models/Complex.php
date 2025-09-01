<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complex extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'complexes';

    protected $fillable = [
        'complex_name',
        'complex_type',
        'county',
        'location',
        'address',
        'postal_code',
        'contact_number',
        'email_address',
        'website',
        'status',
        'opening_hours',
        'amenities',
        'cover_image',
        'gallery_images',
        'video_tour_url',
        'description',
        'terms',
        'social_links'
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'amenities' => 'array',
        'gallery_images' => 'array',
        'social_links' => 'array'
    ];

    public function sports()
    {
        return $this->hasMany(Sport::class, 'complex_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'complex_id');
    }
}
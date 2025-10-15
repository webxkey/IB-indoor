<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complex extends Model
{
    use HasFactory;

    protected $table = 'complexes';

    // Update fillable to match your database columns
    protected $fillable = [
        'complex_name', 'complex_type', 'address', 'county', 'location', 'postal_code',
        'contact_number', 'email_address', 'website', 'status', 'description'
    ];

    // Add this to ensure status can be stored properly
    protected $attributes = [
        'status' => 'Active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function venues()
    {
        return $this->hasMany(BookingVenue::class);
    }

    public function sports()
    {
        return $this->hasMany(Sport::class);
    }
}
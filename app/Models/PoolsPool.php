<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolsPool extends Model
{
    use HasFactory;

    protected $table = 'pools_pool';

    protected $fillable = [
        'venue_id',
        'name',
        'description',
        'image',
        'status',
        'capacity',
        'max_admissions_per_booking',
        'max_sessions_per_booking',
        'cancellation_cutoff_hours',
        'opening_hours',
        'blocked_slots',
        'booking_payment_mode_override',
        'advance_payment_required_override',
        'advance_payment_type_override',
        'advance_payment_value_override',
        'private_booking_price',
        'private_request_enabled',
        'private_request_limit_type',
        'private_request_limit_value',
        'guest_names_required',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'blocked_slots' => 'array',
        'advance_payment_required_override' => 'boolean',
        'private_request_enabled' => 'boolean',
        'guest_names_required' => 'boolean',
        'capacity' => 'integer',
        'max_admissions_per_booking' => 'integer',
        'max_sessions_per_booking' => 'integer',
        'cancellation_cutoff_hours' => 'integer',
        'private_booking_price' => 'decimal:2',
        'advance_payment_value_override' => 'decimal:2',
        'private_request_limit_value' => 'decimal:2',
    ];

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'venue_id');
    }

    public function admissionTypes()
    {
        return $this->hasMany(PoolsPooladmissiontype::class, 'pool_id')->orderBy('sort_order', 'asc');
    }

    public function bookings()
    {
        return $this->hasMany(PoolsPoolbooking::class, 'pool_id');
    }
}

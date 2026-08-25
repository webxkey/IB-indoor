<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingBooking extends Model
{
    protected $table = 'booking_booking';

    protected $attributes = [
        'is_private' => false,
        'is_challenge_booking' => false,
        'is_initial_permanent_occurrence' => false,
        'requires_advance_payment' => false,
        'advance_amount' => 0,
        'amount_paid' => 0,
        'balance_due' => 0,
        'offline_paid_amount' => 0,
        'online_paid_amount' => 0,
        'points_discount_amount' => 0,
        'reward_status' => 'not_eligible',
        'financial_status' => 'Pending',
        'payment_status' => 'Pending',
        'status' => 'Confirmed',
    ];

    protected $fillable = [
        'game_name',
        'user_name',
        'user_number',
        'court_number',
        'permanent_source_id',
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
        'admin_comments',
        'date',
        'time_slot',
        'user_id_id',
        'game_id_id',
        'complex_id_id',
        'is_challenge_booking',
        'opponent_team_id',
        'team_id',
        'advance_amount',
        'amount_paid',
        'balance_due',
        'financial_status',
        'is_initial_permanent_occurrence',
        'offline_paid_amount',
        'online_paid_amount',
        'points_discount_amount',
        'requires_advance_payment',
        'reward_status',
        'is_private',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'date' => 'date',
        'is_private' => 'boolean',
        'is_challenge_booking' => 'boolean',
        'is_initial_permanent_occurrence' => 'boolean',
        'requires_advance_payment' => 'boolean',
    ];

    // Relationships
    public function sport()
    {
        return $this->belongsTo(BookingSport::class, 'game_id_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_id');
    }

    public function venue()
    {
        return $this->belongsTo(BookingVenue::class, 'complex_id_id');
    }
}

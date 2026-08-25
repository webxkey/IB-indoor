<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolsPoolbooking extends Model
{
    use HasFactory;

    protected $table = 'pools_poolbooking';

    protected $attributes = [
        'total_admissions' => 1,
        'booking_total' => 0.00,
        'discount_amount' => 0.00,
        'advance_amount' => 0.00,
        'online_paid_amount' => 0.00,
        'points_discount_amount' => 0.00,
        'amount_paid' => 0.00,
        'balance_due' => 0.00,
        'requires_advance_payment' => false,
        'is_private' => false,
        'status' => 'Confirmed',
        'financial_status' => 'Paid',
    ];

    protected $fillable = [
        'booking_reference',
        'pool_id',
        'user_id',
        'occurrence_id',
        'user_name',
        'user_number',
        'status',
        'total_admissions',
        'booking_total',
        'total_amount',
        'discount_amount',
        'requires_advance_payment',
        'advance_payment_type_snapshot',
        'advance_payment_value_snapshot',
        'advance_amount',
        'online_paid_amount',
        'points_discount_amount',
        'amount_paid',
        'balance_due',
        'financial_status',
        'payment_status',
        'payment_reservation_expires_at',
        'qr_code',
        'checked_in_at',
        'checked_in_by_id',
        'cancelled_at',
        'notes',
        'discount_id',
        'booking_group_id',
        'is_private',
    ];

    protected $casts = [
        'booking_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'online_paid_amount' => 'decimal:2',
        'points_discount_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'requires_advance_payment' => 'boolean',
        'is_private' => 'boolean',
        'checked_in_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'payment_reservation_expires_at' => 'datetime',
    ];

    // Accessor & Mutator for total_amount alias to booking_total
    public function getTotalAmountAttribute()
    {
        return $this->booking_total;
    }

    public function setTotalAmountAttribute($value)
    {
        $this->attributes['booking_total'] = $value;
    }

    // Accessor & Mutator for payment_status alias to financial_status
    public function getPaymentStatusAttribute()
    {
        return $this->financial_status;
    }

    public function setPaymentStatusAttribute($value)
    {
        $this->attributes['financial_status'] = $value;
    }

    public function pool()
    {
        return $this->belongsTo(PoolsPool::class, 'pool_id');
    }

    public function user()
    {
        return $this->belongsTo(UserUser::class, 'user_id');
    }

    public function occurrence()
    {
        return $this->belongsTo(PoolsPoolsessionoccurrence::class, 'occurrence_id');
    }

    public function items()
    {
        return $this->hasMany(PoolsPoolbookingitem::class, 'pool_booking_id');
    }
}

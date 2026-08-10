<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolsPoolbooking extends Model
{
    use HasFactory;

    protected $table = 'pools_poolbooking';

    protected $fillable = [
        'pool_id',
        'user_id',
        'total_amount',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function pool()
    {
        return $this->belongsTo(PoolsPool::class, 'pool_id');
    }

    public function user()
    {
        return $this->belongsTo(UserUser::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(PoolsPoolbookingitem::class, 'booking_id');
    }
}

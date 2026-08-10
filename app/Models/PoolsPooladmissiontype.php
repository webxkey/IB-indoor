<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolsPooladmissiontype extends Model
{
    use HasFactory;

    protected $table = 'pools_pooladmissiontype';

    protected $fillable = [
        'pool_id',
        'name',
        'price',
        'capacity_units',
        'requires_guest_name',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'capacity_units' => 'integer',
        'requires_guest_name' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function pool()
    {
        return $this->belongsTo(PoolsPool::class, 'pool_id');
    }
}

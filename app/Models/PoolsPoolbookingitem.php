<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolsPoolbookingitem extends Model
{
    use HasFactory;

    protected $table = 'pools_poolbookingitem';

    public $timestamps = false; // Schema shows only created_at column

    protected $fillable = [
        'pool_booking_id',
        'admission_type_id',
        'quantity',
        'unit_price',
        'line_total',
        'guest_name',
        'created_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(PoolsPoolbooking::class, 'pool_booking_id');
    }

    public function admissionType()
    {
        return $this->belongsTo(PoolsPooladmissiontype::class, 'admission_type_id');
    }
}

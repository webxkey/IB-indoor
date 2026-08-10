<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolsPoolbookingitem extends Model
{
    use HasFactory;

    protected $table = 'pools_poolbookingitem';

    protected $fillable = [
        'booking_id',
        'admission_type_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(PoolsPoolbooking::class, 'booking_id');
    }

    public function admissionType()
    {
        return $this->belongsTo(PoolsPooladmissiontype::class, 'admission_type_id');
    }
}

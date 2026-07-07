<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafeteriaSaleItem extends Model
{
    use HasFactory;

    protected $table = 'cafeteria_sale_items';

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'product_code',
        'quantity',
        'unit_price',
        'discount_percentage',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function sale()
    {
        return $this->belongsTo(CafeteriaSale::class, 'sale_id');
    }

    public function product()
    {
        return $this->belongsTo(CafeteriaProduct::class, 'product_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafeteriaProduct extends Model
{
    use HasFactory;

    protected $table = 'cafeteria_products';

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'price',
        'wholesale_price',
        'distribute_price',
        'stock',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'distribute_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(CafeteriaCategory::class, 'category_id');
    }
}

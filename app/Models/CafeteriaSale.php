<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafeteriaSale extends Model
{
    use HasFactory;

    protected $table = 'cafeteria_sales';

    protected $fillable = [
        'billing_no',
        'user_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'discount_amount',
        'grand_total',
        'payment_method',
        'payment_status',
        'price_type',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(CafeteriaSaleItem::class, 'sale_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(UserUser::class, 'customer_id');
    }
}

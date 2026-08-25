<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'complex_id',
        'requested_by',
        'request_amount',
        'paid_amount',
        'status',
        'notes',
    ];

    public function requester()
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }
}

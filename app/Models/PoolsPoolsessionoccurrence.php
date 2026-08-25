<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolsPoolsessionoccurrence extends Model
{
    use HasFactory;

    protected $table = 'pools_poolsessionoccurrence';

    protected $fillable = [
        'pool_id',
        'template_id',
        'session_date',
        'start_time',
        'end_time',
        'name',
        'capacity',
        'status',
    ];

    protected $casts = [
        'session_date' => 'date',
        'capacity' => 'integer',
    ];

    public function pool()
    {
        return $this->belongsTo(PoolsPool::class, 'pool_id');
    }

    public function template()
    {
        return $this->belongsTo(PoolsPoolsessiontemplate::class, 'template_id');
    }

    public function bookings()
    {
        return $this->hasMany(PoolsPoolbooking::class, 'occurrence_id');
    }
}

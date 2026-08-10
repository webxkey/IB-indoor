<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolsPoolsessiontemplate extends Model
{
    use HasFactory;

    protected $table = 'pools_poolsessiontemplate';

    protected $fillable = [
        'pool_id',
        'weekday',
        'start_time',
        'end_time',
        'name',
        'capacity_override',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity_override' => 'integer',
        'weekday' => 'integer',
    ];

    public function pool()
    {
        return $this->belongsTo(PoolsPool::class, 'pool_id');
    }

    public function occurrences()
    {
        return $this->hasMany(PoolsPoolsessionoccurrence::class, 'template_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'layout_type',
        'gap_size',
        'border_radius',
        'display_duration',
        'sort_order',
        'media_items',
        'is_active',
        'complex_id',
    ];

    protected $casts = [
        'media_items' => 'array',
        'is_active' => 'boolean',
    ];
}

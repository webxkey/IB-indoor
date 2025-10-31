<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'page_name',
        'section_title',
        'section_description',
        'images',
        'display_order',
        'is_active',
    ];

    // This is key: cast images column to array
    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
    ];
}

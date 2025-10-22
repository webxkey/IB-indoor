<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $table = 'landing_pages';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'page_name',
        'section_title',
        'section_description',
        'images',
        'display_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'images' => 'array', // decode/encode JSON automatically
        'is_active' => 'boolean',
    ];
}

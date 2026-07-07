<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafeteriaCategory extends Model
{
    use HasFactory;

    protected $table = 'cafeteria_categories';

    protected $fillable = [
        'name',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(CafeteriaProduct::class, 'category_id');
    }
}

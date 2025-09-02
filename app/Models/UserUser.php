<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // if you use API tokens

class UserUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users_user'; // specify custom table name

    protected $primaryKey = 'id'; // primary key

    public $timestamps = false; // since your table has no created_at / updated_at

    protected $fillable = [
        'password',
        'last_login',
        'is_superuser',
        'email',
        'phone_number',
        'is_active',
        'is_staff',
        'first_name',
        'last_name',
        'profile_picture',
        'address',
        'bio',
        'sports_preferences',
        'teams',
        'availability',
        'is_public_profile',
        'is_show_contact',
        'points',
        'referral_code',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_superuser' => 'boolean',
        'is_active' => 'boolean',
        'is_staff' => 'boolean',
        'is_public_profile' => 'boolean',
        'is_show_contact' => 'boolean',
        'sports_preferences' => 'array',
        'teams' => 'array',
        'points' => 'integer',
        'last_login' => 'datetime',
    ];
}

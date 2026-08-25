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
        'point_penalty_debt',
        'reserved_points',
        'referral_code',
        'username',
        'username_changes_used',
        'accepted_tnc',
        'gender',
        'allow_direct_team_add',
    ];

    protected $attributes = [
        'is_active' => true,
        'is_staff' => false,
        'is_superuser' => false,
        'points' => 0,
        'point_penalty_debt' => 0,
        'reserved_points' => 0,
        'is_public_profile' => true,
        'is_show_contact' => true,
        'availability' => 'both',
        'username_changes_used' => 0,
        'accepted_tnc' => true,
        'gender' => 'prefer_not_to_say',
        'allow_direct_team_add' => true,
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
        'accepted_tnc' => 'boolean',
        'allow_direct_team_add' => 'boolean',
        'sports_preferences' => 'array',
        'teams' => 'array',
        'points' => 'integer',
        'point_penalty_debt' => 'integer',
        'reserved_points' => 'integer',
        'username_changes_used' => 'integer',
        'last_login' => 'datetime',
    ];

     public function reviews()
    {
        return $this->hasMany(BookingVenueReview::class, 'user_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LeagueLeague extends Model
{
    use HasUuids;

    protected $table = 'league_league';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name', 'slug', 'description', 'sport_type', 'format', 'logo', 'banner',
        'theme_color', 'cricket_config', 'sport_config', 'num_teams', 'squad_size',
        'min_players', 'season_year', 'season_number', 'season_name',
        'registration_start', 'registration_end', 'start_date', 'end_date',
        'prize_pool', 'sponsors', 'rules_text', 'status', 'is_active', 'is_public',
        'is_featured', 'allow_draws', 'enable_tiebreaker', 'total_matches_planned',
        'matches_completed', 'contact_email', 'contact_phone', 'primary_venue',
        'created_by_id',
    ];

    protected $casts = [
        'cricket_config'    => 'array',
        'sport_config'      => 'array',
        'sponsors'          => 'array',
        'tiebreaker_config' => 'array',
        'social_links'      => 'array',
        'metadata'          => 'array',
        'is_active'         => 'boolean',
        'is_public'         => 'boolean',
        'is_featured'       => 'boolean',
        'allow_draws'       => 'boolean',
        'enable_tiebreaker' => 'boolean',
        'start_date'        => 'date',
        'end_date'          => 'date',
        'registration_start'=> 'datetime',
        'registration_end'  => 'datetime',
    ];

    public function teams()
    {
        return $this->hasMany(LeagueTeam::class, 'league_id');
    }

    public function matches()
    {
        return $this->hasMany(LeagueMatch::class, 'league_id');
    }

    public function pointsTable()
    {
        return $this->hasMany(LeaguePointsTable::class, 'league_id');
    }
}

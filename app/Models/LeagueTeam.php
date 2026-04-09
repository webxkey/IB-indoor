<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LeagueTeam extends Model
{
    use HasUuids;

    protected $table = 'league_leagueteam';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'league_id', 'team_id', 'team_name_override', 'team_short_name',
        'registration_date', 'approved_date', 'status', 'jersey_color',
        'team_logo_override', 'titles_won', 'is_active', 'is_eliminated',
        'slot_number', 'is_placeholder', 'notes',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'is_eliminated'     => 'boolean',
        'is_placeholder'    => 'boolean',
        'registration_date' => 'date',
        'approved_date'     => 'date',
    ];

    public function league()
    {
        return $this->belongsTo(LeagueLeague::class, 'league_id');
    }

    public function pointsEntry()
    {
        return $this->hasOne(LeaguePointsTable::class, 'league_team_id');
    }
}

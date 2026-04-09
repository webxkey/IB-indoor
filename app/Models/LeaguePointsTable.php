<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LeaguePointsTable extends Model
{
    use HasUuids;

    protected $table = 'league_leaguepointstable';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'league_id', 'league_team_id', 'group_id',
        'total_matches', 'matches_played', 'wins', 'losses', 'draws', 'ties', 'no_results',
        'points', 'bonus_points', 'penalty_points', 'rank', 'previous_rank',
        'recent_form', 'net_run_rate', 'total_runs_scored', 'total_runs_conceded',
        'total_overs_faced', 'total_overs_bowled', 'is_eliminated', 'is_qualified',
        'qualification_status', 'last_match_date',
    ];

    protected $casts = [
        'recent_form'         => 'array',
        'sport_specific_stats'=> 'array',
        'is_eliminated'       => 'boolean',
        'is_qualified'        => 'boolean',
    ];

    public function league()
    {
        return $this->belongsTo(LeagueLeague::class, 'league_id');
    }

    public function leagueTeam()
    {
        return $this->belongsTo(LeagueTeam::class, 'league_team_id');
    }
}

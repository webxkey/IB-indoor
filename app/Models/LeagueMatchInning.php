<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LeagueMatchInning extends Model
{
    use HasUuids;

    protected $table = 'league_matchinning';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'match_id', 'batting_team_id', 'bowling_team_id', 'inning_number',
        'total_runs', 'wickets_fallen', 'overs_bowled', 'balls_bowled',
        'extras_total', 'wides', 'no_balls', 'byes', 'leg_byes', 'penalty_runs',
        'is_completed', 'is_declared', 'is_forfeited',
        'run_rate', 'required_run_rate', 'target', 'started_at', 'ended_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'is_declared'  => 'boolean',
        'is_forfeited' => 'boolean',
    ];

    public function match()
    {
        return $this->belongsTo(LeagueMatch::class, 'match_id');
    }

    public function battingTeam()
    {
        return $this->belongsTo(LeagueTeam::class, 'batting_team_id');
    }

    public function bowlingTeam()
    {
        return $this->belongsTo(LeagueTeam::class, 'bowling_team_id');
    }

    public function getScoreStringAttribute(): string
    {
        return $this->total_runs . '/' . $this->wickets_fallen . ' (' . $this->overs_bowled . ' ov)';
    }
}

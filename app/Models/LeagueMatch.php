<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LeagueMatch extends Model
{
    use HasUuids;

    protected $table = 'league_match';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'league_id', 'team1_id', 'team2_id', 'winner_id', 'toss_winner_id',
        'venue', 'match_number', 'match_name', 'match_type',
        'scheduled_date', 'scheduled_time', 'actual_start_time', 'actual_end_time',
        'status', 'result', 'margin', 'toss_decision', 'summary', 'notes',
        'is_featured',
    ];

    protected $casts = [
        'scheduled_date'   => 'date',
        'is_featured'      => 'boolean',
        'had_tiebreaker'   => 'boolean',
        'tiebreaker_details' => 'array',
        'highlights'       => 'array',
        'weather_conditions' => 'array',
    ];

    public function league()
    {
        return $this->belongsTo(LeagueLeague::class, 'league_id');
    }

    public function team1()
    {
        return $this->belongsTo(LeagueTeam::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(LeagueTeam::class, 'team2_id');
    }

    public function winner()
    {
        return $this->belongsTo(LeagueTeam::class, 'winner_id');
    }

    public function innings()
    {
        return $this->hasMany(LeagueMatchInning::class, 'match_id')->orderBy('inning_number');
    }
}

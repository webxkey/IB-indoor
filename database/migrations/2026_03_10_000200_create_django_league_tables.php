<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('league_league')) {
            Schema::create('league_league', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name')->index();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('sport_type', 30)->index();
                $table->string('format', 30)->nullable();
                $table->string('logo')->nullable();
                $table->string('banner')->nullable();
                $table->string('theme_color', 7)->nullable();
                $table->integer('game_duration')->nullable();
                $table->json('cricket_config')->nullable();
                $table->json('sport_config')->nullable();
                $table->integer('num_teams')->default(0);
                $table->integer('squad_size')->default(0);
                $table->integer('min_players')->default(0);
                $table->integer('season_year')->nullable();
                $table->integer('season_number')->nullable();
                $table->string('season_name')->nullable();
                $table->timestamp('registration_start')->nullable();
                $table->timestamp('registration_end')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->decimal('prize_pool', 12, 2)->nullable();
                $table->json('sponsors')->nullable();
                $table->string('rules_document')->nullable();
                $table->text('rules_text')->nullable();
                $table->string('status', 30)->default('draft')->index();
                $table->boolean('is_active')->default(true)->index();
                $table->boolean('is_public')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->boolean('allow_draws')->default(false);
                $table->boolean('enable_tiebreaker')->default(false);
                $table->json('tiebreaker_config')->nullable();
                $table->integer('total_matches_planned')->default(0);
                $table->integer('matches_completed')->default(0);
                $table->string('website')->nullable();
                $table->json('social_links')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone', 30)->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->nullable()->index();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->unsignedBigInteger('admin_team_id')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->string('primary_venue')->nullable();
                $table->unsignedBigInteger('winner_id')->nullable();
                $table->unsignedBigInteger('runner_up_id')->nullable();
            });
        }

        if (!Schema::hasTable('league_league_venues')) {
            Schema::create('league_league_venues', function (Blueprint $table) {
                $table->id();
                $table->uuid('league_id');
                $table->unsignedBigInteger('venue_id');
                $table->unique(['league_id', 'venue_id']);
            });
        }

        if (!Schema::hasTable('league_league_moderators')) {
            Schema::create('league_league_moderators', function (Blueprint $table) {
                $table->id();
                $table->uuid('league_id');
                $table->unsignedBigInteger('user_id');
                $table->unique(['league_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('league_leaguegroup')) {
            Schema::create('league_leaguegroup', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('league_id');
                $table->string('name');
                $table->unsignedInteger('order')->default(0);
                $table->unsignedInteger('teams_to_qualify')->default(0);
                $table->timestamps();
                $table->unique(['league_id', 'name']);
            });
        }

        if (!Schema::hasTable('league_leagueteam')) {
            Schema::create('league_leagueteam', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('league_id');
                $table->uuid('group_id')->nullable();
                $table->unsignedBigInteger('team_id')->nullable();
                $table->unsignedBigInteger('captain_id')->nullable();
                $table->unsignedBigInteger('vice_captain_id')->nullable();
                $table->string('team_name_override')->nullable();
                $table->string('team_short_name')->nullable();
                $table->date('registration_date')->nullable();
                $table->date('approved_date')->nullable();
                $table->string('status', 30)->default('pending')->index();
                $table->string('jersey_color', 30)->nullable();
                $table->string('team_logo_override')->nullable();
                $table->integer('previous_position')->nullable();
                $table->integer('titles_won')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_eliminated')->default(false);
                $table->timestamp('eliminated_at')->nullable();
                $table->text('elimination_reason')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedInteger('slot_number')->nullable();
                $table->boolean('is_placeholder')->default(false);
                $table->timestamps();
                $table->index(['league_id', 'slot_number']);
            });
        }

        if (!Schema::hasTable('league_leagueteam_co_admins')) {
            Schema::create('league_leagueteam_co_admins', function (Blueprint $table) {
                $table->id();
                $table->uuid('leagueteam_id');
                $table->unsignedBigInteger('user_id');
                $table->unique(['leagueteam_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('league_leaguesquadmember')) {
            Schema::create('league_leaguesquadmember', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('league_team_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedInteger('jersey_number')->nullable();
                $table->string('role', 30)->nullable();
                $table->json('sport_specific_role')->nullable();
                $table->string('playing_position', 50)->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_available')->default(true);
                $table->text('unavailable_reason')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('league_leaguestatistics')) {
            Schema::create('league_leaguestatistics', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('league_id');
                $table->unsignedBigInteger('leader_user_id')->nullable();
                $table->uuid('leader_team_id')->nullable();
                $table->string('category', 50);
                $table->string('stat_name');
                $table->string('stat_type', 30)->nullable();
                $table->decimal('stat_value', 12, 2)->default(0);
                $table->decimal('stat_value_secondary', 12, 2)->nullable();
                $table->integer('matches_involved')->default(0);
                $table->text('description')->nullable();
                $table->json('top_performers')->nullable();
                $table->timestamp('last_updated')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('league_leaguepointstable')) {
            Schema::create('league_leaguepointstable', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('league_id');
                $table->uuid('group_id')->nullable();
                $table->uuid('league_team_id')->unique();
                $table->integer('total_matches')->default(0);
                $table->integer('matches_played')->default(0);
                $table->integer('wins')->default(0);
                $table->integer('losses')->default(0);
                $table->integer('draws')->default(0);
                $table->integer('ties')->default(0);
                $table->integer('no_results')->default(0);
                $table->decimal('points', 8, 2)->default(0);
                $table->decimal('bonus_points', 8, 2)->default(0);
                $table->decimal('penalty_points', 8, 2)->default(0);
                $table->unsignedInteger('rank')->default(0)->index();
                $table->unsignedInteger('previous_rank')->nullable();
                $table->json('recent_form')->nullable();
                $table->decimal('net_run_rate', 8, 3)->nullable();
                $table->integer('total_runs_scored')->default(0);
                $table->integer('total_runs_conceded')->default(0);
                $table->decimal('total_overs_faced', 8, 1)->default(0);
                $table->decimal('total_overs_bowled', 8, 1)->default(0);
                $table->integer('goals_for')->default(0);
                $table->integer('goals_against')->default(0);
                $table->integer('goal_difference')->default(0);
                $table->integer('sets_won')->default(0);
                $table->integer('sets_lost')->default(0);
                $table->json('sport_specific_stats')->nullable();
                $table->boolean('is_eliminated')->default(false);
                $table->boolean('is_qualified')->default(false);
                $table->string('qualification_status', 50)->nullable();
                $table->date('last_match_date')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('league_match')) {
            Schema::create('league_match', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('league_id');
                $table->uuid('group_id')->nullable();
                $table->uuid('team1_id');
                $table->uuid('team2_id');
                $table->uuid('winner_id')->nullable();
                $table->uuid('tiebreaker_winner_id')->nullable();
                $table->uuid('toss_winner_id')->nullable();
                $table->unsignedBigInteger('man_of_match_id')->nullable();
                $table->unsignedBigInteger('referee_id')->nullable();
                $table->string('venue')->nullable();
                $table->integer('match_number')->nullable();
                $table->string('match_name')->nullable();
                $table->string('match_type', 30)->nullable();
                $table->date('scheduled_date')->nullable()->index();
                $table->time('scheduled_time')->nullable();
                $table->timestamp('actual_start_time')->nullable();
                $table->timestamp('actual_end_time')->nullable();
                $table->string('status', 30)->default('scheduled')->index();
                $table->string('result', 30)->nullable();
                $table->string('margin')->nullable();
                $table->boolean('had_tiebreaker')->default(false);
                $table->json('tiebreaker_details')->nullable();
                $table->string('toss_decision', 10)->nullable();
                $table->text('summary')->nullable();
                $table->json('highlights')->nullable();
                $table->integer('attendance')->nullable();
                $table->string('live_stream_url')->nullable();
                $table->string('recording_url')->nullable();
                $table->json('weather_conditions')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('league_match_umpires')) {
            Schema::create('league_match_umpires', function (Blueprint $table) {
                $table->id();
                $table->uuid('match_id');
                $table->unsignedBigInteger('user_id');
                $table->unique(['match_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('league_livematchstate')) {
            Schema::create('league_livematchstate', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('match_id')->unique();
                $table->uuid('current_inning_id')->nullable();
                $table->unsignedBigInteger('current_bowler_id')->nullable();
                $table->unsignedBigInteger('striker_id')->nullable();
                $table->unsignedBigInteger('non_striker_id')->nullable();
                $table->integer('current_over')->nullable();
                $table->integer('current_ball')->nullable();
                $table->integer('team1_current_score')->default(0);
                $table->integer('team2_current_score')->default(0);
                $table->boolean('is_live')->default(false)->index();
                $table->boolean('is_break')->default(false);
                $table->string('break_reason')->nullable();
                $table->text('last_commentary')->nullable();
                $table->timestamp('last_ball_at')->nullable();
                $table->boolean('is_free_hit')->default(false);
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('league_matchinning')) {
            Schema::create('league_matchinning', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('match_id');
                $table->uuid('batting_team_id');
                $table->uuid('bowling_team_id');
                $table->integer('inning_number');
                $table->integer('total_runs')->default(0);
                $table->integer('wickets_fallen')->default(0);
                $table->decimal('overs_bowled', 6, 1)->default(0);
                $table->integer('balls_bowled')->default(0);
                $table->integer('extras_total')->default(0);
                $table->integer('wides')->default(0);
                $table->integer('no_balls')->default(0);
                $table->integer('byes')->default(0);
                $table->integer('leg_byes')->default(0);
                $table->integer('penalty_runs')->default(0);
                $table->boolean('is_completed')->default(false);
                $table->boolean('is_declared')->default(false);
                $table->boolean('is_forfeited')->default(false);
                $table->decimal('run_rate', 6, 2)->default(0);
                $table->decimal('required_run_rate', 6, 2)->nullable();
                $table->integer('target')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
            });
        }

        if (!Schema::hasTable('league_ballbyball')) {
            Schema::create('league_ballbyball', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('match_id');
                $table->uuid('inning_id');
                $table->unsignedBigInteger('batsman_striker_id');
                $table->unsignedBigInteger('batsman_non_striker_id');
                $table->unsignedBigInteger('bowler_id');
                $table->unsignedBigInteger('dismissed_batsman_id')->nullable();
                $table->unsignedBigInteger('fielder_id')->nullable();
                $table->unsignedBigInteger('secondary_fielder_id')->nullable();
                $table->integer('over_number');
                $table->integer('ball_number');
                $table->integer('ball_sequence');
                $table->string('ball_type', 30)->nullable();
                $table->integer('runs_scored')->default(0);
                $table->integer('runs_extras')->default(0);
                $table->integer('total_runs')->default(0);
                $table->boolean('is_wicket')->default(false);
                $table->string('wicket_type', 30)->nullable();
                $table->string('shot_played')->nullable();
                $table->boolean('is_boundary')->default(false);
                $table->boolean('is_six')->default(false);
                $table->boolean('is_four')->default(false);
                $table->text('commentary')->nullable();
                $table->string('video_timestamp')->nullable();
                $table->timestamp('bowled_at')->nullable();
                $table->unique(['inning_id', 'ball_sequence']);
            });
        }

        if (!Schema::hasTable('league_oversummary')) {
            Schema::create('league_oversummary', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('match_id');
                $table->uuid('inning_id');
                $table->unsignedBigInteger('bowler_id');
                $table->integer('over_number');
                $table->integer('runs_conceded')->default(0);
                $table->integer('wickets_taken')->default(0);
                $table->integer('dots')->default(0);
                $table->integer('fours')->default(0);
                $table->integer('sixes')->default(0);
                $table->integer('wides')->default(0);
                $table->integer('no_balls')->default(0);
                $table->boolean('is_maiden')->default(false);
                $table->json('ball_sequence')->nullable();
                $table->timestamp('bowled_at')->nullable();
            });
        }

        if (!Schema::hasTable('league_partnership')) {
            Schema::create('league_partnership', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('match_id');
                $table->uuid('inning_id');
                $table->unsignedBigInteger('batsman1_id');
                $table->unsignedBigInteger('batsman2_id');
                $table->integer('wicket_number')->default(0);
                $table->integer('runs')->default(0);
                $table->integer('balls')->default(0);
                $table->integer('batsman1_runs')->default(0);
                $table->integer('batsman2_runs')->default(0);
                $table->integer('batsman1_balls')->default(0);
                $table->integer('batsman2_balls')->default(0);
                $table->boolean('is_ongoing')->default(false);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('league_partnership');
        Schema::dropIfExists('league_oversummary');
        Schema::dropIfExists('league_ballbyball');
        Schema::dropIfExists('league_matchinning');
        Schema::dropIfExists('league_livematchstate');
        Schema::dropIfExists('league_match_umpires');
        Schema::dropIfExists('league_match');
        Schema::dropIfExists('league_leaguepointstable');
        Schema::dropIfExists('league_leaguestatistics');
        Schema::dropIfExists('league_leaguesquadmember');
        Schema::dropIfExists('league_leagueteam_co_admins');
        Schema::dropIfExists('league_leagueteam');
        Schema::dropIfExists('league_leaguegroup');
        Schema::dropIfExists('league_league_moderators');
        Schema::dropIfExists('league_league_venues');
        Schema::dropIfExists('league_league');
    }
};

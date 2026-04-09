<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LeagueLeague;
use App\Models\LeagueTeam;
use App\Models\LeagueMatch;
use App\Models\LeagueMatchInning;
use App\Models\LeaguePointsTable;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TournamentManagement extends Component
{
    use WithPagination;

    // ── View state ──────────────────────────────────────────────────────────────
    public string  $view                 = 'list';
    public ?string $selectedTournamentId = null;
    public ?string $selectedMatchId      = null;
    public string  $fixturesTab          = 'fixtures'; // fixtures | points | teams

    // ── Tournament form ─────────────────────────────────────────────────────────
    public string $form_name         = '';
    public string $form_description  = '';
    public string $form_format       = 'round_robin';
    public string $form_season_year  = '';
    public string $form_season_name  = '';
    public string $form_start_date   = '';
    public string $form_end_date     = '';
    public string $form_num_teams    = '8';
    public string $form_overs        = '20';
    public string $form_contact_email = '';
    public string $form_contact_phone = '';
    public string $form_status       = 'draft';
    public string $form_prize_pool   = '';

    // ── Team form ───────────────────────────────────────────────────────────────
    public string $team_name         = '';
    public string $team_short_name   = '';
    public string $team_jersey_color = '#19722d';

    // ── Score entry ─────────────────────────────────────────────────────────────
    public string $score_inning       = '1';
    public string $score_batting_team = '';
    public string $score_runs         = '';
    public string $score_wickets      = '0';
    public string $score_overs        = '';
    public string $score_wides        = '0';
    public string $score_no_balls     = '0';
    public string $score_byes         = '0';
    public string $score_leg_byes     = '0';
    public bool   $score_is_completed = false;
    public string $match_status       = 'scheduled';
    public string $match_result       = '';
    public string $match_margin       = '';
    public string $match_winner_id    = '';
    public string $match_toss_winner_id = '';
    public string $match_toss_decision  = '';

    public string $search = '';

    // ── Validation ──────────────────────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'form_name'       => 'required|string|min:3|max:200',
            'form_format'     => 'required|in:round_robin,knockout,group_knockout,league,bilateral',
            'form_season_year'=> 'nullable|integer|min:2020|max:2035',
            'form_start_date' => 'nullable|date',
            'form_end_date'   => 'nullable|date|after_or_equal:form_start_date',
            'form_num_teams'  => 'required|integer|min:2|max:32',
            'form_overs'      => 'required|integer|min:1|max:100',
            'form_status'     => 'required|in:draft,published,active,completed,cancelled',
        ];
    }

    public function mount(): void
    {
        $this->form_season_year = date('Y');
        $this->form_start_date  = date('Y-m-d');
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  NAVIGATION
    // ══════════════════════════════════════════════════════════════════════════

    public function showCreate(): void
    {
        $this->resetForm();
        $this->view = 'create';
    }

    public function showDetail(string $id): void
    {
        $this->selectedTournamentId = $id;
        $this->fixturesTab          = 'fixtures';
        $this->view                 = 'detail';
    }

    public function showAddTeam(): void
    {
        $this->team_name         = '';
        $this->team_short_name   = '';
        $this->team_jersey_color = '#19722d';
        $this->view              = 'add_team';
    }

    public function showScoreboard(string $matchId): void
    {
        $this->selectedMatchId = $matchId;
        $this->resetScoreForm($matchId);
        $this->view = 'scoreboard';
    }

    public function backToList(): void
    {
        $this->view                 = 'list';
        $this->selectedTournamentId = null;
        $this->selectedMatchId      = null;
        $this->resetPage();
    }

    public function backToDetail(): void
    {
        $this->view            = 'detail';
        $this->selectedMatchId = null;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  CREATE TOURNAMENT
    // ══════════════════════════════════════════════════════════════════════════

    public function createTournament(): void
    {
        $this->validate();

        $slug = Str::slug($this->form_name) . '-' . time();

        $tournament = LeagueLeague::create([
            'id'             => (string) Str::uuid(),
            'name'           => $this->form_name,
            'slug'           => $slug,
            'description'    => $this->form_description ?: null,
            'sport_type'     => 'cricket',
            'format'         => $this->form_format,
            'season_year'    => $this->form_season_year ?: null,
            'season_name'    => $this->form_season_name ?: null,
            'start_date'     => $this->form_start_date ?: null,
            'end_date'       => $this->form_end_date ?: null,
            'num_teams'      => (int) $this->form_num_teams,
            'status'         => $this->form_status,
            'prize_pool'     => $this->form_prize_pool ? (float) $this->form_prize_pool : null,
            'contact_email'  => $this->form_contact_email ?: null,
            'contact_phone'  => $this->form_contact_phone ?: null,
            'cricket_config' => ['overs' => (int) $this->form_overs],
            'is_active'      => true,
            'is_public'      => true,
            'created_at'     => now(),
            'updated_at'     => now(),
            'created_by_id'  => auth()->id(),
        ]);

        session()->flash('success', 'Tournament "' . $tournament->name . '" created! Now add teams.');
        $this->selectedTournamentId = $tournament->id;
        $this->view                 = 'detail';
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  ADD TEAM
    // ══════════════════════════════════════════════════════════════════════════

    public function addTeam(): void
    {
        $this->validate([
            'team_name'       => 'required|string|min:2|max:100',
            'team_short_name' => 'nullable|string|max:10',
        ]);

        $team = LeagueTeam::create([
            'id'                 => (string) Str::uuid(),
            'league_id'          => $this->selectedTournamentId,
            'team_name_override' => $this->team_name,
            'team_short_name'    => strtoupper($this->team_short_name ?: substr($this->team_name, 0, 3)),
            'jersey_color'       => $this->team_jersey_color ?: '#19722d',
            'status'             => 'approved',
            'is_active'          => true,
            'registration_date'  => now()->toDateString(),
            'approved_date'      => now()->toDateString(),
        ]);

        LeaguePointsTable::create([
            'id'             => (string) Str::uuid(),
            'league_id'      => $this->selectedTournamentId,
            'league_team_id' => $team->id,
            'rank'           => 0,
        ]);

        session()->flash('success', 'Team "' . $this->team_name . '" added!');
        $this->view = 'detail';
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  IPL FIXTURE GENERATION
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Generate full fixture list using IPL round-robin pattern.
     * - League phase: every team plays every other team twice (home & away)
     *   Implemented via the "circle / round-robin rotation" algorithm.
     * - Playoffs phase (if ≥ 4 teams): Qualifier 1, Eliminator, Qualifier 2, Final
     * - Matches spread 1 per day from tournament start_date, with 2-day gaps before playoffs.
     * - Each day's match gets a realistic time slot (14:00 or 19:30 like IPL).
     */
    public function generateFixtures(): void
    {
        $tournament = LeagueLeague::with('teams')->find($this->selectedTournamentId);
        if (!$tournament) return;

        $teams = $tournament->teams->where('is_active', true)->where('is_placeholder', false)->values();
        $n     = $teams->count();

        if ($n < 2) {
            session()->flash('error', 'Add at least 2 teams before generating fixtures.');
            return;
        }

        // ── Wipe existing scheduled (unplayed) matches ──────────────────────
        LeagueMatch::where('league_id', $this->selectedTournamentId)
            ->where('status', 'scheduled')
            ->each(function ($m) {
                LeagueMatchInning::where('match_id', $m->id)->delete();
                $m->delete();
            });

        // ── Build round-robin rounds (circle method) ────────────────────────
        $ids = $teams->pluck('id')->toArray();
        if ($n % 2 !== 0) {
            $ids[] = null; // bye slot for odd number of teams
        }
        $total  = count($ids);
        $half   = $total / 2;
        $rounds = [];

        for ($r = 0; $r < $total - 1; $r++) {
            $roundPairs = [];
            for ($i = 0; $i < $half; $i++) {
                $t1 = $ids[$i];
                $t2 = $ids[$total - 1 - $i];
                if ($t1 !== null && $t2 !== null) {
                    $roundPairs[] = [$t1, $t2];
                }
            }
            if (!empty($roundPairs)) {
                $rounds[] = $roundPairs;
            }
            // Rotate: keep first fixed, shift rest left (clockwise)
            $last  = array_pop($ids);
            array_splice($ids, 1, 0, [$last]);
        }

        // ── IPL = double round-robin: add the reverse leg ───────────────────
        $reversedRounds = array_map(
            fn($round) => array_map(fn($pair) => [$pair[1], $pair[0]], $round),
            $rounds
        );
        $allRounds = array_merge($rounds, $reversedRounds);

        // ── Schedule league matches (1 per day, alternate 14:00 / 19:30) ────
        $startDate   = $tournament->start_date ? Carbon::parse($tournament->start_date) : Carbon::today();
        $matchNumber = 1;
        $dayOffset   = 0;
        $times       = ['14:00', '19:30'];

        foreach ($allRounds as $roundIdx => $roundPairs) {
            foreach ($roundPairs as $pairIdx => [$team1Id, $team2Id]) {
                $matchDate = $startDate->copy()->addDays($dayOffset);
                $time      = $times[$matchNumber % 2]; // alternate day/evening

                LeagueMatch::create([
                    'id'             => (string) Str::uuid(),
                    'league_id'      => $this->selectedTournamentId,
                    'team1_id'       => $team1Id,
                    'team2_id'       => $team2Id,
                    'match_number'   => $matchNumber,
                    'match_name'     => 'Match ' . $matchNumber,
                    'match_type'     => 'league',
                    'scheduled_date' => $matchDate->toDateString(),
                    'scheduled_time' => $time,
                    'status'         => 'scheduled',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                $matchNumber++;
                $dayOffset++; // 1 match per calendar day
            }
        }

        $leagueMatchCount = $matchNumber - 1;

        // ── Playoff phase (IPL-style) ────────────────────────────────────────
        $playoffCount = 0;
        if ($n >= 4) {
            // 2-day buffer between league and playoffs
            $dayOffset += 2;

            // Placeholder team IDs (first 4 teams as stand-ins until league ends)
            $p1 = $teams[0]->id; // 1st place placeholder
            $p2 = $teams[1]->id; // 2nd place placeholder
            $p3 = $teams[2]->id; // 3rd place placeholder
            $p4 = $teams[3]->id; // 4th place placeholder

            $playoffs = [
                [
                    'name'   => 'Qualifier 1 — 1st vs 2nd',
                    'type'   => 'qualifier_1',
                    'team1'  => $p1,
                    'team2'  => $p2,
                    'days'   => 0,
                ],
                [
                    'name'   => 'Eliminator — 3rd vs 4th',
                    'type'   => 'eliminator',
                    'team1'  => $p3,
                    'team2'  => $p4,
                    'days'   => 2,
                ],
                [
                    'name'   => 'Qualifier 2 — Loser Q1 vs Winner Eliminator',
                    'type'   => 'qualifier_2',
                    'team1'  => $p2,
                    'team2'  => $p3,
                    'days'   => 4,
                ],
                [
                    'name'   => 'FINAL',
                    'type'   => 'final',
                    'team1'  => $p1,
                    'team2'  => $p2,
                    'days'   => 7,
                ],
            ];

            foreach ($playoffs as $playoff) {
                $matchDate = $startDate->copy()->addDays($dayOffset + $playoff['days']);

                LeagueMatch::create([
                    'id'             => (string) Str::uuid(),
                    'league_id'      => $this->selectedTournamentId,
                    'team1_id'       => $playoff['team1'],
                    'team2_id'       => $playoff['team2'],
                    'match_number'   => $matchNumber,
                    'match_name'     => $playoff['name'],
                    'match_type'     => $playoff['type'],
                    'scheduled_date' => $matchDate->toDateString(),
                    'scheduled_time' => '19:30',
                    'status'         => 'scheduled',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                $matchNumber++;
                $playoffCount++;
            }
        }

        $totalMatches = $leagueMatchCount + $playoffCount;
        $tournament->update(['total_matches_planned' => $totalMatches]);

        session()->flash('success',
            "Generated {$totalMatches} matches: {$leagueMatchCount} league" .
            ($playoffCount ? " + {$playoffCount} playoffs (Q1, Eliminator, Q2, Final)" : '') . '.'
        );

        $this->fixturesTab = 'fixtures';
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  SCORE ENTRY
    // ══════════════════════════════════════════════════════════════════════════

    private function resetScoreForm(string $matchId): void
    {
        $match = LeagueMatch::find($matchId);
        if (!$match) return;

        $this->score_inning         = '1';
        $this->score_batting_team   = $match->team1_id;
        $this->score_runs           = '';
        $this->score_wickets        = '0';
        $this->score_overs          = '';
        $this->score_wides          = '0';
        $this->score_no_balls       = '0';
        $this->score_byes           = '0';
        $this->score_leg_byes       = '0';
        $this->score_is_completed   = false;
        $this->match_status         = $match->status ?? 'scheduled';
        $this->match_result         = $match->result ?? '';
        $this->match_margin         = $match->margin ?? '';
        $this->match_winner_id      = $match->winner_id ?? '';
        $this->match_toss_winner_id = $match->toss_winner_id ?? '';
        $this->match_toss_decision  = $match->toss_decision ?? '';
    }

    public function saveScore(): void
    {
        $this->validate([
            'score_batting_team' => 'required|string',
            'score_inning'       => 'required|integer|min:1|max:2',
            'score_runs'         => 'required|integer|min:0',
            'score_wickets'      => 'required|integer|min:0|max:10',
            'score_overs'        => 'required|numeric|min:0',
            'match_status'       => 'required|string',
        ]);

        $match = LeagueMatch::find($this->selectedMatchId);
        if (!$match) return;

        $battingTeamId = $this->score_batting_team;
        $bowlingTeamId = ($match->team1_id === $battingTeamId) ? $match->team2_id : $match->team1_id;
        $inningNumber  = (int) $this->score_inning;
        $extrasTotal   = (int)$this->score_wides + (int)$this->score_no_balls + (int)$this->score_byes + (int)$this->score_leg_byes;

        $data = [
            'match_id'        => $this->selectedMatchId,
            'batting_team_id' => $battingTeamId,
            'bowling_team_id' => $bowlingTeamId,
            'inning_number'   => $inningNumber,
            'total_runs'      => (int) $this->score_runs,
            'wickets_fallen'  => (int) $this->score_wickets,
            'overs_bowled'    => (float) $this->score_overs,
            'extras_total'    => $extrasTotal,
            'wides'           => (int) $this->score_wides,
            'no_balls'        => (int) $this->score_no_balls,
            'byes'            => (int) $this->score_byes,
            'leg_byes'        => (int) $this->score_leg_byes,
            'is_completed'    => $this->score_is_completed,
        ];

        $inning = LeagueMatchInning::where('match_id', $this->selectedMatchId)
            ->where('inning_number', $inningNumber)->first();

        if ($inning) {
            $inning->update($data);
        } else {
            LeagueMatchInning::create(array_merge(['id' => (string) Str::uuid()], $data));
        }

        $match->update([
            'status'         => $this->match_status,
            'result'         => $this->match_result ?: null,
            'margin'         => $this->match_margin ?: null,
            'winner_id'      => $this->match_winner_id ?: null,
            'toss_winner_id' => $this->match_toss_winner_id ?: null,
            'toss_decision'  => $this->match_toss_decision ?: null,
        ]);

        if ($this->match_status === 'completed') {
            $this->updatePointsTable($match);
            $match->league()->increment('matches_completed');
        }

        session()->flash('success', 'Score saved!');
        $this->view = 'detail';
    }

    private function updatePointsTable(LeagueMatch $match): void
    {
        foreach ([$match->team1_id, $match->team2_id] as $teamId) {
            $entry = LeaguePointsTable::where('league_id', $match->league_id)
                ->where('league_team_id', $teamId)->first();
            if (!$entry) continue;

            $entry->increment('matches_played');

            if ($match->winner_id === $teamId) {
                $entry->increment('wins');
                $entry->increment('points', 2);
            } elseif ($match->winner_id && $match->winner_id !== $teamId) {
                $entry->increment('losses');
            } else {
                $entry->increment('no_results');
                $entry->increment('points', 1);
            }
        }

        // Recalculate NRR for both teams
        $this->recalculateNRR($match->league_id, $match->team1_id);
        $this->recalculateNRR($match->league_id, $match->team2_id);

        // Re-rank all
        $entries = LeaguePointsTable::where('league_id', $match->league_id)
            ->orderByDesc('points')
            ->orderByDesc('wins')
            ->orderByDesc('net_run_rate')
            ->get();

        foreach ($entries as $i => $entry) {
            $entry->update(['rank' => $i + 1]);
        }
    }

    private function recalculateNRR(string $leagueId, string $teamId): void
    {
        $innings = LeagueMatchInning::join('league_match', 'league_matchinning.match_id', '=', 'league_match.id')
            ->where('league_match.league_id', $leagueId)
            ->where('league_match.status', 'completed')
            ->where(function ($q) use ($teamId) {
                $q->where('league_matchinning.batting_team_id', $teamId)
                  ->orWhere('league_matchinning.bowling_team_id', $teamId);
            })
            ->select('league_matchinning.*')
            ->get();

        $runsScored    = 0;
        $oversFaced    = 0.0;
        $runsConceded  = 0;
        $oversBowled   = 0.0;

        foreach ($innings as $inn) {
            if ((string)$inn->batting_team_id === $teamId) {
                $runsScored += $inn->total_runs;
                $oversFaced += (float)$inn->overs_bowled;
            } else {
                $runsConceded += $inn->total_runs;
                $oversBowled  += (float)$inn->overs_bowled;
            }
        }

        $nrr = 0;
        if ($oversFaced > 0 && $oversBowled > 0) {
            $nrr = ($runsScored / $oversFaced) - ($runsConceded / $oversBowled);
        }

        LeaguePointsTable::where('league_id', $leagueId)
            ->where('league_team_id', $teamId)
            ->update([
                'net_run_rate'        => round($nrr, 3),
                'total_runs_scored'   => $runsScored,
                'total_runs_conceded' => $runsConceded,
                'total_overs_faced'   => $oversFaced,
                'total_overs_bowled'  => $oversBowled,
            ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  DELETE
    // ══════════════════════════════════════════════════════════════════════════

    public function deleteTournament(string $id): void
    {
        $t = LeagueLeague::find($id);
        if ($t) {
            LeagueMatch::where('league_id', $id)->each(function ($m) {
                LeagueMatchInning::where('match_id', $m->id)->delete();
                $m->delete();
            });
            LeaguePointsTable::where('league_id', $id)->delete();
            LeagueTeam::where('league_id', $id)->delete();
            $t->delete();
            session()->flash('success', 'Tournament deleted.');
        }
        $this->view                 = 'list';
        $this->selectedTournamentId = null;
    }

    public function deleteMatch(string $matchId): void
    {
        LeagueMatchInning::where('match_id', $matchId)->delete();
        LeagueMatch::destroy($matchId);
        session()->flash('success', 'Match deleted.');
    }

    public function deleteTeam(string $teamId): void
    {
        LeaguePointsTable::where('league_team_id', $teamId)->delete();
        LeagueTeam::destroy($teamId);
        session()->flash('success', 'Team removed.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    private function resetForm(): void
    {
        $this->form_name          = '';
        $this->form_description   = '';
        $this->form_format        = 'round_robin';
        $this->form_season_year   = date('Y');
        $this->form_season_name   = '';
        $this->form_start_date    = date('Y-m-d');
        $this->form_end_date      = '';
        $this->form_num_teams     = '8';
        $this->form_overs         = '20';
        $this->form_contact_email = '';
        $this->form_contact_phone = '';
        $this->form_status        = 'draft';
        $this->form_prize_pool    = '';
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  RENDER
    // ══════════════════════════════════════════════════════════════════════════

    public function render()
    {
        $tournaments = LeagueLeague::where('sport_type', 'cricket')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderByDesc('created_at')
            ->paginate(10);

        $selectedTournament = null;
        $groupedMatches     = collect();
        $pointsTable        = collect();

        if ($this->selectedTournamentId) {
            $selectedTournament = LeagueLeague::with([
                'teams',
                'matches' => fn($q) => $q->with(['team1', 'team2', 'winner', 'innings'])->orderBy('match_number'),
                'pointsTable.leagueTeam',
            ])->find($this->selectedTournamentId);

            if ($selectedTournament) {
                // Group matches: league rounds first, then playoffs
                $leagueMatches  = $selectedTournament->matches->where('match_type', 'league');
                $playoffMatches = $selectedTournament->matches->whereNotIn('match_type', ['league']);

                // Group league matches into "rounds" of N/2 matches each (IPL style weeks)
                $teamCount  = max($selectedTournament->teams->count(), 2);
                $perRound   = max(1, intdiv($teamCount, 2));
                $roundIndex = 1;
                $chunk      = collect();

                foreach ($leagueMatches as $m) {
                    $chunk->push($m);
                    if ($chunk->count() === $perRound) {
                        $groupedMatches->push(['label' => 'Round ' . $roundIndex, 'type' => 'league', 'matches' => $chunk]);
                        $chunk = collect();
                        $roundIndex++;
                    }
                }
                if ($chunk->isNotEmpty()) {
                    $groupedMatches->push(['label' => 'Round ' . $roundIndex, 'type' => 'league', 'matches' => $chunk]);
                }

                // Playoffs
                if ($playoffMatches->isNotEmpty()) {
                    $groupedMatches->push(['label' => 'Playoffs', 'type' => 'playoffs', 'matches' => $playoffMatches]);
                }

                $pointsTable = $selectedTournament->pointsTable->sortBy('rank');
            }
        }

        $selectedMatch = $this->selectedMatchId
            ? LeagueMatch::with(['team1', 'team2', 'winner', 'innings.battingTeam', 'innings.bowlingTeam'])
                ->find($this->selectedMatchId)
            : null;

        return view('livewire.staff.tournament-management', [
            'tournaments'        => $tournaments,
            'selectedTournament' => $selectedTournament,
            'groupedMatches'     => $groupedMatches,
            'pointsTable'        => $pointsTable,
            'selectedMatch'      => $selectedMatch,
        ])->layout('components.layouts.staff', ['title' => 'Tournament Management']);
    }
}

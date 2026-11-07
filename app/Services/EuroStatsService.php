<?php

namespace App\Services;

use App\Enums\EuroStage;
use App\Models\Club;
use App\Models\EuroCompetition;
use App\Models\EuroMatch;
use App\Models\EuroSeason;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EuroStatsService
{
    /**
     * All-time points in tables are always counted 3 per win, regardless of era.
     */
    const int ALL_TIME_POINTS_FOR_WIN = 3;

    /**
     * All-time table built from every match (penalty shoot-outs count as draws).
     * Without competition — across all European competitions.
     */
    public function allTimeTable(?EuroCompetition $competition = null): Collection
    {
        $perClub = DB::query()
            ->fromSub(
                DB::table('euro_matches')
                    ->select(['euro_season_id', 'home_club_id as club_id', 'home_goals as gf', 'away_goals as ga'])
                    ->unionAll(
                        DB::table('euro_matches')
                            ->select(['euro_season_id', 'away_club_id as club_id', 'away_goals as gf', 'home_goals as ga'])
                    ),
                'm'
            )
            ->when($competition, fn ($q) => $q
                ->join('euro_seasons as s', 's.id', '=', 'm.euro_season_id')
                ->where('s.euro_competition_id', $competition->id)
            )
            ->groupBy('m.club_id')
            ->selectRaw('
                m.club_id,
                COUNT(DISTINCT m.euro_season_id) as seasons,
                COUNT(*) as played,
                SUM(m.gf > m.ga) as won,
                SUM(m.gf = m.ga) as drawn,
                SUM(m.gf < m.ga) as lost,
                SUM(m.gf) as goals_for,
                SUM(m.ga) as goals_against
            ')
            ->get();

        $clubs = Club::query()
            ->with('attachment')
            ->whereIn('id', $perClub->pluck('club_id'))
            ->get(['id', 'name', 'slug'])
            ->keyBy('id');

        return $perClub
            ->map(fn ($row) => $this->tableRow(
                $clubs[$row->club_id],
                (int) $row->won,
                (int) $row->drawn,
                (int) $row->lost,
                (int) $row->goals_for,
                (int) $row->goals_against,
                self::ALL_TIME_POINTS_FOR_WIN,
            ) + ['seasons' => (int) $row->seasons])
            ->sortBy([
                ['points', 'desc'],
                ['goal_difference', 'desc'],
                ['goals_for', 'desc'],
            ])
            ->values();
    }

    /**
     * Totals of one season, computed from its loaded matches.
     *
     * @return array{clubs: int, matches: int, goals: int, goals_per_match: float}
     */
    public function seasonSummary(EuroSeason $season): array
    {
        $matches = $season->matches;
        $goals = $matches->sum(fn (EuroMatch $m) => $m->home_goals + $m->away_goals);
        $clubs = $matches->pluck('home_club_id')->merge($matches->pluck('away_club_id'))->unique()->count();

        return [
            'clubs' => $clubs,
            'matches' => $matches->count(),
            'goals' => $goals,
            'goals_per_match' => $matches->count() ? round($goals / $matches->count(), 2) : 0,
        ];
    }

    /**
     * Totals of a competition over all seasons (SQL aggregates).
     *
     * @return array{seasons: int, clubs: int, matches: int, goals: int}
     */
    public function competitionSummary(EuroCompetition $competition): array
    {
        $matches = EuroMatch::query()
            ->whereIn('euro_season_id', $competition->seasons()->select('id'));

        $totals = (clone $matches)
            ->selectRaw('COUNT(*) as matches, COALESCE(SUM(home_goals + away_goals), 0) as goals, COUNT(DISTINCT euro_season_id) as seasons')
            ->toBase()
            ->first();

        $clubs = DB::query()
            ->fromSub(
                (clone $matches)->select('home_club_id as club_id')
                    ->union((clone $matches)->select('away_club_id as club_id')),
                'c'
            )
            ->count();

        return [
            'seasons' => (int) $totals->seasons,
            'clubs' => $clubs,
            'matches' => (int) $totals->matches,
            'goals' => (int) $totals->goals,
        ];
    }

    /**
     * Winners / runners-up per club, like the domestic cup "All-Time" table.
     *
     * @param  Collection<int, EuroSeason>  $seasons  with winner + runnerUp loaded
     */
    public function finalsTable(Collection $seasons): Collection
    {
        $clubs = [];

        foreach ($seasons->sortBy('name') as $season) {
            foreach (['winner' => 'champions', 'runnerUp' => 'runnerups'] as $relation => $key) {
                if ($club = $season->{$relation}) {
                    $clubs[$club->id] ??= ['club' => $club, 'champions' => collect(), 'runnerups' => collect()];
                    $clubs[$club->id][$key]->push($season->name);
                }
            }
        }

        return collect($clubs)
            ->map(fn ($c) => [
                'club' => $c['club'],
                'champions' => ['count' => $c['champions']->count(), 'years' => $c['champions']->implode(', ')],
                'runnerups' => ['count' => $c['runnerups']->count(), 'years' => $c['runnerups']->implode(', ')],
            ])
            ->sortByDesc(fn ($c) => [$c['champions']['count'], $c['runnerups']['count']])
            ->values();
    }

    /**
     * Group tables of a season computed from its group-stage matches:
     * [stage value => [group => Collection<row>]].
     *
     * Tie-breakers: points, goal difference, goals scored.
     */
    public function groupTables(EuroSeason $season): Collection
    {
        $season->loadMissing(['matches.homeClub:id,name,slug', 'matches.awayClub:id,name,slug']);

        return $season->matches
            ->filter(fn (EuroMatch $m) => $m->stage->isGroup())
            ->groupBy([fn (EuroMatch $m) => $m->stage->value, fn (EuroMatch $m) => $m->group ?? ''])
            ->map(fn (Collection $groups) => $groups
                ->sortKeys()
                ->map(function (Collection $matches) use ($season) {
                    $table = $this->applyAdjustments(
                        $this->buildTable($matches, $season->points_for_win),
                        $season->point_adjustments ?? [],
                        $matches->first()->stage->value,
                    );

                    // league phase and single round-robin groups (UEFA Cup 2004-09): no head-to-head
                    $singleRoundRobin = $matches
                        ->countBy(fn (EuroMatch $m) => collect([$m->home_club_id, $m->away_club_id])->sort()->join('-'))
                        ->max() === 1;

                    return $matches->first()->stage === EuroStage::LEAGUE_PHASE || $singleRoundRobin
                        ? $this->rankLeague($table, $matches, $season->points_for_win)
                        : $this->rankGroup(
                            $table,
                            $matches,
                            $season->points_for_win,
                            // away goals stopped counting in head-to-head from 2021-22
                            $season->startYear() < 2021,
                        );
                }));
    }

    /**
     * League phase order (from 2024-25) and single round-robin groups, no head-to-head: points, goal difference, goals scored,
     * away goals, wins, away wins, then points, goal difference and goals of the opponents played.
     *
     * @param  Collection<int, EuroMatch>  $matches
     */
    protected function rankLeague(Collection $rows, Collection $matches, int $pointsForWin): Collection
    {
        $byId = $rows->keyBy(fn ($r) => $r['club']->id);
        $extra = [];

        foreach ($matches as $m) {
            $extra[$m->away_club_id]['away_goals'] = ($extra[$m->away_club_id]['away_goals'] ?? 0) + $m->away_goals;
            $extra[$m->away_club_id]['away_wins'] = ($extra[$m->away_club_id]['away_wins'] ?? 0) + (int) ($m->away_goals > $m->home_goals);
            $extra[$m->home_club_id]['opponents'][] = $m->away_club_id;
            $extra[$m->away_club_id]['opponents'][] = $m->home_club_id;
        }

        $opp = fn (int $id, string $key) => collect($extra[$id]['opponents'] ?? [])->sum(fn ($o) => $byId[$o][$key] ?? 0);
        $val = fn (array $r, string $key) => $extra[$r['club']->id][$key] ?? 0;

        return $rows->sortBy([
            fn ($a, $b) => $b['points'] <=> $a['points'],
            fn ($a, $b) => $b['goal_difference'] <=> $a['goal_difference'],
            fn ($a, $b) => $b['goals_for'] <=> $a['goals_for'],
            fn ($a, $b) => $val($b, 'away_goals') <=> $val($a, 'away_goals'),
            fn ($a, $b) => $b['won'] <=> $a['won'],
            fn ($a, $b) => $val($b, 'away_wins') <=> $val($a, 'away_wins'),
            fn ($a, $b) => $opp($b['club']->id, 'points') <=> $opp($a['club']->id, 'points'),
            fn ($a, $b) => $opp($b['club']->id, 'goal_difference') <=> $opp($a['club']->id, 'goal_difference'),
            fn ($a, $b) => $opp($b['club']->id, 'goals_for') <=> $opp($a['club']->id, 'goals_for'),
        ])->values();
    }

    /**
     * Group order: points, then head-to-head among the clubs level on points
     * (points, goal difference, goals, and until 2020-21 away goals in their mutual matches),
     * then overall goal difference, goals scored and away goals scored.
     *
     * @param  Collection<int, EuroMatch>  $matches
     */
    protected function rankGroup(Collection $rows, Collection $matches, int $pointsForWin, bool $h2hAwayGoals = true): Collection
    {
        // goals scored away in all group matches: the last tie-breaker
        $awayGoals = [];
        foreach ($matches as $m) {
            $awayGoals[$m->away_club_id] = ($awayGoals[$m->away_club_id] ?? 0) + $m->away_goals;
        }

        return $rows
            ->groupBy('points')
            ->sortKeysDesc()
            ->flatMap(function (Collection $level) use ($matches, $pointsForWin, $h2hAwayGoals, $awayGoals) {
                if ($level->count() === 1) {
                    return $level;
                }

                $ids = $level->map(fn ($r) => $r['club']->id)->all();
                $h2h = $this->headToHead(
                    $matches->filter(fn (EuroMatch $m) => in_array($m->home_club_id, $ids, true) && in_array($m->away_club_id, $ids, true)),
                    $pointsForWin,
                );

                return $level->sortBy([
                    fn ($a, $b) => ($h2h[$b['club']->id]['pts'] ?? 0) <=> ($h2h[$a['club']->id]['pts'] ?? 0),
                    fn ($a, $b) => ($h2h[$b['club']->id]['gd'] ?? 0) <=> ($h2h[$a['club']->id]['gd'] ?? 0),
                    fn ($a, $b) => ($h2h[$b['club']->id]['gf'] ?? 0) <=> ($h2h[$a['club']->id]['gf'] ?? 0),
                    fn ($a, $b) => $h2hAwayGoals ? ($h2h[$b['club']->id]['away'] ?? 0) <=> ($h2h[$a['club']->id]['away'] ?? 0) : 0,
                    fn ($a, $b) => $b['goal_difference'] <=> $a['goal_difference'],
                    fn ($a, $b) => $b['goals_for'] <=> $a['goals_for'],
                    fn ($a, $b) => ($awayGoals[$b['club']->id] ?? 0) <=> ($awayGoals[$a['club']->id] ?? 0),
                ]);
            })
            ->values();
    }

    /**
     * Mini-table of mutual matches: [club_id => [pts, gd, gf, away]].
     *
     * @param  Collection<int, EuroMatch>  $matches
     */
    protected function headToHead(Collection $matches, int $pointsForWin): array
    {
        $t = [];

        foreach ($matches as $m) {
            foreach ([[$m->home_club_id, $m->home_goals, $m->away_goals, false], [$m->away_club_id, $m->away_goals, $m->home_goals, true]] as [$id, $for, $against, $away]) {
                $t[$id] ??= ['pts' => 0, 'gd' => 0, 'gf' => 0, 'away' => 0];
                $t[$id]['pts'] += $for > $against ? $pointsForWin : ($for === $against ? 1 : 0);
                $t[$id]['gd'] += $for - $against;
                $t[$id]['gf'] += $for;
                $t[$id]['away'] += $away ? $for : 0;
            }
        }

        return $t;
    }

    /**
     * Point deductions / bonuses of a season (e.g. Milan −2 in 1994-95):
     * added to the club's points, with the reason kept for a footnote.
     */
    protected function applyAdjustments(Collection $rows, array $adjustments, string $stage): Collection
    {
        $adjustments = collect($adjustments)->where('stage', $stage)->keyBy('club_id');

        if ($adjustments->isEmpty()) {
            return $rows;
        }

        return $rows
            ->map(function (array $row) use ($adjustments) {
                if ($a = $adjustments->get($row['club']->id)) {
                    $row['points'] += $a['points'];
                    $row['adjustment'] = $a['points'];
                    $row['adjustment_note'] = $a['note'];
                }

                return $row;
            });
    }

    /**
     * Club's European record grouped by competition.
     */
    public function clubHistory(Club $club): Collection
    {
        // Only what the block needs: season + stage of every match the club played.
        $matches = EuroMatch::query()
            ->select(['euro_season_id', 'stage', 'played_at'])
            ->where(fn ($q) => $q->where('home_club_id', $club->id)->orWhere('away_club_id', $club->id))
            ->with([
                'season:id,euro_competition_id,name,winner_club_id',
                'season.competition:id,name,slug',
            ])
            ->get();

        $titles = $club->euroTitles()->get(['id', 'euro_competition_id', 'name'])->groupBy('euro_competition_id');
        $finals = $club->euroRunnerUps()->get(['id', 'euro_competition_id', 'name'])->groupBy('euro_competition_id');

        return $matches
            ->groupBy(fn (EuroMatch $m) => $m->season->euro_competition_id)
            ->map(function (Collection $matches, int $competitionId) use ($titles, $finals) {
                $seasons = $matches
                    ->groupBy('euro_season_id')
                    ->map(fn (Collection $sm) => [
                        'season' => $sm->first()->season,
                        // the stage of the club's last match that season
                        'best_stage' => $sm
                            ->sortBy([
                                fn ($a, $b) => $a->played_at <=> $b->played_at,
                                fn ($a, $b) => $a->stage->sortOrder() <=> $b->stage->sortOrder(),
                            ])
                            ->last()
                            ->stage,
                    ])
                    ->sortByDesc(fn ($s) => $s['season']->name)
                    ->values();

                return [
                    'competition' => $matches->first()->season->competition,
                    'titles' => $titles->get($competitionId, collect())->pluck('name')->sort()->values(),
                    'runner_ups' => $finals->get($competitionId, collect())->pluck('name')->sort()->values(),
                    'seasons' => $seasons,
                ];
            })
            ->sortByDesc(fn ($c) => [$c['titles']->count(), $c['seasons']->count()])
            ->values();
    }

    /**
     * @param  Collection<int, EuroMatch>  $matches
     */
    protected function buildTable(Collection $matches, int $pointsForWin): Collection
    {
        $stats = [];

        foreach ($matches as $m) {
            foreach ([[$m->homeClub, $m->home_goals, $m->away_goals], [$m->awayClub, $m->away_goals, $m->home_goals]] as [$club, $for, $against]) {
                $stats[$club->id] ??= ['club' => $club, 'w' => 0, 'd' => 0, 'l' => 0, 'gf' => 0, 'ga' => 0];
                $row = &$stats[$club->id];
                $row['gf'] += $for;
                $row['ga'] += $against;
                $for > $against ? $row['w']++ : ($for === $against ? $row['d']++ : $row['l']++);
                unset($row);
            }
        }

        return collect($stats)
            ->map(fn ($s) => $this->tableRow($s['club'], $s['w'], $s['d'], $s['l'], $s['gf'], $s['ga'], $pointsForWin))
            ->sortBy([
                ['points', 'desc'],
                ['goal_difference', 'desc'],
                ['goals_for', 'desc'],
            ])
            ->values();
    }

    protected function tableRow(Club $club, int $won, int $drawn, int $lost, int $gf, int $ga, int $pointsForWin): array
    {
        return [
            'club' => $club,
            'played' => $won + $drawn + $lost,
            'won' => $won,
            'drawn' => $drawn,
            'lost' => $lost,
            'goals_for' => $gf,
            'goals_against' => $ga,
            'goal_difference' => $gf - $ga,
            'points' => $won * $pointsForWin + $drawn,
        ];
    }
}

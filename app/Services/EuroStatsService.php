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
     * @return array{seasons: int, clubs: int, matches: int, goals: int, goals_per_match: float, biggest_win: ?EuroMatch}
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

        $biggestWin = (clone $matches)
            ->with(['homeClub:id,name,slug', 'awayClub:id,name,slug', 'season:id,name'])
            // goals are unsigned: cast before subtracting, or MariaDB fails on negative results
            ->orderByRaw('ABS(CAST(home_goals AS SIGNED) - CAST(away_goals AS SIGNED)) DESC, (home_goals + away_goals) DESC, played_at ASC')
            ->first();

        return [
            'seasons' => (int) $totals->seasons,
            'clubs' => $clubs,
            'matches' => (int) $totals->matches,
            'goals' => (int) $totals->goals,
            'goals_per_match' => $totals->matches ? round($totals->goals / $totals->matches, 2) : 0,
            'biggest_win' => $biggestWin,
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
                ->map(fn (Collection $matches) => $this->buildTable($matches, $season->points_for_win)));
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

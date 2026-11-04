<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\EuroCompetition;
use App\Models\EuroSeason;
use App\Models\EuroTie;
use App\Enums\EuroStage;
use App\Services\EuroStatsService;
use Illuminate\Contracts\View\View;

class EuroCompetitionController extends Controller
{
    public function index(EuroCompetition $euroCompetition, EuroStatsService $stats): View
    {
        $competition = $euroCompetition;

        $seasons = $competition->seasons()
            ->with([
                'winner:id,name,slug',
                'winner.attachment',
                'winner.names:id,club_id,name,from_year,to_year',
                'runnerUp:id,name,slug',
                'runnerUp.attachment',
                'runnerUp.names:id,club_id,name,from_year,to_year',
                'ties' => fn ($q) => $q->where('stage', EuroStage::FINAL->value),
                'ties.matches',
            ])
            ->get();

        $topClub = $seasons
            ->whereNotNull('winner_club_id')
            ->groupBy('winner_club_id')
            ->map(fn ($s) => ['club' => $s->first()->winner, 'titles' => $s->count()])
            ->sortByDesc('titles')
            ->first();

        $finals = $stats->finalsTable($seasons);
        $summary = $stats->competitionSummary($competition);
        $allTime = $stats->allTimeTable($competition);

        return view('front.euro.competition', compact('competition', 'seasons', 'topClub', 'summary', 'finals', 'allTime'));
    }

    public function season(EuroCompetition $euroCompetition, string $season, EuroStatsService $stats): View
    {
        $competition = $euroCompetition;

        $season = $competition->seasons()
            ->where('name', $season)
            ->with([
                'winner:id,name,slug',
                'winner.attachment',
                'runnerUp:id,name,slug',
                'runnerUp.attachment',
                'winner.names:id,club_id,name,from_year,to_year',
                'ties.matches',
                'ties.homeClub:id,name,slug',
                'ties.homeClub.names:id,club_id,name,from_year,to_year',
                'ties.awayClub:id,name,slug',
                'ties.awayClub.names:id,club_id,name,from_year,to_year',
                'matches.homeClub:id,name,slug',
                'matches.homeClub.names:id,club_id,name,from_year,to_year',
                'matches.awayClub:id,name,slug',
                'matches.awayClub.names:id,club_id,name,from_year,to_year',
            ])
            ->firstOrFail()
            ->setRelation('competition', $competition);

        $ties = $season->ties->groupBy(fn (EuroTie $tie) => $tie->stage->value);
        $groupMatches = $season->matches->whereNull('euro_tie_id')->groupBy([fn ($m) => $m->stage->value, fn ($m) => $m->group ?? '']);
        $groupTables = $stats->groupTables($season);
        $stages = $season->stages();
        $summary = $stats->seasonSummary($season);

        $siblings = $competition->seasons()->get(['id', 'name']);
        $index = $siblings->search(fn (EuroSeason $s) => $s->id === $season->id);
        // seasons are ordered newest first
        $nextSeason = $index > 0 ? $siblings[$index - 1] : null;
        $prevSeason = $siblings[$index + 1] ?? null;

        return view('front.euro.season', compact(
            'competition', 'season', 'summary', 'stages', 'ties', 'groupMatches', 'groupTables', 'prevSeason', 'nextSeason'
        ));
    }
}

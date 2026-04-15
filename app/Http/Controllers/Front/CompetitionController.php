<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Services\StatsService;
use Illuminate\Contracts\View\View;

class CompetitionController extends Controller
{
    public function index(Competition $competition, StatsService $statsService): View
    {
        $style = $competition->type->viewStyle();
        $type = $competition->type->value;

        $seasons = $competition->seasons()
            ->select(['id', 'name', 'competition_id'])
            ->with([
                'result.champions' => fn($q) => $q->select(['clubs.id', 'name']),
                'result.runnerUps' => fn($q) => $q->select(['clubs.id', 'name']),
                'result.thirdPlaces' => fn($q) => $q->select(['clubs.id', 'name']),
                'result.clubs'
            ])
            ->orderByDesc('name')
            ->get();

        $allTime = $statsService->getCompetitionAllTimeTable($competition);

        $topCupClub = $statsService
            ->getTopChampions($competition->country->id, $type, $competition->id, 1)
            ->first();

        $latestChampion = $statsService
            ->getLatestChampions($competition->country->id, $type, $competition->id, 1)
            ->first();

        return view('front.competition', compact(
            'competition',
            'style',
            'seasons',
            'topCupClub',
            'latestChampion',
            'allTime'
        ));
    }
}

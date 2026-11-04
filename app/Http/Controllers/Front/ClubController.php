<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Services\EuroStatsService;
use Illuminate\Contracts\View\View;

class ClubController extends Controller
{
    public function index(Club $club, EuroStatsService $euroStats): View
    {
        $club->load([
            'primaryCountry:id,name,slug',
            'countries:id,name,slug',
            'results.season.competition.country:id,name',
        ]);
        $stats = $club->competition_stats;
        $euroHistory = $euroStats->clubHistory($club);

        return view('front.club', compact('club', 'stats', 'euroHistory'));
    }
}

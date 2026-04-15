<?php

namespace App\Http\Controllers\Front;

use App\Enums\CompetitionType;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\StatsService;
use Illuminate\Contracts\View\View;

class CountryController extends Controller
{
    public function index(Country $country, StatsService $statsService): View
    {
        $type = CompetitionType::CHAMPIONSHIP->value;
        $limit = 10;

        $topChampionClubs = $statsService
            ->getTopChampions($country->id, $type, null, $limit);

        $latestChampions = $statsService
            ->getLatestChampions($country->id, $type, null, $limit);

        return view('front.country', compact('country', 'topChampionClubs', 'latestChampions'));
    }
}

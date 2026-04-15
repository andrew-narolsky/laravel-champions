<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Contracts\View\View;

class ClubController extends Controller
{
    public function index(Club $club): View
    {
        $club->load([
            'results.season.competition:id,name,type'
        ]);
        $stats = $club->competition_stats;

        return view('front.club', compact('club', 'stats'));
    }
}

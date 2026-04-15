<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(StatsService $statsService): View
    {
        return view('front.home', $statsService->get());
    }
}

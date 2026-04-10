<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Country;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        return view('admin.dashboard', [
            'countriesCount'   => Country::count(),
            'clubsCount'       => Club::count(),
            'competitionsCount' => Competition::count(),
        ]);
    }
}

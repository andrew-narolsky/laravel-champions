<?php

namespace App\Providers;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Country;
use App\Models\Season;
use App\Observers\StatsObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Country::observe(StatsObserver::class);
        Club::observe(StatsObserver::class);
        Competition::observe(StatsObserver::class);
        Season::observe(StatsObserver::class);
    }
}

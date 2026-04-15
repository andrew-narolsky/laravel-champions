<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class StatsObserver
{
    public function created($model): void
    {
        $this->clear();
    }

    public function deleted($model): void
    {
        $this->clear();
    }

    protected function clear(): void
    {
        Cache::forget('home.stats');
    }
}

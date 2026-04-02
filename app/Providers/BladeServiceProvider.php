<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerDirectives();
    }

    private function registerDirectives(): void
    {
        Blade::directive('ifroute', function ($arguments) {
            return '<?php if(in_array(Route::currentRouteName(), [' . $arguments . '])): ?>';
        });

        Blade::directive('endifroute', function () {
            return '<?php endif; ?>';
        });
    }
}

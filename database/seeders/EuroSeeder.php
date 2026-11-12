<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Imports every season of every European competition from data/euro/<competition>/<season>.json
 * (same as running "php artisan euro:import <file> --force" for each file).
 * Needs the clubs, so it runs after ClubSeeder: php artisan db:seed --class=EuroSeeder
 */
class EuroSeeder extends Seeder
{
    public function run(): void
    {
        $files = glob(database_path('data/euro') . '/*/*.json') ?: [];
        sort($files);

        $failed = [];

        foreach ($files as $file) {
            $code = Artisan::call('euro:import', ['file' => $file, '--force' => true]);

            if ($code !== 0) {
                $failed[] = $file;
                $this->command?->error(basename(dirname($file)) . '/' . basename($file) . ': ' . trim(Artisan::output()));
            }
        }

        $this->command?->info(sprintf('Euro seasons imported: %d of %d', count($files) - count($failed), count($files)));
    }
}

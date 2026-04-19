<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Competition;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $disallowedCountries = config('countries', []);

        $path = database_path('data/competitions');
        $files = glob($path . '/*.json');

        $destPath = 'uploads/competitions';
        Storage::disk('public')->makeDirectory($destPath);

        $allowedCountries = Country::whereNotIn('slug', $disallowedCountries)
            ->pluck('id')
            ->toArray();

        foreach ($files as $file) {
            $competitions = json_decode(file_get_contents($file), true);

            foreach ($competitions as $competition) {

                if (!in_array($competition['country_id'], $allowedCountries)) {
                    continue;
                }

                $slug = Str::slug($competition['name']);

                $created = Competition::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name'        => $competition['name'],
                        'country_id'  => $competition['country_id'],
                        'description' => $competition['description'],
                        'content'     => $competition['content'],
                        'type'        => $competition['type'],
                    ]
                );

                $srcFile = database_path("data/images/competitions/{$slug}.webp");

                if (!file_exists($srcFile)) {
                    continue;
                }

                $filename = "{$slug}.webp";

                Storage::disk('public')->put(
                    "{$destPath}/{$filename}",
                    file_get_contents($srcFile)
                );

                Attachment::updateOrCreate(
                    [
                        'module'    => 'competitions',
                        'module_id' => $created->id,
                    ],
                    [
                        'filename' => $filename,
                        'path'     => $destPath,
                        'ext'      => 'webp',
                        'type'     => 'image',
                        'size'     => filesize($srcFile),
                    ]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Club;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        $disallowedCountries = config('countries', []);

        $path = database_path('data/clubs');
        $files = glob($path . '/*.json');

        $destPath = 'uploads/clubs';
        Storage::disk('public')->makeDirectory($destPath);

        $countries = Country::whereNotIn('slug', $disallowedCountries)
            ->get()
            ->keyBy('slug');

        foreach ($files as $file) {

            $countrySlug = basename($file, '.json');

            $country = $countries->get($countrySlug);
            if (!$country) {
                continue;
            }

            $clubs = json_decode(file_get_contents($file), true);

            if (empty($clubs)) {
                continue;
            }

            foreach ($clubs as $club) {

                $slug = Str::slug($club['name']);

                $created = Club::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name'          => $club['name'],
                        'country_id'    => $country->id,
                        'nickname'      => $club['nickname'] ?? null,
                        'description'   => $club['description'] ?? null,
                        'content'       => $club['content'] ?? null,
                        'founded_at'    => $club['founded_at'] ?? null,
                        'destroyed_at'  => $club['destroyed_at'] ?: null,
                        'stadium'       => $club['stadium'] ?? null,
                        'city'          => $club['city'] ?? null,
                    ]
                );

                if (!empty($club['names'])) {
                    $created->names()->delete();
                    $created->names()->createMany($club['names']);
                }

                $srcFile = database_path("data/images/clubs/{$countrySlug}/{$slug}.webp");

                if (!file_exists($srcFile)) {
                    continue;
                }

                $filename = "{$slug}.webp";

                Storage::disk('public')->put(
                    "{$destPath}/{$filename}",
                    fopen($srcFile, 'r')
                );

                Attachment::updateOrCreate(
                    [
                        'module'    => 'clubs',
                        'module_id' => $created->id,
                    ],
                    [
                        'filename'  => $filename,
                        'path'      => $destPath,
                        'ext'       => 'webp',
                        'type'      => 'image',
                        'size'      => filesize($srcFile),
                    ]
                );
            }
        }
    }
}

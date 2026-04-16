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
        $path = database_path('data/clubs');
        $files = glob($path . '/*.json');

        $destPath = 'uploads/clubs';
        Storage::disk('public')->makeDirectory($destPath);

        $countries = Country::all()->keyBy('id');

        foreach ($files as $file) {
            $clubs = json_decode(file_get_contents($file), true);

            foreach ($clubs as $club) {
                $slug = Str::slug($club['name']);

                $created = Club::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name'          => $club['name'],
                        'country_id'    => $club['country_id'],
                        'nickname'      => $club['nickname'],
                        'description'   => $club['description'],
                        'content'       => $club['content'],
                        'founded_at'    => $club['founded_at'],
                        'destroyed_at'  => $club['destroyed_at'] ?: null,
                        'stadium'       => $club['stadium'],
                        'city'          => $club['city'],
                    ]
                );

                if (!empty($club['names'])) {
                    $created->names()->delete();
                    $created->names()->createMany($club['names']);
                }

                $country = $countries->get($club['country_id']);
                $countrySlug = $country?->slug;

                if (!$countrySlug) {
                    continue;
                }

                $srcFile = database_path("data/images/clubs/{$countrySlug}/{$slug}.webp");

                if (file_exists($srcFile)) {
                    $filename = "{$slug}.webp";

                    Storage::disk('public')->put(
                        "{$destPath}/{$filename}",
                        file_get_contents($srcFile)
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
}

<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = json_decode(
            file_get_contents(database_path('data/countries.json')),
            true
        );

        $destPath = 'uploads/countries';
        Storage::disk('public')->makeDirectory($destPath);

        foreach ($countries as $country) {
            $slug = Str::slug($country['name']);

            $created = Country::create([
                'name'        => $country['name'],
                'slug'        => $slug,
                'description' => $country['description'],
                'flag_code'   => $country['flag'],
                'content'     => $country['content'],
            ]);

            $srcFile = database_path("data/images/countries/{$slug}.webp");

            if (file_exists($srcFile)) {
                $filename = "{$slug}.webp";
                Storage::disk('public')->put(
                    "{$destPath}/{$filename}",
                    file_get_contents($srcFile)
                );

                Attachment::create([
                    'module'    => 'countries',
                    'module_id' => $created->id,
                    'filename'  => $filename,
                    'path'      => $destPath,
                    'ext'       => 'webp',
                    'type'      => 'image',
                    'size'      => filesize($srcFile),
                ]);
            }
        }
    }
}

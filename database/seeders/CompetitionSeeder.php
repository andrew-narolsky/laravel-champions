<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Competition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $competitions = json_decode(
            file_get_contents(database_path('data/competitions.json')),
            true
        );

        $destPath = 'uploads/competitions';
        Storage::disk('public')->makeDirectory($destPath);

        foreach ($competitions as $competition) {
            $slug = Str::slug($competition['name']);

            $created = Competition::create([
                'name'          => $competition['name'],
                'slug'          => $slug,
                'country_id'    => $competition['country_id'],
                'description'   => $competition['description'],
                'content'       => $competition['content'],
                'type'          => $competition['type'],
            ]);

            $srcFile = database_path("data/images/competitions/{$slug}.webp");

            if (file_exists($srcFile)) {
                $filename = "{$slug}.webp";
                Storage::disk('public')->put(
                    "{$destPath}/{$filename}",
                    file_get_contents($srcFile)
                );

                Attachment::create([
                    'module'    => 'competitions',
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

<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Club;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        $clubs = json_decode(
            file_get_contents(database_path('data/clubs.json')),
            true
        );

        $destPath = 'uploads/clubs';
        Storage::disk('public')->makeDirectory($destPath);

        foreach ($clubs as $club) {
            $slug = Str::slug($club['name']);

            $created = Club::create([
                'name'          => $club['name'],
                'slug'          => $slug,
                'country_id'    => $club['country_id'],
                'nickname'      => $club['nickname'],
                'description'   => $club['description'],
                'content'       => $club['content'],
                'founded_at'    => $club['founded_at'],
                'destroyed_at'  => $club['destroyed_at'] ?: null,
                'stadium'       => $club['stadium'],
                'city'          => $club['city'],
            ]);

            if (!empty($club['names'])) {
                $created->names()->createMany($club['names']);
            }

            $srcFile = database_path("data/images/clubs/{$slug}.webp");

            if (file_exists($srcFile)) {
                $filename = "{$slug}.webp";
                Storage::disk('public')->put(
                    "{$destPath}/{$filename}",
                    file_get_contents($srcFile)
                );

                Attachment::create([
                    'module'    => 'clubs',
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

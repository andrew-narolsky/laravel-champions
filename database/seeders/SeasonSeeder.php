<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    public function run(): void
    {
        $seasons = json_decode(
            file_get_contents(database_path('data/seasons.json')),
            true
        );

        foreach ($seasons as $data) {
            $season = Season::create([
                'name'           => $data['name'],
                'competition_id' => $data['competition_id'],
            ]);

            if (empty($data['result'])) {
                continue;
            }

            $result = $season->result()->create([
                'score' => $data['result']['score'] ?? null,
            ]);

            foreach ($data['result']['places'] ?? [] as $place => $clubNames) {
                foreach ($clubNames as $order => $clubName) {
                    $club = Club::where('name', $clubName)->first();

                    if ($club) {
                        $result->clubs()->attach($club->id, [
                            'place' => $place,
                            'order' => $order,
                        ]);
                    }
                }
            }
        }
    }
}
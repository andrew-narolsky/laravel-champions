<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/seasons');
        $files = glob($path . '/*.json');

        $allClubNames = [];
        $seasonsData = [];

        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);

            foreach ($data as $season) {
                $seasonsData[] = $season;

                foreach ($season['result']['places'] ?? [] as $clubNames) {
                    foreach ($clubNames as $clubName) {
                        $allClubNames[] = $clubName;
                    }
                }
            }
        }

        $clubs = Club::whereIn('name', array_unique($allClubNames))
            ->get()
            ->keyBy('name');

        foreach ($seasonsData as $data) {
            $season = Season::updateOrCreate(
                [
                    'name' => $data['name'],
                    'competition_id' => $data['competition_id'],
                ],
                []
            );

            if (empty($data['result'])) {
                continue;
            }

            $result = $season->result()->updateOrCreate(
                [],
                [
                    'score' => $data['result']['score'] ?? null,
                ]
            );

            $result->clubs()->detach();

            foreach ($data['result']['places'] ?? [] as $place => $clubNames) {
                foreach ($clubNames as $order => $clubName) {
                    $club = $clubs->get($clubName);

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

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // $this->call(UserSeeder::class);
        // $this->call(CountrySeeder::class);
        $this->call(CompetitionSeeder::class);
        $this->call(ClubSeeder::class);
        $this->call(SeasonSeeder::class);
    }
}

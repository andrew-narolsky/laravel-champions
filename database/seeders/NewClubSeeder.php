<?php

namespace Database\Seeders;

/**
 * Seeds only data/clubs/new-clubs.json, without touching the clubs from the country files:
 * php artisan db:seed --class=NewClubSeeder
 */
class NewClubSeeder extends ClubSeeder
{
    public function run(): void
    {
        $this->seedFile(database_path('data/clubs/' . self::NEW_CLUBS_FILE), $this->countries());
    }
}

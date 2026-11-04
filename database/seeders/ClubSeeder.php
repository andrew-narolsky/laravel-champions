<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Club;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClubSeeder extends Seeder
{
    /**
     * Clubs added later (e.g. found while importing European cups): one file for all countries,
     * every club has a "country" field with the country slug.
     */
    public const NEW_CLUBS_FILE = 'new-clubs.json';

    private const DEST_PATH = 'uploads/clubs';

    public function run(): void
    {
        $countries = $this->countries();

        foreach (glob(database_path('data/clubs') . '/*.json') as $file) {
            $this->seedFile($file, $countries);
        }
    }

    /**
     * Country files (<country-slug>.json) take the country from the file name,
     * the new clubs file from each club's "country" field.
     */
    protected function seedFile(string $file, Collection $countries): void
    {
        $fileCountry = basename($file) === self::NEW_CLUBS_FILE ? null : basename($file, '.json');

        if ($fileCountry && !$countries->has($fileCountry)) {
            return;
        }

        foreach (json_decode(file_get_contents($file), true) ?: [] as $club) {
            $countrySlug = $fileCountry ?? ($club['country'] ?? null);
            $country = $countries->get($countrySlug);

            if ($country) {
                $this->seedClub($club, $country, $countrySlug);
            }
        }
    }

    protected function countries(): Collection
    {
        Storage::disk('public')->makeDirectory(self::DEST_PATH);

        return Country::whereNotIn('slug', config('countries', []))
            ->get()
            ->keyBy('slug');
    }

    private function seedClub(array $club, Country $country, string $countrySlug): void
    {
        $slug = Str::slug($club['name']);

        $created = Club::updateOrCreate(
            ['slug' => $slug],
            [
                'name'                  => $club['name'],
                'nickname'              => $club['nickname'] ?? null,
                'description'           => $club['description'] ?? null,
                'content'               => $club['content'] ?? null,
                'founded_at'            => $club['founded_at'] ?? null,
                'destroyed_at'          => ($club['destroyed_at'] ?? null) ?: null,
                'stadium'               => $club['stadium'] ?? null,
                'city'                  => $club['city'] ?? null,
                'primary_country_id'    => $club['primary_country_id'] ?? null,
            ]
        );

        $extraCountryIds = $club['country_id'] ?? [];

        if (!is_array($extraCountryIds)) {
            $extraCountryIds = [$extraCountryIds];
        }

        $created->countries()->sync(array_unique(array_merge([$country->id], $extraCountryIds)));

        if (!empty($club['names'])) {
            $created->names()->delete();
            $created->names()->createMany($club['names']);
        }

        $srcFile = database_path("data/images/clubs/{$countrySlug}/{$slug}.webp");

        if (!file_exists($srcFile)) {
            return;
        }

        $filename = "{$slug}.webp";

        Storage::disk('public')->put(self::DEST_PATH . "/{$filename}", fopen($srcFile, 'r'));

        Attachment::updateOrCreate(
            [
                'module'    => 'clubs',
                'module_id' => $created->id,
            ],
            [
                'filename'  => $filename,
                'path'      => self::DEST_PATH,
                'ext'       => 'webp',
                'type'      => 'image',
                'size'      => filesize($srcFile),
            ]
        );
    }
}

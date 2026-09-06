<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Country;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the public sitemap.xml file';

    public function handle(): int
    {
        $urls = 1;

        $sitemap = Sitemap::create()
            ->add(
                Url::create(route('home'))
                    ->setPriority(1.0)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            );

        Country::query()->select(['id', 'slug', 'updated_at'])->orderBy('slug')
            ->cursor()->each(function (Country $country) use ($sitemap, &$urls) {
                $sitemap->add(
                    Url::create(route('country.show', $country->slug))
                        ->setLastModificationDate($country->updated_at)
                        ->setPriority(0.8)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                );
                $urls++;
            });

        Competition::query()->select(['id', 'slug', 'updated_at'])->orderBy('slug')
            ->cursor()->each(function (Competition $competition) use ($sitemap, &$urls) {
                $sitemap->add(
                    Url::create(route('competition.show', $competition->slug))
                        ->setLastModificationDate($competition->updated_at)
                        ->setPriority(0.7)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                );
                $urls++;
            });

        Club::query()->select(['id', 'slug', 'updated_at'])->orderBy('slug')
            ->cursor()->each(function (Club $club) use ($sitemap, &$urls) {
                $sitemap->add(
                    Url::create(route('club.show', $club->slug))
                        ->setLastModificationDate($club->updated_at)
                        ->setPriority(0.6)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                );
                $urls++;
            });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info("Sitemap generated with {$urls} urls.");

        return self::SUCCESS;
    }
}

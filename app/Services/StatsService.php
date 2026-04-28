<?php

namespace App\Services;

use App\Enums\CompetitionType;
use App\Enums\SeasonPosition;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Country;
use App\Models\Result;
use App\Models\Season;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class StatsService
{
    const int PAGINATION_LIMIT = 20;

    public function get(): array
    {
        $data = fn () => [
            'countriesCount' => Country::count(),
            'clubsCount' => Club::count(),
            'competitionsCount' => Competition::count(),
            'seasonsCount' => Season::count(),
            'topChampionClubs' => $this->getTopChampions(CompetitionType::CHAMPIONSHIP->value),
            'topCupClubs' => $this->getTopChampions(CompetitionType::CUP->value),
            'countries' => $this->getCountries(),
        ];

        if (!config('app.cache_home_stats')) {
            return $data();
        }

        return Cache::rememberForever('home.stats', $data);
    }

    public function clear(): void
    {
        Cache::forget('home.stats');
    }

    public function getTopChampions(
        ?string $type = null,
        ?int $countryId = null,
        ?int $competitionId = null,
        ?int $limit = null
    ): Collection {
        $limit ??= self::PAGINATION_LIMIT;
        $type ??= CompetitionType::CHAMPIONSHIP;

        return Club::query()
            ->select(['id', 'name', 'slug'])
            ->when($countryId, fn($q) => $q->whereHas('countries', fn($q2) => $q2->where('countries.id', $countryId)))
            ->with([
                'countries:id,name',
                'attachment'
            ])
            ->withTrophiesCount($type, 'titles', $competitionId, $countryId)
            ->orderByDesc('titles')
            ->limit($limit)
            ->get();
    }

    public function getLatestChampions(
        ?string $type = null,
        ?int $countryId = null,
        ?int $competitionId = null,
        ?int $limit = null
    ): Collection {
        $limit ??= self::PAGINATION_LIMIT;

        return Season::query()
            ->select(['id', 'name', 'competition_id'])
            ->with([
                'competition:id,name,country_id',
                'competition.country:id,name,flag_code',
                'result.champions.attachment'
            ])
            ->whereHas('competition', function ($q) use ($countryId, $type, $competitionId) {

                if ($competitionId) {
                    $q->where('id', $competitionId);
                    return;
                }

                if ($type) {
                    $q->where('type', $type);
                }

                if ($countryId) {
                    $q->where('country_id', $countryId);
                }
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    protected function getCountries(): Collection
    {
        return Country::query()
            ->select(['id', 'name', 'flag_code', 'slug'])
            ->get();
    }

    public function getCompetitionAllTimeTable(Competition $competition): Collection
    {
        $results = Result::query()
            ->whereHas('season', fn($q) =>
            $q->where('competition_id', $competition->id)
            )
            ->with([
                'season:id,name',
                'clubs.attachment'
            ])
            ->get();

        $clubs = [];

        foreach ($results as $result) {
            foreach ($result->clubs as $club) {

                $clubId = $club->id;
                $place = $club->pivot->place;

                if (!in_array($place, [
                    SeasonPosition::CHAMPION->value,
                    SeasonPosition::RUNNER_UP->value,
                    SeasonPosition::THIRD_PLACE->value,
                ])) {
                    continue;
                }

                if (!isset($clubs[$clubId])) {
                    $clubs[$clubId] = [
                        'club' => $club,
                        'champions' => collect(),
                        'runnerups' => collect(),
                        'third' => collect(),
                    ];
                }

                $key = match ($place) {
                    SeasonPosition::CHAMPION->value => 'champions',
                    SeasonPosition::RUNNER_UP->value => 'runnerups',
                    SeasonPosition::THIRD_PLACE->value => 'third',
                };

                $clubs[$clubId][$key]->push($result->season->name);
            }
        }

        return collect($clubs)
            ->map(fn($c) => [
                'club' => $c['club'],
                'champions' => [
                    'count' => $c['champions']->count(),
                    'years' => $c['champions']->implode(', ')
                ],
                'runnerups' => [
                    'count' => $c['runnerups']->count(),
                    'years' => $c['runnerups']->implode(', ')
                ],
                'third' => [
                    'count' => $c['third']->count(),
                    'years' => $c['third']->implode(', ')
                ],
            ])
            ->sortByDesc(fn($c) => [
                $c['champions']['count'],
                $c['runnerups']['count'],
                $c['third']['count'],
            ])
            ->values();
    }

    public function buildCompetitionStats(Club $club): Collection
    {
        return $club->results
            ->groupBy(fn($r) => optional($r->season->competition->country)->name ?? 'Unknown')
            ->map(function ($countryResults) {

                return $countryResults
                    ->groupBy(fn($r) => $r->season->competition->type->value)
                    ->map(function ($results, $type) {

                        $grouped = $results->groupBy(fn($r) => $r->pivot->place);

                        $get = function ($place) use ($grouped) {
                            if (!$grouped->has($place)) {
                                return null;
                            }

                            $items = $grouped->get($place);

                            return [
                                'count' => $items->count(),
                                'years' => $items
                                    ->pluck('season.name')
                                    ->implode(' · ')
                            ];
                        };

                        $data = [
                            'type' => CompetitionType::from($type),
                            'champions' => $get(SeasonPosition::CHAMPION->value),
                        ];

                        if ($runnerups = $get(SeasonPosition::RUNNER_UP->value)) {
                            $data['runnerups'] = $runnerups;
                        }

                        if ($third = $get(SeasonPosition::THIRD_PLACE->value)) {
                            $data['third'] = $third;
                        }

                        return $data;
                    })
                    ->values();
            });
    }
}

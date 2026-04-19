<?php

namespace App\Models;

use App\Enums\CompetitionType;
use App\Enums\SeasonPosition;
use App\Services\DateParserService;
use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Club extends Model
{
    use HasAttachments;

    const string MODULE_NAME = 'clubs';

    const int PAGINATION_LIMIT = 20;

    protected $fillable = [
        'name',
        'slug',
        'country_id',
        'nickname',
        'description',
        'content',
        'founded_at',
        'destroyed_at',
        'stadium',
        'city',
        'address',
    ];

    public function names()
    {
        return $this->hasMany(ClubName::class)->orderBy('from_year');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function results(): BelongsToMany
    {
        return $this->belongsToMany(Result::class, 'result_clubs')
            ->withPivot(['place', 'order']);
    }

    public function scopeWithTrophiesCount(
        $query,
        string $type,
        string $alias = 'trophies_count',
        ?int $competitionId = null
    ) {
        return $query->withCount([
            'results as ' . $alias => function ($q) use ($type, $competitionId) {

                $q->where('result_clubs.place', SeasonPosition::CHAMPION->value)
                    ->whereHas('season.competition', function ($q) use ($type, $competitionId) {

                        if ($competitionId) {
                            $q->where('id', $competitionId);
                            return;
                        }

                        $q->where('type', $type);
                    });
            }
        ]);
    }


    public function getNormalizedNameAttribute(): string
    {
        return preg_replace('/^(1.|AC|AF|AK|ASKÖ|CE|DSV|FC|FK|HNK|HŠK|K|KAA|KF|KRC|KS|KSC|KSK|KSV|KV|KVC|NK|PFK|R|RAA|RCS|RSC|RFC|RE|RRC|RWD|SC|SK|SKN|SV|SpC|TSV|UE)\s+/i', '', $this->name);
    }

    public function getCompetitionStatsAttribute(): Collection
    {
        return $this->results
            ->groupBy(fn($r) => $r->season->competition->type->value)
            ->map(function ($results, $type) {

                $grouped = $results->groupBy(fn($r) => $r->pivot->place);

                $get = fn($place) => $grouped->has($place)
                    ? [
                        'count' => $grouped->get($place)->count(),
                        'years' => $grouped->get($place)
                            ->pluck('season.name')
                            ->implode(' · ')
                    ]
                    : null;

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
            });
    }

    public function getDateParts(string $field): array
    {
        return app(DateParserService::class)
            ->parse($this->{$field});
    }

    public function getFoundedDatePartsAttribute(): array
    {
        return $this->getDateParts('founded_at');
    }

    public function getDestroyedDatePartsAttribute(): array
    {
        return $this->getDateParts('destroyed_at');
    }
}

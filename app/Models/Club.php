<?php

namespace App\Models;

use App\Enums\SeasonPosition;
use App\Services\DateParserService;
use App\Services\StatsService;
use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Club extends Model
{
    use HasAttachments;

    const string MODULE_NAME = 'clubs';

    const int PAGINATION_LIMIT = 20;

    private const string NAME_PREFIX_REGEX = '/^(1\. FC|AC|AF|AE|AJ|AK|AS|ASKÖ|CA|CE|CO|ČFK|DSV|ÉF|EF|FC|FCI|FF|FK|GNK|HNK|HŠK|IFK|JK|JS|K|KAA|KF|KRC|KS|KSC|KSK|KSV|KV|KVC|LB|Le|NK|OFC|OGC|PFC|PFK|POFC|R|RAA|RC|RCS|RNK|RSC|RFC|RE|RRC|RWD|SC|SFC|SFK|SK|SKN|SpC|SpVgg|SpVg|SV|TSV|TSG|VfB|VfR|UE|US)\s+/i';

    protected $fillable = [
        'name',
        'slug',
        'nickname',
        'description',
        'content',
        'founded_at',
        'destroyed_at',
        'stadium',
        'city',
    ];

    public function names()
    {
        return $this->hasMany(ClubName::class)->orderBy('from_year');
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    public function results(): BelongsToMany
    {
        return $this->belongsToMany(Result::class, 'result_clubs')
            ->withPivot(['place', 'order']);
    }


    protected function country(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('countries')
                ? $this->countries->first()
                : $this->countries()->first()
        );
    }

    protected function normalizedName(): Attribute
    {
        return Attribute::make(
            get: fn () => preg_replace(self::NAME_PREFIX_REGEX, '', $this->name)
        );
    }

    protected function competitionStats(): Attribute
    {
        return Attribute::make(
            get: fn () => app(StatsService::class)->buildCompetitionStats($this)
        );
    }

    protected function dateParts(string $field): Attribute
    {
        return Attribute::make(
            get: fn () => $this->{$field}
                ? $this->getDateParts($field)
                : [null, null]
        );
    }

    protected function foundedDateParts(): Attribute
    {
        return $this->dateParts('founded_at');
    }

    protected function destroyedDateParts(): Attribute
    {
        return $this->dateParts('destroyed_at');
    }

    public function scopeWithTrophiesCount(
        $query,
        string $type,
        string $alias = 'trophies_count',
        ?int $competitionId = null,
        ?int $countryId = null
    ) {
        return $query->withCount([
            'results as ' . $alias => function ($q) use ($type, $competitionId, $countryId) {

                $q->where('result_clubs.place', SeasonPosition::CHAMPION->value)
                    ->whereHas('season.competition', function ($q) use ($type, $competitionId, $countryId) {

                        if ($competitionId) {
                            $q->where('id', $competitionId);
                            return;
                        }

                        $q->where('type', $type);

                        if ($countryId) {
                            $q->where('country_id', $countryId);
                        } else {
                            $q->where('country_id', function ($sub) {
                                $sub->selectRaw('MIN(country_id)')
                                    ->from('club_country')
                                    ->whereColumn('club_id', 'clubs.id');
                            });
                        }
                    });
            }
        ]);
    }

    public function getDateParts(string $field): array
    {
        return app(DateParserService::class)
            ->parse($this->{$field});
    }
}

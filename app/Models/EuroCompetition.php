<?php

namespace App\Models;

use App\Traits\HasAttachments;
use App\Traits\HasSlugRedirects;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EuroCompetition extends Model
{
    use HasAttachments;
    use HasSlugRedirects;

    const string MODULE_NAME = 'euro_competitions';

    const int PAGINATION_LIMIT = 20;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
    ];

    /**
     * Display order of the competitions (home page, club page); unknown slugs go last.
     */
    const array ORDER = [
        'champions-league',
        'europa-league',
        'conference-league',
        'super-cup',
        'cup-winners-cup',
        'fairs-cup',
    ];

    public static function sortPosition(string $slug): int
    {
        $position = array_search($slug, self::ORDER, true);

        return $position === false ? count(self::ORDER) : $position;
    }

    public function scopeOrdered(Builder $query): Builder
    {
        $cases = collect(self::ORDER)->map(fn (string $slug, int $i) => "WHEN ? THEN {$i}")->implode(' ');

        return $query->orderByRaw("CASE slug {$cases} ELSE " . count(self::ORDER) . ' END', self::ORDER)->orderBy('id');
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(EuroSeason::class)->orderBy('name', 'desc');
    }

    public static function slugRedirectRouteName(): string
    {
        return 'euro.show';
    }
}

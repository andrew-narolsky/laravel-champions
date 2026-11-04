<?php

namespace App\Models;

use App\Enums\EuroStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class EuroSeason extends Model
{
    protected $fillable = [
        'euro_competition_id',
        'name',
        'competition_name',
        'points_for_win',
        'point_adjustments',
        'winner_club_id',
        'runner_up_club_id',
    ];

    protected $casts = [
        'point_adjustments' => 'array',
    ];

    /**
     * Competition name as it was called in this season.
     */
    public function competitionTitle(): string
    {
        return $this->competition_name ?: $this->competition->name;
    }

    /**
     * First calendar year of the season ("2008-09" → 2008), used for historical club names.
     */
    public function startYear(): int
    {
        return (int) substr($this->name, 0, 4);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(EuroCompetition::class, 'euro_competition_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'winner_club_id');
    }

    public function runnerUp(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'runner_up_club_id');
    }

    public function ties(): HasMany
    {
        return $this->hasMany(EuroTie::class)->orderBy('order');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(EuroMatch::class)->orderBy('played_at')->orderBy('id');
    }

    /**
     * Stages present in this season, in the order they were played.
     *
     * Formats changed over the years (e.g. group stage after round 2 in 1991-92,
     * but first in 1994-95), so order by the first match date of each stage;
     * EuroStage::sortOrder() is only the tie-breaker / fallback without dates.
     *
     * @return Collection<int, EuroStage>
     */
    public function stages(): Collection
    {
        return $this->matches
            ->groupBy(fn (EuroMatch $m) => $m->stage->value)
            ->map(fn (Collection $matches) => [
                'stage' => $matches->first()->stage,
                'first_date' => $matches->min('played_at')?->format('Y-m-d') ?? '9999-12-31',
            ])
            ->sortBy([
                ['first_date', 'asc'],
                fn ($a, $b) => $a['stage']->sortOrder() <=> $b['stage']->sortOrder(),
            ])
            ->pluck('stage')
            ->values();
    }

    /**
     * Fill winner / runner-up from the final tie, if one exists.
     */
    public function syncWinnerFromFinal(): void
    {
        $final = $this->ties()->where('stage', EuroStage::FINAL->value)->first();

        if (!$final?->winner_club_id) {
            return;
        }

        $this->update([
            'winner_club_id' => $final->winner_club_id,
            'runner_up_club_id' => $final->loserClubId(),
        ]);
    }
}

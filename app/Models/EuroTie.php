<?php

namespace App\Models;

use App\Enums\EuroStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EuroTie extends Model
{
    protected $fillable = [
        'euro_season_id',
        'stage',
        'home_club_id',
        'away_club_id',
        'winner_club_id',
        'note',
        'order',
    ];

    protected $casts = [
        'stage' => EuroStage::class,
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(EuroSeason::class, 'euro_season_id');
    }

    public function homeClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'home_club_id');
    }

    public function awayClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'away_club_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'winner_club_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(EuroMatch::class)->orderBy('leg');
    }

    /**
     * Number of regular legs: two, except a final played as one match at a neutral venue
     * (European Cup / Champions League, UEFA Cup from 1998). Two-legged finals (UEFA Cup
     * until 1997) are played home and away. A later match is a play-off / replay
     * and is not part of the aggregate.
     */
    public function regularLegs(): int
    {
        if ($this->stage !== EuroStage::FINAL) {
            return 2;
        }

        return $this->matches->first()?->neutral === false ? 2 : 1;
    }

    /**
     * Aggregate goals of a club over the regular legs of this tie.
     */
    public function goalsFor(int $clubId): int
    {
        return $this->matches
            ->filter(fn (EuroMatch $m) => $m->leg <= $this->regularLegs())
            ->sum(fn (EuroMatch $m) => $m->goalsFor($clubId));
    }

    /**
     * Play-off / replay after a level aggregate (old-era deciders), if any.
     */
    public function playoff(): ?EuroMatch
    {
        return $this->matches->first(fn (EuroMatch $m) => $m->leg > $this->regularLegs());
    }

    /**
     * "4:3" from the perspective of home_club_id.
     */
    public function aggregate(): string
    {
        return $this->goalsFor($this->home_club_id) . ':' . $this->goalsFor((int) $this->away_club_id);
    }

    public function loserClubId(): ?int
    {
        return match ($this->winner_club_id) {
            null => null,
            $this->home_club_id => $this->away_club_id,
            default => $this->home_club_id,
        };
    }
}

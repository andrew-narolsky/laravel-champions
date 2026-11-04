<?php

namespace App\Models;

use App\Enums\EuroStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EuroMatch extends Model
{
    protected $fillable = [
        'euro_season_id',
        'euro_tie_id',
        'stage',
        'group',
        'leg',
        'played_at',
        'home_club_id',
        'away_club_id',
        'home_goals',
        'away_goals',
        'extra_time',
        'neutral',
        'note',
    ];

    protected $casts = [
        'stage' => EuroStage::class,
        'played_at' => 'date',
        'extra_time' => 'boolean',
        'neutral' => 'boolean',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(EuroSeason::class, 'euro_season_id');
    }

    public function tie(): BelongsTo
    {
        return $this->belongsTo(EuroTie::class, 'euro_tie_id');
    }

    public function homeClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'home_club_id');
    }

    public function awayClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'away_club_id');
    }

    public function goalsFor(int $clubId): int
    {
        return match ($clubId) {
            $this->home_club_id => $this->home_goals,
            $this->away_club_id => $this->away_goals,
            default => 0,
        };
    }

    /**
     * Shoot-out result parsed from the note ("<Club> won 4:3 on penalties"):
     * [winner's kicks, loser's kicks], or null when there was no shoot-out.
     *
     * @return array{0: int, 1: int}|null
     */
    public function penalties(): ?array
    {
        if ($this->note && preg_match('/won (\d+):(\d+) on penalties/', $this->note, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }

        return null;
    }

    /**
     * "4:3" from the home side, or from the perspective of $clubId when given.
     */
    public function score(?int $clubId = null): string
    {
        [$for, $against] = $clubId === $this->away_club_id
            ? [$this->away_goals, $this->home_goals]
            : [$this->home_goals, $this->away_goals];

        return "{$for}:{$against}" . ($this->extra_time ? ' a.e.t.' : '');
    }
}

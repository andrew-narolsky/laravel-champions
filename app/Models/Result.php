<?php

namespace App\Models;

use App\Enums\SeasonPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Result extends Model
{
    protected $fillable = [
        'season_id',
        'score',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'result_clubs')
            ->withPivot('place', 'order')
            ->orderByPivot('order');
    }

    public function champions(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'result_clubs')
            ->wherePivot('place', SeasonPosition::CHAMPION->value)
            ->orderByPivot('order');
    }

    public function runnerUps(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'result_clubs')
            ->wherePivot('place', SeasonPosition::RUNNER_UP->value)
            ->orderByPivot('order');
    }

    public function thirdPlaces(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'result_clubs')
            ->wherePivot('place', SeasonPosition::THIRD_PLACE->value)
            ->orderByPivot('order');
    }
}

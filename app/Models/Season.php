<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Season extends Model
{
    const int PAGINATION_LIMIT = 20;

    protected $fillable = [
        'name',
        'competition_id',
    ];

    /**
     * First year of the season name ("2022-23" => 2022), used to pick the club name of that time.
     */
    public function startYear(): ?int
    {
        return preg_match('/\d{4}/', $this->name, $m) ? (int) $m[0] : null;
    }

    public function result(): HasOne
    {
        return $this->hasOne(Result::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}

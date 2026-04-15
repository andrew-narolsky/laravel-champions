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

    public function result(): HasOne
    {
        return $this->hasOne(Result::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}

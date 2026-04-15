<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubName extends Model
{
    protected $fillable = [
        'club_id',
        'name',
        'from_year',
        'to_year',
        'note',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function getPeriodAttribute(): string
    {
        if ($this->from_year && $this->to_year) {
            return "{$this->from_year} — {$this->to_year}";
        }

        if ($this->from_year) {
            return "{$this->from_year} — present";
        }

        return '—';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    protected $fillable = [
        'season_id',
        'champion_id',
        'runner_up_id',
        'third_place_id',
        'score',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

//    public function champion(): BelongsTo
//    {
//        return $this->belongsTo(Club::class, 'champion_id');
//    }
//
//    public function runnerUp(): BelongsTo
//    {
//        return $this->belongsTo(Club::class, 'runner_up_id');
//    }
//
//    public function thirdPlace(): BelongsTo
//    {
//        return $this->belongsTo(Club::class, 'third_place_id');
//    }
}

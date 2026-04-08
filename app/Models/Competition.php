<?php

namespace App\Models;

use App\Enums\CompetitionType;
use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasAttachments;

    const string MODULE_NAME = 'competitions';

    const int PAGINATION_LIMIT = 20;

    protected $casts = [
        'type' => CompetitionType::class,
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
        'country_id',
        'type',
    ];
}

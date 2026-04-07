<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasAttachments;

    protected $casts = [
        'founded_at' => 'date',
        'destroyed_at' => 'date',
    ];

    const string MODULE_NAME = 'clubs';

    const int PAGINATION_LIMIT = 20;

    protected $fillable = [
        'name',
        'slug',
        'country_id',
        'nickname',
        'description',
        'content',
        'founded_at',
        'destroyed_at',
        'stadium',
        'city',
        'address',
    ];
}

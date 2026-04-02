<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    const PAGINATION_LIMIT = 20;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
    ];
}

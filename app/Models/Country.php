<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasAttachments;

    const string MODULE_NAME = 'countries';

    const int PAGINATION_LIMIT = 20;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'flag_code',
        'content',
    ];

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class)->with('attachment');
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }
}

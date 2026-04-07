<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasAttachments;

    const string MODULE_NAME = 'countries';

    const int PAGINATION_LIMIT = 20;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
    ];
}

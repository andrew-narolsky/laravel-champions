<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function thumbnail(): HasOne
    {
        return $this->hasOne(Attachment::class, 'module_id', 'id')->where('module', self::MODULE_NAME);
    }
}

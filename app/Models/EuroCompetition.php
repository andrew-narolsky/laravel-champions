<?php

namespace App\Models;

use App\Traits\HasAttachments;
use App\Traits\HasSlugRedirects;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EuroCompetition extends Model
{
    use HasAttachments;
    use HasSlugRedirects;

    const string MODULE_NAME = 'euro_competitions';

    const int PAGINATION_LIMIT = 20;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
    ];

    public function seasons(): HasMany
    {
        return $this->hasMany(EuroSeason::class)->orderBy('name', 'desc');
    }

    public static function slugRedirectRouteName(): string
    {
        return 'euro.show';
    }
}

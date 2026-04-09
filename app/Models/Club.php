<?php

namespace App\Models;

use App\Enums\CompetitionType;
use App\Enums\SeasonPosition;
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

    public function names()
    {
        return $this->hasMany(ClubName::class)->orderBy('from_year');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function trophiesByType(CompetitionType $type)
    {
        return $this->results()
            ->where('position', SeasonPosition::CHAMPION->value)
            ->whereHas('season.competition', fn($q) =>
            $q->where('type', $type->value)
            );
    }

    public function championships()
    {
        return $this->trophiesByType(CompetitionType::CHAMPIONSHIP);
    }

    public function cups()
    {
        return $this->trophiesByType(CompetitionType::CUP);
    }

    public function superCups()
    {
        return $this->trophiesByType(CompetitionType::SUPER_CUP);
    }
}

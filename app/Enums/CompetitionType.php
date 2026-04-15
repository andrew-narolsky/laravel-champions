<?php

namespace App\Enums;

enum CompetitionType: string
{
    case CHAMPIONSHIP = 'championship';
    case CUP = 'cup';
    case SUPER_CUP = 'super_cup';

    public function title(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }

    public function viewStyle(): string
    {
        return match ($this) {
            self::CHAMPIONSHIP => 'league',
            self::CUP, self::SUPER_CUP => 'cup',
        };
    }
}

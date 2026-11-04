<?php

namespace App\Enums;

/**
 * Cases are declared in tournament order — use sortOrder() for sorting.
 */
enum EuroStage: string
{
    case PRELIMINARY = 'preliminary';
    case QUALIFYING_1 = 'qualifying_1';
    case QUALIFYING_2 = 'qualifying_2';
    case QUALIFYING_3 = 'qualifying_3';
    case PLAYOFF = 'playoff';
    case GROUP = 'group';
    case LEAGUE_PHASE = 'league_phase';
    case ROUND_1 = 'round_1';
    case ROUND_2 = 'round_2';
    case GROUP_2 = 'group_2';
    case ROUND_3 = 'round_3';
    case KNOCKOUT_PLAYOFF = 'knockout_playoff';
    case ROUND_OF_32 = 'round_of_32';
    case ROUND_OF_16 = 'round_of_16';
    case QUARTER_FINAL = 'quarter_final';
    case SEMI_FINAL = 'semi_final';
    case FINAL = 'final';

    public function label(): string
    {
        return match ($this) {
            self::PRELIMINARY => 'Preliminary round',
            self::QUALIFYING_1 => '1st qualifying round',
            self::QUALIFYING_2 => '2nd qualifying round',
            self::QUALIFYING_3 => '3rd qualifying round',
            self::PLAYOFF => 'Play-off round',
            self::GROUP => 'Group stage',
            self::LEAGUE_PHASE => 'League phase',
            self::ROUND_1 => '1st round',
            self::ROUND_2 => '2nd round',
            self::GROUP_2 => 'Second group stage',
            self::ROUND_3 => '3rd round',
            self::KNOCKOUT_PLAYOFF => 'Knockout play-off',
            self::ROUND_OF_32 => '1/16 final',
            self::ROUND_OF_16 => '1/8 final',
            self::QUARTER_FINAL => 'Quarter-final',
            self::SEMI_FINAL => 'Semi-final',
            self::FINAL => 'Final',
        };
    }

    public function isGroup(): bool
    {
        return in_array($this, [self::GROUP, self::GROUP_2, self::LEAGUE_PHASE], true);
    }

    public function isQualifying(): bool
    {
        return in_array($this, [self::PRELIMINARY, self::QUALIFYING_1, self::QUALIFYING_2, self::QUALIFYING_3, self::PLAYOFF], true);
    }

    public function sortOrder(): int
    {
        return array_search($this, self::cases(), true);
    }
}

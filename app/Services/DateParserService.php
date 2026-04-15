<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;

class DateParserService
{
    public function parse(?string $value): array
    {
        if (!$value) {
            return [null, null];
        }

        if (preg_match('/^\d{4}-00-00$/', $value)) {
            return [substr($value, 0, 4), null];
        }

        if (preg_match('/^\d{4}$/', $value)) {
            return [$value, null];
        }

        try {
            $carbon = Carbon::createFromFormat('Y-m-d', $value);

            return [
                $carbon->format('Y'),
                $carbon->format('j F'),
            ];
        } catch (Exception $e) {
            return [$value, null];
        }
    }
}

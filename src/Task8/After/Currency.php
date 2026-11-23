<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task8\After;

enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';

    /** Formats an amount in cents, e.g. 123450 as "1.234,50 €" or "$1,234.50". */
    public function format(int $cents): string
    {
        $sign = $cents < 0 ? '-' : '';
        $units = intdiv(abs($cents), 100);
        $fraction = str_pad((string) (abs($cents) % 100), 2, '0', STR_PAD_LEFT);

        return match ($this) {
            self::EUR => $sign . number_format($units, 0, '', '.') . ',' . $fraction . ' €',
            self::USD => $sign . '$' . number_format($units, 0, '', ',') . '.' . $fraction,
        };
    }
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task10\After;

/**
 * Labels the first column and sums every other column that is numeric in all rows.
 */
final class TotalsRow implements SummaryRow
{
    public function cells(array $columns, array $rows): array
    {
        $cells = [];
        foreach ($columns as $i => $column) {
            $cells[] = $i === 0 ? 'TOTAL' : $this->sum(array_column($rows, $column));
        }

        return $cells;
    }

    /** @param list<string|int|float> $values */
    private function sum(array $values): string
    {
        $sum = 0;
        foreach ($values as $value) {
            if (!is_numeric($value)) {
                return '';
            }
            $sum += $value;
        }

        return (string) $sum;
    }
}

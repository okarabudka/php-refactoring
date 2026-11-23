<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task10\After;

/**
 * @phpstan-type Row array<string, string|int|float>
 */
interface SummaryRow
{
    /**
     * @param list<string> $columns
     * @param non-empty-list<Row> $rows
     *
     * @return list<string> one cell per column
     */
    public function cells(array $columns, array $rows): array;
}

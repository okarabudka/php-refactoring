<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task10\After;

use InvalidArgumentException;

/**
 * Composes one output format with any number of summary rows, instead of one subclass per combination.
 *
 * @phpstan-import-type Row from SummaryRow
 */
final readonly class ReportExporter
{
    /** @var list<SummaryRow> */
    private array $summaries;

    public function __construct(
        private Format $format,
        SummaryRow ...$summaries,
    ) {
        $this->summaries = array_values($summaries);
    }

    /** @param list<Row> $rows */
    public function export(array $rows): string
    {
        if ($rows === []) {
            throw new InvalidArgumentException('A report needs at least one row to know its columns.');
        }
        $columns = array_keys($rows[0]);

        $out = $this->format->header($columns);
        foreach ($rows as $row) {
            $out .= $this->format->row(array_map(strval(...), array_values($row)));
        }
        foreach ($this->summaries as $summary) {
            $out .= $this->format->row($summary->cells($columns, $rows));
        }

        return $out . $this->format->footer();
    }
}

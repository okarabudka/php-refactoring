<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task10\Before;

// Copied from CsvExporterWithTotals: PHP has no multiple inheritance.
class HtmlExporterWithTotals extends HtmlExporter
{
    protected function footer($rows)
    {
        $totals = [];
        foreach (array_keys($rows[0]) as $i => $column) {
            if ($i == 0) {
                $totals[] = 'TOTAL';
                continue;
            }
            $sum = 0;
            $numeric = true;
            foreach ($rows as $row) {
                if (!is_numeric($row[$column])) {
                    $numeric = false;
                    break;
                }
                $sum += $row[$column];
            }
            $totals[] = $numeric ? $sum : '';
        }
        return '<tr><td>' . implode('</td><td>', $totals) . "</td></tr>\n" . parent::footer($rows);
    }
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task10\Before;

class CsvExporter extends ReportExporter
{
    protected function header($columns)
    {
        return implode(',', $columns) . "\n";
    }

    protected function row($row)
    {
        return implode(',', $row) . "\n";
    }
}

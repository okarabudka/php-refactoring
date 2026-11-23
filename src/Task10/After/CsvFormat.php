<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task10\After;

final class CsvFormat implements Format
{
    public function header(array $columns): string
    {
        return $this->row($columns);
    }

    public function row(array $cells): string
    {
        return implode(',', $cells) . "\n";
    }

    public function footer(): string
    {
        return '';
    }
}

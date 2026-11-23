<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task10\After;

interface Format
{
    /** @param list<string> $columns */
    public function header(array $columns): string;

    /** @param list<string> $cells */
    public function row(array $cells): string;

    public function footer(): string;
}

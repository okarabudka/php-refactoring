<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task10\After;

final class HtmlFormat implements Format
{
    public function header(array $columns): string
    {
        return '<table>' . $this->cells('th', $columns);
    }

    public function row(array $cells): string
    {
        return $this->cells('td', $cells);
    }

    public function footer(): string
    {
        return "</table>\n";
    }

    /** @param list<string> $cells */
    private function cells(string $tag, array $cells): string
    {
        $escaped = array_map(static fn(string $cell): string => htmlspecialchars($cell, ENT_QUOTES, 'UTF-8'), $cells);

        return "<tr><{$tag}>" . implode("</{$tag}><{$tag}>", $escaped) . "</{$tag}></tr>\n";
    }
}

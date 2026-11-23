<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task10\Before;

class HtmlExporter extends ReportExporter
{
    protected function header($columns)
    {
        return '<table><tr><th>' . implode('</th><th>', $columns) . "</th></tr>\n";
    }

    protected function row($row)
    {
        return '<tr><td>' . implode('</td><td>', $row) . "</td></tr>\n";
    }

    protected function footer($rows)
    {
        return "</table>\n";
    }
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task10\Before;

abstract class ReportExporter
{
    public function export($rows)
    {
        $columns = array_keys($rows[0]);
        $out = $this->header($columns);
        foreach ($rows as $row) {
            $out .= $this->row($row);
        }
        $out .= $this->footer($rows);
        return $out;
    }

    abstract protected function header($columns);

    abstract protected function row($row);

    protected function footer($rows)
    {
        return '';
    }
}

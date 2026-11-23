<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task9\Before;

// Stands in for a static file logger; keeps lines in memory so the tests can read them.
class Logger
{
    public static $lines = [];

    public static function log($message)
    {
        self::$lines[] = date('Y-m-d H:i:s') . ' ' . $message;
    }
}

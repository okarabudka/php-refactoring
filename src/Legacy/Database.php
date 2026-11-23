<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Legacy;

use PDO;

/**
 * The global connection the "before" code reaches for, as legacy code often does.
 */
final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        return self::$connection ??= new PDO('mysql:host=localhost;dbname=shop', 'root', '');
    }

    /**
     * Static setter: the seam that lets characterization tests swap in SQLite (Feathers, "Introduce Static Setter").
     */
    public static function setConnection(?PDO $connection): void
    {
        self::$connection = $connection;
    }
}

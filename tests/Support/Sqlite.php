<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Support;

use PDO;

final class Sqlite
{
    public static function inMemory(string ...$statements): PDO
    {
        $pdo = new PDO('sqlite::memory:', options: [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        foreach ($statements as $statement) {
            $pdo->exec($statement);
        }

        return $pdo;
    }
}

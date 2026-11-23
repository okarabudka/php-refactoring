<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task6;

use Override;
use PDO;
use PDOStatement;

final class QueryCountingPdo extends PDO
{
    public int $prepared = 0;

    /** @param array<mixed> $options */
    #[Override]
    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        ++$this->prepared;

        return parent::prepare($query, $options);
    }
}

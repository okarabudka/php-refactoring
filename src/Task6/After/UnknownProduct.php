<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task6\After;

use RuntimeException;

final class UnknownProduct extends RuntimeException
{
    /** @param list<int> $productIds */
    public static function ids(array $productIds): self
    {
        return new self('Unknown products in cart: ' . implode(', ', $productIds));
    }
}

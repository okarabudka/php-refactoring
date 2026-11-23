<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task8\After;

use InvalidArgumentException;

final readonly class LineItem
{
    public function __construct(
        public Money $price,
        public int $quantity,
    ) {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Quantity must be at least 1.');
        }
    }

    public function total(): Money
    {
        return $this->price->multiply($this->quantity);
    }
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task6\After;

use InvalidArgumentException;

final class Cart
{
    /** @param array<int, int> $quantities product id => quantity */
    public function __construct(private array $quantities = []) {}

    public function add(int $productId, int $quantity): void
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Quantity must be at least 1.');
        }
        $this->quantities[$productId] = ($this->quantities[$productId] ?? 0) + $quantity;
    }

    /** @return array<int, int> product id => quantity */
    public function quantities(): array
    {
        return $this->quantities;
    }

    /** @return list<int> */
    public function productIds(): array
    {
        return array_keys($this->quantities);
    }
}

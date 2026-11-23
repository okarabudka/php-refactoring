<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task1\After;

use InvalidArgumentException;

final class OrderItem
{
    private int $quantity;

    public function __construct(
        public readonly string $name,
        public readonly float $price,
        int $quantity,
    ) {
        if ($price < 0) {
            throw new InvalidArgumentException("Price of {$name} must not be negative.");
        }
        $this->quantity = self::positive($quantity);
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function total(): float
    {
        return $this->price * $this->quantity;
    }

    public function increaseQuantity(int $quantity): void
    {
        $this->quantity += self::positive($quantity);
    }

    private static function positive(int $quantity): int
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Quantity must be at least 1.');
        }

        return $quantity;
    }
}

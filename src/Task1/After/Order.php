<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task1\After;

use InvalidArgumentException;

final class Order
{
    /** @var array<string, OrderItem> keyed by item name */
    private array $items = [];

    public function addItem(string $name, float $price, int $quantity): void
    {
        $item = $this->items[$name] ?? null;
        if ($item === null) {
            $this->items[$name] = new OrderItem($name, $price, $quantity);

            return;
        }

        if ($item->price !== $price) {
            throw new InvalidArgumentException("{$name} is already in the order at a different price.");
        }
        $item->increaseQuantity($quantity);
    }

    public function getTotalPrice(): float
    {
        return array_sum(array_map(static fn(OrderItem $item): float => $item->total(), $this->items));
    }

    public function receipt(): string
    {
        $receipt = "Receipt:\n";
        foreach ($this->items as $item) {
            $receipt .= "{$item->name} x{$item->quantity()} - \${$item->total()}\n";
        }

        return $receipt . "Total: \${$this->getTotalPrice()}\n";
    }

    public function printReceipt(): void
    {
        echo $this->receipt();
    }
}

<?php

namespace src\refactors\task1\after;

class Order
{
    private $items = [];
    private float $totalPrice = 0;

    public function addItem(string $name, float $price, int $quantity): void
    {
        $existingItem = $this->findItem($name);

        if ($existingItem) {
            $existingItem->increaseQuantity($quantity);
        } else {
            $this->items[$name] = new OrderItem($name, $price, $quantity);
        }

        $this->totalPrice += $price * $quantity;
    }

    private function findItem(string $name): ?OrderItem
    {
        return $this->items[ $name ] ?? null;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    public function printReceipt(): void
    {
        echo "Receipt:\n";
        foreach ($this->items as $item) {
            echo "{$item->getName()} x{$item->getQuantity()} - \${$item->getTotal()}\n";
        }
        echo "Total: \${$this->totalPrice}\n";
    }
}

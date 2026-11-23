<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task3\Before;

class Invoice
{
    public function __construct(private array $items) {}

    public function getTotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $price = $item['price'];
            $qty = $item['quantity'];
            $discount = $item['discount'];
            $tax = $item['tax'];

            $line = $price * $qty;
            $line = $line - ($line * $discount);
            $line = $line + ($line * $tax);

            $total += $line;
        }
        return $total;
    }
}

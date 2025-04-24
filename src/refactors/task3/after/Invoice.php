<?php

namespace src\refactors\task3\after;

use InvalidArgumentException;

class Invoice
{
    private array $items = [];

    public function __construct(array $items = []) {
        $this->setItems($items);
    }

    private function setItems(array $items): void {
        foreach ($items as $item) {

            if (!is_array($item)) {
                throw new InvalidArgumentException('Each item must be an array.');
            }

            $this->items[] = InvoiceItem::fromArray($item);
        }
    }

    public function getTotal(): float {
        $total = 0;

        foreach ($this->items as $item) {
            $total += $item->calculateTotal();
        }

        return $total;
    }
}

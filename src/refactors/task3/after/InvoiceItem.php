<?php

namespace src\refactors\task3\after;

class InvoiceItem
{
    private function __construct(
        private readonly float $price,
        private readonly int $quantity,
        private readonly float $discount = 0.0,
        private readonly float $tax = 0.0
    )
    {}

    public static function fromArray(array $item): self
    {
        InvoiceItemValidator::validate($item);

        return new self(
            (float)$item['price'],
            (int)$item['quantity'],
            (float)$item['discount'],
            (float)$item['tax']
        );
    }

    public function calculateTotal(): float {
        $subtotal = $this->price * $this->quantity;
        $discounted = $subtotal * (1 - $this->discount);
        return $discounted * (1 + $this->tax);
    }
}

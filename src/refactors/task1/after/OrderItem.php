<?php

namespace src\refactors\task1\after;

class OrderItem
{
    public function __construct(
        private readonly string $name,
        private readonly float  $price,
        private int             $quantity,
    )
    {

    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTotal(): int
    {
        return $this->price * $this->quantity;
    }

    public function increaseQuantity(int $quantity): void {
        $this->quantity += $quantity;
    }
}

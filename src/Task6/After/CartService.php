<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task6\After;

final readonly class CartService
{
    public function __construct(
        private CartStorage $storage,
        private PriceRepository $prices,
    ) {}

    public function addProduct(int $productId, int $quantity): void
    {
        $cart = $this->storage->load();
        $cart->add($productId, $quantity);
        $this->storage->save($cart);
    }

    public function getTotal(): float
    {
        $cart = $this->storage->load();
        $prices = $this->prices->pricesFor($cart->productIds());

        $unknown = array_values(array_diff($cart->productIds(), array_keys($prices)));
        if ($unknown !== []) {
            throw UnknownProduct::ids($unknown);
        }

        $total = 0.0;
        foreach ($cart->quantities() as $productId => $quantity) {
            $total += $prices[$productId] * $quantity;
        }

        return $total;
    }
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task6;

use Okarabudka\Refactoring\Task6\After\Cart;
use Okarabudka\Refactoring\Task6\After\CartStorage;

final class InMemoryCartStorage implements CartStorage
{
    private Cart $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    public function load(): Cart
    {
        return clone $this->cart;
    }

    public function save(Cart $cart): void
    {
        $this->cart = clone $cart;
    }
}

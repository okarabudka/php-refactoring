<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task6\After;

use LogicException;

/**
 * Uses the same session key as the old code, so carts in existing sessions survive the deploy.
 */
final class SessionCartStorage implements CartStorage
{
    private const string KEY = 'cart';

    public function load(): Cart
    {
        $this->assertSessionIsActive();
        $stored = $_SESSION[self::KEY] ?? [];

        $quantities = [];
        foreach (is_array($stored) ? $stored : [] as $productId => $quantity) {
            if (is_int($productId) && is_int($quantity)) {
                $quantities[$productId] = $quantity;
            }
        }

        return new Cart($quantities);
    }

    public function save(Cart $cart): void
    {
        $this->assertSessionIsActive();
        $_SESSION[self::KEY] = $cart->quantities();
    }

    private function assertSessionIsActive(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new LogicException('Start the session before using the cart.');
        }
    }
}

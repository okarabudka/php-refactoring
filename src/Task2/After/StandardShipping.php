<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task2\After;

final class StandardShipping implements ShippingMethod
{
    public function type(): string
    {
        return 'standard';
    }

    public function cost(float $weight): float
    {
        return $weight * 1.2;
    }
}

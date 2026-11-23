<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task2\After;

final class OvernightShipping implements ShippingMethod
{
    public function type(): string
    {
        return 'overnight';
    }

    public function cost(float $weight): float
    {
        return $weight * 5 + 20;
    }
}

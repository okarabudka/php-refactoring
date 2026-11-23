<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task2\After;

final class ExpressShipping implements ShippingMethod
{
    public function type(): string
    {
        return 'express';
    }

    public function cost(float $weight): float
    {
        return $weight * 2.5 + 10;
    }
}

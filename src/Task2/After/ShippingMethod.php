<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task2\After;

interface ShippingMethod
{
    /** The key callers use to choose this method, e.g. "standard". */
    public function type(): string;

    public function cost(float $weight): float;
}

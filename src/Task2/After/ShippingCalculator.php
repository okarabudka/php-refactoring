<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task2\After;

use InvalidArgumentException;

/**
 * Knows no shipping type by name: new types are registered by whoever builds the calculator.
 */
final class ShippingCalculator
{
    /** @var array<string, ShippingMethod> */
    private array $methods = [];

    public function __construct(ShippingMethod ...$methods)
    {
        foreach ($methods as $method) {
            if (isset($this->methods[$method->type()])) {
                throw new InvalidArgumentException("Shipping type {$method->type()} is registered twice.");
            }
            $this->methods[$method->type()] = $method;
        }
    }

    public function calculate(string $type, float $weight): float
    {
        $method = $this->methods[$type] ?? throw UnknownShippingType::named($type);

        return $method->cost($weight);
    }
}

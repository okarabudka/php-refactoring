<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task5\After;

final readonly class Product
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price,
    ) {}
}

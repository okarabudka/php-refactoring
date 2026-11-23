<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task7\After;

final readonly class Customer
{
    public function __construct(
        public bool $active,
        public CustomerTier $tier,
        public int $yearsAsCustomer,
        public int $orderCount,
    ) {}

    public function isFirstOrder(): bool
    {
        return $this->orderCount === 0;
    }
}

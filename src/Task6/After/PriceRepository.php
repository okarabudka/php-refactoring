<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task6\After;

interface PriceRepository
{
    /**
     * @param list<int> $productIds
     *
     * @return array<int, float> product id => price; unknown ids are missing
     */
    public function pricesFor(array $productIds): array;
}

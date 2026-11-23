<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task5\After;

interface ProductRepository
{
    public function find(int $id): ?Product;
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task6\After;

interface CartStorage
{
    public function load(): Cart;

    public function save(Cart $cart): void;
}

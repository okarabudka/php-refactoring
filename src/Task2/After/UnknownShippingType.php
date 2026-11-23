<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task2\After;

use InvalidArgumentException;

final class UnknownShippingType extends InvalidArgumentException
{
    public static function named(string $type): self
    {
        return new self("Unknown shipping type: {$type}");
    }
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Support;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class FrozenClock implements ClockInterface
{
    public function __construct(private DateTimeImmutable $now) {}

    public static function at(string $time): self
    {
        return new self(new DateTimeImmutable($time));
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }
}

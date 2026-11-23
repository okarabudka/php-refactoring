<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task8\After;

final class Checkout
{
    public function total(Currency $currency, LineItem ...$lines): Money
    {
        return array_reduce(
            $lines,
            static fn(Money $sum, LineItem $line): Money => $sum->add($line->total()),
            Money::zero($currency),
        );
    }

    /** @return list<Money> */
    public function split(Money $amount, int $parts): array
    {
        return $amount->allocate($parts);
    }
}

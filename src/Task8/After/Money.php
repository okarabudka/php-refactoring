<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task8\After;

use InvalidArgumentException;

/**
 * An amount in cents with its currency; immutable, so it can be shared and compared freely.
 */
final readonly class Money
{
    private function __construct(
        public int $cents,
        public Currency $currency,
    ) {}

    public static function ofCents(int $cents, Currency $currency): self
    {
        return new self($cents, $currency);
    }

    /** @param string $amount a decimal string with at most two decimals, e.g. "19.99"; never a float */
    public static function of(string $amount, Currency $currency): self
    {
        if (preg_match('/^(-?)(\d+)(?:\.(\d{1,2}))?$/', $amount, $match) !== 1) {
            throw new InvalidArgumentException("Not a money amount: {$amount}");
        }
        $cents = (int) $match[2] * 100 + (int) str_pad($match[3] ?? '', 2, '0');

        return new self($match[1] === '-' ? -$cents : $cents, $currency);
    }

    public static function zero(Currency $currency): self
    {
        return new self(0, $currency);
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->cents + $other->cents, $this->currency);
    }

    public function multiply(int $factor): self
    {
        return new self($this->cents * $factor, $this->currency);
    }

    /**
     * Splits into $parts amounts that add up to exactly this one; the first parts take the leftover cents.
     *
     * @return list<self>
     */
    public function allocate(int $parts): array
    {
        if ($parts < 1) {
            throw new InvalidArgumentException('Allocate into at least one part.');
        }
        $share = intdiv($this->cents, $parts);
        $leftover = $this->cents - $share * $parts;
        $step = $leftover < 0 ? -1 : 1;

        $result = [];
        for ($i = 0; $i < $parts; ++$i) {
            $result[] = new self($share + ($i < abs($leftover) ? $step : 0), $this->currency);
        }

        return $result;
    }

    public function equals(self $other): bool
    {
        return $this->currency === $other->currency && $this->cents === $other->cents;
    }

    public function format(): string
    {
        return $this->currency->format($this->cents);
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw CurrencyMismatch::between($this->currency, $other->currency);
        }
    }
}

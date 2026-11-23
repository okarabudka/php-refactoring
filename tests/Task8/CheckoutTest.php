<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task8;

use Closure;
use InvalidArgumentException;
use Okarabudka\Refactoring\Task8\After;
use Okarabudka\Refactoring\Task8\After\Currency;
use Okarabudka\Refactoring\Task8\After\CurrencyMismatch;
use Okarabudka\Refactoring\Task8\After\LineItem;
use Okarabudka\Refactoring\Task8\After\Money;
use Okarabudka\Refactoring\Task8\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-type Line array{string, int}
 */
final class CheckoutTest extends TestCase
{
    /**
     * Each closure returns the formatted total of the lines (price as decimal string, quantity) in one currency.
     *
     * @return iterable<string, array{Closure(list<Line>, string): string}>
     */
    public static function formattedTotals(): iterable
    {
        yield 'before' => [self::beforeTotal(...)];
        yield 'after' => [self::afterTotal(...)];
    }

    /** @param list<Line> $lines */
    private static function beforeTotal(array $lines, string $currency): string
    {
        $rows = [];
        foreach ($lines as [$price, $quantity]) {
            $rows[] = ['price' => (float) $price, 'qty' => $quantity, 'currency' => $currency];
        }
        $checkout = new Before\Checkout();
        $formatted = $checkout->format($checkout->total($rows), $currency);
        self::assertIsString($formatted);

        return $formatted;
    }

    /** @param list<Line> $lines */
    private static function afterTotal(array $lines, string $currency): string
    {
        $currency = Currency::from($currency);
        $items = [];
        foreach ($lines as [$price, $quantity]) {
            $items[] = new LineItem(Money::of($price, $currency), $quantity);
        }

        return new After\Checkout()->total($currency, ...$items)->format();
    }

    /** @return iterable<string, array{Closure(list<Line>, string): string, list<Line>, string, string}> */
    public static function totals(): iterable
    {
        foreach (self::formattedTotals() as $name => [$total]) {
            yield "{$name}, euro" => [$total, [['19.99', 2], ['5.00', 1]], 'EUR', '44,98 €'];
            yield "{$name}, dollar with thousands" => [$total, [['999.50', 3]], 'USD', '$2,998.50'];
            yield "{$name}, empty" => [$total, [], 'EUR', '0,00 €'];
            yield "{$name}, cents that do not add up in binary" => [$total, [['0.10', 1], ['0.20', 1]], 'USD', '$0.30'];
        }
    }

    /**
     * @param Closure(list<Line>, string): string $total
     * @param list<Line> $lines
     */
    #[DataProvider('totals')]
    public function testFormattedTotal(Closure $total, array $lines, string $currency, string $expected): void
    {
        self::assertSame($expected, $total($lines, $currency));
    }

    public function testBeforeTotalIsAnInexactFloat(): void
    {
        $total = new Before\Checkout()->total([
            ['price' => 0.1, 'qty' => 1, 'currency' => 'USD'],
            ['price' => 0.2, 'qty' => 1, 'currency' => 'USD'],
        ]);

        // Only the formatting rounds this back to "0.30".
        self::assertNotSame(0.3, $total);
    }

    public function testBeforeSplitLosesACent(): void
    {
        $shares = new Before\Checkout()->split(100.0, 3);

        self::assertSame([33.33, 33.33, 33.33], $shares);
    }

    public function testAfterSplitKeepsEveryCent(): void
    {
        $shares = new After\Checkout()->split(Money::of('100', Currency::EUR), 3);

        self::assertSame(['33,34 €', '33,33 €', '33,33 €'], array_map(static fn(Money $m): string => $m->format(), $shares));
    }

    public function testAllocatingANegativeAmountKeepsEveryCent(): void
    {
        $shares = Money::ofCents(-100, Currency::USD)->allocate(3);

        self::assertSame([-34, -33, -33], array_map(static fn(Money $m): int => $m->cents, $shares));
    }

    public function testBeforeSilentlyAddsDifferentCurrencies(): void
    {
        $total = new Before\Checkout()->total([
            ['price' => 10.0, 'qty' => 1, 'currency' => 'EUR'],
            ['price' => 10.0, 'qty' => 1, 'currency' => 'USD'],
        ]);

        self::assertSame(20.0, $total);
    }

    public function testAfterRefusesToAddDifferentCurrencies(): void
    {
        $this->expectException(CurrencyMismatch::class);
        new After\Checkout()->total(
            Currency::EUR,
            new LineItem(Money::of('10', Currency::EUR), 1),
            new LineItem(Money::of('10', Currency::USD), 1),
        );
    }

    /** @return iterable<string, array{string}> */
    public static function invalidAmounts(): iterable
    {
        yield 'three decimals' => ['1.999'];
        yield 'comma' => ['1,99'];
        yield 'empty' => [''];
        yield 'exponent' => ['1e3'];
    }

    #[DataProvider('invalidAmounts')]
    public function testMoneyRejectsAmbiguousInput(string $amount): void
    {
        $this->expectException(InvalidArgumentException::class);
        Money::of($amount, Currency::EUR);
    }

    public function testMoneyParsesAndComparesByValue(): void
    {
        self::assertTrue(Money::of('19.9', Currency::EUR)->equals(Money::ofCents(1990, Currency::EUR)));
        self::assertFalse(Money::of('19.90', Currency::EUR)->equals(Money::of('19.90', Currency::USD)));
        self::assertSame('-1.234,05 €', Money::of('-1234.05', Currency::EUR)->format());
    }
}

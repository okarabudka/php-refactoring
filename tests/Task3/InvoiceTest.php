<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task3;

use Closure;
use InvalidArgumentException;
use Okarabudka\Refactoring\Task3\After;
use Okarabudka\Refactoring\Task3\After\InvoiceItem;
use Okarabudka\Refactoring\Task3\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TypeError;

final class InvoiceTest extends TestCase
{
    /** @return iterable<string, array{Closure(array<mixed>): mixed}> */
    public static function totals(): iterable
    {
        yield 'before' => [static fn(array $rows): mixed => new Before\Invoice($rows)->getTotal()];
        yield 'after' => [static fn(array $rows): float => After\Invoice::fromArray($rows)->total()];
    }

    /** @param Closure(array<mixed>): mixed $total */
    #[DataProvider('totals')]
    public function testDiscountIsAppliedBeforeTax(Closure $total): void
    {
        $rows = [
            ['price' => 100, 'quantity' => 2, 'discount' => 0.1, 'tax' => 0.2], // 200 - 10 % = 180, + 20 % = 216
            ['price' => 50, 'quantity' => 1, 'discount' => 0, 'tax' => 0],
        ];

        self::assertEqualsWithDelta(266.0, $total($rows), 1e-9);
    }

    /** @param Closure(array<mixed>): mixed $total */
    #[DataProvider('totals')]
    public function testEmptyInvoiceIsZero(Closure $total): void
    {
        self::assertEqualsWithDelta(0.0, $total([]), 1e-9);
    }

    /** @param Closure(array<mixed>): mixed $total */
    #[DataProvider('totals')]
    public function testZeroQuantityLineAddsNothing(Closure $total): void
    {
        self::assertEqualsWithDelta(0.0, $total([['price' => 9.99, 'quantity' => 0, 'discount' => 0.5, 'tax' => 0.2]]), 1e-9);
    }

    public function testBeforeFailsLateOnAFlatRow(): void
    {
        $invoice = new Before\Invoice(['price' => '100', 'quantity' => '2', 'discount' => '0.1', 'tax' => '0.2']);

        // Construction succeeds; the wrong shape only surfaces when the total is computed.
        $this->expectException(TypeError::class);
        $invoice->getTotal();
    }

    public function testAfterRejectsAFlatRowOnConstruction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        After\Invoice::fromArray(['price' => 100, 'quantity' => 2, 'discount' => 0.1, 'tax' => 0.2]);
    }

    /** @return iterable<string, array{array<mixed>}> */
    public static function invalidRows(): iterable
    {
        yield 'missing tax' => [['price' => 1, 'quantity' => 1, 'discount' => 0]];
        yield 'negative price' => [['price' => -1, 'quantity' => 1, 'discount' => 0, 'tax' => 0]];
        yield 'fractional quantity' => [['price' => 1, 'quantity' => 1.5, 'discount' => 0, 'tax' => 0]];
        yield 'discount above 100 %' => [['price' => 1, 'quantity' => 1, 'discount' => 1.5, 'tax' => 0]];
        yield 'numeric string price' => [['price' => '1', 'quantity' => 1, 'discount' => 0, 'tax' => 0]];
    }

    /** @param array<mixed> $row */
    #[DataProvider('invalidRows')]
    public function testAfterRejectsInvalidRows(array $row): void
    {
        $this->expectException(InvalidArgumentException::class);
        InvoiceItem::fromArray($row);
    }
}

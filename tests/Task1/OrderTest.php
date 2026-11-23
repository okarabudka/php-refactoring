<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task1;

use Closure;
use InvalidArgumentException;
use Okarabudka\Refactoring\Task1\After;
use Okarabudka\Refactoring\Task1\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    /** @return iterable<string, array{Closure(): (Before\Order|After\Order)}> */
    public static function implementations(): iterable
    {
        yield 'before' => [static fn(): Before\Order => new Before\Order()];
        yield 'after' => [static fn(): After\Order => new After\Order()];
    }

    /** @param Closure(): (Before\Order|After\Order) $newOrder */
    #[DataProvider('implementations')]
    public function testReceiptMergesRepeatedItemsAndKeepsInsertionOrder(Closure $newOrder): void
    {
        $order = $newOrder();
        $order->addItem('Apple', 1.5, 2);
        $order->addItem('Banana', 1.0, 3);
        $order->addItem('Apple', 1.5, 1);

        $this->expectOutputString("Receipt:\nApple x3 - \$4.5\nBanana x3 - \$3\nTotal: \$7.5\n");
        $order->printReceipt();
    }

    /** @param Closure(): (Before\Order|After\Order) $newOrder */
    #[DataProvider('implementations')]
    public function testTotalPriceSumsAllItems(Closure $newOrder): void
    {
        $order = $newOrder();
        $order->addItem('Apple', 2.0, 1);
        $order->addItem('Banana', 1.5, 2);
        $order->addItem('Apple', 2.0, 2);

        self::assertEquals(9.0, $order->getTotalPrice());
    }

    /** @param Closure(): (Before\Order|After\Order) $newOrder */
    #[DataProvider('implementations')]
    public function testEmptyOrderPrintsZeroTotal(Closure $newOrder): void
    {
        $this->expectOutputString("Receipt:\nTotal: \$0\n");
        $newOrder()->printReceipt();
    }

    public function testBeforeSilentlyMixesPricesOfTheSameItem(): void
    {
        $order = new Before\Order();
        $order->addItem('Apple', 1.5, 2);
        $order->addItem('Apple', 2.0, 1);

        // The line keeps price 1.5 while its total uses both prices: the quirk the refactoring removes.
        $this->expectOutputString("Receipt:\nApple x3 - \$5\nTotal: \$5\n");
        $order->printReceipt();
    }

    public function testAfterRejectsTheSameItemAtADifferentPrice(): void
    {
        $order = new After\Order();
        $order->addItem('Apple', 1.5, 2);

        $this->expectException(InvalidArgumentException::class);
        $order->addItem('Apple', 2.0, 1);
    }

    public function testAfterRejectsNonPositiveQuantity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new After\Order()->addItem('Apple', 1.5, 0);
    }
}

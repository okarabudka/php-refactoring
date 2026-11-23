<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task7;

use Okarabudka\Refactoring\Task7\After;
use Okarabudka\Refactoring\Task7\After\Customer;
use Okarabudka\Refactoring\Task7\After\CustomerTier;
use Okarabudka\Refactoring\Task7\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-type CustomerRow array{active: bool, type: string, years: int, orders: int}
 */
final class DiscountCalculatorTest extends TestCase
{
    /** @return iterable<string, array{CustomerRow, float, bool, ?string, float}> */
    public static function cases(): iterable
    {
        $vip = ['active' => true, 'type' => 'vip', 'years' => 1, 'orders' => 12];
        $newVip = ['active' => true, 'type' => 'vip', 'years' => 0, 'orders' => 0];
        $regular = ['active' => true, 'type' => 'regular', 'years' => 2, 'orders' => 3];
        $loyal = ['active' => true, 'type' => 'regular', 'years' => 6, 'orders' => 40];
        $fiveYears = ['active' => true, 'type' => 'regular', 'years' => 5, 'orders' => 40];
        $newcomer = ['active' => true, 'type' => 'regular', 'years' => 0, 'orders' => 0];
        $loyalWithoutOrders = ['active' => true, 'type' => 'regular', 'years' => 6, 'orders' => 0];
        $inactiveVip = ['active' => false, 'type' => 'vip', 'years' => 9, 'orders' => 0];

        yield 'inactive customer gets nothing' => [$inactiveVip, 200.0, true, 'WELCOME10', 200.0];
        yield 'vip above 100' => [$vip, 200.0, false, null, 160.0];
        yield 'vip at exactly 100' => [$vip, 100.0, false, null, 90.0];
        yield 'regular, more than 5 years' => [$loyal, 100.0, false, null, 95.0];
        yield 'regular, exactly 5 years' => [$fiveYears, 100.0, false, null, 100.0];
        yield 'regular on black friday' => [$regular, 100.0, true, null, 90.0];
        yield 'vip on black friday hits the cap' => [$vip, 200.0, true, null, 140.0];
        yield 'everything at once stays capped' => [$newVip, 200.0, true, 'WELCOME10', 140.0];
        yield 'welcome coupon on the first order' => [$newcomer, 49.99, false, 'WELCOME10', 44.99];
        yield 'welcome coupon after the first order' => [$regular, 49.99, false, 'WELCOME10', 49.99];
        yield 'unknown coupon' => [$newcomer, 49.99, false, 'SPRING', 49.99];
        yield 'loyalty, black friday and coupon add up' => [$loyalWithoutOrders, 19.99, true, 'WELCOME10', 14.99];
        yield 'result is rounded to cents' => [$loyal, 10.01, false, null, 9.51];
    }

    /** @param CustomerRow $customer */
    #[DataProvider('cases')]
    public function testBefore(array $customer, float $amount, bool $blackFriday, ?string $coupon, float $expected): void
    {
        $price = new Before\DiscountCalculator()->calculate($customer, $amount, $blackFriday, $coupon);

        self::assertSame($expected, $price);
    }

    /** @param CustomerRow $customer */
    #[DataProvider('cases')]
    public function testAfter(array $customer, float $amount, bool $blackFriday, ?string $coupon, float $expected): void
    {
        $calculator = new After\DiscountCalculator();
        $customer = new Customer(
            $customer['active'],
            CustomerTier::from($customer['type']),
            $customer['years'],
            $customer['orders'],
        );

        $price = $blackFriday
            ? $calculator->blackFridayPrice($customer, $amount, $coupon)
            : $calculator->price($customer, $amount, $coupon);

        self::assertSame($expected, $price);
    }
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task2;

use Exception;
use InvalidArgumentException;
use Okarabudka\Refactoring\Task2\After;
use Okarabudka\Refactoring\Task2\After\ExpressShipping;
use Okarabudka\Refactoring\Task2\After\OvernightShipping;
use Okarabudka\Refactoring\Task2\After\ShippingMethod;
use Okarabudka\Refactoring\Task2\After\StandardShipping;
use Okarabudka\Refactoring\Task2\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ShippingCalculatorTest extends TestCase
{
    /** @return iterable<string, array{Before\ShippingCalculator|After\ShippingCalculator}> */
    public static function calculators(): iterable
    {
        yield 'before' => [new Before\ShippingCalculator()];
        yield 'after' => [self::afterCalculator()];
    }

    /** @return iterable<string, array{Before\ShippingCalculator|After\ShippingCalculator, string, float, float}> */
    public static function costs(): iterable
    {
        foreach (self::calculators() as $name => [$calculator]) {
            yield "{$name}, standard" => [$calculator, 'standard', 2.0, 2.4];
            yield "{$name}, express" => [$calculator, 'express', 2.0, 15.0];
            yield "{$name}, overnight" => [$calculator, 'overnight', 2.0, 30.0];
            yield "{$name}, weightless express" => [$calculator, 'express', 0.0, 10.0];
        }
    }

    #[DataProvider('costs')]
    public function testCostDependsOnTypeAndWeight(
        Before\ShippingCalculator|After\ShippingCalculator $calculator,
        string $type,
        float $weight,
        float $expected,
    ): void {
        self::assertEqualsWithDelta($expected, $calculator->calculate($type, $weight), 1e-9);
    }

    #[DataProvider('calculators')]
    public function testUnknownTypeIsRejected(Before\ShippingCalculator|After\ShippingCalculator $calculator): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown shipping type');

        $calculator->calculate('pigeon', 2.0);
    }

    public function testNewTypeNeedsNoChangeInTheCalculator(): void
    {
        $pickup = new class implements ShippingMethod {
            public function type(): string
            {
                return 'pickup';
            }

            public function cost(float $weight): float
            {
                return 0.0;
            }
        };

        $calculator = new After\ShippingCalculator(new StandardShipping(), $pickup);

        self::assertSame(0.0, $calculator->calculate('pickup', 5.0));
        self::assertEqualsWithDelta(6.0, $calculator->calculate('standard', 5.0), 1e-9);
    }

    public function testATypeCannotBeRegisteredTwice(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new After\ShippingCalculator(new StandardShipping(), new StandardShipping());
    }

    private static function afterCalculator(): After\ShippingCalculator
    {
        return new After\ShippingCalculator(new StandardShipping(), new ExpressShipping(), new OvernightShipping());
    }
}

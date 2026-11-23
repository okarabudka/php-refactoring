<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task6;

use Closure;
use InvalidArgumentException;
use Okarabudka\Refactoring\Legacy\Database;
use Okarabudka\Refactoring\Task6\After\CartService;
use Okarabudka\Refactoring\Task6\After\PdoPriceRepository;
use Okarabudka\Refactoring\Task6\After\SessionCartStorage;
use Okarabudka\Refactoring\Task6\After\UnknownProduct;
use Okarabudka\Refactoring\Task6\Before;
use PDO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CartTest extends TestCase
{
    private QueryCountingPdo $pdo;

    protected function setUp(): void
    {
        $this->pdo = new QueryCountingPdo('sqlite::memory:');
        $this->pdo->exec('CREATE TABLE products (id INTEGER PRIMARY KEY, price REAL NOT NULL)');
        $this->pdo->exec('INSERT INTO products VALUES (1, 2.5), (2, 10.0), (3, 0.75)');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        Database::setConnection(null);
        $_SESSION = [];
    }

    /** @return iterable<string, array{Closure(PDO): (Before\Cart|CartService)}> */
    public static function carts(): iterable
    {
        yield 'before' => [self::beforeCart(...)];
        yield 'after' => [self::afterCart(...)];
    }

    private static function beforeCart(PDO $pdo): Before\Cart
    {
        Database::setConnection($pdo);

        return new Before\Cart();
    }

    private static function afterCart(PDO $pdo): CartService
    {
        return new CartService(new SessionCartStorage(), new PdoPriceRepository($pdo));
    }

    /** @param Closure(PDO): (Before\Cart|CartService) $newCart */
    #[DataProvider('carts')]
    public function testTotalSumsPriceTimesQuantity(Closure $newCart): void
    {
        $cart = $newCart($this->pdo);
        $cart->addProduct(1, 2);
        $cart->addProduct(2, 1);
        $cart->addProduct(3, 4);

        self::assertEqualsWithDelta(18.0, $cart->getTotal(), 1e-9);
    }

    /** @param Closure(PDO): (Before\Cart|CartService) $newCart */
    #[DataProvider('carts')]
    public function testAddingTheSameProductAccumulatesTheQuantity(Closure $newCart): void
    {
        $cart = $newCart($this->pdo);
        $cart->addProduct(1, 1);
        $cart->addProduct(1, 3);

        self::assertSame([1 => 4], $_SESSION['cart']);
        self::assertEqualsWithDelta(10.0, $cart->getTotal(), 1e-9);
    }

    /** @param Closure(PDO): (Before\Cart|CartService) $newCart */
    #[DataProvider('carts')]
    public function testEmptyCartCostsNothing(Closure $newCart): void
    {
        self::assertEqualsWithDelta(0.0, $newCart($this->pdo)->getTotal(), 1e-9);
    }

    /** @param Closure(PDO): (Before\Cart|CartService) $newCart */
    #[DataProvider('carts')]
    public function testCartWrittenByTheOtherVersionIsRead(Closure $newCart): void
    {
        $_SESSION['cart'] = [2 => 3];

        self::assertEqualsWithDelta(30.0, $newCart($this->pdo)->getTotal(), 1e-9);
    }

    public function testBeforeRunsOneQueryPerCartLine(): void
    {
        $cart = self::beforeCart($this->pdo);
        $cart->addProduct(1, 1);
        $cart->addProduct(2, 1);
        $cart->addProduct(3, 1);

        $cart->getTotal();

        self::assertSame(3, $this->pdo->prepared);
    }

    public function testAfterLoadsAllPricesInOneQuery(): void
    {
        $cart = new CartService(new InMemoryCartStorage(), new PdoPriceRepository($this->pdo));
        $cart->addProduct(1, 1);
        $cart->addProduct(2, 1);
        $cart->addProduct(3, 1);

        $cart->getTotal();

        self::assertSame(1, $this->pdo->prepared);
    }

    public function testBeforeCountsAnUnknownProductAsFree(): void
    {
        $cart = self::beforeCart($this->pdo);
        $cart->addProduct(1, 1);
        $cart->addProduct(99, 5);

        self::assertEqualsWithDelta(2.5, $cart->getTotal(), 1e-9);
    }

    public function testAfterRejectsAnUnknownProduct(): void
    {
        $cart = new CartService(new InMemoryCartStorage(), new PdoPriceRepository($this->pdo));
        $cart->addProduct(1, 1);
        $cart->addProduct(99, 5);

        $this->expectException(UnknownProduct::class);
        $this->expectExceptionMessage('99');
        $cart->getTotal();
    }

    public function testAfterRejectsANonPositiveQuantity(): void
    {
        $cart = new CartService(new InMemoryCartStorage(), new PdoPriceRepository($this->pdo));

        $this->expectException(InvalidArgumentException::class);
        $cart->addProduct(1, 0);
    }
}

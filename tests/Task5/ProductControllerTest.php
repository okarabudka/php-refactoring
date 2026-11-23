<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task5;

use Closure;
use Okarabudka\Refactoring\Legacy\Database;
use Okarabudka\Refactoring\Task5\After;
use Okarabudka\Refactoring\Task5\After\PdoProductRepository;
use Okarabudka\Refactoring\Task5\After\ProductView;
use Okarabudka\Refactoring\Task5\Before;
use Okarabudka\Refactoring\Tests\Support\Sqlite;
use PDO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ProductControllerTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = Sqlite::inMemory(
            'CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT NOT NULL, price REAL NOT NULL)',
            "INSERT INTO products VALUES (1, 'Laptop stand', 39.9), (2, 'Fish & Chips <deluxe>', 12.5)",
        );
    }

    protected function tearDown(): void
    {
        Database::setConnection(null);
        $_GET = [];
    }

    /**
     * Each page renderer takes the query string and returns what the user sees.
     *
     * @return iterable<string, array{Closure(PDO, array<string, string>): string}>
     */
    public static function pages(): iterable
    {
        yield 'before' => [self::beforePage(...)];
        yield 'after' => [self::afterPage(...)];
    }

    /** @param array<string, string> $query */
    private static function beforePage(PDO $pdo, array $query): string
    {
        Database::setConnection($pdo);
        $_GET = $query;
        ob_start();
        new Before\ProductController()->handleRequest();

        return (string) ob_get_clean();
    }

    /** @param array<string, string> $query */
    private static function afterPage(PDO $pdo, array $query): string
    {
        return new After\ProductController(new PdoProductRepository($pdo), new ProductView())->show($query);
    }

    /** @param Closure(PDO, array<string, string>): string $page */
    #[DataProvider('pages')]
    public function testShowsNameAndPrice(Closure $page): void
    {
        self::assertSame('<h1>Laptop stand</h1><p>Price: 39.9</p>', $page($this->pdo, ['id' => '1']));
    }

    /** @param Closure(PDO, array<string, string>): string $page */
    #[DataProvider('pages')]
    public function testMissingId(Closure $page): void
    {
        self::assertSame('Missing product ID', $page($this->pdo, []));
        self::assertSame('Missing product ID', $page($this->pdo, ['id' => '0']));
    }

    /** @param Closure(PDO, array<string, string>): string $page */
    #[DataProvider('pages')]
    public function testUnknownProduct(Closure $page): void
    {
        self::assertSame('Product not found', $page($this->pdo, ['id' => '99']));
    }

    public function testBeforePrintsTheNameUnescaped(): void
    {
        self::assertSame('<h1>Fish & Chips <deluxe></h1><p>Price: 12.5</p>', self::beforePage($this->pdo, ['id' => '2']));
    }

    public function testAfterEscapesTheName(): void
    {
        self::assertSame(
            '<h1>Fish &amp; Chips &lt;deluxe&gt;</h1><p>Price: 12.5</p>',
            self::afterPage($this->pdo, ['id' => '2']),
        );
    }

    public function testAfterTreatsANonNumericIdAsMissing(): void
    {
        self::assertSame('Missing product ID', self::afterPage($this->pdo, ['id' => '1 OR 1=1']));
    }
}

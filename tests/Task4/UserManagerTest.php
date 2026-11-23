<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task4;

use Closure;
use Exception;
use Okarabudka\Refactoring\Legacy\Database;
use Okarabudka\Refactoring\Shared\SystemClock;
use Okarabudka\Refactoring\Task4\After;
use Okarabudka\Refactoring\Task4\After\PdoUserRepository;
use Okarabudka\Refactoring\Task4\Before;
use Okarabudka\Refactoring\Tests\Support\FrozenClock;
use Okarabudka\Refactoring\Tests\Support\Sqlite;
use PDO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UserManagerTest extends TestCase
{
    private const string SCHEMA = 'CREATE TABLE users (email TEXT NOT NULL, password TEXT NOT NULL, created_at TEXT NOT NULL)';

    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = Sqlite::inMemory(self::SCHEMA);
    }

    protected function tearDown(): void
    {
        Database::setConnection(null);
    }

    /** @return iterable<string, array{Closure(PDO): (Before\UserManager|After\UserManager)}> */
    public static function managers(): iterable
    {
        yield 'before' => [static function (PDO $pdo): Before\UserManager {
            Database::setConnection($pdo);

            return new Before\UserManager();
        }];
        yield 'after' => [static fn(PDO $pdo): After\UserManager => new After\UserManager(
            new PdoUserRepository($pdo),
            new SpyMailer(),
            new SystemClock(),
        )];
    }

    /** @param Closure(PDO): (Before\UserManager|After\UserManager) $newManager */
    #[DataProvider('managers')]
    public function testRegistrationStoresTheUserWithAHashedPassword(Closure $newManager): void
    {
        $newManager($this->pdo)->register(['email' => 'alex@example.com', 'password' => 'correct horse']);

        $users = $this->users();
        self::assertCount(1, $users);
        self::assertSame('alex@example.com', $users[0]['email']);
        self::assertTrue(password_verify('correct horse', $users[0]['password']));
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $users[0]['created_at']);
    }

    /** @return iterable<string, array{Closure(PDO): (Before\UserManager|After\UserManager), array<string, string>, string}> */
    public static function invalidInput(): iterable
    {
        foreach (self::managers() as $name => [$newManager]) {
            yield "{$name}, malformed email" => [$newManager, ['email' => 'alex_example.com', 'password' => '12345678'], 'Invalid email'];
            yield "{$name}, empty email" => [$newManager, ['email' => '', 'password' => '12345678'], 'Invalid email'];
            yield "{$name}, missing email" => [$newManager, ['password' => '12345678'], 'Invalid email'];
            yield "{$name}, 7 character password" => [$newManager, ['email' => 'alex@example.com', 'password' => '1234567'], 'Password too short'];
        }
    }

    /**
     * @param Closure(PDO): (Before\UserManager|After\UserManager) $newManager
     * @param array<string, string> $input
     */
    #[DataProvider('invalidInput')]
    public function testInvalidInputIsRejectedAndNothingIsStored(Closure $newManager, array $input, string $message): void
    {
        $manager = $newManager($this->pdo);

        try {
            $manager->register($input);
            self::fail('Expected the registration to be rejected.');
        } catch (Exception $e) {
            self::assertSame($message, $e->getMessage());
        }
        self::assertSame([], $this->users());
    }

    public function testAfterSendsTheWelcomeMailToTheNewUser(): void
    {
        $mailer = new SpyMailer();
        $manager = new After\UserManager(new PdoUserRepository($this->pdo), $mailer, new SystemClock());

        $manager->register(['email' => 'alex@example.com', 'password' => 'correct horse']);

        self::assertSame(
            [['to' => 'alex@example.com', 'subject' => 'Welcome!', 'body' => 'Thanks for registering.']],
            $mailer->sent,
        );
    }

    public function testAfterTakesTheRegistrationTimeFromTheClock(): void
    {
        $clock = FrozenClock::at('2026-03-01 12:30:00');
        $manager = new After\UserManager(new PdoUserRepository($this->pdo), new SpyMailer(), $clock);

        $manager->register(['email' => 'alex@example.com', 'password' => 'correct horse']);

        self::assertSame('2026-03-01 12:30:00', $this->users()[0]['created_at']);
    }

    /** @return list<array{email: string, password: string, created_at: string}> */
    private function users(): array
    {
        $statement = $this->pdo->query('SELECT email, password, created_at FROM users');
        self::assertNotFalse($statement);

        /** @var list<array{email: string, password: string, created_at: string}> */
        return $statement->fetchAll();
    }
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task9;

use DateTimeImmutable;
use InvalidArgumentException;
use Okarabudka\Refactoring\Legacy\Database;
use Okarabudka\Refactoring\Task9\After;
use Okarabudka\Refactoring\Task9\After\PdoTrialRepository;
use Okarabudka\Refactoring\Task9\After\TrialPolicy;
use Okarabudka\Refactoring\Task9\After\TrialStatus;
use Okarabudka\Refactoring\Task9\Before;
use Okarabudka\Refactoring\Task9\Before\Logger;
use Okarabudka\Refactoring\Tests\Support\FrozenClock;
use Okarabudka\Refactoring\Tests\Support\RecordingLogger;
use Okarabudka\Refactoring\Tests\Support\Sqlite;
use PDO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TrialServiceTest extends TestCase
{
    private const int USER_ID = 7;

    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = Sqlite::inMemory('CREATE TABLE users (id INTEGER PRIMARY KEY, trial_started_at TEXT NOT NULL)');
        Logger::$lines = [];
    }

    protected function tearDown(): void
    {
        Database::setConnection(null);
        Logger::$lines = [];
    }

    /** @return iterable<string, array{int, string}> */
    public static function elapsedDays(): iterable
    {
        yield 'first day' => [0, 'active'];
        yield 'day 26' => [26, 'active'];
        yield 'day 27, three days left' => [27, 'ending'];
        yield 'day 30, last day' => [30, 'ending'];
        yield 'day 31' => [31, 'expired'];
    }

    /**
     * The before code reads the real clock, so the fixture has to be written relative to "now" as well.
     */
    #[DataProvider('elapsedDays')]
    public function testBefore(int $days, string $expected): void
    {
        $this->startTrial(new DateTimeImmutable()->modify("-{$days} days -1 hour"));
        Database::setConnection($this->pdo);

        self::assertSame($expected, new Before\TrialService()->status(self::USER_ID));
    }

    #[DataProvider('elapsedDays')]
    public function testAfter(int $days, string $expected): void
    {
        $clock = FrozenClock::at('2026-11-23 09:00:00');
        $this->startTrial($clock->now()->modify("-{$days} days -1 hour"));

        self::assertSame($expected, $this->afterService($clock, new RecordingLogger())->status(self::USER_ID)->value);
    }

    public function testBeforeReportsAnUnknownUser(): void
    {
        Database::setConnection($this->pdo);

        self::assertSame('unknown', new Before\TrialService()->status(self::USER_ID));
    }

    public function testAfterReportsAnUnknownUser(): void
    {
        $service = $this->afterService(FrozenClock::at('2026-11-23 09:00:00'), new RecordingLogger());

        self::assertSame(TrialStatus::Unknown, $service->status(self::USER_ID));
    }

    public function testBeforeLogsTheExpiryThroughAStaticLogger(): void
    {
        $this->startTrial(new DateTimeImmutable('-40 days'));
        Database::setConnection($this->pdo);

        new Before\TrialService()->status(self::USER_ID);

        $lines = Logger::$lines;
        self::assertIsArray($lines);
        self::assertCount(1, $lines);
        self::assertIsString($lines[0]);
        self::assertStringEndsWith('Trial expired for user 7', $lines[0]);
    }

    public function testAfterLogsTheExpiryThroughTheInjectedLogger(): void
    {
        $clock = FrozenClock::at('2026-11-23 09:00:00');
        $logger = new RecordingLogger();
        $this->startTrial($clock->now()->modify('-40 days'));

        $this->afterService($clock, $logger)->status(self::USER_ID);

        self::assertSame(
            [['level' => 'info', 'message' => 'Trial expired for user {userId}', 'context' => ['userId' => 7]]],
            $logger->records,
        );
    }

    public function testTheBoundaryIsExactToTheSecond(): void
    {
        $this->startTrial(new DateTimeImmutable('2026-10-23 09:00:00'));
        $logger = new RecordingLogger();

        self::assertSame(TrialStatus::Ending, $this->afterService(FrozenClock::at('2026-11-23 08:59:59'), $logger)->status(self::USER_ID));
        self::assertSame(TrialStatus::Expired, $this->afterService(FrozenClock::at('2026-11-23 09:00:00'), $logger)->status(self::USER_ID));
    }

    public function testThePolicyIsConfigurable(): void
    {
        $policy = new TrialPolicy(trialDays: 14, warningDays: 2);

        self::assertSame(TrialStatus::Active, $policy->statusAfter(11));
        self::assertSame(TrialStatus::Ending, $policy->statusAfter(12));
        self::assertSame(TrialStatus::Expired, $policy->statusAfter(15));
    }

    public function testThePolicyRejectsAWarningLongerThanTheTrial(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TrialPolicy(trialDays: 3, warningDays: 5);
    }

    private function startTrial(DateTimeImmutable $startedAt): void
    {
        $this->pdo->prepare('INSERT INTO users (id, trial_started_at) VALUES (?, ?)')
            ->execute([self::USER_ID, $startedAt->format('Y-m-d H:i:s')]);
    }

    private function afterService(FrozenClock $clock, RecordingLogger $logger): After\TrialService
    {
        return new After\TrialService(new PdoTrialRepository($this->pdo), new TrialPolicy(), $clock, $logger);
    }
}

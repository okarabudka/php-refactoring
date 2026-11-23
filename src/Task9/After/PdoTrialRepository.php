<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task9\After;

use DateTimeImmutable;
use PDO;
use UnexpectedValueException;

final readonly class PdoTrialRepository implements TrialRepository
{
    public function __construct(private PDO $pdo) {}

    public function trialStartedAt(int $userId): ?DateTimeImmutable
    {
        $statement = $this->pdo->prepare('SELECT trial_started_at FROM users WHERE id = ?');
        $statement->execute([$userId]);
        $startedAt = $statement->fetchColumn();
        if ($startedAt === false) {
            return null;
        }
        if (!is_string($startedAt)) {
            throw new UnexpectedValueException("Trial start of user {$userId} is not a date.");
        }

        return new DateTimeImmutable($startedAt);
    }
}

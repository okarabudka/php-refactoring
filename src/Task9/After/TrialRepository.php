<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task9\After;

use DateTimeImmutable;

interface TrialRepository
{
    /** Null if the user does not exist. */
    public function trialStartedAt(int $userId): ?DateTimeImmutable;
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task9\After;

use InvalidArgumentException;

/**
 * The trial rules as plain values, without any I/O: a pure function of elapsed days.
 */
final readonly class TrialPolicy
{
    public function __construct(
        public int $trialDays = 30,
        public int $warningDays = 3,
    ) {
        if ($trialDays < 1 || $warningDays < 0 || $warningDays > $trialDays) {
            throw new InvalidArgumentException('Trial and warning days are inconsistent.');
        }
    }

    public function statusAfter(int $elapsedDays): TrialStatus
    {
        return match (true) {
            $elapsedDays > $this->trialDays => TrialStatus::Expired,
            $elapsedDays >= $this->trialDays - $this->warningDays => TrialStatus::Ending,
            default => TrialStatus::Active,
        };
    }
}

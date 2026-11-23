<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task9\After;

use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;

final readonly class TrialService
{
    public function __construct(
        private TrialRepository $trials,
        private TrialPolicy $policy,
        private ClockInterface $clock,
        private LoggerInterface $logger,
    ) {}

    public function status(int $userId): TrialStatus
    {
        $startedAt = $this->trials->trialStartedAt($userId);
        if ($startedAt === null) {
            return TrialStatus::Unknown;
        }

        $status = $this->policy->statusAfter($this->clock->now()->diff($startedAt)->days);
        if ($status === TrialStatus::Expired) {
            $this->logger->info('Trial expired for user {userId}', ['userId' => $userId]);
        }

        return $status;
    }
}
